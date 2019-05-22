<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Upcountry extends CI_Controller {

    /**
     * load list model and helpers
     */
    function __Construct() {
        parent::__Construct();

        $this->load->model('upcountry_model');
        $this->load->model('booking_model');
        $this->load->model('vendor_model');
        $this->load->library("miscelleneous");
        $this->load->library("notify");

        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee')) {
            return TRUE;
        } else {
            redirect(base_url() . "employee/login");
        }
    }

    function index() {
        
    }

    /**
     * @desc: This method is used to load view to assign SC for Upcountry
     */
    function assign_sc_to_upcountry() {
        log_message('info', __FUNCTION__);
        $data['service_center_id'] = $this->input->post("service_center_id");
        $data['state'] = $this->input->post("state");
        if (!empty($data['service_center_id'])) {
            $data['all_state'] = $this->vendor_model->getall_state();
            $this->miscelleneous->load_nav_header();
            $this->load->view('employee/assign_vendor_to_upcountry', $data);
        } else {
            echo "Please Vist Again";
        }
    }

    /**
     * @desc: This method is used to assign upcountry to SF.
     * This method get input field inthe Array form.
     * Insert All data into table As a Batch
     * @param String $service_center_id
     */
    function add_sub_sf_upcountry($service_center_id) {
        log_message('info', __FUNCTION__ . " Service Center Id " . $service_center_id);
        $state = $this->input->post('state');
        $district = $this->input->post('city');
        $pincode = $this->input->post('pincode');
        $charges = $this->input->post('charges');
        $data = array();
        $flag = 1;
        foreach ($state as $key => $value) {
            $flag = 1;
            $where = array("pincode" => $pincode[$key], 'service_center_id' => $service_center_id, 'district' => $district[$key]);
            $exist_hq_office = $this->upcountry_model->get_sub_service_center_details($where);
            if (!empty($exist_hq_office)) {
                $this->upcountry_model->update_sub_service_center_upcountry_details(array('active' => 1), $exist_hq_office[0]['id']);

                $flag = 0;
                $userSession = array('error' => 'Same District ' . $exist_hq_office[0]['district'] .
                    " Same Pincode " . $pincode[$key] . " Already Added. Please check & Try Again");
                $this->session->set_userdata($userSession);
                log_message('info', __FUNCTION__ . 'District ' . $exist_hq_office[0]['district'] .
                        " Already Added in the Pincode " . $pincode[$key] . " Please check District & Try Again" . print_r($data, true));
               break;

            }
            if ($flag == 1) {
                $data[$key]['state'] = $value;
                $data[$key]['district'] = $district[$key];
                $data[$key]['pincode'] = $pincode[$key];
                $data[$key]['upcountry_rate'] = $charges[$key];
                $data[$key]['service_center_id'] = $service_center_id;
                $data[$key]['create_date'] = date("Y-m-d H:i:s");
            }
        }
        if (!empty($flag)) {
            if (!empty($data)) {
                $response = $this->upcountry_model->insert_batch_sub_sc_details($data);
                $this->vendor_model->edit_vendor(array('is_upcountry' => '1'), $service_center_id);
                if ($response) {
                    $log = array(
                    "entity" => "vendor",
                    "entity_id" => $service_center_id,
                    "agent_id" => $this->session->userdata('id'),
                    "action" =>  "SC HQ Added"
                );
                $this->vendor_model->insert_log_action_on_entity($log);
                    $userSession = array('success' => 'Upcountry Charges Added');
                    $this->session->set_userdata($userSession);
                    log_message('info', __FUNCTION__ . " Added Upcountry Charges for SC id " . $service_center_id);
                } else {
                    $userSession = array('error' => 'Upcountry Charges Insertion Failed');
                    $this->session->set_userdata($userSession);
                    log_message('info', __FUNCTION__ . " Upcountry Charges Not Added " . print_r($data, true));
                }
                redirect(base_url() . "employee/vendor/viewvendor");
            } else {
                $userSession = array('error' => 'Upcountry Charges Insertion Failed');
                $this->session->set_userdata($userSession);
                log_message('info', __FUNCTION__ . " Upcountry Charges Not Added  service center id" . print_r($service_center_id));
                redirect(base_url() . "employee/vendor/viewvendor");
            }
        } else {
            redirect(base_url() . "employee/vendor/viewvendor");
        }
    }

    /**
     * @desc: This is used to load failed upcountry booking edittable list. 
     */
    function get_upcountry_failed_details() {
        $upcountry_details['details'] = $this->upcountry_model->get_upcountry_failed_details();
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/upcountry_failed_details', $upcountry_details);
    }

    /**
     * @desc: This method update upcountry failed booking .
     * This is called by Ajax 
     */
//    function update_failed_upcountry_booking() {
//        $booking_id = $this->input->post('booking_id');
//        $query1 = $this->booking_model->getbooking_history($booking_id);
//
//        $amount_due = $query1[0]['amount_due'];
//        $upcountry_vendor['upcountry_distance'] = ($this->input->post('distance') * 2);
//        $upcountry_vendor['sf_upcountry_rate'] = $query1[0]['sf_upcountry_rate'];
//        $upcountry_vendor['is_upcountry'] = "1";
//        $upcountry_vendor['sub_vendor_id'] = $query1[0]['sub_vendor_id'];
//        $upcountry_vendor['vendor_id'] = $query1[0]['assigned_vendor_id'];
//        $upcountry_vendor['upcountry_pincode'] = $query1[0]['upcountry_pincode'];
//        $p_where = array('id' =>$query1[0]['partner_id'] );
//        $partner_details = $this->partner_model->get_all_partner($p_where);
//
//        $data = $this->upcountry_model->mark_upcountry_vendor($upcountry_vendor, $partner_details);
//
//        $status = false;
//        switch ($data['message']) {
//            case NOT_UPCOUNTRY_BOOKING:
//                $booking['sub_vendor_id'] = NULL;
//                $booking['is_upcountry'] = 0;
//                $booking['sf_upcountry_rate'] = NULL;
//                $booking['upcountry_distance'] = NULL;
//                $booking['upcountry_pincode'] = NULL;
//                $this->booking_model->update_booking($booking_id, $booking);
//                $status = TRUE;
//                break;
//
//            case UPCOUNTRY_BOOKING:
//            case UPCOUNTRY_LIMIT_EXCEED:
//                $is_upcountry = $this->upcountry_model->is_upcountry_booking($booking_id);
//                $booking['upcountry_distance'] = $data['upcountry_distance'];
//                $booking['is_upcountry'] = 1;
//                $booking['partner_upcountry_rate'] = $data['partner_upcountry_rate'];
//                if (!empty($is_upcountry)) {
//                    if ($data['message'] == UPCOUNTRY_BOOKING) {
//                        log_message('info', __METHOD__ . " => Upcountry Booking Free Booking " . $booking_id);
//                        $booking['upcountry_paid_by_customer'] = 0;
//                        $this->booking_model->update_booking($booking_id, $booking);
//                        $status = TRUE;
//                    } else if ($data['partner_upcountry_approval'] == 1 && $data['message'] == UPCOUNTRY_LIMIT_EXCEED) {
//
//                        log_message('info', __METHOD__ . " => Upcountry Waiting for Approval " . $booking_id);
//                        $booking['assigned_vendor_id'] = NULL;
//                        $booking['internal_status'] = UPCOUNTRY_BOOKING_NEED_TO_APPROVAL;
//                        $booking['upcountry_partner_approved'] = '0';
//                        $booking['upcountry_paid_by_customer'] = 0;
//
//                        $this->booking_model->update_booking($booking_id, $booking);
//                        $this->service_centers_model->delete_booking_id($booking_id);
//
//                        $this->notify->insert_state_change($booking_id, "Waiting Partner Approval", _247AROUND_PENDING, "Waiting Upcountry to Approval", $this->session->userdata('id'), $this->session->userdata('employee_id'), _247AROUND);
//                        $unit_details = $this->booking_model->get_unit_details(array('booking_id' => $booking_id));
//
//                        $up_mail_data['name'] = $query1[0]['name'];
//                        $up_mail_data['appliance'] = $query1[0]['services'];
//                        $up_mail_data['booking_address'] = $query1[0]['booking_address'];
//                        $up_mail_data['city'] = $query1[0]['city'];
//                        $up_mail_data['state'] = $query1[0]['state'];
//                        $up_mail_data['booking_pincode'] = $query1[0]['booking_pincode'];
//                        $up_mail_data['booking_id'] = $query1[0]['booking_id'];
//                        $up_mail_data['booking_primary_contact_no'] = $query1[0]['booking_primary_contact_no'];
//                        $up_mail_data['price_tags'] = $unit_details[0]['price_tags'];
//                        $up_mail_data['appliance_brand'] = $unit_details[0]['appliance_brand'];
//                        $up_mail_data['appliance_category'] = $unit_details[0]['appliance_category'];
//                        $up_mail_data['appliance_capacity'] = $unit_details[0]['appliance_capacity'];
//                        $up_mail_data['upcountry_distance'] = $booking[0]['upcountry_distance'];
//
//                        $message1 = $this->load->view('employee/upcountry_approval_template', $up_mail_data, true);
//
//                        $partner_am_email = "";
//                        if (!empty($partner_details[0]['account_manager_id'])) {
//                            $partner_am_email = $this->employee_model->getemployeefromid($partner_details[0]['account_manager_id'])[0]['official_email'];
//                        }
//                        if ($booking['upcountry_distance'] > 300) {
//                            $subject = "Upcountry Distance More Than 300 - Booking ID " . $query1[0]['booking_id'];
//                            $to = NITS_ANUJ_EMAIL_ID;
//                            $cc = "abhaya@247around.com ,".$partner_am_email;
//                        } else {
//                            $subject = "Upcountry Charges Approval Required - Booking ID " . $query1[0]['booking_id'];
//                            $to = $partner_details[0]['upcountry_approval_email'];
//                            $cc = NITS_ANUJ_EMAIL_ID.$partner_am_email;
//                        }
//
//                        $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, "", $subject, $message1, "");
//
//                        $status = FALSE;
//                    } else if ($data['partner_upcountry_approval'] == 0 && $data['message'] == UPCOUNTRY_LIMIT_EXCEED) {
//
//                        log_message('info', __METHOD__ . " => Upcountry, partner not provide approval" . $booking_id);
//                        $this->booking_model->update_booking($booking_id, $booking);
//                        $this->process_cancel_form($booking_id, "Pending", UPCOUNTRY_CHARGES_NOT_APPROVED, "", $this->session->userdata('id'), $this->session->userdata('employee_id'), $query1[0]['partner_id']);
//
//                        $to = NITS_ANUJ_EMAIL_ID;
//                        $cc = "abhaya@247around.com";
//                        $message1 = $booking_id . " has auto cancelled because upcountry limit exceed "
//                                . "and partner does not provide upcountry charges approval. Upcountry Distance " . $data['upcountry_distance'].
//                                " Upcountry Pincode ". $data['upcountry_pincode']. " SF Name ". $query1[0]['vendor_name'];
//                        $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, "", 'Upcountry Auto Cancel Booking', $message1, "");
//
//                        $status = FALSE;
//                    }
//                } else {
//                    $booking['upcountry_paid_by_customer'] = 1;
//                    $unit_details = $this->booking_model->get_unit_details(array('booking_id' => $booking_id));
//                    $cus_net_payable = 0;
//                    foreach ($unit_details as $value) {
//                        $cus_net_payable += $value['customer_net_payable'];
//                    }
//                    $booking['amount_due'] = $cus_net_payable + ($data['partner_upcountry_rate'] * $data['upcountry_distance']);
//
//
//                    $this->booking_model->update_booking($booking_id, $booking);
//                    $status = TRUE;
//                }
//                break;
//        }
//
//        $this->notify->insert_state_change($booking_id, "Upcountry modified", ASSIGNED_VENDOR, "Upcountry details Corrected, Distance " . $upcountry_vendor['upcountry_distance'] . " KM", $this->session->userdata('id'), $this->session->userdata('employee_id'), _247AROUND);
//
//        if ($status) {
//            $this->miscelleneous->send_sms_create_job_card($query1);
//        }
//    }

    /**
     * @desc Waiting to Approval upcountry booking, load in Admin Panel
     */
    function get_waiting_for_approval_upcountry_charges() {
        $data['booking_details'] = $this->upcountry_model->get_waiting_for_approval_upcountry_charges("");
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/get_waiting_to_approval_upcountry', $data);
    }

    /**
     * @desc: Update previous booking  upcountry
     */
    function update_previous_booking() {
        echo ".....Entering........" . PHP_EOL;
        $booking_details = $this->upcountry_model->get_booking();
        foreach ($booking_details as $value1) {

            $this->miscelleneous->assign_upcountry_booking($value1['booking_id'], "1", "247Around");
        }
    }

    /**
     * @desc: This function is used to show the form to get the distance between two pincode
     * @param: void
     * @return : void
     */
    function get_distance_between_pincodes_form() {
        $data['is_ajax'] = false;
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/get_distance_between_pincode_view', $data);
    }

    /**
     * @desc: This function is used to get the distance between two pincode
     * @param: void
     * @return : void
     */
    function get_distance_between_pincodes() {
        $pincode1 = $this->input->post('pincode1');
        $pincode2 = $this->input->post('pincode2');
        $distance = $this->upcountry_model->get_distance_between_pincodes($pincode1, $pincode2);
        if (!empty($distance)) {
            $data['is_ajax'] = true;
            $data['table_data'] = array('pincode1' => $pincode1,
                'pincode2' => $pincode2,
                'distance' => $distance[0]['distance']);

            $this->load->view('employee/get_distance_between_pincode_view', $data);
        } else {
            echo "error";
        }
    }
    
    
    /**
     * @desc This method is used to update the distance for given pincode
     * @param string
     * @return string 
     */
    function update_pincode_distance() {
        $pincode1 = $this->input->post('pincode1');
        $pincode2 = $this->input->post('pincode2');
        $distance = $this->input->post('new_distance');
        $regex = "/^[0-9]+(\.[0-9]{1,2})?$/";
        if (preg_match($regex, $distance)) {
            $update = $this->upcountry_model->update_pincode_distance($pincode1,$pincode2,$distance);
            if($update){
                echo "success";
            }else{
                echo "error";
            }
        } else {
            echo "error";
        }
    }
    
    
    /**
     * @desc This method is used to insert  the distance for given pincode if it is not exist
     * @param void
     * @return string 
     */
    function add_new_pincode_distance(){
        $pincode1 = $this->input->post('pincode1');
        $pincode2 = $this->input->post('pincode2');
        $distance = $this->input->post('new_distance');
        $regex = "/^[0-9]+(\.[0-9]{1,2})?$/";
        $agent_id = $this->session->userdata('id');
        if (preg_match($regex, $distance) && strlen($pincode1) === 6 && strlen($pincode2) === 6) {
            $insert_id = $this->upcountry_model->insert_distance($pincode1,$pincode2,$distance,$agent_id);
            if($insert_id){
                echo "success";
            }else{
                echo "error";
            }
        } else {
            echo "error";
        }
    }

}
