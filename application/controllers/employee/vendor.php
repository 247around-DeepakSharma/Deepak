<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 360000); //3600 seconds = 60 minutes

use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;

require_once BASEPATH . 'libraries/spout-2.4.3/src/Spout/Autoloader/autoload.php';

class vendor extends CI_Controller {

    function __Construct() {
        parent::__Construct();
        $this->load->model('employee_model');
        $this->load->model('booking_model');
        $this->load->library('PHPReport');
        $this->load->model('filter_model');
        $this->load->model('service_centers_model');
        $this->load->helper(array('form', 'url'));
        //$this->load->library('../controllers/api');
        $this->load->library('form_validation');
        $this->load->model('vendor_model');
        $this->load->model('partner_model');
        $this->load->library('booking_utilities');
        $this->load->library('partner_utilities');
        $this->load->library('notify');
        $this->load->library("pagination");
        $this->load->library("asynchronous_lib");
        $this->load->library("session");
        $this->load->library('s3');
        $this->load->library('email');
        $this->load->helper('download');
    }

    /**
     * @desc : This function is used to add/edit vendor details
     *
     * Vendor details like- vendor's name, owner's name, phone no., email, POC(point of contact) details
     *      are added/edited.
     *
     * Few more details like- appliance(s), brand(s) they handle and there non-working days
     *      can also be added/edited.
     *
     * @param : void
     * @return : void
     */
    function index() {
        $checkValidation = $this->checkValidation();
        if ($checkValidation) {

            $non_working_days = $this->input->post('day');
            $appliances = $this->input->post('appliances');
            $brands = $this->input->post('brands');

            if (!empty($non_working_days)) {
                $_POST['non_working_days'] = implode(",", $non_working_days);
            }

            if (!empty($appliances)) {
                $_POST['appliances'] = implode(",", $appliances);
            }

            if (!empty($brands)) {
                $_POST['brands'] = implode(",", $brands);
            }

            unset($_POST['day']);

            if (!empty($_POST['id'])) {
                //if vendor exists, details are edited
                $this->vendor_model->edit_vendor($_POST, $_POST['id']);

                redirect(base_url() . 'employee/vendor/viewvendor', 'refresh');
            } else {
                // get service center code by calling generate_service_center_code() method
                $owner_email = $this->input->post('owner_email');
                $primary_contact_email = $this->input->post('primary_contact_email');
                $to = $owner_email.','.$primary_contact_email;
                
                $subject = "Welcome to 247around";
                $message = "Dear Partner,<br><br>"
                        . "247around welcomes you to its Partner Network, we hope to have a long lasting relationship with you.<br><br>"
                        . "As informed earlier, serial number of appliance is mandatory when you close a booking. All bookings without serial numbers will be cancelled.<br><br> "
                        . "Engineer has to note the serial number when installation is done. In case serial number is not found on the appliance, he needs to bring one of the following proofs:<br><br> "
                        . "1st Option : Serial Number Of Appliance<br><br>"
                        . "2nd Option : Invoice Number Of The Appliance<br><br>"
                        . "3rd Option : Customer ID Card Number - PAN / Aadhar / Driving License etc.<br><br>"
                        . "No completion will be allowed without any one of the above. For any confusion, write to us or call us.<br><br><br>"
                        . "Regards,<br>"
                        . "247around Team";
                
                $_POST['sc_code'] = $this->generate_service_center_code($_POST['name'], $_POST['district']);

                //if vendor do not exists, vendor is added
                $sc_id = $this->vendor_model->add_vendor($_POST);

                $this->sendWelcomeSms($_POST['primary_contact_phone_1'], $_POST['name'],$sc_id);
                $this->sendWelcomeSms($_POST['owner_phone_1'], $_POST['owner_name'],$sc_id);

                $this->notify->sendEmail("booking@247around.com", $to , 'anuj@247around.com, nits@247around.com', '', $subject , $message, "");

		  //create vendor login details as well
		   $sc_login_uname = strtolower($_POST['sc_code']);
		   $login['service_center_id'] = $sc_id;
		   $login['user_name'] = $sc_login_uname;
		   $login['password'] = md5($sc_login_uname);
		   $login['active'] = 1;

		   $this->vendor_model->add_vendor_login($login);
                   
                   // Sending Login details mail to Vendor using Template
                   $email = array();
                   //Getting template from Database
                   $template = $this->booking_model->get_booking_email_template("vendor_login_details");
                   if(!empty($template)){
                   $email['username'] = $sc_login_uname;
                   $email['password'] = $sc_login_uname;
                   $subject = "Login Details";
                   
                   log_message('info', " Email Body" . print_r($email, true));
                   $emailBody = vsprintf($template[0], $email);
                   
                   $this->notify->sendEmail("booking@247around.com", $to , 'anuj@247around.com, nits@247around.com', '', $subject , $emailBody, "");
                   }else{
                       log_message('info', " Login Email Send Error" . print_r($email, true));
                   }
		   redirect(base_url() . 'employee/vendor/viewvendor');
            }
        } else {
            $this->add_vendor();
        }
    }

    /**
     * @desc: this function is used to generate service center code.
     * @param: String(Service center name)
     * @param: String(District)
     * @return : String (Service center code)
     */
    function generate_service_center_code($sc_name, $district) {
        //generate 6 random  letter string
        $sc_code = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);
        $final_sc_code = strtoupper($sc_code); // convert string in upper case
        $status = $this->vendor_model->check_sc_code_exist($final_sc_code);  // check service center code is exist or not
        if ($status == true) {   //if sc code exists
            generate_service_center_code($sc_name, $district); // repeat  process of generating service center code
        } else {
            return $final_sc_code; // if sc code does not  exit, return sc code.
        }
    }

    /**
     * @desc: Sends sms to owner and point of contact of service center on new creation vendor
     *
     * SMS is sent only while adding new vendor not while editing an existing one.
     *
     * @param: String(Service center name)
     * @param: String(District)
     * @return : String (Service center code)
     */
    function sendWelcomeSms($phone_number, $vendor_name,$id) {
        $template = $this->vendor_model->getVendorSmsTemplate("new_vendor_creation");
        $smsBody = sprintf($template, $vendor_name);

        $sms_details = $this->notify->sendTransactionalSms($phone_number, $smsBody);
        //For saving SMS to the database on sucess
        if(isset($sms_details['info']) && $sms_details['info'] == '200'){
                $this->notify->add_sms_sent_details($id, 'vendor' , $phone_number,
                    $smsBody, '');
    }

    }

    /**
     * @desc: This function is used to check validation of the entered data
     *
     * @param: void
     * @return : If validation ok returns true else false
     */
    function checkValidation() {
        $this->form_validation->set_rules('name', 'Vendor Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('address', 'Vendor Address', 'trim|required');
        $this->form_validation->set_rules('state', 'State', 'trim|required');
        $this->form_validation->set_rules('district', 'District', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            return FALSE;
        } else {
            return true;
        }
    }

    /**
     * @desc: This function is used to get add/edit vendor form
     *
     * This form shows all our active services, brands and all the states of India
     *
     * @param: void
     * @return : array(result) to view
     */
    function add_vendor() {
        $results['services'] = $this->vendor_model->selectservice();
        $results['brands'] = $this->vendor_model->selectbrand();
        $results['select_state'] = $this->vendor_model->getall_state();
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $this->load->view('employee/header');
        $this->load->view('employee/addvendor', array('results' => $results, 'days' => $days));
    }

    /**
     * @desc: This function is to edit vendor's details
     *
     * Existing details will be be displayed in respective fields(allowed to edit)
     *      and rest of the fields will be displayed blank.
     *
     * @param: vendor id
     * @return : array(of details) to view
     */
    function editvendor($id) {
        $query = $this->vendor_model->editvendor($id);

        $results['services'] = $this->vendor_model->selectservice();
        $results['brands'] = $this->vendor_model->selectbrand();
        $results['select_state'] = $this->vendor_model->getall_state();

        $appliances = $query[0]['appliances'];
        $selected_appliance_list = explode(",", $appliances);
        $brands = $query[0]['brands'];
        $selected_brands_list = explode(",", $brands);

        $days = ['Sunday', 'Monday', 'Tuseday', 'Wednesday', 'Thursday', 'Friday', 'Satarday'];
        $non_working_days = $query[0]['non_working_days'];
        $selected_non_working_days = explode(",", $non_working_days);
        $this->load->view('employee/header');

        $this->load->view('employee/addvendor', array('query' => $query, 'results' => $results, 'selected_brands_list'
            => $selected_brands_list, 'selected_appliance_list' => $selected_appliance_list,
            'days' => $days, 'selected_non_working_days' => $selected_non_working_days));
    }

    /**
     * @desc: This function is to view particular vendor's details
     *
     * Will display all the details of a particular vendor
     *
     * @param: vendor id
     * @return : array(of details) to view
     */
    function viewvendor($vendor_id = "") {
        $query = $this->vendor_model->viewvendor($vendor_id);

        $this->load->view('employee/header');

        $this->load->view('employee/viewvendor', array('query' => $query));
    }

    /**
     * @desc: This function is to activate a particular vendor
     *
     * For this the vendor must be already registered with us and should be non-active(Active = 0)
     *
     * @param: vendor id
     * @return : void
     */
    function activate($id) {
        $this->vendor_model->activate($id);

        redirect(base_url() . 'employee/vendor/viewvendor', 'refresh');
    }

    /**
     * @desc: This function is to deactivate a particular vendor
     *
     * For this the vendor must be already registered with us and should be active(Active = 1)
     *
     * @param: vendor id
     * @return : void
     */
    function deactivate($id) {
        $this->vendor_model->deactivate($id);

        redirect(base_url() . 'employee/vendor/viewvendor', 'refresh');
    }

    /**
     * @desc: This function to delete a particular vendor
     *
     * For this the vendor must be already registered with us
     *
     * @param: vendor id
     * @return : void
     */
    function delete($id) {
        $this->vendor_model->delete($id);

        redirect(base_url() . 'employee/vendor/viewvendor', 'refresh');
    }


    /**
     *  @desc : This function is to select all pending bookings to assign vendor(if not already assigned)
     *
     * This form displays all the pending bookings for which still no vendor is assigned in a tabular form.
     *
     * Vendors can be assigned for more than one booking simultaneously.
     *
     *  @param : void
     *  @return : booking details and vendor details to view
     */
    function get_assign_booking_form() {
        $results = array();
        $bookings = $this->booking_model->pendingbookings();

        foreach ($bookings as $booking) {
            array_push($results, $this->booking_model->find_sc_by_pincode_and_appliance($booking['service_id'], $booking['booking_pincode']));
        }

        $this->load->view('employee/header');
        $this->load->view('employee/assignbooking', array('data' => $bookings, 'results' => $results));
    }

    /**
     *  @desc : Function to assign vendors for pending bookings in background process,
     *  it send a Post server request.
     *
     * We can select vendors available corresponding to each booking present and can assign that particular booking to vendor.
     *
     *  @param : void
     *  @return : load pending booking view
     */
    function process_assign_booking_form() {
        $service_center = $this->input->post('service_center');
        $url = base_url() . "employee/do_background_process/assign_booking";

        foreach ($service_center as $booking_id => $service_center_id) {
            if ($service_center_id != "") {
                $data = array();
                $data['booking_id'] = $booking_id;
                $data['service_center_id'] = $service_center_id;
                //Assign service centre
                $this->booking_model->assign_booking($booking_id, $service_center_id);

                $this->notify->insert_state_change($booking_id, ASSIGNED_VENDOR,_247AROUND_PENDING,"", $this->session->userdata('id'), $this->session->userdata('employee_id'),_247AROUND);

                // Delete Previous Assigned vendor data from service center action table
                //$this->vendor_model->delete_previous_service_center_action($booking_id);

                $this->asynchronous_lib->do_background_process($url, $data);
            }

        }

        //redirect(base_url() . DEFAULT_SEARCH_PAGE);
    }


    /**
     * @desc: This function is to get the reassign vendor page
     *
     * Its mainly done if already assigned vendor do not covers the pincode taken while entering booking.
     *
     * @param: booking id
     * @return : void
     */
    function get_reassign_vendor_form($booking_id = "") {
        $service_centers = $this->booking_model->select_service_center();

        $this->load->view('employee/header');

        $this->load->view('employee/reassignvendor', array('booking_id' => $booking_id, 'service_centers' => $service_centers));
    }

    /**
     * @desc: This function reassigns vendor for a particular booking.
     *
     * This is done if the assigned vendor is not able to finish his job due to any reason
     *
     * @param: void
     * @return : void
     */
    function process_reassign_vendor_form() {
        $booking_id = $this->input->post('booking_id');
        $service_center_id = $this->input->post('service');

	if ($service_center_id != "Select") {
            $this->booking_model->assign_booking($booking_id, $service_center_id);

           // $pre_service_center_data['current_status'] = "Cancelled";
            //$pre_service_center_data['internal_status'] = "Cancelled";

           // $this->service_centers_model->update_service_centers_action_table($booking_id, $pre_service_center_data);
            $this->vendor_model->delete_previous_service_center_action($booking_id);
            $unit_details = $this->booking_model->getunit_details($booking_id);

            foreach ($unit_details[0]['quantity'] as $value ) {
                $data = array();
                $data['current_status'] = "Pending";
                $data['internal_status'] = "Pending";
                $data['service_center_id'] = $service_center_id;
                $data['booking_id'] = $booking_id;
                $data['create_date'] = date('Y-m-d H:i:s');
                $data['unit_details_id'] = $value['unit_id'];
                $this->vendor_model->insert_service_center_action($data);
            }

            $this->notify->insert_state_change($booking_id, RE_ASSIGNED_VENDOR, ASSIGNED_VENDOR,"", $this->session->userdata('id'), $this->session->userdata('employee_id'),_247AROUND);

            //Setting mail to vendor flag to 0, once booking is re-assigned
            $this->booking_model->set_mail_to_vendor_flag_to_zero($booking_id);

	     log_message('info', "Reassigned - Booking id: " . $booking_id . "  By " .
		$this->session->userdata('employee_id') . " service center id " . $service_center_id);

            redirect(base_url() . DEFAULT_SEARCH_PAGE);
	} else {
            $output = "Please select any service center.";
            $userSession = array('error' => $output);
            $this->session->set_userdata($userSession);
            redirect(base_url() . 'employee/vendor/get_reassign_vendor_form/' . $booking_id, 'refresh');
        }
    }

    /**
     * @desc: This function to get form to broadcast mail to all vendors
     * @param: void
     * @return : void
     */
    function get_broadcast_mail_to_vendors_form() {
        //$service_centers = $this->booking_model->select_service_center();
        $this->load->view('employee/header');
        $this->load->view('employee/broadcastemailtovendor');
    }

    /**
     * @desc: This function sends broadcast mail to vendors
     *
     * Sends mail to all the owner and POC of the vendors, if we want to send some information to
     *      all the service centers simultaniously.
     *
     * @param: void
     * @return : void
     */
    function process_broadcast_mail_to_vendors_form() {
        $bcc_poc = $this->input->post('bcc_poc');
        $bcc_owner = $this->input->post('bcc_owner');
        $mail_to = $this->input->post('mail_to');
        $to = 'anuj@247around.com, nits@247around.com, sales@247around.com,' . $mail_to;
	$cc = $this->input->post('mail_cc');

	$subject = $this->input->post('subject');
	//Replace new lines with line breaks for proper html formatting
	$message = nl2br($this->input->post('mail_body'));

	$tmpFile = $_FILES['fileToUpload']['tmp_name'];
        $fileName = $_FILES['fileToUpload']['name'];
        move_uploaded_file($tmpFile, "/tmp/$fileName");

        //gets primary contact's email and owner's email
        $service_centers = $this->vendor_model->select_active_service_center_email();
        $bcc = $this->getBccToSendMail($service_centers, $bcc_poc, $bcc_owner);
        $attachment = "";
        if (!empty($fileName)) {
            $attachment = "/tmp/$fileName";
        }

        log_message('info', "broadcast mail to: " . $to);
        log_message('info', "broadcast mail cc: " . $cc);
        log_message('info', "broadcast mail bcc: " . $bcc);
        log_message('info', "broadcast mail subject: " . $subject);
        log_message('info', "broadcast mail message: " . $message);

        $this->notify->sendEmail("sales@247around.com", $to, $cc, $bcc, $subject, $message, $attachment);

       redirect(base_url() . DEFAULT_SEARCH_PAGE);
    }

    /**
     * @desc: Get Bcc email to send Broadcast mail when poc and owner flg is not empty.
     * @param: $service_centers
     *         Point of Contact's and Owner's Email which are active
     * @param: $bcc_poc
     *          to check if POC checkbox is checked, if checked will have some value
     * @param: $bcc_owner
     *          to check if owners checkbox is checked,if checked will have some value
     * @return : if true bcc_string else empty
     */
    function getBccToSendMail($service_centers, $bcc_poc, $bcc_owner) {
        $bcc = array();

        foreach ($service_centers as $key => $email) {
            if (!empty($bcc_poc) && !empty($bcc_owner)) {
                $bcc1 = $email['primary_contact_email'] . "," . $email['owner_email'];
                array_push($bcc, $bcc1);
            } else if (!empty($bcc_poc) && empty($bcc_owner)) {
                $bcc1 = $email['primary_contact_email'];
                array_push($bcc, $bcc1);
            } else if (empty($bcc_poc) && !empty($bcc_owner)) {
                $bcc1 = $email['owner_email'];
                array_push($bcc, $bcc1);
            }
        }

        if (!empty($bcc)) {
            $bcc_string = implode(", ", $bcc);
            return $bcc_string;
        } else {
            return "";
        }
    }

    /**
     *  @desc : This function is to get upload pincode through excel form
     *
     *  Stores the latest vendor pincode mapping file
     *
     *  @param : error
     *  @return : displays the view
     */
    function get_pincode_excel_upload_form($error = "") {
        $mapping_file['pincode_mapping_file'] = $this->vendor_model->getLatestVendorPincodeMappingFile();
        if ($error != "") {
            $mapping_file['error'] = $error;
        }
        $this->load->view('employee/header');
        $this->load->view('employee/upload_pincode_excel', $mapping_file);
    }

    /**
     *  @desc : This function get upload master pincode through excel form
     *
     *  This is to store all the pincodes available(i.e master pincode)
     *
     *  @param : void
     *  @return : displays the view
     */
    function get_master_pincode_excel_upload_form() {
        $this->load->view('employee/header');
        $this->load->view('employee/upload_master_pincode_excel');
    }

    /**
     *  @desc : This function is to upload pincode through excel (asynchronously)
     *  @param : void
     *  @return : void
     */
    function process_pincode_excel_upload_form() {
        $return = $this->partner_utilities->validate_file($_FILES);
        if ($return == "true") {

            $inputFileName = $_FILES['file']['tmp_name'];
            $details_pincode['file_name'] = "Consolidated_Pin_Code" . date('d-M-Y') . ".xlsx";
            // move excel file in tmp folder.
            move_uploaded_file($inputFileName, "/tmp/" . $details_pincode['file_name']);

            if (!empty($_POST['emailID'])) {
                $this->notify->sendEmail("booking@247around.com", $_POST['emailID'], '', '', 'Pincode Changes', $_POST['notes'], "/tmp/" . $details_pincode['file_name']);
            }


            $bucket = 'bookings-collateral';
            $details_pincode['bucket_name'] = "vendor-pincodes";
            $directory_xls = $details_pincode['bucket_name'] . "/" . $details_pincode['file_name'];

            // Insert file name and bucket name to S3
            $this->vendor_model->insertS3FileDetails($details_pincode);
            // Upload excel on S3
            $this->s3->putObjectFile(realpath("/tmp/" . $details_pincode['file_name']), $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
            // Insert Pincode Mapping data into table by using Asynchronous
            $url = base_url() . "employee/do_background_process/upload_pincode_file";
            $this->asynchronous_lib->do_background_process($url, array());

            redirect(base_url() . DEFAULT_SEARCH_PAGE);
	} else {

            $this->get_pincode_excel_upload_form("Not valid File");
        }
    }

    /**
     * @desc: Load Vendor Escalation form and get escalation reason and vendor details from table.
     *
     * This will send notification to vendor if he/she didn't call the customer or
     *      engeineer didn't reached on time to customer's place.
     *
     * @param : Booking Id
     * @return : Takes to view
     */
    function get_vendor_escalation_form($booking_id) {

        $data['escalation_reason'] = $this->vendor_model->getEscalationReason(array('entity'=>'vendor','active'=> '1'));
        $data['vendor_details'] = $this->vendor_model->getVendor($booking_id);
        $data['booking_id'] = $booking_id;

        $this->load->view('employee/header');
        $this->load->view('employee/vendor_escalation_form', $data);
    }

    /**
     * @desc: Insert Vendor Escalation reason in database
     *
     * And also requests a method to send sms and email to vendor
     *
     * @param : void
     * @return : Takes to view
     */
    function process_vendor_escalation_form() {
        $escalation['booking_id'] = $this->input->post('booking_id');
        $escalation['vendor_id'] = $this->input->post('vendor_id');
        $checkValidation = $this->checkValidationOnReason();
        if ($checkValidation) {
            $escalation['escalation_reason'] = $this->input->post('escalation_reason_id');
            $booking_date_timeslot = $this->vendor_model->getBookingDateFromBookingID($escalation['booking_id']);

            $booking_date = strtotime($booking_date_timeslot[0]['booking_date']);

            $escalation['booking_date'] = date('Y-m-d', $booking_date);
            $escalation['booking_time'] = $booking_date_timeslot[0]['booking_timeslot'];
            //inserts vendor escalation details
            $escalation_id = $this->vendor_model->insertVendorEscalationDetails($escalation);

            if ($escalation_id) {
                $escalation_policy_details = $this->vendor_model->getEscalationPolicyDetails($escalation['escalation_reason']);
                // Update escalation flag and return userDeatils
                $userDetails = $this->vendor_model->updateEscalationFlag($escalation_id, $escalation_policy_details, $escalation['booking_id']);

                log_message('info', "User Details " . print_r($userDetails));
                log_message('info', "Vendor_ID " . $escalation['vendor_id']);

                $vendorContact = $this->vendor_model->getVendorContact($escalation['vendor_id']);

                $return_mail_to = $this->getMailTo($escalation_policy_details, $vendorContact);

                if ($return_mail_to != "") {

                    $this->notify->sendEmail('booking@247around.com', $return_mail_to, '', '', $escalation_policy_details[0]['mail_subject'], $escalation_policy_details[0]['mail_body'], '');
                }

                $this->sendSmsToVendor($escalation,$escalation_policy_details, $vendorContact, $escalation['booking_id'], $userDetails);

                //$output = "Vendor Escalation Process Completed.";
                //$userSession = array('success' => $output);
                //$this->session->set_userdata($userSession);
                redirect(base_url() . DEFAULT_SEARCH_PAGE);
	    }
        } else {
            $this->get_vendor_escalation_form($escalation['booking_id']);
        }
    }

    /**
     * @desc: Send SMS to Vendor and Owner when flag of sms to owner and sms to vendor is 1.
     *
     * @param : escalation policy details
     * @param : vendor contact
     * @param : booking id
     * @param : user's details
     * @return : void
     */
    function sendSmsToVendor($escalation,$escalation_policy, $contact, $booking_id, $userDetails) {
        
        $id = $escalation['vendor_id'];
       
        if ($escalation_policy[0]['sms_to_owner'] == 1 && $escalation_policy[0]['sms_to_poc'] == 1) {

            $smsBody = $this->replaceSms_body($escalation_policy[0]['sms_body'], $booking_id, $userDetails);

            $sms_details = $this->notify->sendTransactionalSms($contact[0]['primary_contact_phone_1'], $smsBody);
            //For saving SMS to the database on sucess
            if(isset($sms_details['info']) && $sms_details['info'] == '200'){
                $this->notify->add_sms_sent_details($id, 'vendor' , $contact[0]['primary_contact_phone_1'],
                    $smsBody, $booking_id);
            }

            $sms_details = $this->notify->sendTransactionalSms($contact[0]['owner_phone_1'], $smsBody);
            //For saving SMS to the database on sucess
            if(isset($sms_details['info']) && $sms_details['info'] == '200'){
                $this->notify->add_sms_sent_details($id, 'vendor' , $contact[0]['owner_phone_1'],
                    $smsBody, $booking_id);
            }
        } else if ($escalation_policy[0]['sms_to_owner'] == 0 && $escalation_policy[0]['sms_to_poc'] == 1) {

            $smsBody = $this->replaceSms_body($escalation_policy[0]['sms_body'], $booking_id, $userDetails);

            $sms_details = $this->notify->sendTransactionalSms($contact[0]['primary_contact_phone_1'], $smsBody);
            //For saving SMS to the database on sucess
            if(isset($sms_details['info']) && $sms_details['info'] == '200'){
                $this->notify->add_sms_sent_details($id, 'vendor' , $contact[0]['primary_contact_phone_1'],
                    $smsBody, $booking_id);
            }
        } else if ($escalation_policy[0]['sms_to_owner'] == 1 && $escalation_policy[0]['sms_to_poc'] == 0) {

            $smsBody = $this->replaceSms_body($escalation_policy[0]['sms_body'], $booking_id, $userDetails);

            $sms_details = $this->notify->sendTransactionalSms($contact[0]['owner_phone_1'], $smsBody);
            //For saving SMS to the database on sucess
            if(isset($sms_details['info']) && $sms_details['info'] == '200'){
                $this->notify->add_sms_sent_details($id, 'vendor' , $contact[0]['owner_phone_1'],
                    $smsBody, $booking_id);
        }
            
    }
    }

    /**
     * @desc: Send SMS to Vendor and Owner when flag of sms to owner and sms to vendor is 1.
     *
     * @param : sms template
     * @param : booking id
     * @param : user's details
     * @return : sms body
     */
    function replaceSms_body($template, $booking_id, $userDetails) {

        $smsBody = sprintf($template, $userDetails[0]['name'], $userDetails[0]['phone_number'], $booking_id);

        return $smsBody;
    }

    /**
     * @desc: Get Email id of owner and vendor when flag is 1.
     *
     * @param : escalation policy details(mail to owner, mail to poc, etc)
     * @param : email details(primary contact email, owner email, etc)
     * @return : mailto(to whome the mail is to be sent)
     */
    function getMailTo($escalation_policy, $mailDetails) {
        $to = "";

        if ($escalation_policy[0]['mail_to_owner'] == 1 && $escalation_policy[0]['mail_to_poc'] == 1) {

            $to .= $mailDetails[0]['primary_contact_email'] . "," . $mailDetails[0]['owner_email'];
        } else if ($escalation_policy[0]['mail_to_owner'] == 0 && $escalation_policy[0]['mail_to_poc'] == 1) {

            $to .= $mailDetails[0]['primary_contact_email'];
        } else if ($escalation_policy[0]['mail_to_owner'] == 1 && $escalation_policy[0]['mail_to_poc'] == 0) {

            $to .= $mailDetails[0]['owner_email'];
        }

        return $to;
    }

    /**
     * @desc: This function is to check validation on escalation reason
     *
     * @param : void
     * @return : true if validation is true else false
     */
    function checkValidationOnReason() {
        $this->form_validation->set_rules('escalation_reason_id', 'Escalation Reason', 'required');

        if ($this->form_validation->run() == FALSE) {
            return FALSE;
        } else {
            return true;
        }
    }

    /**
     * @desc: Get District of custom State and echo in 'select option value' to load in a form
     *
     * Function also called through Ajax
     *
     * @param : flag (its value determines weather a disrtict is covered by our service centers or not)
     * @return : displays districts to the view
     */
    function getDistrict($flag = "") {
        $state = $this->input->post('state');
        $dis = $this->input->post('district');

        if ($flag == "") {
            $data = $this->vendor_model->getDistrict($state);
        } else {
            $data = $this->vendor_model->getDistrict_from_india_pincode($state);
        }

        if ($dis == "") {
            echo "<option selected='selected' value=''>Select City</option>";
        } else {
            echo "<option value=''>Select City</option>";
        }
        foreach ($data as $district) {
            if (strtolower(trim($dis)) == strtolower(trim($district['district']))) {
                echo "<option selected value='$district[district]'>$district[district]</option>";
            } else {
                echo "<option value='$district[district]'>$district[district]</option>";
            }
        }
    }

    /**
     * @desc: Get Pincode of Custom District and print 'select option value with data' to load in a form
     *
     * Function also called through Ajax.
     *
     * If flag is empty it will give all the pincodes where we have active vendors
     *      else it will give all the pincodes of India.
     *
     * @param : flag (its value determines the pincode)
     * @return : displays pincode to the view
     */
    function getPincode($flag = "") {
        $district = $this->input->post('district');
        $pin = $this->input->post('pincode');
        if ($flag == "") {
            $data = $this->vendor_model->getPincode($district);
        } else {
            $data = $this->vendor_model->getPincode_from_india_pincode($district);
        }
        if (empty($pin)) {
            echo "<option selected='selected' disabled='disabled'>Select Pincode</option>";
        }
        foreach ($data as $pincode) {
            if ($pin == $pincode['pincode']) {
                echo "<option selected value='$pincode[pincode]'>$pincode[pincode]</option>";
            } else {
                echo "<option value='$pincode[pincode]'>$pincode[pincode]</option>";
            }
        }
    }

    /**
     * @desc: Function checks the availability of vendors active in that pincode
     *
     * @param : void
     * @return : Array of pincode to the view
     */
    function vendor_availability_form() {
        $data = $this->vendor_model->get_services_category_city_pincode();
        $this->load->view('employee/header');
        $this->load->view('employee/searchvendor', $data);
    }

    /**
     * @desc: Function checks the availability of vendors in pincodes where we
     *        are active and vendors are active as well.
     *
     *  Function called through AJAX.
     * @param : void
     * @return : Array of active vendor names
     * @return : Array of active pincodes
     * @return : Array of active areas
     */
    function check_availability_for_vendor() {
        $data['service_id'] = $this->input->post('service_id');
        $data['city'] = $this->input->post('city');
        $data['pincode'] = $this->input->post('pincode');
        $vendor['vendor'] = $this->vendor_model->getVendorFromVendorMapping($data);
        $this->load->view('employee/searchvendor', $vendor);
    }

    /**
     * @desc: Function sends email with the excel sheet with latest pincode list.
     *
     * @param : void
     * @return : displays the view with message as mail sent.
     */
    function send_email_with_latest_pincode_excel() {
        $to = $this->input->post('email');
        $notes = $this->input->post('notes');
        $attachment = $this->input->post('fileUrl');
        $this->notify->sendEmail("booking@247around.com", $to, '', '', 'Pincode Changes', $notes, $attachment);
        echo '<div class="alert alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span ;aria-hidden="true">&times;</span>
                    </button>
                    <strong> Mail Sent </strong>
                </div>';
    }

    /**
     * @desc: Function loads a view to check vendors performance
     *
     * @param : void
     * @return : loads the view
     */
    function vendor_performance_view() {
        $data = $this->vendor_model->get_vendor_city_appliance();
        $this->load->view('employee/header');
        $this->load->view('employee/vendorperformance', $data);
    }

    /**
     * @desc: Function displays the vendors performance.
     *
     * This shows the total bookings assigned to a particular vendor, and what is the booking
     *      completion and cancelation reason for the particular vendor.
     *
     * We can select vendor's city, source of booking, and the time period for which we
     *      want to view vvendors performance.
     *
     * @param : void
     * @return : loads the results to view(Array of data)
     */
    function vendor_performance() {
        $vendor['vendor_id'] = $this->input->post('vendor_id');
        $vendor['city'] = $this->input->post('city');
        $vendor['service_id'] = $this->input->post('service_id');
        $vendor['period'] = $this->input->post('period');
        $vendor['source'] = $this->input->post('source');
        $vendor['sort'] = $this->input->post('sort');
        $data['data'] = $this->vendor_model->get_vendor_performance($vendor);
        $result = $this->load->view('employee/vendorperformance', $data);
        print_r($result);
    }

    /**
     * @desc: This function helps to review the bookings
     *
     * Shows mainly the charges collected by vendors on completing the booking and
     *      closing remarks mentioned by vendors.
     *
     * @param : void
     * @return : loads the view with booking charges(Array of charges)
     */
    function review_bookings() {
        $charges['charges'] = $this->vendor_model->getbooking_charges();
        $this->load->view('employee/header');
        $this->load->view('employee/review_booking_complete_cancel', $charges);
    }

    /**
     * @desc: get cancellation reation for specific vendor id
     * @param: void
     * @return: void
     */
    function getcancellation_reason($vendor_id) {
        $reason['reason'] = $this->vendor_model->getcancellation_reason($vendor_id);
        $this->load->view('employee/header');
        $this->load->view('employee/vendor_cancellation_reason', $reason);
    }

    /**
     * @desc: get form to send mail to specific vendor
     * @param: void
     * @return: vendor's list to view
     */
    function get_mail_vendor($vendor_id = "") {
        $vendor_info = $this->vendor_model->viewvendor($vendor_id);

        $this->load->view('employee/header');

        $this->load->view('employee/mail_vendor', array('vendor_info' => $vendor_info));
    }

    /**
     * @desc: sends mail to specific vendor
     *
     * Mail will be sent to the owner and POC1
     *
     * @param: void
     * @return: void
     */
    function process_mail_vendor() {
        $id = $this->input->post('vendor_id');
        $vendor_info = $this->vendor_model->viewvendor($id);
        $to = $vendor_info[0]['owner_email'] . ', ';
        $to .= $vendor_info[0]['primary_contact_email'];
        $cc = 'anuj@247around.com, nits@247around.com';
        $subject = $this->input->post('subject');
        $raw_message = $this->input->post('mail_body');

        //to replace new lines in line breaks for html
        $message = nl2br($raw_message);
        $bcc = "";
        $attachment = "";

        $this->notify->sendEmail("sales@247around.com", $to, $cc, $bcc, $subject, $message, $attachment);

        $this->load->view('employee/header');
        $this->load->view('employee/viewvendor', array('query' => $vendor_info));
    }
    /**
     * @desc: This method loads add engineer view. It gets active vendor and appliance to display in a form
     * This  function is used by vendor panel and admin panel to load add engineer view
     */
    function add_engineer(){
        $data['service_center'] = $this->vendor_model->getactive_vendor();
        $data['services'] = $this->booking_model->selectservice();
        if($this->session->userdata('userType') == 'service_center'){

            $this->load->view('service_centers/header');
            $this->load->view('service_centers/add_engineer', $data);

        } else {
            $this->load->view('employee/header');
            $this->load->view('employee/add_engineer', $data);
        }

    }
    /**
     * @desc: This is used to Edit Engineer
     * params: Engineer ID
     * return : View of Engineer along with Engineer Data Array
     */
    function get_edit_engineer_form($id){
        $data['service_center'] = $this->vendor_model->getactive_vendor();
        $data['services'] = $this->booking_model->selectservice();
        if(!empty($id)){
            $data['data'] = $this->vendor_model->get_engg_by_id($id); 
        }
        if($this->session->userdata('userType') == 'service_center'){

            $this->load->view('service_centers/header');
            $this->load->view('service_centers/add_engineer', $data);

        } else {
            $this->load->view('employee/header');
            $this->load->view('employee/add_engineer', $data);
        }
    }
    /**
     * @desc: This method adds engineers for a service center.
     *  This  function is used by vendor panel and admin panel to load add engineer details.
     */
    function process_add_engineer() {
	$engineer_form_validation = $this->engineer_form_validation();

	if ($engineer_form_validation) {
	    $data['name'] = $this->input->post('name');
	    $data['phone'] = $this->input->post('phone');
	    $data['alternate_phone'] = $this->input->post('alternate_phone');
	    $data['phone_type'] = $this->input->post('phone_type');
	    //$data['address'] = $this->input->post('address');
	    $data['identity_proof'] = $this->input->post('identity_proof');
	    $data['identity_proof_number'] = $this->input->post('identity_id_number');
	    $data['bank_name'] = $this->input->post('bank_name');
	    $data['bank_ac_no'] = $this->input->post('bank_account_no');
	    $data['bank_ifsc_code'] = $this->input->post('bank_ifsc_code');
	    $data['bank_holder_name'] = $this->input->post('bank_holder_name');
	    $data['identity_proof_pic'] = $this->input->post('file');
	    $data['bank_proof_pic'] = $this->input->post('bank_proof_pic');
	    //
	    //Get vendor ID from session if form sent thru vendor CRM
	    //Else from POST variable.
	    if ($this->session->userdata('userType') == 'service_center') {
		$data['service_center_id'] = $this->session->userdata('service_center_id');
	    } else {
		$data['service_center_id'] = $this->input->post('service_center_id');
	    }

	    //applicable services for an engineer come as array in service_id field.
	    $service_id = $this->input->post('service_id');
	    $services = array();
	    foreach ($service_id as $id) {
		array_push($services, array('service_id' => $id));
	    }

	    $data['appliance_id'] = json_encode($services);
	    $data['active'] = "1";
	    $data['create_date'] = date("Y-m-d H:i:s");

	    $engineer_id = $this->vendor_model->insert_engineer($data);

	    if ($engineer_id) {
		log_message('info', __METHOD__ . "=> Engineer Details Added.");

		$output = "Engineer Details Added.";
		$userSession = array('success' => $output);
	    } else {
		log_message('info', __METHOD__ . "=> Engineer Details Not Added. Engineer data  " . print_r($data, true));

		$output = "Engineer Details Not Added.";
		$userSession = array('error' => $output);
	    }

	    $this->session->set_userdata($userSession);

	    if ($this->session->userdata('userType') == 'service_center') {
		log_message('info', __FUNCTION__ . " Engineer addition initiated By Service Center");

		redirect(base_url() . "service_center/add_engineer");
	    } else {
		log_message('info', __FUNCTION__ . " Engineer addition initiated By 247around");

		redirect(base_url() . "employee/vendor/add_engineer");
	    }
	} else { //form validation failed
	    $this->add_engineer();
	}
    }
    /**
     * @desc: This method is used to process edit engineer form
     * params: Post data array
     * 
     */
    function process_edit_engineer(){
        $engineer_form_validation = $this->engineer_form_validation();
        $engineer_id = $this->input->post('id');
        if ($engineer_form_validation) {
	    $data['name'] = $this->input->post('name');
	    $data['phone'] = $this->input->post('phone');
	    $data['alternate_phone'] = $this->input->post('alternate_phone');
	    $data['phone_type'] = $this->input->post('phone_type');
	    $data['identity_proof'] = $this->input->post('identity_proof');
	    $data['identity_proof_number'] = $this->input->post('identity_id_number');
	    $data['bank_name'] = $this->input->post('bank_name');
	    $data['bank_ac_no'] = $this->input->post('bank_account_no');
	    $data['bank_ifsc_code'] = $this->input->post('bank_ifsc_code');
	    $data['bank_holder_name'] = $this->input->post('bank_holder_name');
            if($this->input->post('file')){
	    $data['identity_proof_pic'] = $this->input->post('file');
            }
            if($this->input->post('bank_proof_pic')){
	    $data['bank_proof_pic'] = $this->input->post('bank_proof_pic');
            }
            
	    //Get vendor ID from session if form sent thru vendor CRM
	    //Else from POST variable.
	    if ($this->session->userdata('userType') == 'service_center') {
		$data['service_center_id'] = $this->session->userdata('service_center_id');
	    } else {
		$data['service_center_id'] = $this->input->post('service_center_id');
	    }

	    //applicable services for an engineer come as array in service_id field.
	    $service_id = $this->input->post('service_id');
	    $services = array();
	    foreach ($service_id as $id) {
		array_push($services, array('service_id' => $id));
	    }

	    $data['appliance_id'] = json_encode($services);
	    $data['update_date'] = date("Y-m-d H:i:s");
            
            $where = array('id' => $engineer_id );
	    $engineer_id = $this->vendor_model->update_engineer($where,$data);

                log_message('info', __METHOD__ . "=> Engineer Details Added.");

		$output = "Engineer Details Updated.";
		$userSession = array('update_success' => $output);

	    $this->session->set_userdata($userSession);

	    if ($this->session->userdata('userType') == 'service_center') {
		log_message('info', __FUNCTION__ . " Engineer updation initiated By Service Center ID ". $engineer_id);

		redirect(base_url() . "employee/vendor/get_engineers");
	    } else {
		log_message('info', __FUNCTION__ . " Engineer updation initiated By 247around ID ". $engineer_id);

		redirect(base_url() . "employee/vendor/get_engineers");
	    }
	} else { //form validation failed
            $output = "Engineer Updation Error.";
            $userSession = array('update_error' => $output);
            $this->session->set_userdata($userSession);
            
	    $this->get_edit_engineer_form($engineer_id);
	}
    }

    /**
     * @desc: This is used to view engineers details. This function is used by vendor panel and admin panel,
     * If it used by vendor panel then it gets only particular vendor engineer's otherwise get all engineer
     */

    function get_engineers(){
        $service_center_id = "";
        if($this->session->userdata('userType') == 'service_center'){

            $service_center_id = $this->session->userdata('service_center_id');
            log_message('info', __FUNCTION__ . " view service center Engineer View  " . print_r($service_center_id, true));
        }

       $data['engineers'] =  $this->vendor_model->get_engineers($service_center_id);
       foreach ($data['engineers'] as $key => $value) {
           $service_center = $this->vendor_model->getActiveVendor($value['service_center_id']);
           $data['engineers'][$key]['service_center_name'] = $service_center[0]['name'];
           $service_id  = json_decode($value['appliance_id'],true);
           $appliances = array();
           foreach ($service_id as  $values) {
                $service_name = $this->booking_model->selectservicebyid($values['service_id']);
                array_push($appliances, $service_name[0]['services']);
           }

           $data['engineers'][$key]['appliance_name'] = implode(",", $appliances);
       }
       if($this->session->userdata('userType') == 'service_center'){

            $this->load->view('service_centers/header');
            $this->load->view('service_centers/view_engineers', $data);

       } else {
            $this->load->view('employee/header');
            $this->load->view('employee/view_engineers', $data);
       }

    }

    /**
     * @desc: this Method deactivate/ activate engineer
     */
     function change_engineer_activation($engineer_id, $active){
        log_message('info', __FUNCTION__ . " Activate/Deactivate Engineer Id:  " . $engineer_id .
	    "status: " . $active);

	$where  = array('id' => $engineer_id );
        $this->vendor_model->update_engineer($where, array('active'=> $active));
        if($this->session->userdata('userType') == 'service_center'){

           redirect(base_url()."service_center/get_engineers");

       } else {

            redirect(base_url()."service_center/get_engineers");
       }

     }
     /**
      * @desc: Delete Engineer from database
      */
    function delete_engineer($engineer_id){

        log_message('info', __FUNCTION__ . " Delete Engineer Id:  " . print_r($engineer_id, true));
        $where  = array('id' => $engineer_id );
        $this->vendor_model->update_engineer($where, array('delete'=> '1'));

        if($this->session->userdata('userType') == 'service_center'){

           redirect(base_url()."service_center/get_engineers");

       } else {

            redirect(base_url()."service_center/get_engineers");
       }

    }
    /**
     * @desc: This is used to validate engineer details form And also used to upload images
     */
    function engineer_form_validation(){

        $this->form_validation->set_rules('name', 'Name', 'required|xss_clean');
        $this->form_validation->set_rules('phone', 'Mobile Number', 'trim|exact_length[10]|numeric|required|xss_clean');
        $this->form_validation->set_rules('alternate_phone', 'Alternate Mobile Number', 'trim|exact_length[10]|numeric|xss_clean');
        $this->form_validation->set_rules('identity_id_number', 'ID Number', 'xss_clean');
        $this->form_validation->set_rules('identity_proof', 'Identity Proof', 'xss_clean');
        $this->form_validation->set_rules('bank_account_no', 'Bank Account No', 'numeric|required|xss_clean');
//        $this->form_validation->set_rules('address', 'Address', 'xss_clean');
	    $this->form_validation->set_rules('service_id', 'Appliance ', 'xss_clean');
        $this->form_validation->set_rules('bank_name', 'Bank Name', 'required|xss_clean');
        $this->form_validation->set_rules('bank_ifsc_code', 'IFSC Code', 'required|xss_clean');
        $this->form_validation->set_rules('bank_holder_name', 'Account Holder Name', 'required|xss_clean');
        $this->form_validation->set_rules('file', 'Identity Proof Pic ', 'callback_upload_identity_proof_pic');
	    $this->form_validation->set_rules('bank_proof_pic', 'Bank Proof Pic', 'callback_upload_bank_proof_pic');

	if ($this->form_validation->run() == FALSE) {
            return FALSE;
        }
        else {
            return true;
        }
    }

    /**
     * @desc: This is used to upload Bank Proof Image and return true/false depending on result
     */
    public function upload_bank_proof_pic() {
	$allowedExts = array("png", "jpg", "jpeg", "JPG", "JPEG", "PNG", "PDF", "pdf");
	$temp = explode(".", $_FILES["bank_proof_pic"]["name"]);
	$extension = end($temp);
	//$filename = prev($temp);

	if ($_FILES["bank_proof_pic"]["name"] != null) {
	    if (($_FILES["bank_proof_pic"]["size"] < 2e+6) && in_array($extension, $allowedExts)) {
		if ($_FILES["bank_proof_pic"]["error"] > 0) {
		    $this->form_validation->set_message('upload_bank_proof_pic', $_FILES["bank_proof_pic"]["error"]);
		} else {
		    $pic = str_replace(' ', '-', $this->input->post('name')) . "_" . str_replace(' ', '', $this->input->post('bank_name')) . "_" . uniqid(rand());
		    $picName = $pic . "." . $extension;
		    $_POST['bank_proof_pic'] = $picName;
		    $bucket = "bookings-collateral";
		    $directory = "engineer-bank-proofs/" . $picName;
		    $this->s3->putObjectFile($_FILES["bank_proof_pic"]["tmp_name"], $bucket, $directory, S3::ACL_PUBLIC_READ);

		    return TRUE;
		}
	    } else {
		$this->form_validation->set_message('upload_bank_proof_pic', 'File size or file type is not supported. Allowed extentions are "png", "jpg", "jpeg" and "pdf". '
		    . 'Maximum file size is 2 MB.');
		return FALSE;
	    }
	}
    }

    /**
     * @desc: This is used to upload ID Proof Image and return true/false depending on result
     */
    public function upload_identity_proof_pic() {
	$allowedExts = array("png", "jpg", "jpeg", "JPG", "JPEG", "PNG", "PDF", "pdf");
	$temp = explode(".", $_FILES["file"]["name"]);
	$extension = end($temp);
	//$filename = prev($temp);

	if ($_FILES["file"]["name"] != null) {
	    if (($_FILES["file"]["size"] < 2e+6) && in_array($extension, $allowedExts)) {
		if ($_FILES["file"]["error"] > 0) {
		    $this->form_validation->set_message('upload_identity_proof_pic', $_FILES["file"]["error"]);
		} else {
		    $pic = str_replace(' ', '-', $this->input->post('name')) . "_" . str_replace(' ', '', $this->input->post('identity_proof')) . "_" . uniqid(rand());
		    $picName = $pic . "." . $extension;
		    $_POST['file'] = $picName;
		    $bucket = "bookings-collateral";
		    $directory = "engineer-id-proofs/" . $picName;
		    $this->s3->putObjectFile($_FILES["file"]["tmp_name"], $bucket, $directory, S3::ACL_PUBLIC_READ);

		    return TRUE;
		}
	    } else {
		$this->form_validation->set_message('upload_identity_proof_pic', 'File size or file type is not supported. Allowed extentions are "png", "jpg", "jpeg" and "pdf". '
		    . 'Maximum file size is 2 MB.');
		return FALSE;
	    }
	}
    }

     /**
     *  @desc : This function is used to Add Vendor for a particular Pincode
     *  
     *  It is being called using AJAX request.
     * 
     *  @param : POST data like Pincode, appliance, appliance ID, city, brand
      *         or Empty for New entry of Vendor Pincode Mapping
     *  @return : Mixed  
     *           print variable storing view of Vendor Pincode Form.
     */

    function get_add_vendor_to_pincode_form(){
        $data = array();
        if(!empty($this->input->post())){
        $booking_data  = $this->booking_model->getbooking_history($this->input->post('booking_id'));
        $data['pincode'] = $booking_data[0]['booking_pincode'];
        $data['Appliance'] = $booking_data[0]['services'];
        $data['Appliance_ID'] = $booking_data[0]['service_id'];
        $data['brand'] = $booking_data[0]['appliance_brand'];
        $data['city'] = $booking_data[0]['city'];
        
        //Getting data from Database using Booking ID

            $data['vendors'] = $this->vendor_model->get_distinct_vendor_details($data['Appliance_ID']);
            $data['state'] = $this->vendor_model->getall_state();
        
        //Loading view in $data for parsin response in Ajax success
        $data = $this->load->view('employee/add_vendor_to_pincode', $data, TRUE);
        print_r($data);
        }else{
            
            $data['vendor_details'] = $this->vendor_model->getActiveVendor();
            $data['state'] = $this->vendor_model->getall_state();
            // Return view for adding of New Vendor to Pincode
            $this->load->view('employee/header');
            $this->load->view('employee/add_vendor_to_pincode',$data);
        }

    }

    /**
     *  @desc : This function is used to Process Add Vendor to pincode Form
     *  @param : Array of $_POST data
     *  @return : void
     */

    function process_add_vendor_to_pincode_form(){
        //Getting Post data
        if($this->input->post()){
            //Adding Validation Rules
            $this->form_validation->set_rules('pincode', 'Pincode', 'trim|required|numeric|min_length[6]|max_length[6]');
            $this->form_validation->set_rules('city', 'City', 'trim|required');
            $this->form_validation->set_rules('area', 'Area', 'trim|required');
            $this->form_validation->set_rules('state', 'State', 'trim|required');
            $this->form_validation->set_rules('vendor_id', 'Vendor Id', 'required');
            $this->form_validation->set_rules('brand', 'Brand', 'trim|required');
            $this->form_validation->set_rules('choice', 'Services', 'required');

            //Check for Validation
            if ($this->form_validation->run() == FALSE) {

            $data = $this->input->post();
            
            $data['vendor_details'] = $this->vendor_model->getActiveVendor();
            $data['state'] = $this->vendor_model->getall_state();    
            
            $this->load->view('employee/header');
            $this->load->view('employee/add_vendor_to_pincode', $data);

            }else{

            $choice = $this->input->post('choice');

            $vendor_mapping = array(

                                'Vendor_ID'=>$this->input->post('vendor_id'),
                                'Brand'=>$this->input->post('brand'),
                                'Pincode'=>$this->input->post('pincode'),
                                'Region'=>'Region',
                                'Area'=>$this->input->post('area'),
                                'City'=>$this->input->post('city'),
                                'State'=>$this->input->post('state'),
                                'create_date'=>date('Y-m-d h:m:i'),
                                'active'=>1
                );
            //Looping through Appliance's Selected
            foreach ($choice as $key => $value) {
                    //Getting Appliance Name
                    $appliance = $this->booking_model->selectservicebyid($value);
                    //Getting Vendor Name
                    $vendor_name = $this->vendor_model->getActiveVendor($this->input->post('vendor_id'));
                    //Appending Array
                    $vendor_mapping['Vendor_Name'] = $vendor_name[0]['name'];
                    $vendor_mapping['Appliance'] = $appliance[0]['services'];
                    $vendor_mapping['Appliance_ID'] = $value;

                    //Checking for already entered Record
                    $result = $this->vendor_model->check_vendor_details($vendor_mapping);

                    if($result){
                        //Setting Flash data on success
                        $this->session->set_flashdata('success', 'Pincode Mapped to Vendor successfully.');

                          //Insert Data in vendor_pincode_mapping table
                        $vendor_id = $this->vendor_model->insert_vendor_pincode_mapping($vendor_mapping);
                        if(!empty($vendor_id)){
                            //Logging
                            log_message('info',__FUNCTION__.'Vendor assigned to Pincode in vendor_picode_mapping table. '.print_r($vendor_mapping,TRUE));
                        }else{
                            //Logging
                            log_message('info',__FUNCTION__.' Error in adding vendor to pincode in vendor_pincode_mapping table '.print_r($vendor_mapping,TRUE));
                        }
                        
                        //Replicating data in 247Aroung_vendor_pincode_mapping table
                        $_247_vendor_id = $this->vendor_model->insert_247Around_vendor_pincode_mapping($vendor_mapping);
                        if(!empty($_247_vendor_id)){
                            //Logging
                            log_message('info',__FUNCTION__.'Vendor assigned to Pincode in 247Around_vendor_pincode_mapping table. '.print_r($vendor_mapping,TRUE));
                        }else{
                            //Logging
                            log_message('info',__FUNCTION__.'Error in addding vendor to pincode in 247Around_vendor_pincode_mapping table '.print_r($vendor_mapping,TRUE));
                        }

                    } else {
                        //Echoing duplicay error in Log file
                        log_message('info',__FUNCTION__.'Vendor already assigned to '.$vendor_mapping['Appliance'] );
                        //Setting Flash variable on Error
                        $this->session->set_flashdata('error','Vendor already assigned to '.$vendor_mapping['Appliance']  );
                    }
            }


            //redirect(site_url('employee/booking/view_queries/FollowUp'));
            redirect(base_url() . DEFAULT_SEARCH_PAGE);

            }

        }
    }

     /**
     *  @desc : This function is used get vendor services based on vendor id
     * Call: This function is called using AJAX from Vendor Pincode adding form.
     *  @param : Vendor ID
     *  @return : JSON
     */

     function get_vendor_services($vendor_id){
        //Getting  distinct vendor service details from Vendor Mapping table
        $vendor_services = $this->vendor_model->get_distinct_vendor_service_details($vendor_id);

        foreach ($vendor_services as $key => $value) {
            $data['Appliance'][] = $value['Appliance'];
            $data['Appliance_ID'][] = $value['Appliance_ID'];
        }
        //Returning data in Json Encoded form
        print_r(json_encode($data));

     }

    /**
     *  @desc : This function is used to Delete assigned vendor to vendor_pincode_mapping
     *          and  process form data
     *  @param : void
     *  @return : array
    */
      function process_vendor_pincode_delete_form() {

	$data = array();
	//Getting data from database
	$data['vendor_details'] = $this->vendor_model->getActiveVendor();
	$data['appliance'] = $this->booking_model->selectservice();
	$data['state'] = $this->vendor_model->getall_state();

	//Process Form
	if ($this->input->post()) {
	    if (!empty($this->input->post('service_id')[0])) {
		$service_id = $this->input->post('service_id');

		foreach ($service_id as $key => $value) {
		    if (!empty($value)) {

			$data_post = array(
			    'Appliance_ID' => $value,
			    'Pincode' => $this->input->post('pincode')[$key],
			    'Vendor_ID' => $this->input->post('vendor_id')[$key]
			);

			//Deleting data
			if ($this->vendor_model->delete_vendor($data_post) == '1') {
			    //Echoing ID to log file
			    log_message('info', __FUNCTION__ . ' Vendor has been deleted in Vendor_Pincode_Mapping table. ' . print_r($data_post, TRUE));

			    $data['delete'] = TRUE;
			} else {
			    log_message('info', __FUNCTION__ . ' Following pincode NOT found in Vendor_Pincode_Mapping table =  ' . $this->input->post('pincode')[$key]);

			    $data['not_found'][] = $this->input->post('pincode')[$key];
			}
		    }
		}
	    } else {

		$data['no_input'] = '';
	    }
	}
	$this->load->view('employee/header');
	$this->load->view('employee/list_vendor_pincode', $data);
    }
    
    /**
     * @desc: This method is used to send mail with Vendor Pincode Mapping file.
     * This is called by Ajax. It gets email and notes by form. Pass it to asynchronous method.
     * @param: void
     * @return: print success
     */
    function download_latest_pincode_excel(){

        log_message('info', __FUNCTION__);

        $mail['email'] = $this->input->post('email');
        $mail['notes'] = $this->input->post('notes');
        $url = base_url() . "employee/do_background_process/download_latest_pincode_excel";
        $this->asynchronous_lib->do_background_process($url, $mail);
        echo '<div class="alert alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span ;aria-hidden="true">&times;</span>
                    </button>
                    <strong> Excel file will be Send to mail. </strong>
                </div>';

    }

        /**
     * @desc: This function to send mails to vendors
     * @param: void
     * @return : void
     */
    function get_mail_to_vendors_form() {
        $data = array();
        $data['vendors'] = $this->vendor_model->getActiveVendor();
        $data['partners'] = $this->partner_model->getpartner();

        //Declaring array for modal call to get_247around_email_template function
        //For vendors
        $email = array();
        $email['where'] = array(
            'entity' => 'vendor'
        );
        $email['select'] = 'id,template,subject';
        $data['email_template'] = $this->vendor_model->get_247around_email_template($email);
        
        //For partners
        $partner_email = array();
        $partner_email['where'] = array(
            'entity' => 'partner'
        );
        $partner_email['select'] = 'id,template,subject';
        $data['partner_email_template'] = $this->vendor_model->get_247around_email_template($partner_email);
        
        
        $this->load->view('employee/header');
        $this->load->view('employee/sendemailtovendor', $data);
    }

    /**
     * @desc: This function is used to send mails to the selected vendor along with emails 
     *        It is being called using AJAX
     * params: $_FILES, POST array
     * return: void
     * 
     */
    function process_mail_to_vendor($id) {
        //Setting flag as TRUE ->Success and FALSE -> Failure
        $flag = TRUE;
        $attachment = "";
        //Do file upload if attachment is provided
        if (!empty($_FILES)) {
            $tmpFile = $_FILES['attachment_'.$id]['tmp_name'];
            $fileName = $_FILES['attachment_'.$id]['name'];

           move_uploaded_file($tmpFile, "/tmp/$fileName");
            // move_uploaded_file($tmpFile, "c:\users\bredkhan"."\\$fileName");
            if (!empty($fileName)) {
               $attachment = "/tmp/$fileName";
                // $attachment = "c:\users\bredkhan"."\\$fileName";

            }
        }
        if ($this->input->post()) {
            $vendors = $this->input->post('vendors');
            //Checking for ALL vendors selected
            if($vendors[0] == 0){
                $vendors_array = $this->vendor_model->getActiveVendor();
                foreach ($vendors_array as $value) {
                    $vendors_list[] = $value['id'];
                }
            }else{
                $vendors_list = $vendors;
            }
            //Get email template values
            $email = array();
            $email['where'] = array(
                'entity' => 'vendor',
                'id' => $id
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

                foreach ($vendors_list as $value) {
                    $vendor_details = $this->vendor_model->getVendorContact($value);
                    //Setting TO for Email
                    $to = $vendor_details[0]['owner_email'] . ',' . $vendor_details[0]['primary_contact_email'];

                    foreach ($template as $value) {
                        $value['id'] = $vendor_details[0]['id'];
                        //Getting vendor details
                        $vendor_data = $this->vendor_model->get_data($value);

                        if ($vendor_data) {
                            $temp[] = $vendor_data[0]['user_name'];
                        } else {
                            //Logging error when values not found
                            log_message('info', __FUNCTION__ . ' Mail send Error. No data found to the following vendor ID ' . $vendor_details[0]['id']);
                            log_message('info', __FUNCTION__ . ' Template values are - ' . print_r($value, TRUE));
                            //Set Flag to check success or error of AJAX call
                            $flag = FALSE;
                        }
                    }
                    //Sending Mail to the vendor
                    if (isset($temp)) {
                        $emailBody = vsprintf($email_template[0]['body'], $temp);
                        //Sending Mail
                        $this->notify->sendEmail($email_template[0]['from'], $to, 'belal@247around.com', '', $email_template[0]['subject'], $emailBody, $attachment);
                        //Loggin send mail details
                        log_message('info', __FUNCTION__ . ' Mail send to the following vendor ID ' . $vendor_details[0]['id']);
                        //Set Flag to check success or error of AJAX call
                        $flag = TRUE;
                    }
                }
            }
        } else {
            $flag = FALSE;
        }
        //Returning Flag value to AJAX request
            echo $flag;
    }
    
     /**
     * @desc: This function is used to send mails to the selected partner along with emails 
     *        It is being called using AJAX
     * params: $_FILES, POST array
     * return: void
     * 
     */
     function process_mail_to_partner($id) {
        //Setting flag as TRUE ->Success and FALSE -> Failure
        $flag = TRUE;
        $attachment = "";
        //Do file upload if attachment is provided
        if (!empty($_FILES)) {
            $tmpFile = $_FILES['attachment_' . $id]['tmp_name'];
            $fileName = $_FILES['attachment_' . $id]['name'];

            move_uploaded_file($tmpFile, "/tmp/$fileName");
            if (!empty($fileName)) {
                $attachment = "/tmp/$fileName";
}
        }
        if ($this->input->post()) {
            $partners = $this->input->post('partners');
            //Checking for ALL vendors selected
            if ($partners[0] == 0) {
                $partners_array = $this->partner_model->getpartner();
                foreach ($partners_array as $value) {
                    $partners_list[] = $value['id'];
                }
            } else {
                $partners_list = $partners;
            }
            //Get email template values for partners
            $email = array();
            $email['where'] = array(
                'entity' => 'partner',
                'id' => $id
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

                foreach ($partners_list as $value) {
                    $partner_details = $this->partner_model->getpartner($value);
                    //Setting TO for Email
                    $to = $partner_details[0]['owner_email'] . ',' . $partner_details[0]['primary_contact_email'];

                    foreach ($template as $value) {
                        $value['id'] = $partner_details[0]['id'];
                        //Getting vendor details
                        $partner_data = $this->vendor_model->get_data($value);

                        if ($partner_data) {
                            $temp[] = $partner_data[0]['user_name'];
                        } else {
                            //Logging error when values not found
                            log_message('info', __FUNCTION__ . ' Mail send Error. No data found to the following vendor ID ' . $partner_details[0]['id']);
                            log_message('info', __FUNCTION__ . ' Template values are - ' . print_r($value, TRUE));
                            //Set Flag to check success or error of AJAX call
                            $flag = FALSE;
                        }
                    }
                    //Sending Mail to the vendor
                    if (isset($temp)) {
                        $emailBody = vsprintf($email_template[0]['body'], $temp);
                        //Sending Mail
                        $this->notify->sendEmail($email_template[0]['from'], $to, 'belal@247around.com', '', $email_template[0]['subject'], $emailBody, $attachment);
                        //Loggin send mail details
                        log_message('info', __FUNCTION__ . ' Mail send to the following vendor ID ' . $partner_details[0]['id']);
                        //Set Flag to check success or error of AJAX call
                        $flag = TRUE;
                    }
                }
            }
        } else {
            $flag = FALSE;
        }
        //Returning Flag value to AJAX request
        echo $flag;
    }

}