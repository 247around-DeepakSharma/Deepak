<?php

if (!defined('BASEPATH')){
    exit('No direct script access allowed');
}

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

/**
 * Description of BookingJobCard
 *
 * @author anujaggarwal
 */
class bookingjobcard extends CI_Controller {

    function __Construct() {
        parent::__Construct();

        $this->load->model('reporting_utils');
        $this->load->library('PHPReport');
        $this->load->library('email');
        $this->load->library('s3');
        $this->load->helper('download');
        $this->load->model('employee_model');
        $this->load->model('vendor_model');
        $this->load->model('booking_model');
        
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library("pagination");
        $this->load->library("session");
        $this->load->library("notify");
        $this->load->library("miscelleneous");
        $this->load->library('booking_utilities');

//        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee') ) {
//            return TRUE;
//        } else {
//            redirect(base_url() . "employee/login");
//        }
    }

    /**
     * @desc: accepts post request only and basic validations
     * @param: void
     * @return: void
     */
    public function index() {
        //echo "Hello, World" . PHP_EOL;
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/jobcard');
    }

    /*
     * @desc: This function is to prepare jobcard of a booking using booking id.
     * @param: $booking_id- Booking Id of which we want want to prepare the jobcard
     * @return: void
     */

    public function prepare_job_card_using_booking_id($booking_id) {
        log_message('info', $booking_id);
        $mpdf = $this->booking_utilities->lib_prepare_job_card_using_booking_id($booking_id);
        if(!$mpdf){
            $this->booking_utilities->mpdf_failure_backup_jobcard($booking_id);
        }
        redirect(base_url() . 'employee/booking/view_bookings_by_status/'._247AROUND_PENDING);
    }

    /*
     * @desc: This function sends email to the assigned vendor with the booking details.
     *
     * We can add additional notes to the email we are sending to vendor.
     *
     * @param: $booking_id - to find details of booking
     * @param: $additional_note - extra notes to be sent in email
     *
     * @return: void
     */

    function send_mail_to_vendor($booking_id, $additional_note = "") {
        log_message('info', __FUNCTION__ . "=> Booking ID: " . $booking_id);

        $this->booking_utilities->lib_send_mail_to_vendor($booking_id, $additional_note);

        redirect(base_url() . 'employee/booking/view_bookings_by_status/'._247AROUND_PENDING);
        
    }

    /* Was made earliar, do something as escalate option now does.
     * @desc: This function sends reminder email to the assigned vendor
     *
     * We can add additional notes to the reminder email we are sending to vendor.
     *
     * This mail is sent if vendor is not contacting the user or not completing the
     *      booking on time.
     *
     * @param: $booking_id - to add details of booking to email body.
     * @param: $additional_note - extra notes to be sent in email.
     *
     * @return: void
     */

    function send_reminder_mail_to_vendor($booking_id, $additional_note) {
	log_message('info', __FUNCTION__ . " Booking ID  " . print_r($booking_id, true));
	$getbooking = $this->booking_model->getbooking_history($booking_id,"join");

        if (isset($getbooking[0]['vendor_name'])) {
            
            //Get SF to RM relation if present
            $cc = "";
            $rm = $this->vendor_model->get_rm_sf_relation_by_sf_id($getbooking[0]['assigned_vendor_id']);
            if(!empty($rm)){
                foreach($rm as $key=>$value){
                        if($key == 0){
                            $cc .= "";
                        }else{
                            $cc .= ",";
                        }
                    $cc .= $this->employee_model->getemployeefromid($value['agent_id'])[0]['official_email'];
                }
            }
            //Find last mail sent for this booking id and append it in the bottom
            $last_mail = $this->booking_model->get_last_vendor_mail($booking_id);
            if ($last_mail[0]['type'] == 'Booking') {
                //Send 1st Reminder Email
                $new_sub = $last_mail[0]['subject'] . " - Reminder-1";
                $type = "Reminder-1";
            } else {
                //Send another Reminder Email
                $t = explode("-", $last_mail[0]['type']);
                $type = "Reminder-" . (intval($t[1]) + 1);
                $old_sub = $last_mail[0]['subject'];
                $substr = " - Reminder-X";
                $new_sub = substr($old_sub, 0, 0 - strlen($substr)) . " - " . $type;
            }

            //New message for this email
            $salutation = "Dear " . $getbooking[0]['primary_contact_name'];
            $heading = "<br><br>" . $type;
            $heading .= "<br><br>Please revert back on the current status of this booking.";

            if ($additional_note != "") {
                $note = "<br><b>Note:</b> " . urldecode($additional_note) . "<br><br>";
            } else {
                $note = "<br><br>";
            }

            $message = $salutation . $heading . $note . "Regards,<br><br>Devendra";

            $message = $message . "<br><br>--------------------------------------------------<br><br>" .
                    $last_mail[0]['body'];

            $to = $getbooking[0]['primary_contact_email'];
            $owner = $getbooking[0]['owner_email'];
            $cc .= ($owner . ', '. NITS_ANUJ_EMAIL_ID);
            $from = NOREPLY_EMAIL_ID;
            $bcc = "";
            $attachment = "";
            //$cc = $owner;
            $is_email = $this->notify->sendEmail($from, $to, $cc, $bcc, $new_sub, $message, $attachment,SERVICE_CENTER_REMINDER_EMAIL);
	    if ($is_email) {
		$data['success'] = "Reminder mail sent to Service Center successfully.";
		$this->session->set_flashdata('email_result', 'Reminder mail sent to Service Center successfully.');
	    } else {
		$data['success'] = "Reminder mail could not be sent, please try again.";
		$this->session->set_flashdata('email_result', 'Reminder mail could not be sent, please try again.');
		log_message('info', __FUNCTION__ . " Mail not sent  " . print_r($booking_id, true));
	    }

	    //Save email in database
            $details = array("booking_id" => $booking_id, "subject" => $new_sub,
                "body" => $message, "type" => $type);
            $this->booking_model->save_vendor_email($details);

            redirect(base_url() . 'employee/booking/view_bookings_by_status/'._247AROUND_PENDING);
        } else {
            echo "This booking Id do not exists";
        }
    }


}
