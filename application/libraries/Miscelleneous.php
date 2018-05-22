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
        $this->My_CI->load->library('push_notification_lib');
        $this->My_CI->load->library('send_grid_api');
        $this->My_CI->load->library('s3');
        $this->My_CI->load->library('PHPReport');
        $this->My_CI->load->model('vendor_model');
        $this->My_CI->load->model('reusable_model');
        $this->My_CI->load->model('booking_model');
        $this->My_CI->load->model('upcountry_model');
        $this->My_CI->load->model('partner_model');
        $this->My_CI->load->model('inventory_model');
        $this->My_CI->load->library('form_validation');
        $this->My_CI->load->model('service_centers_model');
        $this->My_CI->load->model('penalty_model');
        $this->My_CI->load->model('engineer_model');
        $this->My_CI->load->driver('cache');
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
             
            // Send New Booking SMS
            $this->My_CI->notify->send_sms_email_for_booking($booking_id, "Newbooking" );
             
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
       
        $unit_details = $this->My_CI->booking_model->get_unit_details(array('booking_id' => $booking_id));
        $cus_net_payable = 0;
        foreach ($unit_details as $value) {
            $cus_net_payable += $value['customer_net_payable'];
        }
        $partner_am_email = "";
        $return_status = TRUE;
        
        $rm = $this->My_CI->vendor_model->get_rm_sf_relation_by_sf_id($query1[0]['assigned_vendor_id']);
        $rm_email = "";
        if (!empty($rm)) {
            $rm_email = ", " . $rm[0]['official_email'];
        }
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
                
                if (empty($is_upcountry)) {
                    log_message('info', __METHOD__ . " => Customer will pay upcountry charges " . $booking_id);
                    $booking['upcountry_paid_by_customer'] = 1;
                    $booking['partner_upcountry_rate'] = DEFAULT_UPCOUNTRY_RATE;
                    $booking['upcountry_remarks'] = CUSTOMER_PAID_UPCOUNTRY;

                    log_message('info', __METHOD__ . " => Amount due added " . $booking_id);
                    $booking['amount_due'] = $cus_net_payable + ($booking['partner_upcountry_rate'] * $booking['upcountry_distance']);


                    $this->My_CI->booking_model->update_booking($booking_id, $booking);
                    $return_status = TRUE;
                } else if (in_array(-1, array_column($is_upcountry, 'is_upcountry')) !== FALSE 
                        && in_array(1, array_column($is_upcountry, 'is_upcountry')) == FALSE ) {
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

                    log_message('info', __METHOD__ . " => Amount due added " . $booking_id);
                    $booking['amount_due'] = $cus_net_payable;

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
                   
                    if ($data['message'] !== UPCOUNTRY_LIMIT_EXCEED) {

                        log_message('info', __METHOD__ . " => Upcountry Booking Free Booking " . $booking_id);
                        $booking['upcountry_paid_by_customer'] = 0;
                        $booking['amount_due'] = $cus_net_payable;
                        $booking['upcountry_remarks'] = PARTNER_PAID_UPCOUNTRY;
                        $this->My_CI->booking_model->update_booking($booking_id, $booking);
                        $return_status = TRUE;
                    } else if ($data['partner_upcountry_approval'] == 1 && $data['message'] == UPCOUNTRY_LIMIT_EXCEED) {

                        log_message('info', __METHOD__ . " => Upcountry Waiting for Approval " . $booking_id);
                        $booking['assigned_vendor_id'] = NULL;
                        $booking['internal_status'] = UPCOUNTRY_BOOKING_NEED_TO_APPROVAL;
                        $booking['upcountry_partner_approved'] = '0';
                        $booking['upcountry_paid_by_customer'] = 0;
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
                            $to = NITS_ANUJ_EMAIL_ID.$rm_email;
                            $cc = $partner_am_email;
                        } else {
                            $subject = "Upcountry Charges Approval Required - Booking ID " . $query1[0]['booking_id'];
                            $to = $data['upcountry_approval_email'];
                            $cc = $partner_am_email.$rm_email;
                            //Send Push Notification
                        $receiverArray['partner'] = array($query1[0]['partner_id']);
                        $notificationTextArray['msg'] = array($booking_id);
                        $this->My_CI->push_notification_lib->create_and_send_push_notiifcation(UPCOUNTRY_APPROVAL,$receiverArray,$notificationTextArray);
                        //End Push Notification
                        }
                        $this->My_CI->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, "", $subject, $message1, "",UPCOUNTRY_APPROVAL_TAG);

                        $return_status = FALSE;
                    } else if ($data['partner_upcountry_approval'] == 0 && $data['message'] == UPCOUNTRY_LIMIT_EXCEED) {

                        log_message('info', __METHOD__ . " => Upcountry, partner does not provide approval" . $booking_id);
                        $this->My_CI->booking_model->update_booking($booking_id, $booking);
                        $this->process_cancel_form($booking_id, "Pending", UPCOUNTRY_CHARGES_NOT_APPROVED, " Upcountry  Distance " . $data['upcountry_distance'], $agent_id, $agent_name, $query1[0]['partner_id']);

                        $to = NITS_ANUJ_EMAIL_ID;
                        $cc = $partner_am_email;
                        $message1 = $booking_id . " has auto cancelled because upcountry limit exceed "
                                . "and partner does not provide upcountry charges approval. Upcountry Distance " . $data['upcountry_distance'] .
                                " Upcountry Pincode " . $data['upcountry_pincode'] . " SF Name " . $query1[0]['vendor_name'];
                        $this->My_CI->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, "", 'Upcountry Auto Cancel Booking', $message1, "",BOOKING_CANCELLED_NO_UPCOUNTRY_APPROVAL);

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

                $to = NITS_ANUJ_EMAIL_ID . ", sales@247around.com , ". $rm_email;
                $cc = "sachinj@247around.com, abhaya@247around.com";
                $message1 = "Upcountry did not calculate for " . $booking_id;
                $this->My_CI->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, "", 'Upcountry Failed', $message1, "",UPCOUNTRY_DISTANCE_CAN_NOT_CALCULATE_EMAIL_TAG);

                $return_status = TRUE;
                break;
        }

        return $return_status;
    }

    function process_cancel_form($booking_id, $status, $cancellation_reason, $cancellation_text, $agent_id, $agent_name, $partner_id) {
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

        $this->update_price_while_cancel_booking($booking_id, $agent_id);
        //Update Engineer table while booking cancelled
        $en_where1 = array("engineer_booking_action.booking_id" => $booking_id);
        $this->My_CI->engineer_model->update_engineer_table(array("current_status" => _247AROUND_CANCELLED, "internal_status" =>_247AROUND_CANCELLED), $en_where1);
        
        $spare = $this->My_CI->partner_model->get_spare_parts_by_any("spare_parts_details.id, spare_parts_details.status", array('booking_id' => $booking_id, 'status NOT IN ("Completed","Cancelled")' =>NULL ), false);
        foreach($spare as $sp){
            //Update Spare parts details table
            $this->My_CI->service_centers_model->update_spare_parts(array('id'=> $sp['id']), array('old_status' => $sp['status'],'status' => _247AROUND_CANCELLED));
        }

        //Log this state change as well for this booking
        //param:-- booking id, new state, old state, employee id, employee name
        $this->My_CI->notify->insert_state_change($booking_id, $data['current_status'], $status, $historyRemarks, $agent_id, $agent_name,$actor,$next_action, _247AROUND);
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

    function update_price_while_cancel_booking($booking_id, $agent_id) {
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
                if (!empty($match)) {
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
                    $data['agent_type'] = _247AROUND_PARTNER_STRING;

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
    function check_upcountry($booking, $appliance, $is_price, $file_type) {
        log_message('info', __FUNCTION__ . ' booking_data: ' . print_r($booking, true) . ' appliance: ' . print_r($appliance, true) . ' file_type: ' . $file_type);
        $partner_data = $this->My_CI->initialized_variable->get_partner_data();
        $data = $this->check_upcountry_vendor_availability($booking['city'], $booking['booking_pincode'], $booking['service_id'], $partner_data, false);
        if (isset($data['vendor_not_found'])) {
            if ($data['vendor_not_found'] == 1) {
                $this->sf_not_exist_for_pincode($booking);
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

            $this->send_sms_to_snapdeal_customer($appliance, $booking['booking_primary_contact_no'], $booking['user_id'], $booking['booking_id'], $partner_data[0]['public_name'], $charges);
            return true;
        } else {
            $this->send_sms_to_snapdeal_customer($appliance, $booking['booking_primary_contact_no'], $booking['user_id'], $booking['booking_id'], $partner_data[0]['public_name'], "");
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
    function send_sms_to_snapdeal_customer($appliance, $phone_number, $user_id, $booking_id, $partner, $price) {
        log_message('info', __FUNCTION__ . ' phone_number: ' . $phone_number . ' user_id: ' . $user_id . ' booking_id: ' . $booking_id . ' partner: ' . $partner . ' appliance: ' . $appliance . ' price: ' . $price);

        $sms['tag'] = "partner_missed_call_for_installation";

        //ordering of smsData is important, it should be as per the %s in the SMS
        $sms['smsData']['service'] = $appliance;
        $sms['smsData']['missed_call_number'] = SNAPDEAL_MISSED_CALLED_NUMBER;

        /* If price exist then send sms according to that otherwise
         *  send sms by checking function get_product_free_not
         */
        if (!empty($price)) {
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

        $where['where'] = array('assigned_cp_id' => $cp_id, 'cp_invoice_id IS NULL' => NULL);
        $where['where_in'] = array('current_status' => array('Delivered', 'Completed'));

        $cp_delivered_charge = $this->My_CI->bb_model->get_bb_order_list($where, "SUM(cp_basic_charge + cp_tax_charge) as cp_delivered_charge")[0]->cp_delivered_charge;
        $where['where_in'] = array('current_status' => array('In-Transit', 'New Item In-transit', 'Attempted'));

        $cp_intransit = $this->My_CI->bb_model->get_bb_order_list($where, "SUM(cp_basic_charge + cp_tax_charge) as cp_intransit")[0]->cp_intransit;
        $total_balance = abs($advance_amount) - ( $unbilled_amount + $cp_delivered_charge + $cp_intransit);

        $cp_amount['total_balance'] = $total_balance;
        $cp_amount['cp_delivered'] = $cp_delivered_charge;
        $cp_amount['cp_transit'] = $cp_intransit;
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
        exit;
    }

    function downloadExcel($data, $config) {
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

    function _allot_source_partner_id_for_pincode($service_id, $state, $brand, $default_partner, $api =false) {
        log_message('info', __FUNCTION__ . ' ' . $service_id, $state, $brand);
        $data = [];
        $flag = FALSE;

        $partner_array = $this->My_CI->partner_model->get_active_partner_id_by_service_id_brand($brand, $service_id);

        if (!empty($partner_array)) {

            foreach ($partner_array as $value) {
                //Now getting details for each Partner
                $filtered_partner_state = $this->My_CI->partner_model->check_activated_partner_for_state_service($state, $value['partner_id'], $service_id);
                if ($filtered_partner_state) {
                    //Now assigning this case to Partner
                    $data['partner_id'] = $value['partner_id'];
                    $data['source'] = $partner_array[0]['code'];
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

        if ($flag) {
            $get_partner_source = $this->My_CI->partner_model->getpartner_details('bookings_sources.code', array('partners.id' => $default_partner));
            $data['partner_id'] = $default_partner;
            $data['source'] = $get_partner_source[0]['code'];
        }

        $blocked_brand = $this->My_CI->partner_model->get_partner_blocklist_brand(array("partner_id" => $data['partner_id'], "brand" => $brand, 
            "service_id" => $service_id), "*");

        if(!empty($blocked_brand)){
           $data['partner_id'] = _247AROUND;
           $data['source'] = 'SB';
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
    public function update_file_uploads($file_name, $tmpFile, $type, $result = "", $email_message_id = "") {

        $data['file_type'] = $type;
        $data['file_name'] = date('d-M-Y-H-i-s') . "-" . $file_name;
        $data['agent_id'] = !empty($this->My_CI->session->userdata('id')) ? $this->My_CI->session->userdata('id') : _247AROUND_DEFAULT_AGENT;
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
            
            // calculate final amount of partner
            $final_amount = -($invoice_amount[0]['amount'] + ($service_amount[0]['amount'] * (1 + SERVICE_TAX_RATE)) + ($upcountry_basic * (1 + SERVICE_TAX_RATE)));

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

    function send_sf_not_found_email_to_rm($booking, $rm_email,$subject, $isPartner) {
        $cc = ANUJ_EMAIL_ID;
        $booking['service'] = NULL;
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
            $cc = NITS_ANUJ_EMAIL_ID;
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
        $notFoundSfArray = array('booking_id' => $booking['booking_id'], 'pincode' => $booking['booking_pincode'], 'city' => $booking['city'], 'service_id' => $booking['service_id']);
        $result = $this->My_CI->reusable_model->get_rm_for_pincode($notFoundSfArray['pincode']);
        if (!empty($result)) {
            $notFoundSfArray['rm_id'] = $result[0]['rm_id'];
            $notFoundSfArray['state'] = $result[0]['state'];
            $query = $this->My_CI->reusable_model->get_search_query("employee", "official_email", array('id' => $result[0]['rm_id'],'active' => 1), NULL, NULL, NULL, NULL, NULL);
            $rm_email = $query->result_array();
            if (empty($rm_email)) {
                $rm_email[0]['official_email'] = NULL;
            }
            $subject = "SF Not Exist in the Pincode " . $booking['booking_pincode'];
            $this->send_sf_not_found_email_to_rm($booking, $rm_email[0]['official_email'],$subject, TRUE);
        }else{
            $rm = $this->My_CI->employee_model->get_rm_details();
            $rm_emails = implode(',', array_column($rm, 'official_email'));
            $subject = "Pincode Not Exist In India Pincode" . $booking['booking_pincode'];
            $this->send_sf_not_found_email_to_rm($booking, $rm_emails,$subject, FALSE);
        }
        if (array_key_exists('partner_id', $booking)) {
            $notFoundSfArray['partner_id'] = $booking['partner_id'];
        }
        $this->My_CI->vendor_model->insert_booking_details_sf_not_exist($notFoundSfArray);
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
                    $picName = $type . rand(10, 100) . $pic . "." . $extension;
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

    function update_insert_bank_account_details($bankDetailsArray, $actionType) {
        $affectedRows = 0;

        if ($actionType == 'insert') {
            // If all values are not blank, atleast one column has value then create entry in bank details table
            if (array_key_exists('bank_name', $bankDetailsArray) || array_key_exists('account_type', $bankDetailsArray) || array_key_exists('bank_account', $bankDetailsArray) || array_key_exists('ifsc_code', $bankDetailsArray) || array_key_exists('cancelled_cheque_file', $bankDetailsArray) || array_key_exists('beneficiary_name', $bankDetailsArray) || array_key_exists('beneficiary_name', $bankDetailsArray)) {
                return $affectedRows = $this->My_CI->reusable_model->insert_into_table('account_holders_bank_details', $bankDetailsArray);
            }
        } else if ($actionType == 'update') {
            $where['entity_id'] = $bankDetailsArray['entity_id'];
             $where['entity_type'] = $bankDetailsArray['entity_type'];
            $this->My_CI->reusable_model->update_table("account_holders_bank_details",$bankDetailsArray,$where);
        }
           
    }

    /**
     * @desc Return Account Manager ID
     * @param int $partner_id
     * @return Array
     */
    function get_am_data($partner_id) {
        $data = [];
        $am_id = $this->My_CI->partner_model->getpartner_details('account_manager_id', array('partners.id' => trim($partner_id)));
        if (!empty($am_id)) {
            $data = $this->My_CI->employee_model->getemployeefromid($am_id[0]['account_manager_id']);
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
    function process_inventory_stocks($data) {
        log_message("info", __FUNCTION__ . " process inventory update" . print_r($data, true));
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
                if(isset($data['is_wh'])){
                    $is_entity_exist = $this->My_CI->reusable_model->get_search_query('inventory_stocks', 'inventory_stocks.id', array('entity_id' => $data['sender_entity_id'], 'entity_type' => $data['sender_entity_type'], 'inventory_id' => $is_part_exist[0]['inventory_id']), NULL, NULL, NULL, NULL, NULL)->result_array();
                }else{
                    $is_entity_exist = $this->My_CI->reusable_model->get_search_query('inventory_stocks', 'inventory_stocks.id', array('entity_id' => $data['receiver_entity_id'], 'entity_type' => $data['receiver_entity_type'], 'inventory_id' => $is_part_exist[0]['inventory_id']), NULL, NULL, NULL, NULL, NULL)->result_array();
                }
                if (!empty($is_entity_exist)) {
                    $stock = "stock + '" . $data['stock'] . "'";
                    $update_stocks = $this->My_CI->inventory_model->update_inventory_stock(array('id' => $is_entity_exist[0]['id']), $stock);
                    if ($update_stocks) {
                        log_message("info", __FUNCTION__ . " Stocks has been updated successfully");
                        $flag = TRUE;
                    } else {
                        log_message("info", __FUNCTION__ . " Error in updating stocks");
                    }
                } else {
                    $insert_data['entity_id'] = $data['receiver_entity_id'];
                    $insert_data['entity_type'] = $data['receiver_entity_type'];
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
                    log_message("info", __FUNCTION__ . " Error in updating inventory" . print_r($data, true));
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
    private function get_main_nav_data($nav_type){
        $where = array("header_navigation.groups LIKE '%".$this->My_CI->session->userdata('user_group')."%'"=>NULL,"header_navigation.is_active"=>"1");
        $where["header_navigation.nav_type"]=$nav_type;
        $parentArray = $structuredData=$navFlowArray=array();
        $data= $this->My_CI->reusable_model->get_search_result_data("header_navigation","header_navigation.*,GROUP_CONCAT(p_m.title) as parent_name",$where,
                array("header_navigation p_m"=>"FIND_IN_SET(p_m.id,header_navigation.parent_ids)"),NULL,array("level"=>"ASC"),NULL,array("header_navigation p_m"=>"LEFT"),array('header_navigation.id'));
         foreach($data as $navData){
            $structuredData["id_".$navData['id']]['title'] = $navData['title'];
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
    function set_header_navigation_in_cache(){
        $data['main_nav'] = $this->get_main_nav_data("main_nav");
        $data['right_nav'] = $this->get_main_nav_data("right_nav");
        $msg = $this->My_CI->load->view('employee/header/header_navigation',$data,TRUE);
        $this->My_CI->cache->file->save('navigationHeader_'.$this->My_CI->session->userdata('id'), $msg, 36000);
    }
    /*
     * This Function used to load navigation header from cache
     */
    function load_nav_header(){
        //Check is navigation there in cache?
        // If not then create navigation and loads into cache
        if(!$this->My_CI->cache->file->get('navigationHeader_'.$this->My_CI->session->userdata('id'))){
                $this->set_header_navigation_in_cache();
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
        $historyWhere['booking_id'] =  $bookingDetails[0]['booking_id'];
        $historyWhere['new_state'] =  "InProcess_Rescheduled";
        $historyWhere['service_center_id'] =  $bookingDetails[0]['assigned_vendor_id'];
        $historyLimitArray['length'] =  1;
        $historyLimitArray['start'] =  0;
        $historyOrderBYArray['id'] =  'ASC';
        $lastResheduledRequestData = $this->My_CI->reusable_model->get_search_result_data("booking_state_change","*",$historyWhere,NULL,$historyLimitArray,$historyOrderBYArray,
                NULL,NULL,array()); 
        $where['from_number'] = $userPhone;
        $where['(date(create_date) >= "'.date('Y-m-d', strtotime($lastResheduledRequestData[0]['create_date'])).'" AND date(create_date)<="'.date('Y-m-d').'" )'] = NULL;
        $logData = $this->My_CI->reusable_model->get_search_result_data("fake_reschedule_missed_call_log log","COUNT(log.id) as count",$where,NULL,NULL,NULL,NULL,NULL,array());
        log_message('info', __METHOD__.' Function Start '.print_r($logData,true));
        if($logData[0]['count'] >0){
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
                $return_mail_to = $vendorContact[0]['owner_email'].','.$vendorContact[0]['primary_contact_email'];
                //Getting template from Database
                $template = $this->My_CI->booking_model->get_booking_email_template("escalation_on_booking");
                if (!empty($template)) {  
                    //From will be currently logged in user
                    $from = $this->My_CI->employee_model->getemployeefromid($id)[0]['official_email'];
                    //Sending Mail
                    $email['booking_id'] = $escalation['booking_id'];
                    $email['count_escalation'] = $booking_date_timeslot[0]['count_escalation'];
                    $email['reason'] = $escalation_policy_details[0]['escalation_reason'];
                    $emailBody = vsprintf($template[0], $email);
                    $subject['booking_id'] = $escalation['booking_id'];
                    $subjectBody = vsprintf($template[4], $subject);
                    $this->My_CI->notify->sendEmail($from, $return_mail_to, $template[3] . "," . $cc.",".$am_email, '', $subjectBody, $emailBody, "",'escalation_on_booking');
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
            $status = $this->My_CI->notify->sendTransactionalSmsMsg91($contact[0]['primary_contact_phone_1'], $smsBody);
            //For saving SMS to the database on sucess
            $this->My_CI->notify->add_sms_sent_details($id, 'vendor' , $contact[0]['primary_contact_phone_1'],
            $smsBody, $booking_id, "Escalation", $status['content']);
            $status1 = $this->My_CI->notify->sendTransactionalSmsMsg91($contact[0]['owner_phone_1'], $smsBody);
            //For saving SMS to the database on sucess
            $this->My_CI->notify->add_sms_sent_details($id, 'vendor' , $contact[0]['owner_phone_1'],
            $smsBody, $booking_id,"Escalation", $status1['content']);
          } 
          else if ($escalation_policy[0]['sms_to_owner'] == 0 && $escalation_policy[0]['sms_to_poc'] == 1) {
            $smsBody = $this->replaceSms_body($escalation_policy[0]['sms_body'], $booking_id, $userDetails);
            $status = $this->My_CI->notify->sendTransactionalSmsMsg91($contact[0]['primary_contact_phone_1'], $smsBody);
            //For saving SMS to the database on sucess
            $this->notify->add_sms_sent_details($id, 'vendor' , $contact[0]['primary_contact_phone_1'],$smsBody, $booking_id, "Escalation", $status['content']);
          } 
          else if ($escalation_policy[0]['sms_to_owner'] == 1 && $escalation_policy[0]['sms_to_poc'] == 0) {
            $smsBody = $this->replaceSms_body($escalation_policy[0]['sms_body'], $booking_id, $userDetails);
            $status = $this->My_CI->notify->sendTransactionalSmsMsg91($contact[0]['owner_phone_1'], $smsBody);
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
    
    function get_SF_payout($booking_id, $service_center_id, $amount_due){
       
        $where['where'] = array('booking_unit_details.booking_id' =>$booking_id, "booking_status != 'Cancelled'" => NULL);
        $where['length'] = -1;
        $select = "(vendor_basic_charges + vendor_st_or_vat_basic_charges "
                . "+ vendor_extra_charges + vendor_st_extra_charges+ vendor_parts+ vendor_st_parts) as sf_earned";
        $b_earned = $this->My_CI->booking_model->get_bookings_by_status($where, $select);
        $unit_amount = 0;
        foreach($b_earned as $earn){
            $unit_amount += $earn->sf_earned;
        }
        
        $penalty_select = "CASE WHEN ((count(booking_id) *  penalty_on_booking.penalty_amount) > cap_amount) THEN (cap_amount)

        ELSE (COUNT(booking_id) * penalty_on_booking.penalty_amount) END  AS p_amount";
        $penalty_where = array('booking_id' => $booking_id,'service_center_id' => $service_center_id,'penalty_on_booking.active' => 1);
        $p_amount = $this->My_CI->penalty_model->get_penalty_on_booking_any($penalty_where, $penalty_select, array('CASE'));
        
        $is_customer_paid = 1;
        if(empty($amount_due)){
            $is_customer_paid = 0;
        }
        $upcountry = $this->My_CI->upcountry_model->upcountry_booking_list($service_center_id, $booking_id, true, $is_customer_paid);
        $up_charges = 0;
        if(!empty($upcountry)){
            if($upcountry[0]['count_booking'] == 0){
                $upcountry[0]['count_booking'] = 1;
            }
            $up_charges = $upcountry[0]['upcountry_price']/$upcountry[0]['count_booking'];
        }
        $return['sf_earned'] = $unit_amount -$p_amount[0]['p_amount'] + $up_charges;
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
        
        //unlink($image_path);
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
    
function convert_html_to_pdf($html,$booking_id,$filename,$s3_folder){
    log_message('info', __FUNCTION__ . " => Entering, Booking ID: " . $booking_id);
        require_once __DIR__ . '/pdf/vendor/autoload.php';
        $mpdf = new \Mpdf\Mpdf();
        $mpdf->WriteHTML($html);
        $tempfilePath = TMP_FOLDER.$filename;
        $mpdf->Output($tempfilePath,'F');
        if($mpdf){
        $is_file = $this->My_CI->s3->putObjectFile($tempfilePath, BITBUCKET_DIRECTORY, $s3_folder."/".$filename, S3::ACL_PUBLIC_READ);
        if($is_file){
        $response_data = array('response' => 'Success',
                                                   'response_msg' => 'PDF generated Successfully and uploaded on S3',
                                                   'output_pdf_file' => $filename,
                                                   'bucket_dir' => BITBUCKET_DIRECTORY,
                                                   'id' => $booking_id
                                                  );
                            //unlink($tempfilePath);
                            return  json_encode($response_data);
        }
        else {
                            //return this response when PDF generated successfully but unable to upload on S3
                            $response_data = array('response' => 'Error',
                                                   'response_msg' => 'PDF generated Successfully But Unable To Upload on S3',
                                                   'output_pdf_file' => $filename,
                                                   'bucket_dir' => BITBUCKET_DIRECTORY,
                                                   'id' => $booking_id
                                                   );
                            return  json_encode($response_data);
                        }
        }
        else{
             $response_data = array('response' => 'Error',
                                                   'response_msg' => 'Error In Generating PDF File',
                                                   );
             $to = 'vijaya@247around.com';
            $cc = DEVELOPER_EMAIL;
            $subject = "Job Card Not Generated";
            $msg = "There are some issue while creating pdf for booking_id/invoice_id $booking_id. Check the issue and fix it immediately";
            $this->My_CI->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, "", $subject, $msg,JOB_CARD_NOT_GENERATED);
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
        if (!empty($sf_details[0]['signature_file'])) {
            $excel_data['sf_name'] = $sf_details[0]['name'];
            $excel_data['sf_address'] = $sf_details[0]['address'];
            $excel_data['sf_owner_name'] = $sf_details[0]['owner_name'];
            $excel_data['date'] = date('Y-m-d');
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
    function create_sf_challan_id($name){
        $challan_id_tmp = $name."-DC-";
        $where['length'] = -1;
        $where['where'] = array("( sf_challan_number LIKE '%".$challan_id_tmp."%' )" => NULL);
        $where['select'] = "sf_challan_number";
        $challan_no_temp = $this->My_CI->inventory_model->get_spare_parts_query($where);
        $challan_no = 1;
        $int_challan_no = array();
        if (!empty($challan_no_temp)) {
            foreach ($challan_no_temp as  $value) {
                 $explode = explode($challan_id_tmp, $value->sf_challan_number);
                 array_push($int_challan_no, $explode[1] + 1);
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
            $bookingID = $bookingDetails[0]['booking_id'];
        }
        if($bookingID){
            $select = "booking_details.*,employee.official_email,service_centres.name,services.services";
            $where["booking_details.booking_id"] = $bookingID; 
            $partnerJoin["partners"] = "partners.id=booking_details.partner_id";
            $join["employee_relation"] = "FIND_IN_SET(booking_details.assigned_vendor_id,employee_relation.service_centres_id)";
            $join["employee"] = "employee.id=employee_relation.agent_id";
            $join["service_centres"] = "service_centres.id=booking_details.assigned_vendor_id";
            $join["services"] = "services.id=booking_details.service_id";
            $partnerJoin["employee"] = "employee.id=partners.account_manager_id";
            $bookingData = $this->My_CI->reusable_model->get_search_result_data("booking_details",$select,$where,$join,NULL,NULL,NULL,NULL,array());
            $amEmail = $this->My_CI->reusable_model->get_search_result_data("booking_details","employee.official_email",$where,$partnerJoin,NULL,NULL,NULL,NULL,array());
            $subject = 'Bad Feedback From Customer, Rating ('.$rating.') For '.$bookingID;
            $message = "Please take action as Customer is Not Satisfied with our Service.<br>"
                    . "SF : ".$bookingData[0]['name']."<br>"
                    . "Customer remarks : ".$bookingData[0]['rating_comments']."<br> "
                    . "Request Type : ".$bookingData[0]['request_type']."<br> "
                    . "Appliance : ".$bookingData[0]['services']."<br> ";
            $to = ANUJ_EMAIL_ID;  
            $cc = $bookingData[0]['official_email'].",".$amEmail[0]['official_email'].",".$this->My_CI->session->userdata("official_email");
            $bcc = "chhavid@247around.com";
            $this->My_CI->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, $bcc, $subject, $message, "","we_get_bad_rating");
            log_message('info', __FUNCTION__ . " END  ".$bookingID.$number);
        }
    }
    function update_serial_number_in_appliance_details($unitTableID){
       $applianceData = $this->My_CI->reusable_model->get_search_result_data("booking_unit_details","appliance_id,serial_number",array("id"=>$unitTableID),NULL,NULL,NULL,NULL,NULL,array());
       if (!empty($applianceData)) {
            $applianceID = $applianceData[0]['appliance_id'];
            $data['sf_serial_number'] = $applianceData[0]['serial_number'];
            $this->My_CI->booking_model->update_appliances($applianceID, $data);
       }
    }
}
