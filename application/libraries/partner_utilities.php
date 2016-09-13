<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;

require_once BASEPATH . 'libraries/spout-2.4.3/src/Spout/Autoloader/autoload.php';

/**
 * Partner Utilities library
 *
 * @author anujaggarwal
 */
class partner_utilities {

    function __Construct() {
	$this->My_CI = & get_instance();

	$this->My_CI->load->model('partner_model');
    }

    /**
     *  @desc  : Validate Excel file
     *  @param : void
     *  @return : void
     */
    function validate_file($file) {
	if (!empty($file['file']['name'])) {
	    $pathinfo = pathinfo($file["file"]["name"]);
	    if (($pathinfo['extension'] == 'xlsx' || $pathinfo['extension'] == 'xls') && $file['file']['size'] > 0) {
		return true;
	    } else {
		$data['error'] = "Please Select Valid Excel File";
		return $data;
	    }
	} else {
	    $data['error'] = "Please Select Excel File";
	    return $data;
	}
    }

     /**
     * @desc: This method used to insert data into partner leads table.
     * @param: Array Booking details
     * @param: Array Unit details
     * @param: Array User details
     * @param: String Service Name
     */
    function insert_booking_in_partner_leads($booking, $unit_details, $user_details, $product){
    	$partner_booking['PartnerID'] = $booking['partner_id'];
    	$partner_booking['OrderID'] = $booking['order_id'];
    	$partner_booking['247aroundBookingID'] = $booking['booking_id'];
    	$partner_booking['Product'] = $product;
    	$partner_booking['Brand'] = $unit_details['appliance_brand'];
    	$partner_booking['Model'] = $unit_details['model_number'];
    	$partner_booking['ProductType'] = $unit_details['appliance_description'];
    	$partner_booking['Category'] = $unit_details['appliance_category'];
    	$partner_booking['Name'] = $user_details['name'];
    	$partner_booking['Mobile'] =  $booking['booking_primary_contact_no'];
    	$partner_booking['AlternatePhone'] =  $booking['booking_alternate_contact_no'];
    	$partner_booking['Email'] = $user_details['user_email'];
    	$partner_booking['Landmark'] = $booking['booking_landmark'];
    	$partner_booking['Address'] = $booking['booking_address'];
    	$partner_booking['Pincode'] = $booking['booking_pincode'];
    	$partner_booking['City'] = $booking['city'];
    	$partner_booking['DeliveryDate'] = $booking['delivery_date'];
    	$partner_booking['RequestType'] = $booking['request_type'];
    	$partner_booking['ScheduledAppointmentDate'] = $booking['booking_date'];
    	$partner_booking['ScheduledAppointmentTime'] = $booking['booking_timeslot'];
    	$partner_booking['Remarks'] = $booking['booking_remarks'];
    	$partner_booking['PartnerRequestStatus'] = "";
    	$partner_booking['247aroundBookingStatus'] = "FollowUp";
    	$partner_booking['247aroundBookingRemarks'] = "FollowUp";
    	$partner_booking['create_date'] = date('Y-m-d H:i:s');

    	$partner_leads_id = $this->My_CI->partner_model->insert_partner_lead($partner_booking);
    	if($partner_leads_id){
    		return true;
    	} else {

    	}

    }

}
