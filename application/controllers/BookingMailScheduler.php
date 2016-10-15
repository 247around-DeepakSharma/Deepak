<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

error_reporting(E_ERROR);
ini_set('display_errors', '0');

/**
 * Description of BookingMailScheduler
 *
 * @author anujaggarwal
 */
class BookingMailScheduler extends CI_Controller {

    function __Construct() {
        parent::__Construct();

        $this->load->model('reporting_utils');

        $this->load->library('PHPReport');
        $this->load->library('email');
        $this->load->library('s3');

        //$this->load->library('excel');
    }

    /**
     * @input: void
     * @description: accepts post request only and basic validations
     * @output: void
     */
    public function index() {
        echo "Hello, World" . PHP_EOL;
    }

    public function test($a = "a", $b = "b") {
        echo "looks like things are working" . PHP_EOL;

        echo "A = " . $a . PHP_EOL;
        echo "B = " . $b . PHP_EOL;
    }

    public function prepare_job_cards($date = "") {
        //log_message('info', __FUNCTION__);

        $file_names = array();

        $template = 'BookingJobCard_Template-v1.xlsx';
        //set absolute path to directory with template files
        $templateDir = __DIR__ . "/";

        //set config for report
        $config = array(
            'template' => $template,
            'templateDir' => $templateDir
        );

        //Fetch today's bookings for which job cards need to be generated
        //get_todays_bookings_for_job_cards ()
        $today_bookings = $this->reporting_utils->get_todays_bookings_for_job_cards($date);
        $count = count($today_bookings);
        //log_message('info', $count);
        echo "Today bookings count: " . $count . PHP_EOL;

        foreach ($today_bookings as $booking) {
            //load template
            $R = new PHPReport($config);

            //Prepare job card for booking
            $booking_id = $booking['booking_id'];
            echo "Booking ID: " . $booking_id . "\n";

            //Get booking details first
            $booking_details = $this->reporting_utils->get_booking_details($booking_id);
            echo "Booking Details: " . "\n";
            //print_r($booking_details);

            //Find unit details for this booking
            $unit_details = $this->reporting_utils->get_unit_details($booking_id);
            echo "Unit Details: " . "\n";
            print_r($unit_details);
            //log_message('info', "Units fetched: " . count($unit_details));

            $R->load(array(
                /*
                  array(
                  'id' => 'meta',
                  'data' => array('date' => date('Y-m-d'), 'count' => $count),
                  'format' => array(
                  'date' => array('datetime' => 'd/M/Y')
                  )
                  ),
                 */
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
            if ($booking_details[0]['current_status'] == "Rescheduled")
                $output_file_suffix = "-RESC-" . $booking_details[0]['booking_date'];
            else
                $output_file_suffix = "";

            //echo "output_file_suffix: " . $output_file_suffix;

            $output_file = "BookingJobCard-" . $booking_id . $output_file_suffix . ".xlsx";
            $R->render('excel', $output_file);

            $output_file_pdf = "BookingJobCard-" . $booking_id . $output_file_suffix . ".pdf";
            //Update output file name in DB
            $this->reporting_utils->update_booking_jobcard($booking_details[0]['id'], $output_file_pdf);

            $cmd = "curl -F file=@" . $output_file . " http://do.convertapi.com/Excel2Pdf?apikey=278325305" . " -o " . $output_file_pdf;
            //echo $cmd;
            exec($cmd);
            //Attach this PDF file
            //$this->email->attach($output_file_pdf, 'attachment');
            //Save filenames to delete later on
            array_push($file_names, $output_file);
            array_push($file_names, $output_file_pdf);

            //Upload Excel & PDF files to AWS
            $bucket = 'bookings-collateral';
            $directory_xls = "jobcards-excel/" . $output_file;
            $this->s3->putObjectFile(realpath($output_file), $bucket, $directory_xls, S3::ACL_PUBLIC_READ);

            $directory_pdf = "jobcards-pdf/" . $output_file_pdf;
            $this->s3->putObjectFile(realpath($output_file_pdf), $bucket, $directory_pdf, S3::ACL_PUBLIC_READ);
        }

        //Send all reports via email
        //$this->email->from('booking@247around.com', 'Around Team');
        //$this->email->to("booking@247around.com");
        //$this->email->to("anuj.aggarwal@gmail.com");

        //$this->email->subject("Booking Job Cards for " . date('Y-m-d'));
        //$this->email->message("Today bookings count: " . $count . "<br/>");
//        if ($this->email->send()) {
//            //log_message('info', __METHOD__ . ": Mail sent successfully");
//
//            echo "Success" . "\n";
//        } else {
//            //log_message('error', __METHOD__ . ": Mail could not be sent");
//
//            echo "Fail" . "\n";
//        }
        //Delete XLS / PDF files now
        foreach ($file_names as $file_name) {
            echo $file_name . "\n";
            exec("rm -rf " . $file_name);
        }

        //exit(0);
    }

    public function prepare_job_card_by_booking_id($booking_id) {
        //log_message('info', __FUNCTION__);

        $file_names = array();

        $template = 'BookingJobCard_Template-v5.xlsx';
        //set absolute path to directory with template files
        $templateDir = __DIR__ . "/";

        //set config for report
        $config = array(
            'template' => $template,
            'templateDir' => $templateDir
        );

        //load template
        $R = new PHPReport($config);

        //Get booking details first
        $booking_details = $this->reporting_utils->get_booking_details($booking_id);
        echo "Booking Details: " . "\n";
        print_r($booking_details);
        
        //Find unit details for this booking
        $unit_details = $this->reporting_utils->get_unit_details($booking_id);
        echo "Unit Details: " . "\n";
        print_r($unit_details);
        //log_message('info', "Units fetched: " . count($unit_details));

        $R->load(array(
            /*
              array(
              'id' => 'meta',
              'data' => array('date' => date('Y-m-d'), 'count' => $count),
              'format' => array(
              'date' => array('datetime' => 'd/M/Y')
              )
              ),
             */
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
        if ($booking_details[0]['current_status'] == "Rescheduled")
            $output_file_suffix = "-RESC-" . $booking_details[0]['booking_date'];
        else
            $output_file_suffix = "";

        //echo "output_file_suffix: " . $output_file_suffix;

        $output_file = "BookingJobCard-" . $booking_id . $output_file_suffix . ".xlsx";
        $R->render('excel', $output_file);

        $output_file_pdf = "BookingJobCard-" . $booking_id . $output_file_suffix . ".pdf";
        //Update output file name in DB
        $this->reporting_utils->update_booking_jobcard($booking_details[0]['id'], $output_file_pdf);

        $cmd = "curl -F WorksheetActive=true -F file=@" . $output_file . " http://do.convertapi.com/Excel2Pdf?apikey=278325305" . " -o " . $output_file_pdf;
        //echo $cmd;
	//exit(0);
        exec($cmd);
        //Attach this PDF file
        //$this->email->attach($output_file_pdf, 'attachment');
        //Save filenames to delete later on
        array_push($file_names, $output_file);
        array_push($file_names, $output_file_pdf);

        //Upload Excel & PDF files to AWS
        $bucket = 'bookings-collateral';
        $directory_xls = "jobcards-excel/" . $output_file;
        $this->s3->putObjectFile(realpath($output_file), $bucket, $directory_xls, S3::ACL_PUBLIC_READ);

        $directory_pdf = "jobcards-pdf/" . $output_file_pdf;
        $this->s3->putObjectFile(realpath($output_file_pdf), $bucket, $directory_pdf, S3::ACL_PUBLIC_READ);

        //Send all reports via email
        //$this->email->from('booking@247around.com', 'Around Team');
        //$this->email->to("booking@247around.com");
        //$this->email->to("anuj.aggarwal@gmail.com");

        //$this->email->subject("Job Card for Booking ID: " . $booking_id);

        /*
          if ($this->email->send()) {
          echo "Success" . "\n";
          } else {
          echo "Fail" . "\n";
          }
         *
         */

        //Delete XLS / PDF files now
        foreach ($file_names as $file_name) {
            echo $file_name . "\n";
            exec("rm -rf " . $file_name);
        }

        //exit(0);
    }

}
