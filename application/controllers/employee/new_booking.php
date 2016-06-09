<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

define('Partner_Integ_Complete', TRUE);

class New_booking extends CI_Controller {

    /**
     * load list modal and helpers
     */
    function __Construct() {
	parent::__Construct();
	$this->load->model('booking_model');
    $this->load->model('new_booking_model');
	$this->load->model('user_model');
    $this->load->model('vendor_model');
    $this->load->model('invoices_model');
    $this->load->model('partner_model');
    $this->load->library('partner_sd_cb');
    $this->load->library('notify');
	$this->load->helper(array('form', 'url'));

	$this->load->library('form_validation');
    $this->load->library('asynchronous_lib');

	if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee') && ($this->session->userdata('add service') == '1')) {
	    return TRUE;
	} else {
	    redirect(base_url() . "employee/login");
	}
    }

    /**
     *  @desc : This function is used to insert booking in booking details
     *  @param : user id
     *  @return : void
     */
    public function index($user_id) {

        $booking = $this->getAllBookingInput($user_id);

        $service = $booking['services'];
        $message = $booking['message'];
        unset($booking['message']); // unset message body from booking deatils array
        unset($booking['services']); // unset service name from booking details array

        $this->new_booking_model->addbooking($booking);

        if ($booking['type'] == 'Booking') {

            //$to = "anuj@247around.com, nits@247around.com";
            $to = "abhaya@247around.com";
            $from = "booking@247around.com";
            $cc = "";
            $bcc = "";
            $subject = 'Booking Confirmation-AROUND';
            //$this->notify->sendEmail($from, $to, $cc, $bcc, $subject, $message, "");
            //-------Sending SMS on booking--------//

            $smsBody = "Got it! Request for " . trim($service) . " Repair is confirmed for " .
                    $booking['booking_date'] . ", " . $booking['booking_timeslot'] .
                    ". 247Around Indias 1st Multibrand Appliance repair App goo.gl/m0iAcS. 011-39595200";

            //$this->notify->sendTransactionalSms($booking['booking_primary_contact_no'], $smsBody);
        }

        redirect(base_url() . 'employee/booking/view');
    }

    /**
     * @desc : This function is used to get city, source, serices and load add booking page
     * @param: String(Phone Number)
     * @return : void
     */
    function addbooking($phone_number) {
        $data = $this->new_booking_model->get_city_booking_source_services($phone_number);

        $this->load->view('employee/header');
        $this->load->view('employee/addbookingmodel');
        $this->load->view('employee/addbooking', $data);
    }

    /**
     *  @desc : This function is used to insert data in booking unit details and appliance details table
     *  @param : user id
     *  @return : Array(booking details)
     */
    function getAllBookingInput($user_id) {
        $user['user_id'] = $booking['user_id'] = $user_id;
        $booking['service_id'] = $this->input->post('service_id');
        $booking['source'] = $this->input->post('source_code');
        $user_name = $this->input->post('user_name');
        $booking['type'] = $this->input->post('type');
        $booking['amount_due'] = $this->input->post('grand_total_price');
        $booking['booking_address'] = $this->input->post('home_address');
        $booking['city'] = $this->input->post('city');
        $booking['booking_id'] = $this->create_booking_id($user_id, $booking['source'],  $booking['type']);
        // select state by city
        $state = $this->vendor_model->selectSate($booking['city']);
        $booking['state'] = $state[0]['state'];
        $booking['booking_pincode'] = $this->input->post('booking_pincode');
        $service = $booking['services'] = $this->input->post('service');
        $booking['booking_primary_contact_no'] = $this->input->post('booking_primary_contact_no');
        $booking['potential_value'] = $this->input->post('potential_value');
        $booking['booking_alternate_contact_no'] = $this->input->post('booking_alternate_contact_no');
        $booking['booking_date'] = date('d-m-Y', strtotime($this->input->post('booking_date')));

        $booking_timeslot = $this->input->post('booking_timeslot');
        $booking_remarks = $this->input->post('query_remarks');
        $booking_timeslot = explode("-", $booking_timeslot);
        $booking['booking_timeslot'] = $booking_timeslot[1];


        $appliance_brand = $this->input->post('appliance_brand');
        $appliance_category = $this->input->post('appliance_category');
        $appliance_capacity = $this->input->post('appliance_capacity');
        $model_number = $this->input->post('model_number');
        $appliance_tags = $this->input->post('appliance_tags');
        $purchase_year = $this->input->post('purchase_year');
        $months = $this->input->post('purchase_month');
        $booking['quantity'] = count($appliance_brand);

        $partner_id = $this->partner_model->get_all_partner_source("", $booking['source']);
        // this case for partner
        if(!empty($partner_id)){
            $booking['partner_id'] = $partner_id[0]['partner_id'];
        }
        

        $discount = $this->input->post('discount');
        $pricesWithId = $this->input->post("prices");
        $user['user_email'] = $this->input->post('user_email');

        $message = "";

        if ($booking['type'] == 'Booking') {

            $booking['current_status'] = 'Pending';
            $booking['internal_status'] = 'Scheduled';
            $booking['booking_remarks'] = $booking_remarks;

            $message .= "Congratulations You have received new booking, details are mentioned below:
      <br>Customer Name: " . $user_name . "<br>Customer Phone Number: " . $booking['booking_primary_contact_no'] .
                    "<br>Customer email address: " . $user['user_email'] . "<br>Booking Id: " .
                    $booking['booking_id'] . "<br>Service name:" . $service .
                    "<br>Number of appliance: " . count($appliance_brand) . "<br>Booking Date: " .
                    $booking['booking_date'] . "<br>Booking Timeslot: " . $booking['booking_timeslot'] .
                    "<br>Amount Due: " . $booking['amount_due'] . "<br>Your Booking Remark is: " .
                    $booking['booking_remarks'] . "<br> Booking address: " . $booking['booking_address'] .
                    " " . $booking['city'] . ", " . $booking['state'] . ", " .
                    "<br>Booking pincode: " . $booking['booking_pincode'] . "<br><br>
        Appliance Details:<br>";
        } else if ($booking['type'] == 'Query') {

            $booking['current_status'] = "FollowUp";
            $booking['query_remarks'] = $booking_remarks;
        }


        foreach ($appliance_brand as $key => $value) {
            
            $services_details = "";
            $services_details['appliance_brand'] = $value;
            $services_details['appliance_category'] = $appliance_category[$key];
            $services_details['appliance_capacity'] = $appliance_capacity[$key];
            $services_details['model_number'] = $model_number[$key];
            $services_details['appliance_tag'] = $appliance_tags[$key];
            $services_details['purchase_year'] = $purchase_year[$key];
            $services_details['booking_id'] = $booking['booking_id'];
            $services_details['purchase_month'] = $months[$key];
            $services_details['service_id'] = $booking['service_id'];
            if(!empty($partner_id)){
                
               $services_details['partner_id'] = $booking['partner_id'];
            }
            
            $services_details['appliance_id'] = $this->new_booking_model->addappliance($services_details, $user_id);

            log_message ('info', __METHOD__ . "Appliance details data". print_r($services_details));

            foreach ($pricesWithId[$value] as $keys => $values) {
                $prices = explode("_", $values);  // split string 
                $services_details['id'] = $prices[0]; // This is id of service_centre_charges table. 
                $services_details['around_net_payable'] = $discount[$value][$services_details['id']][0];  // discount for appliances
                $services_details['around_paid_basic_charges'] = $services_details['around_net_payable'];
                $result = $this->new_booking_model->insert_data_in_booking_unit_details($services_details);

                if ($booking['current_status'] != 'FollowUp') {
                    $message .= "<br>Brand : " . $result['appliance_brand'] . "<br>Category : " .
                            $result['appliance_category'] . "<br>Capacity : " . $result['appliance_capacity'] .
                            "<br>Selected service is: " . $result['price_tags'] . "<br>Total price is: " .
                            $result['customer_net_payable'] . "<br>";

                    $message .= "<br/>";
                }
            }
        }

        if ($booking['type'] == 'Query') {

            $booking['message'] .= "";
        } else {

            $booking['message'] = $message;
        }
        $this->user_model->edit_user($user);

        return $booking;
    }

    function create_booking_id($user_id, $source, $type) {
        $booking['booking_id'] = '';
        $digits = 4;
        $mm = date("m");
        $dd = date("d");
        $booking['booking_id'] = str_pad($user_id, 4, "0", STR_PAD_LEFT) . $dd . $mm;
        // Add 4 digits random number in booking id.
        $booking['booking_id'] .= rand(pow(10, $digits - 1) - 1, pow(10, $digits) - 1);

        //Add source
        $booking['source'] = $source;
        if($type == "Booking"){
            $booking['booking_id'] = $booking['source'] . "-" . $booking['booking_id'];
        } else {
            $booking['booking_id'] = "Q-" . $booking['source'] . "-" . $booking['booking_id'];
        }
        
        return $booking['booking_id'];
    }

    /**
     *  @desc : This function displays list of bookings
     *  @param : void
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
     * @desc: This is function is used to complete booking and update service center action in service_center_action table
     * @param : void
     * @return; void
     */

    function complete_review_booking(){
    	$booking_id = $this->input->post('booking_id');
    	$data['service_charge'] = $this->input->post('service_charge');
    	$data['additional_service_charge'] = $this->input->post('additional_charge');
    	$data['parts_cost'] = $this->input->post('parts_cost');
    	$data['amount_paid'] = $this->input->post('amount_paid');
    	$data['current_status'] = "Completed";
    	$data['closed_date'] = date("Y-m-d h:i:s");
        $data['internal_status'] = $this->input->post('internal_status');
        $admin_remarks =  $this->input->post('admin_remarks');

        $service_charges = $this->booking_model->getbooking_charges($booking_id);
        $data['closing_remarks'] = "Service Center Remarks:- ".$service_charges[0]['service_center_remarks']. " <br/> Admin:-  ".  date("F j") .":- ".$admin_remarks."<br/>".$service_charges[0]['admin_remarks'];
        // rate function to be use for update booking_details table
    	$this->booking_model->rate($booking_id, $data);
    	$data['booking_id'] = $booking_id;
        
    	$this->vendor_model->update_service_center_action($data);
        
        
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
            $sd_where = array("CRM_Remarks_SR_No" => $data[0]['booking_id']);
            $sd_data = array(
                "Status_by_247around" => "Completed",
                "Remarks_by_247around" => $data['internal_status'],
                "Rating_Stars" => "",
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

        $query1 = $this->booking_model->booking_history_by_booking_id($booking_id);

        log_message('info', 'Booking Status Change- Booking id: ' . $booking_id . " Completed By " . $this->session->userdata('employee_id'));

        $message = "Booking Completion.<br>Customer name: " . $query1[0]['name'] . "<br>Customer phone number: " . $query1[0]['phone_number'] . "<br>Customer email: " . $query1[0]['user_email'] . "<br>Booking Id is: " . $query1[0]['booking_id'] . "<br>Your service name is:" . $query1[0]['services'] . "<br>Booking date: " . $query1[0]['booking_date'] . "<br>Booking completion date: " . $data['closed_date'] . "<br>Amount paid for the booking: " . $data['amount_paid'] . "<br>Your booking completion remark is: " . $data['closing_remarks'] . "<br> Thanks!!";

        $to = "anuj@247around.com, nits@247around.com";
        
        $subject = 'Booking Completion-AROUND';
        $cc = "";
        $bcc = "";
        $this->notify->sendEmail("booking@247around.com", $to, $cc, $bcc, $subject, $message, "");
        
        //------End of sending email--------//
        //------Send SMS on Completion of booking-----//
        if ($is_sd == FALSE) {
            $smsBody = "Your request for " . $query1[0]['services'] . " Repair completed. Like us on Facebook goo.gl/Y4L6Hj For discounts download app goo.gl/m0iAcS. For feedback call 011-39595200.";
            $this->notify->sendTransactionalSms($query1[0]['phone_number'], $smsBody);
        }
        
    	print_r('success');
    }
    
    /**
     * @desc: save Admin remarks in service center action table
     * @param: void
     * @return: void
     */
    function admin_remarks(){
        $data['booking_id'] = $this->input->post('booking_id');
        $admin_remarks = $this->input->post('admin_remarks');

        $charges = $this->booking_model->getbooking_charges($data['booking_id']);

        if(empty($charges[0]['admin_remarks'])){
            $data['admin_remarks'] = date("F j")."  :-".$admin_remarks;
            $this->vendor_model->update_service_center_action($data);
            echo "success";
        } else {
            // remove previous text, added in admin_remarks column.
            $string = str_replace($charges[0]['admin_remarks']," ", $admin_remarks);
            // Add current and previous text in admin_remarks column
            $data['admin_remarks'] = $charges[0]['admin_remarks']." <br/> ".date("F j").":- ". $string;
            $this->vendor_model->update_service_center_action($data);
            echo "success";
        }
    }
    
    /**
     * @desc: this funtion is used to complete reviewed booking (All selected checkbox)
     * It completes Asynchronous process
     */
    function complete_booking(){
        $approve['approve'] = $this->input->post('approve');
        
        $url = base_url() . "employee/do_background_process/complete_booking";

        $this->asynchronous_lib->do_background_process($url, $approve);

       
        redirect(base_url() . 'employee/new_booking/review_bookings');
    }

    function review_bookings($booking_id = ""){
        $charges['charges'] = $this->booking_model->get_booking_for_review($booking_id);
        $this->load->view('employee/header');
        $this->load->view('employee/review_booking', $charges);
    } 
    
    /**
     * @desc: This is used to complete booking by admin
     * @param: String Array
     * @return :void
     */
    function process_complete_booking($booking_id){
       $customer_basic_charge = $this->input->post('customer_basic_charge');
       $additional_charge =  $this->input->post('additional_charge');
       $parts_cost =  $this->input->post('parts_cost');
       $internal_status = $this->input->post('internal_status');
       
       foreach ($customer_basic_charge as $unit_id => $value) {
        // variable $unit_id  is existing id in booking unit details table of given booking id 
        $data = array();
        $data['id'] = $unit_id;
        $data['customer_paid_basic_charges'] = $value;
        $data['customer_paid_extra_charges'] = $additional_charge[$unit_id];
        $data['customer_paid_parts'] = $parts_cost[$unit_id];
        $data['internal_status'] = $internal_status;
        // update price in the booking unit details page
        $this->new_booking_model->update_unit_details($data);
          
       }

       $booking['rating_stars'] = $this->input->post('rating_stars');
       $booking['vendor_rating_stars'] = $this->input->post('vendor_rating_stars');
       $booking['vendor_rating_comments'] =  $this->input->post('vendor_rating_comments');
       $booking['rating_comments'] = $this->input->post('rating_comments');
       $booking['closed_date'] = date('Y-m-d h:i:s');
       $booking['current_status'] = "Completed";
       $booking['internal_status'] = $internal_status;
       // rate function is used to update booking details table
       $this->booking_model->rate($booking_id, $booking);
       redirect(base_url() . 'employee/booking/view');
    }
}