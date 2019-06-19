<?php

class Miscelleneous {
    public $tatFaultyBookingCriteria = array();

    public function __construct() {
        $this->My_CI = & get_instance();
        $this->My_CI->load->helper(array('form', 'url'));
        $this->My_CI->load->library('email');
        $this->My_CI->load->library('partner_cb');
        $this->My_CI->load->library('initialized_variable');
        $this->My_CI->load->library('asynchronous_lib');
        $this->My_CI->load->library('booking_utilities');
        $this->My_CI->load->library('notify');
        $this->My_CI->load->library('push_notification_lib');
        $this->My_CI->load->library('send_grid_api');
        $this->My_CI->load->library('s3');
        $this->My_CI->load->library('PHPReport');
        $this->My_CI->load->model('vendor_model');
        $this->My_CI->load->model('reusable_model');
        $this->My_CI->load->model('booking_model');
        $this->My_CI->load->model('upcountry_model');
        $this->My_CI->load->model('partner_model');
        $this->My_CI->load->model('dealer_model');
        $this->My_CI->load->model('inventory_model');
        $this->My_CI->load->library('form_validation');
        $this->My_CI->load->model('service_centers_model');
        $this->My_CI->load->model('penalty_model');
        $this->My_CI->load->model('engineer_model');
        $this->My_CI->load->driver('cache');
        $this->My_CI->load->model('dashboard_model');
    }

    /**
     * @desc This method is used to check upcountry availability on the basis of booking pincode, service id.
     * @param String $booking_city
     * @param String $booking_pincode
     * @param String $service_id
     * @return Array
     */
    function check_upcountry_vendor_availability($booking_city, $booking_pincode, $service_id, $partner_data, $assigned_vendor_id = false) {
        log_message('info', __FUNCTION__ . ' => booking city' . $booking_city . " booking pincode " . $booking_pincode
                . " service id " . $service_id);
        //Get Available Vendor in this pincode who work this service
        $check_vendor = $this->My_CI->upcountry_model->get_vendor_upcountry($booking_pincode, $service_id, $assigned_vendor_id);
        $sf_city = $this->My_CI->vendor_model->get_distict_details_from_india_pincode($booking_pincode)['district'];
        $data1 = array();
        $is_return = 0;
        $mesg1 = array();

        // if $check_vendor is empty then return because we are not providing service in this pincode
        if (!empty($check_vendor)) {
            // If count is one, means only one vebdor is avaliable in this pincode
            if (count($check_vendor) == 1) {
                if ($check_vendor[0]['is_upcountry'] == 1) {
                    $data['vendor_id'] = $check_vendor[0]['Vendor_ID'];
                    $data['city'] = $sf_city;
                    $data['min_upcountry_distance'] = $check_vendor[0]['min_upcountry_distance'];
                    array_push($data1, $data);
                } else {
                    $msg['vendor_id'] = $check_vendor[0]['Vendor_ID'];
                    $msg['message'] = NOT_UPCOUNTRY_BOOKING;
                    $msg['upcountry_remarks'] = NON_UPCOUNTRY_VENDOR;
                    $msg['upcountry_distance'] = 0;

                    return $msg;
                }
            } else  if (count($check_vendor) > 1) {
                foreach ($check_vendor as $vendor) {
                    if ($vendor['is_upcountry'] == 1) {
                        $data['vendor_id'] = $vendor['Vendor_ID'];
                        $data['city'] = $sf_city;
                        $data['min_upcountry_distance'] = $check_vendor[0]['min_upcountry_distance'];

                        array_push($data1, $data);
                    } else {
                        $msg['vendor_id'] = $vendor['Vendor_ID'];
                        $msg['message'] = NOT_UPCOUNTRY_BOOKING;
                        $msg['upcountry_distance'] = 0;
                        $is_return = 1;
                        array_push($mesg1, $msg);
                    }
                }

                if ($is_return == 1) {

                    if (count($mesg1) > 1) {
                        $multiple_vendor['message'] = SF_DOES_NOT_EXIST;
                        $multiple_vendor['upcountry_remarks'] = MULTIPLE_NON_UPCOUNTRY_VENDOR;
                        return $multiple_vendor;
                    } else {
                        $mesg1[0]['upcountry_remarks'] = NON_UPCOUNTRY_VENDOR;
                        return $mesg1[0];
                    }
                }
            }

            return $this->My_CI->upcountry_model->action_upcountry_booking($booking_city, $booking_pincode, $data1, $partner_data);
        } else {

            $msg['message'] = SF_DOES_NOT_EXIST;
            $msg['vendor_not_found'] = 1;
            $msg['upcountry_remarks'] = SF_DOES_NOT_EXIST;

            return $msg;
        }
    }

    /**
     * @desc This method is used to assign service center to booking
     * @param String $service_center_id
     * @param String $booking_id
     * @return boolean
     */
    function assign_vendor_process($service_center_id, $booking_id, $partner_id, $agent_id, $agent_type) {
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
        $partner_status = $this->My_CI->booking_utilities->get_partner_status_mapping_data(_247AROUND_PENDING, ASSIGNED_VENDOR, $partner_id, $booking_id);
        if (!empty($partner_status)) {
            $b['partner_current_status'] = $partner_status[0];
            $b['partner_internal_status'] = $partner_status[1];
            $b['actor'] = $partner_status[2];
            $b['next_action'] = $partner_status[3];
        }
        //Assign service centre and engineer
        $assigned = $this->My_CI->vendor_model->assign_service_center_for_booking($booking_id, $b);
        if ($assigned) {
            log_message('info', __FUNCTION__ . " Assigned...... booking_id " . $booking_id);
            //Send Push Notification
            $receiverArrayVendor['vendor'] = array($service_center_id);
            $notificationTextArrayVendor['msg'] = array($booking_id);
             $this->My_CI->push_notification_lib->create_and_send_push_notiifcation(BOOKING_ASSIGN_TO_VENDOR,$receiverArrayVendor,$notificationTextArrayVendor);
             
            
            //End Sending Push Notification
            // Data to be insert in service center
            $sc_data['current_status'] = "Pending";
            $sc_data['update_date'] = date('Y-m-d H:i:s');
            $sc_data['internal_status'] = "Pending";
            $sc_data['service_center_id'] = $service_center_id;
            $sc_data['booking_id'] = $booking_id;
            
             $vendor_data = $this->My_CI->vendor_model->getVendorDetails("isEngineerApp", array("id" =>$service_center_id, "isEngineerApp" => 1));
            
            // Unit Details Data
            $where = array('booking_id' => $booking_id);
            $unit_details = $this->My_CI->booking_model->get_unit_details($where);
            foreach ($unit_details as $value) {
                $sc_data['unit_details_id'] = $value['id'];
                $sc_id = $this->My_CI->vendor_model->insert_service_center_action($sc_data);
                
                if (!$sc_id) {
                    log_message('info', __METHOD__ . "=> Data is not inserted into service center "
                            . "action table booking_id: " . $booking_id . ", data: " . print_r($sc_data, true));
                    $this->My_CI->notify->sendEmail(NOREPLY_EMAIL_ID, DEVELOPER_EMAIL, "", "", 
                            "BUG IN ASSIGN ". $booking_id, "SF Assigned but Action table not updated", "",SF_ASSIGNED_ACTION_TABLE_NOT_UPDATED);
                    
                }
                if(!empty($vendor_data)){
                     $engineer_action['unit_details_id'] = $value['id'];
                     $engineer_action['booking_id'] = $booking_id;
                     $engineer_action['service_center_id'] = $service_center_id;
                     $engineer_action['current_status'] = _247AROUND_PENDING;
                     $engineer_action['internal_status'] = _247AROUND_PENDING;
                     $engineer_action["create_date"] = date("Y-m-d H:i:s");
                    
                     $enID = $this->My_CI->engineer_model->insert_engineer_action($engineer_action);
                     if(!$enID){
                          $this->My_CI->notify->sendEmail(NOREPLY_EMAIL_ID, DEVELOPER_EMAIL, "", "", 
                             "BUG in Enginner Table ". $booking_id, "SF Assigned but Action table not updated", "",SF_ASSIGNED_ACTION_TABLE_NOT_UPDATED);
                     }
                 }
                 
                 
                    
                //process inventory stock for each unit if price tag is wall mount
                if ($value['price_tags'] == _247AROUND_WALL_MOUNT__PRICE_TAG) {
                    $match = array();
                    //get the size from the capacity to know the part number
                    preg_match('/[0-9]+/', $value['appliance_capacity'], $match);
                    if (!empty($match)) {
                        if ($match[0] <= 32) {
                            $data['part_number'] = LESS_THAN_32_BRACKETS_PART_NUMBER;
                        } else if ($match[0] > 32) {
                            $data['part_number'] = GREATER_THAN_32_BRACKETS_PART_NUMBER;
                        }

                        $data['receiver_entity_id'] = $service_center_id;
                        $data['receiver_entity_type'] = _247AROUND_SF_STRING;
                        $data['stock'] = -1 ;
                        $data['booking_id'] = $booking_id;
                        $data['agent_id'] = $agent_id;
                        $data['agent_type'] = $agent_type;

                        $this->process_inventory_stocks($data);
                    }
                }
            }
            log_message('info', __FUNCTION__ . " Partner Callback booking_id " . $booking_id );
            $this->My_CI->partner_cb->partner_callback($booking_id);
            log_message('info', __FUNCTION__ . " Exit...... booking_id " . $booking_id . " service center id " . $service_center_id);
            return true;
        } else {
            log_message('info', __METHOD__ . "=> Not Assign for Sc "
                    . $service_center_id);
            return false;
        }
    }

    function assign_upcountry_booking($booking_id, $agent_id, $agent_name) {
        log_message('info', __METHOD__ . " => Entering " . $booking_id . ' agent_id: ' . $agent_id . ' agent_name: ' . $agent_name);
        $query1 = $this->My_CI->booking_model->getbooking_history($booking_id, "1");
        $vendor_data = array();
        if (!empty($query1[0]['assigned_vendor_id'])) {
            $vendor_data[0]['vendor_id'] = $query1[0]['assigned_vendor_id'];
            //SF Min Upcountry Distance
            $vendor_data[0]['min_upcountry_distance'] = $query1[0]['min_upcountry_distance'];

            if (!empty($query1[0]['district'])) {
                $vendor_data[0]['city'] = $query1[0]['district'];
            } else {
                $vendor_data[0]['city'] = $this->My_CI->vendor_model->get_distict_details_from_india_pincode($query1[0]['booking_pincode'])['district'];
            }

            $p_where = array("id" => $query1[0]['partner_id']);
            $partner_details = $this->My_CI->partner_model->get_all_partner($p_where);
            $data = $this->My_CI->upcountry_model->action_upcountry_booking($query1[0]['city'], $query1[0]['booking_pincode'], $vendor_data, $partner_details);

            $return_status = $this->_assign_upcountry_booking($booking_id, $data, $query1, $agent_id, $agent_name);

            if ($return_status) {
                log_message('info', __METHOD__ . " => Upcountry return True" . $booking_id);
                return $query1;
            } else {
                log_message('info', __METHOD__ . " => Upcountry return False" . $booking_id);
                return FALSE;
            }
        } else {
            log_message('info', __METHOD__ . " => Booking is not Assigned" . $booking_id);
            return FALSE;
        }
    }

    function _assign_upcountry_booking($booking_id, $data, $query1, $agent_id, $agent_name) {
        
        if(empty($agent_id)){
            $agent_id = _247AROUND_DEFAULT_AGENT;
        }
       
        $unit_details = $this->My_CI->booking_model->get_unit_details(array('booking_id' => $booking_id));
        $cus_net_payable = 0;
        foreach ($unit_details as $value) {
            $cus_net_payable += $value['customer_net_payable'];
        }
        $partner_am_email = "";
        $return_status = TRUE;
        
        //$rm = $this->My_CI->vendor_model->get_rm_sf_relation_by_sf_id($query1[0]['assigned_vendor_id']);
        $rm_email = "";
//        if (!empty($rm)) {
//            $rm_email = ", " . $rm[0]['official_email'];
//        }
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
                $booking['upcountry_update_date'] = date('Y-m-d H:i:s');

                $is_upcountry = $this->My_CI->upcountry_model->is_upcountry_booking($booking_id);
                
                if (empty($is_upcountry)) {
                    log_message('info', __METHOD__ . " => Customer will pay upcountry charges " . $booking_id);
                    
                    $booking['upcountry_paid_by_customer'] = 1;
                    $booking['upcountry_remarks'] = CUSTOMER_PAID_UPCOUNTRY;
                    $booking['partner_upcountry_rate'] = DEFAULT_UPCOUNTRY_RATE;
                    
                    $c_upcountry = $this->My_CI->upcountry_model->is_customer_pay_upcountry($booking_id);
                    if(!empty($c_upcountry) && in_array(1, array_column($c_upcountry, 'flat_upcountry')) !== FALSE ){
                        
                        $cust_price = $c_upcountry[0]['upcountry_customer_price'];
                        $booking['flat_upcountry'] = $c_upcountry[0]['flat_upcountry'];
                        $booking['upcountry_sf_payout'] = $c_upcountry[0]['upcountry_vendor_price'];
                        $booking['partner_upcountry_charges'] = $c_upcountry[0]['upcountry_partner_price'];
                        $booking['upcountry_to_be_paid_by_customer'] = $c_upcountry[0]['upcountry_customer_price'];

                        
                    } else {
                        
                        $cust_price = ($booking['partner_upcountry_rate'] * $booking['upcountry_distance']);
                        $booking['flat_upcountry'] = 0;
                        $booking['upcountry_sf_payout'] = ($booking['sf_upcountry_rate'] * $booking['upcountry_distance']);
                        $booking['partner_upcountry_charges'] = 0;
                        $booking['upcountry_to_be_paid_by_customer'] =  $cust_price;
                    }
                    

                    log_message('info', __METHOD__ . " => Amount due added " . $booking_id);
                    $booking['amount_due'] = $cus_net_payable + $cust_price ;


                    $this->My_CI->booking_model->update_booking($booking_id, $booking);
                    $return_status = TRUE;
                } else if (in_array(-1, array_column($is_upcountry, 'is_upcountry')) !== FALSE 
                        && in_array(1, array_column($is_upcountry, 'is_upcountry')) == FALSE ) {
                    
                    $is_not_upcountry = $this->My_CI->upcountry_model->is_customer_pay_upcountry($booking_id);
                    if(!empty($is_not_upcountry)){
                        log_message('info', __METHOD__ . " => Customer will pay upcountry charges " . $booking_id);
                        $booking['upcountry_paid_by_customer'] = 1;
                        $booking['partner_upcountry_rate'] = DEFAULT_UPCOUNTRY_RATE;
                        $booking['upcountry_remarks'] = CUSTOMER_PAID_UPCOUNTRY;
                        
                         if(!empty($is_not_upcountry) && in_array(1, array_column($is_not_upcountry, 'flat_upcountry')) !== FALSE ){
                        
                                $cust_price = $is_not_upcountry[0]['upcountry_customer_price'];
                                $booking['flat_upcountry'] = $is_not_upcountry[0]['flat_upcountry'];
                                $booking['upcountry_sf_payout'] = $is_not_upcountry[0]['upcountry_vendor_price'];
                                $booking['partner_upcountry_charges'] = $is_not_upcountry[0]['upcountry_partner_price'];
                                $booking['upcountry_to_be_paid_by_customer'] = $is_not_upcountry[0]['upcountry_customer_price'];
                            } else {

                                $cust_price = ($booking['partner_upcountry_rate'] * $booking['upcountry_distance']);
                                $booking['flat_upcountry'] = 0;
                                $booking['upcountry_sf_payout'] = ($booking['sf_upcountry_rate'] * $booking['upcountry_distance']);
                                $booking['partner_upcountry_charges'] = 0;
                                $booking['upcountry_to_be_paid_by_customer'] = $cust_price;
                               
                            }

                        log_message('info', __METHOD__ . " => Amount due added " . $booking_id);
                        $booking['amount_due'] = $cus_net_payable + $cust_price;
                    } else {
                        log_message('info', __METHOD__ . " => Customer or Partner does not pay upcountry charges " . $booking_id);
                        $booking['is_upcountry'] = 0;
                        $booking['upcountry_pincode'] = NULL;
                        $booking['sub_vendor_id'] = NULL;
                        $booking['upcountry_distance'] = NULL;
                        $booking['sf_upcountry_rate'] = NULL;
                        $booking['partner_upcountry_rate'] = NULL;
                        $booking['upcountry_paid_by_customer'] = '0';
                        $booking['upcountry_partner_approved'] = '1';
                        $booking['upcountry_remarks'] = CUSTOMER_AND_PARTNER_BOTH_NOT_PROVIDE_UPCOUNTRY_FOR_THIS_PRICE_TAG;
                        $booking['flat_upcountry'] = 0;
                        $booking['upcountry_sf_payout'] = 0;
                        $booking['partner_upcountry_charges'] = 0;
                        $booking['upcountry_to_be_paid_by_customer'] =  0;

                        log_message('info', __METHOD__ . " => Amount due added " . $booking_id);
                        $booking['amount_due'] = $cus_net_payable;
                    }

                    $this->My_CI->booking_model->update_booking($booking_id, $booking);
                    log_message('info', __METHOD__ . " => Not Upcountry Booking" . $booking_id);
                    $return_status = TRUE;
                    break;
                } else if (!empty($is_upcountry)) {
                    // Upcountry charges once approved should not be asked to approve again
                    if($data['partner_upcountry_approval'] == 1 && $data['message'] == UPCOUNTRY_LIMIT_EXCEED){
                        $is_approved = $this->My_CI->booking_model->getbooking_state_change_by_any(array("booking_id" =>$booking_id, "new_state" =>UPCOUNTRY_CHARGES_APPROVED));
                        if(!empty($is_approved)){
                            $data['message'] = UPCOUNTRY_BOOKING;
                        }
                    }
                    if(in_array(1, array_column($is_upcountry, 'flat_upcountry')) !== FALSE ){
                        $booking['flat_upcountry'] = $is_upcountry[0]['flat_upcountry'];
                        $booking['upcountry_sf_payout'] = $is_upcountry[0]['upcountry_vendor_price'];
                        $booking['partner_upcountry_charges'] = $is_upcountry[0]['upcountry_partner_price'];
                        $booking['upcountry_to_be_paid_by_customer'] = $is_upcountry[0]['upcountry_customer_price'];
                    }
                   
                    if ($data['message'] !== UPCOUNTRY_LIMIT_EXCEED) {

                        log_message('info', __METHOD__ . " => Upcountry Booking Free Booking " . $booking_id);
                        $booking['upcountry_paid_by_customer'] = 0;
                        $booking['amount_due'] = $cus_net_payable;
                        $booking['upcountry_remarks'] = PARTNER_PAID_UPCOUNTRY;
                        $booking['upcountry_bill_to_partner'] = $data['upcountry_bill_to_partner'];
                        $this->My_CI->booking_model->update_booking($booking_id, $booking);
                        $return_status = TRUE;
                    } else if ($data['partner_upcountry_approval'] == 1 && $data['message'] == UPCOUNTRY_LIMIT_EXCEED) {

                        log_message('info', __METHOD__ . " => Upcountry Waiting for Approval " . $booking_id);
                        $booking['assigned_vendor_id'] = NULL;
                        $booking['internal_status'] = UPCOUNTRY_BOOKING_NEED_TO_APPROVAL;
                        $booking['upcountry_partner_approved'] = '0';
                        $booking['upcountry_paid_by_customer'] = 0;
                        $booking['upcountry_bill_to_partner'] = $data['upcountry_bill_to_partner'];
                        $booking['upcountry_remarks'] = UPCOUNTRY_BOOKING_NEED_TO_APPROVAL;
                        $booking['amount_due'] = $cus_net_payable;
                        
                        $partner_status = $this->My_CI->booking_utilities->get_partner_status_mapping_data(_247AROUND_PENDING, UPCOUNTRY_BOOKING_NEED_TO_APPROVAL,
                                $query1[0]['partner_id'], $booking_id);
                        $actor = $next_action = 'not_define';
                        if (!empty($partner_status)) {
                            $booking['partner_current_status'] = $partner_status[0];
                            $booking['partner_internal_status'] = $partner_status[1];
                            $actor = $booking['actor'] = $partner_status[2];
                            $next_action = $booking['next_action'] = $partner_status[3];
                        }

                        $this->My_CI->booking_model->update_booking($booking_id, $booking);
                        $this->My_CI->service_centers_model->delete_booking_id($booking_id);
                        $this->My_CI->notify->insert_state_change($booking_id, "Waiting Partner Approval", _247AROUND_PENDING, "Waiting Upcountry to Approval", $agent_id, $agent_name, 
                                $actor,$next_action,_247AROUND);
                        
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
                        $up_mail_data['upcountry_distance'] = sprintf("%0.2f",$booking['upcountry_distance']);
                        $up_mail_data['partner_upcountry_rate'] = $booking['partner_upcountry_rate'];
                        $up_mail_data['municipal_limit'] = $query1[0]['municipal_limit'];
                        $up_mail_data['upcountry_pincode'] =  $booking['upcountry_pincode'];
                        

                        $message1 = $this->My_CI->load->view('employee/upcountry_approval_template', $up_mail_data, true);
                        
                        if ($booking['upcountry_distance'] > 300) {
                            $subject = "Upcountry Distance More Than 300 - Booking ID " . $query1[0]['booking_id'];
                            $to = ANUJ_EMAIL_ID.$partner_am_email;
                            $cc = "";
                        } else {
                            $subject = "Upcountry Charges Approval Required - Booking ID " . $query1[0]['booking_id'];
                            $to = $data['upcountry_approval_email'];
                            $cc = $partner_am_email;
                            //Send Push Notification
                        $receiverArray['partner'] = array($query1[0]['partner_id']);
                        $notificationTextArray['msg'] = array($booking_id);
                        $this->My_CI->push_notification_lib->create_and_send_push_notiifcation(UPCOUNTRY_APPROVAL,$receiverArray,$notificationTextArray);
                        //End Push Notification
                        }
                        $this->My_CI->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, "", $subject, $message1, "",UPCOUNTRY_APPROVAL_TAG, "", $query1[0]['booking_id']);

                        $return_status = FALSE;
                    } else if ($data['partner_upcountry_approval'] == 0 && $data['message'] == UPCOUNTRY_LIMIT_EXCEED) {

                        log_message('info', __METHOD__ . " => Upcountry, partner does not provide approval" . $booking_id);
                        $this->My_CI->booking_model->update_booking($booking_id, $booking);
                        $this->process_cancel_form($booking_id, "Pending", UPCOUNTRY_CHARGES_NOT_APPROVED, " Upcountry  Distance " . $data['upcountry_distance'], $agent_id, $agent_name, $query1[0]['partner_id'], _247AROUND);

//                        $to = ANUJ_EMAIL_ID;
//                        $cc = $partner_am_email;
//                        $message1 = $booking_id . " has auto cancelled because upcountry limit exceed "
//                                . "and partner does not provide upcountry charges approval. Upcountry Distance " . $data['upcountry_distance'] .
//                                " Upcountry Pincode " . $data['upcountry_pincode'] . " SF Name " . $query1[0]['vendor_name'];
                       // $this->My_CI->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, "", 'Upcountry Auto Cancel Booking', $message1, "",BOOKING_CANCELLED_NO_UPCOUNTRY_APPROVAL);

                        $return_status = FALSE;
                    }
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
                $booking['upcountry_remarks'] = $data['upcountry_remarks'];

                log_message('info', __METHOD__ . " => Amount due added " . $booking_id);
                $booking['amount_due'] = $cus_net_payable;

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
                $booking['amount_due'] = $cus_net_payable;
                $booking['upcountry_remarks'] = $data['upcountry_remarks'];

                $this->My_CI->booking_model->update_booking($booking_id, $booking);

                $to = ANUJ_EMAIL_ID . ", sales@247around.com , ". $rm_email;
                $cc = "abhaya@247around.com";
                $message1 = "Upcountry did not calculate for " . $booking_id;
                $this->My_CI->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, "", 'Upcountry Failed', $message1, "",UPCOUNTRY_DISTANCE_CAN_NOT_CALCULATE_EMAIL_TAG);

                $return_status = TRUE;
                break;
        }

        return $return_status;
    }

    function process_cancel_form($booking_id, $status, $cancellation_reason, $cancellation_text, $agent_id, $agent_name, $partner_id, $cancelled_by) {
        log_message('info', __METHOD__ . " => Entering " . $booking_id, ' status: ' . $status . ' cancellation_reason: ' . $cancellation_reason . ' agent_id: ' . $agent_id . ' agent_name: ' . $agent_name . ' partner_id: ' . $partner_id);
        $data['internal_status'] = $data['cancellation_reason'] = $cancellation_reason;
        $historyRemarks = $cancellation_reason."<br> ".$cancellation_text;
        $data['closed_date'] = $data['update_date'] = date("Y-m-d H:i:s");

        $data['current_status'] = _247AROUND_CANCELLED;
        if (!empty($cancellation_text)) {
            $data['closing_remarks'] = $cancellation_text;
        }
        $data_vendor['cancellation_reason'] = $data['cancellation_reason'];

        $partner_status = $this->My_CI->booking_utilities->get_partner_status_mapping_data($data['current_status'], $data['internal_status'], $partner_id, $booking_id);
        $actor = $next_action = 'not_define';
        if (!empty($partner_status)) {
            $data['partner_current_status'] = $partner_status[0];
            $data['partner_internal_status'] = $partner_status[1];
            $actor = $data['actor'] = $partner_status[2];
            $next_action = $data['next_action'] = $partner_status[3];
        }


        log_message('info', __FUNCTION__ . " Update booking  " . print_r($data, true));

        $data['service_center_closed_date'] = date("Y-m-d h:i:s");
        $this->My_CI->booking_model->update_booking($booking_id, $data);

        //Update this booking in vendor action table
        $data_vendor['update_date'] = date("Y-m-d H:i:s");
        $data_vendor['current_status'] = $data_vendor['internal_status'] = _247AROUND_CANCELLED;
        log_message('info', __FUNCTION__ . " Update Service center action table  " . print_r($data_vendor, true));
        $this->My_CI->vendor_model->update_service_center_action($booking_id, $data_vendor);

        $this->update_price_while_cancel_booking($booking_id, $agent_id, $cancelled_by);
        //Update Engineer table while booking cancelled
        $en_where1 = array("engineer_booking_action.booking_id" => $booking_id);
        $this->My_CI->engineer_model->update_engineer_table(array("current_status" => _247AROUND_CANCELLED, "internal_status" =>_247AROUND_CANCELLED), $en_where1);
        
        $spare = $this->My_CI->partner_model->get_spare_parts_by_any("spare_parts_details.id,spare_parts_details.partner_id, "
                . "spare_parts_details.entity_type, spare_parts_details.status, "
                . "requested_inventory_id, shipped_inventory_id", 
                array('booking_id' => $booking_id, 'status NOT IN ("'._247AROUND_COMPLETED.'","'._247AROUND_CANCELLED.'")' =>NULL ), 
                false);
        foreach($spare as $sp){
            

            if($sp['status'] == SPARE_PARTS_REQUESTED && $sp['entity_type'] == _247AROUND_SF_STRING){
                $this->My_CI->inventory_model->update_pending_inventory_stock_request($sp['entity_type'], 
                            $sp['partner_id'], $sp['requested_inventory_id'], -1);
            }
            
            //Update Spare parts details table
            $this->My_CI->service_centers_model->update_spare_parts(array('id'=> $sp['id']), array('old_status' => $sp['status'],'status' => _247AROUND_CANCELLED));
        }

        //Log this state change as well for this booking
        //param:-- booking id, new state, old state, employee id, employee name
        $this->My_CI->notify->insert_state_change($booking_id, $data['current_status'], $status, $historyRemarks, $agent_id, $agent_name,$actor,$next_action, $cancelled_by);
        $this->process_booking_tat_on_completion($booking_id);
        // Not send Cancallation sms to customer for Query booking
        // this is used to send email or sms while booking cancelled
        $url = base_url() . "employee/do_background_process/send_sms_email_for_booking";
        $send['booking_id'] = $booking_id;
        $send['state'] = $data['current_status'];
        $this->My_CI->asynchronous_lib->do_background_process($url, $send);
        //Inform to sf when partner/call center has cancelled booking
        $this->My_CI->notify->send_email_to_sf_when_booking_cancelled($booking_id);

        // call partner callback
        $this->My_CI->partner_cb->partner_callback($booking_id);
        log_message('info', __METHOD__ . " => Exit " . $booking_id);
    }

    function update_price_while_cancel_booking($booking_id, $agent_id, $cancelled_by) {
        log_message('info', __FUNCTION__ . " Booking Id  " . print_r($booking_id, true));
        $unit_details['booking_status'] = "Cancelled";
        $unit_details['vendor_to_around'] = $unit_details['around_to_vendor'] = 0;
        $unit_details['ud_closed_date'] = date("Y-m-d H:i:s");

        log_message('info', __FUNCTION__ . " Update unit details  " . print_r($unit_details, true));
        $this->My_CI->booking_model->update_booking_unit_details($booking_id, $unit_details);
        
        $booking_unit_details = $this->My_CI->reusable_model->get_search_query('booking_unit_details', 'booking_unit_details.price_tags,booking_unit_details.appliance_capacity', array('booking_unit_details.booking_id' => $booking_id, "booking_unit_details.price_tags like '%" . _247AROUND_WALL_MOUNT__PRICE_TAG . "%'" => NULL), NULL, NULL, NULL, NULL, NULL)->result_array();
        $booking_data = $this->My_CI->reusable_model->get_search_query('booking_details', 'booking_details.assigned_vendor_id', array('booking_details.booking_id' => $booking_id), NULL, NULL, NULL, NULL, NULL)->result_array();
        if (!empty($booking_unit_details)) {
            //process each unit if price tag is wall mount
            foreach ($booking_unit_details as $value) {
                $match = array();
                //get the size from the capacity to know the part number
                preg_match('/[0-9]+/', $value['appliance_capacity'], $match);
                if (!empty($match) && !empty($booking_data[0]['assigned_vendor_id'])) {
                    if ($match[0] <= 32) {
                        $data['part_number'] = LESS_THAN_32_BRACKETS_PART_NUMBER;
                    } else if ($match[0] > 32) {
                        $data['part_number'] = GREATER_THAN_32_BRACKETS_PART_NUMBER;
                    }

                    $data['receiver_entity_id'] = $booking_data[0]['assigned_vendor_id'];
                    $data['receiver_entity_type'] = _247AROUND_SF_STRING;
                    $data['stock'] = 1;
                    $data['booking_id'] = $booking_id;
                    $data['agent_id'] = $agent_id;
                    if($cancelled_by == _247AROUND){
                        $data['agent_type'] = _247AROUND_EMPLOYEE_STRING;
                    } else {
                        $data['agent_type'] = _247AROUND_PARTNER_STRING;
                    }
                    
                    $this->process_inventory_stocks($data);
                }
            }
        }
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
            $data = $this->My_CI->booking_model->getbooking_history($booking_id, "join");
            if (!empty($data)) {
                log_message('info', __FUNCTION__ . " Booking Id DATA Found " . print_r($booking_id, true));
                if (!is_null($data[0]['assigned_vendor_id'])) {
                    log_message('info', __FUNCTION__ . " Booking Assigned");
                    $unit_details = $this->My_CI->booking_model->get_unit_details(array('booking_id' => $booking_id));
                    if (!empty($unit_details)) {
                        log_message('info', __FUNCTION__ . " Booking Unit details exist");
                        foreach ($unit_details as $value) {
                            $sc_ba_data = $this->My_CI->service_centers_model->get_service_center_action_details("unit_details_id,current_status,internal_status", array('booking_id' => $booking_id));
                            $alreadyExist = false;
                            $sc_current_status = 'Pending';
                            $sc_internal_status = 'Pending';
                            foreach($sc_ba_data as $sc_values){
                                if($sc_values['unit_details_id'] ==  $value['id']){
                                    $alreadyExist  = true;
                                }
                                $sc_current_status = $sc_values['current_status'];
                                $sc_internal_status = $sc_values['internal_status'];
                            }
                            if (!$alreadyExist) {
                                $sc_data['current_status'] = $sc_current_status;
                                $sc_data['update_date'] = date('Y-m-d H:i:s');
                                $sc_data['internal_status'] = $sc_internal_status;
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

                            if ($data[0]['isEngineerApp'] == 1) {
                                $en_data = $this->My_CI->engineer_model->getengineer_action_data("unit_details_id", array('unit_details_id' => $value['id'], 'booking_id' => $booking_id));
                                if (empty($en_data)) {
                                    $en['current_status'] = "Pending";
                                    $en['create_date'] = date('Y-m-d H:i:s');
                                    $en['internal_status'] = "Pending";
                                    $en['service_center_id'] = $data[0]['assigned_vendor_id'];
                                    $en['booking_id'] = $booking_id;
                                    $en['unit_details_id'] = $value['id'];

                                    $this->My_CI->engineer_model->insert_engineer_action($en);
                                }
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

                        if ($data[0]['isEngineerApp'] == 1) {
                            $en_data1 = $this->My_CI->engineer_model->getengineer_action_data("unit_details_id", array('booking_id' => $booking_id));
                            if (!empty($en_data1)) {
                                foreach ($en_data1 as $value2) {
                                    $unit_details = $this->My_CI->booking_model->get_unit_details(array('id' => $value2['unit_details_id'], 'booking_id' => $booking_id));
                                    if (empty($unit_details)) {
                                        log_message('info', __FUNCTION__ . " Booking Unit details not exist  unit_id" . $value2['unit_details_id']);
                                        $this->My_CI->engineer_model->delete_engineer_table(array('unit_details_id' => $value2['unit_details_id'], 'booking_id' => $booking_id));
                                    }
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
                    if ($data[0]['isEngineerApp'] == 1) {
                        $this->My_CI->engineer_model->delete_booking_from_engineer_table($booking_id);
                    }
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
            $sms['smsData']['sf_address'] = $query[0]['address'].", ".$query[0]['sf_district'];
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
    function check_upcountry($booking, $appliance, $is_price, $file_type, $appliance_brand = false) {
        log_message('info', __FUNCTION__ . ' booking_data: ' . print_r($booking, true) . ' appliance: ' . print_r($appliance, true) . ' file_type: ' . $file_type);
        $partner_data = $this->My_CI->initialized_variable->get_partner_data();
        $booking_request = "";
        if(isset($booking['request_type'])){
            if ((stripos($booking['request_type'], 'Installation') !== false) || stripos($booking['request_type'], 'Repair') !== false) {
                $request_type = explode(" ", $booking['request_type']);
                $booking_request = $request_type[0];
            } else {
                $booking_request = $booking['request_type'];
            }
            
        }
        $partner_type = $this->My_CI->reusable_model->get_search_query('bookings_sources','partner_type' , array('partner_id'=>$partner_data[0]['partner_id']),NULL, NULL ,NULL,NULL,NULL)->result_array()[0]['partner_type'];
        if($partner_type == OEM){
            if(!empty($appliance_brand)){
                $smsPartner = $appliance_brand;
            }
            else{
               $smsPartner =  $partner_data[0]['public_name'];
            }
        }
        else{
            $smsPartner =  $partner_data[0]['public_name'];
        }
        $data = $this->check_upcountry_vendor_availability($booking['city'], $booking['booking_pincode'], $booking['service_id'], $partner_data, false);
        if (isset($data['vendor_not_found'])) {
            if ($data['vendor_not_found'] == 1) {
                $this->sf_not_exist_for_pincode($booking);
                return false;
            }
        }
        if (!empty($is_price)) {
            log_message('info', __FUNCTION__ . ' Price Exist');
            $charges = 0;
            log_message('info', __FUNCTION__ . ' Upcountry  Provide');
            switch ($data['message']) {
                case NOT_UPCOUNTRY_BOOKING:
                case UPCOUNTRY_BOOKING:

                    if ($is_price['is_upcountry'] == 0) {
                        log_message('info', __FUNCTION__ . ' Upcountry Not Provide');
                        $price = (($data['upcountry_distance'] * DEFAULT_UPCOUNTRY_RATE) +
                                $is_price['customer_net_payable']);
                        if ($price > 0) {
                            $charges = "Rs. " . round($price, 0);
                            log_message('info', __FUNCTION__ . ' Price Sent to Customer ' . $charges);
                        } else {
                            $charges = "FREE";
                        }
                    } else {
                        log_message('info', __FUNCTION__ . ' UPCOUNTRY_BOOKING ');
                        if ($is_price['customer_net_payable'] > 0) {
                            $charges = "Rs. " . round($is_price['customer_net_payable'], 0);
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
                            if ($price > 0) {
                                $charges = "Rs. " . round($price, 0);
                                log_message('info', __FUNCTION__ . ' Price Sent to Customer ' . $charges);
                            } else {
                                $charges = "FREE";
                            }
                            log_message('info', __FUNCTION__ . ' Price Sent to Customer ' . $charges);
                        } else {
                            // limit exceeded, do not send sms
                            log_message('info', __FUNCTION__ . ' limit exceeded, do not send sms ');

                            //send mail to nitin/anuj
                            $subject = $booking['booking_id'] . " UPCOUNTRY LIMIT EXCEED, PARTNER NOT PROVIDE APPROVAL";
                            $to = NITS_ANUJ_EMAIL_ID;
                            $message = $booking['booking_id'] . " BOOKING CITY " . $booking['city'] . " SF ID "
                                    . $data['vendor_id'] . " DISTRICT PINCODE " . $data['upcountry_pincode'];
                            $this->My_CI->notify->sendEmail(NOREPLY_EMAIL_ID, $to, "", "", $subject, $message, "",UPCOUNTRY_LIMIT_EXCEED);

                            return false;
                        }
                    } else {
                        // Not send sms, partner provide upcountry charges approval or not
                        log_message('info', __FUNCTION__ . ' Upcountry Limit exceed ');

                        //send mail to nitin/anuj if partner does not approve additional upcountry charges
                        if ($data['partner_upcountry_approval'] == 0) {
                            $subject = $booking['booking_id'] . " UPCOUNTRY LIMIT EXCEED, PARTNER NOT PROVIDE APPROVAL";
                            $to = NITS_ANUJ_EMAIL_ID;
                            $message = $booking['booking_id'] . " BOOKING CITY " . $booking['city'] . " SF ID "
                                    . $data['vendor_id'] . " DISTRICT PINCODE " . $data['upcountry_pincode'];
                            $this->My_CI->notify->sendEmail(NOREPLY_EMAIL_ID, $to, "", "", $subject, $message, "",UPCOUNTRY_LIMIT_EXCEED);
                        }
                        return false;
                    }
                    break;

                case SF_DOES_NOT_EXIST:

                    log_message('info', __FUNCTION__ . SF_DOES_NOT_EXIST);
                    if (isset($data['vendor_not_found'])) {
                        return FALSE;
                    } else {
                        $price = $is_price['customer_net_payable'];
                        if ($price > 0) {
                            $charges = "Rs. " . round($price, 0);
                            log_message('info', __FUNCTION__ . ' Price Sent to Customer ' . $charges);
                        } else {
                            $charges = "FREE";
                        }
                    }

                    break;
                case UPCOUNTRY_DISTANCE_CAN_NOT_CALCULATE:
                    return FALSE;
            }

            $this->send_sms_to_snapdeal_customer($appliance, $booking['booking_primary_contact_no'], $booking['user_id'], $booking['booking_id'], $smsPartner, $charges, $booking_request);
            return true;
        } else {
            $this->send_sms_to_snapdeal_customer($appliance, $booking['booking_primary_contact_no'], $booking['user_id'], $booking['booking_id'], $smsPartner, "", $booking_request);
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
     * @param String $partner
     * @param String $price
     * @return int
     */
    function send_sms_to_snapdeal_customer($appliance, $phone_number, $user_id, $booking_id, $partner, $price, $request_type) {
        log_message('info', __FUNCTION__ . ' phone_number: ' . $phone_number . ' user_id: ' . $user_id . ' booking_id: ' . $booking_id . ' partner: ' . $partner . ' appliance: ' . $appliance . ' price: ' . $price);

        $sms['tag'] = "partner_missed_call_for_installation";

        //ordering of smsData is important, it should be as per the %s in the SMS
        $sms['smsData']['service'] = $appliance;
        $sms['smsData']['request_type'] = $request_type;
        $sms['smsData']['missed_call_number'] = SNAPDEAL_MISSED_CALLED_NUMBER;
        
        /* If price exist then send sms according to that otherwise
         *  send sms by checking function get_product_free_not
         */
        if (!empty($price)) {
            $sms['smsData']['request_type_charge'] = $request_type;
            $sms['smsData']['message'] = $price;
        } else {
            //Price does not go in this SMS template
            $sms['tag'] = "missed_call_initial_prod_desc_not_found";
        }

        $sms['smsData']['partner'] = $partner;

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

    public function convert_excel_to_pdf($excel_file, $id, $s3_folder_name) {

        $output_file_excel = $excel_file;
        $target_url = PDF_CONVERTER_URL . 'pdfconverter/excel_to_pdf_converter';

        if (function_exists('curl_file_create')) {
            $cFile = curl_file_create($output_file_excel);
        } else { //
            $cFile = '@' . realpath($output_file_excel);
        }
        $post = array('bucket_dir' => BITBUCKET_DIRECTORY, 'id' => $id,
            'file_contents' => $cFile, 'auth_key' => PDF_CONVERTER_AUTH_KEY,
            's3_folder_name' => $s3_folder_name);
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $target_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $result = curl_exec($ch);
        // get HTTP response code
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpcode >= 200 && $httpcode < 300) {
            return $result;
        } else {
            
            $pathinfo = pathinfo($excel_file);
            $output_pdf_file_name = explode('.', $pathinfo['basename'])[0];
        
            $result1 = $this->My_CI->booking_utilities->convert_excel_to_pdf_paidApi($pathinfo['extension'], 'pdf', $excel_file);
            if(isset($result1->Files[0]->FileData) && $result1->Files[0]->FileSize > 0){
               
                $output_pdf_file = $pathinfo['dirname']."/".$output_pdf_file_name . ".pdf";
                
                $binary = base64_decode($result1->Files[0]->FileData);
                $file = fopen($output_pdf_file, 'wb');
                fwrite($file, $binary);
                fclose($file);
                
                $directory_pdf = $s3_folder_name."/" . $output_pdf_file_name . '.pdf';
                $this->My_CI->s3->putObjectFile($output_pdf_file, BITBUCKET_DIRECTORY, $directory_pdf, S3::ACL_PUBLIC_READ);
                
                exec("rm -rf " . escapeshellarg($output_pdf_file));
                if(file_exists($output_pdf_file)){
                    unlink($output_pdf_file);
                }
                
                return json_encode(array(
                    'response' => 'Success',
                    'response_msg' => 'PDF generated Successfully and uploaded on S3',
                    'output_pdf_file' => $output_pdf_file_name.'.pdf',
                    'bucket_dir' => BITBUCKET_DIRECTORY,
                    'id' => $id
                ));
               
            } else {
                $to = DEVELOPER_EMAIL;

                $subject = "Stag01 Server Might Be Down";
                $msg = "There are some issue while creating pdf for booking_id/invoice_id $id from stag01 server. Check the issue and fix it immediately";
                $this->My_CI->notify->sendEmail(NOREPLY_EMAIL_ID, $to, "", "", $subject, $msg, $output_file_excel,STAG_01_DOWN);
                return $result;
            }
            
        }
    }

    /**
     * @desc Checl delaer process
     * @param Array $requestData
     * @param Int $partner_id
     * @return Int
     */
    function dealer_process($requestData, $partner_id) {
        $dealer_id = $requestData['dealer_id'];
        $dealer_name = $requestData['dealer_name'];
        $dealer_phone_number = $requestData['dealer_phone_number'];
        if (empty($dealer_id)) {
            $condition = array(
                "where" => array('dealer_details.dealer_phone_number_1' => $dealer_phone_number));
            $select = " dealer_details.dealer_id";
            $dealer_mapping_status = $this->My_CI->dealer_model->get_dealer_mapping_details($condition, $select);
            if (!empty($dealer_mapping_status)) {
                $dealer_id = $dealer_mapping_status[0]['dealer_id'];
            }
        }
        if (!empty($dealer_id)) {
            $condition = array(
                "where" => array('dealer_brand_mapping.partner_id' => $partner_id,
                    'dealer_brand_mapping.dealer_id' => $dealer_id,
                    "dealer_brand_mapping.brand" => $requestData['brand'],
                    "dealer_brand_mapping.service_id" => $requestData['service_id']));
            $select = " dealer_brand_mapping.dealer_id";

            $dealer_mapping_status = $this->My_CI->dealer_model->get_dealer_mapping_details($condition, $select);
            if (empty($dealer_mapping_status)) {
                $mapping_data = array();
                $mapping_data[0]['dealer_id'] = $dealer_id;
                $mapping_data[0]['city'] = $requestData['city'];
                $mapping_data[0]['create_date'] = date("Y-m-d H:i:s");
                $mapping_data[0]['partner_id'] = $partner_id;
                $mapping_data[0]['brand'] = $requestData['brand'];
                $mapping_data[0]['service_id'] = $requestData['service_id'];

                $status = $this->My_CI->dealer_model->insert_dealer_mapping_batch($mapping_data);
            }
            return $dealer_id;
        } else if (empty($dealer_id)) {
            //make dealer details data
            $dealer_data['dealer_name'] = $dealer_name;
            $dealer_data['dealer_phone_number_1'] = $dealer_phone_number;
            $dealer_data['city'] = $requestData['city'];
            $dealer_data['state'] = $requestData['state'];
            $dealer_data['create_date'] = date('Y-m-d H:i:s');

            $dealer_id = $this->My_CI->dealer_model->insert_dealer_details($dealer_data);

            $select1 = "partner_id, service_id, brand";
            $partner_data_sp = $this->My_CI->partner_model->get_partner_specific_details(array('partner_id' => $partner_id, "active" => 1), $select1, "service_id");
            if (!empty($partner_data_sp)) {
                // don not remove $value
                for ($i = 0; $i < count($partner_data_sp); $i++) {
                    $partner_data_sp[$i]['dealer_id'] = $dealer_id;
                    $partner_data_sp[$i]['city'] = $dealer_data['city'];
                    $partner_data_sp[$i]['create_date'] = date("Y-m-d H:i:s");
                }
                $status = $this->My_CI->dealer_model->insert_dealer_mapping_batch($partner_data_sp);

                if (!empty($status)) {
                    log_message('info', __METHOD__ . "Dealer details added successfully" . print_r($dealer_data, true));

                    //do mapping for dealer and brand

                    $this->create_dealer_login($dealer_data, $dealer_id);
                } else {
                    log_message('info', __METHOD__ . "Error in inserting dealer details" . print_r($dealer_data, true));
                }
            }

            return $dealer_id;
        }
    }

    /**
     * @desc Create dealer login
     * @param Array $posData
     * @return boolean
     */
    function create_dealer_login($posData, $dealer_id) {
        log_message("info", __METHOD__);
        $login['user_id'] = $posData['dealer_phone_number_1'];
        $login['password'] = md5($posData['dealer_phone_number_1'] . "247");
        $login['clear_password'] = $posData['dealer_phone_number_1'] . "247";
        $login['entity'] = "dealer";
        $login['agent_name'] = $posData['dealer_name'];
        $login['entity_name'] = $posData['dealer_name'];
        $login['email'] = (isset($posData['dealer_email']) ? $posData['dealer_email'] : NULL);
        $login['entity_id'] = $dealer_id;
        $login['create_date'] = date('Y-m-d H:i:s');
        $this->My_CI->dealer_model->insert_entity_login($login);

        return true;
    }

    /**
     * @desc This is used to calculate buyabck overdue or credit amout for CP
     * @param Int $cp_id
     * @return Array
     */
    function get_cp_buyback_credit_debit($cp_id) {
        $where['length'] = -1;
        $invoice_amount = $this->My_CI->invoices_model->get_invoices_details(array('vendor_partner' => 'vendor', 'vendor_partner_id' => $cp_id,
            'type IN ("' . BUYBACK_VOUCHER . '", "Buyback")' => NULL, 'settle_amount' => 0), 'SUM(CASE WHEN (type_code = "B") THEN ( amount_collected_paid + `amount_paid`) WHEN (type_code = "A" ) THEN ( amount_collected_paid -`amount_paid`) END)  AS amount', 'type_code');

        $unbilled_amount = 0;
        $advance_amount = 0;

        if ($invoice_amount[0]['amount'] > 0) {

            $unbilled_amount = $invoice_amount[0]['amount'];
            if (isset($invoice_amount[1]['amount']) && $invoice_amount[1]['amount'] < 0) {
                $advance_amount = $invoice_amount[1]['amount'];
            }
        } else if ($invoice_amount[0]['amount'] < 0) {

            $advance_amount = $invoice_amount[0]['amount'];
            if (isset($invoice_amount[1]['amount']) && $invoice_amount[1]['amount'] > 0) {
                $unbilled_amount = $invoice_amount[1]['amount'];
            }
        }
        //Get auto acknowledge completed amount
        $where['where'] = array('assigned_cp_id' => $cp_id, "bb_order_details.auto_acknowledge" => 1, 'cp_invoice_id IS NULL' => NULL, 'bb_order_details.acknowledge_date IS NOT NULL' => NULL,'bb_cp_order_action.current_status != "InProcess"' => NULL);
        //$where['where_in'] = array('bb_cp_order_action.current_status' => array(_247AROUND_BB_DELIVERED, _247AROUND_BB_Damaged_STATUS), 'bb_cp_order_action.internal_status' => array(_247AROUND_BB_DELIVERED,_247AROUND_BB_Damaged_STATUS));
        $where['where_in'] = array('bb_unit_details.order_status' => array(_247AROUND_BB_DELIVERED,_247AROUND_BB_ORDER_COMPLETED_CURRENT_STATUS));
        $where['select'] =  " SUM(CASE WHEN ( bb_unit_details.cp_claimed_price > 0) THEN (round(bb_unit_details.cp_claimed_price,0)) ELSE (round(bb_unit_details.cp_basic_charge + cp_tax_charge,0)) END ) as auto_ack_charges";
        $auto_ack_charge = $this->My_CI->cp_model->get_bb_cp_order_list($where)[0]->auto_ack_charges;
        
        //Get manual acknowledge completed amount
        $where['where'] = array('assigned_cp_id' => $cp_id, "bb_order_details.auto_acknowledge" => 0, 'cp_invoice_id IS NULL' => NULL,'bb_order_details.acknowledge_date IS NOT NULL' => NULL);
        $where['select'] =  " SUM(CASE WHEN ( bb_unit_details.cp_claimed_price > 0) THEN (round(bb_unit_details.cp_claimed_price,0)) "
                . "ELSE (round(bb_unit_details.cp_basic_charge + cp_tax_charge,0)) END ) as manual_ack_charges";
        $manual_ack_charge = $this->My_CI->cp_model->get_bb_cp_order_list($where)[0]->manual_ack_charges;
        
        // Get Delivered Amount
        $where['where'] = array('assigned_cp_id' => $cp_id,'cp_invoice_id IS NULL' => NULL,'bb_order_details.acknowledge_date IS NULL AND bb_cp_order_action.current_status != "InProcess"' =>NULL);
        $where['where_in'] = array('bb_unit_details.order_status' => array(_247AROUND_BB_DELIVERED,_247AROUND_BB_ORDER_COMPLETED_CURRENT_STATUS));
        $where['select'] = "SUM(bb_unit_details.cp_basic_charge + bb_unit_details.cp_tax_charge) as cp_delivered_charge";
        $cp_delivered_charge =$this->My_CI->cp_model->get_bb_cp_order_list($where)[0]->cp_delivered_charge;
        
        // Get Intransit Amount
        $where['where'] = array('assigned_cp_id' =>$cp_id,'bb_cp_order_action.current_status' => 'Pending', 'cp_invoice_id IS NULL' => NULL,'bb_order_details.acknowledge_date IS NULL' => NULL);
        $where['where_in'] = array('bb_order_details.current_status' => array('In-Transit', 'New Item In-transit', 'Attempted'));
        $where['select'] = "SUM(bb_unit_details.cp_basic_charge + bb_unit_details.cp_tax_charge) as cp_intransit";
        $cp_intransit = $this->My_CI->cp_model->get_bb_cp_order_list($where)[0]->cp_intransit;
        
        // Get Disputed Amount
        $where['where'] = array('assigned_cp_id' => $cp_id,'cp_invoice_id IS NULL' => NULL);
        $where['where_in'] = array('bb_cp_order_action.current_status' => array('InProcess'));
        $where['select'] = "SUM(bb_unit_details.cp_basic_charge + bb_unit_details.cp_tax_charge) as cp_disputed";
        $cp_disputed = $this->My_CI->cp_model->get_bb_cp_order_list($where)[0]->cp_disputed;
        
        $total_balance = abs($advance_amount) - ( $unbilled_amount + $auto_ack_charge + $manual_ack_charge + $cp_delivered_charge + $cp_intransit + $cp_disputed);
        $cp_amount['total_balance'] = $total_balance;
        $cp_amount['cp_auto_ack'] = $auto_ack_charge;
        $cp_amount['cp_manual_ack'] = $manual_ack_charge;
        $cp_amount['cp_total_ack'] = $manual_ack_charge+$auto_ack_charge;
        $cp_amount['cp_delivered'] = $cp_delivered_charge;
        $cp_amount['cp_transit'] = $cp_intransit;
        $cp_amount['cp_disputed'] = $cp_disputed;
        $cp_amount['unbilled'] = $unbilled_amount;
        $cp_amount['advance'] = $advance_amount;
        return $cp_amount;
    }

    /**
     * @desc This function is used to verified appliance description
     * @param $appliances_details array()
     * @return $return_data array()
     */
    function verified_appliance_capacity($appliances_details) {
        switch ($appliances_details['service_id']) {
            case _247AROUND_TV_SERVICE_ID:
                $return_data = $this->verify_tv_description($appliances_details);
                break;
            case _247AROUND_WASHING_MACHINE_SERVICE_ID:
                $return_data = $this->verify_washing_machine_description($appliances_details);
                break;
            case _247AROUND_MICROWAVE_SERVICE_ID:
                $return_data = $this->verify_microwave_description($appliances_details);
                break;
            case _247AROUND_WATER_PURIFIER_SERVICE_ID:
                $return_data = $this->verify_water_purifier_description($appliances_details);
                break;
            case _247AROUND_AC_SERVICE_ID:
                $return_data = $this->verify_ac_description($appliances_details);
                break;
            case _247AROUND_REFRIGERATOR_SERVICE_ID:
                $return_data = $this->verify_refrigerator_description($appliances_details);
                break;
            case _247AROUND_GEYSER_SERVICE_ID:
                $return_data = $this->verify_geyser_description($appliances_details);
                break;
            default :
                $return_data['status'] = FALSE;
                $return_data['is_verified'] = '0';
        }

        return $return_data;
    }

    /**
     * @desc This function is used to download CSV
     * @param $CSVData(Array),$headings(Array(What should be heading for csv),$file(String(File name)))
     * @return It will download the CSV
     */
    function downloadCSV($CSVData, $headings = NULL, $file) {
        ob_clean();
        $filename = $file . '.csv';
        date_default_timezone_set('Asia/Kolkata');
        if (!empty($headings)) {
            array_unshift($CSVData, $headings);
        }
        $number_of_records = count($CSVData);
        $fp = fopen('php://output', 'w');
        for ($i = 0; $i < $number_of_records; $i++) {
            fputcsv($fp, $CSVData[$i]);
        }
        fclose($fp);
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-type: text/x-csv");
        header("Content-type: text/csv");
        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=$filename");
        //readfile(TMP_FOLDER . $filename);
        //exec("rm -rf " . escapeshellarg(TMP_FOLDER . $filename));
        exit;
    }

    function downloadExcel($data, $config) {
        if(ob_get_length() > 0) {
            ob_end_clean();
        }
        $R = new PHPReport($config);
        $R->load(array(array('id' => 'order', 'repeat' => true, 'data' => $data),));
        $output_file_excel = TMP_FOLDER . $config['template'];
        $res1 = 0;
        if (file_exists($output_file_excel)) {
            system(" chmod 777 " . $output_file_excel, $res1);
            unlink($output_file_excel);
        }
        $R->render('excel', $output_file_excel);
        if (file_exists($output_file_excel)) {
            system(" chmod 777 " . $output_file_excel, $res1);
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $config['template']);
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($output_file_excel));
            readfile($output_file_excel);
            exec("rm -rf " . escapeshellarg($output_file_excel));
            exit;
        }
    }

    /* @Desc: This function is used to _allot_source_partner_id_for_pincode
     * @params: String Pincode, brnad, default partner id(SS)
     * @return : Array
     *
     */

    function _allot_source_partner_id_for_pincode($service_id, $state, $brand, $default_partner, $api = false) {
        log_message('info', __FUNCTION__ . ' ' . $service_id, $state, $brand);
        $data = array();
        $flag = FALSE;

        $blocked_brand = $this->My_CI->partner_model->get_partner_blocklist_brand(array("partner_id" => $default_partner, "brand" => $brand,
            "service_id" => $service_id), "*");
        if (!empty($blocked_brand)) {
            if($blocked_brand[0]['whitelist'] == 1){
               log_message('info', ' Whitelist Brand ' . $brand . ' and service_id ' . $service_id. " partner Id ".$default_partner);
               $get_partner_source = $this->My_CI->partner_model->getpartner_details('bookings_sources.code', array('partners.id' => $default_partner));
               $data['partner_id'] = $default_partner;
               $data['source'] = $get_partner_source[0]['code'];
               $data['brand'] = $brand;
               $flag = FALSE;
                
            } else if($blocked_brand[0]['blacklist'] == 1){
                log_message('info', ' Blacklist Brand ' . $brand . ' and service_id ' . $service_id. " partner Id ".$default_partner);
                $data['partner_id'] = _247AROUND;
                $data['source'] = 'SB';
                $data['brand'] = "";
                $flag = FALSE;
            }
        } else {
            log_message('info', ' Not found in the vlacklist table- Brand ' . $brand . ' and service_id ' . $service_id. " partner Id ".$default_partner);
            $partner_array = $this->My_CI->partner_model->get_active_partner_id_by_service_id_brand($brand, $service_id);
            if (!empty($partner_array)) {

                foreach ($partner_array as $value) {
                    //Now getting details for each Partner
                    $filtered_partner_state = $this->My_CI->partner_model->check_activated_partner_for_state_service($state, $value['partner_id'], $service_id);
                    if ($filtered_partner_state) {
                        //Now assigning this case to Partner
                        $data['partner_id'] = $value['partner_id'];
                        $data['source'] = $partner_array[0]['code'];
                        $data['brand'] = $brand;
                        $flag = FALSE;
                    } else {
                        if ($value['partner_id'] == VIDEOTEX && !$api) {
                            return false;
                        } else {
                            $flag = TRUE;
                        }
                    }
                }
            } else {
                log_message('info', ' No Active Partner has been Found in for Brand ' . $brand . ' and service_id ' . $service_id);
                $flag = TRUE;
            }
        }

        if ($flag) {
            $get_partner_source = $this->My_CI->partner_model->getpartner_details('bookings_sources.code', array('partners.id' => $default_partner));
            $data['partner_id'] = $default_partner;
            $data['source'] = $get_partner_source[0]['code'];
            $data['brand'] = "";
        }
        
        return $data;
    }

    /**
     * @Desc: This function is used to Add details in File Uploads table
     * @params: String, String
     * @return: Void
     *
     *
     */
    public function update_file_uploads($file_name, $tmpFile, $type, $result = "", $email_message_id = "", $entity_type="", $entity_id="") {

        $data['file_type'] = $type;
        $data['file_name'] = date('d-M-Y-H-i-s') . "-" . $file_name;
        $data['agent_id'] = !empty($this->My_CI->session->userdata('id')) ? $this->My_CI->session->userdata('id') : _247AROUND_DEFAULT_AGENT;
        $data['entity_id'] = $entity_id;
        $data['entity_type'] = $entity_type;
        $data['result'] = $result;
        $data['email_message_id'] = $email_message_id;

        $insert_id = $this->My_CI->partner_model->add_file_upload_details($data);

        if (!empty($insert_id)) {
            //Logging success
            log_message('info', __FUNCTION__ . ' Added details to File Uploads ' . print_r($data, TRUE));
        } else {
            //Loggin Error
            log_message('info', __FUNCTION__ . ' Error in adding details to File Uploads ' . print_r($data, TRUE));
        }

        //Upload files to AWS
        $bucket = BITBUCKET_DIRECTORY;
        $directory_xls = "vendor-partner-docs/" . $data['file_name'];
        $this->My_CI->s3->putObjectFile($tmpFile, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);

        //Logging
        log_message('info', __FUNCTION__ . 'File has been uploaded in S3');
        return $insert_id;
    }
    
    /**
     * @desc This is used to get the balance of partner account
     * @param int $partner_id
     * @return int
     */
    function get_partner_prepaid_amount($partner_id, $getAll = FALSE) {
        //Get Partner details
        log_message("info",__METHOD__."  Prepaid Amount Request for Partner ". $partner_id);
        $partner_details = $this->My_CI->partner_model->getpartner_details("is_active, is_prepaid,prepaid_amount_limit,"
                . "grace_period_date,prepaid_notification_amount, partner_type ", array('partners.id' => $partner_id));
        
        log_message("info",__METHOD__."  Prepaid Amount Requested for Partner Data". print_r($partner_details, true));
        
        if(!empty($partner_details) && ($partner_details[0]['is_prepaid'] == 1 || !empty($getAll))){
            log_message("info",__METHOD__."  Prepaid Partner Found id ". $partner_id);
            //Get Partner invoice amout
            $invoice_amount = $this->My_CI->invoices_model->get_invoices_details(array('vendor_partner' => 'partner', 'vendor_partner_id' => $partner_id,
                'settle_amount' => 0), 'SUM(CASE WHEN (type_code = "B") THEN ( amount_collected_paid + `amount_paid`) WHEN (type_code = "A" ) '
                    . 'THEN ( amount_collected_paid -`amount_paid`) END)  AS amount');
            log_message("info",__METHOD__."  Prepaid Partner id ".$partner_id." Invoice Amount " . print_r($invoice_amount, true));
            $where = array(
                'partner_id' => $partner_id,
                'partner_invoice_id is null' => NULL,
                'create_date >= "2017-01-01" ' => NULL,
                'partner_net_payable > 0 '=> NULL,
                'booking_status IN ("' . _247AROUND_PENDING . '", "'  . _247AROUND_COMPLETED . '")' => NULL
            );
            // sum of partner payable amount whose booking is in followup, pending and completed(Invoice not generated) state.
            $service_amount = $this->My_CI->booking_model->get_unit_details($where, false, 'SUM(partner_net_payable) as amount');
            log_message("info",__METHOD__."  Prepaid Partner id ".$partner_id." Service Amount " . print_r($service_amount, true));
            
            //Get unpaid upcountry charges
            $upcountry = $this->My_CI->upcountry_model->getupcountry_for_partner_prepaid($partner_id);
            $upcountry_basic = 0;
            if(!empty($upcountry)){
                $upcountry_basic = $upcountry[0]['total_upcountry_price'];
            }
            
            $misc_select = 'SUM(miscellaneous_charges.partner_charge) as misc_charge';

            $misc = $this->My_CI->invoices_model->get_misc_charges_invoice_data($misc_select, "miscellaneous_charges.partner_invoice_id IS NULL", false, FALSE, "booking_details.partner_id", $partner_id, "partner_charge");
            $msic_charge = 0;
            if(!empty($misc)){
                $msic_charge = $misc[0]['misc_charge'];
            }
            
            // calculate final amount of partner
            $final_amount = -($invoice_amount[0]['amount'] + ($service_amount[0]['amount'] * (1 + SERVICE_TAX_RATE)) + ($upcountry_basic * (1 + SERVICE_TAX_RATE)) + $msic_charge * (1 + SERVICE_TAX_RATE));

            log_message("info", __METHOD__ . " Partner Id " . $partner_id . " Prepaid account" . $final_amount);
            $d['prepaid_amount'] = round($final_amount,0);
            // If final amount is greater than notification amount then we will display notification in the Partner CRM
            if (($partner_details[0]['is_prepaid'] == 1) & $final_amount < $partner_details[0]['prepaid_notification_amount']) {

                $d['is_notification'] = TRUE;
            } else {
                $d['is_notification'] = FALSE;
            }
            $d['prepaid_msg'] = "";
            $d['active'] = $partner_details[0]['is_active'];
            // partner is_prepaid falg shoud be 1 and prepaid_amount_limit is should be greater than partner final amount
            if (($partner_details[0]['is_prepaid'] == 1) & $partner_details[0]['prepaid_amount_limit'] > $final_amount) {
                // Display low amount msg on Partner CRM
                $d['prepaid_msg'] = PREPAID_LOW_AMOUNT_MSG_FOR_PARTNER;
                //If grace preiod is not and less than current date then partner is not able to insert new booking
                if (!empty($partner_details[0]['grace_period_date']) && (date("Y-m-d") > date("Y-m-d", strtotime($partner_details[0]['grace_period_date'])))) {
                    $d['active'] = 0;
                } else if (empty($partner_details[0]['grace_period_date'])) {
                // If grace period is empty and they have low balance then partner is not able to inert new booking
                    $d['active'] = 0;
                }
            } else {
                // permanent Deactivated Partner then display De-Activate MSG
                if($d['active'] == 0){
                    $d['is_notification'] = TRUE;
                    $d['prepaid_msg'] = PREPAID_DEACTIVATED_MSG_FOR_PARTNER;
                }

                //$d['active'] = 1;
            }
            $d['partner_type'] = $partner_details[0]['partner_type'];
            log_message("info",__METHOD__."  Prepaid Partner id ".$partner_id." Return Prepaid data " . print_r($d, true));
            return $d;
        } else {
            $d['is_notification'] = false;
            $d['active'] = 1;
            $d['prepaid_msg'] = "";
            $d["prepaid_amount"] = "";
            if(!empty($partner_details)){
                 $d['partner_type'] = $partner_details[0]['partner_type'];
            }
            log_message("info",__METHOD__."  Prepaid Partner id ".$partner_id." Return false Prepaid data " . print_r($d, true));
            return $d;
        }
    }

    /*
     * This Functiotn is used to send sf not found email to associated rm
     */

    function send_sf_not_found_email_to_rm($booking, $rm_email,$subject, $isPartner, $rm_id='') {
        
        $cc = "";
        $booking['service'] = NULL;
        
        if(!empty($rm_id)) {
            $managerData = $this->My_CI->employee_model->getemployeeManagerDetails("employee.*",array('employee_hierarchy_mapping.employee_id' => $rm_id, 'employee.groups' => 'regionalmanager'));
            
            if(!empty($managerData)) {
                $cc .= $managerData[0]['official_email'];
            }
        }
        
        $tempPartner = $this->My_CI->reusable_model->get_search_result_data("partners", "public_name", array('id' => $booking['partner_id']), NULL, NULL, NULL, NULL, NULL);
        if(!empty($booking['service_id'])){
            $booking['service'] = $this->My_CI->reusable_model->get_search_result_data("services", "services", array('id' => $booking['service_id']), NULL, NULL, NULL, NULL, NULL)[0]['services'];
        }
        $booking['partner_name'] = NULL;
        if (!empty($tempPartner)) {
            $booking['partner_name'] = $tempPartner[0]['public_name'];
        }
        $message = $this->My_CI->load->view('employee/sf_not_found_email_template', $booking, true);
        $this->My_CI->notify->sendEmail(NOREPLY_EMAIL_ID, $rm_email, $cc, "", $subject, $message, "",SF_NOT_FOUND);
        if(!empty($isPartner)){
            $this->send_mail_to_partner_sf_not_exist($booking, $subject, SF_NOT_FOUND);
        }
    }
    
    function send_mail_to_partner_sf_not_exist($booking, $subject, $templatetag){
        $partner_email = $this->get_partner_email_constant();
        if(isset($partner_email[$booking['partner_id']])){
            $to = $partner_email[$booking['partner_id']];
            $cc = ANUJ_EMAIL_ID;
            $booking['jeeves_not_assign'] = true;
            $message = $this->My_CI->load->view('employee/sf_not_found_email_template', $booking, true);
            $this->My_CI->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, "", $subject, $message, "",$templatetag);
        }
    }
    
    function get_partner_email_constant(){
        return array(
            JEEVES_ID => VINESH_FLIPKART_EMAIL
       );
    }

    /*
     * This Functiotn is used to map rm to pincode, for which SF not found
     * if pincode does'nt have any rm then an email will goes to nitin
     * @input - An associative array with keys(booking_id,pincode,city,applianceID)
     */

    function sf_not_exist_for_pincode($booking) {
        if(!isset($booking['order_id'])){
            $booking['order_id'] = 'Not_Generated';
        }
        $notFoundSfArray = array('booking_id' => $booking['booking_id'], 'pincode' => $booking['booking_pincode'], 'city' => $booking['city'], 'service_id' => $booking['service_id']);
        $pincode =  $booking['booking_pincode'];
        $result = $this->My_CI->reusable_model->get_rm_for_pincode($notFoundSfArray['pincode']);
        
        if (!empty($result)) {
            $notFoundSfArray['rm_id'] = $result[0]['rm_id'];
            $notFoundSfArray['state'] = $result[0]['state_id'];
            
            $query = $this->My_CI->reusable_model->get_search_query("employee", "official_email", array('id' => $result[0]['rm_id'],'active' => 1), NULL, NULL, NULL, NULL, NULL);
            $rm_email = $query->result_array();
            if (empty($rm_email)) {
                $rm_email[0]['official_email'] = NULL;
            }
            
            $subject = "SF Not Exist in the Pincode " . $pincode;
            $this->send_sf_not_found_email_to_rm($booking, $rm_email[0]['official_email'],$subject, TRUE, $result[0]['rm_id']);
        }else{
            $pincodeJsonData = $this->google_map_address_api($pincode);
            $pincodeArray = json_decode($pincodeJsonData,true);
            if($pincodeArray['status'] == 'OK'){
                $addressCompLength = count($pincodeArray['results'][0]['address_components']);
                $country = $pincodeArray['results']['0']['address_components'][$addressCompLength-1]['long_name'];
                 log_message('info', __METHOD__ . "=>Country" . $country ."Pincode =".$pincode);
                if($country == 'India'){
                        $state = $pincodeArray['results']['0']['address_components'][$addressCompLength-2]['long_name'];
                        $city = $pincodeArray['results']['0']['address_components'][$addressCompLength-3]['long_name'];
                        if(!is_null($pincode) && !is_null($state) && !is_null($city))
                            $this->process_if_pincode_valid($pincode,$state,$city);
                       //Update State and City in sf_not_exist_booking_details
                        $resultTemp = $this->My_CI->reusable_model->get_rm_for_pincode($pincode);
                        //$notFoundSfArray['rm_id'] = $resultTemp[0]['rm_id'];
                        $notFoundSfArray['state'] = $resultTemp[0]['state_id'];
                        $notFoundSfArray['city'] = $city;
                        $notFoundSfArray['is_pincode_valid'] = 1;
                        $this->My_CI->vendor_model->update_not_found_sf_table(array("pincode"=>$pincode),$notFoundSfArray);
                   }
                   else{
                        $this->My_CI->vendor_model->update_not_found_sf_table(array("pincode"=>$pincode),array("is_pincode_valid" => 0));
                   }
            }
            else if($pincodeArray['status'] == 'ZERO_RESULTS'){
                log_message('info', __METHOD__ . "=>ZERO_RESULT"."Pincode =".$pincode);
               }
            else{
                    $template = $this->My_CI->booking_model->get_booking_email_template("google_api_fail_for_address");
                    if (!empty($template)) {  
                        //Sending Mail
                        $email['pincode'] = $notFoundSfArray['pincode'];
                        $email['response'] = $pincodeJsonData;
                        $emailBody = vsprintf($template[0], $email);
                        $subjectBody = $template[4];
                        $this->My_CI->notify->sendEmail(NOREPLY_EMAIL_ID, DEVELOPER_EMAIL,'', '', $subjectBody, $emailBody, "",'google_api_fail_for_address', "", $booking['booking_id']);
                        //Logging
                        log_message('info', " API Fail Mail Send successfully" . $emailBody);
                    } else {
                        //Logging Error Message
                        log_message('info', " Error in Getting Email Template for Escalation Mail");
                    }
            }
        }
        if (array_key_exists('partner_id', $booking)) {
            $notFoundSfArray['partner_id'] = $booking['partner_id'];
        }
        if(isset($notFoundSfArray['state']) && !is_null($notFoundSfArray['state'])) {
            $this->My_CI->vendor_model->insert_booking_details_sf_not_exist($notFoundSfArray);
        }
        else {
            //Logging Error Message
            log_message('info', " Error while Insertion into table sf_not_exist_booking_details");
        }
    }

    /**
     * @desc This function is used to verify television appliance data
     * @param $appliances_details array()
     * @return $new_appliance_details array()
     */
    function verify_tv_description($appliances_details) {
        $match = array();
        $new_appliance_details = array();

        preg_match('/[0-9]+/', $appliances_details['capacity'], $match);
        if (!empty($match) && (stripos($appliances_details['description'], $match[0]) !== False) && (stripos($appliances_details['description'], $appliances_details['brand']) !== False)) {
            $new_appliance_details['category'] = $appliances_details['category'];
            $new_appliance_details['capacity'] = $appliances_details['capacity'];
            $new_appliance_details['brand'] = $appliances_details['brand'];
            $new_appliance_details['status'] = TRUE;
            $new_appliance_details['is_verified'] = '1';
        } else {
            $new_appliance_details['status'] = FALSE;
            $new_appliance_details['is_verified'] = '0';
        }

        return $new_appliance_details;
    }

    /**
     * @desc This function is used to verify washing_machine appliance data
     * @param $appliances_details array()
     * @return $new_appliance_details array()
     */
    function verify_washing_machine_description($appliances_details) {
        $new_appliance_details = array();
        if (((stripos($appliances_details['description'], 'semiautomatic') !== False) || (stripos($appliances_details['description'], 'semi automatic') !== False) ) && (stripos($appliances_details['description'], $appliances_details['brand']) !== False)) {
            $new_appliance_details['category'] = 'Semiautomatic';
            $new_appliance_details['capacity'] = $appliances_details['capacity'];
            $new_appliance_details['brand'] = $appliances_details['brand'];
            $new_appliance_details['status'] = TRUE;
            $new_appliance_details['is_verified'] = '1';
        } else if (((stripos($appliances_details['description'], 'fullyautomatic') !== False) || (stripos($appliances_details['description'], 'Fully Automatic') !== False) || (stripos($appliances_details['description'], 'Fully Automatic') !== False)) && (stripos($appliances_details['description'], $appliances_details['brand']) !== False)) {
            if (stripos($appliances_details['description'], 'front') !== False) {
                $new_appliance_details['category'] = 'Front Load';
                $new_appliance_details['capacity'] = $appliances_details['capacity'];
                $new_appliance_details['brand'] = $appliances_details['brand'];
                $new_appliance_details['status'] = TRUE;
                $new_appliance_details['is_verified'] = '1';
            } else if (stripos($appliances_details['description'], 'top') !== False) {
                $new_appliance_details['category'] = 'Top Load';
                $new_appliance_details['capacity'] = $appliances_details['capacity'];
                $new_appliance_details['brand'] = $appliances_details['brand'];
                $new_appliance_details['status'] = TRUE;
                $new_appliance_details['is_verified'] = '1';
            } else {
                $new_appliance_details['status'] = FALSE;
                $new_appliance_details['is_verified'] = '0';
            }
        } else {
            $new_appliance_details['status'] = FALSE;
            $new_appliance_details['is_verified'] = '0';
        }

        return $new_appliance_details;
    }

    /*
     * This Function use to update sf_not_found_pincode table
     * When we upload any new pincode and that pincode with same service_id exist in sf not found table, then this will update its active flag
     */

    function update_pincode_not_found_sf_table($pincodeServiceArray) {
        $pincodeStrring = "";
        foreach ($pincodeServiceArray as $key => $values) {
            $pincodeArray['(pincode=' . $values['Pincode'] . ' AND service_id=' . $values['Appliance_ID'] . ')'] = NULL;
            $pincodeStrring .= '(pincode=' . $values['Pincode'] . ' AND service_id=' . $values['Appliance_ID'] . ')|||';
        }
        log_message('info', __FUNCTION__ . 'Deactivate following Combination From sf not found table. ' . print_r($pincodeArray, TRUE));
        $this->My_CI->vendor_model->update_not_found_sf_table($pincodeArray, array('active_flag' => 0));
    }
    
    /*
     * Pass the file name to function and it will return file reader version for excel file
     */

    function get_excel_reader_version($fileName) {
        $pathinfo = pathinfo($fileName);
        if ($pathinfo['extension'] == 'xlsx') {
            $readerVersion = 'Excel2007';
        } else {
            $readerVersion = 'Excel5';
        }
        return $readerVersion;
    }

    /*
     * This Function convert excel data into array, 1st row of excel data will be keys of returning array
     * @input - filePath and reader Version and index of sheet in case of multiple sheet excel
     */

    function excel_to_Array_converter($file, $readerVersion = NULL, $sheetIndex = NULL) {
        if (!$sheetIndex) {
            $sheetIndex = 0;
        }
        $readerVersion1 = $this->get_excel_reader_version($file['file']['name']);
        $finalExcelDataArray = array();
        $objReader = PHPExcel_IOFactory::createReader($readerVersion1);
        $objPHPExcel = $objReader->load($file['file']['tmp_name']);
        $sheet = $objPHPExcel->getSheet($sheetIndex);
        $highestRow = $sheet->getHighestDataRow();
        $highestColumn = $sheet->getHighestDataColumn();
        $headings = $sheet->rangeToArray('A1:' . $highestColumn . 1, NULL, TRUE, FALSE);
        $heading = str_replace(array("/", "(", ")", " ", "."), "", $headings[0]);
        $newHeading = str_replace(array(" "), "_", $heading);
        $excelDataArray = array();
        for ($i = 2; $i <= $highestRow; $i++) {
            $excelDataArray = $sheet->rangeToArray('A' . $i . ':' . $highestColumn . $i, NULL, TRUE, FALSE);
            if(array_filter($excelDataArray[0])) {
                foreach ($excelDataArray[0] as $key => $data) {
                    $excelAssociatedArray[$newHeading[$key]] = trim($data);
                }
                $finalExcelDataArray[] = $excelAssociatedArray;
            }
        }
        return $finalExcelDataArray;
    }

    /*
     * @esc: This method upload invoice image OR panel image to S3
     * @param _FILE $file
     * @return boolean|string
     */

    public function upload_file_to_s3($file, $type, $allowedExts, $pic_type_name, $s3_directory, $post_name) {
        log_message('info', __FUNCTION__ . " Enterring ");

        $MB = 1048576;
        $temp = explode(".", $file['name']);
        $extension = end($temp);
        //$filename = prev($temp);

        if ($file["name"] != null) {
            if (($file["size"] < 2 * $MB) && in_array($extension, $allowedExts)) {
                if ($file["error"] > 0) {

                    $this->My_CI->form_validation->set_message('upload_file_to_s3', $file["error"]);
                } else {
                    $pic = str_replace(' ', '-', $pic_type_name);
                    $picName = $type . rand(10, 10000) . $pic . "." . $extension;
                    $_POST[$post_name] = $picName;
                    $bucket = BITBUCKET_DIRECTORY;

                    $directory = $s3_directory . "/" . $picName;
                    $this->My_CI->s3->putObjectFile($file["tmp_name"], $bucket, $directory, S3::ACL_PUBLIC_READ);

                    return $picName;
                }
            } else {
                $this->My_CI->form_validation->set_message('upload_file_to_s3', 'File size or file type is not supported. Allowed extentions are "png", "jpg", "jpeg" and "pdf". '
                        . 'Maximum file size is 2 MB.');
                return FALSE;
            }
        } else {

            $this->My_CI->form_validation->set_message('upload_file_to_s3', 'File size or file type is not supported. Allowed extentions are "png", "jpg", "jpeg" and "pdf". '
                    . 'Maximum file size is 2 MB.');
            return FALSE;
        }
        log_message('info', __FUNCTION__ . " Exit ");
    }

    /**
     * @Desc: This function is used to check if user name is empty or not
     * if user name is not empty then return username otherwise check if email is not
     * empty.if email is empty then return mobile number as username otherwise return email as username
     * @params: String
     * @return: void
     *
     */
    public function is_user_name_empty($userName, $userEmail, $userContactNo) {
        if (empty($userName)) {
            if (empty($userEmail)) {
                $user_name = $userContactNo;
            } else {
                $user_name = $userEmail;
            }
        } else {
            $user_name = $userName;
        }

        return $user_name;
    }

    /**
     * @desc This function is used to verify microwave appliance data
     * @param $appliances_details array()
     * @return $new_appliance_details array()
     */
    function verify_microwave_description($appliances_details) {
        $new_appliance_details = array();
        if ((stripos($appliances_details['description'], $appliances_details['brand']) !== False)) {
            $new_appliance_details['category'] = $appliances_details['category'];
            $new_appliance_details['capacity'] = $appliances_details['capacity'];
            $new_appliance_details['brand'] = $appliances_details['brand'];
            $new_appliance_details['status'] = TRUE;
            $new_appliance_details['is_verified'] = '1';
        } else {
            $new_appliance_details['status'] = FALSE;
            $new_appliance_details['is_verified'] = '0';
        }

        return $new_appliance_details;
    }

    /**
     * @desc This function is used to verify water_purifier appliance data
     * @param $appliances_details array()
     * @return $new_appliance_details array()
     */
    function verify_water_purifier_description($appliances_details) {
        $new_appliance_details = array();
        if ((stripos($appliances_details['description'], $appliances_details['brand']) !== False)) {
            $new_appliance_details['category'] = $appliances_details['category'];
            $new_appliance_details['capacity'] = $appliances_details['capacity'];
            $new_appliance_details['brand'] = $appliances_details['brand'];
            $new_appliance_details['status'] = TRUE;
            $new_appliance_details['is_verified'] = '1';
        } else {
            $new_appliance_details['status'] = FALSE;
            $new_appliance_details['is_verified'] = '0';
        }

        return $new_appliance_details;
    }

    /**
     * @desc This function is used to verify air conditioner appliance data
     * check if brand and category exist in the description
     * if exist then check for the right capacity and set verified flag to 1
     * otherwise set verified flag to 0
     * @param $appliances_details array()
     * @return $new_appliance_details array()
     */
    function verify_ac_description($appliances_details) {
        $new_appliance_details = array();
        if ((stripos($appliances_details['description'], $appliances_details['capacity']) !== False) && (stripos($appliances_details['description'], $appliances_details['brand']) !== False) && (stripos($appliances_details['description'], explode('-', $appliances_details['category'])[1]) !== False)) {
            $new_appliance_details['category'] = $appliances_details['category'];
            $new_appliance_details['capacity'] = $appliances_details['capacity'];
            $new_appliance_details['brand'] = $appliances_details['brand'];
            $new_appliance_details['status'] = TRUE;
            $new_appliance_details['is_verified'] = '1';
        } else {
            $new_appliance_details['status'] = FALSE;
            $new_appliance_details['is_verified'] = '0';
        }

        return $new_appliance_details;
    }

    /**
     * @desc This function is used to verify refrigerator appliance data
     * @param $appliances_details array()
     * @return $new_appliance_details array()
     */
    function verify_refrigerator_description($appliances_details) {
        $new_appliance_details = array();
        $flag = FALSE;
        $category = explode(" ", $appliances_details['category']);

        //extract window/split word from category
        array_pop($category);

        /* check if brand and category exist in the description
         * if exist then check for the right capacity and set verified flag to 1
         * otherwise set verified flag to 0
         */
        if ((stripos($appliances_details['description'], $category[0]) !== False) && (stripos($appliances_details['description'], $appliances_details['brand']) !== False)) {
            $match = array();
            //extract integer before words ltr,Ltr,LTR,ltrs,Ltrs,LTRS,L,l
            preg_match('/(\b(\d*\.?\d+) Ltr)|(\b(\d*\.?\d+) L)/i', $appliances_details['description'], $match);
            if (!empty($match)) {
                $capacity = explode(" ", $match[0])[0];
                if ($capacity >= 0 && $capacity <= 250) {
                    $new_appliance_details['capacity'] = "0-250 Ltr";
                    $flag = TRUE;
                } else if ($capacity > 250 && $capacity <= 450) {
                    $new_appliance_details['capacity'] = "250-450 Ltr";
                    $flag = TRUE;
                } else if ($capacity > 450 && $capacity <= 10000) {
                    $new_appliance_details['capacity'] = "450-10000 Ltr";
                    $flag = TRUE;
                } else {
                    $flag = FALSE;
                }
            } else {
                $flag = FALSE;
            }
        } else {
            $flag = FALSE;
        }

        if ($flag) {
            $new_appliance_details['category'] = $appliances_details['category'];
            $new_appliance_details['brand'] = $appliances_details['brand'];
            $new_appliance_details['status'] = TRUE;
            $new_appliance_details['is_verified'] = '1';
        } else {
            $new_appliance_details['status'] = FALSE;
            $new_appliance_details['is_verified'] = '0';
        }

        return $new_appliance_details;
    }

    /**
     * @desc This function is used to verify geyser appliance data
     * @param $appliances_details array()
     * @return $new_appliance_details array()
     */
    function verify_geyser_description($appliances_details) {
        $new_appliance_details = array();
        $flag = FALSE;
        //extract geyser word from category
        $category = explode("-", $appliances_details['category']);
        if (isset($category[1])) {
            array_pop($category);
        }

        /* check if brand and category exist in the description
         * if exist then check for the right capacity and set verified flag to 1
         * otherwise set verified flag to 0
         */
        if ((stripos($appliances_details['description'], $category[0]) !== False) && (stripos($appliances_details['description'], $appliances_details['brand']) !== False)) {
            $match = array();
            //extract integer before words ltr,Ltr,LTR,ltrs,Ltrs,LTRS,L,l
            preg_match('/(\b(\d*\.?\d+) Ltr)|(\b(\d*\.?\d+) L)/i', $appliances_details['description'], $match);
            if (!empty($match)) {
                $capacity = explode(" ", $match[0])[0];
                if ($capacity >= 0 && $capacity <= 15) {
                    $new_appliance_details['capacity'] = "15 Ltr and Below";
                    $flag = TRUE;
                } else if ($capacity > 15) {
                    $new_appliance_details['capacity'] = "16 Ltr and Above";
                    $flag = TRUE;
                } else {
                    $flag = FALSE;
                }
            } else {
                $flag = FALSE;
            }
        } else {
            $flag = FALSE;
        }

        if ($flag) {
            $new_appliance_details['category'] = $appliances_details['category'];
            $new_appliance_details['brand'] = $appliances_details['brand'];
            $new_appliance_details['status'] = TRUE;
            $new_appliance_details['is_verified'] = '1';
        } else {
            $new_appliance_details['status'] = FALSE;
            $new_appliance_details['is_verified'] = '0';
        }

        return $new_appliance_details;
    }
    /*
     * This Function is used to perform update or insert  action on the basis of input type over bank details table
     */

    function update_insert_bank_account_details($bankDetailsArray) {
        if(!$bankDetailsArray['cancelled_cheque_file']){
            unset($bankDetailsArray['cancelled_cheque_file']);
        }
        if($bankDetailsArray['entity_id']){
            $where['entity_id'] = $bankDetailsArray['entity_id'];
            $where['entity_type'] = $bankDetailsArray['entity_type'];
            $affectedRows = $this->My_CI->reusable_model->update_table("account_holders_bank_details",$bankDetailsArray,$where);
            if($affectedRows == 0){
                if (array_key_exists('bank_name', $bankDetailsArray) || array_key_exists('account_type', $bankDetailsArray) || array_key_exists('bank_account', $bankDetailsArray) || array_key_exists('ifsc_code', $bankDetailsArray) || array_key_exists('cancelled_cheque_file', $bankDetailsArray) || array_key_exists('beneficiary_name', $bankDetailsArray) || array_key_exists('beneficiary_name', $bankDetailsArray)) {
                    $affectedRows = $this->My_CI->reusable_model->insert_into_table('account_holders_bank_details', $bankDetailsArray);
                }
            } 
            return $affectedRows;
        }
    }

    /**
     * @desc Return Account Manager ID
     * @param int $partner_id
     * @return Email ID
     */
    function get_am_data($partner_id) {
        $data = [];
        /*$am_id = $this->My_CI->partner_model->getpartner_details('account_manager_id', array('partners.id' => trim($partner_id)));
        if (!empty($am_id)) {
            $data = $this->My_CI->employee_model->getemployeefromid($am_id[0]['account_manager_id']);
        }*/
        $am_id = $this->My_CI->partner_model->getpartner_data("group_concat(distinct agent_filters.agent_id) as account_manager_id", 
                    array('partners.id' => trim($partner_id)),"",0,1,1,"partners.id");
        if (!empty($am_id[0]['account_manager_id'])) {
            $data = $this->My_CI->employee_model->getemployeeMailFromID($am_id[0]['account_manager_id']);
        }
        return $data;
    }

    /**
     * @desc This function is used to generate the excel data and return generated excel file path
     * @param string $template
     * @param string $download_file_name
     * @param array $data
     * @return string $output_file_excel
     */
    function generate_excel_data($template, $download_file_name, $data,$repeat = true,$cell = false, $imagePath = false) {


        // directory
        $templateDir = __DIR__ . "/../controllers/excel-templates/";
        $config = array(
            'template' => $template,
            'templateDir' => $templateDir
        );

        //load template
       if(ob_get_length() > 0) {
            ob_end_clean();
        }
        $R = new PHPReport($config);

        $R->load(array(
            array(
                'id' => 'excel_data',
                'repeat' => false,
                'data' => $data['excel_data'],
            ),
            array(
                'id' => 'excel_data_line_item',
                'repeat' => $repeat,
                'data' => $data['excel_data_line_item'],
            )
                )
        );

        $output_file_excel = TMP_FOLDER . $download_file_name . ".xlsx";

        $res1 = 0;
        if (file_exists($output_file_excel)) {

            system(" chmod 777 " . $output_file_excel, $res1);
            unlink($output_file_excel);
        }

        $R->render('excel', $output_file_excel,$cell,$imagePath);

        return $output_file_excel;
    }

    /**
     * @desc This function is used to extract the zip file
     * @param string $file_path
     * @param string $path_to_extract
     * @return string $response
     */
    function extract_zip_files($file_path, $path_to_extract) {
        $zip = new ZipArchive;
        $res = $zip->open($file_path);
        if ($res === TRUE) {
            // get the zipped file name
            $zip->extractTo($path_to_extract);
            $response['file_name'] = $zip->getNameIndex(0);
            $res1 = 0;
            system(" chmod 777 " . TMP_FOLDER . $response['file_name'], $res1);
            $zip->close();

            $response['status'] = true;
        } else {
            $response['status'] = false;
            switch ($res) {
                case ZipArchive::ER_EXISTS:
                    $response['msg'] = "File already exists.";
                    break;

                case ZipArchive::ER_INCONS:
                    $response['msg'] = "Zip archive inconsistent.";
                    break;

                case ZipArchive::ER_MEMORY:
                    $response['msg'] = "Malloc failure.";
                    break;

                case ZipArchive::ER_NOENT:
                    $response['msg'] = "No such file.";
                    break;

                case ZipArchive::ER_NOZIP:
                    $response['msg'] = "Not a zip archive.";
                    break;

                case ZipArchive::ER_OPEN:
                    $response['msg'] = "Can't open file.";
                    break;

                case ZipArchive::ER_READ:
                    $response['msg'] = "Read error.";
                    break;

                case ZipArchive::ER_SEEK:
                    $response['msg'] = "Seek error.";
                    break;

                default:
                    $response['msg'] = "Unknow (Code)";
                    break;
            }
        }

        return $response;
    }

    function table_updated_history_view($orignalTable, $triggeredTable, $entityID) {
        $finalData = array();
        $joinArray = array("employee" => "employee.id=" . $triggeredTable . ".agent_id");
        $triggeredTableData = $this->My_CI->reusable_model->get_search_result_data($triggeredTable, $triggeredTable . ".*,employee.full_name", array($triggeredTable.".id" => $entityID), $joinArray, NULL,NULL, NULL, NULL);
        $orignalTableData = $this->My_CI->reusable_model->get_search_result_data($orignalTable, "*", array($orignalTable.".id" => $entityID), NULL, NULL, NULL, NULL, NULL);
        array_unshift($triggeredTableData,$orignalTableData[0]);
        if(count($triggeredTableData)>1){
            foreach ($triggeredTableData as $index => $data) {
                if($index < count($triggeredTableData)-1){
                    $finalData['data'][] = array_keys(array_diff_assoc($data,$triggeredTableData[$index+1]));
                    $finalData['update_date'][] = $triggeredTableData[$index+1]['update_date'];
                    $finalData['updated_by'][] = $triggeredTableData[$index+1]['full_name'];
                }
            }
        }
        return $finalData;
    }

// function send_completed_booking_email_to_customer($completedBookingsID){
//      log_message('info', __FUNCTION__ . ' => Completed booking Email Send Function Entry');
//        $completedBookingsData = $this->My_CI->reusable_model->get_search_result_data("booking_details","booking_details.booking_id,users.name,users.user_email,partners.public_name as partner,booking_details.booking_date as booking_date",NULL,array('partners'=>'partners.id=booking_details.partner_id','users'=>'booking_details.user_id=users.user_id'),NULL,NULL,array('booking_id'=>$completedBookingsID),NULL);
//        foreach($completedBookingsData as $data){
//        $emailBasicDataArray['to'] = $data['user_email'];
//        $emailBasicDataArray['subject'] = "Completed Booking ".$data['booking_id'];
//        $emailBasicDataArray['from'] = NOREPLY_EMAIL_ID;
//        $emailBasicDataArray['fromName'] = "247around Team";
//        $emailTemplateDataArray['templateId'] = COMPLETED_BOOKING_CUSTOMER_TEMPLATE;
//        unset($data['user_email']);
//        $emailTemplateDataArray['dynamicParams'] = $data;
//        $this->My_CI->send_grid_api->send_email_using_send_grid_templates($emailBasicDataArray, $emailTemplateDataArray);
//        log_message('info', __FUNCTION__ . ' => Email Sent');
//        log_message('info', __METHOD__ . "=> Email Basic Data" . print_r($emailBasicDataArray, true));
//       log_message('info', __METHOD__ . "=> Email Template Data " . print_r($emailTemplateDataArray, true));
//            }
//    }

    /**
     * @desc This function is used to update the inventory stock
     * @param array $data
     * @return bookean $flag
     */
    function process_inventory_stocks($data, $requested_inventory_id = false) {
        log_message("info", __FUNCTION__ . " process inventory update entering..." . print_r($data, true));
        $flag = FALSE;
        $is_process = FALSE;

        if ($data['receiver_entity_type'] === _247AROUND_SF_STRING && !isset($data['is_wh'])) {
            //check if sf is working with brackets with 247around
            $is_brackets = $this->My_CI->vendor_model->getVendorDetails('brackets_flag', array('id' => $data['receiver_entity_id']))[0]['brackets_flag'];
            if (!empty($is_brackets)) {
                $is_process = TRUE;
                log_message("info","sf id: ".$data['receiver_entity_id']." is working with brackets with 247around");
            } else {
                $is_process = FALSE;
                log_message("info","sf id: ".$data['receiver_entity_id']." is not working with brackets with 247around");
            }
        } else {
            $is_process = TRUE;
        }

        if ($is_process) {
            /* check if part is exist in the master inventory table
             * if exist then get the id of that part and use that id for further process
             */
            
            if(isset($data['inventory_id'])){
                $is_part_exist = array( 0 => array('inventory_id' => $data['inventory_id']));
            }else{
                $is_part_exist = $this->My_CI->reusable_model->get_search_query('inventory_master_list', 'inventory_master_list.inventory_id', array('part_number' => $data['part_number']), NULL, NULL, NULL, NULL, NULL)->result_array();
            }            
            if (!empty($is_part_exist)) {
                /* check if entity is exist in the inventory stock table
                 * if exist then get update the stock
                 * else insert into the table
                 */
                if(isset($data['is_wh']) && !isset($data['is_cancel_part'])){
                    $is_entity_exist = $this->My_CI->reusable_model->get_search_query('inventory_stocks', 'inventory_stocks.id,inventory_stocks.stock, pending_request_count', array('entity_id' => $data['sender_entity_id'], 'entity_type' => $data['sender_entity_type'], 'inventory_id' => $is_part_exist[0]['inventory_id']), NULL, NULL, NULL, NULL, NULL)->result_array();
                }else{
                    $is_entity_exist = $this->My_CI->reusable_model->get_search_query('inventory_stocks', 'inventory_stocks.id,inventory_stocks.stock, pending_request_count', array('entity_id' => $data['receiver_entity_id'], 'entity_type' => $data['receiver_entity_type'], 'inventory_id' => $is_part_exist[0]['inventory_id']), NULL, NULL, NULL, NULL, NULL)->result_array();
                }
                if (!empty($is_entity_exist)) {
                    //if stock goes negative then do not update stock
                    $updated_stock = $is_entity_exist[0]['stock'] + $data['stock'];
                    if($updated_stock >= 0){
                        $stock = "stock + '" . $data['stock'] . "'";
                        if(isset($data['is_wh']) && !empty($requested_inventory_id)){
                            if($is_entity_exist[0]['pending_request_count'] > 0){
                                $this->My_CI->inventory_model->update_pending_inventory_stock_request($data['sender_entity_type'], $data['sender_entity_id'], $requested_inventory_id, -1);
                            }
                        }
                        $update_stocks = $this->My_CI->inventory_model->update_inventory_stock(array('id' => $is_entity_exist[0]['id']), $stock);
                        if ($update_stocks) {
                            log_message("info", __FUNCTION__ . " Stocks has been updated successfully");
                            $flag = TRUE;
                        } else {
                            log_message("info", __FUNCTION__ . " Error in updating stocks");
                        }
                    }else{
                        log_message('info','inventory id '. $is_part_exist[0]['inventory_id'] . ' details for which stock not found ' .print_r($data,true) );
                    }
                } else {
                    $insert_data['entity_id'] = isset($data['is_wh'])?$data['sender_entity_id']:$data['receiver_entity_id'];
                    $insert_data['entity_type'] = isset($data['is_wh'])?$data['sender_entity_type']:$data['receiver_entity_type'];
                    $insert_data['inventory_id'] = $is_part_exist[0]['inventory_id'];
                    $insert_data['stock'] = $data['stock'];
                    $insert_data['create_date'] = date('Y-m-d H:i:s');

                    $insert_id = $this->My_CI->inventory_model->insert_inventory_stock($insert_data);
                    if (!empty($insert_id)) {
                        log_message("info", __FUNCTION__ . " Stocks has been inserted successfully" . print_r($insert_data, true));
                        $flag = TRUE;
                    } else {
                        log_message("info", __FUNCTION__ . " Error in inserting stocks" . print_r($insert_data, true));
                    }
                }

                //insert inventory details into the inventory ledger table
                if ($flag) {
                    $insert_ledger_data = array('receiver_entity_id' => $data['receiver_entity_id'],
                        'receiver_entity_type' => $data['receiver_entity_type'],
                        'quantity' => $data['stock'],
                        'inventory_id' => $is_part_exist[0]['inventory_id']
                    );
                    if (isset($data['sender_entity_id']) && isset($data['sender_entity_type'])) {
                        $insert_ledger_data['sender_entity_id'] = $data['sender_entity_id'];
                        $insert_ledger_data['sender_entity_type'] = $data['sender_entity_type'];
                    }

                    if (isset($data['agent_id']) && isset($data['agent_type'])) {
                        $insert_ledger_data['agent_id'] = $data['agent_id'];
                        $insert_ledger_data['agent_type'] = $data['agent_type'];
                    }

                    if (isset($data['order_id'])) {
                        $insert_ledger_data['order_id'] = $data['order_id'];
                    }

                    if (isset($data['booking_id'])) {
                        $insert_ledger_data['booking_id'] = $data['booking_id'];
                    }

                    if (isset($data['invoice_id'])) {
                        $insert_ledger_data['invoice_id'] = $data['invoice_id'];
                    }

                    $insert_id = $this->My_CI->inventory_model->insert_inventory_ledger($insert_ledger_data);
                    if (!empty($insert_id)) {
                        log_message("info", __FUNCTION__ . " Inventory Ledger has been inserted successfully" . print_r($insert_ledger_data, true));
                        $flag = TRUE;
                        if (isset($data['booking_id']) && !empty($data['booking_id'])) {
                            $update = $this->My_CI->booking_model->update_booking_unit_details_by_any(array('booking_id' => $data['booking_id'], 'price_tags like "' . _247AROUND_WALL_MOUNT__PRICE_TAG . '"' => NULL), array('inventory_id' => $is_part_exist[0]['inventory_id']));
                            if (!empty($update)) {
                                log_message("info", "Inventory id updated successfully in booking unit details for booking_id " . $data['booking_id']);
                            } else {
                                log_message("info", "error in updating inventory_id in unit details for booking_id " . $data['booking_id']);
                            }
                        }
                    } else {
                        log_message("info", __FUNCTION__ . " Error in inserting inventory ledger details" . print_r($insert_ledger_data, true));
                        $flag = FALSE;
                    }
                } else {
                    log_message("info", __FUNCTION__ . " Error in updating inventory " . print_r($data, true));
                }
            } else {
                log_message("info", __FUNCTION__ . " Error in updating inventory. Part number does not exist in the inventory_master_list table" . print_r($data, true));
            }
        }

        return $flag;
    }
/*
 * This Is a helper function to create Navigation and upload in cache
 * This Function is use to get nav data and convert data into structure Format
 * @input - Navigation Type (eg- main_nav,right_nav)
 */
    private function get_main_nav_data($nav_type,$entity_type){
        $where = array("header_navigation.groups LIKE '%".$this->My_CI->session->userdata('user_group')."%'"=>NULL,"header_navigation.is_active"=>"1");
        $where["header_navigation.nav_type"]=$nav_type;
        $where["header_navigation.entity_type"]=$entity_type;
        $parentArray = $structuredData=$navFlowArray=array();
        $data= $this->My_CI->reusable_model->get_search_result_data("header_navigation","header_navigation.*,GROUP_CONCAT(p_m.title) as parent_name",$where,
                array("header_navigation p_m"=>"FIND_IN_SET(p_m.id,header_navigation.parent_ids)"),NULL,array("level"=>"ASC"),NULL,array("header_navigation p_m"=>"LEFT"),array('header_navigation.id'));
         foreach($data as $navData){
            $structuredData["id_".$navData['id']]['title'] = $navData['title'];
            $structuredData["id_".$navData['id']]['title_icon'] = $navData['title_icon'];
            $structuredData["id_".$navData['id']]['link'] = $navData['link'];
            $structuredData["id_".$navData['id']]['level'] = $navData['level'];
            $structuredData["id_".$navData['id']]['parent_ids'] = $navData['parent_ids'];
            $structuredData["id_".$navData['id']]['groups'] = $navData['groups'];
            $structuredData["id_".$navData['id']]['is_active'] = $navData['is_active'];
            $structuredData["id_".$navData['id']]['parent_name'] = $navData['parent_name'];
             if($navData['parent_ids'] == ''){
                $parentArray[] = $navData['id'];}
            else{
                    $navFlowArray["id_".$navData['parent_ids']][] = $navData['id'];}
        }
        return array("parents"=> $parentArray,"navData"=>$structuredData,"navFlow"=>$navFlowArray);
    }
    /*
     * This Function is used to create navigation and set it into cache
     */
    function set_header_navigation_in_cache($entity_type){
        $data['main_nav'] = $this->get_main_nav_data("main_nav",$entity_type);
        $data['right_nav'] = $this->get_main_nav_data("right_nav",$entity_type);
        if($entity_type == "Partner"){
            $agent_id=$this->My_CI->session->userdata('agent_id');
            $data['loginname']=$this->My_CI->reusable_model->get_search_query('entity_login_table','agent_name',array('agent_id'=>$agent_id),array(),array(),array(),array(),array())->row_array()['agent_name'];
            $msg = $this->My_CI->load->view('partner/header_navigation',$data,TRUE);
           $this->My_CI->cache->file->save('navigationHeader_partner_'.$this->My_CI->session->userdata('user_group').'_'.$this->My_CI->session->userdata('agent_id'), $msg, 36000);
        }
        else{
            $data['saas_module'] = $this->My_CI->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
            $msg = $this->My_CI->load->view('employee/header/header_navigation',$data,TRUE);
            $this->My_CI->cache->file->save('navigationHeader_'.$this->My_CI->session->userdata('id'), $msg, 36000);
        }
    }
    /*
     * This Function used to load navigation header from cache
     */
    function load_nav_header(){
        //Check is navigation there in cache?
        // If not then create navigation and loads into cache
        if(!$this->My_CI->cache->file->get('navigationHeader_'.$this->My_CI->session->userdata('id'))){
                $this->set_header_navigation_in_cache("247Around");
         }
        $data['header_navigation_html'] = $this->My_CI->cache->file->get('navigationHeader_'.$this->My_CI->session->userdata('id'));
        $this->My_CI->load->view('employee/header/load_header_navigation', $data);
    }
    /*
     * This is a helper function for fake_reschedule_handling , This function is used to get fake reschedule booking data
     */
    function get_fake_reschedule_booking_details($userPhone,$bookingID,$whereArray){
         log_message('info', __METHOD__.'Function Start');
        if($bookingID){
            $whereArray["booking_details.booking_id"] = $bookingID; 
        }
        else{
            $whereArray["users.phone_number"] = $userPhone; 
        }
        //get Booking id
        $bookingDetails = $this->My_CI->reusable_model->get_search_result_data("booking_details","booking_details.booking_id,booking_details.booking_date,booking_details.assigned_vendor_id,"
                . "booking_details.booking_timeslot,booking_details.assigned_vendor_id",
                $whereArray,array("users"=>"users.user_id=booking_details.user_id","service_center_booking_action"=>"service_center_booking_action.booking_id=booking_details.booking_id"),
                NULL,NULL,NULL,NULL,array("booking_details.booking_id"));
        return $bookingDetails;
         log_message('info', __METHOD__.'Function End');
    }
    /*
     * This Function is used to handle Fake Reschedule request By Miss Call Functionality
     * 1st) reschedule request will get rejected 
     * 2nd) booking will be escalated
     */
    function fake_reschedule_handling($userPhone,$id,$employeeID,$remarks,$bookingID=NULL){
        $isEscalationDone = FALSE;
        log_message('info', __METHOD__.' Function Start');
        $already_rescheduled =0;
        $whereArray["service_center_booking_action.internal_status"] = "Reschedule"; 
        $bookingDetails = $this->get_fake_reschedule_booking_details($userPhone,$bookingID,$whereArray);
        if(empty($bookingDetails)){
            $where["service_center_booking_action.internal_status"] = "Pending"; 
            $bookingDetails = $this->get_fake_reschedule_booking_details($userPhone,$bookingID,$where);
            $already_rescheduled  = 1;
        }
        $numberOfBookings = count($bookingDetails);
        if($numberOfBookings == 1){
            $booking_id = $bookingDetails[0]['booking_id'];
            $vendor_id = $bookingDetails[0]['assigned_vendor_id'];
            $escalation_reason_id = PENALTY_FAKE_COMPLETED_CUSTOMER_DOES_NOT_WANT;
            if($already_rescheduled !=1){
                $this->reject_reschedule_request($booking_id,$escalation_reason_id,$remarks,$id,$employeeID);
            }
            //Send Push Notification
            $rmArray = $this->My_CI->vendor_model->get_rm_sf_relation_by_sf_id($vendor_id);
            $receiverArray['vendor']= array($vendor_id);
            $receiverArray['employee']= array($rmArray[0]['agent_id']);
            $notificationTextArray['msg'] = array($booking_id,"Cancelled");
            $notificationTextArray['title'] = array("Cancelled(Rescheduled)");
            $this->My_CI->push_notification_lib->create_and_send_push_notiifcation(BOOKING_UPDATED_BY_247AROUND,$receiverArray,$notificationTextArray);
            //End Sending Push Notification
            $is_penalty_applicable = $this->is_fake_rescheduled_penalty_valid($bookingDetails,$userPhone);
            if($is_penalty_applicable){
                $isEscalationDone =  $this->process_escalation($booking_id,$vendor_id,$escalation_reason_id,$remarks,TRUE,$id,$employeeID);
            }
           return $isEscalationDone;
        }
         log_message('info', __METHOD__.' Function End');
    }
    function is_fake_rescheduled_penalty_valid($bookingDetails,$userPhone){
        log_message('info', __METHOD__.' Function Start '.$userPhone);
//        $historyWhere['booking_id'] =  $bookingDetails[0]['booking_id'];
//        $historyWhere['new_state'] =  "InProcess_Rescheduled";
//        $historyWhere['service_center_id'] =  $bookingDetails[0]['assigned_vendor_id'];
//        $historyLimitArray['length'] =  1;
//        $historyLimitArray['start'] =  0;
//        $historyOrderBYArray['id'] =  'ASC';
//        $lastResheduledRequestData = $this->My_CI->reusable_model->get_search_result_data("booking_state_change","*",$historyWhere,NULL,$historyLimitArray,$historyOrderBYArray,
//                NULL,NULL,array()); 
//        $where['from_number'] = $userPhone;
//        $where['(date(create_date) >= "'.date('Y-m-d', strtotime($lastResheduledRequestData[0]['create_date'])).'" AND date(create_date)<="'.date('Y-m-d').'" )'] = NULL;
//        $logData = $this->My_CI->reusable_model->get_search_result_data("fake_reschedule_missed_call_log log","COUNT(log.id) as count",$where,NULL,NULL,NULL,NULL,NULL,array());
//        log_message('info', __METHOD__.' Function Start '.print_r($logData,true));
//        if($logData[0]['count'] >0){
//            log_message('info', __METHOD__.' Function End With False '.$userPhone);
//            return false;    
//        }
//        else{
//            log_message('info', __METHOD__.' Function End With True '.$userPhone);
//            return true;
//        }
       $where['booking_id'] =  $bookingDetails[0]['booking_id'];
       $where['escalation_reason'] =  PENALTY_FAKE_COMPLETED_CUSTOMER_DOES_NOT_WANT;
       $where['vendor_id'] =  $bookingDetails[0]['assigned_vendor_id'];
       $alreadyApplicablePenalty = $this->My_CI->reusable_model->get_search_result_data("vendor_escalation_log","COUNT(id) as count",$where,NULL,NULL,NULL,NULL,NULL,array()); 
       log_message('info', __METHOD__.' Function Start '.print_r($alreadyApplicablePenalty,true));
        if($alreadyApplicablePenalty[0]['count'] >0){
            log_message('info', __METHOD__.' Function End With False '.$userPhone);
            return false;    
        }
        else{
            log_message('info', __METHOD__.' Function End With True '.$userPhone);
            return true;
        }
    }
    /*
     * This function is used to reject reschedule request in case of fake reschedule
     */
    function reject_reschedule_request($booking_id,$escalation_reason_id,$remarks,$id,$employeeID){
        log_message('info', __METHOD__.' Function Start');
        //Change Booking Status Back to Pending
       $affectedRows = $this->My_CI->reusable_model->update_table("service_center_booking_action",array("current_status"=>"Pending","internal_status"=>"Pending"),
                array("booking_id"=>$booking_id));
        if($affectedRows>0){
            //State Change
            $escalation_reason  = $this->My_CI->vendor_model->getEscalationReason(array('id'=>$escalation_reason_id));
            if(!empty($remarks)){
                $escalation_reason_final = $escalation_reason[0]['escalation_reason'].' - '.$remarks;
             }
             else{
                $escalation_reason_final = $escalation_reason[0]['escalation_reason'];
              }
            $this->My_CI->notify->insert_state_change($booking_id,"Fake_Reschedule","Pending",$escalation_reason_final,$id,$employeeID,ACTOR_REJECT_RESCHEDULE_REQUEST,
                    NEXT_ACTION_REJECT_RESCHEDULE_REQUEST, _247AROUND);
            return TRUE;
        }
        else{
            return FALSE;
        }
        log_message('info', __METHOD__.' Function End');
    }
    function process_escalation($booking_id,$vendor_id,$escalation_reason_id,$remarks,$checkValidation,$id,$employeeID){
        log_message('info',__FUNCTION__);
        $escalation['booking_id'] = $booking_id;
        $escalation['vendor_id'] = $vendor_id;       
        if ($checkValidation) {
            //Get SF to RM relation if present
            $cc = "";
            $rm = $this->My_CI->vendor_model->get_rm_sf_relation_by_sf_id($escalation['vendor_id']);
            if(!empty($rm)){
                foreach($rm as $key=>$value){
                    if($key == 0){
                        $cc .= "";
                    }else{
                        $cc .= ",";
                    }
                    $cc .= $this->My_CI->employee_model->getemployeefromid($value['agent_id'])[0]['official_email'];
                }
            }
        
            $escalation['escalation_reason'] = $escalation_reason_id;
            $this->My_CI->booking_model->increase_escalation_reschedule($escalation['booking_id'], "count_escalation");
            $booking_date_timeslot = $this->My_CI->vendor_model->getBookingDateFromBookingID($escalation['booking_id']);
            $booking_date = strtotime($booking_date_timeslot[0]['booking_date']);
            $escalation['booking_date'] = date('Y-m-d', $booking_date);
            $escalation['booking_time'] = $booking_date_timeslot[0]['booking_timeslot'];
            //inserts vendor escalation details
            $escalation_id = $this->My_CI->vendor_model->insertVendorEscalationDetails($escalation);
            if ($escalation_id) {
                $escalation_policy_details = $this->My_CI->vendor_model->getEscalationPolicyDetails($escalation['escalation_reason']);     
                //Send Push Notification
                $receiverArray['vendor']= array($vendor_id);
                $notificationTextArray['title'] = array($booking_id);
                $notificationTextArray['msg'] = array($booking_id,$escalation_policy_details[0]['escalation_reason']);
                $this->My_CI->push_notification_lib->create_and_send_push_notiifcation(BOOKING_ESCALATION_VENDOR,$receiverArray,$notificationTextArray);
                //End Sending Push Notification
                // Update escalation flag and return userDeatils
                $userDetails = $this->My_CI->vendor_model->updateEscalationFlag($escalation_id, $escalation_policy_details, $escalation['booking_id']);
                log_message('info', "User Details " . print_r($userDetails, TRUE));
                log_message('info', "Vendor_ID " . $escalation['vendor_id']);
                //get account manager details
                $am_email = "";
                $partner_id = $this->My_CI->booking_model->get_bookings_count_by_any('booking_details.partner_id',array('booking_details.booking_id'=>$booking_id));
                if(!empty($partner_id)){
                    $accountManagerData = $this->get_am_data($partner_id[0]['partner_id']);
                    
                    if(!empty($accountManagerData)){
                        $am_email = $accountManagerData[0]['official_email'];
                    }
                }
                $vendorContact = $this->My_CI->vendor_model->getVendorContact($escalation['vendor_id']);
                //From will be AM email id if not exist then currently logged in user
                if($am_email){
                    $from = $am_email;
                }
                else{
                    $from = $this->My_CI->employee_model->getemployeefromid($id)[0]['official_email'];
                }
               
                $return_mail_to = $vendorContact[0]['owner_email'].','.$vendorContact[0]['primary_contact_email'];
                //Getting template from Database
                $template = $this->My_CI->booking_model->get_booking_email_template("escalation_on_booking");
                if (!empty($template)) {  
                    //Sending Mail
                    $email['booking_id'] = $escalation['booking_id'];
                    $email['count_escalation'] = $booking_date_timeslot[0]['count_escalation'];
                    $email['reason'] = $escalation_policy_details[0]['escalation_reason'];
                    $emailBody = vsprintf($template[0], $email);
                    $subject['booking_id'] = $escalation['booking_id'];
                    $subjectBody = vsprintf($template[4], $subject);
                    $this->My_CI->notify->sendEmail($from, $return_mail_to, $template[3] . "," . $cc.",".$from, '', $subjectBody, $emailBody, "",'escalation_on_booking', "", $booking_id);
                    //Logging
                    log_message('info', " Escalation Mail Send successfully" . $emailBody);
                } else {
                    //Logging Error Message
                    log_message('info', " Error in Getting Email Template for Escalation Mail");
                }
                $this->sendSmsToVendor($escalation,$escalation_policy_details, $vendorContact, $escalation['booking_id'], $userDetails);
                $escalation_reason  = $this->My_CI->vendor_model->getEscalationReason(array('id'=>$escalation['escalation_reason']));
                if(!empty($remarks)){
                    $escalation_reason_final = $escalation_reason[0]['escalation_reason'].' - '.$remarks;
                }
                else{
                    $escalation_reason_final = $escalation_reason[0]['escalation_reason'];
                }
                $this->My_CI->notify->insert_state_change($escalation['booking_id'],"Escalation","Pending",$escalation_reason_final,$id,$employeeID,ACTOR_ESCALATION,NEXT_ACTION_ESCALATION
                        , _247AROUND);
                //Processing Penalty on Escalations
                $value['booking_id'] = $escalation['booking_id'];
                $value['assigned_vendor_id'] = $escalation['vendor_id'];
                $value['current_state'] = "Escalation";
                $value['agent_id'] = $id;
                $value['remarks'] = $escalation_reason_final;
                $value['agent_type'] = 'admin';
                $where = array('escalation_id' => $escalation_reason_id, 'active' => '1');
                //Adding values in penalty on booking table
                $this->My_CI->penalty_model->get_data_penalty_on_booking($value, $where);
                log_message('info', 'Penalty added for Escalations - Booking : ' . $escalation['booking_id']);
                return TRUE;
	    }
            else{
                return FALSE;
            }
        } else {
            return FALSE;
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
    function sendSmsToVendor($escalation,$escalation_policy, $contact, $booking_id, $userDetails) {
        $id = $escalation['vendor_id'];
        if ($escalation_policy[0]['sms_to_owner'] == 1 && $escalation_policy[0]['sms_to_poc'] == 1){
            $smsBody = $this->replaceSms_body($escalation_policy[0]['sms_body'], $booking_id, $userDetails);
            $status = $this->My_CI->notify->sendTransactionalSmsMsg91($contact[0]['primary_contact_phone_1'], $smsBody,SMS_WITHOUT_TAG);
            //For saving SMS to the database on sucess
            $this->My_CI->notify->add_sms_sent_details($id, 'vendor' , $contact[0]['primary_contact_phone_1'],
            $smsBody, $booking_id, "Escalation", $status['content']);
            $status1 = $this->My_CI->notify->sendTransactionalSmsMsg91($contact[0]['owner_phone_1'], $smsBody,SMS_WITHOUT_TAG);
            //For saving SMS to the database on sucess
            $this->My_CI->notify->add_sms_sent_details($id, 'vendor' , $contact[0]['owner_phone_1'],
            $smsBody, $booking_id,"Escalation", $status1['content']);
          } 
          else if ($escalation_policy[0]['sms_to_owner'] == 0 && $escalation_policy[0]['sms_to_poc'] == 1) {
            $smsBody = $this->replaceSms_body($escalation_policy[0]['sms_body'], $booking_id, $userDetails);
            $status = $this->My_CI->notify->sendTransactionalSmsMsg91($contact[0]['primary_contact_phone_1'], $smsBody,SMS_WITHOUT_TAG);
            //For saving SMS to the database on sucess
            $this->notify->add_sms_sent_details($id, 'vendor' , $contact[0]['primary_contact_phone_1'],$smsBody, $booking_id, "Escalation", $status['content']);
          } 
          else if ($escalation_policy[0]['sms_to_owner'] == 1 && $escalation_policy[0]['sms_to_poc'] == 0) {
            $smsBody = $this->replaceSms_body($escalation_policy[0]['sms_body'], $booking_id, $userDetails);
            $status = $this->My_CI->notify->sendTransactionalSmsMsg91($contact[0]['owner_phone_1'], $smsBody,SMS_WITHOUT_TAG);
            //For saving SMS to the database on sucess
            $this->My_CI->notify->add_sms_sent_details($id, 'vendor' , $contact[0]['owner_phone_1'],
            $smsBody, $booking_id, "Escalation", $status['content']); 
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
    /*
     * This Function is used to approve rescheduled booking
     */
    function approved_rescheduled_bookings($reschedule_booking_id,$reschedule_booking_date,$reschedule_reason,$partner_id_array,$id,$employeeID){
         log_message('info', __FUNCTION__);
         foreach ($reschedule_booking_id as $booking_id) {
            $partner_id = $partner_id_array[$booking_id];
            $booking['booking_date'] = date('d-m-Y', strtotime($reschedule_booking_date[$booking_id]));
            $booking['current_status'] = 'Rescheduled';
            $booking['internal_status'] = 'Rescheduled';
            $booking['update_date'] = date("Y-m-d H:i:s");
            $booking['mail_to_vendor'] = 0;
            
            $booking['reschedule_reason'] = $reschedule_reason[$booking_id];
            //check partner status from partner_booking_status_mapping table  
            $partner_status =$this->My_CI->booking_utilities->get_partner_status_mapping_data($booking['current_status'], $booking['internal_status'], $partner_id, $booking_id);
            $actor = $next_action = 'not_define';
            if (!empty($partner_status)) {
                $booking['partner_current_status'] = $partner_status[0];
                $booking['partner_internal_status'] = $partner_status[1];
                $actor = $booking['actor'] = $partner_status[2];
                $next_action = $booking['next_action'] = $partner_status[3];
            }
            log_message('info', __FUNCTION__ . " update booking: " . print_r($booking, true));
            $this->My_CI->booking_model->update_booking($booking_id, $booking);
            
            $data['internal_status'] = _247AROUND_PENDING;
            $data['current_status'] = _247AROUND_PENDING;
            log_message('info', __FUNCTION__ . " update service cenetr action table: " . print_r($data, true));
            $this->My_CI->vendor_model->update_service_center_action($booking_id, $data);
            //Send Push Notification
            $vendorData = $this->My_CI->vendor_model->getVendor($booking_id);
            $receiverArray['vendor']= array($vendorData[0]['id']);
            $notificationTextArray['msg'] = array($booking_id,"Rescheduled");
            $notificationTextArray['title'] = array("Rescheduled");
            $this->My_CI->push_notification_lib->create_and_send_push_notiifcation(BOOKING_UPDATED_BY_247AROUND,$receiverArray,$notificationTextArray);
            //End Sending Push Notification
            //Log this state change as well for this booking
            //param:-- booking id, new state, old state, employee id, employee name
            $this->My_CI->notify->insert_state_change($booking_id, _247AROUND_RESCHEDULED, _247AROUND_PENDING, $booking['reschedule_reason'], $id,$employeeID, $actor,$next_action,_247AROUND);          
            log_message('info', __FUNCTION__ . " partner callback: " . print_r($booking_id, true));
            $this->My_CI->partner_cb->partner_callback($booking_id);
            log_message('info', 'Rescheduled- Booking id: ' . $booking_id . " Rescheduled By " . $employeeID . " data " . print_r($data, true));
        }
    } 
    function get_reader_by_file_type($type, $url, $width) {
        $finalString = '';
        if ($type == 'video') {
            $finalString = '<video width="' . $width . '" controls>
  <source src="' . $url . '" type="video/mp4">
  Your browser does not support HTML5 video.
</video>';
        }
        if ($type == 'pdf') {
            $finalString = '<a target="_blank" href="' . $url . '">View</a>';
        }
        if ($type == 'audio') {
            $finalString = '<audio controls>
  <source src="' . $url . '" type="audio/ogg">
Your browser does not support the audio element.
</audio>';
        }
        return $finalString;
    }
    
    function get_SF_payout($booking_id, $service_center_id, $amount_due, $flat_upcountry){
       
        $where = array('booking_unit_details.booking_id' =>$booking_id, "booking_status != 'Cancelled'" => NULL);
        
        $select = "(vendor_basic_charges + vendor_st_or_vat_basic_charges "
                . "+ vendor_extra_charges + vendor_st_extra_charges+ vendor_parts+ vendor_st_parts) as sf_earned";
      
        $b_earned = $this->My_CI->booking_model->get_unit_details($where, FALSE, $select);
        $unit_amount = 0;
        foreach($b_earned as $earn){
            $unit_amount += $earn['sf_earned'];
        }
        $misc_charge = 0;
        $misc_charge_data = $this->My_CI->booking_model->get_misc_charges_data('sum(vendor_basic_charges + vendor_tax) as misc_charge', array('booking_id' => $booking_id, 'active' => 1));
        if(!empty($misc_charge_data)){
            $misc_charge = $misc_charge_data[0]['misc_charge'];
        }
        
        $penalty_select = "CASE WHEN ((count(booking_id) *  penalty_on_booking.penalty_amount) > cap_amount) THEN (cap_amount)

        ELSE (COUNT(booking_id) * penalty_on_booking.penalty_amount) END  AS p_amount";
        $penalty_where = array('booking_id' => $booking_id,'service_center_id' => $service_center_id,'penalty_on_booking.active' => 1);
        $p_amount = $this->My_CI->penalty_model->get_penalty_on_booking_any($penalty_where, $penalty_select, array('CASE'));
        
        $is_customer_paid = 1;
        if(empty($amount_due)){
            $is_customer_paid = 0;
        }
        
        if($flat_upcountry == 1){
            $is_customer_paid = 1;
        }
        $upcountry = $this->My_CI->upcountry_model->upcountry_booking_list($service_center_id, $booking_id, true, $is_customer_paid, $flat_upcountry);
        $up_charges = 0;
        if(!empty($upcountry)){
            if($upcountry[0]['count_booking'] == 0){
                $upcountry[0]['count_booking'] = 1;
            }
            $up_charges = $upcountry[0]['upcountry_price']/$upcountry[0]['count_booking'];
        }
        $return['sf_earned'] = round($unit_amount -$p_amount[0]['p_amount'] + $up_charges + $misc_charge, 0);
        if($p_amount[0]['p_amount'] > 0){
            $return['penalty'] = TRUE;
        } else{
            $return['penalty'] = FALSE;
        }
        
        return $return;
    }
function get_district_covered_by_vendors(){
    // below query - SELECT Vendor_ID as vendorID, GROUP_CONCAT(DISTINCT city) as district FROM (vendor_pincode_mapping) GROUP BY Vendor_ID
    $data = $this->My_CI->reusable_model->get_search_result_data("vendor_pincode_mapping","Vendor_ID as vendorID,GROUP_CONCAT(DISTINCT city) as district",NULL,NULL,NULL,NULL,NULL,NULL,array('Vendor_ID'));
    foreach($data as $values){
       $finalArray[$values['vendorID']] = $values['district'];
    }
    return $finalArray;
}
function generate_image($base64, $image_name,$directory){
        $binary = base64_decode($base64);
        $image_path = TMP_FOLDER . $image_name;
        $file = fopen($image_path, 'wb');
        fwrite($file, $binary);
        fclose($file);
        
        $s3directory = $directory."/" . $image_name;

        $this->My_CI->s3->putObjectFile(TMP_FOLDER.$image_name, BITBUCKET_DIRECTORY, $s3directory, S3::ACL_PUBLIC_READ,array(),"binary/octet-stream");
        
        unlink($image_path);
        return $s3directory;
    }
    
    function getShortUrl($url) {
        return file_get_contents("http://tinyurl.com/api-create.php?url=".$url);

//        $apiKey = GOOGLE_URL_SHORTNER_KEY;
//
//        $postData = array('longUrl' => $url);
//        $jsonData = json_encode($postData);
//
//        $curlObj = curl_init();
//        curl_setopt($curlObj, CURLOPT_URL, 'https://www.googleapis.com/urlshortener/v1/url?key=' . $apiKey);
//        curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, 0);
//        curl_setopt($curlObj, CURLOPT_HEADER, 0);
//        curl_setopt($curlObj, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
//        curl_setopt($curlObj, CURLOPT_POST, 1);
//        curl_setopt($curlObj, CURLOPT_POSTFIELDS, $jsonData);
//
//        $response = curl_exec($curlObj);
//
//        $json = json_decode($response);
//
//        curl_close($curlObj);
//
//        if (isset($json->error)) {
//          
//            log_message("info", __METHOD__. " Short url not generated ". print_r($json->error, true));
//            $email_template = $this->My_CI->booking_model->get_booking_email_template("google_short_url_generation_failed");
//            $subject = $email_template[4];
//            $message = "long Url - ". $url." Google Response ". $response;
//            $email_from = $email_template[2];
//
//            $to = $email_template[1];
//            $cc = $email_template[3];
//
//            $this->My_CI->notify->sendEmail($email_from, $to, $cc, "", $subject, $message,"",'google_short_url_generation_failed');
//            return false;
//        } else {
//            return $json->id;
//        }
    }
    
   function convert_html_to_pdf($html, $booking_id, $filename, $s3_folder) {

        log_message('info', __FUNCTION__ . " => Entering, Booking ID: " . $booking_id);
        require_once __DIR__ . '/pdf/vendor/autoload.php';
        $mpdf = new \Mpdf\Mpdf();
        $t = $mpdf->WriteHTML($html);
        $tempfilePath = TMP_FOLDER . $filename;
        $mpdf->Output($tempfilePath, 'F');
        if ($mpdf) {
            $is_file = $this->My_CI->s3->putObjectFile($tempfilePath, BITBUCKET_DIRECTORY, $s3_folder . "/" . $filename, S3::ACL_PUBLIC_READ);
            if ($is_file) {
                $response_data = array('response' => 'Success',
                    'response_msg' => 'PDF generated Successfully and uploaded on S3',
                    'output_pdf_file' => $filename,
                    'bucket_dir' => BITBUCKET_DIRECTORY,
                    'id' => $booking_id
                );
                //unlink($tempfilePath);

                return json_encode($response_data);
            } else {
                //return this response when PDF generated successfully but unable to upload on S3
                $response_data = array('response' => 'Error',
                    'response_msg' => 'PDF generated Successfully But Unable To Upload on S3',
                    'output_pdf_file' => $filename,
                    'bucket_dir' => BITBUCKET_DIRECTORY,
                    'id' => $booking_id
                );
                return json_encode($response_data);
            }
        } else {
            $response_data = array('response' => 'Error',
                'response_msg' => 'Error In Generating PDF File',
            );
            $to = DEVELOPER_EMAIL;
            $cc = "";
            $subject = "Job Card Not Generated By Mpdf";
            $msg = "There are some issue while creating pdf for booking_id/invoice_id $booking_id. Check the issue and fix it immediately";
            $this->My_CI->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, "", $subject, $msg, JOB_CARD_NOT_GENERATED);
            return json_encode($response_data);
        }
    }

    /**
     * @desc: This function is used to download SF declaration who don't have GST number hen Partner update spare parts
     * @params: String $sf_id
     * @return: void
     */
    function generate_sf_declaration($sf_id){
        log_message("info", __METHOD__." SF Id ". $sf_id);
        $sf_details = $this->My_CI->vendor_model->getVendorDetails('id,name,address,owner_name,is_signature_doc,signature_file', array('id' => trim($sf_id)));
        $template = 'sf_without_gst_declaration.xlsx';
        $output_pdf_file = "";
        $excel_file = "";
        $excel_data = array();
        if (!empty($sf_details[0]['signature_file'])) {
            $excel_data['excel_data']['sf_name'] = $sf_details[0]['name'];
            $excel_data['excel_data']['sf_address'] = $sf_details[0]['address'];
            $excel_data['excel_data']['sf_owner_name'] = $sf_details[0]['owner_name'];
            $excel_data['excel_data']['date'] = date('Y-m-d');
            $excel_data['excel_data_line_item'] = array();
            $cell = 'B21';
            if (file_exists($sf_details[0]['signature_file'])) {
                $signature_file = TMP_FOLDER . $sf_details[0]['signature_file'];
            } else {
                $s3_bucket = "https://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/vendor-partner-docs/" . $sf_details[0]['signature_file'];
                //get signature file from s3 and save it to server
                copy($s3_bucket, TMP_FOLDER . $sf_details[0]['signature_file']);
                system(" chmod 777 " . TMP_FOLDER . $sf_details[0]['signature_file']);
                $signature_file = TMP_FOLDER . $sf_details[0]['signature_file'];
            }
            $output_file = "sf_declaration_" . $sf_details[0]['id'] . "_" . date('d_M_Y_H_i_s');
            $excel_file = $this->generate_excel_data($template, $output_file, $excel_data, false, $cell, $signature_file);
            //generate pdf
            if (file_exists($excel_file)) {
                $json_result = $this->convert_excel_to_pdf($excel_file, $sf_details[0]['id'], 'vendor-partner-docs');
                log_message('info', __FUNCTION__ . ' PDF JSON RESPONSE' . print_r($json_result, TRUE));
                $pdf_response = json_decode($json_result, TRUE);

                if ($pdf_response['response'] === 'Success') {
                    $output_pdf_file = $pdf_response['output_pdf_file'];
                    unlink($excel_file);
                    log_message('info', __FUNCTION__ . ' Generated PDF File Name' . $excel_file);
                } else if ($pdf_response['response'] === 'Error') {

                    log_message('info', __FUNCTION__ . ' Error in Generating PDF File');
                }
            } else {
                log_message("info", "File Not Generated for " . $sf_details[0]['id']);
            }

            if (!empty($output_pdf_file)) {
                $s3_bucket = "https://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/vendor-partner-docs/" . $output_pdf_file;
                //get pdf file from s3 and save it to server
                copy($s3_bucket, TMP_FOLDER . $output_pdf_file);
                system(" chmod 777 " . TMP_FOLDER . $output_pdf_file);

                if (file_exists(TMP_FOLDER . $output_pdf_file)) {
                    
                    $response['status'] = true;
                    $response['message'] = "Pdf denerated successfully.";
                    $response['file_name'] = $output_pdf_file;
                }
            } else {
                log_message("info", "Error In generating Declaration file SF ID: ".$sf_details[0]['id']);
                $response['status'] = true;
                $response['message'] = "Some Error Occured!!! Please Try Again";
                $response['file_name'] = $output_pdf_file;
            }
        } else {
            log_message("info", "SF Id ".$sf_details[0]['id'] . " does not have signature file");
            $response['status'] = true;
            $response['message'] = "Invalid Request";
            $response['file_name'] = $output_pdf_file;
        }
        
        return $response;
    }
    
    /**
     * @desc This is used to map jeeves completed booking status with our status
     * @param Int $partner_id
     * @param String $remarks
     */
    function partner_completed_call_status_mapping($partner_id, $status){
        log_message("info", __METHOD__ . " Partner ID ". $partner_id. " Remarks ". $status);
        
        $data = array();
        $data[JEEVES_ID][CALLBACK_SCHEDULED] = JEEVES_CUSTOMER_RESCHEDULED;
        $data[JEEVES_ID][RESCHEDULE_FOR_UPCOUNTRY] = JEEVES_CUSTOMER_RESCHEDULED;
        $data[JEEVES_ID][CUSTOMER_NOT_REACHABLE] = JEEVES_CUSTOMER_NO_RESPONSE;
        $data[JEEVES_ID][ENGINEER_ON_ROUTE] = JEEVES_CUSTOMER_RESCHEDULED;
        $data[JEEVES_ID][CUSTOMER_ASK_TO_RESCHEDULE] = JEEVES_CUSTOMER_RESCHEDULED;
        $data[JEEVES_ID][PRODUCT_NOT_DELIVERED_TO_CUSTOMER] = JEEVES_PRODUCT_NOT_DELIVERED;
        
        if(isset($data[$partner_id][$status])){
            
            return $data[$partner_id][$status];
            
        } else {
            return FALSE;
        }
    }
    /**
     * /**
     * @desc This is used to fetch challan id.
     * In this method, simply pass sc code of vendor then it return latest challan id
     * @param String $name
     * @return String
     */
    function create_sf_challan_id($name,$is_wh = false){
        $challan_id_tmp = $name."-DC-";
        $where['length'] = -1;
        
        if($is_wh){
            $where['where'] = array("( partner_challan_number LIKE '%".$challan_id_tmp."%' )" => NULL);
            $where['select'] = "partner_challan_number as challan_number";
        }else{
            $where['where'] = array("( sf_challan_number LIKE '%".$challan_id_tmp."%' )" => NULL);
            $where['select'] = "sf_challan_number as challan_number";
        }
        
        $challan_no_temp = $this->My_CI->partner_model->get_spare_parts_by_any($where['select'], $where['where']);
        
        $challan_no = 1;
        $int_challan_no = array();
        
        if (!empty($challan_no_temp)) {
           
            foreach ($challan_no_temp as  $value) {
                $c_explode = explode(",", $value['challan_number']);
                foreach ($c_explode as $value1) {
                    $explode = explode($challan_id_tmp, $value1);
                 
                    array_push($int_challan_no, $explode[1] + 1);
                }
                
            }
            rsort($int_challan_no);
            $challan_no = $int_challan_no[0];
        }
        
        return trim($challan_id_tmp . sprintf("%'.04d\n", $challan_no));
    }
    function create_serviceability_report_csv($postData){
        log_message('info', __FUNCTION__ . " Function Start With Request  ".print_r($postData,true));
        $services = $postData['service_id'];
        $appliace_opt = $postData['appliance_opt'];
        $pincode_opt = $postData['pincode_opt'];
        $state_opt = $postData['state_opt'];
        $city_opt = $postData['city_opt'];
        $whereIN = $join = NULL;
        $groupBY = array();
        $orderBY = array('vendor_pincode_mapping.Pincode'=>'ASC');
        if($appliace_opt == 1){
             $service_id = explode(",",$services);
             if (in_array('all', $service_id)) {
                $service_id = array_column($this->My_CI->booking_model->selectservice(true), 'id');
             }
            $whereIN['services.id'] =  $service_id;
            $join['services'] =  'services.id = vendor_pincode_mapping.Appliance_ID';
            $select[] = "services.services as Appliance";
            $groupBY[] = 'vendor_pincode_mapping.Appliance_ID';
        }
         if($pincode_opt == 1){
            $select[] = "vendor_pincode_mapping.Pincode";
            $groupBY[] = 'vendor_pincode_mapping.Pincode';
        }
         if($state_opt){
            $select[] = "vendor_pincode_mapping.State";
            $groupBY[] = 'vendor_pincode_mapping.State';
        }
         if($city_opt){
            $select[] = "vendor_pincode_mapping.City";
            $groupBY[] = 'vendor_pincode_mapping.City';
        }
        $join['service_centres'] =  'service_centres.id = vendor_pincode_mapping.Vendor_ID AND service_centres.on_off = 1 AND service_centres.active = 1';
        $data = $this->My_CI->reusable_model->get_search_result_data('vendor_pincode_mapping',implode(',',$select),NULL,$join,NULL,$orderBY,$whereIN,NULL,$groupBY);
        foreach($data as $dataValues){
            $headings = array_keys($dataValues);
            $CSVData[] = array_values($dataValues);
        }
        $csv = implode(",",$headings)." \n";//Column headers
        foreach ($CSVData as $record){
            $csv.=implode(",",$record)."\n"; //Append data to csv
        }
        $output_file = TMP_FOLDER . "serviceability_report.csv";
        $csv_handler = fopen ($output_file,'w');
        fwrite ($csv_handler,$csv);
        fclose ($csv_handler);
        log_message('info', __FUNCTION__ . " Function End  ");
    }
    function send_bad_rating_email($rating,$bookingID=NULL,$number=NULL){
        log_message('info', __FUNCTION__ . " Start For  ".$bookingID.$number);
        if(!$bookingID){
            $bookingDetails = $this->My_CI->booking_model->get_missed_call_rating_booking_count($number);
            if($bookingDetails){
                $bookingID = $bookingDetails[0]['booking_id'];
            }
        }
        if($bookingID){
            $join['service_centres'] = 'booking_details.assigned_vendor_id = service_centres.id';
            $JoinTypeTableArray['service_centres'] = 'left';
            $booking_state = $this->My_CI->reusable_model->get_search_query('booking_details','service_centres.state',array('booking_details.booking_id' => $bookingID),$join,NULL,NULL,NULL,$JoinTypeTableArray)->result_array();
            
            $select = "booking_details.*,employee.id as emp_id,employee.official_email,service_centres.name,services.services,service_centres.primary_contact_email as sf_email";
            $where["booking_details.booking_id"] = $bookingID; 
            $partnerJoin["agent_filters"] = "agent_filters.entity_id=booking_details.partner_id";
            $join["employee_relation"] = "FIND_IN_SET(booking_details.assigned_vendor_id,employee_relation.service_centres_id)";
            $join["employee"] = "employee.id=employee_relation.agent_id";
            $join["service_centres"] = "service_centres.id=booking_details.assigned_vendor_id";
            $join["services"] = "services.id=booking_details.service_id";
            $partnerJoin["employee"] = "employee.id=agent_filters.agent_id";
            $bookingData = $this->My_CI->reusable_model->get_search_result_data("booking_details",$select,$where,$join,NULL,NULL,NULL,NULL,array());
            
            $where['agent_filters.entity_type'] = "247around";
            $where['agent_filters.state'] = $booking_state[0]['state'];
            
            $amEmail = $this->My_CI->reusable_model->get_search_result_data("booking_details","group_concat(distinct employee.official_email) as official_email",$where,$partnerJoin,NULL,NULL,NULL,NULL,array());
            if(!empty($bookingData[0]['emp_id'])) {
                $managerData = $this->My_CI->employee_model->getemployeeManagerDetails("employee.*",array('employee_hierarchy_mapping.employee_id' => $bookingData[0]['emp_id'], 'employee.groups' => 'regionalmanager'));
            }
            
            $template = $this->My_CI->booking_model->get_booking_email_template(BAD_RATING);
            $subject = vsprintf($template[4], array($rating,$bookingID));
            $message = vsprintf($template[0], array($bookingData[0]['name'],$bookingData[0]['rating_comments'],$bookingData[0]['request_type'],$bookingData[0]['services']));
            $to = $template[1];  
            $cc = $bookingData[0]['official_email'].",".$amEmail[0]['official_email'].",".$this->My_CI->session->userdata("official_email").",".$bookingData[0]['sf_email'];
            
            if(!empty($managerData)) {
                $cc .= ",".$managerData[0]['official_email'];
            }
            
            $bcc = "";
            $from = $template[2];
            $this->My_CI->notify->sendEmail($from, $to, $cc, $bcc, $subject, $message, "",BAD_RATING);
            log_message('info', __FUNCTION__ . " END  ".$bookingID.$number);
        }
    }
    function update_appliance_details($unitTableID){
       $applianceData = $this->My_CI->reusable_model->get_search_result_data("booking_unit_details","appliance_id,serial_number,purchase_date",array("id"=>$unitTableID),NULL,NULL,NULL,NULL,NULL,array());
       if (!empty($applianceData)) {
            $applianceID = $applianceData[0]['appliance_id'];
            $data['sf_serial_number'] = $applianceData[0]['serial_number'];
            $data['sf_purchase_date'] = $applianceData[0]['purchase_date'];
            $this->My_CI->booking_model->update_appliances($applianceID, $data);
       }
    }
    function download_csv_from_s3($folder,$file){
        $csv = TMP_FOLDER . $file;
        $object = $this->My_CI->s3->getObject(BITBUCKET_DIRECTORY, $folder."/".$file);
        write_file($csv, $object->body);
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($csv) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($csv));
        readfile($csv);
        exec("rm -rf " . escapeshellarg($csv));
        unlink($csv);
    }
    /*
     * This Function used to load navigation header from cache
     */
    function load_partner_nav_header(){
        //Check is navigation there in cache?
        // If not then create navigation and loads into cache
        if(!$this->My_CI->cache->file->get('navigationHeader_partner_'.$this->My_CI->session->userdata('user_group').'_'.$this->My_CI->session->userdata('agent_id'))){
                $this->set_header_navigation_in_cache("Partner");
         }
        $data['header_navigation_html'] = $this->My_CI->cache->file->get('navigationHeader_partner_'.$this->My_CI->session->userdata('user_group').'_'.$this->My_CI->session->userdata('agent_id'));
        $this->My_CI->load->view('partner/header/load_header_navigation', $data);
    }
    
    /**
     * @desc this is used to send email to partner to inform about serial no
     * @param Int $partner_id
     * @param String $serial_number
     * @param String $pic_name
     */
    function inform_partner_for_serial_no($booking_id, $sid, $partner_id, $serial_number, $pic_name) {
        log_message('info', __METHOD__ . " Enterring..");
        //$get_partner_details = $this->My_CI->partner_model->getpartner_details('account_manager_id, primary_contact_email, owner_email', array('partners.id' => $partner_id));
        $get_partner_details = $this->My_CI->partner_model->getpartner_data("group_concat(distinct agent_filters.agent_id) as account_manager_id,primary_contact_email,owner_email", 
                        array('partners.id' => $partner_id),"",0,1,1,"partners.id");
        $am_email = "";
        if (!empty($get_partner_details[0]['account_manager_id'])) {
            //$am_email = $this->My_CI->employee_model->getemployeefromid($get_partner_details[0]['account_manager_id'])[0]['official_email'];
            $am_email = $this->My_CI->employee_model->getemployeeMailFromID($get_partner_details[0]['account_manager_id'])[0]['official_email'];
        }

        $email_template = $this->My_CI->booking_model->get_booking_email_template(INFORM_PARTNER_FOR_NEW_SERIAL_NUMBER);
        if (!empty($email_template)) {
            $to = $get_partner_details[0]['primary_contact_email'];
            
            $rm = $this->My_CI->vendor_model->get_rm_sf_relation_by_sf_id($sid);
            $rm_email = "";
            if (!empty($rm)) {
                $rm_email = ", " . $rm[0]['official_email'];
            }

            $bcc = $email_template[5];
            $subject = vsprintf($email_template[4], array($serial_number));
            $message = vsprintf($email_template[0], array($serial_number, $booking_id));
            if (!empty($am_email)) {
                $from = $am_email;
                $cc = $email_template[3]. ",".$am_email.$rm_email;
            } else {
                $from = $email_template[2];
                $cc = $email_template[3].$rm_email;
            }
            $attachment = S3_WEBSITE_URL . "engineer-uploads/" . $pic_name;
            $this->My_CI->notify->sendEmail($from, $to, $cc, $bcc, $subject, $message, $attachment, INFORM_PARTNER_FOR_NEW_SERIAL_NUMBER);
        }
    }
    function create_entity_login($data){
        $email_sent = true;
        $check_username = $this->My_CI->dealer_model->entity_login(array('entity' => 'partner', 'user_id' => $data['user_id']));
        if(empty($check_username)) {
            $p_where = array('id' => $data['entity_id']);
            //Getting name of Partner by Partner ID
            $partner_details = $this->My_CI->partner_model->get_all_partner($p_where);
            $data['entity_name'] = $partner_details[0]['public_name'];
            if(isset($data['email_not_sent'])){ 
                $email_sent = false;
                unset($data['email_not_sent']);
            }
            $data['user_id'] = str_replace(' ', '-', $data['user_id']);
            $data['user_id'] = preg_replace('/[^A-Za-z0-9-]/', '', $data['user_id']);
            $s1 = $this->My_CI->dealer_model->insert_entity_login($data);
            if ($s1) {
                //Log Message
                
                if($email_sent){
                log_message('info', __FUNCTION__ . ' Partner Login has been Added for id : ' . $data['entity_id'] . ' with values ' . print_r($data, TRUE));
                //Getting template from Database to send mail
                $accountManagerData = $this->get_am_data($data['entity_id']);
                $login_template = $this->My_CI->booking_model->get_booking_email_template("partner_login_details");
                if (!empty($login_template)) {
                    $login_email['username'] = $data['user_id'];
                    $login_email['password'] = $data['clear_password'];
                    $cc = $login_template[3];
                    $bcc = $login_template[5];
                    if(!empty($accountManagerData)){
                        $accountManagerEmail = $accountManagerData[0]['official_email'];
                        $cc = $login_template[3].",".$accountManagerEmail;
                    }
                    $login_subject = $login_template[4];
                    $login_emailBody = vsprintf($login_template[0], $login_email);
                   // $login_email['password'] = "***********";
                    $login_emailBody247 = vsprintf($login_template[0], $login_email);
                    //Send Login Details to partner
                    $this->My_CI->notify->sendEmail($login_template[2], $data['email'], "", "",$login_subject, $login_emailBody, "",'partner_login_details');
                    //Send Login Details to 247around 
                    $to = $this->My_CI->session->userdata('official_email');
                    $this->My_CI->notify->sendEmail($login_template[2], $to, $cc, $bcc,$login_subject, $login_emailBody247, "",'partner_login_details');
                    log_message('info', $login_subject . " Email Send successfully" . $login_emailBody);
                } else {
                    //Logging Error
                    log_message('info', " Error in Getting Email Template for New Vendor Login credentials Mail");
                }
                
                }
                return $s1;
            } else {
                //Log Message
                log_message('info', __FUNCTION__ . ' Error in Adding Partner Login Details for id : ' . $partner_id . ' with values ' . print_r($data, TRUE));
                return false;
            }
        }
    }
    function multi_array_sort_by_key($array, $on, $order=SORT_ASC){
    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case SORT_ASC:
                asort($sortable_array);
                break;
            case SORT_DESC:
                arsort($sortable_array);
                break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }

    return $new_array;
}
    function send_and_save_booking_internal_conversation_email($entity_type,$booking_id,$to,$cc = NULL,$from,$subject,$msg,$agentID,$sender_entity_id){
        $this->My_CI->notify->sendEmail($from, $to, $cc, "", $subject, $msg, "",INTERNAL_CONVERSATION_EMAIL);
        $data['booking_id'] = $booking_id;
        $data['subject'] = $subject;
        $data['msg'] = $msg;
        $data['sender_entity_type'] = $entity_type;
        $data['sender_entity_id'] = $sender_entity_id;
        $data['agent_id'] = $agentID;
        $data['email_to'] = $to;
        $data['email_cc'] = $cc;
        $data['email_from'] = $from;
        return $this->My_CI->reusable_model->insert_into_table("booking_internal_conversation",$data);
    }
    function get_booking_contacts($bookingID,$state_check=1){
        $join['service_centres'] = 'booking_details.assigned_vendor_id = service_centres.id';
        $JoinTypeTableArray['service_centres'] = 'left';
        $booking_state = $this->My_CI->reusable_model->get_search_query('booking_details','service_centres.state',array('booking_details.booking_id' => $bookingID),$join,NULL,NULL,NULL,$JoinTypeTableArray)->result_array();

        $select = "e.phone as am_caontact,e.official_email as am_email, e.full_name as am,partners.primary_contact_name as partner_poc,"
                . "partners.primary_contact_phone_1 as poc_contact,service_centres.primary_contact_email as service_center_email,partners.public_name as partner,"
                . "booking_details.assigned_vendor_id,employee.official_email as rm_email,employee.full_name as rm ,employee.phone as rm_contact, group_concat(distinct agent_filters.state) as am_state";
        $join['employee_relation'] = "FIND_IN_SET(booking_details.assigned_vendor_id,employee_relation.service_centres_id)";
        $join['partners'] = "partners.id = booking_details.partner_id";
        $join['agent_filters'] = "partners.id = agent_filters.entity_id";
        $join['service_centres'] = "service_centres.id = booking_details.assigned_vendor_id";
        $join['employee e'] = "e.id = agent_filters.agent_id";
        $join['employee'] = "employee.id = employee_relation.agent_id";
        $where['booking_details.booking_id'] = $bookingID;
        $where['agent_filters.entity_type'] = "247around";
        if($state_check) {
            $limitArray = array();
            $where['agent_filters.state'] = $booking_state[0]['state'];
        } else {
            $limitArray['length'] = 1;
            $limitArray['start'] = "";
        }
        $data = $this->My_CI->reusable_model->get_search_result_data("booking_details",$select,$where,$join,$limitArray,NULL,NULL,NULL,"agent_filters.agent_id");
        return $data;
    }
    
    function check_inventory_stock($inventory_id, $partner_id, $state, $assigned_vendor_id) {
        log_message('info', __METHOD__. " Inventory ID ". $inventory_id. " Partner ID ".$partner_id. "  Assigned vendor ID ". $assigned_vendor_id. " State ".$state);
        $response = array(); 

        $inventory_part_number = $this->My_CI->inventory_model->get_inventory_master_list_data('inventory_master_list.part_number,inventory_master_list.part_name, inventory_master_list.inventory_id, price, gst_rate,inventory_master_list.oow_around_margin', array('inventory_id' => $inventory_id));

        $partner_details = $this->My_CI->partner_model->getpartner_details("is_micro_wh,is_wh, is_defective_part_return_wh", array('partners.id' => $partner_id));
        $is_partner_wh = '';
        $is_micro_wh = '';
        if(!empty($partner_details)){
          $is_partner_wh = $partner_details[0]['is_wh'];
          $is_micro_wh = $partner_details[0]['is_micro_wh'];  
        }

        if (!empty($inventory_part_number)) {
            //Check Partner Works Micro
            if ($is_micro_wh == 1) {

                //check SF inventory stock
                $response = $this->_check_inventory_stock_with_micro($inventory_part_number, $state, $assigned_vendor_id);
                if (!empty($response)) {
                    //Defective Parts Return To
                    if ($partner_details[0]['is_defective_part_return_wh'] == 1) {
                        $wh_address_details = $this->get_247aroud_warehouse_in_sf_state($state);
                        if(!empty($wh_address_details)){
                            $response['defective_return_to_entity_type'] = $wh_address_details[0]['entity_type'];
                            $response['defective_return_to_entity_id'] = $wh_address_details[0]['entity_id'];
                        } else {
                            $response['defective_return_to_entity_type'] = _247AROUND_PARTNER_STRING;
                            $response['defective_return_to_entity_id'] = $partner_id;     
                        }
                       
                    } else {
                        $response['defective_return_to_entity_type'] = _247AROUND_PARTNER_STRING;
                        $response['defective_return_to_entity_id'] = $partner_id;                        
                    }
                }
                
                if (empty($response) && $is_partner_wh == 1) {
                    
                    $response = $this->_check_inventory_stock_with_micro($inventory_part_number, $state);
                    if(!empty($response)){
                        $response['defective_return_to_entity_type'] = $response['entity_type'];
                        $response['defective_return_to_entity_id'] = $response['entity_id'];
                    }
                    
                } 
                
            } else if ($is_partner_wh == 1) {
                
                $response = $this->_check_inventory_stock_with_micro($inventory_part_number, $state);
                if(!empty($response)){

                    $response['defective_return_to_entity_type'] = $response['entity_type'];
                    $response['defective_return_to_entity_id'] = $response['entity_id'];
                }
            }

        } else {

            return false;
        }

        if (empty($response) && !empty($inventory_part_number)) {
            $response['stock'] = false;
            $response['entity_id'] = $partner_id;
            $response['part_name'] = $inventory_part_number[0]['part_name'];
            $response['entity_type'] = _247AROUND_PARTNER_STRING;
            $response['gst_rate'] = $inventory_part_number[0]['gst_rate'];
            $response['estimate_cost'] = round($inventory_part_number[0]['price'] * ( 1 + $inventory_part_number[0]['gst_rate'] / 100), 0);
            $response['inventory_id'] = $inventory_id;
            $response['is_micro_wh'] = 0;    //
            $response['challan_approx_value'] = round($response['estimate_cost'] * ( 1 + $inventory_part_number[0]['oow_around_margin'] / 100), 0);
            $response['defective_return_to_entity_type'] = _247AROUND_PARTNER_STRING;
            $response['defective_return_to_entity_id'] = $partner_id;
            // if ($partner_details[0]['is_defective_part_return_wh'] == 1) {
            //     $wh_address_details = $this->get_247aroud_warehouse_in_sf_state($state);
            //     $response['defective_return_to_entity_type'] = $wh_address_details[0]['entity_type'];
            //     $response['defective_return_to_entity_id'] = $wh_address_details[0]['entity_id'];
            // } else {
            //     $response['defective_return_to_entity_type'] = _247AROUND_PARTNER_STRING;
            //     $response['defective_return_to_entity_id'] = $partner_id;
            // }
        }
        return $response;
    }

    
    
    

    
    function get_247aroud_warehouse_in_sf_state($state){
        $select = "contact_person.entity_id, contact_person.entity_type";
        $where1 = array('warehouse_state_relationship.state' => $state,'warehouse_details.entity_id' => _247AROUND, 
            'warehouse_details.entity_type' => _247AROUND_PARTNER_STRING);
        return $this->My_CI->inventory_model->get_warehouse_details($select,$where1,true);
    }

    function _check_inventory_stock_with_micro($inventory_part_number, $state, $service_center_id= ""){
        $response = array();
        $post['length'] = -1;
               
        $post['where'] = array('inventory_stocks.inventory_id' => $inventory_part_number[0]['inventory_id'],'inventory_stocks.entity_type' => _247AROUND_SF_STRING,'(inventory_stocks.stock - inventory_stocks.pending_request_count) > 0'=>NULL);
        if (!empty($service_center_id)) {
            $post['where']['inventory_stocks.entity_id'] = $service_center_id;
        } else {
            $post['where']['service_centres.is_wh'] = 1;
        }
        $select = '(inventory_stocks.stock - pending_request_count) As stock,inventory_stocks.entity_id,inventory_stocks.entity_type,inventory_stocks.inventory_id';
        $inventory_stock_details = $this->My_CI->inventory_model->get_inventory_stock_list($post,$select,array(),FALSE);
        
       
        if (empty($inventory_stock_details)) {
            $alternate_inventory_stock_details = $this->My_CI->inventory_model->get_alternate_inventory_stock_list($inventory_part_number[0]['inventory_id'], $service_center_id);
           
            if (!empty($alternate_inventory_stock_details)) {
                if (!empty($alternate_inventory_stock_details[0]['stocks']) && !empty($alternate_inventory_stock_details[0]['inventory_id'])) {
                    $inventory_part_number = $this->My_CI->inventory_model->get_inventory_master_list_data('inventory_master_list.part_number,inventory_master_list.part_name, '
                            . 'inventory_master_list.inventory_id, price, gst_rate,oow_around_margin', array('inventory_id' => $alternate_inventory_stock_details[0]['inventory_id']));

                    $inventory_stock_details = $alternate_inventory_stock_details;
                }
            }
            
        }
        
        if(!empty($inventory_stock_details)){
            if(!empty($service_center_id)){
                $response = array();
                $response['stock'] = TRUE;
                $response['entity_id'] = $service_center_id;
                $response['entity_type'] = _247AROUND_SF_STRING;
                $response['part_name'] = $inventory_part_number[0]['part_name'];
                $response['gst_rate'] = $inventory_part_number[0]['gst_rate'];
                $response['estimate_cost'] =round($inventory_part_number[0]['price'] *( 1 + $inventory_part_number[0]['gst_rate']/100), 0);
                $response['inventory_id'] = $inventory_part_number[0]['inventory_id'];
                $response['is_micro_wh'] = 1;
                $response['challan_approx_value'] = round($response['estimate_cost'] * ( 1 + $inventory_part_number[0]['oow_around_margin'] / 100), 0);
                
            } else {

                foreach($inventory_stock_details as $value){                    
                    $warehouse_details = $this->My_CI->inventory_model->get_warehouse_details('warehouse_state_relationship.state,contact_person.entity_id',
                            array('warehouse_state_relationship.state' => $state,'contact_person.entity_type' => _247AROUND_SF_STRING,
                                'contact_person.entity_id' => $value['entity_id'], 'service_centres.is_wh' => 1), true, true, true);
                    if(!empty($warehouse_details)){
                        $response = array();
                        $response['stock'] = TRUE;
                        $response['entity_id'] = $value['entity_id'];
                        $response['entity_type'] = _247AROUND_SF_STRING;
                        $response['part_name'] = $inventory_part_number[0]['part_name'];
                        $response['gst_rate'] = $inventory_part_number[0]['gst_rate'];
                        $response['estimate_cost'] =round($inventory_part_number[0]['price'] *( 1 + $inventory_part_number[0]['gst_rate']/100), 0);
                        $response['inventory_id'] = $inventory_part_number[0]['inventory_id'];
                        $response['is_micro_wh'] = 2;

                        $response['challan_approx_value'] = round($response['estimate_cost'] * ( 1 + $inventory_part_number[0]['oow_around_margin'] / 100), 0);
                        break;
                    }
                }
            }
        } 
        
        return $response;
            
    }
    function is_booking_valid_for_partner_panelty($request_type){
        $is_valid = 1;
        if(stripos($request_type, 'Out of Warranty') !== false) {
            $is_valid = 0;
        }
        if(stripos($request_type, 'Repeat') !== false) {
            $is_valid = 0;
        }
        if(stripos($request_type, 'Service Center Visit') !== false) {
            $is_valid = 0;
        }
        return $is_valid;
    }
    /*
     * This function is used to calculate tat between 2 dates by considering non working days of SF
     * if tat is 5 and there was an holiday in between then tat will be 4
     * If start date is holiday means booking assigned  on holiday then calculate tat from next day
     * If End date is holiday then don't consider it as holiday
     * @input - 1) $non_working_day - string of non_working day of sf comma seprated
     * 2) $startDate - Action start date
     * 3) $endDate - Action End Date
     *  @output -  final tat(Tat After all calculations)
     * 
     */
    function get_tat_with_considration_of_non_working_day($non_working_day,$startDate,$endDate){
         $holidayInTatArray = $nonWorkingDaysArray = array();
         log_message('info', __FUNCTION__ . "Start non_working_day = ".$non_working_day.", startDate = ".$startDate."end date= ".$endDate);
        //Create a week array to get week into days
        $weekArray = array("Monday"=>1,"Tuesday"=>2,"Wednesday"=>3,"Thursday"=>4,"Friday"=>5,"Saturday"=>6,"Sunday"=>7);
        // get day on start date
        $dayOfStartDate = date('w', strtotime($startDate));
        // get day on end date
        $dayOfEndDate = date('w', strtotime($endDate));
        // calculate normal  tat from start to end date without working days considration
        $tatDays = floor((strtotime($endDate) - strtotime($startDate))/(60 * 60 * 24));
        //Convert non working days string into array
        if($non_working_day){
        $nonWorkingDaysArray = explode(",",$non_working_day);
        }
        //Process all holidays through array, because holiday may be more then 1
        foreach($nonWorkingDaysArray as $nonWorkingDay){
            // Calculate days upto 1st holiday from start date
            $daysUptoHoliday =   $weekArray[$nonWorkingDay] - $dayOfStartDate;
            // If there was a holiday on start day then calculate days upto  holiday from next day
            if($dayOfStartDate ==  $weekArray[$nonWorkingDay]){
                $daysUptoHoliday =   $daysUptoHoliday -1;
            }
            // If day upto holiday is in negative means monday is holiday and start date is wednesday then 1-3 will be -ve so (7+(-2)) =5 , after 5 days there will be next holiday  
           if($daysUptoHoliday < 0){
               $daysUptoHoliday = 7 + $daysUptoHoliday;
           }
           // If tat is less then number of days upto holiday then take holiday as 0
           if($daysUptoHoliday > $tatDays){
               $holidayInTatArray[] = 0;
           }
           // If not then calculate number of hoildays between tat days
           else{     
               $holidayTemp = floor(($tatDays - $daysUptoHoliday)/7)+1;
               // If end date is a holiday then minus 1 day from holiday because don't considerd action day as holiday
               if($dayOfEndDate == $weekArray[$nonWorkingDay]){
                   $holidayTemp = $holidayTemp -1;
               }
               $holidayInTatArray[] = $holidayTemp;
           }
        }
        $finalTat = $tatDays - array_sum($holidayInTatArray);
        if($finalTat<0){
            $finalTat = 0;
        }
        log_message('info', __FUNCTION__ . "End finalTat = ".$finalTat);
        return $finalTat;
    }
    function get_faulty_booking_criteria($partner_id){
         //Where condition to get faulty booking criteria for partner
        $where_in['entity_id'] = array(_247AROUND,$partner_id);
        //Get Partner Data to calculate "is legs faulty"
        $tatFaultyBookingCriteriaTemp = $this->My_CI->reusable_model->get_search_result_data("tat_defactive_booking_criteria","*",NULL,NULL,NULL,NULL,$where_in,NULL,array());
        $count = count($tatFaultyBookingCriteriaTemp);
        foreach ($tatFaultyBookingCriteriaTemp as $values){
            if($values['entity_type'] == 'Vendor'){
                $this->tatFaultyBookingCriteria['Vendor'] = $values;
            }
            else{
                if($count == 2){
                    $this->tatFaultyBookingCriteria['Partner'] = $values;
                }
                else{
                     if($values['entity_type'] == 'Partner' && $values['entity_id'] != _247AROUND){
                         $this->tatFaultyBookingCriteria['Partner'] = $values;
                     }
                }
            }
        }
    }
    /*
     * This function is used to calculate TAT between diffrent legs of booking processing
     * leg_1:
     * With Spare - spare_request_date - initial_booking_date
     * Without Spare - service_center_closed_date - initial_booking_date
     * leg_2:
     * With Spare - 1) (spare cancelled) - spare_cancelled_date - service_center_closed date, 2) (spare completed) - spare receieved date - service_center_closed_date
     * leg_3:
     * With spare (spare completed)   - service_center_closed_date - defactive_part_shipped_date 
     * leg_4:
     * service_center_closed_date - around_completed_date
     */
    function process_booking_tat_on_completion($booking_id){
         log_message('info', __FUNCTION__ . "Start booking_id = ".$booking_id);
        //Get booking + spare data 
        //if spare not requested then all spare related fields will be blank
        $data = $this->My_CI->booking_model->get_booking_tat_required_data($booking_id);
        $this->get_faulty_booking_criteria($data[0]['partner_id']);
        //Set all variable as blank initiallly
        $tatArray['leg_2'] = $tatArray['leg_3'] = $tatArray['leg_4'] =NULL;
        $tatArray['applicable_on_partner'] = $tatArray['applicable_on_sf'] = 1;
        //Process data through loop
        foreach($data as $values){
            //If sf_closed_date blank then consider around_completion_date as sf_completion_date
            if(!$values['sf_closed_date']){
                log_message('info', __FUNCTION__ . "SF closed date was null so consider close date as sf date. sf_date= ".$values['around_closed_date']);
                $values['sf_closed_date'] =  $values['around_closed_date'];
             }
             if(!$values['initial_booking_date']){
                log_message('info', __FUNCTION__ . "SF Initial booking date was null so consider create date as initial_booking_date. initial_booking_date= ".$values['around_closed_date']);
                $values['initial_booking_date'] =  $values['create_date'];
             }
            // Leg 4 will be TAT between around closed date and sf closed date
            log_message('info', __FUNCTION__ . "leg_4 = ".$booking_id);
            $tatArray['leg_4'] = $this->get_tat_with_considration_of_non_working_day($values['non_working_days'],$values['sf_closed_date'],$values['around_closed_date']);
            // IF Booking is without Spare Part then calculate leg_1
            //leg_2,leg_3 is not applicable for this case
            if(!$values['spare_id']){
                $tatArray['leg_1'] = $this->get_tat_with_considration_of_non_working_day($values['non_working_days'],$values['initial_booking_date'],$values['sf_closed_date']);
            }
            // Else Calculate leg_2,leg_3,leg_4 because leg_1 is already updated when spare part request was made
            else{
                // IF spare_receieved_date blank then consider spare_receieved_date as a day before service center closed date
                if(!$values['spare_receieved_date']){
                    $newdateb = strtotime ( '-1 day' , strtotime ( $values['sf_closed_date'] ) ) ;
                    $values['spare_receieved_date'] = date ( 'Y-m-d' , $newdateb);
                    log_message('info', __FUNCTION__ . "spare_receieved_date was null so minus a day from sf_closed_date and consider it as spare_receieved_date. spare_receieved_date= ".$values['spare_receieved_date']);
                }
                // IF spare_cancelled_date blank then consider spare_cancelled_date as a day before service center closed date
                if(!$values['spare_cancelled_date']){
                    $newdatec = strtotime ( '-1 day' , strtotime ( $values['sf_closed_date'] ) ) ;
                    $values['spare_cancelled_date'] = date ( 'Y-m-d' , $newdatec);
                     log_message('info', __FUNCTION__ . "spare_cancelled_date was null so minus a day from sf_closed_date and consider it as spare_cancelled_date. spare_cancelled_date= ".$values['spare_receieved_date']);
                }
                // IF defactive_part_shipped_date blank then consider defactive_part_shipped_date as a day after sf_closed_date
                if(!$values['defactive_part_shipped_date']){
                    $newdated = strtotime ( '+1 day' , strtotime ( $values['sf_closed_date'] ) ) ;
                    $values['defactive_part_shipped_date'] = date ( 'Y-m-d' , $newdated);
                     log_message('info', __FUNCTION__ . "defactive_part_shipped_date was null so add a day from sf_closed_date and consider it as defactive_part_shipped_date. defactive_part_shipped_date= ".$values['defactive_part_shipped_date']);
                }
                //If spare was cancelled then leg_2 will be between cancellation date and service center closed date
                //leg_3 is not applicable for this case
                if($values['spare_status'] == 'Cancelled'){
                     $tatArray['leg_2'] = $this->get_tat_with_considration_of_non_working_day($values['non_working_days'],$values['spare_cancelled_date'],$values['sf_closed_date']);
                }
                //IF Spare flow was completed then leg_2 will be between spare_receieved_date and service_center_closed_date
                //leg_3 is TAT between  service center cloased date and defactive part shipped
                else{
                    $tatArray['leg_2'] = $this->get_tat_with_considration_of_non_working_day($values['non_working_days'],$values['spare_receieved_date'],$values['sf_closed_date']);
                    $tatArray['leg_3'] = $this->get_tat_with_considration_of_non_working_day($values['non_working_days'],$values['sf_closed_date'],$values['defactive_part_shipped_date']);
                }
            }
            $tatArray['is_upcountry'] =  $values['is_upcountry'];
            if($values['spare_id']){
                $tatArray['is_leg_2_faulty_for_partner'] = $this->is_booking_faulty(TRUE,$values['is_upcountry'],"leg_2",$tatArray['leg_2'],"Partner");
                $tatArray['is_leg_3_faulty_for_partner'] = $this->is_booking_faulty(TRUE,$values['is_upcountry'],"leg_3",$tatArray['leg_3'],"Partner");
                $tatArray['is_leg_4_faulty_for_partner'] = $this->is_booking_faulty(TRUE,$values['is_upcountry'],"leg_4",$tatArray['leg_4'],"Partner");  
                $tatArray['is_leg_2_faulty_for_vendor'] = $this->is_booking_faulty(TRUE,$values['is_upcountry'],"leg_2",$tatArray['leg_2'],"Vendor");
                $tatArray['is_leg_3_faulty_for_vendor'] = $this->is_booking_faulty(TRUE,$values['is_upcountry'],"leg_3",$tatArray['leg_2'],"Vendor");
                $tatArray['is_leg_4_faulty_for_vendor'] = $this->is_booking_faulty(TRUE,$values['is_upcountry'],"leg_4",$tatArray['leg_2'],"Vendor");
            }
            else{
                $tatArray['is_leg_1_faulty_for_partner'] = $this->is_booking_faulty($values['spare_id'],$values['is_upcountry'],"leg_1",$tatArray['leg_1'],"Partner");
                $tatArray['is_leg_1_faulty_for_vendor'] = $this->is_booking_faulty($values['spare_id'],$values['is_upcountry'],"leg_1",$tatArray['leg_1'],"Vendor");
            }
            $tatArray['booking_id'] = $booking_id;
            $tatArray['partner_id'] = $values['partner_id'];
            $tatArray['applicable_on_partner'] = $this->is_booking_valid_for_partner_panelty($values['request_type']);
            $tatArray['sf_closed_date'] = $values['sf_closed_date'];
            $tatArray['around_closed_date'] = $values['around_closed_date'];
            if (stripos($values['request_type'], 'Repair') !== false || stripos($values['request_type'], 'Repeat') !== false) {
                $requestType = '1';
            }
            else{
                $requestType = '0';
            }
            $tatArray['request_type'] = $requestType;
            if($values['spare_id']){
                $this->My_CI->reusable_model->update_table("booking_tat",$tatArray,array("booking_id"=>$booking_id,"spare_id"=>$values['spare_id']));
            }
            else{
                $is_exists = $this->My_CI->reusable_model->get_search_result_data("booking_tat","1",array("booking_id"=>$booking_id),NULL,NULL,NULL,NULL,NULL,array());
                if(!empty($is_exists)){
                  $this->My_CI->reusable_model->update_table("booking_tat",$tatArray,array("booking_id"=>$booking_id,"spare_id"=>NULL));
                }
                else{
                  $this->My_CI->reusable_model->insert_into_table("booking_tat",$tatArray);
                }
            }
        }
        log_message('info', __FUNCTION__ . "End booking_id = ".$booking_id);
    }
    function process_booking_tat_on_spare_request($booking_id,$spare_id){
        log_message('info', __FUNCTION__ . "Start booking_id = ".$booking_id.", spare_id = ".$spare_id);
        $data['booking_id'] = $booking_id;
        $data['spare_id'] = $spare_id;
        $bookingData = $this->My_CI->reusable_model->get_search_result_data("booking_details","booking_details.is_upcountry,booking_details.partner_id,service_centres.non_working_days,"
                . "STR_TO_DATE(booking_details.initial_booking_date,'%d-%m-%Y') as initial_booking_date,booking_details.request_type,booking_details.partner_id",
                array("booking_id"=>$booking_id),array("service_centres"=>"service_centres.id = booking_details.assigned_vendor_id"),NULL,NULL,NULL,NULL,array());
        $this->get_faulty_booking_criteria($bookingData[0]['partner_id']);
        $data['leg_1'] = $this->get_tat_with_considration_of_non_working_day($bookingData[0]['non_working_days'],$bookingData[0]['initial_booking_date'],date("Y-m-d"));
        $data['applicable_on_partner'] = $this->is_booking_valid_for_partner_panelty($bookingData[0]['request_type']);
        $data['applicable_on_sf'] = 1;
        $data['is_upcountry'] =  $bookingData[0]['is_upcountry'];
        $data['is_leg_1_faulty_for_partner'] = $this->is_booking_faulty($spare_id,$bookingData[0]['is_upcountry'],"leg_1",$data['leg_1'],"Partner");
        $data['is_leg_1_faulty_for_vendor'] = $this->is_booking_faulty($spare_id,$bookingData[0]['is_upcountry'],"leg_1",$data['leg_1'],"Vendor");
        $data['partner_id'] = $bookingData[0]['partner_id'];
        $data['request_type'] = "1";
        $this->My_CI->reusable_model->insert_into_table("booking_tat",$data);
        log_message('info', __FUNCTION__ . "End booking_id = ".$booking_id.", spare_id = ".$spare_id);
    }
   function is_booking_faulty($spare_id,$isUpcountry,$leg,$tat,$entity_type) {
       if(!empty($this->tatFaultyBookingCriteria)){
            if($spare_id && $isUpcountry){
                $tatLimit = $this->tatFaultyBookingCriteria[$entity_type]['with_repair_upcountry_'.$leg];
            }
            else if($spare_id && !($isUpcountry)){
                $tatLimit = $this->tatFaultyBookingCriteria[$entity_type]['with_repair_non_upcountry_'.$leg];
            }
            else if(!($spare_id) && !($isUpcountry)){
                $tatLimit = $this->tatFaultyBookingCriteria[$entity_type]['without_repair_non_upcountry'];
            }
            else if(!($spare_id) && $isUpcountry){
                $tatLimit = $this->tatFaultyBookingCriteria[$entity_type]['without_repair_upcountry'];
            }
            if($tat>$tatLimit){
                return 1;
            }
            else{
                return 0;
            }
       }
       else{
           return 1;
       }
    }
    function reject_booking_from_review($postData){
        log_message('info', __FUNCTION__. " POST ". json_encode($postData, true));
        $booking_id =$postData['booking_id'];
        $admin_remarks = $postData['admin_remarks'];
        $data['internal_status'] = _247Around_Rejected_SF_Update;
        $data['current_status'] = _247AROUND_PENDING;
        $data['update_date'] = date("Y-m-d H:i:s");
        $data['serial_number'] = "";
        $data['service_center_remarks'] = NULL; 
        $data['booking_date'] = $data['booking_timeslot'] = NUll;
        $data['closed_date'] = NULL;
        $data['service_charge'] = $data['additional_service_charge'] = $data['parts_cost'] = "0.00";
        $data['admin_remarks'] = date("F j") . "  :-" . $admin_remarks;
        log_message('info', __FUNCTION__ . " Booking_id " . $booking_id . " Update service center action table: " . print_r($data, true));
        $this->My_CI->vendor_model->update_service_center_action($booking_id, $data);
        //Send Push Notification
        $b = $this->My_CI->booking_model->get_bookings_count_by_any("booking_details.partner_id, assigned_vendor_id",array('booking_details.booking_id' => $booking_id));
        //Get RM For Assigned Vendor
        $rmArray = $this->My_CI->vendor_model->get_rm_sf_relation_by_sf_id($b[0]['assigned_vendor_id']);
        if(!empty($rmArray)){
            $receiverArray['employee']= array($rmArray[0]['agent_id']);
            $receiverArray['vendor']= array($b[0]['assigned_vendor_id']);
            $notificationTextArray['msg'] = array($booking_id,"Rejected");
            $notificationTextArray['title'] = array("Rejected");
            $this->My_CI->push_notification_lib->create_and_send_push_notiifcation(BOOKING_UPDATED_BY_247AROUND,$receiverArray,$notificationTextArray);
        }
        $partner_status = $this->My_CI->booking_utilities->get_partner_status_mapping_data(_247AROUND_PENDING, _247Around_Rejected_SF_Update , $b[0]['partner_id'], $booking_id);
        $actor = ACTOR_REJECT_FROM_REVIEW;
        $next_action = REJECT_FROM_REVIEW_NEXT_ACTION;
        if (!empty($partner_status)) {
            $booking['partner_current_status'] = $partner_status[0];
            $booking['partner_internal_status'] = $partner_status[1];
            $booking['internal_status'] = "Rejected From Review";
            $booking['rejected_by'] = $postData['rejected_by'];
            $booking['is_in_process'] = 0;
            $actor = $booking['actor'] = $partner_status[2];
            $next_action = $booking['next_action'] = $partner_status[3];
            $booking['service_center_closed_date'] = NULL;
            $data['cancellation_reason'] = NULL;
            $this->My_CI->booking_model->update_booking($booking_id, $booking);
        }
        if($postData['rejected_by'] == _247AROUND){
            $this->My_CI->notify->insert_state_change($booking_id, "Rejected", "InProcess_Completed", $admin_remarks, $this->My_CI->session->userdata('id'), $this->My_CI->session->userdata('employee_id'), 
                $actor,$next_action,_247AROUND);
        }
        else{
            $this->My_CI->notify->insert_state_change($booking_id, "Rejected", "InProcess_Completed", $admin_remarks, $this->My_CI->session->userdata('agent_id'), $this->My_CI->session->userdata('partner_name'), 
                $actor,$next_action,$postData['rejected_by']);
        }
    }
    function get_review_bookings_for_partner($partnerID,$booking_id = NULL,$structuredData = 1,$limit = REVIEW_LIMIT_BEFORE){
         $finalArray = array();
        $whereIN = array();
        $statusData = $this->My_CI->reusable_model->get_search_result_data("partners","partners.booking_review_for,partners.review_time_limit",array("booking_review_for IS NOT NULL"=>NULL,"id"=>$partnerID),NULL,NULL,NULL,NULL,NULL,array());
        if(!empty($statusData)){
            $where['booking_details.partner_id'] = $partnerID;
            $statusArray = explode(",",$statusData[0]['booking_review_for']);
            $whereIN['service_center_booking_action.internal_status'] = array("Completed","Cancelled");
            if($limit == REVIEW_LIMIT_BEFORE){
              $where['DATEDIFF(CURRENT_TIMESTAMP,  service_center_booking_action.closed_date)<='.$statusData[0]['review_time_limit']] = NULL;
            }
            else if($limit == REVIEW_LIMIT_AFTER){
              $where['DATEDIFF(CURRENT_TIMESTAMP,  service_center_booking_action.closed_date)>'.$statusData[0]['review_time_limit']] = NULL;
            }
            else{
                $days = $statusData[0]['review_time_limit'] - $limit;
                $where['(DATEDIFF(CURRENT_TIMESTAMP,  service_center_booking_action.closed_date)>='.$days
                   . ' AND DATEDIFF(CURRENT_TIMESTAMP,  service_center_booking_action.closed_date)<='.$statusData[0]['review_time_limit'].')'] = NULL;
            }
            if($booking_id){
                $where['booking_details.booking_id'] = $booking_id;
            }
            $where['booking_details.amount_due'] = 0;
            $where['service_center_booking_action.current_status'] = 'InProcess';
            $where['booking_details.is_in_process'] = 0;
            $tempData = $this->My_CI->partner_model->get_booking_review_data($where,$whereIN,$booking_id);
            if(!empty($tempData)){
                foreach($tempData as $values){
                    $is_considrable = TRUE;
                    if(count($statusArray) == 1 && $statusArray[0] == 'Cancelled'){
                        if (strpos($values['combined_status'], 'Completed') !== false) {
                            $is_considrable = FALSE;
                        }
                    }
                    if($is_considrable){
                            $finalArray[$values['booking_id']]['appliance_brand'] = $values['appliance_brand'];
                        $finalArray[$values['booking_id']]['services'] = $values['services'];
                        $finalArray[$values['booking_id']]['request_type']= $values['request_type'];
                        $finalArray[$values['booking_id']]['internal_status'] = $values['internal_status'];
                        $finalArray[$values['booking_id']]['name'] = $values['name'];
                        $finalArray[$values['booking_id']]['booking_primary_contact_no'] = $values['booking_primary_contact_no'];
                        $finalArray[$values['booking_id']]['city'] = $values['city'];
                        $finalArray[$values['booking_id']]['state'] = $values['state'];
                        $finalArray[$values['booking_id']]['initial_booking_date'] = $values['initial_booking_date'];
                        $finalArray[$values['booking_id']]['age'] = $values['age'];
                        $finalArray[$values['booking_id']]['is_upcountry'] = $values['is_upcountry'];
                        $finalArray[$values['booking_id']]['booking_jobcard_filename'] = $values['booking_jobcard_filename'];
                        $finalArray[$values['booking_id']]['internal_status'] = $values['internal_status'];
                        $finalArray[$values['booking_id']]['amount_due'] = $values['amount_due'];
                        $finalArray[$values['booking_id']]['partner_id'] = $values['partner_id'];
                        $finalArray[$values['booking_id']]['cancellation_reason'] = $values['cancellation_reason'];
                    }
                }
            }
            else{
                 $data= array();
            }
            if($structuredData == 0){
                return $tempData;
            }
            else{
                return $finalArray;
            }
        }
    }
    function reopen_booking($booking_id, $status){
            $data['booking_date'] = date('d-m-Y', strtotime($this->My_CI->input->post('booking_date')));
            $data['booking_timeslot'] = $this->My_CI->input->post('booking_timeslot');
            $data['current_status'] = _247AROUND_PENDING;
            $data['internal_status'] = "Booking Opened From " . $status;
            $data['update_date'] = date("Y-m-d H:i:s");
            $data['cancellation_reason'] = NULL;
            $data['closed_date'] = NULL;
            $data['vendor_rating_stars'] = NULL;
            $data['vendor_rating_comments'] = NULL;
            $data['amount_paid'] = NULL;
            $data['rating_stars'] = NULL;
            $data['rating_comments'] = NULL;
            $data['closing_remarks'] = NULL;
            $data['booking_jobcard_filename'] = NULL;
            $data['mail_to_vendor'] = 0;
            $data['service_center_closed_date'] = NULL;
            //$data['booking_remarks'] = $this->input->post('reason');
            //check partner status from partner_booking_status_mapping table  
            $partner_id = $this->My_CI->input->post('partner_id');
            $actor = $next_action = 'not_define';
            $partner_status = $this->My_CI->booking_utilities->get_partner_status_mapping_data($data['current_status'], $data['internal_status'], $partner_id, $booking_id);
            if (!empty($partner_status)) {
                $data['partner_current_status'] = $partner_status[0];
                $data['partner_internal_status'] = $partner_status[1];
                $actor = $data['actor'] = $partner_status[2];
                $next_action = $data['next_action'] = $partner_status[3];
            }

            if ($data['booking_timeslot'] == "Select") {
                echo "Please Select Booking Timeslot.";
            } else {
                log_message('info', __FUNCTION__ . " Convert booking, data : " . print_r($data, true));
                $this->My_CI->booking_model->update_booking($booking_id, $data);
                $assigned_vendor_id = $this->My_CI->input->post("assigned_vendor_id");
                if (!empty($assigned_vendor_id)) {
                    $service_center_data['internal_status'] = _247AROUND_PENDING;
                    $service_center_data['current_status'] = _247AROUND_PENDING;
                    $service_center_data['update_date'] = date("Y-m-d H:i:s");
                    $service_center_data['serial_number'] = "";
                    $service_center_data['cancellation_reason'] = NULL;
                    $service_center_data['reschedule_reason'] = NULL;
                    $service_center_data['service_center_remarks'] = $service_center_data['admin_remarks'] = NULL;
                    $service_center_data['booking_date'] = $service_center_data['booking_timeslot'] = NUll;
                    $service_center_data['closed_date'] = NULL;
                    $service_center_data['service_charge'] = $service_center_data['additional_service_charge'] = $service_center_data['parts_cost'] = "0.00";
                    
                    if($this->My_CI->input->post('admin_remarks')){
                        $service_center_data['admin_remarks'] = $remarks = $this->My_CI->input->post('admin_remarks');
                    }else{
                        $service_center_data['admin_remarks'] = $remarks = NULL;
                    }
                    log_message('info', __FUNCTION__ . " Convert booking, Service center data : " . print_r($service_center_data, true));
                    $this->My_CI->vendor_model->update_service_center_action($booking_id, $service_center_data);
                    //if booking status is cancelled then do action on inventory
                    if ($status === _247AROUND_CANCELLED) {
                        //get the unit details data and update the inventory stock
                        $booking_unit_details = $this->My_CI->reusable_model->get_search_query('booking_unit_details', 'booking_unit_details.price_tags,booking_unit_details.appliance_capacity', array('booking_unit_details.booking_id' => $booking_id, "booking_unit_details.price_tags like '%" . _247AROUND_WALL_MOUNT__PRICE_TAG . "%'" => NULL), NULL, NULL, NULL, NULL, NULL)->result_array();
                        if (!empty($booking_unit_details)) {
                            //process each unit if price tag is wall mount
                            foreach ($booking_unit_details as $value) {
                                $match = array();
                                //get the size from the capacity to know the part number
                                preg_match('/[0-9]+/', $value['appliance_capacity'], $match);
                                if (!empty($match)) {
                                    if ($match[0] <= 32) {
                                        $data['part_number'] = LESS_THAN_32_BRACKETS_PART_NUMBER;
                                    } else if ($match[0] > 32) {
                                        $data['part_number'] = GREATER_THAN_32_BRACKETS_PART_NUMBER;
                                    }

                                    $data['receiver_entity_id'] = $assigned_vendor_id;
                                    $data['receiver_entity_type'] = _247AROUND_SF_STRING;
                                    $data['stock'] = -1;
                                    $data['booking_id'] = $booking_id;
                                    if($this->My_CI->session->userdata('id')){
                                        $data['agent_id'] = $this->My_CI->session->userdata('id');
                                    }
                                    else{
                                        $data['agent_id'] = _247AROUND_DEFAULT_AGENT;
                                    }
                                    $data['agent_type'] = _247AROUND_EMPLOYEE_STRING;
                                    $this->My_CI->miscelleneous->process_inventory_stocks($data);
                                }
                            }
                        }
                    }
                }


                $unit_details['booking_status'] = _247AROUND_PENDING;
                $unit_details['vendor_to_around'] = "0.00";
                $unit_details['around_to_vendor'] = "0.00";
                $unit_details['ud_closed_date'] = NULL;

                log_message('info', __FUNCTION__ . " Convert Unit Details - data : " . print_r($unit_details, true));

                $this->My_CI->booking_model->update_booking_unit_details($booking_id, $unit_details);

                $spare = $this->My_CI->partner_model->get_spare_parts_by_any("spare_parts_details.id, spare_parts_details.old_status, requested_inventory_id, "
                        . "shipped_inventory_id", array('booking_id' => $booking_id), false);
                foreach ($spare as $sp) {
                    if($sp['old_status'] == SPARE_PARTS_REQUESTED ){
                        
                        if(!empty($sp['requested_inventory_id'])){
                            $sf_state = $this->My_CI->vendor_model->getVendorDetails("service_centres.state", array('service_centres.id' => $assigned_vendor_id));
                            $stock =$this->My_CI->miscelleneous->check_inventory_stock($sp['requested_inventory_id'], $partner_id, $sf_state[0]['state'], $assigned_vendor_id);
                            if(!empty($stock)){

                                $this->My_CI->service_centers_model->update_spare_parts(array('id' => $sp['id']), 
                                        array('status' => $sp['old_status'], 'entity_type' => $stock['entity_type'],
                                            'is_micro_wh' => $stock['is_micro_wh'],
                                            'defective_return_to_entity_type' => $stock['defective_return_to_entity_type'],
                                            'defective_return_to_entity_id' => $stock['defective_return_to_entity_id'],
                                            'partner_id' => $stock['entity_id']));
                                if($stock['entity_type'] == _247AROUND_SF_STRING){
                                    $this->My_CI->inventory_model->update_pending_inventory_stock_request($stock['entity_type'], $stock['entity_id'], $sp['requested_inventory_id'], 1);

                                }
                                
                                
                            } else {
                                //Update Spare parts details table
                               $this->My_CI->service_centers_model->update_spare_parts(array('id' => $sp['id']), array('status' => $sp['old_status'],
                                   "entity_type" => _247AROUND_PARTNER_STRING, "partner_id" => $partner_id));
                            }
                        } else {
                           //Update Spare parts details table
                          $this->My_CI->service_centers_model->update_spare_parts(array('id' => $sp['id']), array('status' => $sp['old_status'])); 
                        }
                        
                        $this->My_CI->vendor_model->update_service_center_action($booking_id, array('current_status' => 'InProcess', 'internal_status' => SPARE_PARTS_REQUIRED));
                    } else if(empty($sp['requested_inventory_id'])){
                        $this->My_CI->service_centers_model->update_spare_parts(array('id' => $sp['id']), array('status' => $sp['old_status']));
                    } 
                }
                // Update Engineer Action table Status When Booking Opened
                $en_where = array("engineer_booking_action.booking_id" => $booking_id);
                $this->My_CI->engineer_model->update_engineer_table(array("current_status" => _247AROUND_PENDING, "internal_status" =>_247AROUND_PENDING), $en_where);
         
                if($this->My_CI->session->userdata('id')){
                    $agentID = $this->My_CI->session->userdata('id');
                    $agentName = $this->My_CI->session->userdata('employee_id');
                }
                else{
                     $agentID = _247AROUND_DEFAULT_AGENT;
                     $agentName = _247AROUND_DEFAULT_AGENT_NAME;
                }
                //Log this state change as well for this booking          
                $this->My_CI->notify->insert_state_change($booking_id, _247AROUND_PENDING, $status, $remarks, $agentID, $agentName,$actor,$next_action, _247AROUND);
                if (!empty($assigned_vendor_id)) {

                    $up_flag = 1;

                    $url = base_url() . "employee/vendor/update_upcountry_and_unit_in_sc/" . $booking_id . "/" . $up_flag;
                    $async_data['booking'] = array();
                    $this->My_CI->asynchronous_lib->do_background_process($url, $async_data);

                    $this->My_CI->booking_utilities->lib_send_mail_to_vendor($booking_id, "");
                } else {
                    $this->My_CI->booking_utilities->lib_prepare_job_card_using_booking_id($booking_id);
                }

                $url = base_url() . "employee/do_background_process/send_sms_email_for_booking";
                $send['booking_id'] = $booking_id;
                $send['state'] = "OpenBooking";
                $this->My_CI->asynchronous_lib->do_background_process($url, $send);

                log_message('info', $status . ' Booking Opened - Booking id: ' . $booking_id . " Opened By: " . $this->My_CI->session->userdata('employee_id') . " => " . print_r($data, true));

                redirect(base_url() . DEFAULT_SEARCH_PAGE);
            }
    }
    function get_posible_parent_booking(){
        $contact = $this->My_CI->input->post('contact');
        $service_id = $this->My_CI->input->post('service_id');
        $partnerID = $this->My_CI->input->post('partnerID');
        $dayDiff = $this->My_CI->input->post('day_diff');
        $bookingsArray = $this->My_CI->booking_model->get_posible_parent_booking_id($contact,$service_id,$partnerID,$dayDiff);
        $count = count($bookingsArray);
        if($count == 1){
            $resultArray['html'] = $bookingsArray[0]['booking_id'];
            $resultArray['status'] =_ONE_REPEAT_BOOKING_FLAG;
        }
        else if($count == 0){
            $resultArray['status'] = _NO_REPEAT_BOOKING_FLAG;
        }
        else{
            $html = '<table class="table">
  <thead>
    <tr>
      <th scope="col">Booking ID</th>
      <th scope="col">Appliance</th>
      <th scope="col">Brand</th>
      <th scope="col">Capacity</th>
      <th scope="col">Status</th>
      <th scope="col">Closed Date</th>
      <th scope="col"></th>
    </tr>
  </thead><tbody>';
    foreach($bookingsArray as $bookingDetails){
        $html .= '<tr>
      <td>'.$bookingDetails['booking_id'].'</td>
      <td>'.$bookingDetails['services'].'</td>
      <td>'.$bookingDetails['brand'].'</td>
      <td>'.$bookingDetails['capacity'].'</td>
      <td>'.$bookingDetails['current_status'].'</td>
      <td>'.$bookingDetails['closed_date'].'</td>
      <td><input type="radio" name = "parent_booking_id_options" id="'.$bookingDetails['booking_id'].'" onclick = "parentBooking(this.id)""></td>
    </tr>';
    }
    $html .= '</tbody></table>'; 
    $resultArray['status'] = _MULTIPLE_REPEAT_BOOKING_FLAG;
    $resultArray['html'] = $html;
        }
        echo json_encode($resultArray);
    }
    function process_if_pincode_valid($pincode,$state,$city){
        log_message('info', __METHOD__ . "=>Start"."Pincode =".$pincode);
         // Insert State City in India Pincode
        $tempArray['district'] = $city;
        $tempArray['taluk'] = $city;
        $tempArray['region'] = $city;
        $tempArray['state'] = $state;
        $tempArray['pincode'] = $pincode;
        $insertArray[] = $tempArray;  
        $this->My_CI->vendor_model->insert_india_pincode_in_batch($insertArray);
        log_message('info', __METHOD__ . "=>End"."Pincode =".$pincode);
    }
    function google_map_address_api($pincode){
        log_message('info', __METHOD__ . "=>Start"."Pincode =".$pincode);
        $request = "https://maps.google.com/maps/api/geocode/json?address=".$pincode."&sensor=false&region=India&key=".GOOGLE_MAPS_API_KEY;
        $ch = curl_init();
        curl_setopt_array(
        $ch, array(
        CURLOPT_URL =>$request,
        CURLOPT_RETURNTRANSFER => true
        ));
       $output = curl_exec($ch);
       log_message('info', __METHOD__ . "=>End"."Pincode =".$pincode." , Response - ".$output);
       // $output = '{ "results" : [ { "address_components" : [ { "long_name" : "110051", "short_name" : "110051", "types" : [ "postal_code" ] }, { "long_name" : "New Delhi", "short_name" : "New Delhi", "types" : [ "locality", "political" ] }, { "long_name" : "Delhi", "short_name" : "DL", "types" : [ "administrative_area_level_1", "political" ] }, { "long_name" : "India", "short_name" : "IN", "types" : [ "country", "political" ] } ], "formatted_address" : "New Delhi, Delhi 110051, India", "geometry" : { "bounds" : { "northeast" : { "lat" : 28.66559119999999, "lng" : 77.29854069999999 }, "southwest" : { "lat" : 28.6433122, "lng" : 77.2725126 } }, "location" : { "lat" : 28.6569035, "lng" : 77.28229229999999 }, "location_type" : "APPROXIMATE", "viewport" : { "northeast" : { "lat" : 28.66559119999999, "lng" : 77.29854069999999 }, "southwest" : { "lat" : 28.6433122, "lng" : 77.2725126 } } }, "place_id" : "ChIJ85SOHWD7DDkRI-0i7DDZy-M", "types" : [ "postal_code" ] } ], "status" : "OK" }';
        return $output;
    }
    /**
     * @desc This function is used to send SMS to customer/Dealer when SF requested new parts from partner
     * @param String $part_type
     * @param String $booking_id
     */
    function send_spare_requested_sms_to_customer($part_type, $booking_id, $sms_tag){
        if(!empty($booking_id)){
            $booking_details = $this->My_CI->booking_model->getbooking_history($booking_id);
            if(!empty($booking_details)){
                $sms['tag'] = $sms_tag;
                $sms['phone_no'] = $booking_details[0]['booking_primary_contact_no'];
                $sms['smsData']['part_type'] = $part_type;
                $sms['smsData']['booking_id'] = $booking_id;
                if($booking_details[0]['partner_id'] == VIDEOCON_ID){
                    $sms['smsData']['cc_number'] = "Call 0120-4500600";
                }
                else{
                    $sms['smsData']['cc_number'] = _247AROUND_CALLCENTER_NUMBER;
                }
                $sms['booking_id'] = $booking_id;
                $sms['type'] = "user";
                $sms['type_id'] = $booking_details[0]['user_id'];
                $this->My_CI->notify->send_sms_msg91($sms);
                
                
                if(!empty($booking_details[0]['dealer_id'])){
                   $dealer_details =  $this->My_CI->dealer_model->get_dealer_details('dealer_phone_number_1', array('dealer_id' => $booking_details[0]['dealer_id']));
                   if(!empty($dealer_details)){
                        $sms1['tag'] = SPARE_REQUESTED_DEALER_SMS_TAG;
                        $sms1['phone_no'] = $dealer_details[0]['dealer_phone_number_1'];
                        $sms1['smsData']['part_type'] = $part_type;
                        $sms1['smsData']['user_name'] = $booking_details[0]['name'];
                        $sms1['smsData']['booking_id'] = $booking_id;
                        $sms1['booking_id'] = $booking_id;
                        $sms1['type'] = "dealer";
                        $sms1['type_id'] = $booking_details[0]['dealer_id'];
                        $this->My_CI->notify->send_sms_msg91($sms1);
                   }
                }
            }
        }
    }
    /**
     * @desc This function is used to send sms to Customer and dealer when new part delivered to SF
     * @param int $spare_id
     * @param Strng $booking_id
     */
    function send_spare_delivered_sms_to_customer($spare_id, $booking_id){
        if(!empty($booking_id)){
            $booking_details = $this->My_CI->booking_model->getbooking_history($booking_id);
            if(!empty($booking_details)){
                $getsparedata = $this->My_CI->partner_model->get_spare_parts_by_any("spare_parts_details.parts_requested_type", array("spare_parts_details.id" =>$spare_id));
                $part_type = $getsparedata[0]['parts_requested_type'];
                $sms['tag'] = SPARE_DELIVERED_CUSTOMER_SMS_TAG;
                $sms['phone_no'] = $booking_details[0]['booking_primary_contact_no'];
                $sms['smsData']['part_type'] = $part_type;
                $sms['smsData']['booking_id'] = $booking_id;
                if($booking_details[0]['partner_id'] == VIDEOCON_ID){
                    $sms['smsData']['cc_number'] = "Call 0120-4500600";
                }
                else{
                    $sms['smsData']['cc_number'] = _247AROUND_CALLCENTER_NUMBER;
                }
                $sms['booking_id'] = $booking_id;
                $sms['type'] = "user";
                $sms['type_id'] = $booking_details[0]['user_id'];
                $this->My_CI->notify->send_sms_msg91($sms);
                
                
                if(!empty($booking_details[0]['dealer_id'])){
                   $dealer_details =  $this->My_CI->dealer_model->get_dealer_details('dealer_phone_number_1', array('dealer_id' => $booking_details[0]['dealer_id']));
                   if(!empty($dealer_details)){
                        $sms1['tag'] = SPARE_DELIVERED_DEALER_SMS_TAG;
                        $sms1['phone_no'] = $dealer_details[0]['dealer_phone_number_1'];
                        $sms1['smsData']['part_type'] = $part_type;
                        $sms1['smsData']['user_name'] = $booking_details[0]['name'];
                        $sms1['smsData']['booking_id'] = $booking_id;
                        $sms1['booking_id'] = $booking_id;
                        $sms1['type'] = "dealer";
                        $sms1['type_id'] = $booking_details[0]['dealer_id'];
                        $this->My_CI->notify->send_sms_msg91($sms1);
                   }
                }
            }
        }
    }
    
    /**
     * @desc This function is used to Create new micro-warehouse
     * @param array $data
     * @param array $wh_on_of_data
     */
    function create_micro_warehouse($data, $wh_on_of_data) {
        $select = 'partners.id,micro_warehouse_state_mapping.state, micro_warehouse_state_mapping.micro_warehouse_charges';
        $micro_wh_mapping_list = $this->My_CI->inventory_model->get_micro_wh_mapping_list(array('micro_warehouse_state_mapping.vendor_id' => $data['vendor_id'], 'partners.id' => $data['partner_id']), $select);
        if (empty($micro_wh_mapping_list)) {
            $this->My_CI->inventory_model->insert_query('micro_warehouse_state_mapping', $data);
            $this->My_CI->inventory_model->insert_query('warehouse_on_of_status', $wh_on_of_data);
            $service_center = array('is_micro_wh' => 1);
            $this->My_CI->vendor_model->edit_vendor($service_center, $data['vendor_id']);
        }
    }

    /**
     * @desc: This is method return index key, if service caregory matches with given price tags
     * @param: Price tag and Array
     * @return: key
     */
    function search_for_pice_tag_key($price_tag, $array) {
        foreach ($array as $key => $val) {
            if ($val['service_category'] === $price_tag) {
                return $key;
            }
        }
        return null;
    }
    /**
     * @desc: This funtion is used to review bookings (All selected checkbox) which are
     * completed/cancelled by our vendors.
     * It completes/cancels these bookings in the background and returns immediately.
     * @param : void
     * @return : void
     */
    function checked_complete_review_booking($record) {
        $requested_bookings = $record['approved_booking'];
        
        $agent_id = !empty($this->My_CI->session->userdata('id')) ? $this->My_CI->session->userdata('id') : _247AROUND_DEFAULT_AGENT;
        $agent_name = !empty($this->My_CI->session->userdata('employee_id')) ? $this->My_CI->session->userdata('employee_id') : _247AROUND_DEFAULT_AGENT_NAME;
        
        if($requested_bookings){
            $state_change_bookings = array();
            $where['is_in_process'] = 0;
            $whereIN['booking_id'] = $requested_bookings; 
            $tempArray = $this->My_CI->reusable_model->get_search_result_data("booking_details","booking_id",$where,NULL,NULL,NULL,$whereIN,NULL,array());
            foreach($tempArray as $values){
                $approved_booking[] = $values['booking_id'];
                /* If bookings came from completion approval than we add extra state change in booking state change for closure team peformane graph*/
                $booking_status = $this->My_CI->booking_model->getbooking_charges($values['booking_id']);
                if(!empty($booking_status)){
                    $actor = $next_action = 'NULL';
                    if($booking_status[0]['internal_status'] == _247AROUND_COMPLETED){
                       $new_state = _247AROUND_COMPLETED_APPROVED;
                       $closing_remarks = "Booking completed approved by 247around";
                       $this->My_CI->notify->insert_state_change($values['booking_id'], $new_state, _247AROUND_PENDING, $closing_remarks, $agent_id, $agent_name, $actor,$next_action,$record['approved_by']);
                    }
                    else{
                        $new_state = _247AROUND_CANCELED_APPROVED;
                        $closing_remarks = "Booking cancelled approved by 247around";
                        $this->My_CI->notify->insert_state_change($values['booking_id'], $new_state, _247AROUND_PENDING, $closing_remarks, $agent_id, $agent_name, $actor,$next_action,$record['approved_by']);
                    }
                }
                /*end*/
            }
            $inProcessBookings = array_diff($requested_bookings,$approved_booking);

            $url = base_url() . "employee/do_background_process/complete_booking";
            if (!empty($approved_booking)) {
                $this->My_CI->booking_model->mark_booking_in_process($approved_booking);
                $data['booking_id'] = $approved_booking;
                $data['agent_id'] = $agent_id;
                $data['agent_name'] = $agent_name;
                $data['partner_id'] = $record['partner_id'];
                $data['approved_by'] = $record['approved_by']; 
                $this->My_CI->asynchronous_lib->do_background_process($url, $data);
                $this->My_CI->push_notification_lib->send_booking_completion_notification_to_partner($approved_booking);
            } else {
                //Logging
                log_message('info', __FUNCTION__ . ' Approved Booking Empty from Post');
            }
        }
    }

    function get_request_type_life_cycle($bookingID) {
        $where['booking_id'] = $bookingID;
        $orderBYArray['date'] = 'ASC';
        return $this->My_CI->reusable_model->get_search_result_data("booking_request_type_state_change","*",$where,NULL,NULL,$orderBYArray,NULL,NULL,NULL,array());
        
    }

    /**
     * @desc This function is used to process spare transfer
     */
    function spareTransfer($bookings_spare, $agentid, $agent_name, $login_partner_id, $login_service_center_id) {
        $tcount = 0;
        $booking_error_array = array();
        $add_row = array();
        
        foreach ($bookings_spare as $booking) {
            $spareid = $booking['id'];
            $partner_id = $booking['partner_id'];
            $state = $booking['state'];
            
            $requested_inventory = $booking['requested_inventory_id'];
            
            $data = $this->check_inventory_stock($booking['requested_inventory_id'], $booking['booking_partner_id'], $state, "");
            if (!empty($data)) {
                 
                if ($data['stock']) {
                    $dataupdate = array(
                        'is_micro_wh' => $data['is_micro_wh'],
                        'entity_type' => $data['entity_type'],
                        'defective_return_to_entity_id' => $data['defective_return_to_entity_id'],
                        'partner_id' => $data['entity_id'],
                        'defective_return_to_entity_type' => $data['defective_return_to_entity_type'],
                        'challan_approx_value' => $data['challan_approx_value'],
                        'requested_inventory_id' => $data['inventory_id'],
                        'parts_requested' => $data['part_name']
                    );
                    $next_action = _247AROUND_TRANSFERED_TO_NEXT_ACTION;
                    $actor = 'Warehouse';
                    $new_state = 'Spare Part Transferred to ' . $data['entity_id'];
                    $old_state = 'Spare Part Transferred from ' . $partner_id;
                    $this->My_CI->inventory_model->update_spare_courier_details($spareid, $dataupdate);
                    if ($data['entity_type'] == _247AROUND_SF_STRING) {
                        $remarks = _247AROUND_TRANSFERED_TO_VENDOR;
                        $this->My_CI->notify->insert_state_change($booking['booking_id'], $new_state, $old_state, $remarks, $agentid,$agent_name, $actor, $next_action, $login_partner_id, $login_service_center_id);
                        $this->My_CI->inventory_model->update_pending_inventory_stock_request(_247AROUND_SF_STRING, $data['entity_id'], $data['inventory_id'], 1);
                        $this->My_CI->inventory_model->update_pending_inventory_stock_request(_247AROUND_SF_STRING, $partner_id, $requested_inventory, -1);
                    } else if ($data['entity_type'] == _247AROUND_PARTNER_STRING && $booking['entity_type'] != _247AROUND_PARTNER_STRING) {
                        $remarks = _247AROUND_TRANSFERED_TO_PARTNER;
                        $this->My_CI->notify->insert_state_change($booking['booking_id'], $new_state, $old_state, $remarks, $agentid,$agent_name, $actor, $next_action, $login_partner_id, $login_service_center_id);
                        $this->My_CI->inventory_model->update_pending_inventory_stock_request(_247AROUND_SF_STRING, $partner_id, $requested_inventory, -1);
                    }
                    $tcount++;
                } else {

                    $add_row[] = array($booking['booking_id'], $booking['part_number'],$spareid);
                    array_push($booking_error_array, $booking['booking_id']);
                    
                }
            } else {
                $add_row[] = array($booking['booking_id'], $booking['part_number'],$spareid);
                array_push($booking_error_array, $booking['booking_id']);
                                    
            }
        }   /// for loop ends
        
        return array($tcount, $booking_error_array, $add_row);
    }
    
}
