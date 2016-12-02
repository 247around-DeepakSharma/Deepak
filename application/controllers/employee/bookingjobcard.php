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
        $this->load->model('booking_model');
        $this->load->model('filter_model');
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library("pagination");
        $this->load->library("session");
        $this->load->library("notify");

        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee') ) {
            return TRUE;
        } else {
            redirect(base_url() . "employee/login");
        }
    }

    /**
     * @desc: accepts post request only and basic validations
     * @param: void
     * @return: void
     */
    public function index() {
        //echo "Hello, World" . PHP_EOL;

        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
        $this->load->view('employee/jobcard');
    }

    /*
     * @desc: This function is to prepare jobcard of a booking.
     * @param: void
     * @return: void
     */

    public function prepare_job_card_by_booking_id() {
        log_message('info', __FUNCTION__);

        $booking_id = $this->input->post('booking_id');
        log_message('info', $booking_id);

        $template = 'BookingJobCard_Template-v8.xlsx';
	//set absolute path to directory with template files
        $templateDir = __DIR__ . "/../";
        //set config for report
        $config = array(
            'template' => $template,
            'templateDir' => $templateDir
        );
        //load template
        $R = new PHPReport($config);
        $booking_details = $this->booking_model->getbooking_history($booking_id);
        $unit_where = array('booking_id'=>$booking_id);
        $unit_details = $this->booking_model->get_unit_details($unit_where);
        $R->load(array(
            array(
                'id' => 'booking',
                //'repeat' => TRUE,
                'data' => $booking_details[0],
                //'minRows' => 2,
                'format' => array(
                    'booking_date' => array('datetime' => 'd/M/Y'),
                    'amount_due' => array('number' => array('prefix' => 'Rs. ')),
                )
            ),
            array(
                'id' => 'unit',
                'repeat' => TRUE,
                'data' => $unit_details,
                //'minRows' => 2,
                'format' => array(
                    //'create_date' => array('datetime' => 'd/M/Y'),
                    'total_price' => array('number' => array('prefix' => 'Rs. ')),
                )
             ),
            )
        );

        //Get populated XLS with data
        if ($booking_details[0]['current_status'] == "Rescheduled"){
            $output_file_suffix = "-RESC-" . $booking_details[0]['booking_date'];
        } else{
            $output_file_suffix = "";
        }

        $output_file_dir = "/tmp/";
        $output_file = "BookingJobCard-" . $booking_id . $output_file_suffix;
        $output_file_excel = $output_file_dir . $output_file . ".xlsx";
        $R->render('excel', $output_file_excel);
        $output_file_pdf = $output_file_dir . $output_file . ".pdf";
        //Update output file name in DB
        $this->reporting_utils->update_booking_jobcard($booking_details[0]['id'], $output_file . ".pdf");

        //$cmd = "curl -F file=@" . $output_file_excel . " http://do.convertapi.com/Excel2Pdf?apikey=278325305" . " -o " . $output_file_pdf;
        putenv('PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/usr/games:/usr/local/games:/opt/node/bin');
        $tmp_path = libreoffice_pdf;
        $tmp_output_file = libreoffice_output_file;

        $cmd = 'echo ' . $tmp_path . ' & echo $PATH & UNO_PATH=/usr/lib/libreoffice & ' .
                '/usr/bin/unoconv --format pdf --output ' . $output_file_pdf . ' ' .
                $output_file_excel . ' 2> ' . $tmp_output_file;

        //echo $cmd;
        $output = '';
        $result_var = '';
        exec($cmd, $output, $result_var);

        //Upload Excel & PDF files to AWS
        $bucket = 'bookings-collateral';
        $directory_xls = "jobcards-excel/" . $output_file . ".xlsx";
        $this->s3->putObjectFile($output_file_excel, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);

        $directory_pdf = "jobcards-pdf/" . $output_file . ".pdf";
        $this->s3->putObjectFile($output_file_pdf, $bucket, $directory_pdf, S3::ACL_PUBLIC_READ);

        $data['success'] = "Job card generated and mailed Successfully";

        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
        $this->load->view('employee/jobcard', $data);
    }

    /*
     * @desc: This function is to prepare jobcard of a booking using booking id.
     * @param: $booking_id- Booking Id of which we want want to prepare the jobcard
     * @return: void
     */

    public function prepare_job_card_using_booking_id($booking_id) {
        log_message('info', __FUNCTION__);

        log_message('info', $booking_id);

        $template = 'BookingJobCard_Template-v8.xlsx';
	//set absolute path to directory with template files
        $templateDir = __DIR__ . "/../";
        //set config for report
        $config = array(
            'template' => $template,
            'templateDir' => $templateDir
        );
        //load template
        $R = new PHPReport($config);
        //log_message('info', "PHP report");
        $booking_details = $this->booking_model->getbooking_history($booking_id);
        $unit_where = array('booking_id'=>$booking_id);
        $unit_details = $this->booking_model->get_unit_details($unit_where);
        $R->load(array(
            array(
                'id' => 'booking',
                //'repeat' => TRUE,
                'data' => $booking_details[0],
                //'minRows' => 2,
                'format' => array(
                    'booking_date' => array('datetime' => 'd/M/Y'),
                    'amount_due' => array('number' => array('prefix' => 'Rs. ')),
                )
            ),
            array(
                'id' => 'unit',
                'repeat' => TRUE,
                'data' => $unit_details,
                //'minRows' => 2,
                'format' => array(
                    //'create_date' => array('datetime' => 'd/M/Y'),
                    'total_price' => array('number' => array('prefix' => 'Rs. ')),
                )
             ),
            )
        );

        //Get populated XLS with data
        if ($booking_details[0]['current_status'] == "Rescheduled"){
            $output_file_suffix = "-RESC-" . $booking_details[0]['booking_date'];
        } else {
            $output_file_suffix = "";
        }

        $output_file_dir = "/tmp/";
        $output_file = "BookingJobCard-" . $booking_id . $output_file_suffix;
        $output_file_excel = $output_file_dir . $output_file . ".xlsx";
        $R->render('excel', $output_file_excel);
        $output_file_pdf = $output_file_dir . $output_file . ".pdf";
        //Update output file name in DB
        $this->reporting_utils->update_booking_jobcard($booking_details[0]['id'], $output_file . ".pdf");

        //$cmd = "curl -F file=@" . $output_file_excel . " http://do.convertapi.com/Excel2Pdf?apikey=278325305" . " -o " . $output_file_pdf;
        putenv('PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/usr/games:/usr/local/games:/opt/node/bin');

        $tmp_path = libreoffice_pdf;
        $tmp_output_file = libreoffice_output_file;
        $cmd = 'echo ' . $tmp_path . ' & echo $PATH & UNO_PATH=/usr/lib/libreoffice & ' .
                '/usr/bin/unoconv --format pdf --output ' . $output_file_pdf . ' ' .
                $output_file_excel . ' 2> ' . $tmp_output_file;

        $output = '';
        $result_var = '';
        exec($cmd, $output, $result_var);

        //Upload Excel & PDF files to AWS
        $bucket = 'bookings-collateral';
        $directory_xls = "jobcards-excel/" . $output_file . ".xlsx";
        $this->s3->putObjectFile($output_file_excel, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);

        $directory_pdf = "jobcards-pdf/" . $output_file . ".pdf";
        $this->s3->putObjectFile($output_file_pdf, $bucket, $directory_pdf, S3::ACL_PUBLIC_READ);

        $this->session->set_flashdata('result', 'Job card generated successfully');
        redirect(base_url() . 'employee/booking/view');
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

    function send_mail_to_vendor($booking_id, $additional_note) {
        log_message('info', __FUNCTION__ . "=> Booking ID: " . $booking_id);

        $getbooking = $this->booking_model->getbooking_history($booking_id,"join");

        if (!empty($getbooking)) {
            $salutation = "Dear " . $getbooking[0]['primary_contact_name'];
            $heading = "<br><br>Please find attached job card " . $getbooking[0]['booking_id'] . " for "
                    . $getbooking[0]['services'] .
                    "<br><br>Date: " . $getbooking[0]['booking_date'] .
                    "<br>Time Slot: " . $getbooking[0]['booking_timeslot'];

            $booking_remarks = "<br><br>Booking remarks: " . $getbooking[0]['booking_remarks'];
            $heading .= $booking_remarks;
            $note = "<br>Special Note: " . urldecode($additional_note) . "<br>";

            $fixedPara = "<br><br>Engineer should follow below guidelines:<br>
                        <br>1. Be very polite,
                        <br>2. Carry tools, No service to be attended without tools,
                        <br>3. Carry soap in case of AC Service,
                        <br>4. Helper to clean the service area after completing service.
                        <br>5. Be very careful while Uninstalling and Installing any Appliance.
                        <br>6. RUN AND CHECK THE Appliance after repair for 5-10 minutes..
                        <br><br>With Regards
                        <br>Devendra - 247Around - 9555118612
                        <br>247Around is part of Businessworld Startup Accelerator & Google Bootcamp 2015
                        <br>Follow us on Facebook: www.facebook.com/247Around | Website:
                        <br>www.247Around.com
                        <br>You will Love our Video Advertisements:
                        <br>https://www.youtube.com/watch?v=y8sBWDPHAhI
                        <br>Playstore - 247Around -
                        <br>https://play.google.com/store/apps/details?id=com.handymanapp
                        ";

            $message = $salutation . $heading . $note . $fixedPara;

            $to = $getbooking[0]['primary_contact_email'];
            $owner = $getbooking[0]['owner_email'];
            $from = "booking@247around.com";
            $cc = $owner;
            $bcc = '';

            $subject = "247Around / Job Card " . $getbooking[0]['booking_id'] . " / " . $getbooking[0]['booking_date'] .
                    " / " . $getbooking[0]['booking_timeslot'];

            $file_pdf = $getbooking[0]['booking_jobcard_filename'];
            $output_file_pdf = "/tmp/" . $getbooking[0]['booking_jobcard_filename'];

            $cmd = "curl https://s3.amazonaws.com/bookings-collateral/jobcards-pdf/" . $file_pdf . " -o " . $output_file_pdf;
            exec($cmd);

            $date1 = date('d-m-Y', strtotime('now'));
            $date2 = $getbooking[0]['booking_date'];
            $datediff = ($date1 - $date2) / (60 * 60 * 24);

            $month = date("m", strtotime($getbooking[0]['booking_date']));
            $dd = date("d", strtotime($getbooking[0]['booking_date']));
            $months = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
            $mm = $months[$month - 1];

            if ($datediff == 0) {
                $bookingdate = "Today";
            } elseif ($datediff == 1) {
                $bookingdate = "Tomorrow";
            } else {
                $bookingdate = $dd . " " . $mm;
            }

            $smsBody = "Booking - " . $getbooking[0]['name'] . ", " . $getbooking[0]['booking_primary_contact_no'] . ", " . $getbooking[0]['services'] . ", " . $bookingdate ."/" . $getbooking[0]['booking_timeslot'] .  ", " . $getbooking[0]['booking_address'] . ", ". $getbooking[0]['booking_pincode'] . ". 247around";
            //Send SMS to vendor
            $this->notify->sendTransactionalSmsAcl($getbooking[0]['primary_contact_phone_1'], $smsBody);
            
            //For saving SMS to the database on sucess
          
            $this->notify->add_sms_sent_details($getbooking[0]['user_id'], 'vendor' , $getbooking[0]['primary_contact_phone_1'],
                    $smsBody, $getbooking[0]['booking_id'],"booking_details_to_sf");
            
            
	    //Save email in database
            $details = array("booking_id" => $booking_id, "subject" => $subject,
                "body" => $message, "type" => "Booking",
                "attachment" => $getbooking[0]['booking_jobcard_filename']);
            $this->booking_model->save_vendor_email($details);

	    $is_mail = $this->notify->sendEmail($from, $to, $cc, $bcc, $subject, $message, $output_file_pdf);

	    if ($is_mail) {
		$data['success'] = "Mail sent to Service Center successfully.";
                $this->session->set_flashdata('result', 'Mail sent to Service Center successfully');
                //Setting flag to 1, once mail is sent.
                $this->booking_model->set_mail_to_vendor($booking_id);
            } else {
                log_message('info', __FUNCTION__ . " => Error: Mail could not be sent");
                
                $data['success'] = "Mail could not be sent, please try again.";
                $this->session->set_flashdata('result', 'Mail could not be sent, please try again');
            }

            redirect(base_url() . 'employee/booking/view');
        } else {
            echo "Booking does not exist.";
        }
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
	$getbooking = $this->booking_model->getbooking($booking_id);

        if ($getbooking) {
            $servicecentredetails = $this->booking_model->selectservicecentre($booking_id);

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
            $salutation = "Dear " . $servicecentredetails[0]['primary_contact_name'];
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

            $to = $servicecentredetails[0]['primary_contact_email'];
            $owner = $servicecentredetails[0]['owner_email'];
            $cc = ($owner . ', anuj@247around.com, nits@247around.com');
            $from = "booking@247around.com";
            $bcc = "";
            $attachment = "";
            //$cc = $owner;
            $is_email = $this->notify->sendEmail($from, $to, $cc, $bcc, $new_sub, $message, $attachment);
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

            redirect(base_url() . 'employee/booking/view');
        } else {
            echo "This booking Id do not exists";
        }
    }


}
