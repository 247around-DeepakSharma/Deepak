<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

/**
 * Description of Booking_utilities
 *
 * @author anujaggarwal
 */
class Booking_utilities {

    var $My_CI;

    function __Construct() {
	$this->My_CI = & get_instance();

	$this->My_CI->load->library('PHPReport');
	$this->My_CI->load->library('email');
	$this->My_CI->load->library('s3');
	$this->My_CI->load->library('form_validation');
	$this->My_CI->load->library("session");
    $this->My_CI->load->library("notify");
	$this->My_CI->load->helper('download');
	$this->My_CI->load->helper(array('form', 'url'));
	$this->My_CI->load->model('employee_model');
	$this->My_CI->load->model('booking_model');
	$this->My_CI->load->model('reporting_utils');
    }

    public function lib_prepare_job_card_using_booking_id($booking_id) {
	log_message('info', __FUNCTION__);

	log_message('info', $booking_id);
	$template = 'BookingJobCard_Template-v8.xlsx';
	//set absolute path to directory with template files
	$templateDir = __DIR__ . "/../controllers/";
	//set config for report
	$config = array(
	    'template' => $template,
	    'templateDir' => $templateDir
	);

	//load template
	$R = new PHPReport($config);
	//log_message('info', "PHP report");
	$booking_details = $this->My_CI->booking_model->getbooking_history($booking_id);
	$unit_details = $this->My_CI->booking_model->get_unit_details($booking_id);
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
	$this->My_CI->reporting_utils->update_booking_jobcard($booking_details[0]['id'], $output_file . ".pdf");

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
	$this->My_CI->s3->putObjectFile($output_file_excel, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);

	$directory_pdf = "jobcards-pdf/" . $output_file . ".pdf";
	$this->My_CI->s3->putObjectFile($output_file_pdf, $bucket, $directory_pdf, S3::ACL_PUBLIC_READ);

//	$this->My_CI->session->set_flashdata('result', 'Job card generated successfully');
//	redirect(base_url() . 'employee/booking/view', 'refresh');*/
    }

    //This function sends email to the assigned vendor
    function lib_send_mail_to_vendor($booking_id, $additional_note) {
        log_message('info', __FUNCTION__." Booking Id: ". print_r($booking_id));

        $getbooking = $this->My_CI->booking_model->getbooking_history($booking_id,"join");

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
            $cc = $owner . ',anuj@247around.com';
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
            $this->My_CI->notify->sendTransactionalSms($getbooking[0]['primary_contact_phone_1'], $smsBody);
            //Save email in database
            $details = array("booking_id" => $booking_id, "subject" => $subject,
                "body" => $message, "type" => "Booking",
                "attachment" => $getbooking[0]['booking_jobcard_filename']);
            $this->My_CI->booking_model->save_vendor_email($details);

	    $is_mail = $this->My_CI->notify->sendEmail($from, $to, $cc, $bcc, $subject, $message, $output_file_pdf);

	    if ($is_mail) {
		$data['success'] = "Mail sent to Service Center successfully.";
                $this->session->set_flashdata('result', 'Mail sent to Service Center successfully');
                //Setting flag to 1, once mail is sent.
                $this->My_CI->booking_model->set_mail_to_vendor($booking_id);
            } else {
                $data['success'] = "Mail could not be sent, please try again.";
                $this->session->set_flashdata('result', 'Mail could not be sent, please try again');
            }

           // redirect(base_url() . 'employee/booking/view');
        } else {
            echo "Booking does not exist.";
        }
    }


}
