<?php

if (!defined('BASEPATH')){
    exit('No direct script access allowed');
}

error_reporting(E_ALL);
ini_set('display_errors', '1');

ini_set('max_execution_time', 3600); //3600 seconds = 60 minutes

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

	    if (!empty($appliances)){
		$_POST['appliances'] = implode(",", $appliances);
            }

	    if (!empty($brands)){
		$_POST['brands'] = implode(",", $brands);
            }

	    unset($_POST['day']);

	    if (!empty($_POST['id'])) {
                //if vendor exists, details are edited
		$this->vendor_model->edit_vendor($_POST, $_POST['id']);

		redirect(base_url() . 'employee/vendor/viewvendor', 'refresh');
	    } else {
        // get service center code by calling generate_service_center_code() method
	    $_POST['sc_code'] =	$this->generate_service_center_code($_POST['name'], $_POST['district']);

                //if vendor do not exists, vendor is added
		$this->vendor_model->add_vendor($_POST);
		$this->sendWelcomeSms($_POST['primary_contact_phone_1'], $_POST['name']);
		$this->sendWelcomeSms($_POST['owner_phone_1'], $_POST['owner_name']);

		$this->notify->sendEmail("booking@247around.com", 'anuj@247around.com', '', '', 'New Vendor Creation', json_encode($_POST), "");

		redirect(base_url() . 'employee/vendor/viewvendor', 'refresh');
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
    function generate_service_center_code($sc_name, $district){
    	//generate 6 random  letter string
    	$sc_code =  substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);
    	$final_sc_code = strtoupper($sc_code); // convert string in upper case
    	$status = $this->vendor_model->check_sc_code_exist($final_sc_code);  // check service center code is exist or not
    	if($status == true){   //if sc code exists
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
    function sendWelcomeSms($phone_number, $vendor_name) {
	$template = $this->vendor_model->getVendorSmsTemplate("new_vendor_creation");
	$smsBody = sprintf($template, $vendor_name);

	$this->notify->sendTransactionalSms($phone_number, $smsBody);
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
//	$query = $this->vendor_model->activate($id);    //$query is not used here, can be deleted
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
//	$query = $this->vendor_model->deactivate($id);      //$query is not used here, can be deleted
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
//	$query = $this->vendor_model->delete($id);      //$query is not used here, can be deleted
        $this->vendor_model->delete($id);

	redirect(base_url() . 'employee/vendor/viewvendor', 'refresh');
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

	$this->load->view('employee/reassignvendor',
	array('booking_id'=>$booking_id,'service_centers' => $service_centers));
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
	$service_center = $this->input->post('service');

	if ($service_center != "Select") {
            $this->booking_model->assign_booking($booking_id, $service_center);
	    $this->vendor_model->delete_previous_service_center_action($booking_id);

	    //Setting mail to vendor flag to 0, once booking is re-assigned
	    $this->booking_model->set_mail_to_vendor_flag_to_zero($booking_id);

	     log_message('info', "Reassigned - Booking id: " . $booking_id . "  By " .
		$this->session->userdata('employee_id') . " service center id " . $service_center);

	    redirect(base_url() . 'employee/booking/view');
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
	$message = $this->input->post('mail_body');
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

	redirect(base_url() . 'employee/booking/view', 'refresh');
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
	if($error != ""){
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

    function process_pincode_excel_upload_form(){
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

	        redirect(base_url().'employee/booking/view');

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

	$data['escalation_reason'] = $this->vendor_model->getEscalationReason();
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

		$this->sendSmsToVendor($escalation_policy_details, $vendorContact, $escalation['booking_id'], $userDetails);

		//$output = "Vendor Escalation Process Completed.";
		//$userSession = array('success' => $output);
		//$this->session->set_userdata($userSession);
	    redirect(base_url().'employee/booking/view');
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
    function sendSmsToVendor($escalation_policy, $contact, $booking_id, $userDetails) {
	if ($escalation_policy[0]['sms_to_owner'] == 1 && $escalation_policy[0]['sms_to_poc'] == 1) {

	    $smsBody = $this->replaceSms_body($escalation_policy[0]['sms_body'], $booking_id, $userDetails);

	    $this->notify->sendTransactionalSms($contact[0]['primary_contact_phone_1'], $smsBody);


	    $this->notify->sendTransactionalSms($contact[0]['owner_phone_1'], $smsBody);
	} else if ($escalation_policy[0]['sms_to_owner'] == 0 && $escalation_policy[0]['sms_to_poc'] == 1) {

	    $smsBody = $this->replaceSms_body($escalation_policy[0]['sms_body'], $booking_id, $userDetails);

	    $this->notify->sendTransactionalSms($contact[0]['primary_contact_phone_1'], $smsBody);
	} else if ($escalation_policy[0]['sms_to_owner'] == 1 && $escalation_policy[0]['sms_to_poc'] == 0) {

	    $smsBody = $this->replaceSms_body($escalation_policy[0]['sms_body'], $booking_id, $userDetails);

	    $this->notify->sendTransactionalSms($contact[0]['owner_phone_1'], $smsBody);
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
        }
        else {
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
    function getDistrict($flag="") {

	$state = $this->input->post('state');
	$dis = $this->input->post('district');

	if($flag ==""){
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
    function getPincode($flag="") {
	$district = $this->input->post('district');
	$pin = $this->input->post('pincode');
	if($flag == ""){
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
    function vendor_availability_form(){
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
    function check_availability_for_vendor(){
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
    function send_email_with_latest_pincode_excel(){
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
    function vendor_performance_view(){
    	$data = $this->vendor_model->get_vendor_city_appliance();
    	$this->load->view('employee/header');
    	$this->load->view('employee/vendorperformance',$data);
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
    function vendor_performance(){
    	$vendor['vendor_id'] = $this->input->post('vendor_id');
	$vendor['city'] = $this->input->post('city');
    	$vendor['service_id'] = $this->input->post('service_id');
    	$vendor['period'] = $this->input->post('period');
	$vendor['source'] = $this->input->post('source');
    	$vendor['sort'] = $this->input->post('sort');
    	$data['data'] = $this->vendor_model->get_vendor_performance($vendor);
    	$result = $this->load->view('employee/vendorperformance',$data);
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
    function review_bookings(){
    	$charges['charges'] = $this->vendor_model->getbooking_charges();
    	$this->load->view('employee/header');
    	$this->load->view('employee/review_booking', $charges);
    }

    /**
     * @desc: get cancellation reation for specific vendor id
     * @param: void
     * @return: void
     */
    function getcancellation_reason($vendor_id){
    	$reason['reason'] = $this->vendor_model->getcancellation_reason($vendor_id);
    	$this->load->view('employee/header');
    	$this->load->view('employee/vendor_cancellation_reason', $reason);
    }

   /* function test(){
    	$post_data = array(
            'From' => "9971634265",
            'To' => "01139595200",
            'CallerId' => "01130017601",
		    //'TimeLimit' => "<time-in-seconds> (optional)",
		    //'TimeOut' => "<time-in-seconds (optional)>",
		    'CallType' => "trans" //Can be "trans" for transactional and "promo" for promotional content
        );

        $exotel_sid = "aroundhomz";
	    $exotel_token = "a041058fa6b179ecdb9846ccf0e4fd8e09104612";

        $url = "https://".$exotel_sid.":".$exotel_token."@twilix.exotel.in/v1/Accounts/".$exotel_sid."/Calls/01130017601";


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
       // curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query());

        $http_result = curl_exec($ch);
        $error = curl_error($ch);
        $http_code = curl_getinfo($ch ,CURLINFO_HTTP_CODE);

        curl_close($ch);

        print "Response = ".print_r($http_result);

    }*/
}

