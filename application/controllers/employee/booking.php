<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

define('Partner_Integ_Complete', TRUE);

class Booking extends CI_Controller {

    /**
     * load list model and helpers
     */
    function __Construct() {
        parent::__Construct();
        $this->load->model('employee_model');
        $this->load->model('booking_model');
        $this->load->model('user_model');
        $this->load->model('vendor_model');
        $this->load->model('filter_model');
        $this->load->model('partner_model');
        $this->load->model('invoices_model');

        $this->load->helper(array('form', 'url'));

        $this->load->library('form_validation');
        $this->load->library("pagination");
        $this->load->library("session");
        $this->load->library('s3');
        $this->load->library('email');
        $this->load->library('notify');
        $this->load->library('booking_utilities');
        $this->load->library('partner_sd_cb');
        $this->load->library('asynchronous_lib');

        if (($this->session->userdata('loggedIn') == TRUE) &&
                ($this->session->userdata('userType') == 'employee')) {
            return TRUE;
        } else {
            redirect(base_url() . "employee/login");
        }
    }

    /**
     * @desc : This function will load booking and Add booking
     * @param: void
     * @return : print Booking on Booking Page
     */
    function addbooking() {
        //$results['service'] = $this->filter_model->getserviceforfilter();
        //$results['agent'] = $this->filter_model->getagent();
        //$employee_id = $this->session->userdata('employee_id');
        //$results['one'] = $this->employee_model->verifylist($employee_id, '0');
        //$results['three'] = $this->employee_model->verifylist($employee_id, '2');
        //$results['forteen'] = $this->employee_model->verifylist($employee_id, '14');

        $results['user_id'] = $this->input->post('user_id');
        $results['home_address'] = $this->input->post('home_address');
        $results['user_email'] = $this->input->post('user_email');
        $results['city'] = $this->input->post('city');
        $results['state'] = $this->input->post('state');
        $results['phone_number'] = $this->input->post('phone_number');
        $results['alternate_phone_number'] = $this->input->post('alternate_phone_number');
        $results['pincode'] = $this->input->post('pincode');
        $results['name'] = $this->input->post('name');

        $results['reason'] = $this->booking_model->cancelreason("247around");
        $results['services'] = $this->booking_model->selectservice();
        $results['sources'] = $this->booking_model->select_booking_source();

        $this->load->view('employee/header', $results);
        $this->load->view('employee/addbooking');
    }

    /**
     *  @desc : This method is used to add booking for a user.
     *
     * This function will get all the booking details from confirmation booking page,
     *      and then insert all the booking details in booking details table.

     * These booking details are the details which are filled in add new booking form while taking the actual booking.
     *
     * After insertion of booking details, if it is not a query then an email and SMS will be sent to the user for booking confirmation.

     *  @param : void
     *
     *  @return : void
     */
    public function index() {
        $validation = true; // $this->checkValidation();

        if ($validation) {
            $booking['type'] = $this->input->post('type');
            $booking['source'] = $this->input->post('source');
	    //Find Partner ID for this Source
	    $booking['partner_id'] = $this->partner_model->get_partner_id_from_booking_source_code($booking['source']);
	    $booking['city'] = $this->input->post('city');
            $booking['state'] = $this->input->post('state');
            $booking['quantity'] = $this->input->post('quantity');
            $booking['partner_source'] = $this->input->post('partner_source');
            $booking['order_id'] =  $this->input->post('order_id');
            $booking['serial_number'] =  $this->input->post('serial_number');
            $booking['appliance_brand1'] = $this->input->post('appliance_brand1');
            $booking['appliance_category1'] = $this->input->post('appliance_category1');
            $booking['appliance_capacity1'] = $this->input->post('appliance_capacity1');
            $booking['items_selected1'] = $this->input->post('items_selected1');
            $booking['total_price1'] = $this->input->post('total_price1');
            $booking['model_number1'] = $this->input->post('model_number1');
            $booking['appliance_tags1'] = $this->input->post('appliance_tags1');
            $booking['purchase_year1'] = $this->input->post('purchase_year1');
            $booking['potential_value'] = $this->input->post('potential_value');
            $booking['appliance_brand2'] = $this->input->post('appliance_brand2');
            $booking['appliance_category2'] = $this->input->post('appliance_category2');
            $booking['appliance_capacity2'] = $this->input->post('appliance_capacity2');
            $booking['items_selected2'] = $this->input->post('items_selected2');
            $booking['total_price2'] = $this->input->post('total_price2');
            $booking['model_number2'] = $this->input->post('model_number2');
            $booking['appliance_tags2'] = $this->input->post('appliance_tags2');
            $booking['purchase_year2'] = $this->input->post('purchase_year2');
            if ($booking['total_price2'] == '') {
                $booking['appliance_brand2'] = " ";
                $booking['appliance_category2'] = " ";
                $booking['appliance_capacity2'] = " ";
            }
            $booking['appliance_brand3'] = $this->input->post('appliance_brand3');
            $booking['appliance_category3'] = $this->input->post('appliance_category3');
            $booking['appliance_capacity3'] = $this->input->post('appliance_capacity3');
            $booking['items_selected3'] = $this->input->post('items_selected3');
            $booking['total_price3'] = $this->input->post('total_price3');
            $booking['model_number3'] = $this->input->post('model_number3');
            $booking['appliance_tags3'] = $this->input->post('appliance_tags3');
            $booking['purchase_year3'] = $this->input->post('purchase_year3');
            if ($booking['total_price3'] == '') {
                $booking['appliance_brand3'] = " ";
                $booking['appliance_category3'] = " ";
                $booking['appliance_capacity3'] = " ";
            }
            $booking['appliance_brand4'] = $this->input->post('appliance_brand4');
            $booking['appliance_category4'] = $this->input->post('appliance_category4');
            $booking['appliance_capacity4'] = $this->input->post('appliance_capacity4');
            $booking['items_selected4'] = $this->input->post('items_selected4');
            $booking['total_price4'] = $this->input->post('total_price4');
            $booking['model_number4'] = $this->input->post('model_number4');
            $booking['appliance_tags4'] = $this->input->post('appliance_tags4');
            $booking['purchase_year4'] = $this->input->post('purchase_year4');
            if ($booking['total_price4'] == '') {
                $booking['appliance_brand4'] = " ";
                $booking['appliance_category4'] = " ";
                $booking['appliance_capacity4'] = " ";
            }
            $booking['user_id'] = $this->input->post('user_id');
            $foremail['phone_number'] = $this->input->post('booking_primary_contact_no');
            $foremail['user_email'] = $this->input->post('user_email');
            $foremail['name'] = $this->input->post('name');

            $booking['booking_primary_contact_no'] = $this->input->post('booking_primary_contact_no');
            $booking['booking_alternate_contact_no'] = $this->input->post('booking_alternate_contact_no');
            $booking['service_id'] = $this->input->post('service_id');
            $booking['booking_date'] = $this->input->post('booking_date');
            $booking['booking_timeslot'] = $this->input->post('booking_timeslot');
            $booking['booking_remarks'] = $this->input->post('booking_remarks');
            $booking['query_remarks'] = $this->input->post('query_remarks');
            $booking['booking_address'] = $this->input->post('booking_address');

            $booking['booking_pincode'] = $this->input->post('booking_pincode');
            $booking['amount_due'] = $booking['total_price1'] + $booking['total_price2'] + $booking['total_price3'] + $booking['total_price4'];
            $booking['create_date'] = date("Y-m-d H:i:s");
            $booking['booking_date'] = date('d-m-Y', strtotime($booking['booking_date']));
            $yy = date("y", strtotime($booking['booking_date']));
            $mm = date("m", strtotime($booking['booking_date']));
            $dd = date("d", strtotime($booking['booking_date']));
            if ($booking['type'] == "Query") {
                $booking['booking_id'] = str_pad($booking['user_id'], 4, "0", STR_PAD_LEFT) . $yy . $mm . $dd;
                $booking['booking_id'] .= (intval($this->booking_model->getBookingCountByUser($booking['user_id'])) + 1);
                //Add source
                $booking['booking_id'] = $booking['source'] . "-" . $booking['booking_id'];
                $add = "Q-";
                $booking['booking_id'] = $add . $booking['booking_id'];
                $booking['current_status'] = "FollowUp";
                $booking['internal_status'] = "FollowUp";
                $booking['type'] = "Query";
                $booking['total_price1'] = 0;
                $booking['total_price2'] = 0;
                $booking['total_price3'] = 0;
                $booking['total_price4'] = 0;
            } else {
                $booking['booking_id'] = str_pad($booking['user_id'], 4, "0", STR_PAD_LEFT) . $yy . $mm . $dd;
                $booking['booking_id'] .= (intval($this->booking_model->getBookingCountByUser($booking['user_id'])) + 1);
                //Add source
                $booking['booking_id'] = $booking['source'] . "-" . $booking['booking_id'];

                $booking['type'] = "Booking";
                $booking['current_status'] = "Pending";
                $booking['internal_status'] = "Scheduled";
            }

            $appliance_id = $this->booking_model->addappliancedetails($booking);

            $this->booking_model->addunitdetails($booking);

            $this->booking_model->addbooking($booking, $appliance_id[0]['id'], $booking['city'], $booking['state']);

            $query1 = $this->booking_model->booking_history_by_booking_id($booking['booking_id']);
            $query2 = $this->booking_model->get_unit_details($booking['booking_id']);

            $months = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');

            $mm = $months[$mm - 1];

            $booking['booking_date'] = $dd . $mm;

            if ($booking['booking_timeslot'] == "10AM-1PM") {
                $booking['booking_timeslot'] = "1PM";
            } elseif ($booking['booking_timeslot'] == "1PM-4PM") {
                $booking['booking_timeslot'] = "4PM";
            } elseif ($booking['booking_timeslot'] == "4PM-7PM") {
                $booking['booking_timeslot'] = "7PM";
            }

            //-------Sending Email On Booking--------//
            if ($booking['current_status'] != "FollowUp") {
                $message = "Congratulations You have received new booking, details are mentioned below:
      <br>Customer Name: " . $query1[0]['name'] . "<br>Customer Phone Number: " . $query1[0]['phone_number'] .
                        "<br>Customer email address: " . $query1[0]['user_email'] . "<br>Booking Id: " .
                        $booking['booking_id'] . "<br>Service name:" . $query1[0]['services'] .
                        "<br>Number of appliance: " . $booking['quantity'] . "<br>Booking Date: " .
                        $booking['booking_date'] . "<br>Booking Timeslot: " . $booking['booking_timeslot'] .
                        "<br>Amount Due: " . $booking['amount_due'] . "<br>Your Booking Remark is: " .
                        $booking['booking_remarks'] . "<br>Booking address: " . $booking['booking_address'] .
                        "<br>Booking city: " . $booking['city'] .
                        "<br>Booking pincode: " . $booking['booking_pincode'] . "<br><br>
        Appliance Details:<br>";

                $appliance = "";
                for ($i = 0; $i < $booking['quantity']; $i++) {

                    $appliance = "<br>Brand : " . $query2[$i]['appliance_brand'] . "<br>Category : " .
                            $query2[$i]['appliance_category'] . "<br>Capacity : " . $query2[$i]['appliance_capacity'] .
                            "<br>Selected service/s is/are: " . $query2[$i]['price_tags'] . "<br>Total price is: " .
                            $query2[$i]['total_price'] . "<br>";
                    $message = $message . $appliance;
                }
                $message = $message . "<br> Thanks!!";

                $from = "booking@247around.com";
                $to = "anuj@247around.com, nits@247around.com";
                $cc = "";
                $bcc = "";
                $subject = 'Booking Confirmation-AROUND';
                $attachment = "";
                //$this->notify->sendEmail($from, $to, $cc, $bcc, $subject, $message, $attachment);
                //-------Sending SMS on booking--------//

                $sms['tag'] = "add_new_booking";
                $sms['smsData']['service'] = $query1[0]['services'];
                $sms['smsData']['booking_date'] = $booking['booking_date'];
                $sms['smsData']['booking_timeslot'] = $booking['booking_timeslot'];
                $sms['phone_no'] = $query1[0]['phone_number'];
                $sms['booking_id'] = $booking['booking_id'];

                //$this->notify->send_sms($sms);
            }
            //------End of sending SMS--------//

            redirect(base_url() . search_page);
        }
    }

    /**
     *  @desc : This method is for booking confirmation.
     * This method is for getting all the booking details from the booking form.

     * This will display the details entered in the form to us to re-check that we have 		entred all the details correctly.
     *
     * When all the details look correct we click the save button to save the details for 	that booking.
     *
     *  @param : void
     *
     *  @return : all the booking details
     */
    function bookingconfirmation() {
        $booking['user_id'] = $this->input->post('user_id');
        $foremail['phone_number'] = $this->input->post('booking_primary_contact_no');
        $foremail['user_email'] = $this->input->post('user_email');
        $foremail['name'] = $this->input->post('name');
        $booking['city'] = $this->input->post('booking_city');
        $booking['state'] = $this->input->post('booking_state');
        $booking['serial_number'] =  $this->input->post('serial_number');
        $booking['order_id'] = $this->input->post('order_id');
        $booking['partner_source'] =  $this->input->post('partner_source');

        $booking['newbrand1'] = $this->input->post('newbrand1');
        $booking['newbrand2'] = $this->input->post('newbrand2');
        $booking['newbrand3'] = $this->input->post('newbrand3');
        $booking['newbrand4'] = $this->input->post('newbrand4');
        //For future use, i.e. for multiple appliances
        //$booking['potential_value2'] = $this->input->post('potential_value2');
        //$booking['potential_value3'] = $this->input->post('potential_value3');
        //$booking['potential_value4'] = $this->input->post('potential_value4');
        $booking['booking_primary_contact_no'] = $this->input->post('booking_primary_contact_no');
        $booking['booking_alternate_contact_no'] = $this->input->post('booking_alternate_contact_no');
        $booking['service_id'] = $this->input->post('service_id');
        $booking['booking_date'] = $this->input->post('booking_date');
        $booking['type'] = $this->input->post('type');
        $booking['source'] = $this->input->post('source_code');
        $booking['query_remarks'] = $this->input->post('query_remarks');
        $yy = date("y", strtotime($booking['booking_date']));
        $mm = date("m", strtotime($booking['booking_date']));
        $dd = date("d", strtotime($booking['booking_date']));
        if ($booking['type'] == "Query") {    //For Query
            $booking['booking_id'] = "";
            $booking['current_status'] = "FollowUp";
            $booking['type'] = "Query";
            $booking['booking_timeslot'] = $this->input->post('booking_timeslot');
            $booking['quantity'] = $this->input->post('quantity');
            $booking['booking_remarks'] = $this->input->post('booking_remarks');
            $booking['booking_address'] = $this->input->post('booking_address');
            $booking['booking_pincode'] = $this->input->post('booking_pincode');
            $booking['potential_value'] = $this->input->post('potential_value1');
            if ($booking['newbrand1'] != "") {
                $booking['appliance_brand1'] = $booking['newbrand1'];

                $this->booking_model->addNewApplianceBrand($booking['service_id'], $booking['newbrand1']);
            } else {
                $booking['appliance_brand1'] = $this->input->post('appliance_brand1');
            }
            $booking['appliance_category1'] = $this->input->post('appliance_category1');
            $booking['appliance_capacity1'] = $this->input->post('appliance_capacity1');
            $booking['items_selected1'] = $this->input->post('items_selected1');
            $booking['model_number1'] = $this->input->post('model_number1');
            $booking['appliance_tags1'] = $this->input->post('appliance_tags1');
            $booking['purchase_year1'] = $this->input->post('purchase_year1');
            if ($booking['newbrand2'] != "") {
                $booking['appliance_brand2'] = $booking['newbrand2'];
                $this->booking_model->addNewApplianceBrand($booking['service_id'], $booking['newbrand2']);
            } else {
                $booking['appliance_brand2'] = $this->input->post('appliance_brand2');
            }
            $booking['appliance_category2'] = $this->input->post('appliance_category2');
            $booking['appliance_capacity2'] = $this->input->post('appliance_capacity2');
            $booking['items_selected2'] = $this->input->post('items_selected2');
            $booking['model_number2'] = $this->input->post('model_number2');
            $booking['appliance_tags2'] = $this->input->post('appliance_tags2');
            $booking['purchase_year2'] = $this->input->post('purchase_year2');
            if ($booking['quantity'] <= 1) {
                $booking['appliance_brand2'] = " ";
                $booking['appliance_category2'] = " ";
                $booking['appliance_capacity2'] = " ";
            }
            if ($booking['newbrand3'] != "") {
                $booking['appliance_brand3'] = $booking['newbrand3'];
                $this->booking_model->addNewApplianceBrand($booking['service_id'], $booking['newbrand3']);
            } else {
                $booking['appliance_brand3'] = $this->input->post('appliance_brand3');
            }
            $booking['appliance_category3'] = $this->input->post('appliance_category3');
            $booking['appliance_capacity3'] = $this->input->post('appliance_capacity3');
            $booking['items_selected3'] = $this->input->post('items_selected3');
            $booking['model_number3'] = $this->input->post('model_number3');
            $booking['appliance_tags3'] = $this->input->post('appliance_tags3');
            $booking['purchase_year3'] = $this->input->post('purchase_year3');
            if ($booking['quantity'] <= 2) {
                $booking['appliance_brand3'] = " ";
                $booking['appliance_category3'] = " ";
                $booking['appliance_capacity3'] = " ";
            }
            if ($booking['newbrand4'] != "") {
                $booking['appliance_brand4'] = $booking['newbrand4'];
                $this->booking_model->addNewApplianceBrand($booking['service_id'], $booking['newbrand4']);
            } else {
                $booking['appliance_brand4'] = $this->input->post('appliance_brand4');
            }
            $booking['appliance_category4'] = $this->input->post('appliance_category4');
            $booking['appliance_capacity4'] = $this->input->post('appliance_capacity4');
            $booking['items_selected4'] = $this->input->post('items_selected4');
            $booking['model_number4'] = $this->input->post('model_number4');
            $booking['appliance_tags4'] = $this->input->post('appliance_tags4');
            $booking['purchase_year4'] = $this->input->post('purchase_year4');
            if ($booking['quantity'] <= 3) {
                $booking['appliance_brand4'] = " ";
                $booking['appliance_category4'] = " ";
                $booking['appliance_capacity4'] = " ";
            }
            $booking['total_price1'] = 0;
            $booking['total_price2'] = 0;
            $booking['total_price3'] = 0;
            $booking['total_price4'] = 0;
        } else { //For Booking
            $booking['booking_id'] = str_pad($booking['user_id'], 4, "0", STR_PAD_LEFT) . $yy . $mm . $dd;
            $booking['booking_id'] .= (intval($this->booking_model->getBookingCountByUser($booking['user_id'])) + 1);

            $booking['type'] = "Pending";
            $booking['current_status'] = "Pending";
            $booking['potential_value'] = 0;
            $booking['booking_timeslot'] = $this->input->post('booking_timeslot');
            $booking['quantity'] = $this->input->post('quantity');
            $booking['booking_remarks'] = $this->input->post('booking_remarks');
            $booking['booking_address'] = $this->input->post('booking_address');
            $booking['booking_pincode'] = $this->input->post('booking_pincode');
            $booking['appliance_brand1'] = $this->input->post('appliance_brand1');
            if ($booking['newbrand1'] != "") {
                $booking['appliance_brand1'] = $booking['newbrand1'];
                $this->booking_model->addNewApplianceBrand($booking['service_id'], $booking['newbrand1']);
            }
            $booking['appliance_category1'] = $this->input->post('appliance_category1');
            $booking['appliance_capacity1'] = $this->input->post('appliance_capacity1');
            $booking['items_selected1'] = $this->input->post('items_selected1');
            $booking['total_price1'] = $this->input->post('total_price1');
            $booking['model_number1'] = $this->input->post('model_number1');
            $booking['appliance_tags1'] = $this->input->post('appliance_tags1');
            $booking['purchase_year1'] = $this->input->post('purchase_year1');
            $booking['appliance_brand2'] = $this->input->post('appliance_brand2');
            $booking['appliance_category2'] = $this->input->post('appliance_category2');
            $booking['appliance_capacity2'] = $this->input->post('appliance_capacity2');
            $booking['items_selected2'] = $this->input->post('items_selected2');
            $booking['total_price2'] = $this->input->post('total_price2');
            $booking['model_number2'] = $this->input->post('model_number2');
            $booking['appliance_tags2'] = $this->input->post('appliance_tags2');
            $booking['purchase_year2'] = $this->input->post('purchase_year2');
            if ($booking['total_price2'] == '') {
                $booking['appliance_brand2'] = " ";
                $booking['appliance_category2'] = " ";
                $booking['appliance_capacity2'] = " ";
            } elseif ($booking['newbrand2'] != "") {
                $booking['appliance_brand2'] = $booking['newbrand2'];
                $this->booking_model->addNewApplianceBrand($booking['service_id'], $booking['newbrand2']);
            }
            $booking['appliance_brand3'] = $this->input->post('appliance_brand3');
            $booking['appliance_category3'] = $this->input->post('appliance_category3');
            $booking['appliance_capacity3'] = $this->input->post('appliance_capacity3');
            $booking['items_selected3'] = $this->input->post('items_selected3');
            $booking['total_price3'] = $this->input->post('total_price3');
            $booking['model_number3'] = $this->input->post('model_number3');
            $booking['appliance_tags3'] = $this->input->post('appliance_tags3');
            $booking['purchase_year3'] = $this->input->post('purchase_year3');
            if ($booking['total_price3'] == '') {
                $booking['appliance_brand3'] = " ";
                $booking['appliance_category3'] = " ";
                $booking['appliance_capacity3'] = " ";
            } elseif ($booking['newbrand3'] != "") {
                $booking['appliance_brand3'] = $booking['newbrand3'];
                $this->booking_model->addNewApplianceBrand($booking['service_id'], $booking['newbrand3']);
            }
            $booking['appliance_brand4'] = $this->input->post('appliance_brand4');
            $booking['appliance_category4'] = $this->input->post('appliance_category4');
            $booking['appliance_capacity4'] = $this->input->post('appliance_capacity4');
            $booking['items_selected4'] = $this->input->post('items_selected4');
            $booking['total_price4'] = $this->input->post('total_price4');
            $booking['model_number4'] = $this->input->post('model_number4');
            $booking['appliance_tags4'] = $this->input->post('appliance_tags4');
            $booking['purchase_year4'] = $this->input->post('purchase_year4');
            if ($booking['total_price4'] == '') {
                $booking['appliance_brand4'] = " ";
                $booking['appliance_category4'] = " ";
                $booking['appliance_capacity4'] = " ";
            } elseif ($booking['newbrand4'] != "") {
                $booking['appliance_brand4'] = $booking['newbrand4'];
                $this->booking_model->addNewApplianceBrand($booking['service_id'], $booking['newbrand4']);
            }
        }
        $booking['amount_due'] = $booking['total_price1'] + $booking['total_price2'] + $booking['total_price3'] + $booking['total_price4'];
        $booking['create_date'] = date("Y-m-d H:i:s");

        $result = $this->booking_model->service_name($booking['service_id']);

        $booking_source = $this->booking_model->get_booking_source($booking['source']);

        $this->load->view('employee/header');
        $this->load->view('employee/bookingconfirmation', array('booking' => $booking, 'result' => $result,
            'booking_source' => $booking_source[0]));
    }

    function loadViews($output) {
        $data['sucess'] = $output;
        $results['service'] = $this->filter_model->getserviceforfilter();
        $results['agent'] = $this->filter_model->getagent();
        $employee_id = $this->session->userdata('employee_id');
        $results['one'] = $this->employee_model->verifylist($employee_id, '0');
        $results['three'] = $this->employee_model->verifylist($employee_id, '2');
        $results['forteen'] = $this->employee_model->verifylist($employee_id, '14');
        $this->load->view('employee/header', $results);
        //$this->load->view('employee/addbooking',$data);
        $this->load->view('employee/bookinghistory');
    }

    /**
     *  @desc : This method is for checking validation.
     *
     * This method will check the validity for various fields in the booking form.
     *
     * Few of the validations are for user_id, service_id, booking date, etc.
     *
     *  @param : void
     *
     *  @return : true if validation true otherwise FALSE
     */
    public function checkValidation() {
        $this->form_validation->set_rules('user_id', 'user_id', 'required');

        $this->form_validation->set_rules('service_id', 'service_id', 'required');
        $this->form_validation->set_rules('booking_date', 'booking_date', 'required');
        $this->form_validation->set_rules('booking_timeslot', 'booking_timeslot', 'required');
        $this->form_validation->set_rules('appliance_brand', 'appliance_brand', 'required');
        $this->form_validation->set_rules('appliance_category', 'appliance_category', 'required');
        $this->form_validation->set_rules('appliance_capacity', 'appliance_capacity', 'required');
        $this->form_validation->set_rules('quantity', 'quantity', 'required');

        $this->form_validation->set_error_delimiters('<div class="error" role="alert">', '</div>');
        if ($this->form_validation->run() == FALSE) {
            return FALSE;
        } else {
            return true;
        }
    }

    /**
     *  @desc : This method displays list of bookings present in the database.
     *
     *  @param : void
     *
     *  @return : all the bookings to view
     */
    function viewbooking($offset = 0, $page = 0) {
        $query = $this->booking_model->viewbooking();

        $data['Bookings'] = null;

        if ($query) {
            $data['Bookings'] = $query;
        }

        $this->load->view('employee/header');

        $this->load->view('employee/booking', $data);
    }

    /**
     *  @desc : This method is to view all pending bookings.
     *
     * This method will be called when you select all from the pagination option present on the top of the page.
     *
     *
     *  @param : void
     *
     *  @return : all the pending bookings present.
     */
    function view_all_pending_booking() {
        $query = $this->booking_model->viewallpendingbooking();

        $data['Bookings'] = null;

        if ($query) {
            $data['Bookings'] = $query;
        }

        $this->load->view('employee/header');

        $this->load->view('employee/booking', $data);
    }

    /**
     *  @desc : This function displays list of pending bookings according to pagination
     *  @param : Starting page & number of results per page
     *  @return : pending bookings according to pagination
     */
    function view($offset = 0, $page = 0, $booking_id = "") {
        if ($page == 0) {
            $page = 50;
        }

        $offset = ($this->uri->segment(4) != '' ? $this->uri->segment(4) : 0);
        $config['base_url'] = base_url() . 'employee/booking/view';
        $config['total_rows'] = $this->booking_model->total_pending_booking($booking_id);

        $config['per_page'] = $page;
        $config['uri_segment'] = 4;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';

        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['Count'] = $config['total_rows'];
        $data['Bookings'] = $this->booking_model->date_sorted_booking($config['per_page'], $offset, $booking_id);
        if ($this->session->flashdata('result') != '')
            $data['success'] = $this->session->flashdata('result');

        $this->load->view('employee/header');
        $this->load->view('employee/booking', $data);
    }

    /**
     *  @desc : This method is used to view all the completed bookings.
     *
     *  This method will show all the completed bookings when you select All from the pagination.
     *
     *  @param : void
     *
     *  @return : all the completed booking.
     */
    function viewallcompletedbooking() {
        $query = $this->booking_model->view_all_completed_booking();

        $data['Bookings'] = null;

        if ($query) {
            $data['Bookings'] = $query;
        }

        $this->load->view('employee/header');

        $this->load->view('employee/viewcompletedbooking', $data);
    }

    /**
     *  @desc : This method is used to view all the cancelled bookings.
     *
     * This method will show all the cancelled bookings when you select All from the pagination.
     *
     *  @param : void
     *
     *  @return : all the cancelled booking.
     */
    function viewallcancelledbooking() {
        $query = $this->booking_model->view_all_cancelled_booking();

        $data['Bookings'] = null;

        if ($query) {
            $data['Bookings'] = $query;
        }

        $this->load->view('employee/header');

        $this->load->view('employee/viewcancelledbooking', $data);
    }

    /**
     *  @desc : This function displays list of completed bookings according to pagination
     *
     * This method will show only that number of bookings which are being selected from the pagination section(50/100/200).
     *
     *  @param : Starting page & number of results per page
     *  @return : completed bookings according to pagination
     */
    function viewcompletedbooking($offset = 0, $page = 0) {
        if ($page == 0) {
            $page = 50;
        }

        $offset = ($this->uri->segment(4) != '' ? $this->uri->segment(4) : 0);
        $config['base_url'] = base_url() . 'employee/booking/viewcompletedbooking';
        $config['total_rows'] = $this->booking_model->total_completed_booking();
        $config['per_page'] = $page;
        $config['uri_segment'] = 4;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';

        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['Bookings'] = $this->booking_model->view_completed_booking($config['per_page'], $offset);
        $this->load->view('employee/header');

        $this->load->view('employee/viewcompletedbooking', $data);
    }

    /**
     *  @desc : This function displays list of cancelled bookings according to pagination
     *
     * This method will show only that number of cancelled bookings which are being selected from the pagination section(50/100/200).
     *
     *  @param : Starting page & number of results per page
     *  @return : cancelled bookings according to pagination
     */
    function viewcancelledbooking($offset = 0, $page = 0) {
        if ($page == 0) {
            $page = 50;
        }

        $offset = ($this->uri->segment(4) != '' ? $this->uri->segment(4) : 0);
        $config['base_url'] = base_url() . 'employee/booking/viewcancelledbooking';
        $config['total_rows'] = $this->booking_model->total_completed_booking();
        $config['per_page'] = $page;
        $config['uri_segment'] = 4;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';

        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['Bookings'] = $this->booking_model->view_cancelled_booking($config['per_page'], $offset);
        $this->load->view('employee/header');

        $this->load->view('employee/viewcancelledbooking', $data);
    }

    /**
     *  @desc : This function selects all the services
     *
     * This method will return only that services that are in active state(Active = 1).
     *
     *  @param : void
     *  @return : all the active services to view
     */
    function ServiceSelect() {
        $query = $this->booking_model->selectservice();

        $data['Services'] = null;

        if ($query) {
            $data['Services'] = $query;
        }

        $this->load->view('employee/addbooking', $data);
    }

    /**
     *  @desc : This function selects the brands present in appliance_brand table.
     *
     * The brand's list will be in ascending order.
     *
     * The brand's list will be distinct.
     *
     *  @param : void
     *  @return : all the brands to view.
     */
    function brandselect() {
        $query = $this->booking_model->selectbrand();

        $data['brands'] = null;

        if ($query) {
            $data['brands'] = $query;
        }

        $this->load->view('employee/addbooking', $data);
    }

    /**
     *  @desc : This function displays all the categoties present
     *  @param : void
     *  @return : all the categories to view
     */
    function categoryselect() {
        $query = $this->booking_model->selectcategory();

        $data['category'] = null;

        if ($query) {
            $data['category'] = $query;
        }

        $this->load->view('employee/addbooking', $data);
    }

    /**
     *  @desc : This function displays user details
     *
     * With the help of user's phone number the user is searched and its personal detsils and booking details are displayed if user exists.
     *
     *  @param : phone number
     *  @return : the details of particular user
     */
    function finduser($phone) {
//	$query = $this->booking_model->finduser($phone);
        $this->booking_model->finduser($phone);
    }

    /**
     *  @desc : This function returns the cancelation reason for booking
     *  @param : void
     *  @return : all the cancelation reasons present in the database
     */
    function cancelreason() {


        $query = $this->booking_model->cancelreason("247around");

        $data['reason'] = null;

        if ($query) {
            $data['reason'] = $query;
        }

        $this->load->view('employee/cancelbooking', $data);
    }

    /**
     *  @desc : This function is to select booking to be completed
     *
     * Opens a form with basic booking details and feilds to be filled before completing the booking like amount collected, amount collected by, etc.
     *
     *  @param : booking id
     *  @return : user details and booking history to view
     */
    function get_complete_booking_form($booking_id) {

        $getbooking = $this->booking_model->getbooking($booking_id);
        $query2 = $this->booking_model->get_unit_details($booking_id);
        if ($getbooking) {

            $data['booking_id'] = $getbooking;

            $query = $this->booking_model->booking_history_by_booking_id($booking_id);
            $page = "Complete";
            $internal_status = $this->booking_model->get_internal_status($page);
            $data1['booking_id'] = $query;

            $this->load->view('employee/header');
            $this->load->view('employee/completebooking', array('data' => $data,
                'data1' => $data1,
                'internal_status' => $internal_status,
                'query2' => $query2));
        } else {
            echo "This Id doesn't Available";
        }
    }

    /**
     *  @desc : This function is to complete the booking.
     *
     * Accepts all the inputs provided in complete booking form and then completes the booking with the details provided.
     *
     *  @param : booking id
     *  @return : completes the booking and load view
     */
    function process_complete_booking_form($booking_id) {
        $data['closing_remarks'] = $this->input->post('closing_remarks');
        $data['service_charge'] = $this->input->post('service_charge');
        $data['service_charge_collected_by'] = $this->input->post('service_charge_collected_by');
        $data['additional_service_charge'] = $this->input->post('additional_service_charge');
        $data['additional_service_charge_collected_by'] = $this->input->post('additional_service_charge_collected_by');
        $data['parts_cost'] = $this->input->post('parts_cost');
        $data['parts_cost_collected_by'] = $this->input->post('parts_cost_collected_by');
        $data['amount_paid'] = $data['service_charge'] + $data['parts_cost'] + $data['additional_service_charge'];
        $data['internal_status'] = $this->input->post('internal_status');
        $data['rating_star'] = $this->input->post('rating_star');
        $data['rating_comments'] = $this->input->post('rating_comments');
        $data['vendor_rating_stars'] = $this->input->post('vendor_rating_star');
        $data['vendor_rating_comments'] = $this->input->post('vendor_rating_comments');

        if ($data['rating_star'] === "Select" && $data['rating_comments'] == '') {
            $data['rating_star'] = "";
            $data['rating_comments'] = "";
        }
        $service_center_data['update_date'] = $data['closed_date'] = date("Y-m-d H:i:s");

	   //TODO: Change this to update_booking
	   $this->booking_model->complete_booking($booking_id, $data);

        $service_center_data['booking_id'] = $booking_id;
        $service_center_data['current_status'] = "Completed";
        $service_center_data['internal_status'] =  $data['internal_status'];
        $this->vendor_model->update_service_center_action($service_center_data);


        //Save this booking id in booking_invoices_mapping table as well now
        $this->invoices_model->insert_booking_invoice_mapping(array('booking_id' => $booking_id));

        //Is this SD booking?
        if (strpos($booking_id, "SS") !== FALSE) {
            $is_sd = TRUE;
        } else {
            $is_sd = FALSE;
        }

        //Update SD bookings if required
        if ($is_sd) {
            if ($this->booking_model->check_sd_lead_exists_by_booking_id($booking_id) === TRUE) {
                $sd_where = array("CRM_Remarks_SR_No" => $booking_id);
                $sd_data = array(
                    "Status_by_247around" => "Completed",
                    "Remarks_by_247around" => $data['internal_status'],
                    "Rating_Stars" => $data['rating_star'],
                    "update_date" => $data['closed_date']
                );
                $this->booking_model->update_sd_lead($sd_where, $sd_data);
            } else {
                //Update Partner leads table
                if (Partner_Integ_Complete) {
                    $partner_where = array("247aroundBookingID" => $booking_id);
                    $partner_data = array(
                        "247aroundBookingStatus" => "Completed",
                        "247aroundBookingRemarks" => $data['internal_status'],
                        "update_date" => $data['closed_date']
                    );
                    $this->partner_model->update_partner_lead($partner_where, $partner_data);

                    //Call relevant partner API
                    //TODO: make it dynamic, use service object model (interfaces)
                    $partner_cb_data = array_merge($partner_where, $partner_data);
                    $this->partner_sd_cb->update_status_complete_booking($partner_cb_data);
                }
            }
        }

	//Log this state change as well for this booking
	$state_change['booking_id'] = $booking_id;
	$state_change['old_state'] = 'Pending';
	$state_change['new_state'] = 'Completed';
	$state_change['agent_id'] = $this->session->userdata('id');
	$this->booking_model->insert_booking_state_change($state_change);

	$query1 = $this->booking_model->booking_history_by_booking_id($booking_id, "join");

        log_message('info', 'Booking Status Change- Booking id: ' . $booking_id . " Completed By " . $this->session->userdata('employee_id'));

        $email['name'] = $query1[0]['name'];
        $email['phone_no'] = $query1[0]['phone_number'];
        $email['user_email'] = $query1[0]['user_email'];
        $email['booking_id'] = $query1[0]['booking_id'];
        $email['service'] = $query1[0]['services'];
        $email['booking_date'] = $query1[0]['booking_date'];
        $email['closed_date'] = $data['closed_date'];
        $email['amount_paid'] = $data['amount_paid'];
        $email['closing_remarks'] = $data['closing_remarks'];
        $email['vendor_name'] = $query1[0]['vendor_name'];
        $email['district'] = $query1[0]['district'];
        $email['tag'] = "complete_booking";
        $email['subject'] = "Booking Completion-AROUND";

        $this->notify->send_email($email);

        if ($is_sd == FALSE) {
            $sms['tag'] = "complete_booking";
            $sms['smsData']['service'] = $query1[0]['services'];
            $sms['phone_no'] = $query1[0]['phone_number'];
            $sms['booking_id'] = $query1[0]['booking_id'];

            $this->notify->send_sms($sms);
        } else {
	    $sms['tag'] = "complete_booking_snapdeal";
	    $sms['smsData']['service'] = $query1[0]['services'];
	    $sms['phone_no'] = $query1[0]['phone_number'];
	    $sms['booking_id'] = $query1[0]['booking_id'];

	    $this->notify->send_sms($sms);
	}

	redirect(base_url() . search_page);
    }

    /**
     *  @desc : This function is to present form to open completed bookings
     *
     * It converts a Completed Booking into Pending booking and schedule it to
     * a new booking date & time.
     *
     *  @param : String (Booking Id)
     *  @return :
     */
    function get_convert_completed_booking_to_pending_form($booking_id) {
	$bookings = $this->booking_model->booking_history_by_booking_id($booking_id);

	$this->load->view('employee/header');
	$this->load->view('employee/complete_to_pending', $bookings[0]);
    }

    /**
     *  @desc : This function is to process form to open completed bookings
     *
     * Accepts the new booking date and timeslot povided in form and then opens
     * a completed booking.
     *
     *  @param : booking id
     *  @return : Converts the booking to Pending stage and load view
     */
    function process_convert_completed_booking_to_pending_form($booking_id) {
	$data['booking_date'] = date('d-m-Y', strtotime($this->input->post('booking_date')));
	$data['booking_timeslot'] = $this->input->post('booking_timeslot');
	$data['current_status'] = 'Pending';
	$data['internal_status'] = 'Scheduled';
	$data['update_date'] = date("Y-m-d H:i:s");
	$data['closed_date'] = NULL;
	$data['vendor_rating_stars'] = NULL;
	$data['vendor_rating_comments'] = NULL;
	$data['service_charge'] = NULL;
	$data['service_charge_collected_by'] = NULL;
	$data['additional_service_charge'] = NULL;
	$data['additional_service_charge_collected_by'] = NULL;
	$data['parts_cost'] = NULL;
	$data['parts_cost_collected_by'] = NULL;
	$data['amount_paid'] = NULL;
	$data['rating_stars'] = NULL;
	$data['rating_comments'] = NULL;
	$data['closing_remarks'] = NULL;
	$data['booking_jobcard_filename'] = NULL;
	$data['mail_to_vendor'] = 0;

	//Is this SD booking?
	if (strpos($booking_id, "SS") !== FALSE) {
	    $is_sd = TRUE;
	} else {
	    $is_sd = FALSE;
	}

	if ($data['booking_timeslot'] == "Select") {
	    echo "Please Select Booking Timeslot.";
	} else {
	    $this->booking_model->convert_completed_booking_to_pending($booking_id, $data);

	    //Update SD leads table if required
	    if ($is_sd) {
		if ($this->booking_model->check_sd_lead_exists_by_booking_id($booking_id) === TRUE) {
		    $sd_where = array("CRM_Remarks_SR_No" => $booking_id);
		    $sd_data = array(
			"Status_by_247around" => $data['current_status'],
			"Remarks_by_247around" => $data['internal_status'],
			"Scheduled_Appointment_DateDDMMYYYY" => $data['booking_date'],
			"Scheduled_Appointment_Time" => $data['booking_timeslot'],
			"update_date" => $data['update_date']
		    );
		    $this->booking_model->update_sd_lead($sd_where, $sd_data);
		}
	    }

	    //Log this state change as well for this booking
	    $state_change['booking_id'] = $booking_id;
	    $state_change['old_state'] = 'Completed';
	    $state_change['new_state'] = 'Pending';
	    $state_change['agent_id'] = $this->session->userdata('id');
	    $this->booking_model->insert_booking_state_change($state_change);

	    $query1 = $this->booking_model->booking_history_by_booking_id($booking_id, "join");

	    $email['booking_id'] = $query1[0]['booking_id'];
	    $email['name'] = $query1[0]['name'];
	    $email['phone_no'] = $query1[0]['phone_number'];
	    $email['service'] = $query1[0]['services'];
	    $email['booking_date'] = $data['booking_date'];
	    $email['booking_timeslot'] = $data['booking_timeslot'];
	    $email['vendor_name'] = $query1[0]['vendor_name'];
	    $email['city'] = $query1[0]['city'];
	    $email['agent'] = $this->session->userdata('employee_id');

	    $email['tag'] = "open_completed_booking";
	    $email['subject'] = "Completed Booking Converted to Pending - AROUND";

	    $this->notify->send_email($email);

	    log_message('info', 'Completed Booking Opened - Booking id: ' . $booking_id . " Opened By: " . $this->session->userdata('employee_id') . " => " . print_r($data, true));

	    redirect(base_url() . search_page);
	}
    }

    /**
     *  @desc : This function is to present form to open cancelled bookings
     *
     * It converts a Cancelled Booking into Pending booking and schedule it to
     * a new booking date & time.
     *
     *  @param : String (Booking Id)
     *  @return :
     */
    function get_convert_cancelled_booking_to_pending_form($booking_id) {
	$bookings = $this->booking_model->booking_history_by_booking_id($booking_id);

	$this->load->view('employee/header');
	$this->load->view('employee/cancelled_to_pending', $bookings[0]);
    }

    /**
     *  @desc : This function is to process form to open cancelled bookings
     *
     * Accepts the new booking date and timeslot povided in form and then opens
     * a cancelled booking.
     *
     *  @param : booking id
     *  @return : Converts the booking to Pending stage and load view
     */
    function process_convert_cancelled_booking_to_pending_form($booking_id) {
	$data['booking_date'] = date('d-m-Y', strtotime($this->input->post('booking_date')));
	$data['booking_timeslot'] = $this->input->post('booking_timeslot');
	$data['current_status'] = 'Pending';
	$data['internal_status'] = 'Scheduled';
	$data['cancellation_reason'] = NULL;
	$data['update_date'] = date("Y-m-d H:i:s");
	$data['closed_date'] = NULL;
	$data['vendor_rating_stars'] = NULL;
	$data['vendor_rating_comments'] = NULL;
	$data['service_charge'] = NULL;
	$data['service_charge_collected_by'] = NULL;
	$data['additional_service_charge'] = NULL;
	$data['additional_service_charge_collected_by'] = NULL;
	$data['parts_cost'] = NULL;
	$data['parts_cost_collected_by'] = NULL;
	$data['amount_paid'] = NULL;
	$data['rating_stars'] = NULL;
	$data['rating_comments'] = NULL;
	$data['closing_remarks'] = NULL;
	$data['booking_jobcard_filename'] = NULL;
	$data['mail_to_vendor'] = 0;

	//Is this SD booking?
	if (strpos($booking_id, "SS") !== FALSE) {
	    $is_sd = TRUE;
	} else {
	    $is_sd = FALSE;
	}

	if ($data['booking_timeslot'] == "Select") {
	    echo "Please Select Booking Timeslot.";
	} else {
	    $this->booking_model->convert_cancelled_booking_to_pending($booking_id, $data);

	    //Update SD leads table if required
	    if ($is_sd) {
		if ($this->booking_model->check_sd_lead_exists_by_booking_id($booking_id) === TRUE) {
		    $sd_where = array("CRM_Remarks_SR_No" => $booking_id);
		    $sd_data = array(
			"Status_by_247around" => $data['current_status'],
			"Remarks_by_247around" => $data['internal_status'],
			"Scheduled_Appointment_DateDDMMYYYY" => $data['booking_date'],
			"Scheduled_Appointment_Time" => $data['booking_timeslot'],
			"update_date" => $data['update_date']
		    );
		    $this->booking_model->update_sd_lead($sd_where, $sd_data);
		}
	    }

	    //Log this state change as well for this booking
	    $state_change['booking_id'] = $booking_id;
	    $state_change['old_state'] = 'Cancelled';
	    $state_change['new_state'] = 'Pending';
	    $state_change['agent_id'] = $this->session->userdata('id');
	    $this->booking_model->insert_booking_state_change($state_change);

	    $query1 = $this->booking_model->booking_history_by_booking_id($booking_id, "join");

	    $email['booking_id'] = $query1[0]['booking_id'];
	    $email['name'] = $query1[0]['name'];
	    $email['phone_no'] = $query1[0]['phone_number'];
	    $email['service'] = $query1[0]['services'];
	    $email['booking_date'] = $data['booking_date'];
	    $email['booking_timeslot'] = $data['booking_timeslot'];
	    $email['vendor_name'] = $query1[0]['vendor_name'];
	    $email['city'] = $query1[0]['city'];
	    $email['agent'] = $this->session->userdata('employee_id');

	    $email['tag'] = "open_cancelled_booking";
	    $email['subject'] = "Cancelled Booking Converted to Pending - AROUND";

	    $this->notify->send_email($email);

	    log_message('info', 'Cancelled Booking Opened - Booking id: ' . $booking_id . " Opened By: " . $this->session->userdata('employee_id') . " => " . print_r($data, true));

	    redirect(base_url() . search_page);
	}
    }

    /**
     *  @desc : This function is to select booking to be canceled.
     *
     * Opens a form with user's name and option to be choosen to cancel the booking.
     *
     * Atleast one booking cancellation reason must be selected.
     *
     * If others option is choosen, then the cancellation reason must be entered in the textarea.
     *
     *  @param : String $booking id Booking ID
     *  @param : Bool $pending_booking
     *
     * It is 1 if a pending booking is getting cancelled.
     * It is 0 if a completed booking is getting cancelled.
     *
     *  @return : user details and booking history to view
     */
    function get_cancel_booking_form($booking_id, $pending_booking) {
	$data['user_and_booking_details'] = $this->booking_model->booking_history_by_booking_id($booking_id);
        $data['reason'] = $this->booking_model->cancelreason("247around");
	$data['pending_booking'] = $pending_booking;

	$this->load->view('employee/header');
        $this->load->view('employee/cancelbooking', $data);
    }

    /**
     *  @desc : This function is to cancel the booking
     *
     * Accepts the cancellation reason provided in cancel booking form and then cancels
     * booking with the reason provided.
     *
     *  @param : String $booking id Booking ID
     *  @param : Bool $pending_booking
     *
     * It is 1 if a pending booking is getting cancelled.
     * It is 0 if a completed booking is getting cancelled.
     *
     *  @return : cancels the booking and load view
     */
    function process_cancel_booking_form($booking_id, $pending_booking) {
	$data['cancellation_reason'] = $this->input->post('cancellation_reason');

        $data['update_date'] = date("Y-m-d H:i:s");
        $data['closed_date'] = date("Y-m-d H:i:s");

        if ($data['cancellation_reason'] === 'Other') {
            $data['cancellation_reason'] = "Other : " . $this->input->post("cancellation_reason_text");
        }
        $data['current_status'] = "Cancelled";
        $data['internal_status'] = "Cancelled";

	$data['service_charge'] = NULL;
	$data['service_charge_collected_by'] = NULL;
	$data['additional_service_charge'] = NULL;
	$data['additional_service_charge_collected_by'] = NULL;
	$data['parts_cost'] = NULL;
	$data['parts_cost_collected_by'] = NULL;
	$data['amount_paid'] = NULL;

	//TODO: cancel_completed_booking() can be merged with cancel_booking() later
	if ($pending_booking)
	    $this->booking_model->cancel_booking($booking_id, $data);
	else
	    $this->booking_model->cancel_completed_booking($booking_id, $data);

	//Update this booking in vendor action table as well if required
	$data_vendor['update_date'] = date("Y-m-d H:i:s");
	$data_vendor['current_status'] = "Cancelled";
	$data_vendor['internal_status'] = "Cancelled";
    $data_vendor['cancellation_reason'] = $data['cancellation_reason'];
	$data_vendor['booking_id'] = $booking_id;

	$this->vendor_model->update_service_center_action($data_vendor);

	//Update SD leads table if required
        //$this->booking_model->update_sd_lead_status($booking_id, 'Cancelled');
        //Is this SD booking?
        if (strpos($booking_id, "SS") !== FALSE) {
            $is_sd = TRUE;
        } else {
            $is_sd = FALSE;
        }

        if ($is_sd) {
            if ($this->booking_model->check_sd_lead_exists_by_booking_id($booking_id) === TRUE) {
                $sd_where = array("CRM_Remarks_SR_No" => $booking_id);
                $sd_data = array(
                    "Status_by_247around" => $data['current_status'],
                    "Remarks_by_247around" => $data['internal_status'],
                    "update_date" => $data['update_date']
                );
                $this->booking_model->update_sd_lead($sd_where, $sd_data);
            } else {
                if (Partner_Integ_Complete) {
                    //Update Partner leads table
                    $partner_where = array("247aroundBookingID" => $booking_id);
                    $partner_data = array(
                        "247aroundBookingStatus" => $data['current_status'],
                        "247aroundBookingRemarks" => $data['internal_status'],
                        "update_date" => $data['update_date']
                    );
                    $this->partner_model->update_partner_lead($partner_where, $partner_data);

                    //Call relevant partner API
                    //TODO: make it dynamic, use service object model (interfaces)
                    $partner_cb_data = array_merge($partner_where, $partner_data);
                    $this->partner_sd_cb->update_status_cancel_booking($partner_cb_data);
                }
            }
        }

	//Log this state change as well for this booking
	$state_change['booking_id'] = $booking_id;

	if ($pending_booking) {
	    $state_change['old_state'] = 'Pending';
	} else {
	    $state_change['old_state'] = 'Completed';
	}

	$state_change['new_state'] = 'Cancelled';
	$state_change['agent_id'] = $this->session->userdata('id');
	$this->booking_model->insert_booking_state_change($state_change);

	$query1 = $this->booking_model->booking_history_by_booking_id($booking_id, "join");

	$email['name'] = $query1[0]['name'];
        $email['phone_no'] = $query1[0]['phone_number'];
        $email['user_email'] = $query1[0]['user_email'];
        $email['booking_id'] = $query1[0]['booking_id'];
        $email['service'] = $query1[0]['services'];
        $email['booking_date'] = $query1[0]['booking_date'];
        $email['booking_timeslot'] = $query1[0]['booking_timeslot'];
        $email['update_date'] = $data['update_date'];
        $email['cancellation_reason'] = $data['cancellation_reason'];
        $email['vendor_name'] = $query1[0]['vendor_name'];
        $email['district'] = $query1[0]['district'];

	if ($pending_booking) {
	    $email['tag'] = "cancel_booking";
	    $email['subject'] = "Pending Booking Cancellation - AROUND";

	    $this->notify->send_email($email);

	    if ($is_sd == FALSE) {
		$sms['tag'] = "cancel_booking";
		$sms['smsData']['service'] = $query1[0]['services'];
		$sms['phone_no'] = $query1[0]['phone_number'];
		$sms['booking_id'] = $query1[0]['booking_id'];

		$this->notify->send_sms($sms);
	    }

	    log_message('info', 'Booking Status Change - Pending Booking ID: ' . $booking_id . " Cancelled By " . $this->session->userdata('employee_id'));
	} else {
	    $email['tag'] = "cancel_completed_booking";
	    $email['subject'] = "Completed Booking Cancellation - AROUND";

	    $this->notify->send_email($email);

	    log_message('info', 'Booking Status Change - Completed Booking ID: ' . $booking_id . " Cancelled By " . $this->session->userdata('employee_id'));
	}

        redirect(base_url() . search_page);
    }

    /**
     *  @desc : This function is to select booking to be rescheduled
     *
     * Opens a form with user's name and current date and timeslot.
     *
     * Select the new date and timeslot for current booking.
     *
     *  @param : booking id
     *  @return : user details and booking history to view
     */
    function get_reschedule_booking_form($booking_id) {
        $getbooking = $this->booking_model->getbooking($booking_id);

        if ($getbooking) {
//	    $employee_id = $this->session->userdata('employee_id'); // variable is unused
            $this->session->userdata('employee_id');
            $data['booking_id'] = $getbooking;

            $query = $this->booking_model->booking_history_by_booking_id($booking_id);

            $data1['booking_id'] = $query;

            $this->load->view('employee/header');
            $this->load->view('employee/reschedulebooking', array('data' => $data, 'data1' => $data1));
        } else {
            echo "This Id doesn't Exists";
        }
    }

    /**
     *  @desc : This function is to reschedule the booking.
     *
     * Accepts the new booking date and timeslot povided in form and then reschedules booking
     * accordingly.
     *
     *  @param : booking id
     *  @return : reschedules the booking and load view
     */
    function process_reschedule_booking_form($booking_id) {
        $data['booking_date'] = date('d-m-Y', strtotime($this->input->post('booking_date')));

        $yy = date("y", strtotime($data['booking_date']));
        $mm = date("m", strtotime($data['booking_date']));
        $dd = date("d", strtotime($data['booking_date']));

        $data['booking_timeslot'] = $this->input->post('booking_timeslot');
        $data['current_status'] = 'Rescheduled';
        $data['internal_status'] = 'Rescheduled';
        $data['update_date'] = date("Y-m-d H:i:s");

        //Is this SD booking?
        if (strpos($booking_id, "SS") !== FALSE) {
            $is_sd = TRUE;
        } else {
            $is_sd = FALSE;
        }

        if ($data['booking_timeslot'] == "Select") {
            echo "Please Select Booking Timeslot.";
        } else {
            //$insertData = $this->booking_model->reschedule_booking($booking_id, $data);
            $this->booking_model->reschedule_booking($booking_id, $data);

            $service_center_data['booking_id'] = $booking_id;
            $service_center_data['internal_status'] = "Pending";
            $service_center_data['current_status'] = "Pending";
            $service_center_data['update_date'] = date("Y-m-d H:i:s");
            $this->vendor_model->update_service_center_action($service_center_data);

            //Update SD leads table if required
            if ($is_sd) {
                if ($this->booking_model->check_sd_lead_exists_by_booking_id($booking_id) === TRUE) {
                    $sd_where = array("CRM_Remarks_SR_No" => $booking_id);
                    $sd_data = array(
                        "Status_by_247around" => $data['current_status'],
                        "Remarks_by_247around" => $data['internal_status'],
                        "Scheduled_Appointment_DateDDMMYYYY" => $data['booking_date'],
                        "Scheduled_Appointment_Time" => $data['booking_timeslot'],
                        "update_date" => $data['update_date']
                    );
                    $this->booking_model->update_sd_lead($sd_where, $sd_data);
                } else {
                    if (Partner_Integ_Complete) {
                        //Update Partner leads table
                        $sch_date = date_format(date_create($yy . "-" . $mm . "-" . $dd), "Y-m-d H:i:s");
                        $partner_where = array("247aroundBookingID" => $booking_id);
                        $partner_data = array(
                            "247aroundBookingStatus" => $data['current_status'],
                            "247aroundBookingRemarks" => $data['internal_status'],
                            "ScheduledAppointmentDate" => $sch_date,
                            "ScheduledAppointmentTime" => $data['booking_timeslot'],
                            "update_date" => $data['update_date']
                        );
                        $this->partner_model->update_partner_lead($partner_where, $partner_data);

                        //Call relevant partner API
                        //TODO: make it dynamic, use service object model (interfaces)
                        $partner_cb_data = array_merge($partner_where, $partner_data);
                        $this->partner_sd_cb->update_status_reschedule_booking($partner_cb_data);
                    }
                }
            }


            $query1 = $this->booking_model->booking_history_by_booking_id($booking_id);

            $email['name'] = $query1[0]['name'];
            $email['phone_no'] = $query1[0]['phone_number'];
            $email['user_email'] = $query1[0]['user_email'];
            $email['booking_id'] = $query1[0]['booking_id'];
            $email['service'] = $query1[0]['services'];
            $email['booking_date'] = $data['booking_date'];
            $email['booking_timeslot'] = $data['booking_timeslot'];
            $email['update_date'] = $data['update_date'];
            $email['booking_address'] = $query1[0]['booking_address'];
            $email['tag'] = "reschedule_booking";
            $email['subject'] = "Booking Rescheduled-AROUND";

            //$this->notify->sendEmail($from, $to, $cc, $bcc, $subject, $message, $attachment);
            $this->notify->send_email($email);

            $months = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
            $mm = $months[$mm - 1];
            $data['booking_date'] = $dd . $mm;
            if ($data['booking_timeslot'] == "10AM-1PM") {
                $data['booking_timeslot'] = "1PM";
            } elseif ($data['booking_timeslot'] == "1PM-4PM") {
                $data['booking_timeslot'] = "4PM";
            } elseif ($data['booking_timeslot'] == "4PM-7PM") {
                $data['booking_timeslot'] = "7PM";
            }

            if ($is_sd == FALSE) {
                $sms['tag'] = "reschedule_booking";
                $sms['smsData']['service'] = $query1[0]['services'];
                $sms['smsData']['booking_date'] = $data['booking_date'];
                $sms['smsData']['booking_timeslot'] = $data['booking_timeslot'];
                $sms['phone_no'] = $query1[0]['phone_number'];
                $sms['booking_id'] = $query1[0]['booking_id'];

                $this->notify->send_sms($sms);
            }

            //Setting mail to vendor flag to 0, once booking is rescheduled
            $this->booking_model->set_mail_to_vendor_flag_to_zero($booking_id);

	    //Prepare job card
	    $this->booking_utilities->lib_prepare_job_card_using_booking_id($booking_id);

	    log_message('info', 'Rescheduled- Booking id: ' . $booking_id . " Rescheduled By " . $this->session->userdata('employee_id') . " data " . print_r($data, true));

	    redirect(base_url() . search_page);
        }
    }

    /**
     * @desc : This function will get all the brands for that particular service with help of service_id on ajax call
     * @param: service_id of booking
     * @return : all present brands
     */
    function getBrandForService($service_id) {

        $result = $this->booking_model->getBrandForService($service_id);
        foreach ($result as $brand) {
            echo "<option>$brand[brand_name]</option>";
        }
    }

    /**
     * @desc : This function will load category with help of service_id on ajax call
     * @param: service_id of booking
     * @return : all category present
     */
    function getCategoryForService($service_id) {

        $result = $this->booking_model->getCategoryForService($service_id);

        foreach ($result as $category) {
            echo "<option>$category[category]</option>";
        }
    }

    /**
     * @desc : This function will load capacity with help of Category and service_id on ajax call
     * @param: Category and service_id of booking
     * @return : all capacity present
     */
    public function getCapacityForCategory($service_id, $category) {
        //Return column "capacity", only unique results
        $category = urldecode($category);

        $result = $this->booking_model->getCapacityForCategory($service_id, $category);

        foreach ($result as $capacity) {
            echo "<option>$capacity[capacity]</option>";
        }
    }

    /**
     * @desc : This function will show the price and services for ajax call
     *
     * Shows the service name and their prices in a table.
     *
     * Select(check) the service/services to be performed.
     *
     * @param: service_id,category and capacity of the booking
     * @return : services name and there prices
     */
    public function getPricesForCategoryCapacity($service_id, $category, $capacity) {
        //Return columns "service_category" and "total_charges",
        if ($capacity != "NULL") {
            $capacity = urldecode($capacity);
        } else {
            $capacity = "";
        }
        $category = urldecode($category);

        $result = $this->booking_model->getPricesForCategoryCapacity($service_id, $category, $capacity);

        echo "<tr><th>Service Category</th><th>Total Charges</th><th>Selected Services</th></tr>";
        foreach ($result as $prices) {
            echo "<tr><td>" . $prices['service_category'] . "</td><td>" .
            $prices['total_charges'] .
            "</td><td><input id='Checkbox1' class='Checkbox1' type='checkbox' " .
            "name='" . str_replace(" ", "", $prices['service_category']) . "'" .
            "value=" . $prices['total_charges'] . "></td><tr>";
        }
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
            if ($service_center_id != "Select") {

                $data = array();
                $data['booking_id'] = $booking_id;
                $data['service_center_id'] = $service_center_id;

                $this->asynchronous_lib->do_background_process($url, $data);
            }
        }

        redirect(base_url() . search_page);
    }

    /**
     *  @desc : Ajax call(This function is to get non working days for particular vendor)
     *
     *  To know the non working days for the selected vendor.
     *
     *  @param : vendor's id(service centre id)
     *  @return : Non working days for particular vendor
     */
    function get_non_working_days_for_vendor($service_centre_id) {
        $result = $this->vendor_model->get_non_working_days_for_vendor($service_centre_id);
        if (empty($result)) {
            echo "No non working days found";
        }
        $non_working_days = $result[0]['non_working_days'];
        echo $non_working_days;
    }

    /**
     *  @desc : This function is to select completed booking to be rated
     *  @param : booking id
     *  @return : user details to view
     */
    function get_rating_form($booking_id) {
        $getbooking = $this->booking_model->getbooking($booking_id);
        if ($getbooking) {
//	    $employee_id = $this->session->userdata('employee_id'); // variable is not used
            $this->session->userdata('employee_id');
            $data = $getbooking;
            $this->load->view('employee/header');
            $this->load->view('employee/rating', array('data' => $data));
        } else {
            echo "Id doesn't exist";
        }
    }

    /**
     *  @desc : This function is to save ratings for booking and for vendors
     *
     * With the help of this form you can rate the booking as per user experience and for vendors for the quality of service provided by the vendor.
     *
     *  @param : booking id
     *  @return : rate for booking and load view
     */
    function process_rating_form($booking_id) {

        if ($this->input->post('rating_star') != "Select") {
            $data['rating_stars'] = $this->input->post('rating_star');
            $data['rating_comments'] = $this->input->post('rating_comments');
        } else {
            $data['rating_stars'] = '';
            $data['rating_comments'] = '';
        }

        if ($this->input->post('vendor_rating_star') != "Select") {
            $data['vendor_rating_stars'] = $this->input->post('vendor_rating_star');
            $data['vendor_rating_comments'] = $this->input->post('vendor_rating_comments');
        } else {
            $data['vendor_rating_stars'] = '';
            $data['vendor_rating_comments'] = '';
        }

        $this->booking_model->update_booking($booking_id, $data);

        //Is this SD booking?
        if (strpos($booking_id, "SS") !== FALSE) {
            $is_sd = TRUE;
        } else {
            $is_sd = FALSE;
        }

        //Update SD bookings if required
        if ($is_sd) {
            $sd_where = array("CRM_Remarks_SR_No" => $booking_id);
            $sd_data = array(
                "Rating_Stars" => $data['rating_stars'],
                "update_date" => $data['closed_date']
            );
            $this->booking_model->update_sd_lead($sd_where, $sd_data);
        }

        redirect(base_url() . 'employee/booking/viewcompletedbooking', 'refresh');
    }

    /**
     *  @desc : This function is to save ratings for vendors
     *
     * With the help of this form you can rate for vendors for the quality of service provided by the vendor.
     *
     *  @param : booking id
     *  @return : rate for booking and load view
     */
    function vendor_rating($booking_id) {
        $this->booking_model->vendor_rating($booking_id, $data);
        $query = $this->booking_model->viewbooking();
        $data['Bookings'] = null;
        if ($query) {
            $data['Bookings'] = $query;
        }
        $this->load->view('employee/header');
        $this->load->view('employee/booking', $data);
    }

    /**
     *  @desc : This function is to select queries for confirmation
     *
     *  This form is used to get form to convert query to booking or again query by extending date.
     *
     *  @param : booking id
     *  @return : user, booking details, appliance id, category and service to view
     */
    function get_update_query_form($booking_id) {
        //get booking details
        $query1 = $this->booking_model->getbooking($booking_id);
        //get uit details
        $query2 = $this->booking_model->get_unit_details($booking_id);
        $page = "FollowUp";
        $internal_status = $this->booking_model->get_internal_status($page);

        if ($query1) {
            //get user and other details
            $query3 = $this->booking_model->booking_history_by_booking_id($booking_id);
            //echo print_r($query3, true);
        }

        $service_id = $query1[0]['service_id'];
        //echo print_r($service_id, true);

        $appliance_id = $query1[0]['appliance_id'];
        $all_brands = $this->booking_model->getBrandForService($service_id);
        $all_categories = $this->booking_model->getCategoryForService($service_id);
        //echo print_r($all_categories, true);
        $all_capacities = $this->booking_model->getCapacityForAppliance($service_id);
        //echo print_r($all_capacities, true);

        if (count($query2) > 0) {
            $unit_id = $query2[0]['id'];
            $brand = $query2[0]['appliance_brand'];

            //rearrange brands array so that $brand comes on top
            $brands = array(0 => array("brand_name" => $brand));
            foreach ($all_brands as $value) {
                if ($brand != $value['brand_name']) {
                    array_push($brands, $value);
                }
            }

            $category = $query2[0]['appliance_category'];

            //rearrange categories array so that $category comes on top
            $categories = array(0 => array("category" => $category));
            foreach ($all_categories as $value) {
                if ($category != $value['category']) {
                    array_push($categories, $value);
                }
            }

            $all_capacities = $this->booking_model->getCapacityForCategory($service_id, $category);
            $capacity = $query2[0]['appliance_capacity'];

            //rearrange capacities array so that $capacity comes on top
            $capacities = array(0 => array("capacity" => $capacity));
            foreach ($all_capacities as $value) {
                if ($capacity != $value['capacity']) {
                    array_push($capacities, $value);
                }
            }
        } else {
            $unit_id = '';
            $brands = $all_brands;
            $categories = $all_categories;
            $capacities = $all_capacities;
        }

        $this->load->view('employee/header');
        $this->load->view('employee/update_query', array(
            'query1' => $query1,
            'unit_details' => $query2[0],
            'internal_status' => $internal_status,
            'query3' => $query3,
            'unit_id' => $unit_id,
            'appliance_id' => $appliance_id,
            'brands' => $brands,
            'categories' => $categories,
            'capacities' => $capacities));
    }

    /**
     *  @desc : This function is to process the followup
     *
     *  We can either make it a booking or extend the date in case of query.
     *
     *  @param : booking id
     *  @return : confirms as booking/query and load view
     */
    function process_update_query_form($booking_id) {
        $booking['user_id'] = $this->input->post('user_id');
        $booking['service_id'] = $this->input->post('service_id');

        //Appliance details
        $booking['appliance_brand'] = $this->input->post('appliance_brand');
        $booking['appliance_category'] = $this->input->post('appliance_category');
        $booking['appliance_capacity'] = $this->input->post('appliance_capacity');
        $booking['purchase_year'] = $this->input->post('purchase_year');
        $booking['appliance_tag'] = $this->input->post('appliance_tag');
        $booking['model_number'] = $this->input->post('model_number');
	    $booking['partner_source'] = $this->input->post('partner_source');
	    $booking['order_id'] = $this->input->post('order_id');

	    $booking['booking_primary_contact_no'] = $this->input->post('booking_primary_contact_no');
        $booking['booking_alternate_contact_no'] = $this->input->post('booking_alternate_contact_no');

        $booking['total_price'] = $this->input->post('total_price');
        $booking['potential_value'] = $this->input->post('potential_value');
        $booking['items_selected'] = $this->input->post('items_selected');
        $booking['booking_date'] = $this->input->post('booking_date');
        $booking['query_remarks'] = $this->input->post('query_remarks');
        $booking['booking_remarks'] = $this->input->post('booking_remarks');
        $booking['booking_timeslot'] = $this->input->post('booking_timeslot');
        $booking['booking_address'] = $this->input->post('booking_address');
        $booking['booking_pincode'] = $this->input->post('booking_pincode');
        $booking['quantity'] = $this->input->post('quantity');

        //internal_status would be empty if booking is confirmed
        if ($this->input->post('internal_status') != "") {
            $booking['internal_status'] = $this->input->post('internal_status');
        } else {
            $booking['internal_status'] = "FollowUp";
        }

        $booking['type'] = "Query";
        $booking['amount_due'] = $booking['total_price'];
        $booking['booking_date'] = date('d-m-Y', strtotime($booking['booking_date']));
        $yy = date("y", strtotime($booking['booking_date']));
        $mm = date("m", strtotime($booking['booking_date']));
        $dd = date("d", strtotime($booking['booking_date']));

        $unit_id = $this->input->post('unit_id');
        $appliance_id = $this->input->post('appliance_id');

        //Insert appliance if required
        if (!$appliance_id) {
            $appliance_id = $this->booking_model->addsingleappliance($booking);
            $booking['appliance_id'] = $appliance_id[0]['id'];
        } else {
            $booking['appliance_id'] = $appliance_id;
            $this->booking_model->update_appliance_details($booking);
        }

        //Is this SD booking?
        //TODO: Check whether this is a Partner booking
        if (strpos($booking_id, "SS") !== FALSE) {
            $is_sd = TRUE;
        } else {
            $is_sd = FALSE;
        }
        //Check to find which button is clicked
        //echo print_r($this->input->post('sbm'), true);
        if ($this->input->post('sbm') == "Confirm Booking") {
            //Remove "Q-" from booking ID
            $booking['booking_id'] = substr($booking_id, 2);
            $booking['current_status'] = "Pending";
            $booking['internal_status'] = "Scheduled";
            //$booking['potential_value'] = 0;


            //Updating booking details
            if ($this->booking_model->update_booking_details($booking_id, $booking)) {
                $booking['serial_number'] = $this->input->post('serial_number');
                if (!$unit_id) {
                    //Insert unit appliance
                    $this->booking_model->add_single_unit_details($booking);
                    } else {
                        //Update unit appliance
                        $this->booking_model->update_booking_unit_details($booking_id, $booking);
                }
                //Update SD leads table if required
                if ($is_sd) {
                    if ($this->booking_model->check_sd_lead_exists_by_booking_id($booking_id) === TRUE) {
                        //Booking came through old method of excel file sharing
                        //Update it in snapdeal_leads table
                        $sd_where = array("CRM_Remarks_SR_No" => $booking_id);
                        $sd_data = array(
                            "CRM_Remarks_SR_No" => $booking['booking_id'],
                            "Status_by_247around" => $booking['current_status'],
                            "Remarks_by_247around" => $booking['internal_status'],
                            "Scheduled_Appointment_DateDDMMYYYY" => $booking['booking_date'],
                            "Scheduled_Appointment_Time" => $booking['booking_timeslot'],
                            "update_date" => date("Y-m-d H:i:s")
                        );
                        $this->booking_model->update_sd_lead($sd_where, $sd_data);
                    } else {
                        //Update Partner leads table
                        if (Partner_Integ_Complete) {
                            $sch_date = date_format(date_create($yy . "-" . $mm . "-" . $dd), "Y-m-d H:i:s");
                            $partner_where = array("247aroundBookingID" => $booking_id);
                            $partner_data = array(
                                "247aroundBookingStatus" => $booking['current_status'],
                                "247aroundBookingRemarks" => $booking['internal_status'],
                                "ScheduledAppointmentDate" => $sch_date,
                                "ScheduledAppointmentTime" => $booking['booking_timeslot'],
                                "update_date" => date("Y-m-d H:i:s")
                            );
                            $this->partner_model->update_partner_lead($partner_where, $partner_data);

                            //Call relevant partner API
                            //TODO: make it dynamic, use service object model (interfaces)
                            $partner_cb_data = array_merge($partner_where, $partner_data);
                            $this->partner_sd_cb->update_status_schedule_booking($partner_cb_data);
                        }
                    }
                }

		//Log this state change as well for this query
		$state_change['booking_id'] = $booking['booking_id'];
		$state_change['old_state'] = 'FollowUp';
		$state_change['new_state'] = 'Pending';
		$state_change['agent_id'] = $this->session->userdata('id');
		$this->booking_model->insert_booking_state_change($state_change);

		$query1 = $this->booking_model->booking_history_by_booking_id($booking['booking_id']);
                //echo print_r($query1, true);
                //$query2 = $this->booking_model->get_unit_details($booking['booking_id']);
                //echo print_r($query2, true);

                $months = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
                $mm = $months[$mm - 1];
                $booking_date = $dd . $mm;
                $booking_timeslot = $booking['booking_timeslot'];

                //-------Sending Email On Booking--------//
                $message = "Congratulations! Query has been converted to Booking, details are mentioned below:
                            <br>Customer Name: " . $query1[0]['name'] . "<br>Customer Phone Number: " .
                        $query1[0]['phone_number'] . "<br>Customer email address: " . $query1[0]['user_email'] .
                        "<br>Booking Id: " . $booking['booking_id'] . "<br>Service name:" . $query1[0]['services'] .
                        "<br>Number of appliance: " . $query1[0]['quantity'] . "<br>Booking Date: " .
                        $booking_date . "<br>Booking Timeslot: " . $booking_timeslot .
                        "<br>Amount Due: " . $query1[0]['amount_due'] . "<br>Your Booking Remark is: " .
                        $booking['booking_remarks'] . "<br>Booking address: " . $booking['booking_address'] .
                        "<br>Booking city: " . $query1[0]['city'] .
                        "<br>Booking pincode: " . $query1[0]['booking_pincode'] . "<br><br>
                              Appliance Details:<br>";

                $appliance = "";

                $appliance = "<br>Brand : " . $booking['appliance_brand'] . "<br>Category : " . $booking['appliance_category'] . "<br>Capacity : " . $booking['appliance_capacity'] .
                        "<br>Selected service/s is/are: " . $booking['items_selected'] . "<br>Total price is: " . $booking['total_price'] . "<br>";
                $message = $message . $appliance;

                $message = $message . "<br> Thanks!!";

                $from = 'booking@247around.com';
                $to = "anuj@247around.com, nits@247around.com";
                $cc = "";
                $bcc = "";
                $subject = 'Booking Confirmation-AROUND';
                $attachment = "";
                //Send mail
                $this->notify->sendEmail($from, $to, $cc, $bcc, $subject, $message, $attachment);

                //TODO: Make it generic
                if ($is_sd == FALSE) {

                    $sms['tag'] = "add_new_booking";
                    $sms['smsData']['service'] = $query1[0]['services'];
                    $sms['smsData']['booking_date'] = $booking_date;
                    $sms['smsData']['booking_timeslot'] = $booking_timeslot;
                    $sms['phone_no'] = $query1[0]['phone_number'];
                    $sms['booking_id'] = $booking['booking_id'];

                    $this->notify->send_sms($sms);
                } else {
                    $sms['tag'] = "new_snapdeal_booking";
                    $sms['smsData']['service'] = $query1[0]['services'];
                    $sms['smsData']['booking_date'] = $booking_date;
                    $sms['smsData']['booking_timeslot'] = $booking_timeslot;
                    $sms['phone_no'] = $query1[0]['phone_number'];
                    $sms['booking_id'] = $booking['booking_id'];

                    $this->notify->send_sms($sms);
                }

                //------End of sending SMS--------//

                redirect(base_url() . search_page);
            } else {
                echo "Booking not inserted";
            }
        }
        //booking not confirmed
        else {
	    $booking['current_status'] = "FollowUp";
            $booking['potential_value'] = $this->input->post('potential_value');
            $booking['booking_id'] = $booking_id;

            //Updating booking details
            $result = $this->booking_model->update_booking_details($booking_id, $booking);

            $booking['serial_number'] = $this->input->post('serial_number');

            if (!$unit_id) {
                //Insert unit appliance
                $this->booking_model->add_single_unit_details($booking);
            } else {
                //Update unit appliance
                $this->booking_model->update_booking_unit_details($booking_id, $booking);
            }

	    //Send SMS if customer didn't pick the call
	    if ($this->input->post('internal_status') == INT_STATUS_CUSTOMER_NOT_REACHABLE) {
		$query1 = $this->booking_model->booking_history_by_booking_id($booking['booking_id']);

		if ($is_sd) {
		    $sms['tag'] = "call_not_picked_snapdeal";
		} else {
		    $sms['tag'] = "call_not_picked_other";
		}
		$sms['smsData']['name'] = $query1[0]['name'];
		$sms['smsData']['service'] = $query1[0]['services'];
		$sms['phone_no'] = $query1[0]['phone_number'];

		$this->notify->send_sms($sms);
	    }

	    if ($result) {
                //Update SD leads table if required
                if ($is_sd) {
                    if ($this->booking_model->check_sd_lead_exists_by_booking_id($booking_id) === TRUE) {
                        $sd_where = array("CRM_Remarks_SR_No" => $booking_id);
                        $sd_data = array(
                            "Status_by_247around" => $booking['current_status'],
                            "Remarks_by_247around" => $booking['internal_status'],
                            "update_date" => date("Y-m-d H:i:s")
                        );
                        $this->booking_model->update_sd_lead($sd_where, $sd_data);
                    } else {
                        if (Partner_Integ_Complete) {
                            //Update Partner leads table
                            $partner_where = array("247aroundBookingID" => $booking_id);
                            $partner_data = array(
                                "247aroundBookingStatus" => $booking['current_status'],
                                "247aroundBookingRemarks" => $booking['internal_status'],
                                "update_date" => date("Y-m-d H:i:s")
                            );
                            $this->partner_model->update_partner_lead($partner_where, $partner_data);

                            //Call relevant partner API
                            //TODO: make it dynamic, use service object model (interfaces)
                            $partner_cb_data = array_merge($partner_where, $partner_data);
                            $this->partner_sd_cb->update_status_schedule_booking($partner_cb_data);
                        }
                    }
                }

                redirect(base_url() . search_page);
            } else {
                echo "Query is not saved";
            }
        }
    }

    /**
     *  @desc : This function is to select queries for cancellation
     *
     *  Opens a new form through which we can select the cancellation reason
     *
     *  @param : booking id
     *  @return : users, booking details and cancellation reason to view
     */
    function get_cancel_followup_form($booking_id) {
        $query = $this->booking_model->getbooking($booking_id);
        $reasons = $this->booking_model->cancelreason("247around");
        $page = "Cancel";
        $internal_status = $this->booking_model->get_internal_status($page);
        $this->load->view('employee/header');
        $this->load->view('employee/cancelfollowup', array('query' => $query[0],
            'reasons' => $reasons,
            'internal_status' => $internal_status));
    }

    /**
     *  @desc : This function is to cancel the query
     *
     * 	We have to select one reason for the cancellation of the query.
     *
     * 	If other is selected, enter the cancellation reason in textarea.
     *
     *  @param : booking id
     *  @return : cancel the query and load view
     */
    function process_cancel_followup_form($booking_id) {
        $booking['current_status'] = "Cancelled";
        $booking['internal_status'] = $this->input->post('internal_status');
        $booking['cancellation_reason'] = $this->input->post('cancellation_reason');
        $booking['closed_date'] = date("Y-m-d H:i:s");
        $booking['update_date'] = $booking['closed_date'];

        //Is this SD booking?
        if (strpos($booking_id, "SS") !== FALSE) {
            $is_sd = TRUE;
        } else {
            $is_sd = FALSE;
        }

        if ($booking['cancellation_reason'] == 'Other') {
            if ($is_sd) {
                //For SD bookings, save internal status as cancellation reason
                $booking['cancellation_reason'] = "Other : " . $booking['internal_status'];
            } else {
                //For other bookings, save other reason text
                $booking['cancellation_reason'] = "Other : " . $this->input->post("cancellation_reason_text");
            }
        }

        $this->booking_model->cancel_followup($booking_id, $booking);

        //Update SD bookings if required
        if ($is_sd) {
            if ($this->booking_model->check_sd_lead_exists_by_booking_id($booking_id) === TRUE) {
                $sd_where = array("CRM_Remarks_SR_No" => $booking_id);
                $sd_data = array(
                    "Status_by_247around" => "Cancelled",
                    "Remarks_by_247around" => $booking['internal_status'],
                    "update_date" => $booking['closed_date']
                );
                $this->booking_model->update_sd_lead($sd_where, $sd_data);
            } else {
                if (Partner_Integ_Complete) {
                    //Update Partner leads table
                    $partner_where = array("247aroundBookingID" => $booking_id);
                    $partner_data = array(
                        "247aroundBookingStatus" => "Cancelled",
                        "247aroundBookingRemarks" => $booking['internal_status'],
                        "update_date" => $booking['closed_date']
                    );
                    $this->partner_model->update_partner_lead($partner_where, $partner_data);

                    //Call relevant partner API
                    //TODO: make it dynamic, use service object model (interfaces)
                    $partner_cb_data = array_merge($partner_where, $partner_data);
                    $this->partner_sd_cb->update_status_cancel_booking($partner_cb_data);
                }
            }
        }

	//Log this state change as well for this query
	$state_change['booking_id'] = $booking_id;
	$state_change['old_state'] = 'FollowUp';
	$state_change['new_state'] = 'Cancelled';
	$state_change['agent_id'] = $this->session->userdata('id');
	$this->booking_model->insert_booking_state_change($state_change);

	$query1 = $this->booking_model->booking_history_by_booking_id($booking_id);

	log_message('info', 'Query Status Change - Booking ID: ' . $booking_id . " Cancelled By " . $this->session->userdata('employee_id'));

	$email['name'] = $query1[0]['name'];
        $email['phone_no'] = $query1[0]['phone_number'];
        $email['booking_id'] = $query1[0]['booking_id'];
        $email['service'] = $query1[0]['services'];
        $email['booking_date'] = $query1[0]['booking_date'];
        $email['cancellation_reason'] = $booking['cancellation_reason'];

	$email['tag'] = "cancel_query";
	$email['subject'] = "Query Cancellation - AROUND";

	$this->notify->send_email($email);

        redirect(base_url() . search_page);
    }

    /**
     *  @desc : This function is to create jobcard
     *
     * 	Jobcard is created and attached in mail when we reschedule booking and is sent to the vendor to whome we assign this booking.
     *
     *  @param : booking id
     *  @return : void
     */
    function jobcard($booking_id) {
        $query1 = $this->booking_model->booking_history_by_booking_id($booking_id);
        $query2 = $this->booking_model->get_unit_details($booking_id);

        $this->load->view('employee/header');
        $this->load->view('employee/unassignedjobcard', array('query1' => $query1, 'query2' => $query2));
    }

    /**
     *  @desc : This function is to view deatils of any particular booking.
     *
     * 	We get all the details like User's details, booking details, and also the appliance's unit details.
     *
     *  @param : booking id
     *  @return : booking details and load view
     */
    function viewdetails($booking_id) {
        $data['query1'] = $this->booking_model->booking_history_by_booking_id($booking_id);
        $data['query2'] = $this->booking_model->get_unit_details($booking_id);
        $data['query4'] = $this->booking_model->getdescription_about_booking($booking_id);

        $data['query3'] = $this->booking_model->selectservicecentre($booking_id);
        if (count($data['query3']) == 0) {
            //Service centre not assigned yet
            $data['query3'][0]['service_centre_name'] = 'NA';
            $data['query3'][0]['primary_contact_name'] = 'NA';
            $data['query3'][0]['primary_contact_phone_1'] = 'NA';
        }

        $this->load->view('employee/header');
        $this->load->view('employee/viewdetails', $data);
    }

    /**
     *  @desc : Function to sort pending bookings with current status
     *
     * 	This will display all the bookings present in sorted manner according to there booking status.
     *
     *  @param : start booking and bookings per page
     *  @return : bookings and load view
     */
    function status_sorted_booking($offset = 0, $page = 0) {
        if ($page == 0) {
            $page = 50;
        }

        $offset = ($this->uri->segment(4) != '' ? $this->uri->segment(4) : 0);
        $config['base_url'] = base_url() . 'employee/booking/status_sorted_booking';
        $config['total_rows'] = $this->booking_model->total_pending_booking();
        $config['per_page'] = $page;
        $config['uri_segment'] = 4;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';

        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['Bookings'] = $this->booking_model->status_sorted_booking($config['per_page'], $offset);
        $this->load->view('employee/header');

        $this->load->view('employee/statussortedbooking', $data);
    }

    /**
     *  @desc : Function to sort pending and rescheduled bookings with booking date
     *
     * 	This method will display all the pending and rescheduled bookings present in sorted manner according to there booking date.
     *
     *  @param : start booking and bookings per page
     *  @return : sorted bookings and load view
     */
    function date_sorted_booking($offset = 0, $page = 0) {
        if ($page == 0) {
            $page = 50;
        }
        //$offset = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
        $offset = ($this->uri->segment(4) != '' ? $this->uri->segment(4) : 0);
        $config['base_url'] = base_url() . 'employee/booking/date_sorted_booking';
        $config['total_rows'] = $this->booking_model->total_pending_booking();
        $config['per_page'] = $page;
        $config['uri_segment'] = 4;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';

        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['Bookings'] = $this->booking_model->date_sorted_booking($config['per_page'], $offset);
        $this->load->view('employee/header');

        $this->load->view('employee/datesortedbooking', $data);
    }

    /**
     *  @desc : Function to sort pending and rescheduled bookings with service center's name
     *
     * 	This method will display all the pending and rescheduled bookings present in
     *      sorted manner according to service centre's name assigned for the booking.
     *
     * 	This function is usefull to get all the bookings assigned to particular vendor together.
     *
     *  @param : start booking and bookings per page
     *  @return : assigned vendor sorted bookings and load view
     */
    function service_center_sorted_booking($offset = 0, $page = 0) {
        if ($page == 0) {
            $page = 50;
        }

        $offset = ($this->uri->segment(4) != '' ? $this->uri->segment(4) : 0);
        $config['base_url'] = base_url() . 'employee/booking/service_center_sorted_booking';
        $config['total_rows'] = $this->booking_model->total_pending_booking();
        $config['per_page'] = $page;
        $config['uri_segment'] = 4;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();
        $data['Bookings'] = $this->booking_model->service_center_sorted_booking($config['per_page'], $offset);
        $this->load->view('employee/header');
        $this->load->view('employee/booking', $data);
    }

    /**
     *  @desc : This method is to select completed bookings for editing
     *
     * 	A form will open with basic booking details.
     *
     * 	Through this form we can only edit the prices(service charge, parts cost, etc)
      and also collected by.
     *
     * 	We can also edit the closing remarks entered during completing the booking earliar with wrong details(remarks).
     *
     *  @param : booking id
     *  @return : user's and booking details to view
     */
    function get_edit_completed_booking_form($booking_id) {
        $getbooking = $this->booking_model->getbooking($booking_id);

        $query2 = $this->booking_model->get_unit_details($booking_id);
        if ($getbooking) {
//	    $employee_id = $this->session->userdata('employee_id');  //variable looks unused, can be removed after checking
            $this->session->userdata('employee_id');

            $data = $getbooking;

            $query = $this->booking_model->booking_history_by_booking_id($booking_id);

            $data1 = $query;

            $this->load->view('employee/header');
            $this->load->view('employee/editcompletedbooking', array('data' => $data,
                'data1' => $data1,
                'query2' => $query2));
        } else {
            echo "This Id doesn't Available";
        }
    }

    /**
     *  @desc : This method will edit the completed booking details
     *
     * 	Through this the prices(service charge, parts cost, etc) and collected by is edited(if changed).
     *
     * 	Closing remarks also get edited if changed.
     *
     *  @param : booking id
     *  @return : edits the completed booking and load view
     */
    function process_edit_completed_booking_form($booking_id) {
        $data['service_charge'] = $this->input->post('service_charge');
        $data['service_charge_collected_by'] = $this->input->post('service_charge_collected_by');
        $data['additional_service_charge'] = $this->input->post('additional_service_charge');
        $data['additional_service_charge_collected_by'] = $this->input->post('additional_service_charge_collected_by');
        $data['parts_cost'] = $this->input->post('parts_cost');
        $data['parts_cost_collected_by'] = $this->input->post('parts_cost_collected_by');
        $data['closing_remarks'] = $this->input->post('closing_remarks');
        $data['booking_remarks'] = $this->input->post('booking_remarks');
        $data['amount_paid'] = $data['service_charge'] + $data['parts_cost'] + $data['additional_service_charge'];

//	$insertData = $this->booking_model->edit_completed_booking($booking_id, $data);
        $this->booking_model->edit_completed_booking($booking_id, $data);

        redirect(base_url() . 'employee/booking/viewcompletedbooking', 'refresh');
    }

    /**
     *  @desc : This function is to select particular appliance for booking.
     *
     * 	Through this we get a form with the appliance details for a appliance which is already registered with us under a particular user.
     *
     *  @param : appliance id
     *  @return : user's and appliance details to view
     */
    function get_appliance_booking_form($id) {
        $sources = $this->booking_model->select_booking_source();
        $details = $this->booking_model->get_appliance_details($id);

        $price_details = $this->booking_model->getPricesForCategoryCapacity($details[0]['service_id'], $details[0]['category'], $details[0]['capacity']);

        $user_details = $this->booking_model->get_user_details($details[0]['user_id']);

        if ($details) {
            $this->load->view('employee/header');
            $this->load->view('employee/appliancebooking', array('sources' => $sources,
                'details' => $details,
                'price_details' => $price_details,
                'user_details' => $user_details));
        } else {
            echo "This Appliance dosn't exists";
        }
    }

    /**
     *  @desc : This function is to get appliance booking confirmation page
     *
     * 	This method will show all the entered details in form for that particular appliance's booking.
     *
     * 	This will help us to re-check the entered details before making it a booking.
     *
     *  @param : appliance id
     *  @return : user and appliance details and load view
     */
    function appliancebookingconf($appliance_id) {
        $booking['user_id'] = $this->input->post('user_id');
        $booking['service_id'] = $this->input->post('service_id');
        $booking['user_email'] = $this->input->post('user_email');
        $booking['city'] = $this->input->post('city');
        $booking['state'] = $this->input->post('state');
        $booking['user_name'] = $this->input->post('name');
        $booking['phone_number'] = $this->input->post('phone_number'); //For pagination to user's detils page
        $booking['appliance_id'] = $appliance_id;
        $booking['appliance_brand'] = $this->input->post('appliance_brand');
        $booking['appliance_category'] = $this->input->post('appliance_category');
        $booking['model_number'] = $this->input->post('model_number');
        $booking['appliance_capacity'] = $this->input->post('appliance_capacity');
        $booking['purchase_year'] = $this->input->post('purchase_year');
        $booking['booking_primary_contact_no'] = $this->input->post('booking_primary_contact_no');
        $booking['booking_alternate_contact_no'] = $this->input->post('booking_alternate_contact_no');
        $booking['appliance_tags'] = $this->input->post('appliance_tags');
        $booking['total_price'] = $this->input->post('total_price');
        $booking['items_selected'] = $this->input->post('items_selected');
        $booking['booking_timeslot'] = $this->input->post('booking_timeslot');
        $booking['booking_address'] = $this->input->post('booking_address');
        $booking['booking_pincode'] = $this->input->post('booking_pincode');
        $booking['booking_remarks'] = $this->input->post('booking_remarks');
        $booking['booking_date'] = $this->input->post('booking_date');
        $booking['booking_date'] = date('d-m-Y', strtotime($booking['booking_date']));
        $yy = date("y", strtotime($booking['booking_date']));
        $mm = date("m", strtotime($booking['booking_date']));
        $dd = date("d", strtotime($booking['booking_date']));
        $booking['booking_id'] = str_pad($booking['user_id'], 4, "0", STR_PAD_LEFT) . $yy . $mm . $dd;
        $booking['booking_id'] .= (intval($this->booking_model->getBookingCountByUser($booking['user_id'])) + 1);
        $booking['amount_due'] = $booking['total_price'];
        $booking['quantity'] = 1;
        $booking['type'] = 'Booking';
        $booking['query_remarks'] = '';
        $booking['current_status'] = 'Pending';
        $booking['internal_status'] = 'Scheduled';
        $booking['create_date'] = date("Y-m-d H:i:s");
        $booking['source'] = $this->input->post('source_code');

        $result = $this->booking_model->service_name($booking['service_id']);

        $this->load->view('employee/header');
        $this->load->view('employee/appliancebookingconf', array('booking' => $booking, 'result' => $result));
    }

    /**
     *  @desc : This function is to enter booking for particular appliance.
     *
     * 	The booking will be inserted for the particular appliance of the particular user with the services selected.
     *
     *  @param : void
     *  @return : loads the pending booking view
     */
    function process_appliance_booking_form() {
        $booking['user_id'] = $this->input->post('user_id');
        $booking['service_id'] = $this->input->post('service_id');
        $booking['service_name'] = $this->input->post('services');
        $booking['user_email'] = $this->input->post('user_email');
        $booking['user_name'] = $this->input->post('user_name');
        $booking['city'] = $this->input->post('city');
        $booking['state'] = $this->input->post('state');
        $booking['phone_number'] = $this->input->post('phone_number');      //For pagination to user's detils page
        $booking['appliance_id'] = $this->input->post('appliance_id');
        $booking['appliance_brand'] = $this->input->post('appliance_brand');
        $booking['appliance_capacity'] = $this->input->post('appliance_capacity');
        $booking['appliance_category'] = $this->input->post('appliance_category');
        $booking['source'] = $this->input->post('source');
	//Find Partner ID for this Source
	$booking['partner_id'] = $this->partner_model->get_partner_id_from_booking_source_code($booking['source']);
	$booking['model_number'] = $this->input->post('model_number');
        $booking['purchase_year'] = $this->input->post('purchase_year');
        $booking['booking_primary_contact_no'] = $this->input->post('booking_primary_contact_no');
        $booking['booking_alternate_contact_no'] = $this->input->post('booking_alternate_contact_no');
        $booking['appliance_tags'] = $this->input->post('appliance_tags');
        $booking['total_price'] = $this->input->post('total_price');
        $booking['items_selected'] = $this->input->post('items_selected');
        $booking['booking_timeslot'] = $this->input->post('booking_timeslot');
        $booking['booking_address'] = $this->input->post('booking_address');
        $booking['booking_pincode'] = $this->input->post('booking_pincode');
        $booking['booking_remarks'] = $this->input->post('booking_remarks');
        $booking['booking_date'] = $this->input->post('booking_date');
        $booking['booking_date'] = date('d-m-Y', strtotime($booking['booking_date']));
        $yy = date("y", strtotime($booking['booking_date']));
        $mm = date("m", strtotime($booking['booking_date']));
        $dd = date("d", strtotime($booking['booking_date']));
        $booking['booking_id'] = str_pad($booking['user_id'], 4, "0", STR_PAD_LEFT) . $yy . $mm . $dd;
        $booking['booking_id'] .= (intval($this->booking_model->getBookingCountByUser($booking['user_id'])) + 1);
        $booking['amount_due'] = $booking['total_price'];
        $booking['quantity'] = 1;
        $booking['type'] = 'Booking';
        $booking['query_remarks'] = '';
        $booking['current_status'] = 'Pending';
        $booking['internal_status'] = 'Scheduled';
        $booking['create_date'] = date("Y-m-d H:i:s");
        $booking['potential_value'] = 0;

        $this->booking_model->addapplianceunitdetails($booking);

	$this->booking_model->addbooking($booking, $booking['appliance_id'], $booking['city'], $booking['state']);

        $months = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
        $mm = $months[$mm - 1];
        $booking['booking_date'] = $dd . $mm;

        if ($booking['booking_timeslot'] == "10AM-1PM") {
            $booking['booking_timeslot'] = "1PM";
        } elseif ($booking['booking_timeslot'] == "1PM-4PM") {
            $booking['booking_timeslot'] = "4PM";
        } elseif ($booking['booking_timeslot'] == "4PM-7PM") {
            $booking['booking_timeslot'] = "7PM";
        }

        //-------Sending Email On Booking--------//
        if ($booking['current_status'] != "FollowUp") {
            $message = "Congratulations You have received new booking from existing appliance, details are mentioned below:
          <br>Customer Name: " . $booking['user_name'] . "<br>Customer Phone Number: " .
                    $booking['booking_primary_contact_no'] . "<br>Customer email address: " .
                    $booking['user_email'] . "<br>Booking Id: " . $booking['booking_id'] . "<br>Service name:" .
                    $booking['service_name'] . "<br>Number of appliance: " . $booking['quantity'] .
                    "<br>Booking Date: " . $booking['booking_date'] . "<br>Booking Timeslot: " .
                    $booking['booking_timeslot'] . "<br>Amount Due: " . $booking['amount_due'] .
                    "<br>Your Booking Remark is: " . $booking['booking_remarks'] . "<br>Booking address: " .
                    $booking['booking_address'] . "<br>Booking pincode: " . $booking['booking_pincode'] .
                    "<br>Booking city: " . $booking['city'] .
                    "<br><br>
            Appliance Details:<br>";

            $appliance = "";
            for ($i = 0; $i < $booking['quantity']; $i++) {

                $appliance = "<br>Brand : " . $booking['appliance_brand'] . "<br>Category : " . $booking['appliance_category'] . "<br>Capacity : " . $booking['appliance_capacity'] .
                        "<br>Selected service/s is/are: " . $booking['items_selected'] . "<br>Total price is: " . $booking['total_price'] . "<br>";
                $message = $message . $appliance;
            }
            $message = $message . "<br> Thanks!!";

            $from = 'booking@247around.com';
            $to = "anuj@247around.com, nits@247around.com";
            $cc = "";
            $bcc = "";
            $subject = 'Booking Confirmation-AROUND';
            $attachment = "";

            $this->notify->sendEmail($from, $to, $cc, $bcc, $subject, $message, $attachment);
            //-------Sending SMS on booking--------//

            if (strstr($booking['booking_id'], "SS") == FALSE) {

                $sms['tag'] = "add_new_booking";
                $sms['smsData']['service'] = $booking['service_name'];
                $sms['smsData']['booking_date'] = $booking['booking_date'];
                $sms['smsData']['booking_timeslot'] = $booking['booking_timeslot'];
                $sms['phone_no'] = $booking['booking_primary_contact_no'];
                $sms['booking_id'] = $booking['booking_id'];

                $this->notify->send_sms($sms);
            }
            //------End of sending SMS--------//
        }

        redirect(base_url() . search_page);
    }

    /**
     *  @desc : This function is to get add new brand page
     *
     * 	Through this we add a new brand for selected service.
     *
     *  @param : void
     *  @return : list of active services present
     */
    function get_add_new_brand_form() {
        $services = $this->booking_model->selectservice();

        $this->load->view('employee/header');
        $this->load->view('employee/addnewbrand', array('services' => $services));
    }

    /**
     *  @desc : This function is to add new brand.
     *
     * 	Enters the new brand to our existing brand list for a particular service
     *
     *  @param : void
     *  @return : add new brand and load view
     */
    function process_add_new_brand_form() {
        $new_brand = $this->input->post('new_brand');
        $brand_name = $this->input->post('brand_name');

        foreach ($new_brand as $service_id => $service) {
            if ($service != "Select") {
                $arr[$service] = $brand_name[$service_id];
            }
        }
        foreach ($arr as $service_id => $brand) {
            $this->booking_model->addNewApplianceBrand($service_id, $brand);
        }

        redirect(base_url() . 'employee/booking/get_add_new_brand_form', 'refresh');
    }

    /**
     *  @desc : This function is to view all pending queries
     *  @param : void
     *  @return : list of all pending queries
     */
    function view_all_pending_queries() {
        //$query = $this->booking_model->view_all_pending_queries();
        $query = $this->booking_model->get_pending_queries(-1, 0, '');

        $data['Bookings'] = $query;

        $this->load->view('employee/header');
        $this->load->view('employee/viewpendingqueries', $data);
    }

    /**
     *  @desc : This function is to view pending queries according to pagination
     *  @param : offset and per page number
     *  @return : list of pending queries according to pagination
     */
    function view_pending_queries($offset = 0, $page = 0, $booking_id = "") {
        if ($page == 0) {
            $page = 50;
        }

        $offset = ($this->uri->segment(4) != '' ? $this->uri->segment(4) : 0);
        $config['base_url'] = base_url() . 'employee/booking/view_pending_queries';
        $config['total_rows'] = $this->booking_model->total_pending_queries($booking_id);

        $config['per_page'] = $page;
        $config['uri_segment'] = 4;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';

        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['Bookings'] = $this->booking_model->get_pending_queries($config['per_page'], $offset, $booking_id);

        $this->load->view('employee/header');
        $this->load->view('employee/viewpendingqueries', $data);
    }

    /**
     *  @desc : This function is to view all cancelled queries
     *  @param : void
     *  @return : list of all cancelled queries
     */
    function view_all_cancelled_queries() {
        $query = $this->booking_model->view_all_cancelled_queries();

        $data['Bookings'] = null;

        if ($query) {
            $data['Bookings'] = $query;
        }
        $this->load->view('employee/header');
        $this->load->view('employee/viewcancelledqueries', $data);
    }

    /**
     *  @desc : This function is to view cancelled queries according to pagination
     *  @param : void
     *  @return : list of cancelled queries according to pagination
     */
    function view_cancelled_queries($offset = 0, $page = 0) {
        if ($page == 0) {
            $page = 50;
        }

        $offset = ($this->uri->segment(4) != '' ? $this->uri->segment(4) : 0);
        $config['base_url'] = base_url() . 'employee/booking/view_cancelled_queries';
        $config['total_rows'] = $this->booking_model->total_cancelled_queries();

        $config['per_page'] = $page;
        $config['uri_segment'] = 4;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';

        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['Bookings'] = $this->booking_model->get_cancelled_queries($config['per_page'], $offset);

        if ($this->session->flashdata('result') != '')
            $data['success'] = $this->session->flashdata('result');

        $this->load->view('employee/header');

        $this->load->view('employee/viewcancelledqueries', $data);
    }

    /**
     *  @desc : This function is to select booking to be edit
     *
     * 	The form will show all the fields as it appears while taking new booking.
     *
     * 	Form will get populated with the booking details entered while adding/taking particular(new) booking.
     *
     *  @param : booking id
     *  @return : user, booking details, appliance id, category and service to view
     */
    function get_edit_booking_form($booking_id) {
        //get booking details
        $query1 = $this->booking_model->getbooking($booking_id);
        //get uit details
        $query2 = $this->booking_model->get_unit_details($booking_id);
        $description = $this->booking_model->getdescription_about_booking($booking_id);

        if ($query1) {
            //get user and other details
            $query3 = $this->booking_model->booking_history_by_booking_id($booking_id);
            //echo print_r($query3, true);
        }
        $service_id = $query1[0]['service_id'];
        //echo print_r($service_id, true);
        $appliance_id = $query1[0]['appliance_id'];
        $all_brands = $this->booking_model->getBrandForService($service_id);
        $all_categories = $this->booking_model->getCategoryForService($service_id);
        //echo print_r($all_categories, true);
        $all_capacities = $this->booking_model->getCapacityForAppliance($service_id);
        //echo print_r($all_capacities, true);
        if (count($query2) > 0) {
            $unit_id = $query2[0]['id'];
            $brand = $query2[0]['appliance_brand'];
            //rearrange brands array so that $brand comes on top
            $brands = array(0 => array("brand_name" => $brand));
            foreach ($all_brands as $value) {
                if ($brand != $value['brand_name']) {
                    array_push($brands, $value);
                }
            }
            $category = $query2[0]['appliance_category'];
            //rearrange categories array so that $category comes on top
            $categories = array(0 => array("category" => $category));
            foreach ($all_categories as $value) {
                if ($category != $value['category']) {
                    array_push($categories, $value);
                }
            }
            $all_capacities = $this->booking_model->getCapacityForCategory($service_id, $category);
            $capacity = $query2[0]['appliance_capacity'];
            //rearrange capacities array so that $capacity comes on top
            $capacities = array(0 => array("capacity" => $capacity));
            foreach ($all_capacities as $value) {
                if ($capacity != $value['capacity']) {
                    array_push($capacities, $value);
                }
            }
        } else {
            $unit_id = '';
            $brands = $all_brands;
            $categories = $all_categories;
            $capacities = $all_capacities;
        }
        $this->load->view('employee/header');
        $this->load->view('employee/editbooking', array(
            'query1' => $query1,
            'unit_details' => $query2[0],
            'query3' => $query3,
            'unit_id' => $unit_id,
            'appliance_id' => $appliance_id,
            'brands' => $brands,
            'description' => $description,
            'categories' => $categories,
            'capacities' => $capacities));
    }

    /**
     *  @desc : This function is to process the edit booking
     *
     * 	With this the edited details for the particular booking will be saved.
     *
     *  The booking can also be converted to query if required.
     *
     *  @param : booking id
     *  @return : confirms as booking/query and load view
     */
    function process_edit_booking_form($booking_id) {
        $booking['user_id'] = $this->input->post('user_id');
        $booking['service_id'] = $this->input->post('service_id');
        $booking['unit_id'] = $this->input->post('unit_id');
        //Appliance details
        $booking['appliance_id'] = $this->input->post('appliance_id');
        $booking['appliance_brand'] = $this->input->post('appliance_brand');
        $booking['appliance_category'] = $this->input->post('appliance_category');
        $booking['appliance_capacity'] = $this->input->post('appliance_capacity');
        $booking['purchase_year'] = $this->input->post('purchase_year');
        $booking['appliance_tag'] = $this->input->post('appliance_tag');
        $booking['model_number'] = $this->input->post('model_number');
        $booking['order_id'] = $this->input->post('order_id');
        $booking['serial_number'] =  $this->input->post('serial_number');
        $booking['partner_source']  = $this->input->post('partner_source');

        $booking['city'] = $this->input->post('booking_city');
        $booking['state'] = $this->input->post('booking_state');

        $booking['booking_alternate_contact_no'] = $this->input->post('booking_alternate_contact_no');
        $booking['booking_primary_contact_no'] = $this->input->post('booking_primary_contact_no');
        $booking['total_price'] = $this->input->post('total_price');
        $booking['items_selected'] = $this->input->post('items_selected');
        $booking['query_remarks'] = $this->input->post('query_remarks');
        $booking['booking_remarks'] = $this->input->post('booking_remarks');
        $booking['booking_date'] = $this->input->post('booking_date');
        $booking['booking_timeslot'] = $this->input->post('booking_timeslot');
        $booking['booking_address'] = $this->input->post('booking_address');
        $booking['booking_pincode'] = $this->input->post('booking_pincode');
        $booking['current_booking_date'] = $this->input->post('current_booking_date');
        $booking['current_booking_timeslot'] = $this->input->post('current_booking_timeslot');
        $booking['new_booking_date'] = $this->input->post('new_booking_date');
        $booking['new_booking_timeslot'] = $this->input->post('new_booking_timeslot');
        $data['update_date'] = date("Y-m-d H:i:s");
        $booking['booking_date'] = date('d-m-Y', strtotime($booking['booking_date']));
        $yy = date("y", strtotime($booking['booking_date']));
        $mm = date("m", strtotime($booking['booking_date']));
        $dd = date("d", strtotime($booking['booking_date']));
        $booking['amount_due'] = $booking['total_price'];
        $booking['quantity'] = 1;
        $booking['type'] = "Booking";
        $booking['booking_id'] = $booking_id;
        //Check to find which button is clicked
        if ($this->input->post('sbm') == "Edit Booking") {  //To edit an existing booking
            $booking['booking_id'] = $booking_id;
            $booking['potential_value'] = $this->input->post('potential_value');
            $booking['current_status'] = "Pending";
            //Update appliance details if required
            $this->booking_model->update_appliance_details($booking);
            //Update unit appliance
            $this->booking_model->update_booking_unit_details($booking_id, $booking);

            unset($booking['serial_number']);

            //Updating booking details
            if ($this->booking_model->update_booking_details($booking_id, $booking)) {
                $query1 = $this->booking_model->booking_history_by_booking_id($booking['booking_id']);
                $months = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
                $mm = $months[$mm - 1];
                $booking['booking_date'] = $dd . $mm;
                if ($booking['booking_timeslot'] == "10AM-1PM") {
                    $booking['booking_timeslot'] = "1PM";
                } elseif ($booking['booking_timeslot'] == "1PM-4PM") {
                    $booking['booking_timeslot'] = "4PM";
                } elseif ($booking['booking_timeslot'] == "4PM-7PM") {
                    $booking['booking_timeslot'] = "7PM";
                }
                //-------Sending Email On Booking--------//
                $message = "Conratulations You have received an edited booking, details are mentioned below:
      <br>Customer Name: " . $query1[0]['name'] . "<br>Customer Phone Number: " . $query1[0]['phone_number'] . "<br>Customer email address: " . $query1[0]['user_email'] . "<br>Booking Id: " . $booking['booking_id'] . "<br>Service name:" . $query1[0]['services'] . "<br>Number of appliance: " . $booking['quantity'] . "<br>Booking Date: " . $booking['booking_date'] . "<br>Booking Timeslot: " . $booking['booking_timeslot'] . "<br>Amount Due: " . $query1[0]['amount_due'] . "<br>Your Booking Remark is: " . $booking['booking_remarks'] . "<br>Booking address: " . $booking['booking_address'] . "<br>Booking pincode: " . $query1[0]['booking_pincode'] . "<br><br>
        Appliance Details:<br>";
                $appliance = "";
                for ($i = 0; $i < $booking['quantity']; $i++) {
                    $appliance = "<br>Brand : " . $booking['appliance_brand'] . "<br>Category : " . $booking['appliance_category'] . "<br>Capacity : " . $booking['appliance_capacity'] .
                            "<br>Selected service/s is/are: " . $booking['items_selected'] . "<br>Total price is: " . $booking['total_price'] . "<br>";
                    $message = $message . $appliance;
                }
                $message = $message . "<br> Thanks!!";

                $from = 'booking@247around.com';
                $to = "anuj@247around.com, nits@247around.com";
                $cc = "";
                $bcc = "";
                $subject = 'Booking Confirmation-AROUND';
                $attachment = "";

                $this->notify->sendEmail($from, $to, $cc, $bcc, $subject, $message, $attachment);
                redirect(base_url() . 'employee/booking/view', 'refresh');
            } else {
                echo "Booking not inserted";
            }
        } elseif ($this->input->post('sbm') == "Convert to Query") { //To convert booking to query
            $booking['current_status'] = "FollowUp";
            $booking['internal_status'] = "FollowUp";
            $booking['potential_value'] = $this->input->post('potential_value');
            //Add "Q-" into booking ID
            $booking['booking_id'] = "Q-" . $booking['booking_id'];
            //Update unit appliance, to update new booking id
            $this->booking_model->update_booking_unit_details($booking_id, $booking);
            if ($this->booking_model->update_booking_details($booking_id, $booking)) {
                $query1 = $this->booking_model->booking_history_by_booking_id($booking['booking_id']);
                $months = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
                $mm = $months[$mm - 1];
                $booking['booking_date'] = $dd . $mm;
                if ($booking['booking_timeslot'] == "10AM-1PM") {
                    $booking['booking_timeslot'] = "1PM";
                } elseif ($booking['booking_timeslot'] == "1PM-4PM") {
                    $booking['booking_timeslot'] = "4PM";
                } elseif ($booking['booking_timeslot'] == "4PM-7PM") {
                    $booking['booking_timeslot'] = "7PM";
                }
                //-------Sending Email On Booking--------//
                $message = "One booking has been converted to query, details are mentioned below:
                  <br>Customer Name: " . $query1[0]['name'] . "<br>Customer Phone Number: " . $query1[0]['phone_number'] . "<br>Customer email address: " . $query1[0]['user_email'] . "<br>Booking Id: " . $booking['booking_id'] . "<br>Service name:" . $query1[0]['services'] . "<br>Number of appliance: " . $query1[0]['quantity'] . "<br>Booking Date: " . $booking['booking_date'] . "<br>Booking Timeslot: " . $booking['booking_timeslot'] . "<br>Amount Due: " . $query1[0]['amount_due'] . "<br>Query Remark is: " . $booking['query_remarks'] . "<br>Booking address: " . $booking['booking_address'] . "<br>Booking pincode: " . $query1[0]['booking_pincode'] . "<br><br>
                Appliance Details:<br>";
                $appliance = "";
                for ($i = 0; $i < $booking['quantity']; $i++) {
                    $appliance = "<br>Brand : " . $booking['appliance_brand'] . "<br>Category : " . $booking['appliance_category'] . "<br>Capacity : " . $booking['appliance_capacity'] .
                            "<br>Selected service/s is/are: " . $booking['items_selected'] . "<br>Total price is: " . $booking['total_price'] . "<br>";
                    $message = $message . $appliance;
                }
                $message = $message . "<br> Thanks!!";
                $from = 'booking@247around.com';
                $to = "anuj@247around.com, nits@247around.com";
                $cc = "";
                $bcc = "";
                $subject = 'Booking Confirmation-AROUND';
                $attachment = "";

                $this->notify->sendEmail($from, $to, $cc, $bcc, $subject, $message, $attachment);
                redirect(base_url() . search_page);
            } else {
                echo "Query is not saved";
            }
        }
    }

    /**
     *  @desc : This function is to get delete booking form
     *  @param : void
     *  @return : takes to view
     */
    function get_delete_booking_form() {
        $this->load->view('employee/header');
        $this->load->view('employee/delete_bookings');
    }

    /**
     *  @desc : This function is to delete the booking
     *  @param : void
     *  @return : takes to view
     */
    function process_delete_booking_form() {
        $booking_id_from_textarea = $this->input->post('booking_id');
        //converting textarea string to array
        $booking_id_array = explode("\n", $booking_id_from_textarea);

        for ($i = 0; $i < count($booking_id_array); $i++) {
            $booking_id = trim($booking_id_array[$i]);
            $getbookingdetails = $this->booking_model->getbooking($booking_id);
            if (empty($getbookingdetails)) {
                echo "This Booking Id does not Exist!";
            } else {
                $appliance_id = $getbookingdetails[0]['appliance_id'];
                $this->booking_model->delete_booking_details($booking_id);
                $this->booking_model->delete_unit_booking_details($booking_id);
                $this->booking_model->delete_appliance_details($appliance_id);
            }
        }
        redirect(base_url() . search_page);
    }

    /**
     *  @desc : This function is used to open a cancelled query
     *  @param : String (Booking Id)
     *  @return : redirect user controller
     */
    function open_cancelled_query($booking_id) {
	$this->booking_model->change_booking_status($booking_id);

	redirect(base_url() . 'employee/booking/view_pending_queries/0/0/' . $booking_id, 'refresh');
    }

    /**
     *  @desc : This function is used to get state by city
     *
     * 	Takes city as input and then gives its state
     *
     *  @param : void
     *  @return : state
     */
    function get_state_by_city() {
        $city = $this->input->post('city');
        $state = $this->booking_model->selectSate($city);
        print_r($state);
    }

    /**
     *  @desc : This function is used to call customer from admin panel
     *  @param : Phone Number
     *  @return : none
     */
    function call_customer($cust_phone) {
//        log_message('info', __FUNCTION__);

	$s1 = $_SERVER['HTTP_REFERER'];
        //$s2 = "https://www.aroundhomzapp.com/";
	$s2 = base_url();
	$redirect_url = substr($s1, strlen($s2));

        $this->checkUserSession();

        //Get customer id
        $cust_id = '';
        $user = $this->user_model->search_user($cust_phone);
        if ($user) {
            $cust_id = $user[0]['user_id'];
        }

        //Find agent phone from session
        $agent_id = $this->session->userdata('id');
        $agent_phone = $this->session->userdata('phone');

        //Save call log
        $this->booking_model->insert_outbound_call_log(array(
            'agent_id' => $agent_id, 'customer_id' => $cust_id,
            'customer_phone' => $cust_phone
        ));

        //Make call to customer now
        $this->notify->make_outbound_call($agent_phone, $cust_phone);

        //Redirect to the page from where you landed in this function, do not refresh
	redirect(base_url() . $redirect_url);
    }

    /**
     *  @desc : Callback fn called after agent finishes customer call
     *  @param : Phone Number
     *  @return : none
     */
    function call_customer_status_callback() {
        log_message('info', "Entering: " . __METHOD__);

        //http://support.exotel.in/support/solutions/articles/48259-outbound-call-to-connect-an-agent-to-a-customer-
        $callDetails['call_sid'] = (isset($_GET['CallSid'])) ? $_GET['CallSid'] : null;
        $callDetails['status'] = (isset($_GET['Status'])) ? $_GET['Status'] : null;
        $callDetails['recording_url'] = (isset($_GET['RecordingUrl'])) ? $_GET['RecordingUrl'] : null;
        $callDetails['date_updated'] = (isset($_GET['DateUpdated'])) ? $_GET['DateUpdated'] : null;

        log_message('info', print_r($callDetails, true));
//	//insert in database
//	$this->apis->insertPassthruCall($callDetails);
    }

    /**
     * @desc :This funtion will check user session for an eemplouee.
     */
    function checkUserSession() {
        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee')) {
            return TRUE;
        } else {
            $this->session->sess_destroy();
            redirect(base_url() . "employee/login");
        }
    }

}
