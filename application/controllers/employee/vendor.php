<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

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
	$this->load->library('booking_utilities');
	$this->load->library('notify');
	$this->load->library("pagination");
	$this->load->library("session");
	$this->load->library('s3');
	$this->load->library('email');
    }


    function index() {

	$checkValidation = $this->checkValidation();
	if ($checkValidation) {

	    $non_working_days = $this->input->post('day');
	    $appliances = $this->input->post('appliances');
	    $brands = $this->input->post('brands');

	    if (!empty($non_working_days)) {

		$_POST['non_working_days'] = implode(",", $non_working_days);
	    }

	    if (!empty($appliances))
		$_POST['appliances'] = implode(",", $appliances);

	    if (!empty($brands))
		$_POST['brands'] = implode(",", $brands);

	    unset($_POST['day']);

	    if (!empty($_POST['id'])) {

		$this->vendor_model->edit_vendor($_POST, $_POST['id']);

		redirect(base_url() . 'employee/vendor/viewvendor', 'refresh');
	    } else {
        // get service center code by calling generate_service_center_code() method
	    $_POST['sc_code'] =	$this->generate_service_center_code($_POST['name'], $_POST['district']); 

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
     * @param: String(Service center name and District)
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
    // send sms to vendor and admin on new creation vendor
    function sendWelcomeSms($phone_number, $vendor_name) {
	$template = $this->vendor_model->getVendorSmsTemplate("new_vendor_creation");
	$smsBody = sprintf($template, $vendor_name);

	$this->notify->sendTransactionalSms($phone_number, $smsBody);
    }

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

    function add_vendor() {
	$results['services'] = $this->vendor_model->selectservice();
	$results['brands'] = $this->vendor_model->selectbrand();
	$results['select_state'] = $this->vendor_model->selectSate();
	$days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
	$this->load->view('employee/header');
	$this->load->view('employee/addvendor', array('results' => $results, 'days' => $days));
    }

//Function to edit vendor details
    function editvendor($id) {
	$query = $this->vendor_model->editvendor($id);

	$results['services'] = $this->vendor_model->selectservice();
	$results['brands'] = $this->vendor_model->selectbrand();
	$results['select_state'] = $this->vendor_model->selectSate();

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

    //Function to view vendor details
    function viewvendor($vendor_id = "") {
	$query = $this->vendor_model->viewvendor($vendor_id);

	$this->load->view('employee/header');

	$this->load->view('employee/viewvendor', array('query' => $query));
    }

    //Function to activate particular vendor
    function activate($id) {
	$query = $this->vendor_model->activate($id);

	redirect(base_url() . 'employee/vendor/viewvendor', 'refresh');
    }

    //Function to deactivate particular vendor
    function deactivate($id) {
	$query = $this->vendor_model->deactivate($id);

	redirect(base_url() . 'employee/vendor/viewvendor', 'refresh');
    }

    //Function to delete particular vendor
    function delete($id) {
	$query = $this->vendor_model->delete($id);

	redirect(base_url() . 'employee/vendor/viewvendor', 'refresh');
    }

    //Function to get the reassign vendor page
    function get_reassign_vendor_form($booking_id = "") {
	$service_centers = $this->booking_model->select_service_center();

	$this->load->view('employee/header');

	$this->load->view('employee/reassignvendor',
	array('booking_id'=>$booking_id,'service_centers' => $service_centers));
    }

    //Function to set the reassigned vendor
    function process_reassign_vendor_form() {
	$booking_id = $this->input->post('booking_id');
	$service_center = $this->input->post('service');

	if ($service_center != "Select") {
	    $data = $this->booking_model->assign_booking($booking_id, $service_center);

	    //Setting mail to vendor flag to 0, once booking is re-assigned
	    $this->booking_model->set_mail_to_vendor_flag_to_zero($booking_id);

	     log_message('info', "Reassigned - Booking id: " . $booking_id. "  By ". $this->session->userdata('employee_id'). " data ".print_r($data));



	    redirect(base_url() . 'employee/booking/view', 'refresh');
	} else {


	    $output = "Please select any service center.";
	    $userSession = array('error' => $output);
	    $this->session->set_userdata($userSession);
	    redirect(base_url() . 'employee/vendor/get_reassign_vendor_form/' . $booking_id, 'refresh');
	}
    }

    //Function to get form to broadcast mail to all vendors
    function get_broadcast_mail_to_vendors_form() {
	//$service_centers = $this->booking_model->select_service_center();
	$this->load->view('employee/header');
	$this->load->view('employee/broadcastemailtovendor');
    }

    //Send broadcast mail to vendors
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
     * Get Bcc email to send Broadcast mail when poc and owner flg is not empty
     * @return : Stirng
     *
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
     *  @param : void
     *  @return : displays the view
     */
    function get_pincode_excel_upload_form() {
	$mapping_file['pincode_mapping_file'] = $this->vendor_model->getLatestVendorPincodeMappingFile();
	$this->load->view('employee/header');
	$this->load->view('employee/upload_pincode_excel', $mapping_file);
    }

    function get_master_pincode_excel_upload_form() {
	$this->load->view('employee/header');
	$this->load->view('employee/upload_master_pincode_excel');
    }

    /**
     *  @desc : This function is to upload pincode through excel form
     *  @param : void
     *  @return : all the charges added to view
     */
    public function process_pincode_excel_upload_form() {
	if (!empty($_FILES['file']['name'])) {
	    $pathinfo = pathinfo($_FILES["file"]["name"]);

	    if ($pathinfo['extension'] == 'xlsx') {
		if ($_FILES['file']['size'] > 0) {
		    $inputFileName = $_FILES['file']['tmp_name'];
		    $inputFileExtn = 'Excel2007';
		}
	    } else {
		if ($pathinfo['extension'] == 'xls') {
		    if ($_FILES['file']['size'] > 0) {
			$inputFileName = $_FILES['file']['tmp_name'];
			$inputFileExtn = 'Excel5';
		    }
		}
	    }
	}
	//echo $inputFileName, EOL;

	$reader = ReaderFactory::create(Type::XLSX);
	$details_pincode['file_name'] = "Consolidated_Pin_Code" . date('d-M-Y') . ".xlsx";
	// move excel file in tmp folder.
	move_uploaded_file($inputFileName, "/tmp/" . $details_pincode['file_name']);
	$reader->open("/tmp/" . $details_pincode['file_name']);
	//echo "Inserting data from xls to db\n\n";
	if (!empty($_POST['emailID'])) {
	    $this->notify->sendEmail("booking@247around.com", $_POST['emailID'], '', '', 'Pincode Changes', $_POST['notes'], "/tmp/" . $details_pincode['file_name']);
	}

	$count = 1;
	$pincodes_inserted = 0;
	$err_count = 0;
	$header_row = FALSE;

	$rows = array();
	foreach ($reader->getSheetIterator() as $sheet) {
	    foreach ($sheet->getRowIterator() as $row) {
		if ($count > 0) {
		    if ($count % 1000 == 0) {
			if (!$header_row) {
			    //header row to be removed for the first iteration
			    array_shift($rows);

			    $header_row = TRUE;
			}

			//call insert_batch function for $rows..
			$this->vendor_model->insert_vendor_pincode_mapping_temp($rows);
			$pincodes_inserted += count($rows);
			//echo date("Y-m-d H:i:s") . "=> " . $pincodes_inserted . " pincodes added\n";
			unset($rows);
			$rows = array();

			//reset count
			$count = 0;
		    }

		    $data['Vendor_Name'] = $row[0];
		    $data['Vendor_ID'] = $row[1];
		    $data['Appliance'] = $row[2];
		    $data['Appliance_ID'] = $row[3];
		    $data['Brand'] = $row[4];
		    $data['Area'] = $row[5];
		    $data['Pincode'] = $row[6];
		    $data['Region'] = $row[7];
		    $data['City'] = $row[8];
		    $data['State'] = $row[9];

		    array_push($rows, $data);
		}
		$count++;
	    }

	    //insert remaining rows
	    $this->vendor_model->insert_vendor_pincode_mapping_temp($rows);
	    //echo date("Y-m-d H:i:s") . "=> " . ($count - 1) . " records added\n";
	    $pincodes_inserted += count($rows);
	}

	$reader->close();

	$data['error'] = $err_count;
	$data['pincode'] = $pincodes_inserted;

	if ($err_count === 0) {
	    //Drop the original pincode mapping table and rename the temp table with new pincodes mapping
	    $result = $this->vendor_model->switch_temp_pincode_table();

	    if ($result)
		$data['table_switched'] = TRUE;
	}

	$bucket = 'bookings-collateral';
	$details_pincode['bucket_name'] = "vendor-pincodes";
	$directory_xls = $details_pincode['bucket_name'] . "/" . $details_pincode['file_name'];

	// Insert file name and bucket name to S3
	$this->vendor_model->insertS3FileDetails($details_pincode);
	// Upload excel on S3
	$this->s3->putObjectFile(realpath("/tmp/" . $details_pincode['file_name']), $bucket, $directory_xls, S3::ACL_PUBLIC_READ);

	$this->load->view('employee/header');
	$this->load->view('employee/upload_pincode_excel_summary', $data);
    }

    /**
     * Load Vendor Escalation form and get escalation reason and vendor details from table.
     * @param : Booking Id
     */
    function get_vendor_escalation_form($booking_id) {

	$data['escalation_reason'] = $this->vendor_model->getEscalationReason();
	$data['vendor_details'] = $this->vendor_model->getVendor($booking_id);
	$data['booking_id'] = $booking_id;

	$this->load->view('employee/header');
	$this->load->view('employee/vendor_escalation_form', $data);
    }

    /**
     *  Insert Vendor Escalation reason in database and request a method to send sms and email to vendor
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

	    $escalation_id = $this->vendor_model->insertVendorEscalationDetails($escalation);

	    if ($escalation_id) {
		$escalation_policy_details = $this->vendor_model->getEscalationPolicyDetails($escalation['escalation_reason']);

		$this->vendor_model->updateEscalationFlag($escalation_id, $escalation_policy_details);

		$vendorContact = $this->vendor_model->getVendorContact($escalation['vendor_id']);

		$return_mail_to = $this->getMailTo($escalation_policy_details, $vendorContact);

		if ($return_mail_to != "") {

		    $this->notify->sendEmail('booking@247around.com', $return_mail_to, '', '', $escalation_policy_details[0]['mail_subject'], $escalation_policy_details[0]['mail_body'], '');
		}

		$this->sendSmsToVendor($escalation_policy_details, $vendorContact, $escalation['booking_id']);

		//$output = "Vendor Escalation Process Completed.";
		//$userSession = array('success' => $output);
		//$this->session->set_userdata($userSession);
	    		redirect(base_url().'employee/booking/view');
	    }
	} else {
	    $this->vendor_escalation_form($booking_id);
	}
    }

    /**
     *  Send SMS to Vendor and Owner when flag of sms to owner and sms to vendor is 1.
     */
    function sendSmsToVendor($escalation_policy, $contact, $booking_id) {
	if ($escalation_policy[0]['sms_to_owner'] == 1 && $escalation_policy[0]['sms_to_poc'] == 1) {

	    $smsBody = $this->replaceSms_body($escalation_policy[0]['sms_body'], $contact, $booking_id);

	    $this->notify->sendTransactionalSms($contact[0]['primary_contact_phone_1'], $smsBody);


	    $this->notify->sendTransactionalSms($contact[0]['owner_phone_1'], $smsBody);
	} else if ($escalation_policy[0]['sms_to_owner'] == 0 && $escalation_policy[0]['sms_to_poc'] == 1) {

	    $smsBody = $this->replaceSms_body($escalation_policy[0]['sms_body'], $contact, $booking_id);

	    $this->notify->sendTransactionalSms($contact[0]['primary_contact_phone_1'], $smsBody);
	} else if ($escalation_policy[0]['sms_to_owner'] == 1 && $escalation_policy[0]['sms_to_poc'] == 0) {

	    $smsBody = $this->replaceSms_body($escalation_policy[0]['sms_body'], $contact, $booking_id);

	    $this->notify->sendTransactionalSms($contact[0]['owner_phone_1'], $smsBody);
	}
    }

    function replaceSms_body($template, $contact, $booking_id) {

	$smsBody = sprintf($template, $contact[0]['name'], $contact[0]['primary_contact_phone_1'], $booking_id);

	return $smsBody;
    }

    /**
     * Get Email id of owner and vendor when flag is 1.
     * @return : Email id
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
     * Get District of custom State and echo in 'select option value' to load in a form
     */
    function getDistrict() {
	$state = $this->input->post('state');
	$dis = $this->input->post('district');
	$data = $this->vendor_model->getDistrict($state);
	if ($dis == "") {
	    echo "<option selected='selected' disabled='disabled'>Select District</option>";
	}
	foreach ($data as $district) {
	    if ($dis == $district['district']) {
		echo "<option selected value='$district[district]'>$district[district]</option>";
	    } else {
		echo "<option value='$district[district]'>$district[district]</option>";
	    }
	}
    }

    /**
     * Get Pincode of Custom District and print 'select option value with data' to load in a form
     */
    function getPincode() {
	$district = $this->input->post('district');
	$pin = $this->input->post('pincode');
	$data = $this->vendor_model->getPincode($district);
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

    function vendor_availability_form(){
        $data = $this->vendor_model->get_services_category_city_pincode(); 
    	$this->load->view('employee/header');
    	$this->load->view('employee/searchvendor', $data);
    }
    function check_availability_for_vendor(){
    	$data['service_id'] = $this->input->post('service_id');
    	$data['city'] = $this->input->post('city');
    	$data['pincode'] = $this->input->post('pincode');
    	$vendor['vendor'] = $this->vendor_model->getVendorFromVendorMapping($data);
    	$this->load->view('employee/searchvendor', $vendor);
    }
    function send_email_with_latest_pincode_excel(){
    	$to = $this->input->post('email');
    	$notes = $this->input->post('notes');
    	$attachment = $this->input->post('fileUrl');
    	$this->notify->sendEmail("booking@247around.com", $to, '', '', 'Pincode Changes', $notes, $attachment);
    	 echo '<div class="alert alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <strong> Mail Sent </strong>
                </div>';
    }
    function vendor_performance_view(){
    	$data = $this->vendor_model->get_vendor_city_appliance();
    	$this->load->view('employee/header');
    	$this->load->view('employee/vendorperformance',$data);
    }
    function vendor_performance(){
    	$vendor['vendor_id'] = $this->input->post('vendor_id');
    	$vendor['city'] = $this->input->post('city');
    	$vendor['service_id'] = $this->input->post('service_id');
    	$vendor['period'] = $this->input->post('period');
    	$vendor['date_range'] = $this->input->post('date_range');
    	$data['data'] = $this->vendor_model->get_vendor_performance($vendor);
    	$result = $this->load->view('employee/vendorperformance',$data);
    	print_r($result);
    }
}
