<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

error_reporting(E_ALL);
ini_set('display_errors', '1');

ini_set('max_execution_time', 3600); //3600 seconds = 60 minutes

use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;

require_once BASEPATH . 'libraries/spout-2.4.3/src/Spout/Autoloader/autoload.php';

class Partner_booking extends CI_Controller {

    /**
     * load list modal and helpers
     */
    function __Construct() {
        parent::__Construct();
        $this->load->model('booking_model');
        $this->load->model('partner_model');
        $this->load->model('user_model');
        $this->load->model('vendor_model');

        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library("session");
        $this->load->library('email');
        $this->load->library('partner_sd_cb');
        $this->load->library('partner_utilities');

        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee') && ($this->session->userdata('add service') == '1')) {
            return TRUE;
        } else {
            redirect(base_url() . "employee/login");
        }
    }

    /**
     *  @desc  : Use to get upload partner excel file form
     *  @param : $error - error if occured while loading the page
     *  @return : array of sources to view
     */
    function index($error = "") {
        $source['source'] = $this->partner_model->get_all_partner_source();
        if (!empty($error)) {
            $source['error'] = $error;
        }
        $this->load->view('employee/header');
        $this->load->view('employee/partner_upload_booking', $source);
    }

    /**
     *  @desc  : Use to upload partner excel file in partner_leads table
     *  @param : void
     *  @return : void
     */
    function upload_partner_booking() {

        $validation = $this->checkValidation();
        if ($validation) {
            $partner_id = $this->input->post('partner');
            $return = $this->partner_utilities->validate_file($_FILES);

            if ($return == "true") {
                $inputFileName = $_FILES['file']['tmp_name'];
                $this->upload_excel($inputFileName, $partner_id);
            } else {

                $this->index($return['error']);
            }
        } else {

            $this->index();
        }
    }

    /**
     *  @desc  : Use to get form to upload partner's excel
     *  @param : void
     *  @return : void
     */
    function get_upload_partners_cancelled_booking() {
        $source['source'] = $this->partner_model->get_all_partner_source();
        if (!empty($error)) {
            $source['error'] = $error;
        }
        $this->load->view('employee/header');
        $this->load->view('employee/upload_partners_cancelled_bookings', $source);
    }

    /**
     *  @desc  : Use to enter partner's excel's data in booking_details table
     *  @param : void
     *  @return : void
     */
    public function post_upload_partners_cancelled_booking() {

        $return = $this->partner_utilities->validate_file($_FILES);
        if ($return == "true") {
            $inputFileName = $_FILES['file']['tmp_name'];
            $reader = ReaderFactory::create(Type::XLSX);
            $reader->open($inputFileName);
            $count = 1;
            $rows = array();
            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $row) {
                    // if($count>1){
                    $output = $this->user_model->search_user(trim($row[3]));
                    $user = "";
                    $user['state'] = "";
                    if (empty($output)) {
                        $state = "";
                        //User doesn't exist
                        $user['name'] = $row[1];
                        $user['phone_number'] = $row[3];
                        $user['user_email'] = "";
                        $user['city'] = $row[4];
                        $state = $this->vendor_model->getall_state($row[4]);

                        if (!empty($state)) {
                            $user['state'] = $state[0]['state'];
                        } else {
                            $user['state'] = "";
                        }

                        $user_id = $this->user_model->add_user($user);
                        $data = $this->set_price_rows_data($row, $user_id);

                        //For adding unit and appliance details
                        //$appliance_id = $this->booking_model->addappliancedetails($data);
                        //$this->booking_model->addunitdetails($data);

                        $output = $this->booking_model->addbooking($data, "", $row[4], $user['state']);
                    }
                }
            }
            redirect(base_url() . 'employee/booking/view');
            $reader->close();
        } else {
            $this->get_upload_partners_cancelled_booking();
        }
    }

    /**
     *  @desc  : Use to enter partner's excel's data in booking_details table
     *  @param : excel's data and user_id
     *  @return : booking_details array
     */
    function set_price_rows_data($row, $user_id) {
        $booking['user_id'] = $user_id;
        $booking['booking_primary_contact_no'] = $row[3];
        $service_id = $this->booking_model->getServiceId($row[7]);

        if (!empty($service_id)) {
            $booking['service_id'] = $service_id;
        }
        $booking['booking_date'] = date_format($row[6], 'Y-m-d');
        $booking['source'] = 'SQ';
        $booking_id = $this->create_booking_id($booking['user_id'], $booking['source']);
        $split_booking_id = explode("Q", $booking_id);
        $booking['booking_id'] = "SQ" . $split_booking_id[2];
        $booking['type'] = "Booking";
        $booking['current_status'] = "Cancelled";
        $booking['internal_status'] = "Cancelled";
        $booking['quantity'] = 1;
        $booking['booking_primary_contact_no'] = $row[3];
        $booking['booking_address'] = $row[5];
        $booking['booking_alternate_contact_no'] = "";
        $booking['booking_timeslot'] = "";
        $booking['booking_pincode'] = 000000;
        $booking['booking_remarks'] = "NULL";
        $booking['query_remarks'] = "NULL";
        $booking['potential_value'] = "NULL";
        $booking['amount_due'] = "NULL";

        return $booking;
    }

    /**
     *  @desc  : Extract rows data from excel
     *  @param : File name, Partner Id
     *  @return : void
     */
    function upload_excel($inputFileName, $partner_id) {
        $reader = ReaderFactory::create(Type::XLSX);
        $reader->open($inputFileName);
        $count = 1;
        $rows = array();

        $source_code = $this->partner_model->get_source_code_for_partner($partner_id);
        switch ($source_code) {
            case 'SP':
                // Header for Excel file
                //Order ID,  Item ID, Merchant ID, Product Name,  Product Category (L3), Brand, Order Date,  Delivery Date, Customer Name, Customer Address,  Customer City, Customer Pincode,  Customer Mobile Number,  Email id,  Reference Date,  Processed with in 24 hrs,  Status (Scheduled for  XYZ date / Installed, Installation Date, Customer ID Proof Details, Customer Remarks (if any, on scheduling)

                foreach ($reader->getSheetIterator() as $sheet) {

                    foreach ($sheet->getRowIterator() as $row) {

                        // Number of cell from top of Excel file
                        if ($count > 1) {

                            $data = $this->retreive_cell_for_paytm($partner_id, $row);
                            if (!empty($data)) {
                                array_push($rows, $data);
                            }
                        }
                        $count++;
                    }

                    if (!empty($rows)) {

                        $this->insert_data_list($rows);
                    }
                }

                $reader->close();
                //print_r($rows);
                if (!empty($rows)) {

                    $output = "No New Entries Inserted.";
                    $userSession = array('error' => $output);
                    $this->redirect_upload_form($userSession);
                }


                break;
            default:

                break;
                echo "Partner Not Exist";
        }
    }

    /**
     *  @desc  : Use to add user details
     *  @param : user name, email, address, phone
     *  @return : user id
     */
    function add_user($username, $email, $address, $phone) {
        $user['name'] = $username;
        $user['user_email'] = $email;
        $user['home_address'] = $address;
        $user['phone_number'] = $phone;

        $user_id = $this->user_model->add_user($user);
        //echo print_r($user, true), EOL;
        //Add sample appliances for this user
        $count = $this->booking_model->getApplianceCountByUser($user_id);
        //Add sample appliances if user has < 5 appliances in wallet
        if ($count < 5) {
            $this->booking_model->addSampleAppliances($user_id, 5 - intval($count));
        }
        return $user_id;
    }

    /**
     *  @desc : Use to check user exist or not. if user is not exit request to add user and user is  exit, get user id.
     *  Check order id is duplicate.
     *  Request to retrieve cell data;
     *  @param : partner id, array(data)
     *  @return : void
     */
    function retreive_cell_for_paytm($partner_id, $row) {

        $output = $this->user_model->search_user(trim($row[12]));
        if (empty($output)) {

            // Call add_user method to add user with parameter user name, email, address, phone

            $user_id = $this->add_user($row[8], $row[13], $row[9] .
                    ", " . $row[11], trim($row[12]));
            //Add this lead into the leads table
            //Check whether this is a new Lead or Not
        } else {
            //User exists
            $user_id = $output[0]['user_id'];
        }

        //Add this lead into the leads table
        //Check whether this is a new Lead or Not

        if ($this->partner_model->check_partner_lead_exists_by_order_id($row[0], $partner_id) == false) {
            return $this->set_price_rows_data_for_paytm($row, $partner_id, $user_id);
        }
    }

    /**
     *  @desc : Request to retrieve cell data;
     *  @param : array(data), partner id, user id
     *  @return : array(data)
     */
    function set_price_rows_data_for_paytm($row, $partner_id, $user_id) {

        $data['OrderID'] = $row[0];
        $data['ItemID'] = $row[1];
        $data['MerchantID'] = $row[2];
        $data['ProductType'] = $row[3];
        $data['Product'] = $this->getProduct($row[4]);
        $data['Brand'] = $row[5];
        $data['OrderDate'] = $row[6]->format('Y-m-d H:i:s');
        $data['DeliveryDate'] = $row[7]->format('Y-m-d H:i:s');
        $data['Name'] = $row[8];
        $data['Address'] = $row[9];
        $data['City'] = $row[10];
        $data['Pincode'] = $row[11];
        $data['Mobile'] = $row[12];
        $data['Email'] = $row[13];
        $data['ReferenceDate'] = $row[14]->format('Y-m-d H:i:s');
        ;
        $data['247aroundBookingRemarks'] = $row[16];
        /* if($row[17] instanceof \DateTime){
          $data['ScheduledAppointmentDate'] =  $row[17]->format('Y-m-d H:i:s');
          } */
        $data['247aroundBookingStatus'] = "FollowUp";
        $data['CustomerIDProofDetails'] = $row[18];
        $data['Remarks'] = $row[19];
        $data['PartnerID'] = $partner_id;
        $data['247aroundBookingID'] = $this->createBooking($data, $partner_id, $user_id);


        return $data;
    }

    /**
     *  @desc : Request to retrieve cell data;
     *  @param : array(data), partner id, user id
     *  @return : booking id
     */
    function createBooking($data, $partner_id, $user_id) {
        // print_r("times");
        $source = $this->partner_model->get_source_code_for_partner($partner_id);
        $data['user_id'] = $user_id;
        $data['source'] = $source;
        $data['booking_id'] = $this->create_booking_id($data['user_id'], $data['source']);
        $data['quantity'] = '1';
        $data['appliance_brand'] = $data['Brand'];
        $data['appliance_category'] = '';
        $data['appliance_capacity'] = '';
        $data['service_id'] = $this->booking_model->getServiceId($data['Product']);
        $data['booking_primary_contact_no'] = $data['Mobile'];
        $data['booking_alternate_contact_no'] = '';
        $data['model_number'] = '';
        $data['description'] = '';
        $data['appliance_tags'] = $data['Brand'] . " " . $data['Product'];
        $data['purchase_month'] = date('m');
        $data['purchase_year'] = date('Y');

        $data['items_selected'] = '';
        $data['total_price'] = '';
        $data['potential_value'] = '';
        $data['last_service_date'] = date('d-m-Y');

        //echo "<br><br>";
        //print_r("expression");
        $appliance_id = $this->booking_model->addexcelappliancedetails($data);
        //echo print_r($appliance_id, true) . "<br><br>";
        //print_r("appliance_id".$appliance_id);
        $return = $this->booking_model->addapplianceunitdetails($data);
        //print_r("appliance_unit_id".$return);

        $data['current_status'] = "FollowUp";
        $data['internal_status'] = "FollowUp";
        $data['type'] = "Query";
        $data['booking_date'] = '';
        $data['booking_timeslot'] = '';
        $data['booking_address'] = $data['Address'];
        $data['booking_pincode'] = $data['Pincode'];
        $data['amount_due'] = '';
        $data['booking_remarks'] = '';
        $data['query_remarks'] = '';

        //Insert query
        //echo print_r($booking, true) . "<br><br>";
        $id = $this->booking_model->addbooking($data, $appliance_id);
        //print_r("add booking".$id);

        return $data['booking_id'];
    }

    /**
     *  @desc : Spelling check for product name
     *  @param : product name
     *  @return : product name
     */
    function getProduct($product) {
        $prod = trim($product);

        if (stristr($prod, "Washing Machine") || stristr($prod, "WashingMachine") || stristr($prod, "Dryer")) {
            $productName = 'Washing Machine';
        }
        if (stristr($prod, "Television")) {
            $productName = 'Television';
        }
        if (stristr($prod, "Airconditioner") || stristr($prod, "Air Conditioner")) {
            $productName = 'Air Conditioner';
        }
        if (stristr($prod, "Refrigerator")) {
            $productName = 'Refrigerator';
        }
        if (stristr($prod, "Microwave")) {
            $productName = 'Microwave';
        }
        if (stristr($prod, "Purifier")) {
            $productName = 'Water Purifier';
        }
        if (stristr($prod, "Chimney")) {
            $productName = 'Chimney';
        }

        return $productName;
    }

    /**
     *  @desc : This is used to create booking id
     *  @param : user id, source code
     *  @return : booking id
     */
    function create_booking_id($user_id, $source) {
        $booking['booking_id'] = '';
        $digits = 4; // use to create random number (4 number digits)
        $mm = date("m");
        $dd = date("d");
        $booking['booking_id'] = str_pad($user_id, 4, "0", STR_PAD_LEFT) . $dd . $mm;
        // Add 4 digits random number in booking id.
        $booking['booking_id'] .= rand(pow(10, $digits - 1) - 1, pow(10, $digits) - 1);

        //Add source
        $booking['source'] = $source;
        $booking['booking_id'] = "Q-" . $booking['source'] . "-" . $booking['booking_id'];
        return $booking['booking_id'];
    }

    /*  @desc : First partner data insert into temporary file then switch partner_leads table .
     *  @param : array(data)
     *  @return : void
     */

    function insert_data_list($rows) {

        $table_name = "partner_leads";

        $return = $this->partner_model->insert_data_in_batch($table_name, $rows);

        if ($return == 1) {

            $output = "File uploaded.";
            $userSession = array('success' => $output);
            $this->redirect_upload_form($userSession);
        }
    }

    /**
     *  @desc : This function is to redirect to upload form(takes back to upload page)
     *  @param : $userSession - user's session who is logged in
     *  @return : true if validation true otherwise FALSE
     */
    function redirect_upload_form($userSession) {

        $this->session->set_userdata($userSession);
        redirect(base_url() . "employee/partner_booking");
    }

    /**
     *  @desc : This function for check validation
     *  @param : void
     *  @return : true if validation true otherwise FALSE
     */
    function checkValidation() {
        $this->form_validation->set_rules('partner', 'Partner', 'required');

        if ($this->form_validation->run() == FALSE) {
            return FALSE;
        } else {
            return true;
        }
    }

}
