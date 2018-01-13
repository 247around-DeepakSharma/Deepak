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
     * @desc This method is used to check upcountry avaliablity on the basis of booking pincode, service id.
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

        // if $check_vendor is empty then return because we are are providing service in this pincode
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
                    $msg['upcountry_distance'] = 0;

                    return $msg;
                }
            } else if (count($check_vendor > 1)) {
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
                        return $multiple_vendor;
                    } else {

                        return $mesg1[0];
                    }
                }
            }

            return $this->My_CI->upcountry_model->action_upcountry_booking($booking_city, $booking_pincode, $data1, $partner_data);
        } else {

            $msg['message'] = SF_DOES_NOT_EXIST;
            $msg['vendor_not_found'] = 1;

            return $msg;
        }
    }

    /**
     * @desc This method is used to assign service center to booking
     * @param String $service_center_id
     * @param String $booking_id
     * @return boolean
     */
    function assign_vendor_process($service_center_id, $booking_id,$agent_id, $agent_type) {
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
        $partner_status = $this->My_CI->booking_utilities->get_partner_status_mapping_data(_247AROUND_PENDING, ASSIGNED_VENDOR, _247AROUND, $booking_id);
        if (!empty($partner_status)) {
            $b['partner_current_status'] = $partner_status[0];
            $b['partner_internal_status'] = $partner_status[1];
        }
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
            
            // $vendor_data = $this->My_CI->vendor_model->getVendorDetails("isEngineerApp", array("id" =>$service_center_id, "isEngineerApp" => 1));
            
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
                            "BUG IN ASSIGN ". $booking_id, "SF Assigned but Action table not updated", "");
                    
                }
                // if(!empty($vendor_data)){
                //     $engineer_action['unit_details_id'] = $value['id'];
                //     $engineer_action['booking_id'] = $booking_id;
                //     $engineer_action['current_status'] = _247AROUND_PENDING;
                //     $engineer_action['internal_status'] = _247AROUND_PENDING;
                //     $engineer_action["create_date"] = date("Y-m-d H:i:s");
                    
                //     $enID = $this->My_CI->engineer_model->insert_engineer_action($engineer_action);
                //     if(!$enID){
                //          $this->My_CI->notify->sendEmail(NOREPLY_EMAIL_ID, DEVELOPER_EMAIL, "", "", 
                //             "BUG in Enginner Table ". $booking_id, "SF Assigned but Action table not updated", "");
                //     }
                // }
                    
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
        $return_status = TRUE;
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

                    log_message('info', __METHOD__ . " => Amount due added " . $booking_id);
                    $booking['amount_due'] = $cus_net_payable + ($booking['partner_upcountry_rate'] * $booking['upcountry_distance']);


                    $this->My_CI->booking_model->update_booking($booking_id, $booking);
                    $return_status = TRUE;
                } else if (array_search(-1, array_column($is_upcountry, 'is_upcountry')) !== False) {
                    log_message('info', __METHOD__ . " => Customer or Partner does not pay upcountry charges " . $booking_id);
                    $booking['is_upcountry'] = 0;
                    $booking['upcountry_pincode'] = NULL;
                    $booking['sub_vendor_id'] = NULL;
                    $booking['upcountry_distance'] = NULL;
                    $booking['sf_upcountry_rate'] = NULL;
                    $booking['partner_upcountry_rate'] = NULL;
                    $booking['upcountry_paid_by_customer'] = '0';
                    $booking['upcountry_partner_approved'] = '1';

                    log_message('info', __METHOD__ . " => Amount due added " . $booking_id);
                    $booking['amount_due'] = $cus_net_payable;

                    $this->My_CI->booking_model->update_booking($booking_id, $booking);
                    log_message('info', __METHOD__ . " => Not Upcountry Booking" . $booking_id);
                    $return_status = TRUE;
                    break;
                } else if (!empty($is_upcountry)) {

                    if ($data['message'] !== UPCOUNTRY_LIMIT_EXCEED) {

                        log_message('info', __METHOD__ . " => Upcountry Booking Free Booking " . $booking_id);
                        $booking['upcountry_paid_by_customer'] = 0;
                        $booking['amount_due'] = $cus_net_payable;
                        $this->My_CI->booking_model->update_booking($booking_id, $booking);
                        $return_status = TRUE;
                    } else if ($data['partner_upcountry_approval'] == 1 && $data['message'] == UPCOUNTRY_LIMIT_EXCEED) {

                        log_message('info', __METHOD__ . " => Upcountry Waiting for Approval " . $booking_id);
                        $booking['assigned_vendor_id'] = NULL;
                        $booking['internal_status'] = UPCOUNTRY_BOOKING_NEED_TO_APPROVAL;
                        $booking['upcountry_partner_approved'] = '0';
                        $booking['upcountry_paid_by_customer'] = 0;
                        $booking['amount_due'] = $cus_net_payable;
                        $partner_status = $this->My_CI->booking_utilities->get_partner_status_mapping_data(_247AROUND_PENDING, UPCOUNTRY_BOOKING_NEED_TO_APPROVAL,
                                $query1[0]['partner_id'], $booking_id);
                        if (!empty($partner_status)) {
                            $booking['partner_current_status'] = $partner_status[0];
                            $booking['partner_internal_status'] = $partner_status[1];
                        }

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
                            $to = $data['upcountry_approval_email'];
                            $cc = NITS_ANUJ_EMAIL_ID;
                        }

                        $this->My_CI->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, "", $subject, $message1, "");

                        $return_status = FALSE;
                    } else if ($data['partner_upcountry_approval'] == 0 && $data['message'] == UPCOUNTRY_LIMIT_EXCEED) {

                        log_message('info', __METHOD__ . " => Upcountry, partner does not provide approval" . $booking_id);
                        $this->My_CI->booking_model->update_booking($booking_id, $booking);
                        $this->process_cancel_form($booking_id, "Pending", UPCOUNTRY_CHARGES_NOT_APPROVED, " Upcountry  Distance " . $data['upcountry_distance'], $agent_id, $agent_name, $query1[0]['partner_id']);

                        $to = NITS_ANUJ_EMAIL_ID;
                        $cc = "abhaya@247around.com";
                        $message1 = $booking_id . " has auto cancelled because upcountry limit exceed "
                                . "and partner does not provide upcountry charges approval. Upcountry Distance " . $data['upcountry_distance'] .
                                " Upcountry Pincode " . $data['upcountry_pincode'] . " SF Name " . $query1[0]['vendor_name'];
                        $this->My_CI->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, "", 'Upcountry Auto Cancel Booking', $message1, "");

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

                $this->My_CI->booking_model->update_booking($booking_id, $booking);

                $to = NITS_ANUJ_EMAIL_ID . ", sales@247around.com";
                $cc = "sachinj@247around.com, abhaya@247around.com";
                $message1 = "Upcountry did not calculate for " . $booking_id;
                $this->My_CI->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, "", 'Upcountry Failed', $message1, "");

                $return_status = TRUE;
                break;
        }

        return $return_status;
    }

    function process_cancel_form($booking_id, $status, $cancellation_reason, $cancellation_text, $agent_id, $agent_name, $partner_id) {
        log_message('info', __METHOD__ . " => Entering " . $booking_id, ' status: ' . $status . ' cancellation_reason: ' . $cancellation_reason . ' agent_id: ' . $agent_id . ' agent_name: ' . $agent_name . ' partner_id: ' . $partner_id);
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
        
        $spare = $this->My_CI->partner_model->get_spare_parts_by_any("spare_parts_details.id, spare_parts_details.status", array('booking_id' => $booking_id, 'status NOT IN ("Completed","Cancelled")' =>NULL ), false);
        foreach($spare as $sp){
            //Update Spare parts details table
            $this->My_CI->service_centers_model->update_spare_parts(array('id'=> $sp['id']), array('old_status' => $sp['status'],'status' => _247AROUND_CANCELLED));
        }

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
                            $this->My_CI->notify->sendEmail(NOREPLY_EMAIL_ID, $to, "", "", $subject, $message, "");

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
                            $this->My_CI->notify->sendEmail(NOREPLY_EMAIL_ID, $to, "", "", $subject, $message, "");
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
            $to = 'vijaya@247around.com';
            $cc = DEVELOPER_EMAIL;

            $subject = "Stag01 Server Might Be Down";
            $msg = "There are some issue while creating pdf for booking_id/invoice_id $id from stag01 server. Check the issue and fix it immediately";
            $this->My_CI->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, "", $subject, $msg, $output_file_excel);
            return $result;
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

    function _allot_source_partner_id_for_pincode($service_id, $state, $brand, $default_partner) {
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
                    if ($value['partner_id'] == 247041) {
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
            switch ($default_partner) {
                case SNAPDEAL_ID:
                    $data['partner_id'] = SNAPDEAL_ID;
                    $data['source'] = 'SS';
                    break;
                case WYBOR_ID:
                    $data['partner_id'] = WYBOR_ID;
                    $data['source'] = 'SY';
                    break;
                case PAYTM:
                    $data['partner_id'] = PAYTM;
                    $data['source'] = 'SP';
                    break;
                case AKAI_ID:
                    $data['partner_id'] = AKAI_ID;
                    $data['source'] = 'PA';
                    break;
            }
        }

        $blocked_brand = $this->My_CI->partner_model->get_partner_blocklist_brand(array("partner_id" => $data['partner_id'], "brand" => $brand), "*");

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
    function get_partner_prepaid_amount($partner_id) {
        //Get Partner details

        $partner_details = $this->My_CI->partner_model->getpartner_details("is_active, is_prepaid,prepaid_amount_limit,"
                . "grace_period_date,prepaid_notification_amount ", array('partners.id' => $partner_id));
        if (!empty($partner_details)) {
            //Get Partner invoice amout
            $invoice_amount = $this->My_CI->invoices_model->get_invoices_details(array('vendor_partner' => 'partner', 'vendor_partner_id' => $partner_id,
                'settle_amount' => 0), 'SUM(CASE WHEN (type_code = "B") THEN ( amount_collected_paid + `amount_paid`) WHEN (type_code = "A" ) '
                    . 'THEN ( amount_collected_paid -`amount_paid`) END)  AS amount');
            $where = array(
                'partner_id' => $partner_id,
                'partner_invoice_id is null' => NULL,
                'booking_status IN ("' . _247AROUND_PENDING . '", "'  . _247AROUND_COMPLETED . '")' => NULL
            );
            // sum of partner payable amount whose booking is in followup, pending and completed(Invoice not generated) state.
            $service_amount = $this->My_CI->booking_model->get_unit_details($where, false, 'SUM(partner_net_payable) as amount');
            // calculate final amount of partner
            $final_amount = -($invoice_amount[0]['amount'] + ($service_amount[0]['amount'] * (1 + SERVICE_TAX_RATE)));

            log_message("info", __METHOD__ . " Partner Id " . $partner_id . " Prepaid account" . $final_amount);
            $d['prepaid_amount'] = $final_amount;
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

            return $d;
        } else {
            return false;
        }
    }

    /*
     * This Functiotn is used to send sf not found email to associated rm
     */

    function send_sf_not_found_email_to_rm($booking, $rm_email) {
        $cc = SF_NOT_EXISTING_IN_PINCODE_MAPPING_FILE_CC;
        $subject = "SF Not Exist in the Pincode " . $booking['booking_pincode'];
        $tempPartner = $this->My_CI->reusable_model->get_search_result_data("partners", "public_name", array('id' => $booking['partner_id']), NULL, NULL, NULL, NULL, NULL);
        $booking['partner_name'] = NULL;
        if (!empty($tempPartner)) {
            $booking['partner_name'] = $tempPartner[0]['public_name'];
        }
        $message = $this->My_CI->load->view('employee/sf_not_found_email_template', $booking, true);
        $this->My_CI->notify->sendEmail(NOREPLY_EMAIL_ID, $rm_email, $cc, "", $subject, $message, "");
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
            $query = $this->My_CI->reusable_model->get_search_query("employee", "official_email", array('id' => $result[0]['rm_id']), NULL, NULL, NULL, NULL, NULL);
            $rm_email = $query->result_array();
            if (empty($rm_email)) {
                $rm_email[0]['official_email'] = NULL;
            }
            $this->send_sf_not_found_email_to_rm($booking, $rm_email[0]['official_email']);
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
        // Remove all columns which has blank values
        foreach ($bankDetailsArray as $key => $value) {
            if($key != 'is_verified'){
                if ($value == '' || $value == '0') {
                    unset($bankDetailsArray[$key]);
                }
            }
        }

        if ($actionType == 'insert') {
            // If all values are not blank, atleast one column has value then create entry in bank details table
            if (array_key_exists('bank_name', $bankDetailsArray) || array_key_exists('account_type', $bankDetailsArray) || array_key_exists('bank_account', $bankDetailsArray) || array_key_exists('ifsc_code', $bankDetailsArray) || array_key_exists('cancelled_cheque_file', $bankDetailsArray) || array_key_exists('beneficiary_name', $bankDetailsArray) || array_key_exists('beneficiary_name', $bankDetailsArray)) {
                return $affectedRows = $this->My_CI->reusable_model->insert_into_table('account_holders_bank_details', $bankDetailsArray);
            }
        } else if ($actionType == 'update') {
            $where['entity_id'] = $bankDetailsArray['entity_id'];
            $where['entity_type'] = $bankDetailsArray['entity_type'];
            //Checkk is there any entry in bank table for associated entityID and entityType
            $is_exist = $this->My_CI->reusable_model->get_search_result_count("account_holders_bank_details", "entity_id", $where, NULL, NULL, NULL, NULL, NULL);
            if ($is_exist > 0) {
                //If yes then update that row
                $agentID = $bankDetailsArray['agent_id'];
                unset($bankDetailsArray['entity_id']);
                unset($bankDetailsArray['agent_id']);
                // check is there any new updation for bank table or not
                $affectedRows = $this->My_CI->reusable_model->update_table('account_holders_bank_details', $bankDetailsArray, $where);
                if ($affectedRows == 1) {
                    //if yes then update table
                    return $this->My_CI->reusable_model->update_table('account_holders_bank_details', array('agent_id' => $agentID), $where);
                } else {
                    //if not then don't update the table
                    return $affectedRows;
                }
            } else {
                // Else Insert new entry
                if (array_key_exists('bank_name', $bankDetailsArray) || array_key_exists('account_type', $bankDetailsArray) || array_key_exists('bank_account', $bankDetailsArray) || array_key_exists('ifsc_code', $bankDetailsArray) || array_key_exists('cancelled_cheque_file', $bankDetailsArray) || array_key_exists('beneficiary_name', $bankDetailsArray) || array_key_exists('beneficiary_name', $bankDetailsArray)) {
                    return $affectedRows = $this->My_CI->reusable_model->insert_into_table('account_holders_bank_details', $bankDetailsArray);
                }
            }
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
    function generate_excel_data($template, $download_file_name, $data) {


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
                'repeat' => true,
                'data' => $data,
            )
                )
        );

        $output_file_excel = TMP_FOLDER . $download_file_name . ".xlsx";

        $res1 = 0;
        if (file_exists($output_file_excel)) {

            system(" chmod 777 " . $output_file_excel, $res1);
            unlink($output_file_excel);
        }

        $R->render('excel', $output_file_excel);

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
        $orderByArray[$triggeredTable . '.id,' . $triggeredTable . '.update_date'] = 'DESC';
        $joinArray = array("employee" => "employee.id=" . $triggeredTable . ".agent_id");
        $triggeredTableData = $this->My_CI->reusable_model->get_search_result_data($triggeredTable, $triggeredTable . ".*,employee.full_name", array($triggeredTable.".id" => $entityID), $joinArray, NULL, $orderByArray, NULL, NULL);
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

        if ($data['receiver_entity_type'] === _247AROUND_SF_STRING) {
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
            $is_part_exist = $this->My_CI->reusable_model->get_search_query('inventory_master_list', 'inventory_master_list.inventory_id', array('part_number' => $data['part_number']), NULL, NULL, NULL, NULL, NULL)->result_array();
            if (!empty($is_part_exist)) {
                /* check if entity is exist in the inventory stock table
                 * if exist then get update the stock
                 * else insert into the table
                 */
                $is_entity_exist = $this->My_CI->reusable_model->get_search_query('inventory_stocks', 'inventory_stocks.id', array('entity_id' => $data['receiver_entity_id'], 'entity_type' => $data['receiver_entity_type'], 'inventory_id' => $is_part_exist[0]['inventory_id']), NULL, NULL, NULL, NULL, NULL)->result_array();
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
     * This Function is used to handle Fake Reschedule request By Miss Call Functionality
     * 1st) reschedule request will get rejected 
     * 2nd) booking will be escalated
     */
    function fake_reschedule_handling($userPhone,$id,$employeeID,$remarks,$bookingID=NULL){
        log_message('info', __METHOD__.'Call Details Added');
        $whereArray["service_center_booking_action.internal_status"] = "Reschedule"; 
        if($bookingID){
            $whereArray["booking_details.booking_id"] = $bookingID; 
        }
        else{
            $whereArray["users.phone_number"] = $userPhone; 
        }
        //get Booking id
        $bookingDetails = $this->My_CI->reusable_model->get_search_result_data("booking_details","booking_details.booking_id,booking_details.booking_date,booking_details.assigned_vendor_id,booking_details.booking_timeslot",
                $whereArray,array("users"=>"users.user_id=booking_details.user_id","service_center_booking_action"=>"service_center_booking_action.booking_id=booking_details.booking_id"),
                NULL,NULL,NULL,NULL,array("booking_details.booking_id"));
        $numberOfBookings = count($bookingDetails);
        if($numberOfBookings == 1){
            $booking_id = $bookingDetails[0]['booking_id'];
            $vendor_id = $bookingDetails[0]['assigned_vendor_id'];
            $escalation_reason_id = 11;
            $this->reject_reschedule_request($booking_id,$escalation_reason_id,$remarks,$id,$employeeID);
            $isEscalationDone =  $this->process_escalation($booking_id,$vendor_id,$escalation_reason_id,$remarks,TRUE,$id,$employeeID);
           return $isEscalationDone;
        }
    }
    /*
     * This function is used to reject reschedule request in case of fake reschedule
     */
    function reject_reschedule_request($booking_id,$escalation_reason_id,$remarks,$id,$employeeID){
        log_message('info', __METHOD__.'Call Details Added');
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
            $this->My_CI->notify->insert_state_change($booking_id,"Fake_Reschedule","Pending",$escalation_reason_final,$id,$employeeID, _247AROUND);
            return TRUE;
        }
        else{
            return FALSE;
        }
    }
    function process_escalation($booking_id,$vendor_id,$escalation_reason_id,$remarks,$checkValidation,$id,$employeeID){
        log_message('info',__FUNCTION__);
        $escalation['booking_id'] = $booking_id;
        $escalation['vendor_id'] = $vendor_id;
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
        if ($checkValidation) {
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
                // Update escalation flag and return userDeatils
                $userDetails = $this->My_CI->vendor_model->updateEscalationFlag($escalation_id, $escalation_policy_details, $escalation['booking_id']);
                log_message('info', "User Details " . print_r($userDetails, TRUE));
                log_message('info', "Vendor_ID " . $escalation['vendor_id']);
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
                    $this->My_CI->notify->sendEmail($from, $return_mail_to, $template[3] . "," . $cc, '', $subjectBody, $emailBody, "");
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
                $this->My_CI->notify->insert_state_change($escalation['booking_id'],"Escalation","Pending",$escalation_reason_final,$id,$employeeID, _247AROUND);
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
    function approved_rescheduled_bookings($reschedule_booking_id,$reschedule_booking_date,$reschedule_reason,$partner_id,$id,$employeeID){
         log_message('info', __FUNCTION__);
         foreach ($reschedule_booking_id as $booking_id) {
            $booking['booking_date'] = date('d-m-Y', strtotime($reschedule_booking_date[$booking_id]));
            $send['state'] = $booking['current_status'] = 'Rescheduled';
            $booking['internal_status'] = 'Rescheduled';
            $booking['update_date'] = date("Y-m-d H:i:s");
            $booking['mail_to_vendor'] = 0;
            $send['booking_id'] = $booking_id;
            $booking['reschedule_reason'] = $reschedule_reason[$booking_id];
            //check partner status from partner_booking_status_mapping table  
            $partner_status =$this->My_CI->booking_utilities->get_partner_status_mapping_data($booking['current_status'], $booking['internal_status'], $partner_id, $booking_id);
            if (!empty($partner_status)) {
                $booking['partner_current_status'] = $partner_status[0];
                $booking['partner_internal_status'] = $partner_status[1];
            }
            log_message('info', __FUNCTION__ . " update booking: " . print_r($booking, true));
            $this->My_CI->booking_model->update_booking($booking_id, $booking);
            $this->My_CI->booking_model->increase_escalation_reschedule($booking_id, "count_reschedule");
            $data['internal_status'] = "Pending";
            $data['current_status'] = "Pending";
            log_message('info', __FUNCTION__ . " update service cenetr action table: " . print_r($data, true));
            $this->My_CI->vendor_model->update_service_center_action($booking_id, $data);
            $url = base_url() . "employee/do_background_process/send_sms_email_for_booking";
            $this->My_CI->asynchronous_lib->do_background_process($url, $send);
            //Log this state change as well for this booking
            //param:-- booking id, new state, old state, employee id, employee name
            $this->My_CI->notify->insert_state_change($booking_id, _247AROUND_RESCHEDULED, _247AROUND_PENDING, $booking['reschedule_reason'], $id,$employeeID, _247AROUND);          
            log_message('info', __FUNCTION__ . " partner callback: " . print_r($booking_id, true));
            $this->My_CI->partner_cb->partner_callback($booking_id);
            log_message('info', 'Rescheduled- Booking id: ' . $booking_id . " Rescheduled By " . $employeeID . " data " . print_r($data, true));
        }
    } 
}
