<?php

class Miscelleneous {

    public function __construct() {
	$this->My_CI = & get_instance();
        $this->My_CI->load->helper(array('form', 'url'));
	$this->My_CI->load->library('email');
        $this->My_CI->load->library('partner_cb');
        $this->My_CI->load->library('initialized_variable');
        $this->My_CI->load->library('asynchronous_lib');
        $this->My_CI->load->library('booking_utilities');
        $this->My_CI->load->library('notify');
	$this->My_CI->load->model('vendor_model');
	$this->My_CI->load->model('booking_model');
        $this->My_CI->load->model('upcountry_model');
       
        $this->My_CI->load->model('service_centers_model');
    }
    /**
     * @desc This method is used to check upcountry avaliablity on the basis of booking pincode, service id.
     * @param String $booking_city
     * @param String $booking_pincode
     * @param String $service_id
     * @return Array
     */
    function check_upcountry_vendor_availability($booking_city, $booking_pincode, $service_id, $partner_data, $assigned_vendor_id= false){
         log_message('info', __FUNCTION__ . ' => booking city' . $booking_city." booking pincode ". $booking_pincode
                 ." service id ".$service_id );
        //Get Available Vendor in this pincode who work this service
        $check_vendor = $this->My_CI->upcountry_model->get_vendor_upcountry($booking_pincode, $service_id, $assigned_vendor_id);
        $sf_city = $this->My_CI->vendor_model->get_distict_details_from_india_pincode($booking_pincode)['district'];
        $data1 = array();
        $is_return = 0;
        $mesg1 = array();
        
        // if $check_vendor is empty then return because we are are providing service in this pincode
        if(!empty($check_vendor)){
            // If count is one, means only one vebdor is avaliable in this pincode
            if(count($check_vendor) ==1){
                if($check_vendor[0]['is_upcountry'] ==1){
                    $data['vendor_id'] = $check_vendor[0]['Vendor_ID'];
                    $data['city'] = $sf_city;
                    array_push($data1, $data);
                } else {
                    $msg['vendor_id'] = $check_vendor[0]['Vendor_ID'];
                    $msg['message'] = NOT_UPCOUNTRY_BOOKING;
                    $msg['upcountry_distance'] = 0;
                   
                    return $msg;
                }
            } else if(count($check_vendor > 1)){
                foreach($check_vendor as $vendor){
                    if($vendor['is_upcountry'] ==1){
                        $data['vendor_id'] = $vendor['Vendor_ID'];
                        $data['city'] = $sf_city;
                        
                        array_push($data1, $data);
                    } else {
                        $msg['vendor_id'] = $vendor['Vendor_ID'];
                        $msg['message'] = NOT_UPCOUNTRY_BOOKING;
                        $msg['upcountry_distance'] = 0;
                        $is_return = 1;
                        array_push($mesg1, $msg);
                    }
                }
                
                if($is_return ==1){
                   
                    if(count($mesg1) > 1){
                        $multiple_vendor['message'] = SF_DOES_NOT_EXIST;
                        return $multiple_vendor;
                        
                    } else {
                        
                        return $mesg1[0];
                    }
                }
            }
           
            return $this->My_CI->upcountry_model->action_upcountry_booking($booking_city,
                    $booking_pincode, $data1, $partner_data);
           
            
        
            
        } else {
            $to = RM_EMAIL.", ". SF_NOT_EXISTING_IN_PINCODE_MAPPING_FILE_TO;
            $cc = SF_NOT_EXISTING_IN_PINCODE_MAPPING_FILE_CC;
            
            $subject = "SF Does Not Exist In Pincode: ".$booking_pincode;
            $message = "Booking City: ". $booking_city." /n  Booking Pincode: ".$booking_pincode; 
            $this->My_CI->notify->sendEmail("booking@247around.com", $to, $cc, "", $subject, $message, "");
            $msg['message'] = SF_DOES_NOT_EXIST;
            
            return $msg;
        }
        
    }
    /**
     * @desc This method is used to assign service center to booking
     * @param String $service_center_id
     * @param String $booking_id
     * @return boolean
     */
    function assign_vendor_process($service_center_id, $booking_id){
        log_message('info', __FUNCTION__ . " Entering...... booking_id " . $booking_id . " service center id " . $service_center_id);
        $b['assigned_vendor_id'] = $service_center_id;
        // Set Default Engineer 
        if (IS_DEFAULT_ENGINEER == TRUE) {
            $b['assigned_engineer_id'] = DEFAULT_ENGINEER;
        } else {
            $engineer = $this->My_CI->vendor_model->get_engineers($service_center_id);
            if (!empty($engineer)) {
                $b['assigned_engineer_id'] = $engineer[0]['id'];
            }
        }
        $b['upcountry_partner_approved'] = '1';
        //Assign service centre and engineer
        $assigned = $this->My_CI->vendor_model->assign_service_center_for_booking($booking_id, $b);
        if ($assigned) {
            log_message('info', __FUNCTION__ . " Assigned...... booking_id " . $booking_id);
            // Data to be insert in service center
            $sc_data['current_status'] = "Pending";
            $sc_data['update_date'] = date('Y-m-d H:i:s');
            $sc_data['internal_status'] = "Pending";
            $sc_data['service_center_id'] = $service_center_id;
            $sc_data['booking_id'] = $booking_id;

            // Unit Details Data
            $where = array('booking_id' => $booking_id);
            $unit_details = $this->My_CI->booking_model->get_unit_details($where);
            foreach ($unit_details as $value) {
                $sc_data['unit_details_id'] = $value['id'];
                $sc_id = $this->My_CI->vendor_model->insert_service_center_action($sc_data);

                if (!$sc_id) {
                    log_message('info', __METHOD__ . "=> Data is not inserted into service center "
                            . "action table booking_id: " . $booking_id . ", data: " . print_r($sc_data, true));
                }
            }
            
            log_message('info', __FUNCTION__ . " Exit...... booking_id " . $booking_id . " service center id " . $service_center_id);
            return true;
        } else {
            log_message('info', __METHOD__ . "=> Not Assign for Sc "
                    . $service_center_id);
            return false;
        }
    }
    
    function assign_upcountry_booking($booking_id, $agent_id, $agent_name) {
        log_message('info', __METHOD__ . " => Entering " . $booking_id);
        $query1 = $this->My_CI->booking_model->getbooking_history($booking_id, "1");
        $vendor_data = array();
        if (!empty($query1[0]['assigned_vendor_id'])) {
            $vendor_data[0]['vendor_id'] = $query1[0]['assigned_vendor_id'];

            if (!empty($query1[0]['district'])) {
                $vendor_data[0]['city'] = $query1[0]['district'];
            } else {
                $vendor_data[0]['city'] = $this->My_CI->vendor_model->get_distict_details_from_india_pincode($query1[0]['booking_pincode'])['district'];
            }

            $return_status = 0;
            $partner_details = $this->My_CI->partner_model->get_all_partner($query1[0]['partner_id']);
            $data = $this->My_CI->upcountry_model->action_upcountry_booking($query1[0]['city'], $query1[0]['booking_pincode'], $vendor_data, $partner_details);

            switch ($data['message']) {
                case UPCOUNTRY_BOOKING:
                case UPCOUNTRY_LIMIT_EXCEED:
                    log_message('info', __METHOD__ . " => " . $data['message'] . " booking_id " . $booking_id);

                    $booking['is_upcountry'] = 1;
                    $booking['upcountry_pincode'] = $data['upcountry_pincode'];
                    $booking['sub_vendor_id'] = $data['sub_vendor_id'];
                    $booking['upcountry_distance'] = $data['upcountry_distance'];
                    $booking['sf_upcountry_rate'] = $data['sf_upcountry_rate'];
                    $booking['partner_upcountry_rate'] = $data['partner_upcountry_rate'];
                    $is_upcountry = $this->My_CI->upcountry_model->is_upcountry_booking($booking_id);
                    if (!empty($is_upcountry)) {

                        if ($data['message'] !== UPCOUNTRY_LIMIT_EXCEED) {

                            log_message('info', __METHOD__ . " => Upcountry Booking Free Booking " . $booking_id);
                            $booking['upcountry_paid_by_customer'] = 0;
                            $this->My_CI->booking_model->update_booking($booking_id, $booking);
                            $return_status = TRUE;
                        } else if ($data['partner_upcountry_approval'] == 1 && $data['message'] == UPCOUNTRY_LIMIT_EXCEED) {

                            log_message('info', __METHOD__ . " => Upcountry Waiting for Approval " . $booking_id);
                            $booking['assigned_vendor_id'] = NULL;
                            $booking['internal_status'] = UPCOUNTRY_BOOKING_NEED_TO_APPROVAL;
                            $booking['upcountry_partner_approved'] = '0';
                            $booking['upcountry_paid_by_customer'] = 0;

                            $this->My_CI->booking_model->update_booking($booking_id, $booking);
                            $this->My_CI->service_centers_model->delete_booking_id($booking_id);

                            $this->My_CI->notify->insert_state_change($booking_id, "Waiting Partner Approval", _247AROUND_PENDING, "Waiting Upcountry to Approval", $agent_id, $agent_name, _247AROUND);
                            $unit_details = $this->My_CI->booking_model->get_unit_details(array('booking_id' => $booking_id));

                            $up_mail_data['name'] = $query1[0]['name'];
                            $up_mail_data['appliance'] = $query1[0]['services'];
                            $up_mail_data['booking_address'] = $query1[0]['booking_address'];
                            $up_mail_data['city'] = $query1[0]['city'];
                            $up_mail_data['state'] = $query1[0]['state'];
                            $up_mail_data['booking_pincode'] = $query1[0]['booking_pincode'];
                            $up_mail_data['booking_id'] = $query1[0]['booking_id'];
                            $up_mail_data['booking_primary_contact_no'] = $query1[0]['booking_primary_contact_no'];
                            $up_mail_data['price_tags'] = $unit_details[0]['price_tags'];
                            $up_mail_data['appliance_brand'] = $unit_details[0]['appliance_brand'];
                            $up_mail_data['appliance_category'] = $unit_details[0]['appliance_category'];
                            $up_mail_data['appliance_capacity'] = $unit_details[0]['appliance_capacity'];
                            $up_mail_data['upcountry_distance'] = $booking['upcountry_distance'];
                            $up_mail_data['partner_upcountry_rate'] = $booking['partner_upcountry_rate'];

                            $message1 = $this->My_CI->load->view('employee/upcountry_approval_template', $up_mail_data, true);


                            if ($booking['upcountry_distance'] > 300) {
                                $subject = "Upcountry Distance More Than 300 - Booking ID " . $query1[0]['booking_id'];
                                $to = NITS_ANUJ_EMAIL_ID;
                                $cc = "abhaya@247around.com";
                            } else {
                                $subject = "Upcountry Charges Approval Required - Booking ID " . $query1[0]['booking_id'];
                                $to = $partner_details[0]['upcountry_approval_email'];
                                $cc = NITS_ANUJ_EMAIL_ID;
                            }

                            $this->My_CI->notify->sendEmail("booking@247around.com", $to, $cc, "", $subject, $message1, "");

                            $return_status = FALSE;
                        } else if ($data['partner_upcountry_approval'] == 0 && $data['message'] == UPCOUNTRY_LIMIT_EXCEED) {

                            log_message('info', __METHOD__ . " => Upcountry, partner does not provide approval" . $booking_id);
                            $this->My_CI->booking_model->update_booking($booking_id, $booking);
                            $this->process_cancel_form($booking_id, "Pending", UPCOUNTRY_CHARGES_NOT_APPROVED, " Upcountry  Distance " . $data['upcountry_distance'], $agent_id, $agent_name, $query1[0]['partner_id']);

                            $to = NITS_ANUJ_EMAIL_ID;
                            $cc = "abhaya@247around.com";
                            $message1 = $booking_id . " has auto cancelled because upcountry limit exceed "
                                    . "and partner does not provide upcountry charges approval. Upcountry Distance " . $data['upcountry_distance'];
                            $this->My_CI->notify->sendEmail("booking@247around.com", $to, $cc, "", 'Upcountry Auto Cancel Booking', $message1, "");

                            $return_status = FALSE;
                        }
                    } else {

                        log_message('info', __METHOD__ . " => Partner does not provide Upcountry charges " . $booking_id);
                        $booking['upcountry_paid_by_customer'] = 1;
                        $booking['partner_upcountry_rate'] = DEFAULT_UPCOUNTRY_RATE;
                        if ($query1[0]['is_upcountry'] == 0) {
                            log_message('info', __METHOD__ . " => Amount due added " . $booking_id);
                            $booking['amount_due'] = $query1[0]['amount_due'] + ($booking['partner_upcountry_rate'] * $booking['upcountry_distance']);
                        }

                        $this->My_CI->booking_model->update_booking($booking_id, $booking);
                        $return_status = TRUE;
                    }

                    break;

                case NOT_UPCOUNTRY_BOOKING:
                    $booking['is_upcountry'] = 0;
                    $booking['upcountry_pincode'] = NULL;
                    $booking['sub_vendor_id'] = NULL;
                    $booking['upcountry_distance'] = NULL;
                    $booking['sf_upcountry_rate'] = NULL;
                    $booking['partner_upcountry_rate'] = NULL;
                    $booking['upcountry_paid_by_customer'] = '0';
                    $booking['upcountry_partner_approved'] = '1';

                    $this->My_CI->booking_model->update_booking($booking_id, $booking);
                    log_message('info', __METHOD__ . " => Not Upcountry Booking" . $booking_id);
                    $return_status = TRUE;
                    break;
                case UPCOUNTRY_DISTANCE_CAN_NOT_CALCULATE:

                    log_message('info', __METHOD__ . " => Upcountry distance cannot calculate" . $booking_id);
                    // Assigned Vendor Id is Not NULL or sub vendor id is NULl
                    $booking['is_upcountry'] = 0;
                    $booking['upcountry_pincode'] = $data['upcountry_pincode'];
                    $booking['sub_vendor_id'] = $data['sub_vendor_id'];
                    $booking['sf_upcountry_rate'] = $data['sf_upcountry_rate'];

                    $this->My_CI->booking_model->update_booking($booking_id, $booking);

                    $to = NITS_ANUJ_EMAIL_ID . ", sales@247around.com";
                    $cc = "abhaya@247around.com";
                    $message1 = "Upcountry did not calculate for " . $booking_id;
                    $this->My_CI->notify->sendEmail("booking@247around.com", $to, $cc, "", 'Upcountry Failed', $message1, "");

                    $return_status = TRUE;
                    break;
            }

            if ($return_status) {
                log_message('info', __METHOD__ . " => Upcountry return True" . $booking_id);
                return $query1;
            } else {
                log_message('info', __METHOD__ . " => Upcountry return False" . $booking_id);
                return FALSE;
            }
        } else {
            log_message('info', __METHOD__ . " => Booking is not Assigned" . $booking_id);
        }
    }

    function process_cancel_form($booking_id, $status,$cancellation_reason, $cancellation_text,
        $agent_id, $agent_name, $partner_id) {
        log_message('info', __METHOD__ . " => Entering " . $booking_id);
        $data['internal_status'] = $data['cancellation_reason'] = $cancellation_reason;
        $data['closed_date'] = $data['update_date'] = date("Y-m-d H:i:s");

        $data['current_status'] = _247AROUND_CANCELLED;
        if (!empty($cancellation_text)) {
            $data['closing_remarks'] = $cancellation_text;
        }
        $data_vendor['cancellation_reason'] = $data['cancellation_reason'];

        $partner_status = $this->My_CI->booking_utilities->get_partner_status_mapping_data($data['current_status'], $data['internal_status'], $partner_id, $booking_id);
        if (!empty($partner_status)) {
            $data['partner_current_status'] = $partner_status[0];
            $data['partner_internal_status'] = $partner_status[1];
        }


        log_message('info', __FUNCTION__ . " Update booking  " . print_r($data, true));

        $this->My_CI->booking_model->update_booking($booking_id, $data);

        //Update this booking in vendor action table
        $data_vendor['update_date'] = date("Y-m-d H:i:s");
        $data_vendor['current_status'] = $data_vendor['internal_status'] = _247AROUND_CANCELLED;
        log_message('info', __FUNCTION__ . " Update Service center action table  " . print_r($data_vendor, true));
        $this->My_CI->vendor_model->update_service_center_action($booking_id, $data_vendor);

        $this->update_price_while_cancel_booking($booking_id);

        //Update Spare parts details table
        $this->My_CI->service_centers_model->update_spare_parts(array('booking_id' => $booking_id), array('status' => _247AROUND_CANCELLED));

        //Log this state change as well for this booking
        //param:-- booking id, new state, old state, employee id, employee name
        $this->My_CI->notify->insert_state_change($booking_id, $data['current_status'], $status, $data['cancellation_reason'], $agent_id, $agent_name, _247AROUND);
        // Not send Cancallation sms to customer for Query booking
        // this is used to send email or sms while booking cancelled
        $url = base_url() . "employee/do_background_process/send_sms_email_for_booking";
        $send['booking_id'] = $booking_id;
        $send['state'] = $data['current_status'];
        $this->My_CI->asynchronous_lib->do_background_process($url, $send);

        // call partner callback
        $this->My_CI->partner_cb->partner_callback($booking_id);
        log_message('info', __METHOD__ . " => Exit " . $booking_id);
    }
    
    function update_price_while_cancel_booking($booking_id) {
	log_message('info', __FUNCTION__ . " Booking Id  " . print_r($booking_id, true));
	$unit_details['booking_status'] = "Cancelled";
	$unit_details['vendor_to_around'] = $unit_details['around_to_vendor'] = 0;
        $unit_details['ud_closed_date'] = date("Y-m-d H:i:s");

	log_message('info', __FUNCTION__ . " Update unit details  " . print_r($unit_details, true));
	$this->My_CI->booking_model->update_booking_unit_details($booking_id, $unit_details);
    }
    /**
     * @desc: This is used to insert unit in sc table when booking updated and unit not not exist
     * If units do not exist in sc table then it insert into sc table. 
     * And if Sc table has extra unit means unit deleted from uit details table then it will be delete from sc table.
     * And create a job card
     * @param String $booking_id
     */
    function check_unit_in_sc($booking_id) {
        log_message('info', __FUNCTION__ . " Booking Id  " . print_r($booking_id, true));
        if (!empty($booking_id)) {
            $data = $this->My_CI->booking_model->getbooking_history($booking_id);
            if (!empty($data)) {
                log_message('info', __FUNCTION__ . " Booking Id DATA Found " . print_r($booking_id, true));
                if (!is_null($data[0]['assigned_vendor_id'])) {
                    log_message('info', __FUNCTION__ . " Booking Assigned");
                    $unit_details = $this->My_CI->booking_model->get_unit_details(array('booking_id' => $booking_id));
                    if (!empty($unit_details)) {
                        log_message('info', __FUNCTION__ . " Booking Unit details exist");
                        foreach ($unit_details as $value) {
                            $sc_data = $this->My_CI->service_centers_model->get_service_center_action_details("unit_details_id", array('unit_details_id' => $value['id'], 'booking_id' => $booking_id));
                            if (empty($sc_data)) {
                                $sc_data['current_status'] = "Pending";
                                $sc_data['update_date'] = date('Y-m-d H:i:s');
                                $sc_data['internal_status'] = "Pending";
                                $sc_data['service_center_id'] = $data[0]['assigned_vendor_id'];
                                $sc_data['booking_id'] = $booking_id;
                                $sc_data['unit_details_id'] = $value['id'];
                                $sc_id = $this->My_CI->vendor_model->insert_service_center_action($sc_data);
                                if (!$sc_id) {
                                    log_message('info', __METHOD__ . "=> Data is not inserted into service center "
                                            . "action table booking_id: " . $booking_id . ", data: " . print_r($sc_data, true));
                                }
                            } else {
                                log_message('info', __FUNCTION__ . " Booking Id  " . print_r($booking_id, true) . " Unit exist in sc table " . $value['id']);
                            }
                        }
                        $sc_data1 = $this->My_CI->service_centers_model->get_service_center_action_details("unit_details_id", array('booking_id' => $booking_id));
                        if (!empty($sc_data1)) {
                            foreach ($sc_data1 as $value1) {
                                $unit_details = $this->My_CI->booking_model->get_unit_details(array('id' => $value1['unit_details_id'], 'booking_id' => $booking_id));
                                if (empty($unit_details)) {
                                    log_message('info', __FUNCTION__ . " Booking Unit details not exist  unit_id" . $value1['unit_details_id']);
                                    $this->My_CI->service_centers_model->delete_sc_unit_details(array('unit_details_id' => $value1['unit_details_id'], 'booking_id' => $booking_id));
                                }
                            }
                        }
                    } else {
                        log_message('info', __FUNCTION__ . " Booking Unit details not exist  Booking Id  " . print_r($booking_id, true));
                    }
                } else {
                    //Since booking has been converted to query, delete this entry from
                    //service center booking action table as well.
                    log_message('info', __FUNCTION__ . " Request to delete booking from service center action table Booking ID" . $booking_id);
                    $this->My_CI->service_centers_model->delete_booking_id($booking_id);
                    log_message('info', __FUNCTION__ . " Booking Not Assign-  Booking Id  " . print_r($booking_id, true));
                }
                //Prepare job card
                $this->My_CI->booking_utilities->lib_prepare_job_card_using_booking_id($booking_id);
                log_message('info', "Async Process to create Job card: " . $booking_id);
            } else {
                log_message('info', __FUNCTION__ . " Booking Id Not Exist  " . print_r($booking_id, true));
            }
        }
    }

    function send_sms_create_job_card($query) {
        if ($query[0]['request_type'] == HOME_THEATER_REPAIR_SERVICE_TAG || $query[0]['request_type'] == HOME_THEATER_REPAIR_SERVICE_TAG_OUT_OF_WARRANTY) {
            $unit_details = $this->My_CI->booking_model->get_unit_details(array('booking_id' => $query[0]['booking_id']));
            $sms['smsData']['brand_service'] = $unit_details[0]['appliance_brand'] . " " . $query[0]['services'];
            $sms['smsData']['sf_phone'] = $query[0]['phone_1'] . ", "
                    . $query[0]['primary_contact_phone_1'] . ", " . $query[0]['owner_phone_1'];
            $sms['smsData']['sf_address'] = $query[0]['address'];
            $sms['tag'] = "home_theater_repair";
            $sms['booking_id'] = $query[0]['booking_id'];
            $sms['type'] = "user";
            $sms['type_id'] = $query[0]['user_id'];
            $sms['phone_no'] = $query[0]['booking_primary_contact_no'];
            $this->My_CI->notify->send_sms_msg91($sms);
        } 
        //else {
//            //Send SMS to customer
//            $sms['tag'] = "service_centre_assigned";
//            $sms['phone_no'] = $query[0]['booking_primary_contact_no'];
//            $sms['booking_id'] = $query[0]['booking_id'];
//            $sms['type'] = "user";
//            $sms['type_id'] = $query[0]['user_id'];
//            $sms['smsData'] = "";
//
//            $this->My_CI->notify->send_sms_msg91($sms);
//        }


        log_message('info', "Send SMS to customer: " . $query[0]['booking_id']);

        //Prepare job card
        $this->My_CI->booking_utilities->lib_prepare_job_card_using_booking_id($query[0]['booking_id']);
        $this->My_CI->booking_utilities->lib_send_mail_to_vendor($query[0]['booking_id'], "");
        log_message('info', "Async Process to create Job card: " . $query[0]['booking_id']);

    }
    
   /**
     * @desc: This method is used to send sms on the basis of upcountry charges
     * @param Array $booking
     * @param String $appliance
     * @param Array/boolean $is_price
     * @param Array $appliance_category
     * @param String $file_type
     * @param String $partner_data
     * @return boolean
     */
    function check_upcountry($booking, $appliance, $is_price, $file_type) {
        log_message('info', __FUNCTION__ );
        $partner_data = $this->My_CI->initialized_variable->get_partner_data();
        if (!empty($is_price)) {
            log_message('info', __FUNCTION__ . ' Price Exist');
            $data = $this->check_upcountry_vendor_availability($booking['city'], $booking['booking_pincode'], $booking['service_id'], $partner_data, false);
            $charges = 0;
                log_message('info', __FUNCTION__ . ' Upcountry  Provide');
                switch ($data['message']) {
                    case NOT_UPCOUNTRY_BOOKING:
                    case UPCOUNTRY_BOOKING:
                    
                        if ($is_price['is_upcountry'] == 0) {
                            log_message('info', __FUNCTION__ . ' Upcountry Not Provide');
                            $price = (($data['upcountry_distance'] * DEFAULT_UPCOUNTRY_RATE) +
                                    $is_price['customer_net_payable']);
                            if($price >0){
                                $charges = "Rs. " . round($price,0);
                               log_message('info', __FUNCTION__ . ' Price Sent to Customer ' . $charges);
                                
                            } else {
                                $charges = "FREE";
                            }
                            
                        } else {
                            log_message('info', __FUNCTION__ . ' UPCOUNTRY_BOOKING ');
                            if($is_price['customer_net_payable'] >0){
                                $charges = "Rs. " . round($is_price['customer_net_payable'],0);
                            } else {
                                $charges = "FREE";
                            }
                            
                            log_message('info', __FUNCTION__ . ' Price Sent to Customer ' . $charges);
                        }
                        

                        break;

                    case UPCOUNTRY_LIMIT_EXCEED:
                        log_message('info', __FUNCTION__ . ' UPCOUNTRY_LIMIT_EXCEED ');
                        if ($is_price['is_upcountry'] == 0) {
                            log_message('info', __FUNCTION__ . ' Upcountry Not Provide');
                            
                            //do not send sms to customer if upcountry distance is > 150 km
                            if ($data['upcountry_distance'] <= 150) {
                                $price = (($data['upcountry_distance'] * DEFAULT_UPCOUNTRY_RATE) +
                                        $is_price['customer_net_payable']);
                                if($price >0){
                                    $charges = "Rs. " . round($price,0);
                                   log_message('info', __FUNCTION__ . ' Price Sent to Customer ' . $charges);

                                } else {
                                    $charges = "FREE";
                                }
                                log_message('info', __FUNCTION__ . ' Price Sent to Customer ' . $charges);
                            } else {
                                // limit exceeded, do not send sms
                                log_message('info', __FUNCTION__ . ' limit exceeded, do not send sms ');
                                
                                //send mail to nitin/anuj
                                $subject = $booking['booking_id']." UPCOUNTRY LIMIT EXCEED, PARTNER NOT PROVIDE APPROVAL";
                                $to = NITS_ANUJ_EMAIL_ID;
                                $message = $booking['booking_id']. " BOOKING CITY ". $booking['city']. " SF ID "
                                        .$data['vendor_id']. " DISTRICT PINCODE ".$data['upcountry_pincode'];
                                $this->My_CI->notify->sendEmail("booking@247around.com", $to, "", "", $subject, $message, "");
                                
                                return false;
                            }
                        }
                        else {
                            // Not send sms, partner provide upcountry charges approval or not
                            log_message('info', __FUNCTION__ . ' Upcountry Limit exceed ');
                            
                            //send mail to nitin/anuj if partner does not approve additional upcountry charges
                            if($data['partner_upcountry_approval'] == 0){
                                $subject = $booking['booking_id']." UPCOUNTRY LIMIT EXCEED, PARTNER NOT PROVIDE APPROVAL";
                                $to = NITS_ANUJ_EMAIL_ID;
                                $message = $booking['booking_id']. " BOOKING CITY ". $booking['city']. " SF ID "
                                        .$data['vendor_id']. " DISTRICT PINCODE ".$data['upcountry_pincode'];
                                $this->My_CI->notify->sendEmail("booking@247around.com", $to, "", "", $subject, $message, "");
                            }
                            return false;
                        }
                        break;

                    case SF_DOES_NOT_EXIST:
                    
                        log_message('info', __FUNCTION__ . SF_DOES_NOT_EXIST );
                        $to = RM_EMAIL.", ". SF_NOT_EXISTING_IN_PINCODE_MAPPING_FILE_TO;
                        $cc = SF_NOT_EXISTING_IN_PINCODE_MAPPING_FILE_CC;
                        
                        $subject = "SF Not Exist in the Pincode ".$booking['booking_pincode']." For Appliance ". $appliance;
                        $message = "Booking City: ". $booking['city']." /n  Booking Pincode: ".$booking['booking_pincode']; 
                        
                        $this->My_CI->notify->sendEmail("booking@247around.com", $to, $cc, "", $subject, $message, "");
                        
                        return FALSE;
                    //break;
                        case UPCOUNTRY_DISTANCE_CAN_NOT_CALCULATE:
                            return FALSE;
                }
            
             $this->send_sms_to_snapdeal_customer($appliance, $booking['booking_primary_contact_no'], $booking['user_id'], $booking['booking_id'], $file_type, $partner_data[0]['public_name'], $charges);
             return true;
             } else {
             $this->send_sms_to_snapdeal_customer($appliance, $booking['booking_primary_contact_no'], $booking['user_id'], $booking['booking_id'], $file_type, $partner_data[0]['public_name'], "");
            return true;
        }
    }
    
    /**
     * @desc: This method is used to send sms to snapdeal shipped customer, whose edd is not tommorrow. It gets appliance free or not from notify.
     * Make sure array of smsData has index services first then message
     * @param String $appliance
     * @param String $phone_number
     * @param String $user_id
     * @param String $booking_id
     * @param String $file_type
     * @param String $partner
     * @param String $price
     * @return int
     */
    function send_sms_to_snapdeal_customer($appliance, $phone_number, $user_id, $booking_id, $file_type, $partner,$price) {
        log_message('info', __FUNCTION__ );
        switch ($file_type) {
            case "shipped":
                $sms['tag'] = "sd_shipped_missed_call_initial";

                //ordering of smsData is important, it should be as per the %s in the SMS
                $sms['smsData']['service'] = $appliance;
                $sms['smsData']['missed_call_number'] = SNAPDEAL_MISSED_CALLED_NUMBER;
                /* If price exist then send sms according to that otherwise
                 *  send sms by checking function get_product_free_not
                 */
                if(!empty($price)){
                    $sms['smsData']['message'] = $price;
                }else{
                    $sms['tag'] = "missed_call_initial_prod_desc_not_found";
                    
                }
                $sms['smsData']['partner'] = $partner;
                break;

            case "delivered":
                $sms['tag'] = "sd_delivered_missed_call_initial";

                //ordering of smsData is important, it should be as per the %s in the SMS
                $sms['smsData']['service'] = $appliance;
                $sms['smsData']['missed_call_number'] = SNAPDEAL_MISSED_CALLED_NUMBER;
                /* If price exist then send sms according to that otherwise
                 *  send sms by checking function get_product_free_not
                 */
                if(!empty($price)){
                    
                    $sms['smsData']['message'] = $price;
                   
                }else{
                    $sms['tag'] = "missed_call_initial_prod_desc_not_found";
                }
                $sms['smsData']['partner'] = $partner;
                break;

            default:
                return 0;
        }

	$sms['phone_no'] = $phone_number;
	$sms['booking_id'] = $booking_id;
	$sms['type'] = "user";
	$sms['type_id'] = $user_id;

	$this->My_CI->notify->send_sms_msg91($sms);
    }
    
   function allot_partner_id_for_brand($service_id, $state, $brand) {
        log_message('info', __FUNCTION__ . ' ' . $service_id, $state, $brand);
       
        $partner_array = $this->My_CI->partner_model->get_active_partner_id_by_service_id_brand($brand, $service_id);
        
        if (!empty($partner_array)) {

            foreach ($partner_array as $value) {
                //Now getting details for each Partner 
                $filtered_partner_state = $this->My_CI->partner_model->check_activated_partner_for_state_service($state, $value['partner_id'], $service_id);
                if ($filtered_partner_state) {
                    //Now assigning this case to Partner
                   
                    return $value['partner_id'];
                } else {
                    return false;
                }
            }
        } else {
            log_message('info', ' No Active Partner has been Found in for Brand ' . $brand . ' and service_id ' . $service_id);
            //Now assigning this case to SS
            return false;
        }
        return false;
    }
    
    /* @desc: This function is used to convert the excel file into pdf
     * @param: $excel_file string  excel file with path which need to be converted into PDF  
     * @param: $bitbuket_dir string S3 directory in which PDF need to be upload
     * @param: $id string booking_id/invoice_id/any_other_id for reference to file
     * @return: $result JSON
     */
    public function convert_excel_to_pdf($excel_file,$id, $s3_folder_name) {
        
        $output_file_excel = $excel_file;
        $target_url = PDF_CONVERTER_URL.'pdfconverter/excel_to_pdf_converter';

        if (function_exists('curl_file_create')) {
            $cFile = curl_file_create($output_file_excel);
        } else { // 
            $cFile = '@' . realpath($output_file_excel);
        }
        $post = array('bucket_dir' => BITBUCKET_DIRECTORY, 'id' => $id, 
            'file_contents' => $cFile,'auth_key'=>PDF_CONVERTER_AUTH_KEY,
            's3_folder_name' => $s3_folder_name);
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $target_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }


}