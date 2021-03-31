<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('memory_limit', -1);

class Service_centers extends CI_Controller {

    /**
     * load list modal and helpers
     */
    function __Construct() {
        parent::__Construct();
        $this->load->model('service_centers_model');
        $this->load->model('service_centre_charges_model');
        $this->load->model('booking_model');
        $this->load->model('reporting_utils');
        $this->load->model('dealer_model');
        $this->load->model('partner_model');
        $this->load->model('upcountry_model');
        $this->load->model('vendor_model');
        $this->load->model('user_model');
        $this->load->model('employee_model');
        $this->load->model('invoices_model');
        $this->load->model('penalty_model');
        $this->load->model('inventory_model');
        $this->load->model('cp_model');
        $this->load->library("pagination");
        $this->load->library('asynchronous_lib');
        $this->load->library('booking_utilities');
        $this->load->library('warranty_utilities');
        $this->load->library("session");
        $this->load->library('s3');
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('PHPReport');
        $this->load->helper('download');
        $this->load->library('user_agent');
        $this->load->library('notify');
        $this->load->library('buyback');
        $this->load->library("partner_cb");
        $this->load->library("miscelleneous");
        $this->load->library("push_notification_lib");
        $this->load->library("paytm_payment_lib");
        $this->load->library("validate_serial_no");
        $this->load->library("invoice_lib");
        $this->load->library("booking_creation_lib");
        $this->load->helper('url'); 
    }

    /**
     * @desc: This is used to load vendor Login Page
     *
     * @param: void
     * @return: void
     */
    function index() {
        $select = "partner_logo,alt_text";
        $where = array('partner_logo IS NOT NULL' => NULL);
        $data['partner_logo'] = $this->booking_model->get_partner_logo($select, $where);
        $data['is_saas'] = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        $this->load->view('service_centers/service_center_login', $data);
    }

    /**
     * @desc: this is used to load pending booking
     * @param: booking id (optional)
     * @return: void
     */
    function pending_booking($booking_id = "") {
        $this->checkUserSession();
        $data['booking_id'] = $booking_id;
        $data['multiple_booking'] = 0;
        if ($this->input->post('booking_id_status')) {
            $temp = explode(",", $this->input->post('booking_id_status'));
            $data['booking_id'] = implode("','", $temp);
            $data['multiple_booking'] = 1;
        }
        $rating_data = $this->service_centers_model->get_vendor_rating_data($this->session->userdata('service_center_id'));
        if (!empty($rating_data[0]['rating'])) {
            $data['rating'] = $rating_data[0]['rating'];
            $data['count'] = $rating_data[0]['count'];
        } else {
            $data['rating'] = 0;
            $data['count'] = $rating_data[0]['count'];
        }
        $data['msl'] = $this->miscelleneous->get_msl_amounts($this->session->userdata('service_center_id'));
        $this->load->view('service_centers/header');
        $this->load->view('service_centers/pending_booking', $data);
        if (!$this->session->userdata("login_by")) {
            $this->load->view('employee/header/push_notification');
        }
    }

    function appliance_model_list() {
        $this->checkUserSession();
        // $this->miscelleneous->load_nav_header();
        $this->load->view('service_centers/header');
        $this->load->view("service_centers/appliance_model_details");
    }

    function get_inventory_by_model($model_number_id = '', $service_id = '', $booking_id = '') {

        if (!empty($booking_id)) {
            $booking_unit = $this->booking_model->getunit_details($booking_id, $service_id);
            if (!empty($booking_unit)) {
                $sf_data['model'] = $booking_unit[0]['model_number'];
            }
        }

        if (!empty($model_number_id) && empty($service_id)) {
            $model_number_id = urldecode($model_number_id);
            $sf_data['model_number_id'] = $model_number_id;
            $sf_data['partner_id'] = '';
            $sf_data['service_id'] = '';
            $data['inventory_details'] = $this->inventory_model->get_inventory_model_mapping_data('inventory_master_list.*,appliance_model_details.model_number,services.services', array('inventory_model_mapping.model_number_id' => $model_number_id, 'inventory_model_mapping.active' => 1));
        } else {
            $data['inventory_details'] = array();
            $sf_data['model_number_id'] = '';
            $sf_data['partner_id'] = urldecode($model_number_id);
            $sf_data['service_id'] = $service_id;
        }
        $sf_data['saas_flag'] = $data['saas_flag'] = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        if ($this->session->userdata('employee_id')) {
            $this->miscelleneous->load_nav_header();
            $this->load->view('employee/show_inventory_details_by_model', $data);
        } else if ($this->session->userdata('partner_id')) {
            $this->miscelleneous->load_partner_nav_header();
            $this->load->view('employee/show_inventory_details_by_model', $data);
            $this->load->view('partner/partner_footer');
        } else if ($this->session->userdata('userType') == 'service_center') {


            $this->load->view('service_centers/header');
            $this->load->view('service_centers/show_inventory_details_by_model', $sf_data);
        }
    }

    function get_service_id_by_partner() {
        $partner_id = $this->input->get('partner_id');
        if ($partner_id) {
            $appliance_list = $this->service_centers_model->get_service_brands_for_partner($partner_id);
            if ($this->input->get('is_option_selected')) {
                $option = '<option  selected="" disabled="">Select Appliance  </option>';
            } else {
                $option = '';
            }

            foreach ($appliance_list as $value) {
                $option .= "<option value='" . $value['id'] . "'";
                $option .= " > ";
                $option .= $value['services'] . "</option>";
            }

            if ($this->input->get('is_all_option')) {
                $option .= '<option value="all" >All</option>';
            }
            echo $option;
        } else {
            echo FALSE;
        }
    }

    function get_header_summary() {
        //firstly,if we have data in cache then take data from cache otherwise caculate data
        if (!$this->cache->file->get('Sfdashboard_' . $this->session->userdata('service_center_id'))) {
            $service_center_id = $this->session->userdata('service_center_id');
            $data['eraned_details'] = $this->service_centers_model->get_sc_earned($service_center_id);
            $data['cancel_booking'] = $this->service_centers_model->count_cancel_booking_sc($service_center_id);
            if ($this->session->userdata('is_upcountry') == 1) {
                $data['upcountry'] = $this->upcountry_model->upcountry_service_center_3_month_price($service_center_id);
            }

            $this->cache->file->save('Sfdashboard_' . $this->session->userdata('service_center_id'), $data);
            //for testing data come from cache or dynamic calculation store that data in database
            $sf_dashboard_id = $this->service_centers_model->dashboard_data_count('db_count', 'cache_count');
        } else {
            $data = $this->cache->file->get('Sfdashboard_' . $this->session->userdata('service_center_id'));
            //for testing data come from cache or dynamic calculation store that data in database
            $sf_dashboard_id = $this->service_centers_model->dashboard_data_count('cache_count', 'db_count');
        }
        $this->load->view("service_centers/header_summary", $data);
    }

    function pending_booking_on_tab($booking_id = "") {
        if ($this->input->post('booking_list')) {
            $booking_id = $this->input->post('booking_list');
        }
        $service_center_id = $this->session->userdata('service_center_id');
        $data['bookings'] = $this->service_centers_model->pending_booking($service_center_id, $booking_id);
        if ($this->session->userdata('is_update') == 1) {
            //$data['engineer_details'] = $this->vendor_model->get_engineers($service_center_id);
            $data['spare_parts_data'] = $this->service_centers_model->get_updated_spare_parts_booking($service_center_id);
        }
        //$data['collateral'] = $this->service_centers_model->get_collateral_for_service_center_bookings($service_center_id);
        $data['service_center_id'] = $service_center_id;
        $arrEngineer = $this->vendor_model->getVendorDetails("isEngineerApp", array("id" => $service_center_id));
        $data['is_engineer_app'] = (!empty($arrEngineer[0]['isEngineerApp']) ? $arrEngineer[0]['isEngineerApp'] : "");
        $data['saas_module'] = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        $this->load->view('service_centers/pending_on_tab', $data);
    }

    /**
     * @desc: this is used to get booking details by using booking ID And load booking booking details view
     * @param: booking id
     * @return: void
     */
    function booking_details($code) {
        log_message('info', __FUNCTION__ . " Booking ID: " . base64_decode(urldecode($code)));
        $this->checkUserSession();
        $booking_id = base64_decode(urldecode($code));
        $data['booking_history'] = $this->booking_model->getbooking_history($booking_id);
        $data['booking_symptom'] = $this->booking_model->getBookingSymptom($booking_id);
        $data['booking_files'] = $this->booking_model->get_booking_files(array('booking_id' => $booking_id, 'file_description_id' => SF_PURCHASE_INVOICE_FILE_TYPE));
        if ($data['booking_history'][0]['dealer_id']) {
            $dealer_detail = $this->dealer_model->get_dealer_details('dealer_name, dealer_phone_number_1', array('dealer_id' => $data['booking_history'][0]['dealer_id']));
            $data['booking_history'][0]['dealer_name'] = $dealer_detail[0]['dealer_name'];
            $data['booking_history'][0]['dealer_phone_number_1'] = $dealer_detail[0]['dealer_phone_number_1'];
        }
        $unit_where = array('booking_id' => $booking_id, 'pay_to_sf' => '1');
        $booking_unit_details = $this->booking_model->get_unit_details($unit_where);
        $data['booking_state_change_data'] = $this->booking_model->get_booking_state_change_by_id($booking_id);
        $data['sms_sent_details'] = $this->booking_model->get_sms_sent_details($booking_id);

        if (!is_null($data['booking_history'][0]['sub_vendor_id'])) {
            $data['dhq'] = $this->upcountry_model->get_sub_service_center_details(array('id' => $data['booking_history'][0]['sub_vendor_id']));
        }
        $engineer_action_not_exit = false;
        if ($this->session->userdata('is_engineer_app') == 1) {
            foreach ($booking_unit_details as $key1 => $b) {

                $unitWhere = array("engineer_booking_action.booking_id" => $booking_id,
                    "engineer_booking_action.unit_details_id" => $b['id'], "service_center_id" => $data['booking_history'][0]['assigned_vendor_id']);
                $en = $this->engineer_model->getengineer_action_data("engineer_booking_action.*", $unitWhere);
                if (!empty($en)) {
                    $booking_unit_details[$key1]['en_serial_number'] = $en[0]['serial_number'];
                    $booking_unit_details[$key1]['en_serial_number_pic'] = $en[0]['serial_number_pic'];
                    $booking_unit_details[$key1]['en_is_broken'] = $en[0]['is_broken'];
                    $booking_unit_details[$key1]['en_internal_status'] = $en[0]['internal_status'];
                    $booking_unit_details[$key1]['en_current_status'] = $en[0]['current_status'];

                    $engineer_action_not_exit = true;
                }
            }
            if (isset($engineer_action_not_exit)) {
                $sig_table = $this->engineer_model->getengineer_sign_table_data("*", array("booking_id" => $booking_id,
                    "service_center_id" => $data['booking_history'][0]['assigned_vendor_id']));
                $data['signature_details'] = $sig_table;
            }
        }

        $isPaytmTxn = $this->paytm_payment_lib->get_paytm_transaction_data($booking_id);
        if (!empty($isPaytmTxn)) {
            if ($isPaytmTxn['status']) {
                $data['booking_history'][0]['onlinePaymentAmount'] = $isPaytmTxn['total_amount'];
            }
        }

        //get engineer name
        if ($data['booking_history'][0]['assigned_engineer_id']) {
            $engineer_name = $this->engineer_model->get_engineers_details(array("id" => $data['booking_history'][0]['assigned_engineer_id']), "name");
            if (!empty($engineer_name)) {
                $data['booking_history'][0]['assigned_engineer_name'] = $engineer_name[0]['name'];
            }
        }

        $data['engineer_action_not_exit'] = $engineer_action_not_exit;
        $data['symptom'] = $data['completion_symptom'] = $data['technical_defect'] = $data['technical_solution'] = array();
        if (count($data['booking_symptom']) > 0) {
            if (!is_null($data['booking_symptom'][0]['symptom_id_booking_creation_time'])) {
                $data['symptom'] = $this->booking_request_model->get_booking_request_symptom('symptom', array('symptom.id' => $data['booking_symptom'][0]['symptom_id_booking_creation_time']));

                if (count($data['symptom']) <= 0) {
                    $data['symptom'][0] = array("symptom" => "Default");
                }
            }
            if (!is_null($data['booking_symptom'][0]['symptom_id_booking_completion_time'])) {
                $data['completion_symptom'] = $this->booking_request_model->get_booking_request_symptom('symptom', array('symptom.id' => $data['booking_symptom'][0]['symptom_id_booking_completion_time']));

                if (count($data['completion_symptom']) <= 0) {
                    $data['completion_symptom'][0] = array("symptom" => "Default");
                }
            }
            if (!is_null($data['booking_symptom'][0]['defect_id_completion'])) {
                $cond['where'] = array('defect.id' => $data['booking_symptom'][0]['defect_id_completion']);
                $data['technical_defect'] = $this->booking_request_model->get_defects('defect', $cond);

                if (count($data['technical_defect']) <= 0) {
                    $data['technical_defect'][0] = array("defect" => "Default");
                }
            }
            if (!is_null($data['booking_symptom'][0]['solution_id'])) {
                $data['technical_solution'] = $this->booking_request_model->symptom_completion_solution('technical_solution', array('symptom_completion_solution.id' => $data['booking_symptom'][0]['solution_id']));

                if (count($data['technical_solution']) <= 0) {
                    $data['technical_solution'][0] = array("technical_solution" => "Default");
                }
            }
        } else {
            $data['symptom'][0] = array("symptom" => "Default");

            if (in_array($data['booking_history'][0]['internal_status'], array(SF_BOOKING_COMPLETE_STATUS, _247AROUND_COMPLETED))) {
                $data['completion_symptom'][0] = array("symptom" => "Default");
                $data['technical_defect'][0] = array("defect" => "Default");
                $data['technical_solution'][0] = array("technical_solution" => "Default");
            }
        }

        $data['unit_details'] = $booking_unit_details;
        $data['penalty'] = $this->penalty_model->get_penalty_on_booking_by_booking_id($booking_id, $data['booking_history'][0]['assigned_vendor_id']);
        $data['paytm_transaction'] = $this->paytm_payment_model->get_paytm_transaction_and_cashback($booking_id);

        if (!empty($data['booking_history']['spare_parts'])) {
            $spare_parts_list = array();
            foreach ($data['booking_history']['spare_parts'] as $key => $val) {
                if (!empty($val['requested_inventory_id'])) {
                    $inventory_spare_parts_details = $this->inventory_model->get_generic_table_details('inventory_master_list', 'inventory_master_list.part_number,inventory_master_list.part_name', array('inventory_master_list.inventory_id' => $val['requested_inventory_id']), array());
                    if (!empty($inventory_spare_parts_details)) {
                        $spare_parts_list[] = array_merge($val, array('final_spare_parts' => $inventory_spare_parts_details[0]['part_name']));
                    }
                }
            }
        }

        if (!empty($spare_parts_list)) {
            $data['booking_history']['spare_parts'] = $spare_parts_list;
        }
/*
        $select = "courier_company_invoice_details.id, courier_company_invoice_details.awb_number, courier_company_invoice_details.company_name, courier_company_invoice_details.courier_charge, courier_company_invoice_details.billable_weight, courier_company_invoice_details.actual_weight, courier_company_invoice_details.create_date, courier_company_invoice_details.update_date, courier_company_invoice_details.partner_id, courier_company_invoice_details.basic_billed_charge_to_partner, courier_company_invoice_details.partner_invoice_id, courier_company_invoice_details.booking_id, courier_company_invoice_details.box_count, courier_company_invoice_details.courier_invoice_file, courier_company_invoice_details.shippment_date, courier_company_invoice_details.created_by, courier_company_invoice_details.is_exist";

        $spare_parts_details = $this->partner_model->get_spare_parts_by_any('spare_parts_details.awb_by_sf', array('spare_parts_details.booking_id' => $booking_id, 'spare_parts_details.awb_by_sf !=' => ''));
        if (!empty($spare_parts_details)) {
            $awb = $spare_parts_details[0]['awb_by_sf'];
            $courier_boxes_weight = $this->inventory_model->get_generic_table_details('courier_company_invoice_details', $select, array('awb_number' => $awb), array());
            if (!empty($courier_boxes_weight)) {
                $data['courier_boxes_weight_details'] = $courier_boxes_weight[0];
            }
        }

        $spare_parts_list = $this->partner_model->get_spare_parts_by_any('spare_parts_details.awb_by_wh', array('spare_parts_details.booking_id' => $booking_id, "spare_parts_details.awb_by_wh IS NOT NULL " => NULL));
        if (!empty($spare_parts_list)) {
            $courier_boxes_weight_wh = $this->inventory_model->get_generic_table_details('courier_company_invoice_details', $select, array('awb_number' => $spare_parts_list[0]['awb_by_wh']), array());
            if (!empty($courier_boxes_weight_wh)) {
                $data['wh_courier_boxes_weight_details'] = $courier_boxes_weight_wh[0];
            }
        }
 */
        //$data['spare_history'] = $this->partner_model->get_spare_state_change_tracking("spare_state_change_tracker.id,spare_state_change_tracker.spare_id,spare_state_change_tracker.action,spare_state_change_tracker.remarks,spare_state_change_tracker.agent_id,spare_state_change_tracker.entity_id,spare_state_change_tracker.entity_type, spare_state_change_tracker.create_date, spare_parts_details.parts_requested",array('spare_parts_details.booking_id' => $booking_id), true);
        $data['saas_module'] = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);

        $this->load->view('service_centers/header');
        $this->load->view('service_centers/booking_details', $data);
    }

    /**
     * @desc: This is used to get complete booking form.
     * @param: booking id
     * @return: void
     */
    function complete_booking_form($code) {
        log_message('info', __FUNCTION__ . " Booking ID: " . base64_decode(urldecode($code)));
        $this->checkUserSession();
        $booking_id = base64_decode(urldecode($code));
        $data['booking_id'] = $booking_id;
        $data['booking_history'] = $this->booking_model->getbooking_history($booking_id);
        $data['booking_symptom'] = $this->booking_model->getBookingSymptom($booking_id);
        $bookng_unit_details = $this->booking_model->getunit_details($booking_id);
        $partner_id = $data['booking_history'][0]['partner_id'];
        //Define Blank Price array
        $data['prices'] = array();
        $source = $this->partner_model->getpartner_details('bookings_sources.source, partner_type', array('bookings_sources.partner_id' => $data['booking_history'][0]['partner_id']));
        //Add source name in booking_history array
        $data['booking_history'][0]['source_name'] = $source[0]['source'];

        $where = array(
            "partner_appliance_details.partner_id" => $data['booking_history'][0]['partner_id'],
            'partner_appliance_details.service_id' => $data['booking_history'][0]['service_id'],
            'partner_appliance_details.brand' => $bookng_unit_details[0]['brand'],
            'partner_appliance_details.active' => 1,
            'appliance_model_details.active' => 1,
            "NULLIF(model, '') IS NOT NULL" => NULL);

        $data['model_data'] = $this->partner_model->get_model_number("appliance_model_details.id, appliance_model_details.model_number", $where);


        $price_tags = array();
        foreach ($bookng_unit_details as $key1 => $b) {

            if ($source[0]['partner_type'] == OEM) {
                $prices = $this->booking_model->getPricesForCategoryCapacity($data['booking_history'][0]['service_id'], $bookng_unit_details[$key1]['category'], $bookng_unit_details[$key1]['capacity'], $data['booking_history'][0]['partner_id'], $b['brand']);
            }
            //If partner type is not OEM then check is brand white list for partner if brand is white listed then use brands if not then 
            else {
                $isWbrand = "";
                $whiteListBrand = $this->partner_model->get_partner_blocklist_brand(array("partner_id" => $data['booking_history'][0]['partner_id'], "brand" => $b['brand'], "service_id" => $data['booking_history'][0]['service_id'], "whitelist" => 1), "*");
                if (!empty($whiteListBrand)) {
                    $isWbrand = $b['brand'];
                }
                $prices = $this->booking_model->getPricesForCategoryCapacity($data['booking_history'][0]['service_id'], $bookng_unit_details[$key1]['category'], $bookng_unit_details[$key1]['capacity'], $partner_id, $isWbrand);
            }

            $broken = 0;
            foreach ($b['quantity'] as $key2 => $u) {
                $price_tags1 = str_replace('(Free)', '', $u['price_tags']);
                $price_tags2 = str_replace('(Paid)', '', $price_tags1);
                array_push($price_tags, $price_tags2);
                if ($this->session->userdata('is_engineer_app') == 1) {

                    $unitWhere = array("engineer_booking_action.booking_id" => $booking_id,
                        "engineer_booking_action.unit_details_id" => $u['unit_id'], "service_center_id" => $data['booking_history'][0]['assigned_vendor_id']);
                    $en = $this->engineer_model->getengineer_action_data("engineer_booking_action.*", $unitWhere);
                    if (!empty($en)) {
                        $bookng_unit_details[$key1]['quantity'][$key2]['en_serial_number'] = $en[0]['serial_number'];
                        $bookng_unit_details[$key1]['quantity'][$key2]['en_serial_number_pic'] = $en[0]['serial_number_pic'];
                        $bookng_unit_details[$key1]['quantity'][$key2]['en_is_broken'] = $en[0]['is_broken'];
                        $bookng_unit_details[$key1]['quantity'][$key2]['en_internal_status'] = $en[0]['internal_status'];
                        $bookng_unit_details[$key1]['quantity'][$key2]['en_current_status'] = $en[0]['current_status'];
                        $bookng_unit_details[$key1]['quantity'][$key2]['en_purchase_date'] = date('Y-m-d', strtotime($en[0]['sf_purchase_date']));
                        $bookng_unit_details[$key1]['quantity'][$key2]['en_service_charge'] = $en[0]['service_charge'];
                        $bookng_unit_details[$key1]['quantity'][$key2]['en_additional_service_charge'] = $en[0]['additional_service_charge'];
                        $bookng_unit_details[$key1]['quantity'][$key2]['en_parts_cost'] = $en[0]['parts_cost'];
                        $bookng_unit_details[$key1]['quantity'][$key2]['en_booking_status'] = $en[0]['booking_status'];
                        $bookng_unit_details[$key1]['en_model_number'] = $en[0]['model_number'];
                        $bookng_unit_details[$key1]['en_symptom_id'] = $en[0]['symptom'];
                        $bookng_unit_details[$key1]['en_defect_id'] = $en[0]['defect'];
                        $bookng_unit_details[$key1]['en_solution_id'] = $en[0]['solution'];
                        $bookng_unit_details[$key1]['en_closing_remark'] = $en[0]['closing_remark'];
                        $bookng_unit_details[$key1]['en_amount_paid'] = $en[0]['amount_paid'];
                        $bookng_unit_details[$key1]['en_purchase_invoice'] = $en[0]['purchase_invoice'];
                        $bookng_unit_details[$key1]['en_closed_date'] = $en[0]['closed_date'];
                        if ($en[0]['is_broken'] == 1) {
                            $broken = 1;
                        }
                    }

                    $en_sign = $this->engineer_model->get_engineer_sign("id, signature", array("service_center_id" => $data['booking_history'][0]['assigned_vendor_id'], "booking_id" => $booking_id));
                    if (!empty($en_sign)) {
                        $data['en_signature_picture'] = $en_sign[0]['signature'];
                    }

                    $en_consumption = $this->service_centers_model->get_engineer_consumed_details("engineer_consumed_spare_details.*", array("booking_id" => $booking_id));
                    if (!empty($en_consumption)) {
                        $c = 0;
                        foreach ($en_consumption as $consumptions) {
                            $data['en_consumpton_details'][$consumptions['spare_id']]['spare_id'] = $consumptions['spare_id'];
                            $data['en_consumpton_details'][$consumptions['spare_id']]['consumption_status_id'] = $consumptions['consumed_part_status_id'];
                            if ($consumptions['consumed_part_status_id'] == CONSUMED_WRONG_PART_STATUS_ID) {
                                $wrong_part_data = array();
                                $wrong_part_data['spare_id'] = $consumptions['spare_id'];
                                $wrong_part_data['part_name'] = $consumptions['part_name'];
                                $wrong_part_data['part_name'] = $consumptions['part_name'];
                                $wrong_part_data['inventory_id'] = $consumptions['inventory_id'];
                                $wrong_part_data['remarks'] = $consumptions['remarks'];
                                $data['en_consumpton_details'][$consumptions['spare_id']]['wrong_part_data'] = json_encode($wrong_part_data);
                            }
                            $c++;
                        }
                    }
                }
                $pid = $this->miscelleneous->search_for_pice_tag_key($u['price_tags'], $prices);
                // remove array key, if price tag exist into price array
                unset($prices[$pid]);
            }
            array_push($data['prices'], $prices);
            $bookng_unit_details[$key1]['is_broken'] = $broken;
            $bookng_unit_details[$key1]['dop'] = $b['purchase_date'];
            $bookng_unit_details[$key1]['sf_dop'] = $b['sf_purchase_date'];
            $bookng_unit_details[$key1]['sf_model_number'] = $b['sf_model_number'];
        }
        
        $data['upcountry_charges'] = 0;
        if ($this->session->userdata('is_engineer_app') == 1) {
            $sig_table = $this->engineer_model->getengineer_sign_table_data("*", array("booking_id" => $booking_id,
                "service_center_id" => $data['booking_history'][0]['assigned_vendor_id']));
            if (!empty($sig_table)) {
                $data['signature'] = $sig_table[0]['signature'];
                $data['amount_paid'] = $sig_table[0]['amount_paid'];
                $data['mismatch_pincode'] = $sig_table[0]['mismatch_pincode'];
                $data['upcountry_charges'] = $sig_table[0]['upcountry_charges'];
            }
        }

        $isPaytmTxn = $this->paytm_payment_lib->get_paytm_transaction_data($booking_id);
        if (!empty($isPaytmTxn)) {
            if ($isPaytmTxn['status']) {
                $data['booking_history'][0]['onlinePaymentAmount'] = $isPaytmTxn['total_amount'];
            }
        }

        $data['bookng_unit_details'] = $bookng_unit_details;

        $data['technical_problem'] = $data['technical_defect'] = array();

        if (!empty($price_tags)) {
            $data['technical_problem'] = $this->booking_request_model->get_booking_request_symptom('symptom.id, symptom', array('symptom.service_id' => $data['booking_history'][0]['service_id'], 'symptom.active' => 1, 'symptom.partner_id' => $data['booking_history'][0]['partner_id']), array('request_type.service_category' => $price_tags));
        }
        if (count($data['technical_problem']) <= 0) {
            $data['technical_problem'][0] = array('id' => 0, 'symptom' => 'Default');
        }

        if (count($data['booking_symptom']) > 0 && !is_null($data['booking_symptom'][0]['symptom_id_booking_creation_time'])) {
            $data['technical_defect'] = $this->booking_request_model->get_defect_of_symptom('defect_id,defect', array('symptom_id' => $data['booking_symptom'][0]['symptom_id_booking_creation_time'], 'partner_id' => $data['booking_history'][0]['partner_id']));
        }
        if (count($data['technical_defect']) <= 0) {
            $data['technical_defect'][0] = array('defect_id' => 0, 'defect' => 'Default');
        }
        $data['spare_parts_details'] = $this->partner_model->get_spare_parts_by_any('spare_parts_details.*, inventory_master_list.part_number', ['booking_id' => $booking_id, 'spare_parts_details.status != "' . _247AROUND_CANCELLED . '"' => NULL, 'parts_shipped is not null' => NULL, '(spare_parts_details.consumed_part_status_id is null or spare_parts_details.consumed_part_status_id = '.OK_PART_BUT_NOT_USED_CONSUMPTION_STATUS_ID.')' => NULL, 'defective_part_shipped is null' => NULL], FALSE, FALSE, FALSE, ['is_inventory' => true]);
        $data['spare_consumed_status'] = $this->reusable_model->get_search_result_data('spare_consumption_status', 'id, consumed_status,status_description,tag', ['active' => 1, "tag <> '".PART_NOT_RECEIVED_TAG."'" => NULL], NULL, NULL, ['consumed_status' => SORT_ASC], NULL, NULL);
        $this->load->view('service_centers/header');
        $this->load->view('service_centers/complete_booking_form', $data);
    }

    /**
     * @desc: This is used to complete the booking once all the required details are filled.
     * @param: booking id
     * @return: void
     */
    function process_complete_booking($booking_id) {
        log_message('info', __FUNCTION__ . ' booking_id: ' . $booking_id . " Json data " . json_encode($this->input->post(), true));
        $this->checkUserSession();
        $this->form_validation->set_rules('customer_basic_charge', 'Basic Charge', 'required');
        $this->form_validation->set_rules('additional_charge', 'Additional Service Charge', 'required');
        $this->form_validation->set_rules('parts_cost', 'Parts Cost', 'required');
        $this->form_validation->set_rules('booking_status', 'Status', 'required');
        $this->form_validation->set_rules('pod', 'POD ', 'callback_validate_serial_no');

        if (($this->form_validation->run() == FALSE) || ($booking_id == "") || (is_null($booking_id))) {
            $this->complete_booking_form(urlencode(base64_encode($booking_id)));
        } else {
            // fetch record from booking details of $booking_id.
            $booking_details = $this->booking_model->get_booking_details('*',['booking_id' => $booking_id])[0];
            $booking_state_change = $this->booking_model->get_booking_state_change($booking_id);
            $old_state = $booking_state_change[count($booking_state_change) - 1]['new_state'];
            $curr_status = $booking_details['current_status'];
            // if current status of the booking is Completed or Cancelled then the booking cannot be completed again.               
            if (!in_array($old_state, array(SF_BOOKING_COMPLETE_STATUS, _247AROUND_COMPLETED, _247AROUND_CANCELLED))) {

                $is_model_drop_down = $this->input->post('is_model_dropdown');
                $model_change = true;
                if ($is_model_drop_down == 1) {
                    $model_change = $this->appliance_modify_by_model_number($booking_id);
                }
                if ($model_change) {
                    // customer paid basic charge is comming in array
                    // Array ( [100] =>  500 , [102] =>  300 )  
                    $customer_basic_charge = $this->input->post('customer_basic_charge');
                    // Additional service charge is comming in array
                    $additional_charge = $this->input->post('additional_charge');
                    // Parts cost is comming in array
                    $parts_cost = $this->input->post('parts_cost');
                    $booking_status = $this->input->post('booking_status');
                    $total_amount_paid = $this->input->post('grand_total_price');
                    $closing_remarks = $this->input->post('closing_remarks');
                    $serial_number = $this->input->post('serial_number');
                    $spare_parts_required = $this->input->post('spare_parts_required');
                    $upcountry_charges = $this->input->post("upcountry_charges");
                    $serial_number_pic = $this->input->post("serial_number_pic");
                    $broken = $this->input->post("appliance_broken");
                    $mismatch_pincode = $this->input->post("mismatch_pincode");
                    //is_sn_correct
                    $is_sn_correct = $this->input->post('is_sn_correct');

                    $model_number = $this->input->post('model_number');

                    $technical_symptom = $this->input->post('closing_symptom');
                    $technical_defect = $this->input->post('closing_defect');
                    $technical_solution = $this->input->post('technical_solution');
                    $purchase_date = $this->input->post('appliance_dop');
                    $purchase_invoice = $this->input->post('appliance_purchase_invoice');
                    $booking_symptom['solution_id'] = $technical_solution;
                    $booking_symptom['symptom_id_booking_completion_time'] = $technical_symptom;
                    $booking_symptom['defect_id_completion'] = $technical_defect;

                    //$internal_status = "Cancelled";
                    $getremarks = $this->booking_model->getbooking_charges($booking_id);
                    //$approval = $this->input->post("approval");
                    $i = 0;

                    $purchase_invoice_file_name = '';
                    if (!empty($_FILES['sf_purchase_invoice']['name'])) {
                        $purchase_invoice_file_name = $this->upload_sf_purchase_invoice_file($booking_id, $_FILES['sf_purchase_invoice']['tmp_name'], ' ', $_FILES['sf_purchase_invoice']['name']);
                    }
                    foreach ($customer_basic_charge as $unit_id => $value) {

                        //Check unit id exist in the sc action table.
                        if (isset($booking_status[$unit_id])) {
                            if ($booking_status[$unit_id] == _247AROUND_CANCELLED || $booking_status[$unit_id] == _247AROUND_COMPLETED) {

                                $unit_temp_id = $this->check_unit_exist_action_table($booking_id, $unit_id);

                                // variable $unit_id  is existing id in booking unit details table of given booking id 
                                $data = array();
                                $ud_data = array();
                                if ($unit_temp_id != $unit_id) {
                                    $data['added_by_sf'] = 1;
                                }
                                $data['unit_details_id'] = $unit_temp_id;
                                $data['closed_date'] = date('Y-m-d H:i:s');
                                $data['is_broken'] = $broken[$unit_id];
                                $data['mismatch_pincode'] = $mismatch_pincode;

//                 if(!empty($approval)){
//                    $unitWhere = array("engineer_booking_action.booking_id" => $booking_id, "engineer_booking_action.unit_details_id" => $unit_id);
//                    $en = $this->engineer_model->get_engineer_action_table_list($unitWhere, "engineer_booking_action.*");
//                   
//                    $data['is_broken'] = $en[0]->is_broken;
//                    //$data['closed_date'] = $en[0]->closed_date;
//                    
//                 }
                                if (isset($model_number[$unit_id])) {
                                    $data['model_number'] = $model_number[$unit_id];
                                }
                                $data['service_charge'] = $value;
                                $data['additional_service_charge'] = $additional_charge[$unit_id];
                                $data['parts_cost'] = $parts_cost[$unit_id];
                                $data['internal_status'] = $booking_status[$unit_id];
                                if ($this->session->userdata('is_engineer_app') == 1) {
                                    $unitWhere1 = array("engineer_booking_action.booking_id" => $booking_id, "engineer_booking_action.unit_details_id" => $unit_id);
                                    $this->engineer_model->update_engineer_table(array("current_status" => $booking_status[$unit_id], "internal_status" => $booking_status[$unit_id]), $unitWhere1);
                                }
                                $data['current_status'] = "InProcess";
                                $data['booking_id'] = $booking_id;
                                $data['amount_paid'] = $total_amount_paid;
                                $data['update_date'] = date("Y-m-d H:i:s");
                                if ($i == 0) {
                                    $data['upcountry_charges'] = $upcountry_charges;
                                }
                                if (isset($serial_number[$unit_id])) {
                                    $trimSno = str_replace(' ', '', trim($serial_number[$unit_id]));
                                    $data['serial_number'] = $trimSno;
                                    $data['serial_number_pic'] = trim($serial_number_pic[$unit_id]);
                                    $data['is_sn_correct'] = $is_sn_correct[$unit_id];
                                    $ud_data['serial_number'] = $trimSno;
                                    $ud_data['serial_number_pic'] = trim($serial_number_pic[$unit_id]);
                                }
                                if (!empty($getremarks[0]['service_center_remarks'])) {

                                    $data['service_center_remarks'] = date("F j") . ":- " . $closing_remarks . " " . $getremarks[0]['service_center_remarks'];
                                } else {
                                    if (!empty($closing_remarks)) {
                                        $data['service_center_remarks'] = date("F j") . ":- " . $closing_remarks;
                                    }
                                }
                                $data['sf_purchase_date'] = (!empty($purchase_date[$unit_id]) ? date("Y-m-d", strtotime($purchase_date[$unit_id])) : NULL);
                                $data['sf_purchase_invoice'] = NULL;
                                if (!empty($purchase_invoice[$unit_id]) || !empty($purchase_invoice_file_name)) {
                                    if (empty($purchase_invoice_file_name)) {
                                        $purchase_invoice_file_name = $purchase_invoice[$unit_id];
                                    }
                                    $data['sf_purchase_invoice'] = $purchase_invoice_file_name;
                                }

                                $i++;
                                $this->vendor_model->update_service_center_action($booking_id, $data);
                                if(!empty($ud_data))
                                {
                                    $this->booking_model->update_booking_unit_details($booking_id, $ud_data);
                                }
                        }
                    }
                    }

                    if ($booking_symptom['symptom_id_booking_completion_time'] || $booking_symptom['defect_id_completion'] || $booking_symptom['solution_id']) {
                        $rowsStatus = $this->booking_model->update_symptom_defect_details($booking_id, $booking_symptom);
                        if (!$rowsStatus) {
                            $booking_symptom['booking_id'] = $booking_id;
                            $booking_symptom['symptom_id_booking_creation_time'] = 0;
                            $booking_symptom['create_date'] = date("Y-m-d H:i:s");
                            $this->booking_model->addBookingSymptom($booking_symptom);
                        }
                    }

                    //Send Push Notification to account group
                    $clouserAccountArray = array();
                    $getClouserAccountHolderID = $this->reusable_model->get_search_result_data("employee", "id", array("groups" => "accountmanager"), NULL, NULL, NULL, NULL, NULL, array());
                    foreach ($getClouserAccountHolderID as $employeeID) {
                        $clouserAccountArray['employee'][] = $employeeID['id'];
                    }
                    $textArray['msg'] = array($booking_id, $this->session->userdata('service_center_name'));
                    $textArray['title'] = array($this->session->userdata('service_center_name'), $booking_id);
                    $this->push_notification_lib->create_and_send_push_notiifcation(CUSTOMER_UPDATE_BOOKING_PUSH_NOTIFICATION_EMPLOYEE_TAG, $clouserAccountArray, $textArray);
                    //End Push Notification
                    $partner_id = $this->input->post("partner_id");
                    $this->miscelleneous->pull_service_centre_close_date($booking_id, $partner_id);

                    //End Update Service Center Closed Date
                    // Insert data into booking state change
                    $this->insert_details_in_state_change($booking_id, SF_BOOKING_COMPLETE_STATUS, $closing_remarks, "247Around", "Review the Booking");


                    // update spare parts.
                    $is_update_spare_parts = $this->miscelleneous->update_spare_consumption_status($this->input->post(), $booking_id);
                    //This is used to cancel those spare parts who has not shipped by partner.
                    $this->cancel_spare_parts($partner_id, $booking_id);
                    /**
                     * Mark booking InProcess_Completed as booking and spare separated .
                     * @modifiedBy Ankit Rajvanshi
                     */
                    $this->update_booking_internal_status($booking_id, SF_BOOKING_COMPLETE_STATUS, $partner_id);
                    $this->session->set_userdata('success', "Updated Successfully!!");

                    if ($is_update_spare_parts) {
                        redirect(base_url() . "service_center/get_defective_parts_booking");
                    } else {
                        redirect(base_url() . "service_center/pending_booking");
                    }
                } else {
                    $this->session->set_userdata('error', "Price Not Found Against Updated Information For Booking  : $booking_id. Please Contact to back Office Team");
                    redirect(base_url() . "service_center/pending_booking");
                }
            } else {
                $this->session->set_userdata('error', "You already marked this booking : $booking_id as ".$old_state);
                redirect(base_url() . "service_center/pending_booking");
            }
        }
    }

    /**
     *  @desc : This function is used to upload the purchase invoice to s3 and save into database
     *  @param : string $booking_primary_contact_no
     *  @return : boolean/string
     */
    function upload_sf_purchase_invoice_file($booking_id, $tmp_name, $error, $name) {

        $support_file_name = false;

        if (($error != 4) && !empty($tmp_name)) {

            $tmpFile = $tmp_name;
            $support_file_name = $booking_id . '_sf_purchase_invoice_' . substr(md5(uniqid(rand(0, 9))), 0, 15) . "." . explode(".", $name)[1];
            //move_uploaded_file($tmpFile, TMP_FOLDER . $support_file_name);
            //Upload files to AWS
            $bucket = BITBUCKET_DIRECTORY;
            $directory_xls = "purchase-invoices/" . $support_file_name;
            $upload_file_status = $this->s3->putObjectFile($tmpFile, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);

            if ($upload_file_status) {
                //Logging success for file uppload
                log_message('info', __METHOD__ . 'Sf purchase invoice has been uploaded sucessfully for booking_id: ' . $booking_id);
                return $support_file_name;
            } else {
                //Logging success for file uppload
                log_message('info', __METHOD__ . 'Error In uploading support file for booking_id: ' . $booking_id);
                return False;
            }
        }
    }

    /**
     * @desc This function is used to change appliance category, capacity and also change prices according to it
     * @return boolean
     */
    function appliance_modify_by_model_number($booking_id) {
        $model_number = $this->input->post('model_number');
        $partner_id = $this->input->post('partner_id');
        $service_id = $this->input->post('appliance_id');
        $brand = $this->input->post('brand');
        $array = array();
        if (!empty($model_number)) {
            foreach ($model_number as $unit_id => $value) {
                if (strpos($unit_id, 'new') === false && !empty($value)) {
                    $return = true;
                    $unit = $this->booking_model->get_unit_details(array('id' => $unit_id), false, 'appliance_capacity, vendor_basic_percentage, customer_total, partner_paid_basic_charges,'
                            . ' appliance_brand, price_tags, around_net_payable, appliance_category, customer_net_payable, partner_net_payable');
                    $model_details = $this->partner_model->get_model_number('category, capacity', array('appliance_model_details.model_number' => $value,
                        'appliance_model_details.entity_id' => $partner_id, 'appliance_model_details.active' => 1, 'partner_appliance_details.active' => 1, 'partner_appliance_details.brand' => $brand));
                    $sc_change = false;
                    if(!empty($unit[0]['appliance_category']) && !empty($model_details[0]['category']) && !empty($unit[0]['appliance_capacity'])&& !empty($model_details[0]['capacity']))
                    {
                     if($unit[0]['appliance_category'] == $model_details[0]['category'] && $unit[0]['appliance_capacity'] == $model_details[0]['capacity']){
                        $sc_change = false;
                      } else {
                        $sc_change = true;
                      }
                    }         
                    if ($sc_change) {
                        $partner_data = $this->partner_model->get_partner_code($partner_id);
                        $partner_type = $partner_data[0]['partner_type'];

                        if ($partner_type == OEM) {
                            $result1 = $this->partner_model->getPrices($service_id, $model_details[0]['category'], $model_details[0]['capacity'], $partner_id, $unit[0]['price_tags'], $unit[0]['appliance_brand'], TRUE);
                        } else {
                            $result1 = $this->partner_model->getPrices($service_id, $model_details[0]['category'], $model_details[0]['capacity'], $partner_id, $unit[0]['price_tags'], "", TRUE);
                        }

                        if (!empty($result1)) {
                            $result1[0]['appliance_brand'] = $unit[0]['appliance_brand'];
                            // Free from Paid
                            $array[$unit_id] = $result1[0];
                        } else {
                            $this->send_mail_for_insert_applaince_by_sf($unit[0]['appliance_category'], $unit[0]['appliance_capacity'], $unit[0]['appliance_brand'], $unit[0]['price_tags'], $booking_id);
                            return FALSE;
                        }
                    }
                }
            }

            if (!empty($array)) {
                foreach ($array as $k => $v) {
                    $data = $this->booking_model->getpricesdetails_with_tax($v['id'], "");
                    if (!empty($data)) {
                        $result = $data[0];

                        if (!empty($data[0]['price_tags']) && ($data[0]['price_tags'] == REPAIR_OOW_PARTS_PRICE_TAGS)) {
                            if (!empty($v['service_category']) && ($v['service_category'] == REPAIR_OOW_PARTS_PRICE_TAGS)) {
                                $result['customer_total'] = $unit[0]['customer_total'];
                                $result['vendor_basic_percentage'] = $unit[0]['vendor_basic_percentage'];
                            }
                        }
                        unset($result['id']);
                        $result['appliance_category'] = $model_details[0]['category'];
                        $result['appliance_capacity'] = $model_details[0]['capacity'];
                        $result['partner_paid_basic_charges'] = $result['partner_net_payable'];
                        $result['around_paid_basic_charges'] = $unit[0]['around_net_payable'];

                        $result['customer_net_payable'] = $result['customer_total'] - $result['partner_paid_basic_charges'] - $result['around_paid_basic_charges'];
                        $result['partner_paid_tax'] = ($result['partner_paid_basic_charges'] * $result['tax_rate']) / 100;


                        $vendor_total_basic_charges = ($result['customer_net_payable'] + $result['partner_paid_basic_charges'] + $result['around_paid_basic_charges'] ) * ($result['vendor_basic_percentage'] / 100);
                        $result['partner_paid_basic_charges'] = $result['partner_paid_basic_charges'] + $result['partner_paid_tax'];
                        $around_total_basic_charges = ($result['customer_net_payable'] + $result['partner_paid_basic_charges'] + $result['around_paid_basic_charges'] - $vendor_total_basic_charges);

                        $result['around_st_or_vat_basic_charges'] = $this->booking_model->get_calculated_tax_charge($around_total_basic_charges, $result['tax_rate']);
                        $result['vendor_st_or_vat_basic_charges'] = $this->booking_model->get_calculated_tax_charge($vendor_total_basic_charges, $result['tax_rate']);

                        $result['around_comm_basic_charges'] = $around_total_basic_charges - $result['around_st_or_vat_basic_charges'];
                        $result['vendor_basic_charges'] = $vendor_total_basic_charges - $result['vendor_st_or_vat_basic_charges'];

                        $a = $this->booking_model->update_booking_unit_details_by_any(array('id' => $k), $result);

                        $array1 = array('booking_id' => $booking_id,
                            'category' => $model_details[0]['category'],
                            'capacity' => $model_details[0]['capacity'],
                            'unit_details_id' => $k);

                        $this->service_centers_model->insert_update_applaince_by_sf($array1);
                    } else {
                        $this->send_mail_for_insert_applaince_by_sf($model_details[0]['category'], $model_details[0]['capacity'], $v['appliance_brand'], $v['price_tags'], $booking_id);
                        return FALSE;
                    }
                }

                return true;
            } else {

                return TRUE;
            }
        } else {
            return true;
        }
    }

    /**
     * @desc This function is used to send email for insert appliance by sf

     */
    function send_mail_for_insert_applaince_by_sf($category, $capacity = "", $brand = "", $service_category = "", $booking_id) {
        $email_template = $this->booking_model->get_booking_email_template(UPDATE_APPLIANCE_BY_SF);
        if (!empty($email_template)) {

            $to = $email_template[1];
            $cc = $email_template[3];
            $bcc = $email_template[4];
            $subject = vsprintf($email_template[4], array());
            $emailBody = vsprintf($email_template[0], array($brand, $category, $capacity, $service_category));

            $this->notify->sendEmail($email_template[2], $to, $cc, $bcc, $subject, $emailBody, "", UPDATE_APPLIANCE_BY_SF, "", $booking_id);
        }
    }

    /**
     * @desc This is used to cancel spare who has not shipped by partner. Also inform to partner.
     * @param String $partner_id
     * @param String $booking_id
     */
    function cancel_spare_parts($partner_id, $booking_id) {
        log_message("info", __METHOD__ . " For booking id " . $booking_id);
        $can_sp_required_id = json_decode($this->input->post("can_sp_required_id"), true);
        if (!empty($can_sp_required_id)) {
            $part_name = array();
            foreach ($can_sp_required_id as $sp) {
                $this->service_centers_model->update_spare_parts(array('id' => $sp['part_id']), array('status' => _247AROUND_CANCELLED, "old_status" => SPARE_PARTS_REQUESTED));
                array_push($part_name, $sp['part_name']);
                
            }
            $join['service_centres'] = 'booking_details.assigned_vendor_id = service_centres.id';
            $JoinTypeTableArray['service_centres'] = 'left';
            $booking_state = $this->reusable_model->get_search_query('booking_details', 'service_centres.state', array('booking_details.booking_id' => $booking_id), $join, NULL, NULL, NULL, $JoinTypeTableArray)->result_array();

            //$get_partner_details = $this->partner_model->getpartner_details('account_manager_id, primary_contact_email, owner_email', array('partners.id' => $partner_id));
            $get_partner_details = $this->partner_model->getpartner_data("group_concat(distinct agent_filters.agent_id) as account_manager_id,primary_contact_email,owner_email", array('partners.id' => $partner_id, 'agent_filters.state' => $booking_state[0]['state']), "", 0, 0, 1, "partners.id");

            $am_email = "";
            if (!empty($get_partner_details[0]['account_manager_id'])) {

                //$am_email = $this->employee_model->getemployeefromid($get_partner_details[0]['account_manager_id'])[0]['official_email'];
                $am_email = $this->employee_model->getemployeeMailFromID($get_partner_details[0]['account_manager_id'])[0]['official_email'];
            }
//        $sid = $this->session->userdata('service_center_id');
//        $rm = $this->vendor_model->get_rm_sf_relation_by_sf_id($sid);
//        $rm_email = "";
//        if (!empty($rm)) {
//            $rm_email = ", " . $rm[0]['official_email'];
//        }
            $part = implode(",", $part_name);
            $email_template = $this->booking_model->get_booking_email_template("partner_spare_cancelled");
            //$to = $get_partner_details[0]['primary_contact_email'] . "," . $get_partner_details[0]['owner_email'];
            $to = $get_partner_details[0]['primary_contact_email'];
            $cc = "";
            $subject = vsprintf($email_template[4], array($part, $booking_id));
            $message = vsprintf($email_template[0], array($part, $booking_id));
            if (!empty($am_email)) {
                $from = $am_email;
            } else {
                $from = $email_template[2];
            }

            $this->notify->sendEmail($from, $to, $cc, "", $subject, $message, "", 'partner_spare_cancelled', "", $booking_id);
        } else {
            log_message('info', __METHOD__ . " No Data found for Cancel Spare parts");
        }
    }

    /**
     * @desc: Validate Serial Number. If pod is 1 then serial number should not empty
     * @return boolean
     */
    function validate_serial_no() {
        //log_message('info', __METHOD__. " ". json_encode($this->input->post()));
        $serial_number = $this->input->post('serial_number');
        $upload_serial_number_pic = array();
        if (isset($_FILES['upload_serial_number_pic'])) {
            $upload_serial_number_pic = $_FILES['upload_serial_number_pic'];
        }

        $pod = $this->input->post('pod');
        $booking_status = $this->input->post('booking_status');
        $partner_id = $this->input->post('partner_id');
        $user_id = $this->input->post('user_id');
        $booking_id = $this->input->post('booking_id');
        $appliance_id = $this->input->post('appliance_id');
        $price_tags_array = $this->input->post('price_tags');
        $return_status = true;
        if (isset($_POST['pod'])) {
            foreach ($pod as $unit_id => $value) {
                if (isset($booking_status[$unit_id])) {
                    if ($booking_status[$unit_id] == _247AROUND_COMPLETED) {
                        $trimSno = str_replace(' ', '', trim($serial_number[$unit_id]));
                        $price_tag = $price_tags_array[$unit_id];

                        switch ($value) {
                            case '0':
                                // upload serial number image in case of POD = 0
                                if (isset($upload_serial_number_pic['name'][$unit_id])) {
                                    $s = $this->upload_insert_upload_serial_no($upload_serial_number_pic, $unit_id, $partner_id, $trimSno);
                                    if (empty($s)) {
                                        $this->form_validation->set_message('validate_serial_no', 'Serial Number, File size or file type is not supported. Allowed extentions are png, jpg, jpeg and pdf. '
                                                . 'Maximum file size is 5 MB.');
                                        $return_status = false;
                                    }
                                }
                                break;
                            case '1':
                                if ($partner_id == AKAI_ID) {
                                    log_message('info', " Akai partner");
                                    if (empty($trimSno) || !ctype_alnum($trimSno)) {
                                        log_message('info', " Serial No with special character " . $trimSno);
                                        $this->form_validation->set_message('validate_serial_no', 'Please Enter Serial Number Without any Special Character');
                                        $return_status = false;
                                        break;
                                    }
                                }
                                if (isset($upload_serial_number_pic['name'][$unit_id])) {
                                    $s = $this->upload_insert_upload_serial_no($upload_serial_number_pic, $unit_id, $partner_id, $trimSno);
                                    if (empty($s)) {
                                        $this->form_validation->set_message('validate_serial_no', 'Serial Number, File size or file type is not supported. Allowed extentions are png, jpg, jpeg and pdf. '
                                                . 'Maximum file size is 5 MB.');
                                        $return_status = false;
                                    }
                                } else {
                                    $return_status = false;
                                    $s = $this->form_validation->set_message('validate_serial_no', "Please upload serial number image");
                                }
                                $status = $this->validate_serial_no->validateSerialNo($partner_id, $trimSno, $price_tag, $user_id, $booking_id, $appliance_id);
                                if (!empty($status)) {
                                    if ($status['code'] == SUCCESS_CODE) {
                                        log_message('info', " Serial No validation success  for serial no " . trim($serial_number[$unit_id]));
                                    } else if ($status['code'] == DUPLICATE_SERIAL_NO_CODE) {
                                        $return_status = false;
                                        $this->form_validation->set_message('validate_serial_no', $status['message']);
                                    } else {
                                        log_message('info', " Serial No validation failed  for serial no " . trim($serial_number[$unit_id]));
                                    }
                                } else if ($value == 1 && empty($trimSno)) {
                                    $return_status = false;
                                    $this->form_validation->set_message('validate_serial_no', 'Please Enter Valid Serial Number');
                                } else if ($value == 1 && is_numeric($serial_number[$unit_id]) && $serial_number[$unit_id] == 0) {
                                    $return_status = false;
                                    $this->form_validation->set_message('validate_serial_no', 'Please Enter Valid Serial Number');
                                }
                                break;
                        }
                    }
                }
            }
            if ($return_status == true) {
                return true;
            } else {

                return FALSE;
            }
        } else {
            return TRUE;
        }
    }

    /**
     * @desc This is used to validate serial no image and insert serial no into DB
     * @param Array $upload_serial_number_pic
     * @param Int $unit
     * @param Strng $partner_id
     * @param String $serial_number
     * @return boolean
     */
    function upload_insert_upload_serial_no($upload_serial_number_pic, $unit, $partner_id, $serial_number) {
        log_message('info', __METHOD__ . " Enterring ...");
        if (!empty($upload_serial_number_pic['tmp_name'][$unit])) {

            $pic_name = $this->upload_serial_no_image_to_s3($upload_serial_number_pic, "serial_number_pic_" . $this->input->post('booking_id') . "_", $unit, SERIAL_NUMBER_PIC_DIR, "serial_number_pic");
            if ($pic_name) {

                return true;
            } else {

                return false;
            }
        } else {

            return TRUE;
        }
    }

    /**
     * @desc This is used to upload serial no image to S3
     * @param Array $file
     * @param String $type
     * @param Int $unit
     * @param String $s3_directory
     * @param String $post_name
     * @return boolean|string
     */
    public function upload_serial_no_image_to_s3($file, $type, $unit, $s3_directory, $post_name) {
        log_message('info', __FUNCTION__ . " Enterring ");
        $allowedExts = array("png", "jpg", "jpeg", "JPG", "JPEG", "PNG", "PDF", "pdf");
        $MB = 1048576;
        $temp = explode(".", $file['name'][$unit]);
        $extension = end($temp);
        //$filename = prev($temp);

        if ($file["name"][$unit] != null) {
            if (($file["size"][$unit] < 2 * $MB) && in_array($extension, $allowedExts)) {
                if ($file["error"][$unit] > 0) {

                    return false;
                } else {

                    $picName = $type . rand(10, 100) . $unit . "." . $extension;
                    $_POST[$post_name][$unit] = $picName;
                    $bucket = BITBUCKET_DIRECTORY;
                    $directory = $s3_directory . "/" . $picName;
                    $this->s3->putObjectFile($file["tmp_name"][$unit], $bucket, $directory, S3::ACL_PUBLIC_READ);

                    return $picName;
                }
            } else {

                return FALSE;
            }
        } else {

            return FALSE;
        }
        log_message('info', __FUNCTION__ . " Exit ");
    }

    /**
     * @desc this function is used to validate serial no from ajax.
     */
    function validate_booking_serial_number() {

        log_message('info', __METHOD__ . " Enterring .. POST DATA " . json_encode($this->input->post(), true) . " SF ID " . $this->session->userdata('service_center_id'));
        $serial_number = $this->input->post('serial_number');
        $partner_id = $this->input->post('partner_id');
        $user_id = $this->input->post('user_id');
        $price_tags = $this->input->post("price_tags");
        $booking_id = $this->input->post("booking_id");
        $appliance_id = $this->input->post("appliance_id");
        $model_number = $this->input->post("model_number");
        if(!empty($this->input->post('booking_request_types'))){
            $price_tags = $this->booking_utilities->get_booking_request_type($this->input->post('booking_request_types')); 
        }
        $validate_serial_number_special_char = false;
        if (!ctype_alnum($serial_number) && !empty($validate_serial_number_special_char)) {
            $status = array('code' => '247', "message" => "Serial Number Entered With Special Character " . $serial_number . " . This is not allowed.");
            log_message('info', "Serial Number Entered With Special Character " . $serial_number . " . This is not allowed.");
            echo json_encode($status, true);
        } else {
            $status = $this->validate_serial_no->validateSerialNo($partner_id, trim($serial_number), trim($price_tags), $user_id, $booking_id, $appliance_id, $model_number);
            if (!empty($status)) {
                $status['notdefine'] = 0;
                log_message('info', __METHOD__ . 'Status ' . print_r($status, true));
                echo json_encode($status, true);
            } else {
                log_message('info', __METHOD__ . 'Partner serial no validation is not define');
                echo json_encode(array('code' => SUCCESS_CODE, 'notdefine' => 1), true);
            }
        }
    }

    /**
     * @desc: This is used to get cancel booking form.
     * @param: booking id
     * @return: void
     */
    function cancel_booking_form($code) {
        log_message('info', __FUNCTION__ . " Booking ID: " . base64_decode(urldecode($code)));
        $this->checkUserSession();
        $booking_id = base64_decode(urldecode($code));
        $data['user_and_booking_details'] = $this->booking_model->getbooking_history($booking_id);
        $data['brand'] = $this->reusable_model->get_search_result_data('booking_unit_details', 'DISTINCT appliance_brand', ['booking_id' => $booking_id], NULL, NULL, NULL, NULL, NULL);

        $where = array('reason_of' => 'vendor');
        $data['reason'] = $this->booking_model->cancelreason($where);
        $data['bookinghistory'] = $this->booking_model->getbooking_history($booking_id);
        if ($this->session->userdata('is_engineer_app') == 1) {
            $en_where = array("booking_id" => $booking_id,
                "service_center_id" => $this->session->userdata('service_center_id')
            );
            $data['engineer_data'] = $this->engineer_model->getengineer_action_data("cancellation_reason, cancellation_remark, closed_date, current_status, internal_status", $en_where);
        }

        $this->load->view('service_centers/header');
        $this->load->view('service_centers/cancel_booking_form', $data);
    }

    /**
     * @desc: This is used to cancel booking for service center.
     * @param: booking id
     * @return: void
     */
    function process_cancel_booking($booking_id) {
        log_message('info', __FUNCTION__ . " Booking ID: " . $booking_id);
        $this->checkUserSession();
        $this->form_validation->set_rules('cancellation_reason', 'Cancellation Reason', 'required');
        // if current status of the booking is Completed or Cancelled then the booking cannot be cancelled again.
        $booking_details = $this->booking_model->get_booking_details('*',['booking_id' => $booking_id])[0]['current_status'];
            if ($booking_details == _247AROUND_COMPLETED || $booking_details == _247AROUND_CANCELLED) {
             $this->session->set_userdata('error', "Booking is already $booking_details. You cannot cancel the booking.");
            redirect(base_url() . "service_center/pending_booking");
        }
        if (($this->form_validation->run() == FALSE) || $booking_id == "" || $booking_id == NULL) {
            log_message('info', __FUNCTION__ . " Form validation failed Booking ID: " . $booking_id);
            $this->cancel_booking_form(urlencode(base64_encode($booking_id)));
        } else {

            $cancellation_reason = trim($this->input->post('cancellation_reason'));
            $cancellation_text = $this->input->post('cancellation_reason_text');
            // Get cancellation reason Text from Id
            $cancellation_reason_text = "";
            if(!empty($cancellation_reason)){
                $arr_cancellation_reason =  $this->reusable_model->get_search_result_data("booking_cancellation_reasons", "*", array('id' => $cancellation_reason), NULL, NULL, NULL, NULL, NULL, array());
                $cancellation_reason_text = !empty($arr_cancellation_reason[0]['reason']) ? $arr_cancellation_reason[0]['reason'] : ""; 
            }
            $correctpin = $this->input->post('correct_pincode');
            $can_state_change = $cancellation_reason_text;
            $partner_id = $this->input->post('partner_id');
            $city = $this->input->post('city');
            $booking_pincode = $this->input->post('booking_pincode');
            $brand = $this->input->post('brand');

            if (!empty($cancellation_text)) {
                $can_state_change = $cancellation_reason_text . " - " . $cancellation_text;
            }


            switch ($cancellation_reason) {
                case PRODUCT_NOT_DELIVERED_TO_CUSTOMER_ID :
                    //Called when sc choose Product not delivered to customer 
                    $this->convert_booking_to_query($booking_id, $partner_id);

                    break;

                default :
                    if ($cancellation_reason == CANCELLATION_REASON_WRONG_AREA_ID) {
                        $this->send_mail_rm_for_wrong_area_picked($booking_id, $partner_id, $city, $booking_pincode, WRONG_CALL_AREA_TEMPLATE);
                    }

                    if (isset($correctpin) && !empty($correctpin) && $cancellation_reason == _247AROUND_WRONG_PINCODE_CANCEL_REASON_ID) {
                        $pinupdate = array(
                            'booking_pincode' => $correctpin,
                            'edit_by_sf' => 1
                        );
                        $this->booking_model->update_booking($booking_id, $pinupdate);
                        $this->initialized_variable->fetch_partner_data($partner_id);
                        $partner_data = $this->initialized_variable->get_partner_data();
                        $booking['service_id'] = $this->input->post('service_id');
                        $response = $this->miscelleneous->check_upcountry_vendor_availability($city, $correctpin, $booking['service_id'], $partner_data, false, $brand);
                        if (!empty($response) && !isset($response['vendor_not_found'])) {
                            $url = base_url() . "employee/vendor/process_reassign_vendor_form/0";
                            $async_data['service'] = $response['vendor_id'];
                            $async_data['booking_id'] = $booking_id;
                            $async_data['remarks'] = "Booking Reassigned While Cancellation by Sf";
                            $this->asynchronous_lib->do_background_process($url, $async_data);
                        }
                        $this->send_mail_rm_for_wrong_area_picked($booking_id, $partner_id, $city, $booking_pincode, WRONG_PINCODE_TEMPLATE, $correctpin);
                        redirect(base_url() . "service_center/pending_booking");
                        break;
                    }

                    // Add Update by SF Value
                    $this->booking_model->update_booking($booking_id, ['edit_by_sf' => 1]);

                    $data['current_status'] = "InProcess";
                    $data['internal_status'] = "Cancelled";
                    $data['service_center_remarks'] = date("F j") . ":- " . $cancellation_text;
                    $data['service_charge'] = $data['additional_service_charge'] = $data['parts_cost'] = $data['amount_paid'] = 0;
                    $data['cancellation_reason'] = $cancellation_reason;
                    $data['closed_date'] = date('Y-m-d H:i:s');
                    $data['update_date'] = date('Y-m-d H:i:s');
                    $this->vendor_model->update_service_center_action($booking_id, $data);
                    $this->miscelleneous->pull_service_centre_close_date($booking_id, $partner_id);

                    $engineer_action = $this->engineer_model->getengineer_action_data("id", array("booking_id" => $booking_id));
                    if (!empty($engineer_action)) {
                        $eng_data = array(
                            "internal_status" => _247AROUND_CANCELLED,
                            "current_status" => _247AROUND_CANCELLED
                        );
                        $this->engineer_model->update_engineer_table($eng_data, array("booking_id" => $booking_id));
                    }

                    //$this->miscelleneous->process_booking_tat_on_completion($booking_id);
                    //End Update Service Center Closed Date
                    $this->update_booking_internal_status($booking_id, "InProcess_Cancelled", $partner_id);
                    $this->insert_details_in_state_change($booking_id, 'InProcess_Cancelled', $can_state_change, "not_define", "not_define");
                    redirect(base_url() . "service_center/pending_booking");
                    break;
            }
        }
    }

    /**
     * @desc This function is used to send email to RM for Booking Not available in your area
     * @param String $booking_id
     * @param int $partner_id
     */
    function send_mail_rm_for_wrong_area_picked($booking_id, $partner_id, $city = "", $pincode = "", $templet = "", $correctpin = "") {
        $email_template = $this->booking_model->get_booking_email_template($templet);
        // Initialize To array
        $to = array();
        if (!empty($email_template)) {
            // Get ASM mail 
            $asm_email = $this->get_asm_email($this->session->userdata('service_center_id'));
            
            $join['service_centres'] = 'booking_details.assigned_vendor_id = service_centres.id';
            $JoinTypeTableArray['service_centres'] = 'left';
            $booking_state = $this->reusable_model->get_search_query('booking_details', 'service_centres.state', array('booking_details.booking_id' => $booking_id), $join, NULL, NULL, NULL, $JoinTypeTableArray)->result_array();

            //$get_partner_details = $this->partner_model->getpartner_details('account_manager_id,', array('partners.id' => $partner_id));
            $get_partner_details = $this->partner_model->getpartner_data("group_concat(distinct agent_filters.agent_id) as account_manager_id", array('partners.id' => $partner_id, 'agent_filters.state' => $booking_state[0]['state']), "", 0, 1, 1, "partners.id");
            $am_email = "";
            if (!empty($get_partner_details[0]['account_manager_id'])) {
                $arr_am_data = $this->employee_model->getemployeeMailFromID($get_partner_details[0]['account_manager_id']);
                $am_email = !empty($arr_am_data[0]['official_email']) ? $arr_am_data[0]['official_email'] : "";
            }

            // push AM mail and ASM mail in To
            array_push($to, $asm_email, $am_email);
            // If ASM is not mapped to SF, send mail to RM
            if(empty($asm_email)){
                $rm_email = $this->get_rm_email($this->session->userdata('service_center_id'));
                array_push($to, $rm_email);
            }
            // Remove Blank emails
            $to = array_filter($to);
            $to = implode(',', $to);
            $cc = $email_template[3];
            $bcc = $email_template[5];
            $subject = vsprintf($email_template[4], array($booking_id));
            $emailBody = vsprintf($email_template[0], array($booking_id, $city, $pincode, $correctpin));
            $this->notify->sendEmail($email_template[2], $to, $cc, $bcc, $subject, $emailBody, "", $templet, "", $booking_id);
        }
    }

    /**
     * @desc: This is used to convert booking into Query.
     * @param String $booking_id
     */
    function convert_booking_to_query($booking_id, $partner_id) {
        log_message('info', __FUNCTION__ . " Booking ID: " . $booking_id . ' Partner_id: ' . $partner_id);
        $booking['booking_id'] = "Q-" . $booking_id;
        $booking['current_status'] = "FollowUp";
        $booking['type'] = "Query";
        $booking['internal_status'] = PRODUCT_NOT_DELIVERED_TO_CUSTOMER;
        $booking['assigned_vendor_id'] = NULL;
        $booking['assigned_engineer_id'] = NULL;
        $booking['mail_to_vendor'] = '0';
        $booking['booking_date'] = date('Y-m-d');

        //Get Partner 
        $actor = $next_action = 'not_define';
        $partner_status = $this->booking_utilities->get_partner_status_mapping_data($booking['current_status'], $booking['internal_status'], $partner_id, $booking['booking_id']);
        if (!empty($partner_status)) {
            $booking['partner_current_status'] = $partner_status[0];
            $booking['partner_internal_status'] = $partner_status[1];
            $actor = $booking['actor'] = $partner_status[2];
            $next_action = $booking['next_action'] = $partner_status[3];
        }
        //Update Booking unit details
        $this->booking_model->update_booking($booking_id, $booking);

        $unit_details['booking_id'] = "Q-" . $booking_id;
        $unit_details['booking_status'] = "FollowUp";
        //update unit details
        $this->booking_model->update_booking_unit_details($booking_id, $unit_details);
        // Delete booking from sc action table
        $this->service_centers_model->delete_booking_id($booking_id);
        //Insert Data into Booking state change
        $this->insert_details_in_state_change($booking_id, PRODUCT_NOT_DELIVERED_TO_CUSTOMER, "Convert Booking to Query", $actor, $next_action);


        $cb_url = base_url() . "employee/do_background_process/send_request_for_partner_cb/" . $booking_id;
        $pcb = array();
        $this->asynchronous_lib->do_background_process($cb_url, $pcb);

        redirect(base_url() . "service_center/pending_booking");
    }

    /**
     * @desc: This funtion will check Session
     * @param: void
     * @return: true if details matches else session is distroyed.
     */
    function checkUserSession() {
        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'service_center') && !empty($this->session->userdata('service_center_id')) && !empty($this->session->userdata('is_sf')) ) {
            if(!empty($this->session->userdata('has_authorization_certificate')) && ($this->session->userdata('has_authorization_certificate') == 1)){
                return TRUE;
            }
            
        } else {
            log_message('info', __FUNCTION__ . " Session Expire for Service Center");
            $this->session->sess_destroy();
            redirect(base_url() . "service_center/login");
        }
    }

    /**
     * @desc: This funtion will check Session
     * @param: void
     * @return: true if details matches else session is distroyed.
     */
    function check_BB_UserSession() {
        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'service_center') && !empty($this->session->userdata('service_center_id')) && !empty($this->session->userdata('is_cp'))) {
            return TRUE;
        } else {
            log_message('info', __FUNCTION__ . " Session Expire for Service Center");
            $this->session->sess_destroy();
            redirect(base_url() . "service_center/login");
        }
    }

    /**
     * @desc : This funtion for logout
     * @param: void
     * @return: void
     */
    function logout() {
        $this->checkUserSession();
        //Saving Login Details in Database
        $login_data['browser'] = $this->agent->browser();
        $login_data['ip'] = $this->session->userdata('ip_address');
        $login_data['action'] = _247AROUND_LOGOUT;
        $login_data['agent_string'] = $this->agent->agent_string();
        $login_data['entity_type'] = $this->session->userdata('userType');
        $login_data['entity_id'] = $this->session->userdata('service_center_id');
        $login_data['agent_id'] = $this->session->userdata('service_center_agent_id');

        $logout_id = $this->employee_model->add_login_logout_details($login_data);
        //Adding Log Details
        if ($logout_id) {
            log_message('info', __FUNCTION__ . ' Logging details have been captured for service center ' . $login_data['entity_id']);
        } else {
            log_message('info', __FUNCTION__ . ' Err in capturing logging details for service center ' . $login_data['entity_id']);
        }
        $sf_skip_sf_id = 'sf_skip_'.$this->session->userdata('service_center_id');
        if((isset($_COOKIE[$sf_skip_sf_id]))){
            delete_cookie($sf_skip_sf_id);
        }
        $this->cache->clean();
        $this->session->sess_destroy();
        redirect(base_url() . "service_center/login");
    }

    /**
     * @desc: this is used to display completed booking for specific service center
     */
    function completed_booking($offset = 0, $page = 0, $booking_id = "") {
        $this->checkUserSession();
        if ($page == 0) {
            $page = 50;
        }

        $service_center_id = $this->session->userdata('service_center_id');

        $offset = ($this->uri->segment(3) != '' ? $this->uri->segment(3) : 0);
        $config['base_url'] = base_url() . 'service_center/completed_booking';
        $config['total_rows'] = $this->service_centers_model->getcompleted_or_cancelled_booking("count", "", $service_center_id, "Completed", $booking_id);

        $config['per_page'] = $page;
        $config['uri_segment'] = 3;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['count'] = $config['total_rows'];
        $bookings = $this->service_centers_model->getcompleted_or_cancelled_booking($config['per_page'], $offset, $service_center_id, "Completed", $booking_id);
        if (!empty($bookings)) {
            foreach ($bookings as $key => $value) {

                $res = $this->miscelleneous->get_SF_payout($value['booking_id'], $service_center_id, $value['amount_due'], $value['flat_upcountry']);
                $bookings[$key]['sf_earned'] = $res['sf_earned'];
                $bookings[$key]['penalty'] = $res['penalty'];
            }
        }
        $data['serial_number'] = $offset;
        $data['bookings'] = $bookings;
        $data['status'] = "Completed";

        $this->load->view('service_centers/header');
        $this->load->view('service_centers/completed_booking', $data);
    }

    function get_sf_payout($booking_id, $service_center_id=0, $amount_due=0, $flat_upcountry=0) {
        $res = $this->miscelleneous->get_SF_payout($booking_id, $service_center_id, $amount_due, $flat_upcountry);
        if(isset($res['sf_earned'])) {
            echo "Total SF Payout &nbsp;&nbsp;<i class='fa fa-inr'></i> <b>" . $res['sf_earned'] . "</b>";
        }
    }

    /**
     * @desc: this is used to display completed booking for specific service center
     */
    function cancelled_booking($offset = 0, $page = 0, $booking_id = "") {
        $this->checkUserSession();
        if ($page == 0) {
            $page = 50;
        }

        $service_center_id = $this->session->userdata('service_center_id');

        $offset = ($this->uri->segment(3) != '' ? $this->uri->segment(3) : 0);
        $config['base_url'] = base_url() . 'service_center/cancelled_booking';
        $config['total_rows'] = $this->service_centers_model->getcompleted_or_cancelled_booking("count", "", $service_center_id, "Cancelled", $booking_id);

        $config['per_page'] = $page;
        $config['uri_segment'] = 3;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['count'] = $config['total_rows'];
        $data['bookings'] = $this->service_centers_model->getcompleted_or_cancelled_booking($config['per_page'], $offset, $service_center_id, "Cancelled", $booking_id);
        $data['status'] = "Cancelled";
        $data['serial_number'] = $offset;

        $this->load->view('service_centers/header');
        $this->load->view('service_centers/completed_booking', $data);
    }

    /**
     * @desc: this method save reschedule request in service center action table. 
     * @param: void
     * @return : void
     */
    function save_reschedule_request() {
        if (!$this->input->post("call_from_api")) {
            $this->checkUserSession();
            log_message('info', __FUNCTION__ . '=> Booking Id: ' . $this->input->post('booking_id'));
            $this->form_validation->set_rules('booking_id', 'Booking ID', 'trim|required');
            $this->form_validation->set_rules('booking_date', 'Booking Date', 'required');
            $this->form_validation->set_rules('reason_text', 'Reascheduled Reason', 'trim');
            $this->form_validation->set_rules('sc_remarks', 'Reascheduled Remarks', 'trim');
            $check_validation = $this->form_validation->run();
        } else {
            $check_validation = TRUE;
            $service_center_id = $this->input->post('service_center_id');
            $sc_agent_id = $this->input->post('sc_agent_id');  /// SC Agent ID 
        }
        

        if ($check_validation == FALSE) {
            log_message('info', __FUNCTION__ . '=> Rescheduled Booking Validation failed ');
            echo "Please Select Rescheduled Date";
        } else {
            log_message('info', __FUNCTION__ . '=> Reascheduled Booking: ');
            $booking_id = $this->input->post('booking_id');
            $data['booking_date'] = date('Y-m-d', strtotime($this->input->post('booking_date')));
            $data['current_status'] = "InProcess";
            $data['internal_status'] = 'Reschedule';
            $reason = $this->input->post('reason');
            $sc_remarks = $this->input->post('sc_remarks');
            if (!empty($reason)) {

                $data['reschedule_reason'] = $this->input->post('reason');
            } else {

                $data['reschedule_reason'] = $this->input->post('reason_text');
            }
            $data['reschedule_reason'] = $data['reschedule_reason'] . " - " . $sc_remarks;
            $data['update_date'] = date("Y-m-d H:i:s");
            date_default_timezone_set('Asia/Calcutta');
            $data['reschedule_request_date'] = date("Y-m-d H:i:s");
            $this->vendor_model->update_service_center_action($booking_id, $data);
            $this->send_reschedule_confirmation_sms($booking_id);
            if (!$this->input->post("call_from_api")) {
                $this->insert_details_in_state_change($booking_id, "InProcess_Rescheduled", $data['reschedule_reason'], "not_define", "not_define");
            } else {

                /* IF Agent Do not Come then to to find it */
                $sc_agent = $this->service_centers_model->get_sc_login_details_by_id($service_center_id);
                if (!empty($sc_agent)) {
                    $sc_agent_id = $sc_agent[0]['id'];
                }
                $this->notify->insert_state_change($booking_id, "InProcess_Rescheduled", "", $data['reschedule_reason'], $sc_agent_id, "Engineer", "not_define", "not_define", NULL, $service_center_id);
            }
            $partner_id = $this->input->post("partner_id");
            $this->update_booking_internal_status($booking_id, $reason, $partner_id, 'reshedule');
            //Update Booking Set location Starts here
            if ($this->input->post("part_brought_at") && $this->input->post("booking_id")) {
                $part_brought_at = $this->input->post("part_brought_at");
                $booking_id_part = $this->input->post("booking_id");
                $this->booking_model->update_booking($booking_id_part,array('part_brought_at' => $part_brought_at));
            }
            //Update Booking Set location 
            if (!$this->input->post("call_from_api")) {
                $userSession = array('success' => 'Booking Updated');
                $this->session->set_userdata($userSession);
                redirect(base_url() . "service_center/pending_booking");
            }
        }
    }

    /**
     * @desc: This method is used to insert action log into state change table. 
     * Just pass booking id, new state and remarks as parameter
     * @param String $booking_id
     * @param String $new_state
     * @param String $remarks
     */
    function insert_details_in_state_change($booking_id, $new_state, $remarks, $actor, $next_action, $spare_id = NULL, $is_cron = false) {
        
        //Save state change
        if($is_cron){
            $agent_id = _247AROUND_DEFAULT_AGENT;
            $entity_id = _247AROUND;
            $agent_name = _247AROUND;
            
            $this->notify->insert_state_change($booking_id, $new_state, "", $remarks, $agent_id, $agent_name, $actor, $next_action, $entity_id, NULL, $spare_id);
            log_message('info', __FUNCTION__ . " Auto Approve From CRON Booking ID: " . $booking_id . ' new_state: ' . $new_state . ' remarks: ' . $remarks);
        }
        else if(!empty($this->session->userdata('warehouse_id'))) {
            $agent_id = $this->session->userdata('id');
            $entity_id = _247AROUND;
            $agent_name = $this->session->userdata('employee_id');
            
            $this->notify->insert_state_change($booking_id, $new_state, "", $remarks, $agent_id, $agent_name, $actor, $next_action, $entity_id, NULL, $spare_id);
            log_message('info', __FUNCTION__ . " SF ID: " . $this->session->userdata('warehouse_id') . " Booking ID: " . $booking_id . ' new_state: ' . $new_state . ' remarks: ' . $remarks);
        } else { 
            $agent_id = $this->session->userdata('service_center_agent_id');
            $agent_name = $this->session->userdata('service_center_name');
            $entity_id = $this->session->userdata('service_center_id');
            
            $this->notify->insert_state_change($booking_id, $new_state, "", $remarks, $agent_id, $agent_name, $actor, $next_action, NULL, $entity_id, $spare_id);
            log_message('info', __FUNCTION__ . " SF ID: " . $this->session->userdata('service_center_id') . " Booking ID: " . $booking_id . ' new_state: ' . $new_state . ' remarks: ' . $remarks);
        }
    }

    /**
     * @desc: get invoice details to display in view
     * Get Service center Id from session.
     */
    function invoices_details($is_msl = 0) {
        //$this->checkUserSession();
        if (!empty($this->session->userdata('service_center_id'))) {
            $data2['partner_vendor'] = "vendor";
            $data2['partner_vendor_id'] = $this->session->userdata('service_center_id');
            if (!$is_msl) {
                $invoice['final_settlement'] = $this->invoices_model->get_summary_invoice_amount($data2['partner_vendor'], $data2['partner_vendor_id'])[0]['final_amount'];
            }
            $invoice['is_msl'] = $is_msl;
            $this->load->view('service_centers/header');
            $this->load->view('service_centers/invoice_summary', $invoice);
        } else {
            $this->session->sess_destroy();
            redirect(base_url() . "service_center/login");
        }
    }

    /**
     * @desc: get bank transacton details to display in view
     * Get Service center Id from session.
     */
    function bank_transactions() {
        //$this->checkUserSession();
        if (!empty($this->session->userdata('service_center_id'))) {
            $this->load->view('service_centers/header');
            $this->load->view('service_centers/bank_transactions');
        } else {
            $this->session->sess_destroy();
            redirect(base_url() . "service_center/login");
        }
    }

    /**
     * @desc: This is used to update assigned engineer in booking details and insert data into state change and update sc sction table
     * Send SMS to Engineer
     * It gets input in Array Like Array([SY-199171609091] => 1(engineer id))
     * Insert data into Assigned Engineer or State change table
     */
    function assigned_engineers() {
        log_message('info', __FUNCTION__ . " Service_center ID: " . $this->session->userdata('service_center_id'));
        $this->checkUserSession();
        $engineers_id_with_booking_id = $this->input->post('engineer');

        foreach ($engineers_id_with_booking_id as $booking_id => $engineer_id) {
            if (!empty($engineer_id)) {
                log_message('info', __FUNCTION__ . '=> Engineer ID: ' . $engineer_id . "Booking ID" . $booking_id);

                $data['assigned_engineer_id'] = $engineer_id;
                $data['internal_status'] = ENGG_ASSIGNED;
                // Update Assigned Engineer
                $updated_status = $this->booking_model->update_booking($booking_id, $data);
                if ($updated_status) {
                    // Update service center internal status in service center action table
                    // $this->service_centers_model->update_service_centers_action_table($booking_id, array('internal_status' => ENGG_ASSIGNED, 'update_date' => date('Y-m-d H:i:s')));

                    $assigned['booking_id'] = $booking_id;
                    $assigned['current_state'] = ENGG_ASSIGNED;

                    // Check, Is engineer already installed
                    $is_engineer_assigned = $this->vendor_model->get_engineer_assigned($assigned);
                    if (!empty($is_engineer_assigned)) {

                        $assigned['current_state'] = RE_ASSIGNED_ENGINEER;
                    }

                    $assigned['engineer_id'] = $engineer_id;
                    $assigned['service_center_id'] = $this->session->userdata('service_center_id');
                    // Insert data into Assigned Engineer Table
                    $inserted_id = $this->vendor_model->insert_assigned_engineer($assigned);

                    $where = array('booking_id' => $booking_id);

                    $unit_details = $this->booking_model->get_unit_details($where);

                    foreach ($unit_details as $value) {
                        $unitWhere = array("engineer_booking_action.booking_id" => $booking_id,
                            "engineer_booking_action.unit_details_id" => $value['id']);
                        $en = $this->engineer_model->getengineer_action_data("engineer_booking_action.*", $unitWhere);
                        if (empty($en)) {
                            $engineer_action['unit_details_id'] = $value['id'];
                            $engineer_action['booking_id'] = $booking_id;
                            $engineer_action['engineer_id'] = $engineer_id;
                            $engineer_action['service_center_id'] = $this->session->userdata('service_center_id');
                            $engineer_action['current_status'] = _247AROUND_PENDING;
                            $engineer_action['internal_status'] = _247AROUND_PENDING;
                            $engineer_action["create_date"] = date("Y-m-d H:i:s");

                            $this->engineer_model->insert_engineer_action($engineer_action);
                        } else {

                            $engineer_action['engineer_id'] = $engineer_id;
                            $engineer_action['service_center_id'] = $this->session->userdata('service_center_id');
                            $engineer_action['current_status'] = _247AROUND_PENDING;
                            $engineer_action['internal_status'] = _247AROUND_PENDING;
                            $this->engineer_model->update_engineer_table($engineer_action, array('id' => $en[0]['id']));
                        }
                    }

/*  Sending Notification to Engineer on assignment */
                   $select = "id,device_firebase_token,phone,name";
                   $where = array('active'=>1,'id'=>$engineer_id); // replace engg id with variable
                   $result = $this->engineer_model->get_active_engineers($select,$where);
                   $data['firebase_token'] = $result[0]->device_firebase_token;
                   $template = $this->vendor_model->getVendorSmsTemplate(ASSIGN_ENGG_NOTIFICATION); // getting templet 
                   $sms['smsData']['eng_name'] = $result[0]->name; // Adding Engg Name
                   $sms['smsData']['booking_id'] = $booking_id;
                   $smsBody = vsprintf($template, $sms['smsData']);
                   $data['message'] = $smsBody; 
                   $data['phone'] = $result[0]->phone;
                   $this->notify->send_push_notification($data); /// Sending push notification on app //

                    if ($inserted_id) {
                        $this->insert_details_in_state_change($booking_id, $assigned['current_state'], "Engineer Id: " . $engineer_id, "not_define", "not_define");
                    } else { // if ($inserted_id) {
                        log_message('info', '=> Engineer details is not inserted into Assigned Engineer table: '
                                . $booking_id . " Data" . print_r($assigned, true));
                    }
                } else {
                    log_message('info', '=> Booking is not updated: ' . $booking_id);
                }
            }
        }
        // Send SMS to  Engineer to inform booking details                 
        $url = base_url() . "employee/do_background_process/send_sms_to_assigned_engineer";
        $send['booking_id_with_engineer_id'] = $engineers_id_with_booking_id;
        $this->asynchronous_lib->do_background_process($url, $send);
    }

    /**
     * @desc: This is used to load update form for service center
     * @param String Base_encode form - $booking_id
     */
    function update_booking_status($code, $flag = '', $request_spare = 0) {
        log_message('info', __FUNCTION__ . " Booking ID: " . base64_decode(urldecode($code)));
        $this->checkUserSession();
        $data = array();
        $model_nunmber = "";
        $serial_number = "";
        $dateofpurchase = "";
        $serial_number_pic = "";
        $is_disable = false;
        if (!empty($flag)) {

            if (is_numeric($flag)) {
                if ($flag == 1) {
                    $data['consume_spare_status'] = true;
                } else {
                    $data['consume_spare_status'] = false;
                }
            } else {
                $data['consume_spare_status'] = false;
            }
        } else {
            $data['consume_spare_status'] = false;
        }

        $booking_id = base64_decode(urldecode($code));
        if (!empty($booking_id) || $booking_id != 0) {
            $data['booking_id'] = $booking_id;
            $where_internal_status = array("page" => "update_sc", "active" => '1');

            $unit_details = $this->booking_model->get_unit_details(array('booking_id' => $booking_id));
            $data['bookinghistory'] = $this->booking_model->getbooking_history($booking_id);
            $data['booking_symptom'] = $this->booking_model->getBookingSymptom($booking_id);
            $data['unit_details'] = $unit_details;
            $data['on_saas'] = FALSE;
            if (!empty($data['bookinghistory'])) {
                $partner_id = $data['bookinghistory'][0]['partner_id'];
                $access = $this->partner_model->get_partner_permission(array('partner_id' => $partner_id,
                    'permission_type' => PARTNER_ON_SAAS, 'is_on' => 1));
                if (!empty($access)) {
                    $data['on_saas'] = TRUE;
                }
            }
             if (!empty($data['bookinghistory'])) {
                    $data['booking_set_location'][0]['part_brought_at'] = $data['bookinghistory'][0]['part_brought_at'];
             }

            if (!empty($data['bookinghistory'][0])) {
                $spare_shipped_flag = false;
                $data['internal_status'] = array();
                $current_date = date_create(date('Y-m-d'));
                $current_booking_date = date_create(date('Y-m-d', strtotime($data['bookinghistory'][0]['booking_date'])));
                $is_est_approved = false;
                $spareShipped = false;
                $data['bookinghistory']['allow_estimate_approved'] = false;
                if (isset($data['bookinghistory']['spare_parts'])) {
                    foreach ($data['bookinghistory']['spare_parts'] as $sp) {
                        if ($sp['status'] == SPARE_OOW_EST_GIVEN) {
                            if($is_est_approved == false) {
                                array_push($data['internal_status'], array("status" => ESTIMATE_APPROVED_BY_CUSTOMER));
                            }
                            $data['bookinghistory']['allow_estimate_approved'] = true;
                            $is_est_approved = true;
                        }

                        if (($sp['auto_acknowledeged'] == 1 || $sp['auto_acknowledeged'] == 2) && $sp['status'] == SPARE_DELIVERED_TO_SF) {
                            $spare_shipped_flag = TRUE;
                        }

                        switch ($sp['status']) {
                            case SPARE_SHIPPED_BY_PARTNER:
                            case DEFECTIVE_PARTS_PENDING:
                            case DEFECTIVE_PARTS_RECEIVED:
                            case DEFECTIVE_PARTS_REJECTED:
                            case DEFECTIVE_PARTS_REJECTED_BY_WAREHOUSE:
                            case DEFECTIVE_PARTS_SHIPPED:
                            case SPARE_DELIVERED_TO_SF:
                                $spareShipped = TRUE;
                                break;
                        }
                        if ($sp['status'] != _247AROUND_CANCELLED) {
                            $model_nunmber = $sp['model_number'];
                            $serial_number = $sp['serial_number'];
                            $dateofpurchase = $sp['date_of_purchase'];
                            $serial_number_pic = $sp['serial_number_pic'];
                            $is_disable = true;
                        }
                    }
                }
                
                $data['spare_shipped'] = $spareShipped;
                $date_diff = date_diff($current_date, $current_booking_date);
                $data['Service_Center_Visit'] = 0;
                // We will not display internal status after 1st day.
                if ($date_diff->days < 1) {
                    $int = $this->booking_model->get_internal_status($where_internal_status, true);
                    $data['internal_status'] = array_merge($data['internal_status'], $int);
                    $data['days'] = 0;
                } else if ($date_diff->days < 3) {
                    $data['days'] = $date_diff->days;
                    array_push($data['internal_status'], array('status' => CUSTOMER_NOT_REACHABLE));
                } else {

                    $data['days'] = 0;

                    if ($spareShipped) {

                        array_push($data['internal_status'], array('status' => CUSTOMER_NOT_REACHABLE));
                    }
                }
                $price_tags_symptom = array();
                $data['spare_flag'] = SPARE_PART_RADIO_BUTTON_NOT_REQUIRED;
                $is_serial_number_required =0;
                $is_invoice_required = 0;
                foreach ($unit_details as $value) {

                $price_tags1 = str_replace('(Free)', '', $value['price_tags']);
                $price_tags2 = str_replace('(Paid)', '', $price_tags1);
                array_push($price_tags_symptom, $price_tags2);

                    if (strcasecmp($value['price_tags'], REPAIR_OOW_TAG) == 0) {
                        if (!$is_est_approved) {
                            $data['spare_flag'] = SPARE_OOW_EST_REQUESTED;
                            $data['price_tags'] = $value['price_tags'];
                        }
                    } else if (stristr($value['price_tags'], "Repair") 
                            || stristr($value['price_tags'], "Repeat") 
                            || stristr($value['price_tags'], "Replacement") 
                            || stristr($value['price_tags'], "Dead On Arrival (DOA)")
                            || stristr($value['price_tags'], "Dead after Purchase (DaP)")
                            || stristr($value['price_tags'], EXTENDED_WARRANTY_TAG)
                            || stristr($value['price_tags'], PRESALE_REPAIR_TAG) 
                            || stristr($value['price_tags'], GAS_RECHARGE_IN_WARRANTY) 
                            || stristr($value['price_tags'], AMC_PRICE_TAGS) 
                            || stristr($value['price_tags'], GAS_RECHARGE_OUT_OF_WARRANTY)) {

                        $data['spare_flag'] = SPARE_PARTS_REQUIRED;
                        $data['price_tags'] = $value['price_tags'];
                    }
                    if (stristr($value['price_tags'], "Service Center Visit")) {
                        array_push($data['internal_status'], array("status" => CUSTOMER_NOT_VISTED_TO_SERVICE_CENTER));
                    }

                    if (empty($model_nunmber)) {
                        if (!empty($value['model_number'])) {
                            $model_nunmber = $value['model_number'];
                        } else if (!empty($value['sf_model_number'])) {
                            $model_nunmber = $value['sf_model_number'];
                        }
                    }
                    if (empty($serial_number)) {
                        if (!empty($value['serial_number'])) {
                            $serial_number = $value['serial_number'];
                        }
                    }
                    if (empty($dateofpurchase)) {
                        if (!empty($value['sf_purchase_date'])) {
                            $dateofpurchase = $value['sf_purchase_date'];
                        } else if (!empty($value['purchase_date'])) {
                            $dateofpurchase = $value['purchase_date'];
                        }
                    }
                    if (empty($serial_number_pic)) {
                        if (!empty($value['serial_number_pic'])) {
                            $serial_number_pic = $value['serial_number_pic'];
                        }
                    }
                    
                    if ($value['pod'] == 1) {
                        $is_serial_number_required = 1;
                    }
                    
                    if ($value['invoice_pod'] == 1) {
                        $is_invoice_required = 1;
                    }
                }

                $data['unit_model_number'] = $model_nunmber;
                $data['unit_serial_number'] = $serial_number;
                $data['purchase_date'] = $dateofpurchase;
                /*  Again Ask for purchase date to fill if no spare involved or all are cancelled */
                $part_dependency = $this->inventory_model->get_spare_parts_details("id, status,partner_id,service_center_id,shipped_inventory_id,shipped_quantity,booking_id,parts_shipped", array("booking_id"=>$booking_id, "status != '"._247AROUND_CANCELLED."'" => NULL));
                $data['ask_purchase_date'] = 0; 
//                if(!empty($part_dependency)){   
//                 $data['ask_purchase_date'] = 0;   
//                }else{
//                   $data['ask_purchase_date'] = 1; 
//                }
                
                $data['is_disable'] = $is_disable;
                $data['is_serial_number_required'] = $is_serial_number_required;
                $data['is_invoice_required'] = $is_invoice_required;
                $data['unit_serial_number_pic'] = $serial_number_pic;
                $where = array('entity_id' => $data['bookinghistory'][0]['partner_id'], 'entity_type' => _247AROUND_PARTNER_STRING, 'service_id' => $data['bookinghistory'][0]['service_id'], 'inventory_model_mapping.active' => 1, 'appliance_model_details.active' => 1);
                $data['inventory_details'] = $this->inventory_model->get_inventory_mapped_model_numbers('appliance_model_details.id,appliance_model_details.model_number', $where);
                $data['spare_shipped_flag'] = $spare_shipped_flag;
                $data['saas_module'] = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
//                $data['spare_flag'] = SPARE_PART_RADIO_BUTTON_NOT_REQUIRED;
                if ($data['bookinghistory'][0]['nrn_approved'] == 1) {                    
                    $data['nrn_flag'] = 1;
                }

                $data['spare_parts_details'] = $this->partner_model->get_spare_parts_by_any('spare_parts_details.*, inventory_master_list.part_number', ['booking_id' => $booking_id, 'spare_parts_details.status != "' . _247AROUND_CANCELLED . '"' => NULL, 'parts_shipped is not null' => NULL, 'consumed_part_status_id is null' => NULL], FALSE, FALSE, FALSE, ['is_inventory' => true]);
                $data['spare_consumed_status'] = $this->reusable_model->get_search_result_data('spare_consumption_status', 'id, consumed_status,status_description,tag', ['active' => 1], NULL, NULL, ['consumed_status' => SORT_ASC], NULL, NULL);

/*  getting symptom */
                if (!empty($price_tags_symptom)) {
                 $data['technical_problem'] = $this->booking_request_model->get_booking_request_symptom('symptom.id, symptom', array('symptom.service_id' => $data['bookinghistory'][0]['service_id'], 'symptom.active' => 1, 'symptom.partner_id' => $data['bookinghistory'][0]['partner_id']), array('request_type.service_category' => $price_tags_symptom));
                 }

                $data['request_spare'] = $request_spare; 
                $this->load->view('service_centers/header');
                $this->load->view('service_centers/get_update_form', $data);
            } else {
                echo "Booking Not Found. Please Retry Again";
            }
        } else {
            echo "Booking Not Found. Please Retry Again";
        }
    }

    /**
     * @desc: This is used to get required spare parts to partner 
     * @param String Base_encode form - $id
     */
    function update_booking_spare_parts_required($code) {
        log_message('info', __FUNCTION__ . " Spare Parts ID: " . base64_decode(urldecode($code)));
        $this->checkUserSession();
        $spare_id = base64_decode(urldecode($code));
        $where = array('spare_parts_details.id' => $spare_id);
        $select = 'spare_parts_details.id,spare_parts_details.booking_id,spare_parts_details.defect_pic,spare_parts_details.spare_request_symptom,spare_parts_details.partner_id,spare_parts_details.entity_type,spare_parts_details.booking_id,spare_parts_details.date_of_purchase,spare_parts_details.model_number,'
                . 'spare_parts_details.serial_number,spare_parts_details.serial_number_pic,spare_parts_details.invoice_pic,'
                . 'spare_parts_details.parts_requested,spare_parts_details.parts_requested_type,spare_parts_details.invoice_pic,spare_parts_details.part_warranty_status,'
                . 'spare_parts_details.defective_parts_pic,spare_parts_details.defective_back_parts_pic,spare_parts_details.requested_inventory_id,spare_parts_details.serial_number_pic,spare_parts_details.remarks_by_sc,'
                . 'booking_details.service_id,booking_details.partner_id as booking_partner_id, spare_parts_details.quantity, booking_details.assigned_vendor_id,booking_details.user_id,booking_details.request_type,booking_details.create_date as booking_create_date';

        $spare_parts_details = $this->partner_model->get_spare_parts_by_any($select, $where, TRUE, TRUE, false);

        $spare_on_approval = $this->partner_model->get_spare_parts_by_any("spare_parts_details.id,spare_parts_details.booking_id,spare_parts_details.status", array('spare_parts_details.booking_id' => $spare_parts_details[0]['booking_id'], 'spare_parts_details.status NOT IN("' . SPARE_PART_ON_APPROVAL . '","' . SPARE_PARTS_CANCELLED . '")' => null));
        
        if (!empty($spare_on_approval)) {
            $data['approval_flag'] = TRUE;
        } else {
            $data['approval_flag'] = false;
        }

        $data['spare_parts_details'] = $spare_parts_details[0];
        $where1 = array('entity_id' => $spare_parts_details[0]['partner_id'], 'entity_type' => _247AROUND_PARTNER_STRING, 'service_id' => $spare_parts_details[0]['service_id'], 'inventory_model_mapping.active' => 1, 'appliance_model_details.active' => 1);
        $data['inventory_details'] = $this->inventory_model->get_inventory_mapped_model_numbers('appliance_model_details.id,appliance_model_details.model_number', $where1);
        $this->load->view('service_centers/header');
        $data['technical_problem'] = array();
        $price_tags_symptom = array();
        $data['bookinghistory'] = $this->booking_model->getbooking_history($spare_parts_details[0]['booking_id']);
        $unit_details = $this->booking_model->get_unit_details(array('booking_id' => $spare_parts_details[0]['booking_id']));
        $data['unit_details'] = $unit_details;
        foreach ($unit_details as $value) {
         $price_tags1 = str_replace('(Free)', '', $value['price_tags']);
         $price_tags2 = str_replace('(Paid)', '', $price_tags1);
         array_push($price_tags_symptom, $price_tags2);
        }
        /*  getting symptom */
        if (!empty($price_tags_symptom)) {
        $data['technical_problem'] = $this->booking_request_model->get_booking_request_symptom('symptom.id, symptom', array('symptom.service_id' => $data['bookinghistory'][0]['service_id'], 'symptom.active' => 1, 'symptom.partner_id' => $data['bookinghistory'][0]['partner_id']), array('request_type.service_category' => $price_tags_symptom));
        }

        $this->load->view('service_centers/get_update_spare_parts_required_form', $data);
    }

    /**
     * @desc: This is used to update spare parts details
     * @$_POST form data 
     */
    function update_spare_parts_details() {
        log_message('info', __FUNCTION__ . " Service_center ID: " . $this->session->userdata('service_center_id') . " Booking Id: " . $this->input->post('booking_id'));
        log_message('info', __METHOD__ . " POST DATA " . json_encode($this->input->post()));
        $access = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        if (!empty($_FILES['defective_parts_pic']['name'][0]) || !empty($_FILES['defective_back_parts_pic']['name'][0]) || !empty($_FILES['defect_pic']['name'][0])) {
            $is_file = $this->validate_part_data();
        }

        if (!empty($_FILES['serial_number_pic']['name'])) {
            $this->validate_serial_number_pic_upload_file();
            $ud_data['serial_number_pic'] = $data['serial_number_pic'] = $this->input->post('serial_number_pic');
        }

        if (!empty($_FILES['invoice_image']['name'])) {
            $this->validate_invoice_image_upload_file();
            $data['invoice_pic'] = $this->input->post('invoice_pic');
        }

        if (!empty($this->input->post('spare_id'))) {
            $parts_requested = $this->input->post('part');
            $ud_data['sf_model_number'] = $data['model_number'] = $this->input->post('model_number');
            $ud_data['serial_number'] = $data['serial_number'] = $this->input->post('serial_number');
            $ud_data['sf_purchase_date'] = $data['date_of_purchase'] = date("Y-m-d", strtotime($this->input->post('dop')));

            $data['part_warranty_status'] = $this->input->post('part_warranty_status');
            $data['remarks_by_sc'] = $this->input->post('reason_text');

            foreach ($parts_requested as $value) {

                $data['parts_requested'] = $value['parts_name'];
                if (!empty($value['parts_type'])) {
                    $data['parts_requested_type'] = $value['parts_type'];
                } else {
                    $data['parts_requested_type'] = $value['parts_name'];
                }

                if (isset($value['defective_parts'])) {
                    $data['defective_parts_pic'] = $value['defective_parts'];
                }

                if (isset($value['defective_back_parts_pic'])) {
                    $data['defective_back_parts_pic'] = $value['defective_back_parts_pic'];
                }
                if (isset($value['date_of_request'])) {
                    $data['date_of_request'] = $value['date_of_request'];
                }

                if (isset($value['service_center_id'])) {
                    $data['service_center_id'] = $value['service_center_id'];
                }

                if (isset($value['booking_id'])) {
                    $data['booking_id'] = $value['booking_id'];
                }

                if (isset($value['quantity'])) {
                    $data['quantity'] = $value['quantity'];
                }
                if(isset($value['defect_pic']) && !empty($value['defect_pic'])){
                        $data['defect_pic'] = $value['defect_pic'];
                }

                if(isset($value['symptom']) && !empty($value['symptom'])){
                       $data['spare_request_symptom'] = $value['symptom']; 
                }
            }
        }

        $partner_id = $this->input->post('partner_id');
        $entity_type = $this->input->post('entity_type');
        $previous_inventory_id = $this->input->post('previous_inventory_id');

        $data['requested_inventory_id'] = $current_inventory_id = $this->input->post('current_inventory_id');
        $booking_id = $this->input->post('booking_id');
        $service_center_id = $this->input->post('service_center_id');

        $change_inventory_id = '';
        if (isset($previous_inventory_id) && !empty($current_inventory_id)) {
            if ($previous_inventory_id != $current_inventory_id) {
                $change_inventory_id = $current_inventory_id;
                
            } else {
                $change_inventory_id = $data['requested_inventory_id'] = $previous_inventory_id;
            }
        } else {
            $change_inventory_id = $data['requested_inventory_id'] = $previous_inventory_id;
        }


        $sf_state = $this->vendor_model->getVendorDetails("service_centres.state", array('service_centres.id' => $service_center_id));
        if (!empty($change_inventory_id)) {

            $warehouse_details = $this->miscelleneous->check_inventory_stock($data['requested_inventory_id'], $partner_id, $sf_state[0]['state'], $service_center_id, $data['model_number']);

            if (!empty($warehouse_details)) {
                $data['partner_id'] = $warehouse_details['entity_id'];
                $data['entity_type'] = $warehouse_details['entity_type'];
                $data['defective_return_to_entity_type'] = $warehouse_details['defective_return_to_entity_type'];
                $data['defective_return_to_entity_id'] = $warehouse_details['defective_return_to_entity_id'];
                $data['is_micro_wh'] = $warehouse_details['is_micro_wh'];
                $data['challan_approx_value'] = $warehouse_details['challan_approx_value'] * $data['quantity'];
                $data['parts_requested'] = $warehouse_details['part_name'];
                $data['parts_requested_type'] = $warehouse_details['type'];
                $data['requested_inventory_id'] = $warehouse_details['inventory_id'];
            } else {
                $data['partner_id'] = $partner_id;
                $data['entity_type'] = _247AROUND_PARTNER_STRING;
                $data['is_micro_wh'] = 0;
                if (isset($warehouse_details['challan_approx_value'])) {
                    $data['challan_approx_value'] = round($warehouse_details['challan_approx_value'] * $data['quantity'], 2);
                }
                $data['defective_return_to_entity_type'] = _247AROUND_SF_STRING;
                $data['defective_return_to_entity_id'] = DEFAULT_WAREHOUSE_ID;
                $data['requested_inventory_id'] = $change_inventory_id;
            }
            
            /**
            * change defective part required in spare part details
            * @modifiedBy Ankit Rajvanshi
            */
            if ($data['part_warranty_status'] == SPARE_PART_IN_WARRANTY_STATUS) {
                $data['defective_part_required'] = $this->inventory_model->is_defective_part_required($this->input->post('booking_id'), $data['requested_inventory_id'], $data['partner_id'], $warehouse_details['type']);
            } else {
                $data['defective_part_required'] = 0;
            }
        } else {
            $data['partner_id'] = $partner_id;
            $data['entity_type'] = _247AROUND_PARTNER_STRING;
            $data['is_micro_wh'] = 0;
            $data['defective_return_to_entity_type'] = _247AROUND_SF_STRING;
            $data['defective_return_to_entity_id'] = DEFAULT_WAREHOUSE_ID;
        }

//        if (!isset($data['defective_return_to_entity_id'])) {
//            if ($partner_details[0]['is_defective_part_return_wh'] == 1) {
//                $wh_address_details = $this->miscelleneous->get_247aroud_warehouse_in_sf_state($sf_state[0]['state']);
//                if (!empty($wh_address_details)) {
//                    $data['defective_return_to_entity_type'] = $wh_address_details[0]['entity_type'];
//                    $data['defective_return_to_entity_id'] = $wh_address_details[0]['entity_id'];
//                } else {
//                    $data['defective_return_to_entity_type'] = _247AROUND_PARTNER_STRING;
//                    $data['defective_return_to_entity_id'] = $partner_id;
//                }
//            } else {
//                $data['defective_return_to_entity_type'] = _247AROUND_PARTNER_STRING;
//                $data['defective_return_to_entity_id'] = $partner_id;
//            }
//        }    
        $delivered_sp = array();
        if ($data['is_micro_wh'] == 1) {

            $data['spare_id'] = $this->input->post('spare_id');
            $data['shipped_inventory_id'] = $data['requested_inventory_id'];
            $data['shipped_quantity'] = $data['quantity'];
            array_push($delivered_sp, $data);
            unset($data['spare_id']);
        }

        if (empty($access)) {
            if ($data['defective_return_to_entity_type'] == _247AROUND_PARTNER_STRING) {
                $data['defective_return_to_entity_type'] = _247AROUND_SF_STRING;
                $data['defective_return_to_entity_id'] = DEFAULT_WAREHOUSE_ID;
            }
        }
        $where = array('id' => $this->input->post('spare_id'));
        if ($this->session->userdata('user_group') == 'admin' || $this->session->userdata('user_group') == 'inventory_manager' || $this->session->userdata('user_group') == 'developer') {

            $affected_row = $this->service_centers_model->update_spare_parts($where, $data);
            if (!empty($ud_data)) {
                $this->booking_model->update_booking_unit_details($booking_id, $ud_data);
            }
            $this->auto_delivered_for_micro_wh($delivered_sp, $partner_id);


            if ($affected_row == TRUE) {
                $spare_id = $this->input->post('spare_id');

                /**
                 * Update model number in all pending spares.
                 * @modifiedBy Ankit Rajvanshi
                 */
                $other_spare_parts_details = $this->partner_model->get_spare_parts_by_any('spare_parts_details.*', array('spare_parts_details.booking_id' => $this->input->post('booking_id'), 'spare_parts_details.parts_shipped is not null and spare_parts_details.shipped_date is not null' => NULL), TRUE, TRUE, false, array('symptom'=>1));
                if(empty($other_spare_parts_details)) {
                    $other_pending_where = array(
                        'booking_id' => $this->input->post('booking_id'),
                        'status <> "'._247AROUND_CANCELLED.'"' => NULL,
                        'id <> '.$this->input->post('spare_id') => NULL,
                        'parts_shipped is null and shipped_date is null' => NULL
                    );

                    // set model details for other pending parts.
                    $other_pending_data = array(
                        'model_number' => $data['model_number'],
                        'serial_number' => $data['serial_number'],
                        'date_of_purchase' => $data['date_of_purchase'],
                    );
                    if (!empty($_FILES['serial_number_pic']['name'])) {
                        $other_pending_data['serial_number_pic'] = $this->input->post('serial_number_pic');
                    }
                    if (!empty($_FILES['invoice_image']['name'])) {
                        $other_pending_data['invoice_pic'] = $this->input->post('invoice_pic');
                    }
                    // update data.
                    $this->service_centers_model->update_spare_parts($other_pending_where, $other_pending_data);
                }
                
                /* Insert Spare Tracking Details */
                if (!empty($spare_id)) {
                    $tracking_details = array('spare_id' => $spare_id, 'action' => SPARE_PART_UPDATED, 'remarks' => trim($data['remarks_by_sc']), 'agent_id' => $this->session->userdata("id"), 'entity_id' => _247AROUND, 'entity_type' => _247AROUND_EMPLOYEE_STRING);
                    $this->service_centers_model->insert_spare_tracking_details($tracking_details);
                }
                $this->notify->insert_state_change($booking_id, SPARE_PART_UPDATED, "", $data['remarks_by_sc'], $this->session->userdata('id'), $this->session->userdata('emp_name'), NULL, NULL, _247AROUND, NULL, $spare_id);
                $userSession = array('success' => 'Spare Parts Updated');
                $this->session->set_userdata($userSession);
                redirect(base_url() . "employee/inventory/get_spare_parts");
            } else {
                $userSession = array('error' => 'Spare Parts Not Updated');
                $this->session->set_userdata($userSession);
                redirect(base_url() . "employee/inventory/get_spare_parts");
            }
        } else {
            $this->checkUserSession();
            $affected_row = $this->service_centers_model->update_spare_parts($where, $data);
            if (!empty($ud_data)) {
                $this->booking_model->update_booking_unit_details($booking_id, $ud_data);
            }
            $this->auto_delivered_for_micro_wh($delivered_sp, $partner_id);

            if ($affected_row == TRUE) {
                $this->notify->insert_state_change($booking_id, SPARE_PART_UPDATED, "", $data['remarks_by_sc'], $this->session->userdata('service_center_id'), $this->session->userdata('service_center_name'), NULL, NULL, $partner_id, NULL);
                $userSession = array('success' => 'Spare Parts Updated');
                $this->session->set_userdata($userSession);
                redirect(base_url() . "service_center/pending_booking");
            } else {
                $userSession = array('error' => 'Spare Parts Not Updated');
                $this->session->set_userdata($userSession);
                redirect(base_url() . "service_center/pending_booking");
            }
        }
    }

    /**
     * @desc: This is used to update booking by SF. 
     *  IF Rescheduled option ( checkbox) is selected Then it perform save_reschedule_request Method 
     *  IF Spare Parts is selected then call update_spare_parts method
     *  Otherwise its get method name from table. If method name is not exist in the table default_update perform.  
     */
    function process_update_booking() {
        log_message('info', __FUNCTION__ . " Service_center ID: " . $this->session->userdata('service_center_id') . " Booking Id: " . $this->input->post('booking_id'));
        if (!$this->input->post("call_from_api")) {
            // Check User Session
            $this->checkUserSession();
        } else {

            $response = array();
        }
       
        $f_status = true;
        $booking_id = $this->input->post('booking_id');
        //if current status of the booking is Completed or Cancelled then the booking cannot be Updated.
         $booking_details = $this->booking_model->get_booking_details('*',['booking_id' => $booking_id])[0]['current_status'];
        if ($booking_details == _247AROUND_COMPLETED || $booking_details == _247AROUND_CANCELLED) {
             $this->session->set_userdata('error', "Booking is already $booking_details. You cannot update the booking.");
            redirect(base_url() . "service_center/pending_booking");
        }
        
        $is_booking_able_to_reschedule = $this->booking_creation_lib->is_booking_able_to_reschedule($this->input->post('booking_id'));
        if ($is_booking_able_to_reschedule === FALSE) {
            if (!$this->input->post("call_from_api")) {
              //  $this->session->set_userdata(['error' => BOOKING_RESCHEDULE_ERROR_MSG]);
                $this->update_booking_status(urlencode(base64_encode($booking_id)));
            } else {
                $response['status'] = false;
                $response['message'] = BOOKING_RESCHEDULE_ERROR_MSG;
            }
        } else {
            if (!$this->input->post("call_from_api")) {
                // Check form validation
                $f_status = $this->checkvalidation_for_update_by_service_center();
            } else {
                $f_status = true;
            }
        }

        if ($f_status) {
            $reason = $this->input->post('reason');


            switch ($reason) {

                CASE PRODUCT_NOT_DELIVERED_TO_CUSTOMER:
                CASE RESCHEDULE_FOR_UPCOUNTRY:
                CASE SPARE_PARTS_NOT_DELIVERED_TO_SF:
                    log_message('info', __FUNCTION__ . " " . $this->input->post('reason') . " Request: " . $this->session->userdata('service_center_id'));
                    $this->save_reschedule_request();
                    break;

                CASE CUSTOMER_ASK_TO_RESCHEDULE:
                    log_message('info', __FUNCTION__ . " " . $this->input->post('reason') . " Request: " . $this->session->userdata('service_center_id'));
                    $this->save_reschedule_request();
                    $booking_id = $this->input->post('booking_id');
                    $this->booking_model->increase_escalation_reschedule($booking_id, "count_reschedule");

                    break;
                CASE ESTIMATE_APPROVED_BY_CUSTOMER:
                    log_message('info', __FUNCTION__ . ESTIMATE_APPROVED_BY_CUSTOMER . " Request: " . $this->session->userdata('service_center_id'));
                    $booking_id = $this->input->post('booking_id');
                    $this->approve_oow($booking_id);
                    break;

                CASE SPARE_PARTS_REQUIRED:
                CASE SPARE_OOW_EST_REQUESTED:
                    log_message('info', __FUNCTION__ . " " . $reason . " :" . $this->session->userdata('service_center_id'));
                    $this->update_part_consumption($booking_id, $this->input->post());
                    $this->update_spare_parts();
                    break;

                CASE CUSTOMER_NOT_REACHABLE:
                    log_message('info', __FUNCTION__ . CUSTOMER_NOT_REACHABLE . $this->session->userdata('service_center_id'));
                    $day = $this->input->post('days');
                    $sc_remarks = $this->input->post('sc_remarks');
                    $spare_shipped = $this->input->post("spare_shipped");
                    if (!$spare_shipped) {
                        if ($day == 2) {
                            $booking_id = $this->input->post('booking_id');
                            $_POST['cancellation_reason'] = CUSTOMER_NOT_REACHABLE_VENDOR_CANCELLATION_ID;
                            $_POST['cancellation_reason_text'] = $sc_remarks;
                            $this->process_cancel_booking($booking_id);

                            $to = NITS_ANUJ_EMAIL_ID;
                            $cc = "";
                            $bcc = "";
                            $subject = "Auto Cancelled Booking - 3rd Day Customer Not Reachable.";
                            $message = "Auto Cancelled Booking " . $booking_id;
                            $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, $bcc, $subject, $message, "", AUTO_CANCELLED_BOOKING, "", $booking_id);
                        } else {
                            $this->default_update(true, true);
                        }
                    } else {
                        $this->default_update(true, true);
                    }

                    break;

                case ENGINEER_ON_ROUTE:
                case CUSTOMER_NOT_VISTED_TO_SERVICE_CENTER:
                    log_message('info', __FUNCTION__ . " " . $reason . " " . $this->session->userdata('service_center_id'));
                    $this->default_update(true, true);
                    break;
            }
            if ($this->input->post("call_from_api")) {
                $response['status'] = true;
                $response['message'] = 'Booking Updated Successfully';
                echo json_encode($response);
            }
        } else {
            if (!$this->input->post("call_from_api")) {
                echo "Update Failed Please Retry Again";
            } else {
                $response['status'] = false;
                $response['message'] = 'Update Failed Please Retry Again';
                echo json_encode($response);
            }
        }

        log_message('info', __FUNCTION__ . " Exit Service_center ID: " . $this->session->userdata('service_center_id'));
    }

    function update_booking_internal_status($booking_id, $internal_status, $partner_id, $booking_action = null) {

        $booking['internal_status'] = $internal_status;
        $partner_status = $this->booking_utilities->get_partner_status_mapping_data(_247AROUND_PENDING, $booking['internal_status'], $partner_id, $booking_id);
        if (!empty($partner_status)) {
            $booking['partner_current_status'] = $partner_status[0];
            $booking['partner_internal_status'] = $partner_status[1];
            $booking['actor'] = $partner_status[2];
            $booking['next_action'] = $partner_status[3];

            if (!empty($booking_action) && $booking_action == 'reshedule') {
                unset($booking['actor']);
                unset($booking['next_action']);
            }
        }

        $this->booking_model->update_booking($booking_id, $booking);

        log_message('info', __METHOD__ . " Partner ID " . $partner_id . " Status " . $internal_status);
        $response = $this->miscelleneous->partner_completed_call_status_mapping($partner_id, $internal_status);
        if (!empty($response)) {

            $this->booking_model->partner_completed_call_status_mapping($booking_id, array('partner_call_status_on_completed' => $response));
        } else {
            log_message('info', __METHOD__ . " Staus Not found for partner ID " . $partner_id . " status " . $internal_status);
        }

        if ($internal_status == "InProcess_Cancelled" || $internal_status == "InProcess_Completed") {
            log_message("info", __METHOD__ . " DO Not Call patner callback");
        } else {
            $cb_url = base_url() . "employee/do_background_process/send_request_for_partner_cb/" . $booking_id;
            $pcb = array();
            $this->asynchronous_lib->do_background_process($cb_url, $pcb);
        }
    }

    /**
     * @desc:
     * @param boolean $redirect
     * @param boolean $state_change
     */
    function default_update($redirect, $state_change) {
        if (!$this->input->post("call_from_api")) {
            log_message('info', __FUNCTION__ . " Service_center ID: " . $this->session->userdata('service_center_id') . " Booking Id: " . $this->input->post('booking_id'));
            $this->checkUserSession();
        }

        $booking_id = $this->input->post('booking_id');
        $sc_data['internal_status'] = $this->input->post('reason');
        $sc_data['current_status'] = 'InProcess';
        $sc_data['service_center_remarks'] = date("F j") . ":- " . $this->input->post('sc_remarks');
        // Update Service center Action table
        $this->service_centers_model->update_service_centers_action_table($booking_id, $sc_data);
        if ($state_change) {
            // Insert data into state change
            if ($this->input->post("call_from_api")) {
                $this->notify->insert_state_change($booking_id, $sc_data['internal_status'], "", $sc_data['service_center_remarks'], $this->input->post('sc_agent_id'), "Engineer", "not_define", "not_define", NULL, $this->input->post('service_center_id'));
            } else {
                $this->insert_details_in_state_change($booking_id, $sc_data['internal_status'], $sc_data['service_center_remarks'], "not_define", "not_define");
            }

            // Send sms to customer while customer not reachable
            if ($sc_data['internal_status'] == CUSTOMER_NOT_REACHABLE) {
                log_message('info', __FUNCTION__ . " Send Sms to customer => Customer not reachable");
                $url = base_url() . "employee/do_background_process/send_sms_email_for_booking";
                $send['booking_id'] = $booking_id;
                $send['state'] = "Customer not reachable";
                $this->asynchronous_lib->do_background_process($url, $send);
            }
        }
        $partner_id = $this->input->post("partner_id");
        $this->update_booking_internal_status($booking_id, $sc_data['internal_status'], $partner_id);
        log_message('info', __FUNCTION__ . " Exit Service_center ID: " . $this->session->userdata('service_center_id'));
        if ($redirect) {
            if (!$this->input->post("call_from_api")) {
                $userSession = array('success' => 'Booking Updated');
                $this->session->set_userdata($userSession);
                redirect(base_url() . "service_center/pending_booking");
            }
        }
    }

    /**
     * 
     * @return boolean
     */
    function checkvalidation_for_update_by_service_center() {
        $this->checkUserSession();
        $this->form_validation->set_rules('booking_id', 'Booking Id', 'trim|required');
        $this->form_validation->set_rules('reason', 'Reason', 'trim|required');
        $this->form_validation->set_rules('reason_text', 'reason_text', 'trim|');
        if ($this->form_validation->run() == FALSE) {
            log_message('info', __FUNCTION__ . " Service_center ID: " . $this->session->userdata('service_center_id'));
            return FALSE;
        } else {
            return true;
        }
    }
    
    /**
     * Update consumption at the time of spare part request.
     * @param type $post
     * @author Ankit Rajvanshi
     */
    function update_part_consumption($booking_id, $post) {
        if(!empty($post['spare_consumption_status']) && !empty($booking_id)) { 
            foreach($post['spare_consumption_status'] as $spare_id => $consumed_status_id) {
                $update_data = [];
                $courier_lost_spare = [];
                $update_data['spare_parts_details.consumed_part_status_id'] = $consumed_status_id;
                // fetch record of $consumed_status_id from table spare_consumption_status. 
                $consumption_status_tag = $this->reusable_model->get_search_result_data('spare_consumption_status', 'tag', ['id' => $consumed_status_id], NULL, NULL, NULL, NULL, NULL)[0]['tag'];
                // fetch record of $spare_id from table spare_parts_details. 
                $spare_part_detail = $this->reusable_model->get_search_result_data('spare_parts_details', '*', ['id' => $spare_id], NULL, NULL, NULL, NULL, NULL)[0];
                
                // set Ok part return in case of wrong & damage/broken part.
                $return_ok_part = 0;
                if($consumption_status_tag == DAMAGE_BROKEN_PART_RECEIVED_TAG || $consumption_status_tag == WRONG_PART_RECEIVED_TAG) {
                    $update_data['status'] = OK_PART_TO_BE_SHIPPED;
                    $update_data['defective_part_required'] = 1;
                    $return_ok_part = 1;
                }
                // in case of courier lost .
                if($consumption_status_tag == PART_NOT_RECEIVED_COURIER_LOST_TAG) {
                    $courier_lost_spare[] = $spare_part_detail;
                    $update_data['status'] = InProcess_Courier_Lost;
                }
                // set defective part return for part consumed.
                if($consumption_status_tag == PART_CONSUMED_TAG) {
                    $update_data['status'] = _247AROUND_COMPLETED;
                    if($spare_part_detail['defective_part_required'] == 1) {
                        $update_data['status'] = DEFECTIVE_PARTS_PENDING;
                    } 
                }
                // set remarks if remarks not empty.
                if(!empty($post['consumption_remarks']) && !empty($post['consumption_remarks'][$spare_id])) {
                    $update_data['spare_parts_details.consumption_remarks'] = $post['consumption_remarks'][$spare_id];
                }
                // update spare parts details.
                $this->service_centers_model->update_spare_parts(array('spare_parts_details.id' => $spare_id), $update_data);
                
                // generate challan.
                if (!empty($this->session->userdata('service_center_id')) 
                    && (!empty($spare_part_detail['defective_part_required']) || $return_ok_part==1) && ($spare_part_detail['defective_part_required'] == 1 || $return_ok_part==1) 
                    && empty($spare_part_detail['defective_part_shipped']) && empty($spare_part_detail['defective_part_shipped_date'])) {
                    $this->invoice_lib->generate_challan_file($spare_id, $this->session->userdata('service_center_id'),'',true);
                }
            }
            // send mail in case of courier lost.
            if (!empty($courier_lost_spare) && !empty($this->session->userdata('service_center_id'))) {
                $this->service_centers_model->get_courier_lost_email_template($booking_id, $courier_lost_spare);
            }
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * @desc: This is used to insert spare parts details into table provided by SF
     * IF Booking date is not empty means its 247Around booking. We reschedule that booking.
     */
    function update_spare_parts() {
        log_message('info', __FUNCTION__ . " Service_center ID: " . $this->session->userdata('service_center_id') . " Booking Id: " . $this->input->post('booking_id'));
        log_message('info', __METHOD__ . " POST DATA " . json_encode($this->input->post()));
        if (!$this->input->post("call_from_api")) {
            $this->checkUserSession();
        } else {
            $returnData = array();
        }
        
        $this->form_validation->set_rules('booking_id', 'Booking Id', 'trim|required');
        $this->form_validation->set_rules('model_number', 'Model Number', 'trim|required');
        $this->form_validation->set_rules('model_number_id', 'Model Number', 'trim');
        $this->form_validation->set_rules('serial_number', 'Serial Number', 'trim|required');

        $this->form_validation->set_rules('invoice_image', 'Invoice Image', 'callback_validate_invoice_image_upload_file');
        $this->form_validation->set_rules('serial_number_pic', 'Invoice Image', 'callback_validate_serial_number_pic_upload_file');

        $is_same_parts_type = $this->is_part_already_requested();
        $access = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);

        if (empty($is_same_parts_type)) {

            if (!$this->input->post("call_from_api")) {
                $service_center_id = $this->session->userdata('service_center_id');
                
                if (empty($access)) {
                    $is_file = $this->validate_part_data();
                } else {
                    $is_file['code'] = true;
                }
                
                if (!$this->form_validation->run()) {
                    $booking_id = urlencode(base64_encode($this->input->post('booking_id')));
                    if (!empty($is_file['code'])) {
                        $userSession = array("error" => "Form validation Error");
                        $this->session->set_userdata($userSession);
                    }
                    $this->update_booking_status($booking_id);
                }
            } else {
                $is_file['code'] = true;
                $service_center_id = $this->input->post("service_center_id");
            }

            if (!empty($is_file['code'])) {
                $parts_requested = $this->input->post('part');
                $booking_id = $this->input->post('booking_id');
                $data_to_insert = array();
                $approval_array = array();

                if ($this->input->post('invoice_pic')) {
                    $data['invoice_pic'] = $this->input->post('invoice_pic');
                }

                $data['serial_number_pic'] = $this->input->post('serial_number_pic');
                $data['model_number'] = $this->input->post('model_number');
                $data['serial_number'] = $this->input->post('serial_number');
                $data['date_of_purchase'] = date("Y-m-d", strtotime($this->input->post('dop')));

                $dataunit_details = array(
                    'sf_model_number' => trim($data['model_number']),
                    'sf_purchase_date' => trim($data['date_of_purchase']),
                    'serial_number_pic' => $data['serial_number_pic'],
                    'serial_number' => $data['serial_number']
                );
                $this->booking_model->update_booking_unit_details($booking_id, $dataunit_details);

                $booking_date = $this->input->post('booking_date');
/* Reason as reason text in Android API CALL */
                if (!$this->input->post("call_from_api")) {
                    $reason = $this->input->post('reason');
                }else{
                    $reason = SPARE_PARTS_REQUIRED; //Reason should be SPARE_PARTS_REQUIRED similar to CRM reason
                }
                //$price_tags = $this->input->post('price_tags');

                $partner_id = $this->input->post('partner_id');
                $partner_details = $this->partner_model->getpartner_details("partners.is_def_spare_required,partners.is_wh, partners.is_micro_wh, partners.is_defective_part_return_wh, partners.spare_approval_by_partner", array('partners.id' => $partner_id));

                $status = SPARE_PART_ON_APPROVAL;

                $data['date_of_request'] = $data['create_date'] = date('Y-m-d H:i:s');
                $data['remarks_by_sc'] = $this->input->post('reason_text');

                $data['booking_id'] = $booking_id;
                $data['status'] = $status;
                $data['service_center_id'] = $service_center_id;

                $parts_stock_not_found = array();
                $new_spare_id = array();
                $requested_part_name = array();
                $delivered_sp_all = array();
                
                foreach ($parts_requested as $value) {

                    $delivered_sp = array();
                    if (array_key_exists("spare_id", $data)) {
                        unset($data['spare_id']);
                    }
                    $data['quantity'] = $value['quantity'];
                    $data['parts_requested'] = $value['parts_name'];
                    $data['quantity'] = $value['quantity'];
                    if (!empty($value['parts_type'])) {
                        $data['parts_requested_type'] = $value['parts_type'];
                    } else {
                        $data['parts_requested_type'] = $value['parts_name'];
                    }

                    array_push($requested_part_name, $value['parts_name']);
                    if ($value['defective_parts']) {
                        $data['defective_parts_pic'] = $value['defective_parts'];
                    }

                    if ($value['defective_back_parts_pic']) {
                        $data['defective_back_parts_pic'] = $value['defective_back_parts_pic'];
                    }

                    if(isset($value['defect_pic']) && !empty($value['defect_pic'])){
                        $data['defect_pic'] = $value['defect_pic'];
                    }

                    if(isset($value['symptom']) && !empty($value['symptom'])){
                       $data['spare_request_symptom'] = $value['symptom']; 
                    }else{
                        $data['spare_request_symptom'] = null;
                    }

                    $data['part_warranty_status'] = $value['part_warranty_status'];

                    $data['part_requested_on_approval'] = 0;

                    if (isset($value['requested_inventory_id'])) {
                        $data['requested_inventory_id'] = $value['requested_inventory_id'];
                        $data['original_inventory_id'] = $value['requested_inventory_id'];
                    }
                    
                    if (!empty($data['requested_inventory_id']) && $value['part_warranty_status'] == SPARE_PART_IN_WARRANTY_STATUS) {
                        //$data['defective_part_required'] = $partner_details[0]['is_def_spare_required'];
                        $data['defective_part_required'] = $this->inventory_model->is_defective_part_required($booking_id, $data['requested_inventory_id'], $this->input->post('partner_id'), $data['parts_requested_type']);
                        $sc_data['internal_status'] = $reason;
                    } else if($value['part_warranty_status'] == SPARE_PART_IN_WARRANTY_STATUS) {
						$data['defective_part_required'] = 1;
						$sc_data['internal_status'] = $reason;
					} else {
                        $data['defective_part_required'] = 0;
                        $sc_data['internal_status'] = SPARE_OOW_EST_REQUESTED;
                    }


                    $data['partner_id'] = $this->input->post('partner_id');
                    $data['entity_type'] = _247AROUND_PARTNER_STRING;
                    $data['is_micro_wh'] = 0;
                    $data['defective_return_to_entity_type'] = _247AROUND_SF_STRING;
                    $data['defective_return_to_entity_id'] = DEFAULT_WAREHOUSE_ID;

                    /** search if there is any warehouse for requested spare parts
                     * if any warehouse exist then assign this spare request to that service center otherwise assign
                     * assign to respective partner. 
                     * (need to discuss) what we will do if no warehouse have this inventory.
                     */
                    $sf_state = $this->vendor_model->getVendorDetails("service_centres.state", array('service_centres.id' => $service_center_id));

                    $is_warehouse = false;
                    if (!empty($partner_details[0]['is_wh'])) {

                        $is_warehouse = TRUE;
                    } else if (!empty($partner_details[0]['is_micro_wh'])) {
                        $is_warehouse = TRUE;
                    }

                    if (!empty($is_warehouse) && $value['part_warranty_status'] == SPARE_PART_IN_WARRANTY_STATUS && !empty($value['requested_inventory_id'])) {

                        $warehouse_details = $this->get_warehouse_details(array('state' => $sf_state[0]['state'], 
                            'inventory_id' => $value['requested_inventory_id'], 
                            'model_number' => $data['model_number'], 
                            'quantity' => $data['quantity']), 
                                $partner_id);


                        if (!empty($warehouse_details) && $warehouse_details['is_micro_wh'] == 1) {
                            $data['partner_id'] = $warehouse_details['entity_id'];
                            $data['entity_type'] = $warehouse_details['entity_type'];
                            $data['defective_return_to_entity_type'] = $warehouse_details['defective_return_to_entity_type'];
                            $data['defective_return_to_entity_id'] = $warehouse_details['defective_return_to_entity_id'];
                            $data['is_micro_wh'] = $warehouse_details['is_micro_wh'];
                            $data['invoice_gst_rate'] = $warehouse_details['gst_rate'];
                            $data['parts_requested'] = $warehouse_details['part_name'];
                            $data['parts_requested_type'] = $warehouse_details['type'];
                            $data['challan_approx_value'] = round($warehouse_details['challan_approx_value'] * $data['quantity'], 2);
                            $data['requested_inventory_id'] = $warehouse_details['inventory_id'];
                            $data['shipped_inventory_id'] = $warehouse_details['inventory_id'];
                            $data['shipped_quantity'] = $data['quantity'];
                        } else {
                            $data['partner_id'] = $this->input->post('partner_id');
                            $data['entity_type'] = _247AROUND_PARTNER_STRING;
                            $data['is_micro_wh'] = 0;
                            $data['defective_return_to_entity_type'] = _247AROUND_SF_STRING;
                            $data['defective_return_to_entity_id'] = DEFAULT_WAREHOUSE_ID;
                            if (isset($value['requested_inventory_id']) && !empty($value['requested_inventory_id'])) {
                                $data['requested_inventory_id'] = $value['requested_inventory_id'];
                            }
                            if (isset($warehouse_details['challan_approx_value'])) {
                                $data['challan_approx_value'] = round($warehouse_details['challan_approx_value'] * $data['quantity'], 2);
                            }
                        }
                    } else {
                        $data['partner_id'] = $this->input->post('partner_id');
                        $data['entity_type'] = _247AROUND_PARTNER_STRING;
                        $data['is_micro_wh'] = 0;
                        $data['defective_return_to_entity_type'] = _247AROUND_SF_STRING;
                        $data['defective_return_to_entity_id'] = DEFAULT_WAREHOUSE_ID;
                    }


                    array_push($data_to_insert, $data);

                    if ($this->input->post("call_from_api")) {
                        $data['part_requested_by_engineer'] = 1;

                    }

                    if (empty($access)) {
                        if ($data['defective_return_to_entity_type'] == _247AROUND_PARTNER_STRING) {
                            $data['defective_return_to_entity_type'] = _247AROUND_SF_STRING;
                            $data['defective_return_to_entity_id'] = DEFAULT_WAREHOUSE_ID;
                        }
                    }

                    $spare_id = $this->service_centers_model->insert_data_into_spare_parts($data);
                    /* Insert Spare Tracking Details */
                    if (!empty($spare_id)) {

                        if ($this->input->post("call_from_api")) {
                        $tracking_details = array('spare_id' => $spare_id, 'action' => $data['status'], 'remarks' => trim($data['remarks_by_sc']), 'agent_id' =>$this->input->post("sc_agent_id"), 'entity_id' => $service_center_id, 'entity_type' => _247AROUND_SF_STRING);
                        }else{
                          $tracking_details = array('spare_id' => $spare_id, 'action' => $data['status'], 'remarks' => trim($data['remarks_by_sc']), 'agent_id' => $this->session->userdata("service_center_agent_id"), 'entity_id' => $this->session->userdata('service_center_id'), 'entity_type' => _247AROUND_SF_STRING);  
                        }

                        $this->service_centers_model->insert_spare_tracking_details($tracking_details);
                    }
                    $this->miscelleneous->process_booking_tat_on_spare_request($booking_id, $spare_id);
                    
                    array_push($approval_array, array('spare_id' => $spare_id, "part_warranty_status" => $value['part_warranty_status']));
                    array_push($new_spare_id, $spare_id);

                    if ($data['is_micro_wh'] == 1) {
                        $data['spare_id'] = $spare_id;
                        $data['shipped_inventory_id'] = $data['requested_inventory_id'];
                        array_push($delivered_sp, $data);
                        array_push($delivered_sp_all, $delivered_sp);
                        unset($data['spare_id']);
                    }
                }

                if (!empty($new_spare_id)) {                    
                    
                    //Send Push Notification 
                    //$receiverArray['partner'] = array($data['partner_id']);
                    $receiverArray[array_unique(array_column($data_to_insert, 'entity_type'))[0]] = array(array_unique(array_column($data_to_insert, 'partner_id'))[0]);
                    $notificationTextArray['msg'] = array(implode(",", $requested_part_name), $booking_id);
                    $this->push_notification_lib->create_and_send_push_notiifcation(SPARE_PART_REQUEST_TO_PARTNER, $receiverArray, $notificationTextArray);
                    //End Push Notification
                    if ($this->input->post("call_from_api")) {
                        if ($this->input->post("sc_agent_id")) {
                            $agent_id = $this->input->post("sc_agent_id");
                        }
                        $this->notify->insert_state_change($booking_id, SPARE_PARTS_REQUIRED, "", $data['remarks_by_sc'], $agent_id, "", "not_define", "not_define", NULL, $service_center_id, $spare_id);
                    } else {
                        $this->insert_details_in_state_change($booking_id, $reason, $data['remarks_by_sc'], "not_define", "not_define");
                    }
                    $sc_data['current_status'] = "InProcess";

                    if (!empty($booking_date)) {
                        $sc_data['current_status'] = _247AROUND_PENDING;
                        $sc_data['booking_date'] = date('Y-m-d H:i:s', strtotime($booking_date));
                        $sc_data['reschedule_reason'] = $data['remarks_by_sc'];
                        // $sc_data['internal_status'] = 'Reschedule';
                        $booking['booking_date'] = date('Y-m-d', strtotime($booking_date));
                        $this->booking_model->update_booking($booking_id, $booking);
                    }

                    $sc_data['service_center_remarks'] = date("F j") . ":- " . $data['remarks_by_sc'];
                    $sc_data['update_date'] = date("Y-m-d H:i:s");

                    $this->vendor_model->update_service_center_action($booking_id, $sc_data);
                    /**
                     * update booking internal status.
                     * @modifiedBy Ankit Rajvanshi
                     */ 
                    $booking['internal_status'] = $status;
                    $partner_status = $this->booking_utilities->get_partner_status_mapping_data(_247AROUND_PENDING, $booking['internal_status'], $partner_id, $booking_id);
                    if (!empty($partner_status)) {
                        $booking['partner_current_status'] = $partner_status[0];
                        $booking['partner_internal_status'] = $partner_status[1];
                        // if spare approval by partner is true then actor should be Partner.
                        if($partner_details[0]['spare_approval_by_partner'] == 1) {
                            $booking['actor'] = ucwords(_247AROUND_PARTNER_STRING);
                        } else {
                            $booking['actor'] = $partner_status[2];
                        }
                        $booking['next_action'] = $partner_status[3];
                    }
                    $this->booking_model->update_booking($booking_id, $booking);                    

                    if (!empty($approval_array)) {
                        foreach ($approval_array as $ap) {
                            $this->auto_approve_requested_spare($ap['spare_id'], $booking_id, $partner_id, $ap['part_warranty_status']);
                        }
                    }


                    /* 	Abhishek Auto deliver //				 */
                    foreach ($delivered_sp_all as $deliver_data) {
                        $this->auto_delivered_for_micro_wh($deliver_data, $partner_id);
                    }
                    //Update Booking Set location Starts here
                    if ($this->input->post("part_brought_at") && $this->input->post("booking_id")) {
                        $part_brought_at = $this->input->post("part_brought_at");
                        $booking_id_part = $this->input->post("booking_id");
                        $this->booking_model->update_booking($booking_id_part,array('part_brought_at' => $part_brought_at));
                    }
                    //Update Booking Set location
                    /* End auto deliver  */
                    if (!$this->input->post("call_from_api")) {
                        $userSession = array('success' => 'Booking Updated');
                        $this->session->set_userdata($userSession);
                        redirect(base_url() . "service_center/pending_booking");
                    } else {
                        $returnData['status'] = true;
                        $returnData['message'] = "Booking Updated Successfully";
                        echo json_encode($returnData);
                    }
                     
                } else { // if($status_spare){
                    if (!$this->input->post("call_from_api")) {
                        log_message('info', __FUNCTION__ . " Not update Spare parts Service_center ID: " . $service_center_id . " Data: " . print_r($data));
                        $userSession = array('error' => 'Booking Not Updated');
                        $this->session->set_userdata($userSession);
                        redirect(base_url() . "service_center/pending_booking");
                    } else {
                        $returnData['status'] = false;
                        $returnData['message'] = "Booking Not Updated";
                        echo json_encode($returnData);
                    }
                }
            } else {
                if (!$this->input->post("call_from_api")) {
                    $booking_id = urlencode(base64_encode($this->input->post('booking_id')));
                    if (!$is_file['code']) {
                        $userSession = array('error' => $is_file['message']);
                        $this->session->set_userdata($userSession);
                    }
                    $this->update_booking_status($booking_id);
                } else {
                    $returnData['status'] = false;
                    $returnData['message'] = "Image file error";
                    echo json_encode($returnData);
                }
            }
        } else {
            if (!$this->input->post("call_from_api")) {
                $booking_id = urlencode(base64_encode($this->input->post('booking_id')));
                if (!empty($is_same_parts_type['parts_requested_type'])) {
                    $userSession = array('error' => $is_same_parts_type['parts_requested_type'] . " already requested.");
                } else {
                    $userSession = array('error' => "Please select requested part type.");
                }
                $this->session->set_userdata($userSession);
                redirect(base_url() . "service_center/update_booking_status/$booking_id");
                //$this->update_booking_status($booking_id);
            } else {
                $returnData['status'] = false;
                $returnData['message'] = $is_same_parts_type['parts_requested_type'] . " already requested.";
                echo json_encode($returnData);
            }
        }
    }

    /**
     * @desc in this function, we are checking permission partner to auto approve his requested parts
     * @param type $spare_id
     * @param type $booking_id
     * @param type $partner_id
     * @param type $part_warranty_status
     * @return type
     */
    function auto_approve_requested_spare($spare_id, $booking_id, $partner_id, $part_warranty_status) {

        $access = $this->partner_model->get_partner_permission(array('partner_id' => $partner_id,
            'permission_type' => SPARE_REQUESTED_ON_APPROVAL, 'is_on' => 1));
        
        $access1 = $this->partner_model->get_partner_permission(array('partner_id' => $partner_id,
            'permission_type' => OW_SPARE_REQUESTED_ON_APPROVAL, 'is_on' => 1));

        if (!empty($access) || (!empty($access1) && $part_warranty_status == 2)) {
            $url = base_url() . 'employee/spare_parts/spare_part_on_approval/' . $spare_id . "/" . $booking_id;
            $fields = array(
                'remarks' => "Auto Approved",
                'part_warranty_status' => $part_warranty_status
            );

            //url-ify the data for the POST
            $fields_string = http_build_query($fields);

            //open connection
            $ch = curl_init($url);
                curl_setopt_array($ch, array(
                    CURLOPT_POST => TRUE,
                    CURLOPT_RETURNTRANSFER => TRUE,
                    CURLOPT_SSL_VERIFYHOST => FALSE,
                    CURLOPT_SSL_VERIFYPEER => FALSE,
                    CURLOPT_POSTFIELDS => $fields_string
                ));


            //execute post
            $result = curl_exec($ch);
            //close connection
            curl_close($ch);

            return json_decode($result, true);
        }
    }

    /**
     * @desc this function is used to auto shipped for micro warehouse
     * @param Array $delivered_sp
     * @param int $partner_id
     */
    function auto_delivered_for_micro_wh($delivered_sp, $partner_id) {
        log_message('info', __METHOD__);
        foreach ($delivered_sp as $value) {
            $data = array();
            $data['model_number_shipped'] = $value['model_number'];
            $data['parts_shipped'] = $value['parts_requested'];
            $data['shipped_parts_type'] = $value['parts_requested_type'];
            $data['shipped_date'] = $value['date_of_request'];
            // $data['shipped_date'] = $value['date_of_request'];
            $data['status'] = SPARE_SHIPPED_BY_PARTNER;
            $data['shipped_inventory_id'] = $value['requested_inventory_id'];

            $where = array('id' => $value['spare_id']);
            $this->service_centers_model->update_spare_parts($where, $data);

            $in['receiver_entity_id'] = $value['service_center_id'];
            $in['receiver_entity_type'] = _247AROUND_SF_STRING;
            $in['sender_entity_id'] = $value['service_center_id'];
            $in['sender_entity_type'] = _247AROUND_SF_STRING;
            $in['stock'] = -$value['quantity']; //-1;
            $in['booking_id'] = $value['booking_id'];
            if ($this->session->userdata('userType') == 'service_center') {
                $in['agent_id'] = $this->session->userdata('service_center_id');
                $in['agent_type'] = _247AROUND_SF_STRING;
            } else if ($this->session->userdata('userType') == 'partner') { ///// handle partner session /// abhishek///
                $in['agent_id'] = $this->session->userdata('id');  /// Partner Agent Id ///
                $in['agent_type'] = _247AROUND_PARTNER_STRING;
            } else {
                $in['agent_id'] = $this->session->userdata('agent_id');
                $in['agent_type'] = _247AROUND_SF_STRING;
            }
            $in['is_wh'] = TRUE;
            $in['inventory_id'] = $data['shipped_inventory_id'];
            $in['spare_id'] = $value['spare_id'];
            $this->miscelleneous->process_inventory_stocks($in);
            $this->acknowledge_delivered_spare_parts($value['booking_id'], $value['service_center_id'], $value['spare_id'], $partner_id, true, FALSE);
        }
    }

    /**
     * @desc this function is used to trigger mail to partner(Inventory Out of stock)
     * @param Array $parts_stock_not_found
     * @param Array $value1
     * @param Array $data
     */
    /*
      function send_out_of_stock_mail($parts_stock_not_found, $value1, $data) {
      if (!empty($parts_stock_not_found)) {
      //Getting template from Database
      $email_template = $this->booking_model->get_booking_email_template("out_of_stock_inventory");
      if (!empty($email_template)) {
      $join['service_centres'] = 'booking_details.assigned_vendor_id = service_centres.id';
      $JoinTypeTableArray['service_centres'] = 'left';
      $booking_state = $this->reusable_model->get_search_query('booking_details','service_centres.state',array('booking_details.booking_id' => $data['booking_id']),$join,NULL,NULL,NULL,$JoinTypeTableArray)->result_array();

      //$get_partner_details = $this->partner_model->getpartner_details('partners.public_name,account_manager_id,primary_contact_email,owner_email', array('partners.id' => $this->input->post('partner_id')));
      $get_partner_details = $this->partner_model->getpartner_data("partners.public_name,group_concat(distinct agent_filters.agent_id) as account_manager_id,primary_contact_email,owner_email",
      array('partners.id' => $this->input->post('partner_id'), 'agent_filters.state' => $booking_state[0]['state']),"",0,1,1,"partners.id");

      $am_email = "";
      if (!empty($get_partner_details[0]['account_manager_id'])) {
      //$am_email = $this->employee_model->getemployeefromid($get_partner_details[0]['account_manager_id'])[0]['official_email'];
      $am_email = $this->employee_model->getemployeeMailFromID($get_partner_details[0]['account_manager_id'])[0]['official_email'];
      }

      $this->load->library('table');
      $template = array(
      'table_open' => '<table border="1" cellpadding="2" cellspacing="1" class="mytable">'
      );

      $this->table->set_template($template);

      $this->table->set_heading(array('Model Number', 'Part Type', 'Part Name'));
      foreach ($parts_stock_not_found as $value) {
      $this->table->add_row($value['model_number'], $value['part_type'], $value['part_name']);
      }
      $body_msg = $this->table->generate();
      $to = $get_partner_details[0]['primary_contact_email'] . "," . $get_partner_details[0]['owner_email'];
      $cc = $email_template[3] . "," . $am_email;
      $subject = vsprintf($email_template[4], array($data['model_number'], $data['parts_requested']));
      $emailBody = vsprintf($email_template[0], $body_msg);
      $this->notify->sendEmail($email_template[2], $to, $cc, '', $subject, $emailBody, "", 'out_of_stock_inventory');
      }
      }
      }
     */

    function upload_defective_spare_pic() {
        $allowedExts = array("png", "jpg", "jpeg", "JPG", "JPEG", "PNG", "PDF", "pdf");
        $booking_id = $this->input->post("booking_id");
        $exist_courier_image = $this->input->post("exist_courier_image");

        // if (!empty($exist_courier_image)) {
        //    $_POST['sp_parts'] = $exist_courier_image;
        //    return true;
        // } else {
        $defective_courier_receipt = $this->miscelleneous->upload_file_to_s3($_FILES["defective_courier_receipt"], "defective_courier_receipt", $allowedExts, $booking_id, "misc-images", "sp_parts");
        if ($defective_courier_receipt) {
            return true;
        } else {
            $this->form_validation->set_message('upload_defective_spare_pic', 'File size or file type is not supported. Allowed extentions are "png", "jpg", "jpeg" and "pdf". '
                    . 'Maximum file size is 5 MB.');
            return false;
        }
        //}
    }

    /**
     * @desc: This is used to update acknowledge date by SF
     * @param String $booking_id
     */
    function acknowledge_delivered_spare_parts($booking_id, $service_center_id, $id, $partner_id, $autoAck = false, $flag = TRUE) {
        log_message('info', __FUNCTION__ . " Booking ID: " . $booking_id . ' service_center_id: ' . $service_center_id . ' id: ' . $id);
        if (empty($autoAck)) {
            $this->checkUserSession();
        }
        if (!empty($booking_id)) {
            $update_service_center_bokking_action = true;
            $get_all_other_booking_pending = $this->service_centers_model->get_spare_part_pending_for_acknowledge($booking_id, $id);
            if(!empty($get_all_other_booking_pending)){
                $update_service_center_bokking_action = false;
            }
            $where = array('id' => $id);
            $sp_data['service_center_id'] = $service_center_id;
            $sp_data['acknowledge_date'] = date('Y-m-d');
            $sp_data['status'] = SPARE_DELIVERED_TO_SF;
            if (!empty($autoAck)) {
                $sp_data['auto_acknowledeged'] = 1;
            } else {
                $sp_data['auto_acknowledeged'] = 0;
            }
            $actor = $next_action = NULL;
            //Update Spare Parts table
            $ss = $this->service_centers_model->update_spare_parts($where, $sp_data);
           
            $pre_sp = $this->partner_model->get_spare_parts_by_any("spare_parts_details.id, awb_by_partner", array('spare_parts_details.id' => $id));
            if(!empty($pre_sp) && !empty($pre_sp[0]['awb_by_partner'])){
                $this->inventory_model->update_courier_company_invoice_details(array('awb_number' => $pre_sp[0]['awb_by_partner'], 'delivered_date IS NULL' => NULL), array('delivered_date' => date('Y-m-d H:i:s')));
            }

            if ($ss) { //if($ss){
                $is_requested = $this->partner_model->get_spare_parts_by_any("spare_parts_details.id, status, booking_id", array('booking_id' => $booking_id, 'status IN ("' . SPARE_SHIPPED_BY_PARTNER . '", "'
                    . SPARE_PARTS_REQUESTED . '", "' . ESTIMATE_APPROVED_BY_CUSTOMER . '", "' . SPARE_OOW_EST_GIVEN . '", "' . SPARE_OOW_EST_REQUESTED . '", "' . SPARE_PART_ON_APPROVAL . '", "' . SPARE_OOW_SHIPPED . '") ' => NULL));
                if ($this->session->userdata('service_center_id')) {
                    $agent_id = $this->session->userdata('service_center_agent_id');
                    $entity_id =  $sc_entity_id = $this->session->userdata('service_center_id');
                    $entity_type = _247AROUND_SF_STRING;
                    $p_entity_id = NULL;
                } else if ($this->session->userdata('partner_id')) {
                    $agent_id = _247AROUND_DEFAULT_AGENT;
                    $sc_entity_id = NULL;
                    $entity_id = $p_entity_id = _247AROUND;
                    $entity_type = _247AROUND_PARTNER_STRING;
                } else {
                    $agent_id = _247AROUND_DEFAULT_AGENT;
                    $entity_id = $p_entity_id = _247AROUND;
                    $sc_entity_id = NULL;
                    $entity_type = _247AROUND_EMPLOYEE_STRING;
                }
                
                /* Insert Spare Tracking Details */
                    if (!empty($id)) {
                        $tracking_details = array('spare_id' => $id, 'action' => $sp_data['status'], 'remarks' => 'SF acknowledged to receive spare parts', 'agent_id' => $agent_id, 'entity_id' => $entity_id, 'entity_type' => $entity_type);
                        $this->service_centers_model->insert_spare_tracking_details($tracking_details);
                    }
                
                if (empty($is_requested)) {
                    $booking['booking_date'] = date('Y-m-d', strtotime('+1 days'));
                    $booking['update_date'] = date("Y-m-d H:i:s");
                    $booking['internal_status'] = SPARE_DELIVERED_TO_SF;

                    $partner_status = $this->booking_utilities->get_partner_status_mapping_data(_247AROUND_PENDING, SPARE_DELIVERED_TO_SF, $partner_id, $booking_id);
                    $actor = $next_action = 'not_define';
                    if (!empty($partner_status)) {
                        $booking['partner_current_status'] = $partner_status[0];
                        $booking['partner_internal_status'] = $partner_status[1];
                        $actor = $booking['actor'] = $partner_status[2];
                        $next_action = $booking['next_action'] = $partner_status[3];
                    }
                    if($update_service_center_bokking_action){
                        $b_status = $this->booking_model->update_booking($booking_id, $booking);
                    }else{
                        $b_status = 1;
                    }

                    if ($b_status) {
                        
                        if($update_service_center_bokking_action){
                        $this->notify->insert_state_change($booking_id, SPARE_DELIVERED_TO_SF, _247AROUND_PENDING, "SF acknowledged to receive spare parts", $agent_id, $agent_id, $actor, $next_action, $p_entity_id, $sc_entity_id, $id);

                        $sc_data['current_status'] = _247AROUND_PENDING;
                        $sc_data['internal_status'] = SPARE_DELIVERED_TO_SF;
                        $sc_data['update_date'] = date("Y-m-d H:i:s");
                        $this->vendor_model->update_service_center_action($booking_id, $sc_data);
                        }
                        if (empty($autoAck)) {
                            $this->miscelleneous->send_spare_delivered_sms_to_customer($id, $booking_id);
                        }
                         
                        if ($this->session->userdata('service_center_id')) {
                            $userSession = array('success' => 'Booking Updated');
                            $this->session->set_userdata($userSession);
                        }
                        $cb_url = base_url() . "employee/do_background_process/send_request_for_partner_cb/" . $booking_id;
                        $pcb = array();
                        $this->asynchronous_lib->do_background_process($cb_url, $pcb);
                    } else {//if ($b_status) {
                        log_message('info', __FUNCTION__ . " Booking is not updated. Service_center ID: "
                                . $service_center_id .
                                "Booking ID: " . $booking_id);
                        if ($this->session->userdata('service_center_id')) {
                            $userSession = array('success' => 'Please Booking is not updated');
                            $this->session->set_userdata($userSession);
                        }
                    }
                } else {

                    if($update_service_center_bokking_action){
                    $this->notify->insert_state_change($booking_id, SPARE_DELIVERED_TO_SF, _247AROUND_PENDING, "SF acknowledged to receive spare parts", $agent_id, $agent_id, $actor, $next_action, $p_entity_id, $sc_entity_id);
                    }
                    if ($this->session->userdata('service_center_id')) {
                        $userSession = array('success' => 'Booking Updated');
                        $this->session->set_userdata($userSession);
                    }
                    $cb_url = base_url() . "employee/do_background_process/send_request_for_partner_cb/" . $booking_id;
                    $pcb = array();
                    $this->asynchronous_lib->do_background_process($cb_url, $pcb);
                }
            } else {
                log_message('info', __FUNCTION__ . " Spare parts ack date is not updated Service_center ID: "
                        . $service_center_id .
                        "Booking ID: " . $booking_id);
                if ($this->session->userdata('service_center_id')) {
                    $userSession = array('error' => 'Booking is not updated');
                    $this->session->set_userdata($userSession);
                }
            }
        }
        log_message('info', __FUNCTION__ . " Exit Service_center ID: " . $service_center_id);
        if ($this->session->userdata('service_center_id')) {
            if ($flag == TRUE) {
                redirect(base_url() . "service_center/pending_booking");
            }
        }
    }

    /**
     * @desc: This method called by Cron.
     * This method is used to convert Shipped spare part booking into Pending
     */
    function get_booking_id_to_convert_pending_for_spare_parts() {
        $data = $this->service_centers_model->get_booking_id_to_convert_pending_for_spare_parts();
        foreach ($data as $value) {
            $this->acknowledge_delivered_spare_parts($value['booking_id'], $value['service_center_id'], $value['id'], $value['partner_id'], TRUE, FALSE);
        }
    }

    /**
     * @desc: This method is used to display whose booking updated by SC.
     */
    function convert_updated_booking_to_pending() {
//        $this->service_centers_model->get_updated_booking_to_convert_pending();
//        // Inserting values in scheduler tasks log
//        $this->reporting_utils->insert_scheduler_tasks_log(__FUNCTION__);
    }

    /**
     * @desc: This is used to get search form in SC CRM
     * params: void
     * return: View form to find user
     */
    function get_search_form() {
        log_message('info', __FUNCTION__ . "  Service_center ID: " . $this->session->userdata('service_center_id'));
        $this->checkUserSession();
        $this->load->view('service_centers/header');
        $this->load->view('service_centers/search_form');
    }

    /**
     * @desc: SF search booking by Phone number or Booking id
     */
    function search() {
        log_message('info', __FUNCTION__ . "  Service_center ID: " . $this->session->userdata('service_center_id'));
        $this->checkUserSession();
        $searched_text = trim($this->input->post('searched_text'));
        $service_center_id = $this->session->userdata('service_center_id');
        $data['data'] = $this->service_centers_model->search_booking_history(trim($searched_text), $service_center_id,'booking_details.create_date desc');

        if (!empty($data['data'])) {
            $this->load->view('service_centers/header');
            $this->load->view('service_centers/bookinghistory', $data);
        } else {
            //if user not found set error session data

            $output = "Booking Not Found";
            $userSession = array('error' => $output);
            $this->session->set_userdata($userSession);

            redirect(base_url() . 'service_center/pending_booking');
        }
    }

    /**
     * @Desc: This function is used to download Pending Bookings Excel list
     * params: void
     * @return: void
     * 
     */
    function download_sf_pending_bookings_list_excel() {
        log_message('info', __FUNCTION__);
        //Getting Logged SF details
        $service_center_id = $this->session->userdata('service_center_id');
        //Getting Pending bookings for service center id
        //$bookings = $this->service_centers_model->pending_booking($service_center_id, "");
        $bookings = $this->service_centers_model->pending_bookings_sf_excel($service_center_id);
        $booking_details = json_decode(json_encode($bookings), true);
        $template = 'SF-Pending-Bookings-List-Template.xlsx';
        //set absolute path to directory with template files
        $templateDir = __DIR__ . "/../excel-templates/";
        //set config for report
        $config = array(
            'template' => $template,
            'templateDir' => $templateDir
        );
        //load template
        if (ob_get_length() > 0) {
            ob_end_clean();
        }
        $R = new PHPReport($config);

        $R->load(array(
            'id' => 'bookings',
            'repeat' => TRUE,
            'data' => $booking_details
        ));

        $output_file_dir = TMP_FOLDER;
        $output_file = "SF-" . $service_center_id . "-Pending-Bookings-List-" . date('y-m-d');
        $output_file_name = $output_file . ".xls";
        $output_file_excel = $output_file_dir . $output_file_name;
        $R->render('excel2003', $output_file_excel);

        //Downloading File
        if (file_exists($output_file_excel)) {

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header("Content-Disposition: attachment; filename=\"$output_file_name\"");
            readfile($output_file_excel);
            // Delete the file from temporary folder
            if(file_exists($output_file_excel)){
                unlink($output_file_excel);
            }
            exit;
        }
    }

    /**
     * @Desc: This function is used to download the SC charges excel
     * @params: void
     * @return: void
     * 
     */
    function download_sf_charges_excel() {
        log_message('info', __FUNCTION__ . ' Used by :' . $this->session->userdata('service_center_name'));
        $this->checkUserSession();
        //Getting SC ID from session
        $service_center_id = $this->session->userdata('service_center_id');
        if (!empty($service_center_id)) {
            //Getting SF Details
            $sc_details = $this->vendor_model->getVendorContact($service_center_id);
            $filter_option = $this->service_centre_charges_model->get_service_centre_charges_by_any(array('tax_rates.state' => $sc_details[0]['state'], 'length' => -1), 'distinct services.id,services.services as product');
//            $data['category'] = array_unique(array_column($filter_option, 'category'));
//            $data['capacity'] = array_unique(array_column($filter_option, 'capacity'));
//            $data['service_category'] = array_unique(array_column($filter_option, 'service_category'));
            $data['appliance'] = array_unique(array_column($filter_option, 'product', 'id'));
            $this->load->view('service_centers/header');
            $this->load->view('service_centers/download_sf_charges_excel', $data);
        } else {
            echo 'Sorry, Session has expired, please log in again!';
        }
    }

    /**
     * @Desc: This function is used to show vendor details
     * @params: void
     * @return: void
     * 
     */
    function show_vendor_details() {
        //$this->checkUserSession();
        log_message('info', __FUNCTION__ . ' Used by :' . $this->session->userdata('service_center_name'));
        $id = $this->session->userdata('service_center_id');
        if (!empty($id)) {

            $query = $this->vendor_model->viewvendor($id);

            $results['services'] = $this->vendor_model->selectservice();
            $results['brands'] = $this->vendor_model->selectbrand();
            $results['select_state'] = $this->vendor_model->get_allstates();
            $results['employee_rm'] = $this->employee_model->get_rm_details();

            $appliances = $query[0]['appliances'];
            $selected_appliance_list = explode(",", $appliances);
            $brands = $query[0]['brands'];
            $selected_brands_list = explode(",", $brands);

            $rm = $this->vendor_model->get_rm_sf_relation_by_sf_id($id);

            $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            $non_working_days = $query[0]['non_working_days'];
            $selected_non_working_days = explode(",", $non_working_days);
            $this->load->view('service_centers/header');

            $this->load->view('service_centers/show_vendor_details', array('query' => $query, 'results' => $results, 'selected_brands_list'
                => $selected_brands_list, 'selected_appliance_list' => $selected_appliance_list,
                'days' => $days, 'selected_non_working_days' => $selected_non_working_days, 'rm' => $rm));
        } else {
            echo 'Sorry, Session has Expired, Please Log In Again!';
        }
    }

    /**
     * @desc: This method is used to display list of booking which need to be ship defective parts by SF
     * @param Integer $offset
     */
    function get_defective_parts_booking($offset = 0) {
        $this->checkUserSession();
        log_message('info', __FUNCTION__ . ' Used by :' . $this->session->userdata('service_center_name'));

        $data['partner_on_saas'] = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        $data['courier_details'] = $this->inventory_model->get_courier_services('*');
        $this->load->view('service_centers/header');
        $this->load->view('service_centers/defective_parts', $data);
    }

    function get_defective_parts_pending_bookings($offset = 0) {
        $this->checkUserSession();
        log_message('info', __FUNCTION__ . ' Used by :' . $this->session->userdata('service_center_name'));
        $service_center_id = $this->session->userdata('service_center_id');

        $where = array(
            "spare_parts_details.defective_part_required" => 1, // no need to check removed coloumn //
            "spare_parts_details.service_center_id" => $service_center_id,
            "status IN ('" . DEFECTIVE_PARTS_PENDING . "', '" . DEFECTIVE_PARTS_REJECTED_BY_WAREHOUSE . "', '" . OK_PART_TO_BE_SHIPPED . "', '" . OK_PARTS_REJECTED_BY_WAREHOUSE . "')  " => NULL,
            "spare_parts_details.consumed_part_status_id is not null" => NULL
        );

        $select = "booking_details.service_center_closed_date,booking_details.booking_primary_contact_no as mobile, parts_shipped, "
                . " spare_parts_details.booking_id,booking_details.partner_id as booking_partner_id, users.name, "
                . " sf_challan_file as challan_file, "
                . " remarks_defective_part_by_partner, "
                . " remarks_by_partner, spare_parts_details.partner_id,spare_parts_details.service_center_id,spare_parts_details.defective_return_to_entity_id,spare_parts_details.entity_type,spare_parts_details.courier_rejection_remarks,"
                . " spare_parts_details.id,spare_parts_details.shipped_quantity,spare_parts_details.challan_approx_value,spare_parts_details.remarks_defective_part_by_wh,spare_parts_details.rejected_defective_part_pic_by_wh ,i.part_number, spare_consumption_status.consumed_status,  spare_consumption_status.is_consumed";

        $group_by = "spare_parts_details.id";
        $order_by = "status = '" . DEFECTIVE_PARTS_REJECTED_BY_WAREHOUSE . "', spare_parts_details.booking_id ASC";


        $config['base_url'] = base_url() . 'service_center/get_defective_parts_booking';
        $config['total_rows'] = $this->service_centers_model->count_spare_parts_booking($where, $select);

        $config['per_page'] = 50;
        $config['uri_segment'] = 3;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['count'] = $config['total_rows'];
        $data['spare_parts'] = $this->service_centers_model->get_spare_parts_booking($where, $select, $group_by, $order_by, $offset, $config['per_page']);
        $data['partner_on_saas'] = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        $data['courier_details'] = $this->inventory_model->get_courier_services('*');
        //$this->load->view('service_centers/header');
        $this->load->view('service_centers/defective_ok_part_to_be_shipped', $data);
        
    }

    function update_defective_parts_pending_bookings($offset = 0) {
        $this->checkUserSession();
        log_message('info', __FUNCTION__ . ' Used by :' . $this->session->userdata('service_center_name'));
        $service_center_id = $this->session->userdata('service_center_id');

        $where = array(
            "spare_parts_details.defective_part_required" => 1, // no need to check removed coloumn //
            "spare_parts_details.service_center_id" => $service_center_id,
            "status IN ('" . DEFECTIVE_PARTS_PENDING . "', '" . DEFECTIVE_PARTS_REJECTED_BY_WAREHOUSE . "', '" . OK_PART_TO_BE_SHIPPED . "', '" . OK_PARTS_REJECTED_BY_WAREHOUSE . "')  " => NULL,
            "spare_parts_details.consumed_part_status_id is null" => NULL
        );

        $select = "booking_details.service_center_closed_date,booking_details.booking_primary_contact_no as mobile, parts_shipped, "
                . " spare_parts_details.booking_id,booking_details.partner_id as booking_partner_id, users.name, "
                . " sf_challan_file as challan_file, "
                . " remarks_defective_part_by_partner, "
                . " remarks_by_partner, spare_parts_details.partner_id,spare_parts_details.service_center_id,spare_parts_details.defective_return_to_entity_id,spare_parts_details.entity_type,"
                . " spare_parts_details.id,spare_parts_details.shipped_quantity,spare_parts_details.challan_approx_value,spare_parts_details.remarks_defective_part_by_wh ,i.part_number, spare_consumption_status.consumed_status,  spare_consumption_status.is_consumed";

        $group_by = "spare_parts_details.id";
        $order_by = "status = '" . DEFECTIVE_PARTS_REJECTED_BY_WAREHOUSE . "', spare_parts_details.booking_id ASC";


//        $config['base_url'] = base_url() . 'service_center/get_defective_parts_booking';
//        $config['total_rows'] = $this->service_centers_model->count_spare_parts_booking($where, $select);
//
//        $config['per_page'] = 50;
//        $config['uri_segment'] = 3;
//        $config['first_link'] = 'First';
//        $config['last_link'] = 'Last';
//        $this->pagination->initialize($config);
//        $data['links'] = $this->pagination->create_links();

        //$data['count'] = $config['total_rows'];
        //$data['spare_parts'] = $this->service_centers_model->get_spare_parts_booking($where, $select, $group_by, $order_by, $offset, $config['per_page']);
        $data['spare_parts'] = $this->service_centers_model->get_spare_parts_booking($where, $select, $group_by, $order_by);
        $data['partner_on_saas'] = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        $data['courier_details'] = $this->inventory_model->get_courier_services('*');
        $data['spare_consumed_status'] = $this->reusable_model->get_search_result_data('spare_consumption_status', 'id, consumed_status,status_description,tag', ['active' => 1, "tag <> '".PART_NOT_RECEIVED_TAG."'" => NULL], NULL, NULL, ['consumed_status' => SORT_ASC], NULL, NULL);

        //$this->load->view('service_centers/header');
        $this->load->view('service_centers/update_defective_ok_part_to_be_shipped', $data);
        
    }
    
    /**
     * @desc: This method is used to display list of booking which received by Partner
     * @param Integer $offset
     */
    function get_approved_defective_parts_booking($offset = 0) {
        $this->checkUserSession();
        log_message('info', __FUNCTION__ . ' Used by :' . $this->session->userdata('service_center_name'));
        $service_center_id = $this->session->userdata('service_center_id');
        $where = "spare_parts_details.service_center_id = '" . $service_center_id . "' "
                . " AND (approved_defective_parts_by_partner = '1' or defective_part_received_by_wh = 1 ) ";

        $config['base_url'] = base_url() . 'service_center/get_approved_defective_parts_booking';
        $total_rows = $this->partner_model->get_spare_parts_booking_list($where, false, false, false);
        $config['total_rows'] = $total_rows[0]['total_rows'];

        $config['per_page'] = 50;
        $config['uri_segment'] = 3;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['count'] = $config['total_rows'];
        $data['spare_parts'] = $this->partner_model->get_spare_parts_booking_list($where, $offset, $config['per_page'], true);

        $this->load->view('service_centers/header');
        $this->load->view('service_centers/approved_defective_parts', $data);
    }

    /**
     * @desc: This method is used to load update form(defective shipped parts)
     * @param String $sp_id
     */
    function update_defective_parts($sp_id) {
        $this->checkUserSession();
        if (!empty($sp_id) || $sp_id != '' || $sp_id != 0) {
            log_message('info', __FUNCTION__ . ' Used by :' . $this->session->userdata('service_center_name'));

            $service_center_id = $this->session->userdata('service_center_id');

            $where = "spare_parts_details.service_center_id = '" . $service_center_id . "'  "
                    . " AND spare_parts_details.id = '" . $sp_id . "' AND spare_parts_details.defective_part_required = 1 "
                    . " AND spare_parts_details.status IN ('" . DEFECTIVE_PARTS_PENDING . "', '" . DEFECTIVE_PARTS_REJECTED_BY_WAREHOUSE . "', '" . OK_PART_TO_BE_SHIPPED . "', '" . OK_PARTS_REJECTED_BY_WAREHOUSE . "', '" . COURIER_LOST . "') ";

            $data['spare_parts'] = $this->partner_model->get_spare_parts_booking($where);
            //     $data['courier_info'] = $this->inventory_model->getCourierInfo();
            $data['courier_details'] = $this->inventory_model->get_courier_services('*');
            $data['spare_consumed_status'] = $this->reusable_model->get_search_result_data('spare_consumption_status', 'id, consumed_status,status_description,tag', ['active' => 1, "tag <> '".PART_NOT_RECEIVED_TAG."'" => NULL], NULL, NULL, ['consumed_status' => SORT_ASC], NULL, NULL);
            if (!empty($data['spare_parts'])) {
                $this->load->view('service_centers/header');
                $this->load->view('service_centers/update_defective_spare_parts_form', $data);
            } else {
                echo "Please Try Again Later";
            }
        } else {
            redirect(base_url() . "service_center/get_defective_parts_booking");
        }
    }

    function do_multiple_spare_shipping() {


        $sp_ids = explode(',', $_POST['sp_ids']);
        $count_spare = count($sp_ids);

        if (!empty($_POST['courier_boxes_weight_flag'] > 0)) {

            $_POST['courier_boxes_weight_flag'] = ($count_spare + $_POST['courier_boxes_weight_flag']);
        }
        $service_center_id = 0;
        if ($this->session->userdata('userType') == 'service_center') {
            $service_center_id = $this->session->userdata('service_center_id');
        } else {
            $res = array(
                'error' => true,
                'errorMessage' => 'Authentication failure: Request origin is not a Service Centre.'
            );
            echo json_encode($res);
            die;
        }
        foreach ($sp_ids as $key => $value) {

            $where = "spare_parts_details.service_center_id = '" . $service_center_id . "'  "
                    . " AND spare_parts_details.id = '" . $value . "' AND spare_parts_details.defective_part_required = 1 "
                    . " AND spare_parts_details.status IN ('" . DEFECTIVE_PARTS_PENDING . "', '" . DEFECTIVE_PARTS_REJECTED_BY_WAREHOUSE . "', '" . OK_PART_TO_BE_SHIPPED . "', '" . OK_PARTS_REJECTED_BY_WAREHOUSE . "') ";

            $spare_part = $this->partner_model->get_spare_parts_booking($where);
            if (!empty($spare_part)) {

                $_POST['sf_id'] = $spare_part[0]['service_center_id'];
                $_POST['booking_partner_id'] = $spare_part[0]['booking_partner_id'];
                $_POST['bulk_courier'] = TRUE;
                $_POST['booking_id'] = $spare_part[0]['booking_id'];
                $_POST['user_name'] = $spare_part[0]['name'];
                $_POST['mobile'] = $spare_part[0]['booking_primary_contact_no'];
                $_POST['defective_return_to_entity_type'] = $spare_part[0]['defective_return_to_entity_type'];
                $_POST['defective_return_to_entity_id'] = $spare_part[0]['defective_return_to_entity_id'];
                $_POST['shipped_inventory_id'] = $spare_part[0]['shipped_inventory_id'];

                $_POST['defective_part_shipped'] = array();
                $_POST['partner_challan_number'] = array();
                $_POST['challan_approx_value'] = array();
                $_POST['parts_requested'] = array();
                $_POST['no_redirect_flag'] = TRUE;
                $_POST['defective_part_shipped'][$value] = $spare_part[0]['parts_shipped'];
                $_POST['partner_challan_number'][$value] = $spare_part[0]['partner_challan_number'];
                $_POST['challan_approx_value'][$value] = $spare_part[0]['challan_approx_value'];
                $_POST['parts_requested'][$value] = $spare_part[0]['parts_requested'];
                if (!isset($_POST['courier_boxes_weight_flag']) || empty($_POST['courier_boxes_weight_flag'])) {
                    $_POST['courier_boxes_weight_flag'] = $count_spare;
                }

                $this->process_update_defective_parts($value);
            }
        }

        $res = array(
            "error" => false
        );
        echo json_encode($res);
        die;
    }

    /**
     * @desc: Process to update defective spare parts
     * @param type $sp_id
     */
    function process_update_defective_parts($sp_id) {
        log_message('info', __FUNCTION__ . ' sf_id: ' . $this->session->userdata('service_center_id') . " Spare id " . $sp_id, true);
        $this->checkUserSession();
        log_message('info', __FUNCTION__ . ' Used by :' . $this->session->userdata('service_center_name'));
        $this->form_validation->set_rules('remarks_defective_part', 'Remarks', 'trim|required');
        $this->form_validation->set_rules('booking_id', 'Booking ID', 'trim|required');
        $this->form_validation->set_rules('courier_name_by_sf', 'Courier Name', 'trim|required');
        $this->form_validation->set_rules('awb_by_sf', 'AWB', 'trim|required');
        $this->form_validation->set_rules('defective_part_shipped_date', 'AWB', 'trim|required|callback_validate_shipped_date');
        $this->form_validation->set_message('validate_shipped_date', 'Shipped date cannot be older than 2 days.');
        $this->form_validation->set_rules('courier_charges_by_sf', 'Courier Charges', 'trim|required');
        $this->form_validation->set_rules('defective_courier_receipt', 'Courier Invoice', 'callback_upload_defective_spare_pic');

        if ($this->form_validation->run() == FALSE) {
            log_message('info', __FUNCTION__ . '=> Form Validation is not updated by Service center ' . $this->session->userdata('service_center_name') .
                    " Spare id " . $sp_id . " Data" . print_r($this->input->post(), true));
            $this->update_defective_parts($sp_id);
            if (isset($_POST['no_redirect_flag']) && !empty($_POST['no_redirect_flag'])) {
                $errors = validation_errors();
                $res = array(
                    'error' => true,
                    'errorMessage' => $errors
                );
                echo json_encode($res);
                die;
            }
        } else {
          
            $spare_part_detail = $this->reusable_model->get_search_result_data('spare_parts_details', '*', ['id' => $sp_id], NULL, NULL, NULL, NULL, NULL)[0];

            $defective_courier_receipt = $this->input->post("sp_parts");
            $spare_details = $this->partner_model->get_spare_parts_by_any("spare_parts_details.*", array('spare_parts_details.id' => $sp_id));

            if (!empty($defective_courier_receipt)) {
                if (!empty($sp_id)) {
                    $data['defective_courier_receipt'] = $this->input->post("sp_parts");
                    $awb = $this->input->post('awb_by_sf');
                    $service_center_id = $this->session->userdata('service_center_id');
                    $defective_part_shipped = $this->input->post('defective_part_shipped');
                    $data['remarks_defective_part_by_sf'] = $this->input->post('remarks_defective_part');
                    $data['defective_part_shipped_date'] = $this->input->post('defective_part_shipped_date');
                    $data['defective_courier_receipt'] = $defective_courier_receipt;
                    $data['approved_defective_parts_by_admin'] = 0;
                    /**
                     * @modifiedBy Ankit Rajvanshi
                     */
                    // Fetch spare details of $spare_id.
                    $is_spare_consumed = $this->reusable_model->get_search_result_data('spare_consumption_status', '*', ['id' => $spare_part_detail['consumed_part_status_id']], NULL, NULL, NULL, NULL, NULL)[0]['is_consumed'];
                    $data['status'] = DEFECTIVE_PARTS_SHIPPED;
                    if(!empty($is_spare_consumed) && $is_spare_consumed == 1) {
                        $data['status'] = DEFECTIVE_PARTS_SHIPPED;
                    } else {
                        $data['status'] = OK_PARTS_SHIPPED;
                    }                    
                    
                    $data['courier_name_by_sf'] = $this->input->post('courier_name_by_sf');
                    $data['defective_part_shipped'] = $defective_part_shipped[$sp_id];
                    $is_p = $this->booking_utilities->check_feature_enable_or_not(AUTO_APPROVE_DEFECTIVE_PARTS_COURIER_CHARGES);
                    if (!empty($is_p)) {
                        $data['approved_defective_parts_by_admin'] = 1;
                    }

                    $booking_id = $this->input->post('booking_id');
                    $booking_details = $this->booking_model->get_booking_details('*',['booking_id' => $booking_id])[0];
                    $quantity = $spare_details[0]['shipped_quantity'];
                    $partner_id = $this->input->post('booking_partner_id');
                    $data['awb_by_sf'] = $awb;
                    $kilo_gram = $this->input->post('defective_parts_shipped_kg') ? : '0';
                    $gram = $this->input->post('defective_parts_shipped_gram') ? : '00';

                    $billable_weight = $kilo_gram . "." . $gram;
                    $courier_boxes_weight_flag = $this->input->post('courier_boxes_weight_flag');

                    if ($courier_boxes_weight_flag > 0) {

                        if (isset($_POST['bulk_courier']) && !empty($_POST['bulk_courier'])) {
                            $pricecourier = round(($this->input->post('courier_charges_by_sf') / ($courier_boxes_weight_flag)), 2);
                        } else {
                            $pricecourier = round(($this->input->post('courier_charges_by_sf') / ($courier_boxes_weight_flag + 1)), 2);
                        }

                        $this->service_centers_model->update_spare_parts(array('awb_by_sf' => $awb, 'status != "' . _247AROUND_CANCELLED . '" ' => NULL), array('courier_charges_by_sf' => $pricecourier));
                    } else {
                        $pricecourier = $this->input->post('courier_charges_by_sf');
                    }

                    $data['courier_charges_by_sf'] = $pricecourier;
                    $this->service_centers_model->update_spare_parts(array('id' => $sp_id), $data);
                    /* Insert Spare Tracking Details */
                    if (!empty($sp_id)) {
                        $tracking_details = array('spare_id' => $sp_id, 'action' => $data['status'], 'remarks' => trim($data['remarks_defective_part_by_sf']), 'agent_id' => $this->session->userdata("service_center_agent_id"), 'entity_id' => $this->session->userdata('service_center_id'), 'entity_type' => _247AROUND_SF_STRING);
                        $this->service_centers_model->insert_spare_tracking_details($tracking_details);
                    }

                    $sc_details = $this->vendor_model->getVendorDetails("district, state", array('service_centres.id' => $this->session->userdata('service_center_id')));
                    $from_city = $sc_details[0]['district'];
                    $from_state = $sc_details[0]['state'];
                    
                    if(($this->input->post('defective_return_to_entity_type') == _247AROUND_PARTNER_STRING)) {
                        $partner_details = $this->partner_model->getpartner($this->input->post("defective_return_to_entity_id"));
                        $to_city = $partner_details[0]['district'];
                        $to_state = $partner_details[0]['state'];
                    }
                    else {
                        $vendor_details = $this->vendor_model->getVendorDetails("district, state", array('service_centres.id' => $this->input->post("defective_return_to_entity_id")));
                        $to_city = $vendor_details[0]['district'];
                        $to_state = $vendor_details[0]['state'];
                    }
                    
                    if ($courier_boxes_weight_flag == 0) {
                        $exist_courier_details = $this->inventory_model->get_generic_table_details('courier_company_invoice_details', 'courier_company_invoice_details.id,courier_company_invoice_details.awb_number', array('awb_number' => $awb), array());
                        if (empty($exist_courier_details)) {
                            $awb_data = array(
                                'awb_number' => trim($awb),
                                'company_name' => trim($this->input->post('courier_name_by_sf')),
                                'partner_id' => $partner_id,
                                'courier_charge' => trim($this->input->post('courier_charges_by_sf')), //
                                'box_count' => trim($this->input->post('defective_parts_shipped_boxes_count')), //defective_parts_shipped_gram
                                'billable_weight' => trim($billable_weight),
                                'actual_weight' => trim($billable_weight),
                                'basic_billed_charge_to_partner' => trim($this->input->post('courier_charges_by_sf')),
                                'booking_id' => trim($this->input->post('booking_id')),
                                'courier_invoice_file' => trim($defective_courier_receipt),
                                'shippment_date' => trim($this->input->post('defective_part_shipped_date')), //defective_part_shipped_date
                                'created_by' => 2,
                                'is_exist' => 0,
                                'sender_city' => $from_city,
                                'receiver_city' => $to_city,
                                'sender_state' => $from_state,
                                'receiver_state' => $to_state
                            );

                            $this->service_centers_model->insert_into_awb_details($awb_data);
                        } else {
                            $awb_data = array(
                                'company_name' => trim($this->input->post('courier_name_by_sf')),
                                'partner_id' => $partner_id,
                                'box_count' => trim($this->input->post('defective_parts_shipped_boxes_count')), //defective_parts_shipped_gram
                                'billable_weight' => trim($billable_weight),
                                'actual_weight' => trim($billable_weight),
                                'basic_billed_charge_to_partner' => trim($this->input->post('courier_charges_by_sf')),
                                'courier_invoice_file' => trim($defective_courier_receipt),
                                'shippment_date' => trim($this->input->post('defective_part_shipped_date')), //defective_part_shipped_date
                                'created_by' => 2,
                                'is_exist' => 0
                            );

                            $this->service_centers_model->update_awb_details($awb_data, trim($awb));
                        }
                    } else {
                        $exist_courier_details = $this->inventory_model->get_generic_table_details('courier_company_invoice_details', 'courier_company_invoice_details.id,courier_company_invoice_details.awb_number', array('awb_number' => $awb), array());
                        if (empty($exist_courier_details)) {
                            $awb_data = array(
                                'awb_number' => trim($awb),
                                'company_name' => trim($this->input->post('courier_name_by_sf')),
                                'partner_id' => $partner_id,
                                'courier_charge' => trim($this->input->post('courier_charges_by_sf')), //
                                'box_count' => trim($this->input->post('defective_parts_shipped_boxes_count')), //defective_parts_shipped_gram
                                'billable_weight' => trim($billable_weight),
                                'actual_weight' => trim($billable_weight),
                                'basic_billed_charge_to_partner' => trim($this->input->post('courier_charges_by_sf')),
                                'booking_id' => trim($this->input->post('booking_id')),
                                'courier_invoice_file' => trim($defective_courier_receipt),
                                'shippment_date' => trim($this->input->post('defective_part_shipped_date')), //defective_part_shipped_date
                                'created_by' => 2,
                                'is_exist' => 0,
                                'sender_city' => $from_city,
                                'receiver_city' => $to_city,
                                'sender_state' => $from_state,
                                'receiver_state' => $to_state
                            );

                            $this->service_centers_model->insert_into_awb_details($awb_data);
                        } else {
                            $awb_data = array(
                                'company_name' => trim($this->input->post('courier_name_by_sf')),
                                //  'courier_charge' => trim($this->input->post('courier_charges_by_sf')), //
                                'partner_id' => $partner_id,
                                'box_count' => trim($this->input->post('defective_parts_shipped_boxes_count')), //defective_parts_shipped_gram
                                'billable_weight' => trim($billable_weight),
                                'actual_weight' => trim($billable_weight),
                                'basic_billed_charge_to_partner' => trim($this->input->post('courier_charges_by_sf')),
                                // 'booking_id' => trim($this->input->post('booking_id')),
                                'courier_invoice_file' => trim($defective_courier_receipt),
                                'shippment_date' => trim($this->input->post('defective_part_shipped_date')), //defective_part_shipped_date
                                'created_by' => 2,
                                'is_exist' => 0
                            );

                            $this->service_centers_model->update_awb_details($awb_data, trim($awb));
                        }
                    }
                    /**
                     * Update booking internal only when booking is completed.
                     * @modifiedBy Ankit Rajvanshi
                     */
                    
                    $defective_part_pending_details = $this->partner_model->get_spare_parts_by_any("spare_parts_details.id, status, booking_id", array('booking_id' => $booking_id, 'status IN ("' . DEFECTIVE_PARTS_PENDING . '", "' . DEFECTIVE_PARTS_REJECTED_BY_WAREHOUSE . '", "' . OK_PART_TO_BE_SHIPPED . '") ' => NULL));
                    if (empty($defective_part_pending_details)) {
                        $booking_internal_status = $data['status'];
                    } else {
                        $booking_internal_status = $defective_part_pending_details[0]['status'];
                    }

                    // if booking completed change internal status.
                    if($booking_details['current_status'] == _247AROUND_COMPLETED) {
                        $booking['internal_status'] = $booking_internal_status;
                        $partner_status = $this->booking_utilities->get_partner_status_mapping_data(_247AROUND_COMPLETED, $booking_internal_status, $partner_id, $booking_id);
                        if (!empty($partner_status)) {
                            $booking['partner_current_status'] = $partner_status[0];
                            $booking['partner_internal_status'] = $partner_status[1];
                            $booking['actor'] = $partner_status[2];
                            $booking['next_action'] = $partner_status[3];

                            if (!empty($booking_action) && $booking_action == 'reshedule') {
                                unset($booking['actor']);
                                unset($booking['next_action']);
                            }
                        }
                        $this->booking_model->update_booking($booking_id, $booking);
                    }
                    
                    //insert details into state change table   
                    $this->insert_details_in_state_change($booking_id, $data['status'], $data['remarks_defective_part_by_sf'], "not_define", "not_define", $sp_id);

                    if (!empty($this->input->post("shipped_inventory_id"))) {
                        $ledger_data = array(
                            "receiver_entity_id" => $this->input->post("defective_return_to_entity_id"),
                            "receiver_entity_type" => $this->input->post('defective_return_to_entity_type'),
                            "sender_entity_id" => $this->session->userdata('service_center_id'),
                            "sender_entity_type" => _247AROUND_SF_STRING,
                            "quantity" => $quantity,
                            "inventory_id" => $this->input->post("shipped_inventory_id"),
                            "agent_id" => $this->session->userdata('service_center_agent_id'),
                            "agent_type" => _247AROUND_SF_STRING,
                            "booking_id" => $this->input->post("booking_id"),
                            "active" => 1,
                            "is_defective" => 1,
                            "spare_id" => $sp_id
                        );

                        $this->inventory_model->insert_inventory_ledger($ledger_data);
                    }

                    //send email
                    $email_template = $this->booking_model->get_booking_email_template(COURIER_DETAILS);
                    if (!empty($email_template)) {
                        $rm_email = $this->get_rm_email($service_center_id);

                        $attachment = S3_WEBSITE_URL . "misc-images/" . $defective_courier_receipt;

                        $subject = vsprintf($email_template[4], array($this->session->userdata('service_center_name'), $booking_id));

                        $message = vsprintf($email_template[0], array($data['awb_by_sf'],
                            $data['courier_name_by_sf'], $this->input->post('courier_charges_by_sf'), $data['defective_part_shipped_date']));

                        $email_from = $email_template[2];

                        $to = $email_template[1];
                        $cc = $rm_email . ',' . $email_template[3];
                        $bcc = $email_template[5];

                        $this->notify->sendEmail($email_from, $to, $cc, $bcc, $subject, $message, $attachment, COURIER_DETAILS, "", $booking_id);
                    }

                    if (!isset($_POST['no_redirect_flag']) || empty($_POST['no_redirect_flag'])) {      //if not bulk update
                        $userSession = array('success' => 'Parts Updated.');
                        $this->session->set_userdata($userSession);
                        redirect(base_url() . "service_center/get_defective_parts_booking");
                    }
                } else {
                    if (isset($_POST['no_redirect_flag']) && !empty($_POST['no_redirect_flag'])) {        //if bulk update
                        $res = array(
                            'error' => true,
                            'errorMessage' => 'Parts Not Updated. Please refresh and try again.'
                        );
                        echo json_encode($res);
                        die;
                    } else {
                        log_message('info', __FUNCTION__ . '=> Defective Spare parts booking is not updated by SF ' . $this->session->userdata('service_center_name') .
                                " booking id " . $booking_id . " Data" . print_r($this->input->post(), true));
                        $userSession = array('success' => 'Parts Not Updated. Please refresh and try again');
                        $this->session->set_userdata($userSession);
                        redirect(base_url() . "service_center/get_defective_parts_booking");
                    }
                }
            } else {
                if (isset($_POST['no_redirect_flag']) && !empty($_POST['no_redirect_flag'])) {             //if bulk update
                    $res = array(
                        'error' => true,
                        'errorMessage' => 'Parts Not Updated. Please Upload Less Than 5 MB File.'
                    );
                    echo json_encode($res);
                    die;
                } else {
                    log_message('info', __FUNCTION__ . '=> Defective Spare parts booking is not updated by SF ' . $this->session->userdata('service_center_name') .
                            " booking id " . $booking_id . " Data" . print_r($this->input->post(), true));
                    $userSession = array('success' => 'Parts Not Updated. Please Upload Less Than 5 MB File.');
                    $this->session->set_userdata($userSession);
                    redirect(base_url() . "service_center/get_defective_parts_booking");
                }
            }
        }
    }

    /**
     * @desc This function is used to validate a date cannot be older than 2 days. -- for validation callback purpose only
     */
    function validate_shipped_date($date) {
        if (empty($date)) {
            return false;
        }
        $start_date = strtotime($date);
        $end_date = strtotime(Date("Y-m-d"));
        $diff = round(($end_date - $start_date) / 60 / 60 / 24);
        if ($diff <= 3) {                            //defective return shipped date cannot be older than 2 days.
            return true;
        }
        return false;
    }

    /**
     * @desc This function is used to get partner warehouses list
     */
    function get_warehouse_partner_list() {

        $partner_id = trim($_POST['partner']);

        $select1 = "warehouse_details.warehouse_address_line1 as company_name, concat('C/o ',contact_person.name,',', warehouse_address_line1,',',warehouse_address_line2,',',warehouse_details.warehouse_city,' Pincode -',warehouse_pincode, ',',warehouse_details.warehouse_state) as address, contact_person.name as contact_person_name,contact_person.official_contact_number as contact_number, warehouse_details.warehouse_city";
        $partner_details = $this->inventory_model->get_warehouse_details($select1, array("contact_person.entity_type" => _247AROUND_PARTNER_STRING, "contact_person.entity_id" => $partner_id), true, true);

        $partner_details1 = $this->partner_model->getpartner_details("company_name, concat(partners.address,',',partners.district,',',partners.state,',',partners.pincode) AS address,gst_number,primary_contact_name as contact_person_name ,primary_contact_phone_1 as contact_number, primary_contact_name as contact_person_name,owner_name,partners.district as warehouse_city", array('partners.id' => $partner_id));

        $addresses = array_merge($partner_details, $partner_details1);

        $option = "";
        $count = 0;
        foreach ($addresses as $key => $partner_wh) {
            $count++;
            if ($count == 1) {
                $selected = "selected";
            } else {
                $selected = "";
            }
            $option .="<option " . $selected . " value='" . $partner_wh['warehouse_city'] . "' >" . $partner_wh['address'] . "</option>";
        }

        echo $option;
    }

    /**
     * @desc This function is used to download partner challan/Address
     */
    function process_partner_challan_file() {
        log_message('info', __METHOD__ . json_encode($_POST, true));
        
        $challan_booking_id = $this->input->post('download_challan');
        $current_warehouseID = '';
        if(!empty($this->session->userdata('service_center_id'))){
            $current_warehouseID = $this->session->userdata('service_center_id');
        }else if(!empty($this->session->userdata('warehouse_id'))){
            $current_warehouseID = $this->session->userdata('warehouse_id');
        }
        $delivery_challan_file_name_array = array();
        foreach ($challan_booking_id as $partner_id => $spare_and_service) {
            $sp_id = implode(',', $spare_and_service);
            $data['wh_challan_file'] = $this->invoice_lib->generate_challan_file_to_partner($sp_id, $current_warehouseID);
            array_push($delivery_challan_file_name_array, $data['wh_challan_file']);
        }
        ////  ZIP The Challan files ///
        $challan_file = 'challan_file' . date('dmYHis');
        if (file_exists(TMP_FOLDER . $challan_file . '.zip')) {
            unlink(TMP_FOLDER . $challan_file . '.zip');
        }
        $zip = 'zip ' . TMP_FOLDER . $challan_file . '.zip ';

        foreach ($delivery_challan_file_name_array as $value1) {
            $zip .= " " . TMP_FOLDER . $value1 . " ";
        }
        $challan_file_zip = $challan_file . ".zip";
        $res = 0;
        system($zip, $res);
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header("Content-Disposition: attachment; filename=\"$challan_file_zip\"");
        $res2 = 0;
        system(" chmod 777 " . TMP_FOLDER . $challan_file . '.zip ', $res2);
        readfile(TMP_FOLDER . $challan_file . '.zip');
        if (file_exists(TMP_FOLDER . $challan_file . '.zip')) {
            unlink(TMP_FOLDER . $challan_file . '.zip');
            
        }
        
        foreach ($delivery_challan_file_name_array as $value_unlink) {
            unlink(TMP_FOLDER . $value_unlink);
        }
    }

    /**
     * @desc This function is used to download challan/Address
     */
    function print_partner_address_challan_file() {
        log_message('info', __METHOD__ . json_encode($_POST, true));
        $this->checkUserSession();
        log_message('info', __FUNCTION__ . ' Used by :' . $this->session->userdata('service_center_name'));
        $booking_address = $this->input->post('download_address');
        $challan_booking_id = $this->input->post('download_challan');
        $download_spare_tag = $this->input->post('download_spare_tag');
        if (!empty($booking_address)) {
            $this->print_partner_address();
        } else if (!empty($download_spare_tag)) {
            /* It's used to print the spare tags */
           $this->print_spar_tag(); 
        } else if (!empty($challan_booking_id)) {


            $partner_on_saas = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
            if ($partner_on_saas) {
                log_message('info', __METHOD__ . 'partner on saas', true);
                $delivery_challan_file_name_array = array();
                foreach ($challan_booking_id as $partner => $spare_and_service) {
                    $sp_id = implode(',', $spare_and_service);
                    $data['partner_challan_file'] = $this->invoice_lib->generate_challan_file($sp_id, $this->session->userdata('service_center_id'));
                    array_push($delivery_challan_file_name_array, $data['partner_challan_file']);
                }
                ////  ZIP The Challan files ///
                $challan_file = 'challan_file' . date('dmYHis');
                if (file_exists(TMP_FOLDER . $challan_file . '.zip')) {
                    unlink(TMP_FOLDER . $challan_file . '.zip');
                }
                $zip = 'zip ' . TMP_FOLDER . $challan_file . '.zip ';

                foreach ($delivery_challan_file_name_array as $value1) {
                    $zip .= " " . TMP_FOLDER . $value1 . " ";
                }
                $challan_file_zip = $challan_file . ".zip";
                $res = 0;
                system($zip, $res);
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header("Content-Disposition: attachment; filename=\"$challan_file_zip\"");
                $res2 = 0;
                system(" chmod 777 " . TMP_FOLDER . $challan_file . '.zip ', $res2);
                readfile(TMP_FOLDER . $challan_file . '.zip');
                if (file_exists(TMP_FOLDER . $challan_file . '.zip')) {
                    unlink(TMP_FOLDER . $challan_file . '.zip');
                    foreach ($delivery_challan_file_name_array as $value_unlink) {
                        unlink(TMP_FOLDER . $value_unlink);
                    }
                }
            } else {
                $this->print_challan_file();
            }
        }
    }

    /**
     * @desc This function is used to download SF challan file in zip
     */
    function print_challan_file() {
        log_message('info', __METHOD__ . json_encode($_POST, true), true);
        $this->checkUserSession();
        $challan = $this->input->post('download_challan');
        $delivery_challan_file_name_array = array();
        $challan_file = 'challan_file' . date('dmYHis');

        $zip = 'zip ' . TMP_FOLDER . $challan_file . '.zip ';
        if (file_exists(TMP_FOLDER . $challan_file . '.zip')) {
            unlink(TMP_FOLDER . $challan_file . '.zip');
        }
        foreach ($challan as $file) {
            $explode = explode(",", $file);
            foreach ($explode as $value) {
                if (copy(S3_WEBSITE_URL . "vendor-partner-docs/" . trim($value), TMP_FOLDER . $value)) {
                    $zip .= TMP_FOLDER . $value . " ";
                    array_push($delivery_challan_file_name_array, $value);
                }
            }
        }

        $challan_file_zip = $challan_file . ".zip";
        $res = 0;
        system($zip, $res);
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header("Content-Disposition: attachment; filename=\"$challan_file_zip\"");

        $res2 = 0;
        system(" chmod 777 " . TMP_FOLDER . $challan_file . '.zip ', $res2);
        readfile(TMP_FOLDER . $challan_file . '.zip');
        if (file_exists(TMP_FOLDER . $challan_file . '.zip')) {
            unlink(TMP_FOLDER . $challan_file . '.zip');
            foreach ($delivery_challan_file_name_array as $value_unlink) {
                unlink(TMP_FOLDER . $value_unlink);
            }
        }
    }

    /**
     * @desc: This is used to print booking partner Address
     */
    function print_partner_address() {
        log_message('info', __METHOD__ . json_encode($_POST, true));
        $this->checkUserSession();
        log_message('info', __FUNCTION__ . ' Used by :' . $this->session->userdata('service_center_name'));
        $booking_address = $this->input->post('download_address');
        $booking_history['details'] = array();

        $main_company_public_name = "";
        $main_company_logo = "";
        $partner_on_saas = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        $main_partner = $this->partner_model->get_main_partner_invoice_detail($partner_on_saas);
        if (!empty($main_partner)) {
            $main_company_public_name = $main_partner['main_company_public_name'];
            $main_company_logo = $main_partner['main_company_logo'];
        }

        if (!empty($booking_address)) {
            $i = 0;
            foreach ($booking_address as $spare_id) {

                $v_select = "spare_parts_details.entity_type,spare_parts_details.booking_id,spare_parts_details.partner_id,spare_parts_details.service_center_id,service_centres.name, service_centres.id, company_name, "
                        . "service_centres.address,service_centres.pincode, service_centres.state, "
                        . "service_centres.district, service_centres.primary_contact_name,"
                        . "service_centres.primary_contact_phone_1,booking_details.partner_id as booking_partner_id, defective_return_to_entity_type,"
                        . "defective_return_to_entity_id";
                $sp_details = $this->partner_model->get_spare_parts_by_any($v_select, array('spare_parts_details.id' => $spare_id), true, true);

                $select = "contact_person.name as  primary_contact_name,contact_person.official_contact_number as primary_contact_phone_1,contact_person.alternate_contact_number as primary_contact_phone_2,"
                        . "concat(warehouse_address_line1,',',warehouse_address_line2) as address,warehouse_details.warehouse_city as district,"
                        . "warehouse_details.warehouse_pincode as pincode,"
                        . "warehouse_details.warehouse_state as state";


                $where = array('contact_person.entity_id' => $sp_details[0]['defective_return_to_entity_id'],
                    'contact_person.entity_type' => $sp_details[0]['defective_return_to_entity_type']);
                $wh_address_details = $this->inventory_model->get_warehouse_details($select, $where, false, true);
                $booking_details = array();
                switch ($sp_details[0]['defective_return_to_entity_type']) {
                    case _247AROUND_PARTNER_STRING:
                        $booking_details = $this->partner_model->getpartner($sp_details[0]['defective_return_to_entity_id'])[0];
                        break;
                    case _247AROUND_SF_STRING:
                        $select1 = 'name as company_name,primary_contact_name,address,pincode,state,district,primary_contact_phone_1,primary_contact_phone_2';
                        $booking_details = $this->vendor_model->getVendorDetails($select1, array('id' => $sp_details[0]['defective_return_to_entity_id']))[0];
                        break;
                }

                if (!empty($wh_address_details)) {
                    $wh_address_details[0]['company_name'] = $booking_details['company_name'];
                    $booking_history['details'][$i] = $wh_address_details[0];
                } else {
                    $booking_history['details'][$i] = $booking_details;
                }
                $booking_history['details'][$i]['vendor'] = $sp_details[0];
                $booking_history['details'][$i]['booking_id'] = $sp_details[0]['booking_id'];
                $i++;
            }

            $booking_history['meta']['main_company_public_name'] = $main_company_public_name;
            $booking_history['meta']['main_company_logo'] = $main_company_logo;
        } else {
            //Logging
            log_message('info', __FUNCTION__ . ' No Download Address from POST');
        }

        $this->load->view('service_centers/print_partner_address', $booking_history);
    }

    
    /**
     * @desc: This is used to print Spare tags
     * @param:void 
     */
    function print_spar_tag() {
        log_message('info', __METHOD__ . json_encode($_POST, true));
        $this->checkUserSession();
        log_message('info', __FUNCTION__ . ' Used by :' . $this->session->userdata('service_center_name'));
        $download_spare_tag = $this->input->post('download_spare_tag');
        $booking_history['details'] = array();
        $main_company_public_name = "";
        $main_company_logo = "";
        $partner_on_saas = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        $main_partner = $this->partner_model->get_main_partner_invoice_detail($partner_on_saas);
        if (!empty($main_partner)) {
            $main_company_public_name = $main_partner['main_company_public_name'];
            $main_company_logo = $main_partner['main_company_logo'];
        }

        if (!empty($download_spare_tag)) {
            $i = 0;
            foreach ($download_spare_tag as $spare_id) {
                $spare_tag_array = array();
                $print_Address_history = array();
                if (!empty($spare_id)) {
                    $select = "booking_details.booking_id, spare_parts_details.model_number, spare_parts_details.serial_number, spare_parts_details.shipped_quantity, i.part_number, i.part_name, partners.public_name, (CASE WHEN spare_parts_details.consumed_part_status_id = 5 THEN 'Ok Part' ELSE 'Defective' END) as consumed_part_status, symptom.symptom";
                    $where = array('spare_parts_details.id' => $spare_id);
                    $spare_tag_detail = $this->inventory_model->get_spare_tag_details($select, $where);
                    if (!empty($spare_tag_detail)) {
                        $spare_tag_array[] = $spare_tag_detail[0];
                    }


                    $v_select = "spare_parts_details.entity_type,spare_parts_details.booking_id,spare_parts_details.partner_id,spare_parts_details.service_center_id,service_centres.name, service_centres.id, company_name, "
                            . "service_centres.address,service_centres.pincode, service_centres.state, "
                            . "service_centres.district, service_centres.primary_contact_name,"
                            . "service_centres.primary_contact_phone_1,booking_details.partner_id as booking_partner_id, defective_return_to_entity_type,"
                            . "defective_return_to_entity_id";
                    $sp_details = $this->partner_model->get_spare_parts_by_any($v_select, array('spare_parts_details.id' => $spare_id), true, true);

                    $select = "contact_person.name as  primary_contact_name,contact_person.official_contact_number as primary_contact_phone_1,contact_person.alternate_contact_number as primary_contact_phone_2,"
                            . "concat(warehouse_address_line1,',',warehouse_address_line2) as address,warehouse_details.warehouse_city as district,"
                            . "warehouse_details.warehouse_pincode as pincode,"
                            . "warehouse_details.warehouse_state as state";


                    $where = array('contact_person.entity_id' => $sp_details[0]['defective_return_to_entity_id'],
                        'contact_person.entity_type' => $sp_details[0]['defective_return_to_entity_type']);
                    $wh_address_details = $this->inventory_model->get_warehouse_details($select, $where, false, true);
                    $booking_details = array();
                    switch ($sp_details[0]['defective_return_to_entity_type']) {
                        case _247AROUND_PARTNER_STRING:
                            $booking_details = $this->partner_model->getpartner($sp_details[0]['defective_return_to_entity_id'])[0];
                            break;
                        case _247AROUND_SF_STRING:
                            $select1 = 'name as company_name,primary_contact_name,address,pincode,state,district,primary_contact_phone_1,primary_contact_phone_2';
                            $vendor_details = $this->vendor_model->getVendorDetails($select1, array('id' => $sp_details[0]['defective_return_to_entity_id']));
                            if (!empty($vendor_details)) {
                                $booking_details = $vendor_details[0];
                            }
                            break;
                    }

                    if (!empty($wh_address_details)) {
                        $wh_address_details[0]['company_name'] = $booking_details['company_name'];
                        $print_Address_history[$i] = $wh_address_details[0];
                    } else {
                        $print_Address_history[$i] = $booking_details;
                    }
                    $print_Address_history[$i]['vendor'] = $sp_details[0];
                    $print_Address_history[$i]['booking_id'] = $sp_details[0]['booking_id'];
                    $i++;
                }

                $booking_history['details'][] = array("spare_tag" => $spare_tag_array, "print_addrres" => $print_Address_history);
            }


            $booking_history['meta']['main_company_public_name'] = $main_company_public_name;
            $booking_history['meta']['main_company_logo'] = $main_company_logo;
        } else {
            //Logging
            log_message('info', __FUNCTION__ . ' No Download Spare Tag from POST');
        }
       
        $this->load->view('service_centers/print_spare_tag', $booking_history);
    }

    /**
     * @desc: This is used to print Concern Details
     */
    function print_declaration_detail() {
        log_message('info', __METHOD__ . json_encode($_POST, true));
        if(!empty($this->session->userdata('warehouse_id'))) {
            $this->checkEmployeeUserSession();
            $agent_name = $this->session->userdata('employee_id');
            $sf_id = $this->session->userdata('warehouse_id');
        } else {
            $this->checkUserSession();
            $agent_name = $this->session->userdata('service_center_name');
            $sf_id = $this->session->userdata('service_center_id');
        }
        
        log_message('info', __FUNCTION__ . ' Used by :' . $agent_name);
        $booking_declaration_detail = $this->input->post('coueriers_declaration');

        $booking_declaration_detail_list['coueriers_declaration'] = array();
        $i = 0;

        if (!empty($booking_declaration_detail)) {

            foreach ($booking_declaration_detail as $partner_id => $spare_id_array) {


                foreach ($spare_id_array as $spare_id) {
                    $v_select = "spare_parts_details.booking_id,spare_parts_details.partner_id,spare_parts_details.service_center_id,spare_parts_details.requested_inventory_id, spare_parts_details.parts_requested, spare_parts_details.invoice_gst_rate,"
                            . "booking_details.partner_id as booking_partner_id, booking_details.service_id, defective_return_to_entity_type, defective_return_to_entity_id, service_centres.name, "
                            . "service_centres.company_name, service_centres.address, service_centres.pincode, service_centres.state, service_centres.district, service_centres.gst_no";

                    $sp_details = $this->partner_model->get_spare_parts_by_any($v_select, array('spare_parts_details.id' => $spare_id), true, true);

                    $select = "partners.id, partners.company_name, partners.public_name, partners.company_type, partners.address, partners.district, partners.state, partners.pincode";

                    $partner_details = $this->partner_model->get_partner_contract_detail($select, array('partners.id' => $sp_details[0]['booking_partner_id']), $join = NULL, $joinType = NULL);

                    $service_details = $this->booking_model->selectservicebyid($sp_details[0]['service_id']);

                    if (!empty($sp_details[0]['requested_inventory_id'])) {
                        $inventory_details = $this->inventory_model->get_inventory_master_list_data('inventory_master_list.price,inventory_master_list.gst_rate,inventory_master_list.hsn_code', array('inventory_master_list.inventory_id' => $sp_details[0]['requested_inventory_id']));

                        $challan_value = round($inventory_details[0]['price'] * ( 1 + $inventory_details[0]['gst_rate'] / 100), 0);
                        $hsn_code = $inventory_details[0]['hsn_code'];
                    } else {
                        $challan_value = '0.00';
                        $hsn_code = '';
                    }

                    $booking_declaration_detail_list['coueriers_declaration'][$i] = $sp_details[0];
                    $booking_declaration_detail_list['coueriers_declaration'][$i]['appliance_name'] = $service_details[0]['services'];
                    $booking_declaration_detail_list['coueriers_declaration'][$i]['challan_approx_value'] = $challan_value;
                    $booking_declaration_detail_list['coueriers_declaration'][$i]['hsn_code'] = $hsn_code;

                    $booking_declaration_detail_list['coueriers_declaration'][$i]['public_name'] = $partner_details[0]->public_name;

                    $i++;
                }
            }
        } else {
            //Logging
            log_message('info', __FUNCTION__ . ' No Download Address from POST');
        }

        $service_center_id = $sf_id;

        $output_file = "declaration-" . $service_center_id . "-" . date('dmYHis');
        $output_file_pdf = $output_file . ".pdf";
        /* Create html for job card */
        $html = $this->load->view('service_centers/print_couriers_declaration_details', $booking_declaration_detail_list, true);
        /* convert html into pdf */
        $json_result = $this->miscelleneous->convert_html_to_pdf($html, '', $output_file_pdf, "vendor-partner-docs");

        $pdf_response = json_decode($json_result, TRUE);
        if ($pdf_response['response'] == "Success") {

            if (file_exists(TMP_FOLDER . $output_file_pdf)) {
                unlink(TMP_FOLDER . $output_file_pdf);
            }

            if (copy(S3_WEBSITE_URL . "vendor-partner-docs/" . trim($output_file_pdf), TMP_FOLDER . $output_file_pdf)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header("Content-Disposition: attachment; filename=\"$output_file_pdf\"");
                $res2 = 0;
                system(" chmod 777 " . TMP_FOLDER . $output_file_pdf, $res2);
                readfile(TMP_FOLDER . $output_file_pdf);
                if (file_exists(TMP_FOLDER . $output_file_pdf)) {
                    unlink(TMP_FOLDER . $output_file_pdf);
                }
            }
        } else {
            echo "File not generated";
        }
    }

    function get_appliance_details() {
        $modelno = $this->input->post('modelno');
        $entityid = $this->input->post('entityid');
        $data = $this->partner_model->get_appliance_model_details($modelno, $entityid);
        echo json_encode($data, true);
    }

    /**
     * @desc: It's used to generate SF Challan
     * @param String $generate_challan
     */
    function generate_sf_challan($generate_challan, $flag) {

        $delivery_challan_file_name_array = array();

        foreach ($generate_challan as $key => $value) {
            if (!empty($generate_challan)) {
                $post = array();
                if (!empty($flag)) {
                    $post['where_in'] = array('spare_parts_details.booking_id' => $value, 'spare_parts_details.entity_type' => _247AROUND_PARTNER_STRING, 'spare_parts_details.status' => SPARE_PARTS_REQUESTED);
                } else {
                    $post['where_in'] = array('spare_parts_details.booking_id' => $value, 'spare_parts_details.entity_type' => _247AROUND_SF_STRING, 'spare_parts_details.status' => SPARE_PARTS_REQUESTED);
                }

                $post['is_inventory'] = true;
                $select = 'booking_details.booking_id, booking_details.assigned_vendor_id, spare_parts_details.model_number,spare_parts_details.serial_number, spare_parts_details.id,spare_parts_details.requested_inventory_id, spare_parts_details.partner_id,spare_parts_details.entity_type,spare_parts_details.part_warranty_status, spare_parts_details.parts_requested, spare_parts_details.challan_approx_value, spare_parts_details.quantity, inventory_master_list.part_number, inventory_master_list.gst_rate, spare_parts_details.partner_id,booking_details.assigned_vendor_id,IF(spare_consumption_status.consumed_status !="" , spare_consumption_status.consumed_status, "NA") as consumed_status';
                $part_details = $this->partner_model->get_spare_parts_by_any($select, array(), true, false, false, $post);


                if (!empty($part_details)) {
                    $spare_details = array();
                    foreach ($part_details as $value) {
                        $spare_parts = array();
                        //if ($value['part_warranty_status'] !== SPARE_PART_IN_OUT_OF_WARRANTY_STATUS) {
                            $spare_parts['spare_id'] = $value['id'];
                            $spare_parts['booking_id'] = $value['booking_id'];
                            $spare_parts['parts_shipped'] = $value['parts_requested'];
                            $spare_parts['challan_approx_value'] = $value['challan_approx_value'];
                            $spare_parts['part_number'] = $value['part_number'];
                            $spare_parts['shipped_quantity'] = $value['quantity'];
                            $spare_parts['inventory_id'] = $value['requested_inventory_id'];
                            $spare_parts['gst_rate'] = $value['gst_rate'];
                            $spare_parts['consumed_status'] = $value['consumed_status'];
                            $spare_parts['model_number_shipped'] = $value['model_number'];
                            $spare_parts['serial_number'] = $value['serial_number'];
                            if (!empty($value['assigned_vendor_id'])) {
                                $vendor_details = $this->vendor_model->getVendorDetails("service_centres.id, service_centres.pincode", array("service_centres.id" => $value['assigned_vendor_id']), 'name', array(), array(), array());
                                if (!empty($vendor_details)) {
                                    $serviceable_area = $this->inventory_model->get_generic_table_details("courier_serviceable_area", "courier_serviceable_area.courier_company_name", array("courier_serviceable_area.pincode" => $vendor_details[0]['pincode'], "courier_serviceable_area.status" => 1), array());
                                    if (!empty($serviceable_area)) {
                                        $couriers_name = implode(', ', array_map(function ($entry) {
                                                    return $entry['courier_company_name'];
                                                }, $serviceable_area));
                                    } else {
                                        $couriers_name = 'NA';
                                    }
                                } else {
                                    $couriers_name = 'NA';
                                }
                            //}
                            $spare_parts['courier_name'] = $couriers_name;
                        }
                        $spare_details[][] = $spare_parts;
                    }
                    $assigned_vendor_id = $part_details[0]['partner_id'];
                    $service_center_id = $part_details[0]['assigned_vendor_id'];
                }
                
                
                $sf_details = $this->vendor_model->getVendorDetails('name as company_name,address,district, pincode, state,sc_code,is_gst_doc,owner_name,signature_file,gst_no,is_signature_doc,primary_contact_name as contact_person_name, primary_contact_phone_1 as contact_number , primary_contact_phone_2 as contact_number_2 , owner_phone_1 as contact_number_3, owner_phone_2 as contact_number_4, service_centres.gst_no as gst_number', array('id' => $service_center_id));



                if (!empty($part_details)) {
                    $select = "concat('C/o ',contact_person.name,',', warehouse_address_line1,',',warehouse_address_line2,',',warehouse_details.warehouse_city,' Pincode -',warehouse_pincode, ',',warehouse_details.warehouse_state) as address,contact_person.name as contact_person_name,contact_person.official_contact_number as contact_number,service_centres.gst_no as gst_number, warehouse_state as state";

                    $where = array('contact_person.entity_id' => $part_details[0]['partner_id'],
                        'contact_person.entity_type' => $part_details[0]['entity_type']);
                    $wh_address_details = $this->inventory_model->get_warehouse_details($select, $where, false, true, true);

                    $partner_details = array();

                    if ($part_details[0]['entity_type'] == _247AROUND_PARTNER_STRING) {

                        if ($this->session->userdata("userType") == "service_center" || !empty($this->session->userdata('warehouse_id'))) {
                            if (!empty($this->session->userdata('warehouse_id'))) {
                                $login_sc_id = $this->session->userdata("warehouse_id");
                            } else {
                                $login_sc_id = $this->session->userdata("service_center_id");
                            }

                            $partner_details = $this->vendor_model->getVendorDetails('name as company_name,address,owner_name,gst_no as gst_number, state', array('id' => $login_sc_id));
                        } else {

                            $partner_details = $this->partner_model->getpartner_details('company_name, address,gst_number,primary_contact_name as contact_person_name ,primary_contact_phone_1 as contact_number, state', array('partners.id' => $part_details[0]['partner_id']));
                        }
                    } else if ($part_details[0]['entity_type'] === _247AROUND_SF_STRING) {
                        $partner_details = $this->vendor_model->getVendorDetails('name as company_name,address,owner_name,gst_no as gst_number', array('id' => $part_details[0]['partner_id']));
                    }

                    if (!empty($wh_address_details)) {
                        $partner_details[0]['address'] = $wh_address_details[0]['address'];
                        $partner_details[0]['contact_person_name'] = $wh_address_details[0]['contact_person_name'];
                        $partner_details[0]['contact_number'] = $wh_address_details[0]['contact_number'];
                        $partner_details[0]['state'] = $wh_address_details[0]['state'];
                    }
                }


                $data = array();
                if (!empty($sf_details)) {
                    $data['partner_challan_number'] = $this->miscelleneous->create_sf_challan_id($sf_details[0]['sc_code'], true);
                    $sf_details[0]['address'] = $sf_details[0]['address'] . ", " . $sf_details[0]['district'] . ", " . $sf_details[0]['state'] . ", Pincode -" . $sf_details[0]['pincode'];
                   
                    $contact_number_array = array($sf_details[0]['contact_number'],$sf_details[0]['contact_number_2'],$sf_details[0]['contact_number_3'],$sf_details[0]['contact_number_4']);
                    $contact_number_string = implode(', ',array_unique(array_filter($contact_number_array))); 
                     $sf_details[0]['contact_number'] = $contact_number_string;
                }
                
                if (!empty($spare_details)) {
                    $data['partner_challan_file'] = $this->invoice_lib->process_create_sf_challan_file($sf_details, $partner_details, $data['partner_challan_number'], $spare_details, '', '', false, true, true);
                    array_push($delivery_challan_file_name_array, $data['partner_challan_file']);
                    if (!empty($data['partner_challan_file'])) {
                        if (!empty($spare_details)) {
                            foreach ($spare_details as $val) {
                                if ($this->session->userdata("userType") == "service_center" || !empty($this->session->userdata('warehouse_id'))) {
                                    if (!empty($this->session->userdata('warehouse_id'))) {
                                        $login_sc_entity_id = $this->session->userdata("warehouse_id");
                                    } else {
                                        $login_sc_entity_id = $this->session->userdata("service_center_id");
                                    }
                                    $data['spare_parts_details.entity_type'] = _247AROUND_SF_STRING;
                                    $data['spare_parts_details.partner_id'] = $login_sc_entity_id;
                                    $data['spare_parts_details.defective_return_to_entity_type'] = _247AROUND_SF_STRING;
                                    $data['spare_parts_details.defective_return_to_entity_id'] = $login_sc_entity_id;
                                }
                                $this->service_centers_model->update_spare_parts(array('id' => $val[0]['spare_id']), $data);
                            }
                        }
                    }
                }
            }
        }  //// for end 
        ////  ZIP The Challan files ///
        $challan_file = 'challan_file' . date('dmYHis');
        if (file_exists(TMP_FOLDER . $challan_file . '.zip')) {
            unlink(TMP_FOLDER . $challan_file . '.zip');
        }
        
        $this->booking_creation_lib->create_and_download_zip_file('challan_file_'.date('YmdHis').'.zip', $delivery_challan_file_name_array);
        exit();

//        
//        
//        $zip = 'zip ' . TMP_FOLDER . $challan_file . '.zip ';
//        
//        $challan_file_zip = $challan_file . ".zip";
//        $res = 0;
//        system($zip, $res);
//        header('Content-Description: File Transfer');
//        header('Content-Type: application/octet-stream');
//        header("Content-Disposition: attachment; filename=\"$challan_file_zip\"");
//
//        $res2 = 0;
//        system(" chmod 777 " . TMP_FOLDER . $challan_file . '.zip ', $res2);
//        readfile(TMP_FOLDER . $challan_file . '.zip');
//        if (file_exists(TMP_FOLDER . $challan_file . '.zip')) {
//            unlink(TMP_FOLDER . $challan_file . '.zip');
//            if (file_exists(TMP_FOLDER . $challan_file_zip)) {
//                unlink(TMP_FOLDER . $challan_file_zip);  // ZIP extension coming two times // 
//            }
//
//            foreach ($delivery_challan_file_name_array as $value_unlink) {
//                if (file_exists(TMP_FOLDER . $value_unlink)) {
//                    unlink(TMP_FOLDER . $value_unlink);
//                }
//            }
//        }
    }

    /**
     * @desc: Call by Ajax to load group upcountry details
     * @param String $booking_id
     */
    function pending_booking_upcountry_price($booking_id, $is_customer_paid, $flat_upcountry) {
        $this->checkUserSession();
        log_message('info', __FUNCTION__ . ' Used by :' . $this->session->userdata('service_center_name'));
        $service_center_id = $this->session->userdata('service_center_id');
        if (empty($is_customer_paid)) {
            $is_customer_paid = 0;
        }

        if ($flat_upcountry == 1) {
            $is_customer_paid = 1;
        }
        $data['data'] = $this->upcountry_model->upcountry_booking_list($service_center_id, $booking_id, true, $is_customer_paid, $flat_upcountry);
        // $this->load->view('service_centers/header');
        $this->load->view('service_centers/upcountry_booking_details', $data);
    }

    /**
     * @Desc: This function is used to show brackets details list
     * @params: void
     * @return: void
     * 
     */
    function show_brackets_list($page = "", $offset = "") {
        $this->checkUserSession();
        if ($page == 0) {
            $page = 50;
        }
        // $offset = ($this->uri->segment(5) != '' ? $this->uri->segment(5) : 0);

        $config['base_url'] = base_url() . 'employee/service_centers/show_brackets_list/' . $page;
        $config['total_rows'] = $this->inventory_model->get_total_brackets_given_count($this->session->userdata('service_center_id'));

        if ($offset != "All") {
            $config['per_page'] = $page;
        } else {
            $config['per_page'] = $config['total_rows'];
        }

        $config['uri_segment'] = 5;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';

        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();
        $data['Count'] = $config['total_rows'];
        $data['brackets'] = $this->inventory_model->get_total_brackets_given($config['per_page'], $offset, $this->session->userdata('service_center_id'));
        //Getting name for order received from  to vendor
        foreach ($data['brackets'] as $key => $value) {
            $data['order_received_from'][$key] = $this->vendor_model->getVendorContact($value['order_received_from'])[0];

            // Getting name for order given to vendor

            $data['order_given_to'][$key] = $this->vendor_model->getVendorContact($value['order_given_to'])[0]['name'];
        }
        $this->load->view('service_centers/header');
        $this->load->view("service_centers/show_vender_brackets_list", $data);
    }

    /**
     * @Desc: This function is used to show brackets order history for order_id
     * @params: Int order_id
     * @return :View
     * 
     */
    function show_brackets_order_history($order_id) {
        $data['data'] = $this->inventory_model->get_brackets_by_order_id($order_id);
        $data['order_id'] = $order_id;
        $data['brackets'] = $this->inventory_model->get_brackets_by_id($order_id);
        $order_received_from_vendor_details = $this->vendor_model->getVendorContact($data['brackets'][0]['order_received_from']);
        $data['order_received_from'] = $order_received_from_vendor_details[0]['name'];
        $data['order_received_from_address'] = $order_received_from_vendor_details[0]['address'] . ',' . $order_received_from_vendor_details[0]['district'] . ',' . $order_received_from_vendor_details[0]['state'] . ',' . $order_received_from_vendor_details[0]['pincode'];
        $data['order_given_to'] = $this->vendor_model->getVendorContact($data['brackets'][0]['order_given_to'])[0]['name'];
        $data['primary_contact_name'] = $order_received_from_vendor_details[0]['primary_contact_name'];
        $data['phone_number'] = $order_received_from_vendor_details[0]['primary_contact_phone_1'] . ", " . $order_received_from_vendor_details[0]['primary_contact_phone_2'];

        $this->load->view('service_centers/header');
        $this->load->view("service_centers/show_vender_brackets_order_history", $data);
    }

    /**
     * @Desc: This function is used to update shipment
     * @params: Int order id
     * @return : view
     */
    function get_update_shipment_form($order_id) {
        if (!empty($order_id) || $order_id != '') {
            $data['brackets'] = $this->inventory_model->get_brackets_by_id($order_id);
            $data['shipped_flag'] = TRUE;
            $data['order_id'] = $order_id;
            $data['order_given_to'] = $this->vendor_model->getVendorContact($data['brackets'][0]['order_given_to'])[0]['name'];
            $data['order_received_from'] = $this->vendor_model->getVendorContact($data['brackets'][0]['order_received_from'])[0]['name'];
            $this->load->view('service_centers/header');
            $this->load->view("service_centers/update_vender_brackets", $data);
        } else {
            echo "Please Try Again! Order Id Not Exist";
        }
    }

    /**
     * @Desc: This function is used to process update shipment form
     * @params: Array
     * @return: void
     */
    function process_vender_update_shipment_form() {
        //Saving Uploading file.
        if ($_FILES['shipment_receipt']['error'] != 4 && !empty($_FILES['shipment_receipt']['tmp_name'])) {
            $tmpFile = $_FILES['shipment_receipt']['tmp_name'];
            //Assigning File Name for uploaded shipment receipt
            $fileName = "Shipment-Receipt-" . $this->input->post('order_id') . '.' . explode('.', $_FILES['shipment_receipt']['name'])[1];
            move_uploaded_file($tmpFile, TMP_FOLDER . $fileName);

            //Uploading images to S3 
            $bucket = BITBUCKET_DIRECTORY;
            $directory = "misc-images/" . $fileName;
            $this->s3->putObjectFile(TMP_FOLDER . $fileName, $bucket, $directory, S3::ACL_PUBLIC_READ);

            $data['shipment_receipt'] = $fileName;
        }
        $order_id = $this->input->post('order_id');
        $order_received_from = $this->input->post('order_received_from');
        $order_given_to = $this->input->post('order_given_to');
//        $data['19_24_shipped'] = $this->input->post('19_24_shipped');
        $data['19_24_shipped'] = '0';
        $data['26_32_shipped'] = $this->input->post('26_32_shipped');
        $data['36_42_shipped'] = $this->input->post('36_42_shipped');
//        $data['43_shipped'] = $this->input->post('43_shipped');
        $data['43_shipped'] = '0';
        $data['total_shipped'] = $this->input->post('total_shipped');
        $data['shipment_date'] = !empty($this->input->post('shipment_date')) ? $this->input->post('shipment_date') : date('Y-m-d H:i:s');
        $data['is_shipped'] = 1;


        $attachment = "";
        if (!empty($fileName)) {
            $data['shipment_receipt'] = $fileName;
            $attachment = TMP_FOLDER . $fileName;
        }

        //Updating value in Brackets
        $update_brackets = $this->inventory_model->update_brackets($data, array('order_id' => $order_id));
        if ($update_brackets) {
            //Loggin success
            log_message('info', __FUNCTION__ . ' Brackets Shipped has been updated ' . print_r($data, TRUE));

            //Adding value in Booking State Change
            $this->insert_details_in_state_change($order_id, "Brackets_Shipped", "Brackets Shipped", "not_define", "not_define");
            //$this->notify->insert_state_change($order_id, _247AROUND_BRACKETS_SHIPPED, _247AROUND_BRACKETS_PENDING, "Brackets Shipped", $this->session->userdata('id'), $this->session->userdata('employee_id'), _247AROUND);
            //Logging Success
            log_message('info', __FUNCTION__ . ' Brackets Pending - Shipped state have been added in Booking State Change ');

            // Sending mail to order_received_from vendor
            $order_received_from_email = $this->vendor_model->getVendorContact($order_received_from);
            $vendor_poc_mail = $order_received_from_email[0]['primary_contact_email'];
            $vendor_owner_mail = $order_received_from_email[0]['owner_email'];
            $to = $vendor_poc_mail . ',' . $vendor_owner_mail;

            // Sending brackets Shipped Mail to order received from vendor
            $email = array();
            //Getting template from Database
            $template = $this->booking_model->get_booking_email_template("brackets_shipment_mail");

            if (!empty($template)) {
                $email['order_id'] = $order_id;
                $subject = vsprintf($template[4], $order_received_from_email[0]['company_name']);
                $emailBody = vsprintf($template[0], $email);
                $this->notify->sendEmail($template[2], $to, $template[3] . ',' . $this->get_rm_email($order_received_from), '', $subject, $emailBody, $attachment, 'brackets_shipment_mail');
            }
            //2. Sending mail to order_given_to vendor
            $order_given_to_email_to = $this->vendor_model->getVendorContact($order_given_to);
            $to = $order_given_to_email_to[0]['primary_contact_email'] . ',' . $order_given_to_email_to[0]['owner_email'];
            $order_given_to_email = array();
            //Getting template from Database
            $template1 = $this->booking_model->get_booking_email_template("brackets_shipment_mail_to_order_given_to");

            if (!empty($template)) {
                $order_given_to_email['order_recieved_from'] = $order_received_from_email[0]['company_name'];
                $order_given_to_email['order_id'] = $order_id;
                $subject = vsprintf($template1[4], $order_received_from_email[0]['company_name']);
                $emailBody = vsprintf($template1[0], $order_given_to_email);

                $this->notify->sendEmail($template1[2], $to, $template1[3] . ',' . $this->get_rm_email($order_given_to), '', $subject, $emailBody, '', 'brackets_shipment_mail_to_order_given_to');

                //Loggin send mail success
                log_message('info', __FUNCTION__ . ' Shipped mail has been sent to order_given_to vendor ' . $emailBody);
            }

            //Loggin send mail success
            log_message('info', __FUNCTION__ . ' Shipped mail has been sent to order_received_from vendor ' . $emailBody);

            //Setting success session data 
            $this->session->set_userdata('brackets_update_success', 'Brackets Shipped updated Successfully');

            redirect(base_url() . 'employee/service_centers/show_brackets_list');
        } else {
            //Loggin error
            log_message('info', __FUNCTION__ . ' Brackets Shipped updated Error ' . print_r($data, TRUE));

            //Setting error session data 
            $this->session->set_userdata('brackets_update_error', 'No changes made to be updated.');
            $this->get_update_shipment_form($order_id);
        }
    }

    /**
     * @Desc: This function is used to get RM email (:POC) details for the corresponding vendor 
     * @params: vendor 
     * @return : string
     */
    private function get_rm_email($vendor_id) {
        $employee_rm_relation = $this->vendor_model->get_rm_contact_details_by_sf_id($vendor_id);
        $rm_poc_email = "";
        if (!empty($employee_rm_relation)) {
            $rm_poc_email = $employee_rm_relation[0]['official_email'];
        }

        return $rm_poc_email;
    }
    
    /**
     * @Desc: This function is used to get ASM email (:POC) details for the corresponding vendor 
     * @params: vendor 
     * @return : string
     */
    private function get_asm_email($vendor_id) {
        $employee_asm_relation = $this->vendor_model->get_asm_contact_details_by_sf_id($vendor_id);
        $asm_poc_email = "";
        if (!empty($employee_asm_relation)) {
            $asm_poc_email = $employee_asm_relation[0]['official_email'];
        }

        return $asm_poc_email;
    }

    /**
     * @desc Used to show buyback order data as requested
     * @param void
     * @return json $output 
     */
    public function view_delivered_bb_order_details() {
        $this->check_BB_UserSession();
	$cp_id = $this->session->userdata('service_center_id');
        $post['where']['cp_id'] = $cp_id;
        $post['order']['column'] = 'id';
        $post['order']['order_by'] = 'desc';
        $post['length'] = 1;
        $post['start'] = 0;
        $otp_detail = $this->bb_model->fetch_buyback_otp($post);
        $data = array();
        if(!empty($otp_detail)){
          $data['otp'] =  $otp_detail[0]['otp'];
        }
        $this->load->view('service_centers/header');
        $this->load->view('service_centers/bb_order_details',$data);
    }

    /**
     * @desc Used to show the buyback order details on cp panel
     * @param $order_id string
     * @param $service_id string
     * @param $city string
     * @return void
     */
    function update_bb_report_issue_order_details($order_id, $service_id, $city, $cp_id, $current_status) {
        $this->check_BB_UserSession();
        $data['order_id'] = rawurldecode($order_id);
        $data['service_id'] = rawurldecode($service_id);
        $data['city'] = rawurldecode($city);
        $data['cp_id'] = rawurldecode($cp_id);
        $data['current_status'] = rawurldecode($current_status);
        $data['products'] = $this->booking_model->selectservice();
        $data['cp_basic_charge'] = $this->bb_model->get_bb_order_appliance_details(array('partner_order_id' => $data['order_id']), 'cp_basic_charge');

        $this->load->view('service_centers/header');
        $this->load->view('service_centers/update_bb_order_details', $data);
    }

    /**
     * @desc Used to get buyback order brand from ajax call
     * @param void
     * @return $option string
     */
    function get_bb_order_brand() {
        //$this->check_BB_UserSession();
        $service_id = $this->input->post('service_id');
        $cp_id = $this->input->post('cp_id');
        $where = array('cp_id' => $cp_id, 'service_id' => $service_id, 'brand != " "' => null, 'visible_to_cp' => '1');
        $select = "brand";
        $brands = $this->service_centre_charges_model->get_bb_charges($where, $select, TRUE);
        $option = '<option selected disabled>Select Brand</option>';
        if (!empty($brands)) {
            //print_r($brands);

            foreach ($brands as $value) {
                $option .= "<option value='" . $value['brand'] . "'";
                if (count($brands) == 1) {
                    $option .= " selected ";
                }
                $option .= " > ";
                $option .= $value['brand'] . "</option>";
            }
        } else {

            $option .= "<option value=''>Others</option>";
        }

        echo $option;
    }

    /**
     * @desc Used to get buyback order physical condition from ajax call
     * @param void
     * @return $option string
     */
    function get_bb_order_physical_condition() {
        //$this->check_BB_UserSession();
        $category = $this->input->post('category');
        $service_id = $this->input->post('service_id');
        $cp_id = $this->input->post('cp_id');
        $where = array('cp_id' => $cp_id,
            'service_id' => $service_id, 'category' => $category, 'physical_condition != " " ' => null, 'visible_to_cp' => '1');
        $select = "physical_condition";
        $physical_condition = $this->service_centre_charges_model->get_bb_charges($where, $select, TRUE);

        if (!empty($physical_condition)) {
            $option = '<option selected disabled>Select Physical Condition</option>';

            foreach ($physical_condition as $value) {
                $option .= "<option value='" . $value['physical_condition'] . "'";
                if (count($physical_condition) == 1) {
                    $option .= " selected ";
                }
                $option .= " > ";
                $option .= $value['physical_condition'] . "</option>";
            }

            echo $option;
        } else {
            echo "empty";
        }
    }

    /**
     * @desc Used to get buyback order working condition from ajax call
     * @param void
     * @return $option string
     */
    function get_bb_order_working_condition() {
        //$this->check_BB_UserSession();
        $category = $this->input->post('category');
        $service_id = $this->input->post('service_id');
        $physical_condition = $this->input->post('physical_condition');
        $cp_id = $this->input->post('cp_id');
        if (!empty($physical_condition)) {
            $where = array('cp_id' => $cp_id, 'service_id' => $service_id, 'category' => $category, 'physical_condition' => $physical_condition, 'visible_to_cp' => '1');
        } else {
            $where = array('cp_id' => $cp_id, 'service_id' => $service_id, 'category' => $category, 'visible_to_cp' => '1');
        }
        $select = "working_condition";
        $working_condition = $this->service_centre_charges_model->get_bb_charges($where, $select, TRUE);

        if (!empty($working_condition)) {
            $option = '<option selected disabled>Select Working Condition</option>';

            foreach ($working_condition as $value) {
                $option .= "<option value='" . $value['working_condition'] . "'";
                if (count($working_condition) == 1) {
                    $option .= " selected ";
                }
                $option .= " > ";
                $option .= $value['working_condition'] . "</option>";
            }

            echo $option;
        }
    }

    /**
     * @desc Used to check buyback order key from ajax call
     * @param void
     * @return string
     */
    function check_bb_order_key() {
        //$this->check_BB_UserSession();
        $category = $this->input->post('category');
        $service_id = $this->input->post('services');
        $physical_condition = $this->input->post('physical_condition');
        $working_condition = $this->input->post('working_condition');
        $brand = $this->input->post('brand');
        $city = $this->input->post('city');
        // $order_id = $this->input->post('order_id');
        $cp_id = $this->input->post('cp_id');
        $where = array('cp_id' => $cp_id,
            'service_id' => $service_id,
            'category' => $category,
            'physical_condition' => $physical_condition,
            'working_condition' => $working_condition,
            'brand' => $brand,
            'city' => $city);
        $select = "order_key, (cp_basic + cp_tax) as cp_charge";
        $order = $this->service_centre_charges_model->get_bb_charges($where, $select, TRUE);
        if (!empty($order)) {
            $array = array("order_key" => $order[0]['order_key'], "cp_charge" => $order[0]['cp_charge']);
            echo json_encode($array, true);
        } else {
            echo "Not Found";
        }
    }

    /**
     * @desc Used to process the  buyback update order form
     * @param void
     * @return void
     */
    function process_report_issue_bb_order_details() {
        $this->check_BB_UserSession();
        $request_data['select'] = "bb_cp_order_action.current_status";
        $request_data['length'] = -1;
        $request_data['where_in'] = array();
        $request_data['where'] = array('bb_cp_order_action.current_status' => _247AROUND_BB_IN_PROCESS,
            'bb_cp_order_action.internal_status NOT IN ("' . _247AROUND_BB_ORDER_NOT_RECEIVED_INTERNAL_STATUS . '")' => NULL,
            "bb_cp_order_action.partner_order_id" => $this->input->post('order_id'));
        $is_inProcess = $this->cp_model->get_bb_cp_order_list($request_data);

        if (!empty($is_inProcess)) {
            $this->session->set_userdata('error', 'Order Already Updated');
            redirect(base_url() . 'service_center/buyback/update_order_details/' . $this->input->post('order_id') . '/' .
                    $this->input->post('service_id') . '/' . $this->input->post('city') . '/' . $this->input->post('cp_id') . '/' . $this->input->post('current_status'));
        } else {
            //check for validation
            $this->form_validation->set_rules('order_id', 'Order Id', 'trim|required');
            $this->form_validation->set_rules('remarks', 'Remarks', 'trim|required');
            $this->form_validation->set_rules('order_working_condition', 'Order Working Condition', 'trim');
            $this->form_validation->set_rules('category', 'Category', 'trim|required');
            $this->form_validation->set_rules('cp_id', 'Collection Partner Id', 'trim|required');
            $this->form_validation->set_rules('claimed_price', 'Claimed Price', 'trim');

            if ($this->form_validation->run() === false) {
                $msg = "Please fill all required field";
                $this->session->set_userdata('error', $msg);
                redirect(base_url() . 'service_center/buyback/update_order_details/' . $this->input->post('order_id') . '/' . $this->input->post('service_id') . '/' . $this->input->post('city') . '/' . $this->input->post('cp_id') . '/' . $this->input->post('current_status'));
            } else {

                $order_id = $this->input->post('order_id');
                $remarks = $this->input->post('remarks');
                $working_condition = $this->input->post('order_working_condition');
                $category = $this->input->post('category');
                $cp_id = $this->input->post('cp_id');
                $cp_claimed_price = $this->input->post('claimed_price');
                $order_brand = $this->input->post('order_brand');
                $order_key = $this->input->post('partner_order_key');
                $physical_condition = $this->input->post('order_physical_condition');

                $upload_images = $this->buyback->process_bb_report_issue_upload_image($this->input->post());

                if (isset($upload_images['status']) && $upload_images['status'] == 'error') {
                    $this->session->set_userdata('error', $upload_images['msg']);
                    redirect(base_url() . 'service_center/buyback/update_order_details/' . $this->input->post('order_id') . '/' . $this->input->post('service_id') . '/' . $this->input->post('city') . '/' . $this->input->post('cp_id') . '/' . $this->input->post('current_status'));
                } else {
                    $physical_condition = isset($physical_condition) ? $physical_condition : '';
                    if (!empty($physical_condition)) {
                        $physical_condition = $physical_condition;
                    } else {
                        $physical_condition = '';
                    }

                    $data = array(
                        'category' => $category,
                        'physical_condition' => $physical_condition,
                        'working_condition' => $working_condition,
                        'remarks' => $remarks,
                        'brand' => $order_brand,
                        'current_status' => _247AROUND_BB_IN_PROCESS,
                        'internal_status' => _247AROUND_BB_Damaged_STATUS,
                        'order_key' => $order_key,
                        'cp_claimed_price' => $cp_claimed_price,
                        'acknowledge_date' => date('Y-m-d H:i:s'));

                    $where = array('partner_order_id' => $order_id, 'cp_id' => $cp_id);
                    //update bb_cp_action_table
                    $update_id = $this->cp_model->update_bb_cp_order_action($where, $data);
                    if ($update_id) {
                        log_message("info", __METHOD__ . "Cp Action table updated for order id: " . $order_id);
                        //update order details table
                        $mainTableData['is_delivered'] = 1;
                        if ($this->input->post('current_status') == _247AROUND_BB_IN_TRANSIT) {
                            $mainTableData['current_status'] = _247AROUND_BB_DELIVERED;
                            $mainTableData['internal_status'] = _247AROUND_BB_DELIVERED;
                            $mainTableData['delivery_date'] = date("Y-m-d");
                            $this->bb_model->update_bb_unit_details(array('partner_order_id' => $order_id), array("order_status" => _247AROUND_BB_DELIVERED));
                        }
                        $order_details_update_id = $this->bb_model->update_bb_order_details(array('partner_order_id' => $order_id, 'assigned_cp_id' => $cp_id), $mainTableData);
                        if (!empty($order_details_update_id)) {
                            if ($this->input->post('current_status') == _247AROUND_BB_IN_TRANSIT) {
                                $this->buyback->insert_bb_state_change($order_id, _247AROUND_BB_DELIVERED, "Delivered", $this->session->userdata('service_center_agent_id'), NULL, $cp_id);
                            }
                            $this->buyback->insert_bb_state_change($order_id, _247AROUND_BB_IN_PROCESS, $remarks, $this->session->userdata('service_center_agent_id'), NULL, $cp_id);
                            $this->session->set_userdata('success', 'Order has been updated successfully');
                            redirect(base_url() . 'service_center/buyback/bb_order_details');
                        } else {
                            $this->session->set_userdata('error', 'Oops!!! There are some issue in updating order. Please Try Again...');
                            redirect(base_url() . 'service_center/buyback/update_order_details/' . $this->input->post('order_id') . '/' . $this->input->post('service_id') . '/' . $this->input->post('city') . '/' . $this->input->post('cp_id') . '/' . $this->input->post('current_status'));
                        }
                    }
                }
            }
        }
    }

    function validate_claimed_price() {
        $cp_claimed_price = $this->input->post('claimed_price');
        $cp_basic_charge = $this->input->post('cp_basic_charge');
        $final_price = $cp_basic_charge * .30;

        if ($cp_claimed_price < $final_price) {
            $flag = FALSE;
        } else {
            $flag = TRUE;
        }

        return $flag;
    }

    /**
     * @desc Used to get the buyback order category
     * @param void
     * @return string
     */
    function get_bb_order_category_size() {
        //$this->check_BB_UserSession();
        $service_id = $this->input->post('product_service_id');
        $cp_id = $this->input->post('cp_id');
        $where = array('service_id' => $service_id, 'cp_id' => $cp_id);
        $select = "category";
        $categories = $this->service_centre_charges_model->get_bb_charges($where, $select, TRUE);
        $option = '<option selected disabled>Select Category</option>';
        if (!empty($categories)) {

            foreach ($categories as $value) {
                $option .= "<option value='" . $value['category'] . "'";
                $option .= " > ";
                $option .= $value['category'] . "</option>";
            }
        } else {
            $option .= "<option value='' disabled=''>No Data Found</option>";
        }

        echo $option;
    }

    /**
     * @desc Used to get  buyback form to update received bb order
     * @param $order_id string
     * @param $service_id string
     * @param $city string
     * @param $cp_id string
     * @return void
     */
    function update_received_bb_order($order_id, $service_id, $city, $cp_id) {
        $this->check_BB_UserSession();

        $data['order_id'] = rawurldecode($order_id);
        $data['service_id'] = rawurldecode($service_id);
        $data['city'] = rawurldecode($city);
        $data['cp_id'] = rawurldecode($cp_id);
        $data['agent_id'] = $this->session->userdata('service_center_agent_id');

        $response = $this->buyback->process_update_received_bb_order_details($data);

        if ($response['status'] === 'success') {
            $this->session->set_userdata('success', $response['msg']);
            redirect(base_url() . 'service_center/buyback/bb_order_details');
        } else if ($response['status'] === 'error') {
            $this->session->set_userdata('error', $response['msg']);
            redirect(base_url() . 'service_center/buyback/buyback/bb_order_details');
        }
    }

    /**
     * @desc Used to update not received bb order
     * @param $order_id string
     * @param $service_id string
     * @param $city string
     * @param $cp_id string
     * @return void
     */
    function update_not_received_bb_order($order_id, $service_id, $city) {
        $this->check_BB_UserSession();

        $data['order_id'] = rawurldecode($order_id);
        $data['service_id'] = rawurldecode($service_id);
        $data['city'] = rawurldecode($city);
        $data['cp_id'] = $this->session->userdata('service_center_id');
        $agent_id = $this->session->userdata('service_center_agent_id');
        $request_data['select'] = "bb_cp_order_action.current_status";
        $request_data['length'] = -1;
        $request_data['where_in'] = array();
        $request_data['where'] = array('bb_cp_order_action.current_status' => _247AROUND_BB_IN_PROCESS,
            "bb_cp_order_action.partner_order_id" => $data['order_id']);
        $is_inProcess = $this->cp_model->get_bb_cp_order_list($request_data);

        if (!empty($is_inProcess)) {

            $this->session->set_userdata('error', 'Order Already Updated');
            redirect(base_url() . 'service_center/buyback/bb_order_details');
        } else {
            $update_data = array('current_status' => _247AROUND_BB_IN_PROCESS,
                'internal_status' => _247AROUND_BB_ORDER_NOT_RECEIVED_INTERNAL_STATUS,
                'acknowledge_date' => date('Y-m-d H:i:s')
            );

            $update_where = array('partner_order_id' => $data['order_id'], 'cp_id' => $data['cp_id']);

            //update cp action table
            $update_id = $this->cp_model->update_bb_cp_order_action($update_where, $update_data);
            if ($update_id) {
                $this->buyback->insert_bb_state_change($data['order_id'], _247AROUND_BB_IN_PROCESS, _247AROUND_BB_ORDER_NOT_RECEIVED_INTERNAL_STATUS, $agent_id, Null, $data['cp_id']);

                $this->session->set_userdata('success', 'Order has been updated successfully');
                redirect(base_url() . 'service_center/buyback/bb_order_details');
            } else {
                $this->session->set_userdata('error', 'Oops!!! There are some issue in updating order. Please Try Again...');
                redirect(base_url() . 'service_center/buyback/bb_order_details');
            }
        }
    }

    /**
     * @desc It check if sc update gst form first then show its profile otherwies GST form
     */
    function gst_update_form() {
        $this->checkUserSession();
        log_message('info', __METHOD__ . $this->session->userdata('service_center_id'));
        $data = $this->reusable_model->get_search_result_data("service_centres", "id as service_center_id,company_name,address as company_address,pan_no as company_pan_number"
                . ",is_gst_doc as is_gst,gst_no as company_gst_number,gst_file as gst_certificate_file,signature_file", array("id" => $this->session->userdata('service_center_id')), NULL, NULL, NULL, NULL, NULL, array());
        $select = "stamp_file";
        $where['vendor_id'] = $this->session->userdata('service_center_id');
        $where['status'] = 1;
        $stamp_file = $this->vendor_model->fetch_sf_miscellaneous_data($select,$where);
        if(!empty($stamp_file)){
            $data[0]['stamp_file'] = $stamp_file[0]['stamp_file'];
        }else{
            $data[0]['stamp_file'] ='';
        }
        if ($data[0]['is_gst'] == 1 && !empty($data[0]['gst_certificate_file'])) {
            $this->load->view('service_centers/header');
            $this->load->view('service_centers/gst_details_view', $data[0]);
        } else {
            $this->load->view('service_centers/header');
            $this->load->view('service_centers/gst_update_form', $data[0]);
        }
    }

    /**
     * @desc This is used to insert gst for data.
     */
    function process_gst_update() {

        //$this->checkUserSession();
        log_message('info', __METHOD__ . $this->session->userdata('service_center_id'));
        $this->load->library('table');

        $this->form_validation->set_rules('company_name', 'Company Name', 'trim|required');
        $this->form_validation->set_rules('company_address', 'Company Address', 'trim|required');
        $this->form_validation->set_rules('pan_number', 'PAN NUmber', 'required|trim|min_length[10]|max_length[10]');
        $this->form_validation->set_rules('is_gst', 'Have You GST No.', 'required');
        if (empty($this->input->post('is_signature_aval'))) {
            $this->form_validation->set_rules('signature_file', 'Signature file', 'callback_upload_signature');
        }

        if ($this->form_validation->run() === false) {
            $this->gst_update_form();
        } else {

            $status_flag = true;
            $is_gst = $this->input->post('is_gst');
            $is_gst_number = NULL;
            $gst_file_name = NULL;
            $gst_number = NULL;

            if ($is_gst == 1) {
                $this->form_validation->set_rules('gst_number', 'Company GST Number', 'required|trim|min_length[15]|max_length[15]|regex_match[/^[0-9]{2}[a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}[0-9]{1}[a-zA-Z]{1}[a-zA-Z0-9]{1}/]');
                $this->form_validation->set_rules('file', 'Company GST File', 'callback_upload_gst_certificate_file');

                if ($this->form_validation->run() === false) {

                    $this->gst_update_form();
                    $status_flag = false;
                } else {
                    $is_gst_number = trim($this->input->post('gst_number'));
                    $gst_file_name = $this->input->post('gst_cer_file');
                    $gst_number = trim($this->input->post('gst_number'));
                }
            }

            if (!empty($this->input->post('is_signature_doc'))) {
                $gst_details['signature_file'] = trim($this->input->post('signature_file_name'));
                $sc['is_signature_doc'] = 1;
                $sc['signature_file'] = $gst_details['signature_file'];
            }


            // It not Accessed When validation failed above
            if ($status_flag) {
                $gst_details['service_center_id'] = $this->session->userdata('service_center_id');
                $gst_details['company_name'] = trim($this->input->post('company_name'));
                $gst_details['company_address'] = preg_replace('/\s+/', ' ', trim($this->input->post('company_address')));
                $gst_details['company_pan_number'] = trim($this->input->post('pan_number'));
                $gst_details['is_gst'] = $this->input->post('is_gst');
                $gst_details['company_gst_number'] = $gst_number;
                $gst_details['gst_certificate_file'] = $gst_file_name;
                $gst_details['create_date'] = date('Y-m-d H:i:s');

                $sc['is_gst_doc'] = $gst_details['is_gst'];
                $sc['gst_no'] = $gst_details['company_gst_number'];
                $sc['gst_file'] = $gst_details['gst_certificate_file'];

                $sc['agent_id'] = _247AROUND_DEFAULT_AGENT;
                $this->vendor_model->edit_vendor($sc, $this->session->userdata('service_center_id'));

                $template = array(
                    'table_open' => '<table border="1" cellpadding="2" cellspacing="1" class="mytable">'
                );

                $this->table->set_template($template);

                $this->table->set_heading(array('SC Name', 'Company Name', 'Company Address', 'Pan', 'IS GST', 'GST NUmber', 'GST FILE', 'Signature File'));
                $this->table->add_row($this->session->userdata('service_center_name'), $gst_details['company_name'], $gst_details['company_address'], $gst_details['company_pan_number'], !empty($gst_details['is_gst']) ? "YES" : "NO", $gst_details['company_gst_number'], !empty($sc['gst_file']) ? "https://s3.amazonaws.com/bookings-collateral/vendor-partner-docs/" . $sc['gst_file'] : '', !empty($sc['signature_file']) ? "https://s3.amazonaws.com/bookings-collateral/vendor-partner-docs/" . $sc['signature_file'] : '');

                $to = NITS_ANUJ_EMAIL_ID;

                $rm_details = $this->vendor_model->get_rm_sf_relation_by_sf_id($this->session->userdata('service_center_id'));
                $cc = $rm_details[0]['official_email'];

                $subject = "GST Form Updated By " . $this->session->userdata('service_center_name');
                $message = "";
                $message .= $this->table->generate();

//                $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, "", $subject, $message, "",GST_FORM_UPDATED);

                redirect(base_url() . "service_center/gst_details");
            }
        }
    }

    function upload_signature() {
        $allowedExts = array("png", "jpg", "jpeg", "JPG", "JPEG", "bmp", "BMP", "gif", "GIF", "PNG");
        $temp = explode(".", $_FILES["signature_file"]["name"]);
        $extension = end($temp);
        if (($_FILES['signature_file']['error'] != 4) && !empty($_FILES['signature_file']['tmp_name'])) {
            if ($_FILES["signature_file"]["name"] != null) {
                if (($_FILES["signature_file"]["size"] < 2e+6) && in_array($extension, $allowedExts)) {
                    if ($_FILES["signature_file"]["error"] > 0) {
                        $this->form_validation->set_message('upload_signature', $_FILES["signature_file"]["error"]);
                        return FALSE;
                    } else {
                        $pic = md5(uniqid(rand()));
                        $picName = $pic . "." . $extension;
                        $_POST['signature_file_name'] = $picName;

                        $bucket = BITBUCKET_DIRECTORY;
                        $directory = "vendor-partner-docs/" . $picName;
                        $this->s3->putObjectFile($_FILES["signature_file"]["tmp_name"], $bucket, $directory, S3::ACL_PUBLIC_READ);
                        $_POST['is_signature_doc'] = 1;
                        return TRUE;
                    }
                } else {
                    $this->form_validation->set_message('upload_signature', 'File size or File type is not supported.Allowed extentions are "png", "jpg", "jpeg". Maximum file size is 2MB.');
                    return FALSE;
                }
            }
        } else {

            $this->form_validation->set_message('upload_signature', 'Please Attach Signature Image File');
            return false;
        }
    }

    /**
     * @desc Upload GST Certificate FIle to S3
     * @return String
     */
    function upload_gst_certificate_file() {
        log_message('info', __METHOD__ . " :" . $this->session->userdata('service_center_id'));

        if (($_FILES['file']['error'] != 4) && !empty($_FILES['file']['tmp_name'])) {

            $tmpFile = $_FILES['file']['tmp_name'];
            $extention = explode(".", $_FILES['file']['name'])[1];
            $gst_file = $this->session->userdata('service_center_id') . '-gst-'
                    . substr(md5(uniqid(rand(0, 9))), 0, 10) . "." . $extention;
            //move_uploaded_file($tmpFile, TMP_FOLDER . $support_file_name);
            //Upload files to AWS
            $bucket = BITBUCKET_DIRECTORY;
            $directory_xls = "vendor-partner-docs/" . $gst_file;
            $upload_file_status = $this->s3->putObjectFile($tmpFile, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
            if ($upload_file_status) {
                //Logging success for file uppload
                log_message('info', __METHOD__ . 'GST Certificate File Uploaded: ' . $this->session->userdata('service_center_id'));
                $_POST['gst_cer_file'] = $gst_file;
                return true;
            } else {
                //Logging success for file uppload
                log_message('info', __METHOD__ . 'Error In uploading sGST Certificate : ' . $this->session->userdata('service_center_id'));
                $this->form_validation->set_message('upload_gst_certificate_file', 'Please Valid GST File.');

                return false;
            }
        } else {

            $this->form_validation->set_message('upload_gst_certificate_file', 'Please Attach GST Certificate File.');
            return false;
        }
    }

    function get_vendor_rating() {
        $rating_data = $this->service_centers_model->get_vendor_rating_data($this->session->userdata('service_center_id'));
        if (!empty($rating_data)) {
            echo $rating_data[0]['rating'];
        } else {
            echo '0';
        }
    }

    /**
     * @desc Used to get data as requested and also search 
     */
    function get_bb_order_details() {
        //log_message("info",__METHOD__);
        $data = array();
        switch ($this->input->post('status')) {
            case 0:
                $data = $this->get_delivered_data();
                break;
            case 1:
                $data = $this->get_pending_data();
                break;
            case 2:
                $data = $this->get_acknowledge_data();
                break;
            case 3:
                $data = $this->get_inprocess_order_data();
                break;
        }

        $post = $data['post'];
        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->cp_model->cp_order_list_count_all($post),
            "recordsFiltered" => $this->cp_model->cp_order_list_count_filtered($post),
            "data" => $data['data'],
        );
        unset($post);
        unset($data);
        echo json_encode($output);
    }

    /**
     * @desc Used to get  delivered buyback data
     * @param void
     * @return array
     */
    function get_delivered_data() {
        //log_message("info",__METHOD__);      
        $post = $this->get_post_view_data();
        $post['where'] = array('assigned_cp_id' => $this->session->userdata('service_center_id'),
            'bb_cp_order_action.current_status' => 'Pending', 'bb_order_details.internal_status' => 'Delivered', 'bb_order_details.current_status' => 'Delivered');
        $post['where_in'] = array();
        $post['column_order'] = array(NULL, 'bb_order_details.partner_order_id', 'bb_order_details.partner_tracking_id', 'services', 'category',
            'cp_basic_charge', 'category', 'delivery_date', NULL, NULL);
        $post['column_search'] = array('bb_order_details.partner_order_id', 'bb_order_details.partner_tracking_id', 'services', 'city',
            'order_date', 'delivery_date', 'bb_cp_order_action.current_status');
        $list = $this->cp_model->get_bb_cp_order_list($post);

        $data = array();
        $no = $post['start'];
        foreach ($list as $order_list) {
            $no++;
            $row = $this->get_delivered_table_data($order_list, $no);
            $data[] = $row;
        }

        return array(
            'data' => $data,
            'post' => $post
        );
    }

    /**
     * @desc Used to get pending buyback data
     * @param void
     * @return array
     */
    function get_pending_data() {
        //log_message("info",__METHOD__);
        $post = $this->get_post_view_data();
        $post['where'] = array('assigned_cp_id' => $this->session->userdata('service_center_id'),
            'bb_cp_order_action.current_status' => 'Pending');
        $post['where_in'] = array('bb_order_details.internal_status' => array('In-Transit', 'New Item In-transit', 'Attempted'),
            'bb_order_details.current_status' => array('In-Transit', 'New Item In-transit', 'Attempted'));
        $post['column_order'] = array(NULL, 'bb_order_details.partner_order_id', 'bb_order_details.partner_tracking_id', 'services', 'category',
            'order_date', 'cp_basic_charge', NULL, NULL);
        $post['column_search'] = array('bb_order_details.partner_order_id', 'bb_order_details.partner_tracking_id', 'services', 'bb_unit_details.category', 'order_date', 'cp_basic_charge');
        $list = $this->cp_model->get_bb_cp_order_list($post);
        $data = array();
        $no = $post['start'];
        foreach ($list as $order_list) {
            $no++;
            $row = $this->get_pending_table_data($order_list, $no);
            $data[] = $row;
        }

        return array(
            'data' => $data,
            'post' => $post
        );
    }

    /**
     * @desc Used to get acknowledge buyback data
     * @param void
     * @return array
     */
    function get_acknowledge_data() {
      
        $post = $this->get_post_view_data();
        if(!empty($this->input->post('auto_acknowledge')) && $this->input->post('auto_acknowledge')==1){
            $order_by_auto = 'bb_order_details.auto_acknowledge_date';
        }else{
            $order_by_auto = 'bb_order_details.acknowledge_date';
        }
        $post['where'] = array('assigned_cp_id' => $this->session->userdata('service_center_id'), "bb_order_details.auto_acknowledge" => $this->input->post('auto_acknowledge'));
        $post['where_in'] = array('bb_cp_order_action.current_status' => array(_247AROUND_BB_DELIVERED, _247AROUND_BB_NOT_DELIVERED, _247AROUND_BB_Damaged_STATUS),
            'bb_cp_order_action.internal_status' => array(_247AROUND_BB_DELIVERED, _247AROUND_BB_NOT_DELIVERED, _247AROUND_BB_247APPROVED_STATUS, _247AROUND_BB_Damaged_STATUS));
        $post['column_order'] = array(NULL, 'bb_order_details.partner_order_id', 'bb_order_details.partner_tracking_id', 'services', 'category',
            'order_date', 'delivery_date', 'cp_basic_charge', NULL, 'bb_cp_order_action.current_status',$order_by_auto);
        $post['column_search'] = array('bb_order_details.partner_order_id', 'bb_order_details.partner_tracking_id', 'services', 'city',
            'order_date', 'delivery_date', 'bb_cp_order_action.current_status');
        $list = $this->cp_model->get_bb_cp_order_list($post);
        $data = array();
        $no = $post['start'];
        foreach ($list as $order_list) {
            $no++;
            $row = $this->get_acknowledge_table_data($order_list, $no, false);
            $data[] = $row;
        }

        return array(
            'data' => $data,
            'post' => $post
        );
    }

    /**
     * @desc This function used to get inprocess buyback data
     */
    function get_inprocess_order_data() {
        $post = $this->get_post_view_data();
        $post['where'] = array('assigned_cp_id' => $this->session->userdata('service_center_id'));
        $post['where_in'] = array('bb_cp_order_action.current_status' => array('InProcess'),
            'bb_cp_order_action.internal_status' => array(_247AROUND_BB_DELIVERED, _247AROUND_BB_NOT_DELIVERED, _247AROUND_BB_247APPROVED_STATUS, _247AROUND_BB_Damaged_STATUS, _247AROUND_BB_ORDER_NOT_RECEIVED_INTERNAL_STATUS));
        $post['column_order'] = array(NULL, 'bb_order_details.partner_order_id', 'bb_order_details.partner_tracking_id', 'services', 'category',
            'order_date', 'delivery_date', 'cp_basic_charge', NULL, 'bb_cp_order_action.current_status');
        $post['column_search'] = array('bb_order_details.partner_order_id', 'bb_order_details.partner_tracking_id', 'services', 'city',
            'order_date', 'delivery_date', 'bb_cp_order_action.current_status');
        $list = $this->cp_model->get_bb_cp_order_list($post);
        $data = array();
        $no = $post['start'];
        foreach ($list as $order_list) {
            $no++;
            $row = $this->get_acknowledge_table_data($order_list, $no, true);
            $data[] = $row;
        }

        return array(
            'data' => $data,
            'post' => $post
        );
    }

    /**
     * @desc Used to get  delivered buyback data table
     * @param $order_list
     * @param $no
     * @return array
     */
    function get_delivered_table_data($order_list, $no) {
        //log_message("info", __METHOD__);
        $row = array();
        $datetime1 = date_create(date("Y-m-d"));
        $datetime2 = date_create(date('Y-m-d', strtotime($order_list->auto_acknowledge_date)));

        $interval = date_diff($datetime1, $datetime2);
        $ack_days = $interval->days;
        $days = NULL;
        if ($interval->invert == 1) {
            $days = -$ack_days;
        }
        $row[] = $no;
        $row[] = "<a target='_blank' href='" . base_url() . "service_center/buyback/view_bb_order_details/" .
                $order_list->partner_order_id . "'>$order_list->partner_order_id</a>";
        $row[] = $order_list->partner_tracking_id;
        $row[] = $order_list->services;
        $row[] = $order_list->category;
        $row[] = ($order_list->cp_basic_charge + $order_list->cp_tax_charge);
        $row[] = ($order_list->cp_claimed_price);
        $row[] = $order_list->order_date;
        $row[] = $order_list->delivery_date;
        $row[] = "<p style='color:red;' class='blinking'>" . $ack_days . "</p>";
        $row[] = "<div class='truncate_text' data-toggle='popover' title='" . $order_list->admin_remarks . "'>$order_list->admin_remarks</div>";
        $a = "<div class='dropdown'>
                            <button class='btn btn-default dropdown-toggle' type='button' id='menu1' data-toggle='dropdown'>Actions
                            <span class='caret'></span></button>
                            <ul class='dropdown-menu' role='menu' aria-labelledby='menu1'>
                              <li role='presentation'><a role='menuitem' tabindex='-1' onclick=showConfirmDialougeBox('" . base_url() . "service_center/buyback/update_received_bb_order/" . rawurlencode($order_list->partner_order_id) . "/" . rawurlencode($order_list->service_id) . "/" . rawurlencode($order_list->city) . "/" . rawurlencode($order_list->assigned_cp_id) . "')>Received</a></li>";
        ;
        //  if ($days < NO_OF_DAYS_NOT_SHOW_NOT_RECEIVED_BUTTON) {

        $a .= "<li role='presentation'><a role='menuitem' tabindex='-1' onclick=showConfirmDialougeBox('" . base_url() . "service_center/buyback/update_not_received_bb_order/" . rawurlencode($order_list->partner_order_id) . "/" . rawurlencode($order_list->service_id) . "/" . rawurlencode($order_list->city) . "/" . rawurlencode($order_list->assigned_cp_id) . "')>Not Received</a></li>";
        //  }
        if($order_list->service_id != _247AROUND_TV_SERVICE_ID){
            $a .= "<li role='presentation'><a role='menuitem' tabindex='-1' target='_blank' href='" . base_url() . "service_center/buyback/update_order_details/" . rawurlencode($order_list->partner_order_id) . "/" . rawurlencode($order_list->service_id) . "/" . rawurlencode($order_list->city) . "/" . rawurlencode($order_list->assigned_cp_id) . "/" . rawurlencode(_247AROUND_BB_DELIVERED) . "'>Broken/Wrong Product</a></li>";
        }
         $a .= " </ul></div>";
        $row[] = $a;

        return $row;
    }

    /**
     * @desc Used to get pending buyback data table
     * @param $order_list
     * @param $no
     * @return array
     */
    function get_pending_table_data($order_list, $no) {
        //log_message("info", __METHOD__);
        $row = array();
        $row[] = $no;
        $row[] = "<a target='_blank' href='" . base_url() . "service_center/buyback/view_bb_order_details/" .
                $order_list->partner_order_id . "'>$order_list->partner_order_id</a>";
        $row[] = $order_list->partner_tracking_id;
        $row[] = $order_list->services;
        $row[] = $order_list->category;
        $row[] = $order_list->order_date;
        $row[] = ($order_list->cp_basic_charge + $order_list->cp_tax_charge);
        $a = "<div class='dropdown'>
                            <button class='btn btn-default dropdown-toggle' type='button' id='menu1' data-toggle='dropdown'>Actions
                            <span class='caret'></span></button>
                            <ul class='dropdown-menu' role='menu' aria-labelledby='menu1'>
                              <li role='presentation'><a role='menuitem' tabindex='-1' onclick=showConfirmDialougeBox('" . base_url() . "service_center/buyback/update_received_bb_order/" . rawurlencode($order_list->partner_order_id) . "/" . rawurlencode($order_list->service_id) . "/" . rawurlencode($order_list->city) . "/" . rawurlencode($order_list->assigned_cp_id) . "')>Received</a></li>";
        if($order_list->service_id !=_247AROUND_TV_SERVICE_ID){
            $a .= "<li role='presentation'><a role='menuitem' tabindex='-1' target='_blank' href='" . base_url() . "service_center/buyback/update_order_details/" . rawurlencode($order_list->partner_order_id) . "/" . rawurlencode($order_list->service_id) . "/" . rawurlencode($order_list->city) . "/" . rawurlencode($order_list->assigned_cp_id) . "/" . rawurlencode(_247AROUND_BB_IN_TRANSIT) . "'>Broken/Wrong Product</a></li>";
        }
        $a .= "</ul></div>";
        
        $row[] = $a;
        return $row;
    }

    /**
     * @desc Used to get acknowledge buyback data table
     * @param $order_list
     * @param $no
     * @return array
     */
    function get_acknowledge_table_data($order_list, $no, $inprocess) {
        //log_message("info", __METHOD__);
        $row = array();
        $row[] = $no;
        $row[] = "<a target='_blank' href='" . base_url() . "service_center/buyback/view_bb_order_details/" .
                $order_list->partner_order_id . "'>$order_list->partner_order_id</a>";
        $row[] = $order_list->partner_tracking_id;
        $row[] = $order_list->services;
        $row[] = $order_list->category;
        $row[] = $order_list->order_date;
        $row[] = $order_list->delivery_date;
        $row[] = ($order_list->cp_basic_charge + $order_list->cp_tax_charge);
        $row[] = ($order_list->cp_claimed_price);
        $row[] = $order_list->current_status . "<b> (" . $order_list->internal_status . " )</b>";
        if(!$inprocess) {
            if(!empty($order_list->auto_acknowledge)) {
                $row[] = $order_list->auto_acknowledge_date;
            } else {
                $row[] = $order_list->acknowledge_date;
            }
        }
        if ($inprocess) {
            switch ($order_list->internal_status) {
                case _247AROUND_BB_NOT_DELIVERED:
                case _247AROUND_BB_ORDER_NOT_RECEIVED_INTERNAL_STATUS:
                    $a = "<div class='dropdown'>
                            <button class='btn btn-default dropdown-toggle' type='button' id='menu1' data-toggle='dropdown'>Actions
                            <span class='caret'></span></button>
                            <ul class='dropdown-menu' role='menu' aria-labelledby='menu1'>
                              <li role='presentation'><a role='menuitem' tabindex='-1' onclick=showConfirmDialougeBox('" . base_url() . "service_center/buyback/update_received_bb_order/" . rawurlencode($order_list->partner_order_id) . "/" . rawurlencode($order_list->service_id) . "/" . rawurlencode($order_list->city) . "/" . rawurlencode($order_list->assigned_cp_id) . "')>Received</a></li>";
                    if($order_list->service_id !=_247AROUND_TV_SERVICE_ID){
                        $a .="<li role='presentation'><a role='menuitem' tabindex='-1' target='_blank' href='" . base_url() . "service_center/buyback/update_order_details/" . rawurlencode($order_list->partner_order_id) . "/" . rawurlencode($order_list->service_id) . "/" . rawurlencode($order_list->city) . "/" . rawurlencode($order_list->assigned_cp_id) . "/" . rawurlencode($order_list->current_status) . "'>Broken/Wrong Product</a></li>";
                    }
                    $a .= "</ul>
                          </div>";
                    $row[] = $a;
                    break;
                default:
                    $row[] = "";
            }
        }

        return $row;
    }

    /**
     * @desc Used to get  post data from the datatable
     * @param void
     * @return $post array()
     */
    function get_post_view_data() {
        //log_message("info",__METHOD__);
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $search = $this->input->post('search');
        $post['search_value'] = $search['value'];
        $post['order'] = $this->input->post('order');
        $post['draw'] = $this->input->post('draw');
        $post['status'] = $this->input->post('status');

        return $post;
    }

    /**
     * @desc Used to get the bb price list according to cp
     * @param void
     * @return void
     */
    function show_bb_price_list() {
        $this->check_BB_UserSession();
        $select = 'service_id,s.services';
        $where['cp_id'] = $this->session->userdata('service_center_id');
        $data['appliance_list'] = $this->bb_model->get_bb_price_data($select, $where, true, true);
        $this->load->view('service_centers/header');
        $this->load->view('service_centers/show_show_bb_price_list', $data);
    }

    /**
     * @desc This function is used to the filtered charges data from bb_charges table
     * @param void()
     * @return void()
     */
    function get_bb_price_list() {
        $response = $this->buyback->get_bb_price_list($this->input->post());
        echo $response;
    }

    function get_bb_cp_charges($cp_id) {

        $where['length'] = -1;
        $data = array();
        //get delivered charges by month
        $where['where_in'] = array('bb_order_details.current_status' => array('Delivered', 'Completed'));
        $select = "SUM(CASE WHEN ( bb_unit_details.cp_claimed_price > 0) 
                THEN (bb_unit_details.cp_claimed_price) 
                ELSE (bb_unit_details.cp_basic_charge) END ) as cp_delivered_charge, count(bb_order_details.partner_order_id) as total_delivered_order";
        for ($i = 0; $i < 3; $i++) {
            if ($i == 0) {
                //$delivery_date = "bb_order_details.delivery_date >=  '" . date('Y-m-01') . "'";
                $delivery_date = "(CASE WHEN bb_order_details.acknowledge_date IS NOT Null THEN `bb_order_details`.`acknowledge_date` >= '" . date('Y-m-01') . "' ELSE `bb_order_details`.`delivery_date` >= '" . date('Y-m-01') . "'  END)";
                $select .= ", date(now()) As month";
            } else if ($i == 1) {
                $delivery_date = "(CASE WHEN bb_order_details.acknowledge_date IS NOT Null THEN bb_order_details.acknowledge_date  >=  DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m-01')
			    AND bb_order_details.acknowledge_date < DATE_FORMAT(NOW() ,'%Y-%m-01') ELSE bb_order_details.delivery_date  >=  DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m-01')
			    AND bb_order_details.delivery_date < DATE_FORMAT(NOW() ,'%Y-%m-01') END) ";
                $select .= ", DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m-01') as month";
            } else if ($i == 2) {
                $delivery_date = "(CASE WHEN bb_order_details.acknowledge_date IS NOT Null THEN bb_order_details.acknowledge_date  >=  DATE_FORMAT(NOW() - INTERVAL 2 MONTH, '%Y-%m-01')
			    AND bb_order_details.acknowledge_date < DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m-01') ELSE bb_order_details.delivery_date  >=  DATE_FORMAT(NOW() - INTERVAL 2 MONTH, '%Y-%m-01')
			    AND bb_order_details.delivery_date < DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m-01') END)";
                $select .= ", DATE_FORMAT(NOW() - INTERVAL 2 MONTH, '%Y-%m-01') as month";
            }

            $where['where'] = array('assigned_cp_id' => $cp_id, "$delivery_date" => NULL);
            $cp_delivered_charge[$i] = $this->bb_model->get_bb_order_list($where, $select);
        }

        //get total delivered charges data
        $where['where'] = array('assigned_cp_id' => $cp_id);


        //get in_transit data by month
        $where['where_in'] = array('bb_order_details.current_status' => array('In-Transit', 'New Item In-transit', 'Attempted'));
        $select_in_transit = "SUM(CASE WHEN ( bb_unit_details.cp_claimed_price > 0) 
                THEN (bb_unit_details.cp_claimed_price) 
                ELSE (bb_unit_details.cp_basic_charge) END ) as cp_in_transit_charge,count(bb_order_details.partner_order_id) as total_inTransit_order";
        for ($i = 0; $i < 3; $i++) {
            if ($i == 0) {
                $in_transit_date = "bb_order_details.order_date >=  '" . date('Y-m-01') . "'";
                $select_in_transit .= ", date(now()) As month";
            } else if ($i == 1) {
                $in_transit_date = "bb_order_details.order_date  >=  DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m-01')
			    AND bb_order_details.order_date < DATE_FORMAT(NOW() ,'%Y-%m-01')  ";
                $select_in_transit .= ", DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m-01') as month";
            } else if ($i == 2) {
                $in_transit_date = "bb_order_details.order_date  >=  DATE_FORMAT(NOW() - INTERVAL 2 MONTH, '%Y-%m-01')
			    AND bb_order_details.order_date < DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m-01')";
                $select_in_transit .= ", DATE_FORMAT(NOW() - INTERVAL 2 MONTH, '%Y-%m-01') as month";
            }

            $where['where'] = array('assigned_cp_id' => $cp_id, "$in_transit_date" => NULL);
            $cp_in_transit_charge[$i] = $this->bb_model->get_bb_order_list($where, $select_in_transit);
        }
        $amount_cr_deb = $this->miscelleneous->get_cp_buyback_credit_debit($cp_id);
        $data['delivered_charges'] = $cp_delivered_charge;
        $data['in_transit_charges'] = $cp_in_transit_charge;
        $data['total_charges'] = $amount_cr_deb;
        $this->load->view('service_centers/show_bb_charges_summary', $data);
    }

    /**
     * @desc Used to get data as requested and also search 
     */
    function get_sf_data() {
        $data = array();
        switch ($this->input->post('status')) {
            case 0:
                $data = $this->get_sf_charges_data();
                break;
        }

        $post = $data['post'];
        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->service_centre_charges_model->count_all_charges($post),
            "recordsFiltered" => $this->service_centre_charges_model->count_filtered_charges($post),
            "data" => $data['data'],
        );

        unset($post);
        unset($data);
        echo json_encode($output);
    }

    /**
     * @desc Used to get sf charges data
     * @param void
     * @return array
     */
    function get_sf_charges_data() {
        $this->checkUserSession();
        //Getting SC ID from session
        $service_center_id = $this->session->userdata('service_center_id');
        if (!empty($service_center_id)) {
            //Getting SF Details
            //$sc_details = $this->vendor_model->getVendorContact($service_center_id);

            $post = $this->get_post_view_data();
            $new_post = $this->get_filterd_post_data($post, '');

            $select = "service_centre_charges.brand,service_centre_charges.category,service_centre_charges.capacity,"
                    . "service_centre_charges.service_category,service_centre_charges.vendor_total,service_centre_charges.partner_id, service_centre_charges.vendor_basic_charges, "
                    . "service_centre_charges.vendor_tax_basic_charges, service_centre_charges.customer_net_payable,service_centre_charges.pod, services.services as product";

            //Getting Charges Data
            $list = $this->service_centre_charges_model->get_service_centre_charges_by_any($new_post, $select);
            $data = array();
            $no = $post['start'];
            foreach ($list as $charges_list) {
                $no++;
                $row = $this->get_charges_list_table($charges_list, $no);
                $data[] = $row;
            }
        }

        return array(
            'data' => $data,
            'post' => $new_post
        );
    }

    /**
     *  @desc : This function is used to make filter logic for pagination
     *  @param : $post string
     *  @param : $state string
     *  @return : $post Array()
     */
    private function get_filterd_post_data($post, $state) {
        $product = $this->input->post('product');
        $category = $this->input->post('category');
        $capacity = $this->input->post('capacity');
        $service_category = $this->input->post('service_category');

        if (!empty($state)) {
            $post['where'] = array('tax_rates.state' => $state);
        }
        if (!empty($product)) {
            $post['where']['service_id'] = $product;
        }
        if (!empty($category)) {
            $post['where']['category'] = $category;
        }
        if (!empty($capacity)) {
            $post['where']['capacity'] = $capacity;
        }
        if (!empty($service_category)) {
            $post['where']['service_category'] = $service_category;
        }

        $post['column_order'] = array(NULL, NULL, 'brand', 'category', 'capacity', 'service_category', 'vendor_total', NULL, NULL, 'customer_net_payable', 'pod');
        $post['column_search'] = array('service_centre_charges.brand');

        return $post;
    }

    function get_charges_list_table($charges_list, $no) {
        $row = array();

        //Getting Details from Booking Sources
//        $booking_sources = $this->partner_model->get_booking_sources_by_price_mapping_id($charges_list->partner_id);
//        $code_source = $booking_sources[0]['code'];
        //Calculating vendor base charge 
        // $vendor_base_charge = $charges_list->vendor_total / (1 + ($charges_list->rate / 100));
        //Calculating vendor tax - [Vendor Total - Vendor Base Charge]
        // $vendor_tax = $charges_list->vendor_total - $vendor_base_charge;

        $row[] = $no;
        //$row[] = $code_source;
        $row[] = $charges_list->product;
        $row[] = $charges_list->brand;
        $row[] = $charges_list->category;
        $row[] = $charges_list->capacity;
        $row[] = $charges_list->service_category;
        $row[] = round($charges_list->vendor_basic_charges, 0);
        $row[] = round($charges_list->vendor_tax_basic_charges, 0);
        $row[] = round($charges_list->vendor_total, 0);
        $row[] = round($charges_list->customer_net_payable, 0);
        $row[] = $charges_list->pod;

        return $row;
    }

    function view_bb_order_details($partner_order_id) {
        $data['partner_order_id'] = $partner_order_id;
        $this->load->view('service_centers/header');
        $this->load->view('service_centers/view_bb_order_details', $data);
    }

    /**
     * @desc Used to get the order details data to take action 
     * @param $partner_order_id string
     * @return $data json
     */
    function get_bb_order_details_data($partner_order_id) {
        $this->check_BB_UserSession();
        log_message("info", __METHOD__);
        if ($partner_order_id) {
            $data = $this->bb_model->get_bb_order_details(
                    array('bb_order_details.partner_order_id' => $partner_order_id), 'bb_order_details.*, name as cp_name, public_name as partner_name');
            print_r(json_encode($data));
        }
    }

    /**
     * @desc Used to get order history data
     * @param $partner_order_id string
     * @return $data json
     */
    function get_bb_order_history_details($partner_order_id) {
        log_message("info", __METHOD__);
        if ($partner_order_id) {
            $data = $this->bb_model->get_bb_order_history($partner_order_id);
            print_r(json_encode($data));
        }
    }

    /**
     * @desc Used to get the order appliance details
     * @param $partner_order_id string
     * @return $data json
     */
    function get_bb_order_appliance_details($partner_order_id) {
        log_message("info", __METHOD__);
        if ($partner_order_id) {
            $select = 'bb_unit.category, bb_unit.physical_condition, 
                bb_unit.working_condition,
                round(bb_unit.cp_basic_charge + bb_unit.cp_tax_charge) as cp_tax,
                bb_unit.partner_sweetner_charges,s.services as service_name,bb_unit.cp_claimed_price,bb_unit.cp_invoice_id';
            $data = $this->bb_model->get_bb_order_appliance_details(array('partner_order_id' => $partner_order_id), $select);
            print_r(json_encode($data));
        }
    }

    /**
     * @desc Used to get sf escalation percentage
     * @param $sf_id string
     * @return $escalation_per string
     */
    function get_sf_escalation($sf_id) {
        if (!empty($sf_id)) {
            $total_escalation_per = "";
            $current_month_escalation_per = "";
            $total_booking = $this->reusable_model->get_search_query('booking_details', 'count(booking_id) AS total_booking', array('assigned_vendor_id' => $sf_id), NULL, NULL, NULL, NULL, NULL)->result_array();
            $total_escalation = $this->reusable_model->get_search_query('vendor_escalation_log', 'count(booking_id) AS total_escalation', array('vendor_id' => $sf_id), NULL, NULL, NULL, NULL, NULL)->result_array();
            if (!empty($total_booking[0]['total_booking'])) {
                $total_escalation_per = ($total_escalation[0]['total_escalation'] * 100) / $total_booking[0]['total_booking'];
            }

            $current_month_booking = $this->reusable_model->get_search_query('booking_details', 'count(booking_id) AS total_booking', array('assigned_vendor_id' => $sf_id, "month(STR_TO_DATE(booking_details.booking_date,'%Y-%m-%d')) = month(now()) AND year(STR_TO_DATE(booking_details.booking_date,'%Y-%m-%d')) = year(now())" => NULL), NULL, NULL, NULL, NULL, NULL)->result_array();
            $current_month__escalation = $this->reusable_model->get_search_query('vendor_escalation_log', 'count(booking_id) AS total_escalation', array('vendor_id' => $sf_id, "month(create_date) = month(now()) AND year(create_date) = year(now())" => NULL), NULL, NULL, NULL, NULL, NULL)->result_array();
            if (!empty($current_month_booking[0]['total_booking'])) {
                $current_month_escalation_per = ($current_month__escalation[0]['total_escalation'] * 100) / $current_month_booking[0]['total_booking'];
            }

            $response['total_escalation_per'] = !empty($total_escalation_per) ? sprintf("%1\$.2f", $total_escalation_per) : 0;
            $response['current_month_escalation_per'] = !empty($current_month_escalation_per) ? sprintf("%1\$.2f", $current_month_escalation_per) : 0;

            echo json_encode($response);
        } else {
            echo 'empty';
        }
    }

    /**
     * @desc This is uesd to for buyback search. It will get the data from Order ID/Tracking ID
     */
    function search_for_buyback() {
        $this->check_BB_UserSession();
        //$search_data =  preg_replace('/[^A-Za-z0-9-]/', '', trim($this->input->post('search')));
        $search_data = $this->input->post('search');
        if (strpos($search_data, ',')) {
            $search_value = explode(',', $search_data);
        } else {
            $search_value = explode(" ", $search_data);
        }
        $post['column_search'] = array('bb_order_details.partner_order_id', 'bb_order_details.partner_tracking_id');
        $post['where'] = array('assigned_cp_id' => $this->session->userdata('service_center_id'));
        $post['where_in'] = array();
        $post['column_order'] = array();
        $post['length'] = -1;
        $list['list'] = array();
        foreach ($search_value as $value) {
            if (!empty($value)) {
                $post['search_value'] = trim($value);
                $data = $this->cp_model->get_bb_cp_order_list($post);
                if (!empty($data)) {
                    array_push($list['list'], $data[0]);
                }
            }
        }
        $this->load->view('service_centers/search_for_buyback', $list);
    }

    public function get_contact_us_page() {
        //$this->checkUserSession();
        //$data['rm_details'] = $this->vendor_model->get_rm_sf_relation_by_sf_id($this->session->userdata('service_center_id'));
        $data['new_rm_details'] = $this->vendor_model->get_rm_contact_details_by_sf_id($this->session->userdata('service_center_id'));
        $data['new_asm_details'] = $this->vendor_model->get_asm_contact_details_by_sf_id($this->session->userdata('service_center_id'));
        $this->load->view('service_centers/contact_us', $data);
    }

    /**
     * @desc This is used to Approve Spare Estimate by SF
     * @param String $booking_id
     */
    function approve_oow($booking_id) {
        log_message("info", __METHOD__ . "Enterring");
        if (!empty($booking_id)) {
            $req['where'] = array("spare_parts_details.booking_id" => $booking_id, "status" => SPARE_OOW_EST_GIVEN);
            $req['length'] = -1;
            $req['select'] = "spare_parts_details.id, "
                    . "spare_parts_details.is_micro_wh,"
                    . "spare_parts_details.entity_type,"
                    . "spare_parts_details.quantity,"
                    . "spare_parts_details.partner_id,"
                    . "spare_parts_details.requested_inventory_id,"
                    . "spare_parts_details.original_inventory_id,"
                    . "spare_parts_details.model_number,"
                    . "spare_parts_details.parts_requested,"
                    . "spare_parts_details.parts_requested_type,"
                    . "spare_parts_details.date_of_request,"
                    . "spare_parts_details.service_center_id,"
                    . "spare_parts_details.booking_id,"
                    . "spare_parts_details.shipped_inventory_id";

            $sp_data = $this->inventory_model->get_spare_parts_query($req);

            if ($this->session->userdata('service_center_id')) {
                $agent_id = $this->session->userdata('service_center_agent_id');
                $agent_name = $this->session->userdata('service_center_name');
                $track_entity_id = $this->session->userdata('service_center_id');
                $track_entity_type = _247AROUND_SF_STRING;
                $l_partner = NULL;
            } else if ($this->input->post("call_from_api")) {
                $agent_id = $this->input->post("service_center_id");
                $agent_name = "";
                $track_entity_id = $service_center_id = $this->input->post("service_center_id");
                $l_partner = NULL;
                $track_entity_type = _247AROUND_SF_STRING;
            } else if ($this->session->userdata('partner_id')) {  ////  handle partner session // abhishek
                $agent_id = $this->input->post("agent_id");
                $agent_name = $this->session->userdata('partner_name');
                $service_center_id = NULL;
                $track_entity_id = $l_partner = $this->session->userdata('partner_id');
                $track_entity_type = _247AROUND_PARTNER_STRING;
            } else {
                $agent_id = _247AROUND_DEFAULT_AGENT;
                $agent_name = _247AROUND_DEFAULT_AGENT_NAME;
                $track_entity_id = $l_partner = _247AROUND;
                $track_entity_type = _247AROUND_EMPLOYEE_STRING;
            }
            $partner_id = $this->input->post("partner_id");


            if (!empty($sp_data)) {
                
                $next_action = '';
                foreach ($sp_data as $key => $value) {
                    $flag = TRUE;
                    $spare_data = array();
                    $delivered_sp = array();
                    $service_center_id = $value->service_center_id;
                    $spare_data['model_number'] = $value->model_number;
                    $spare_data['parts_requested'] = $value->parts_requested;
                    $spare_data['parts_requested_type'] = $value->parts_requested_type;
                    $spare_data['date_of_request'] = $value->date_of_request;
                    // $spare_data['shipped_inventory_id'] = $value->shipped_inventory_id;
                    $spare_data['requested_inventory_id'] = $value->requested_inventory_id;
                    $spare_data['service_center_id'] = $service_center_id;
                    $spare_data['booking_id'] = $booking_id;
                    $spare_data['entity_type'] = $value->entity_type;
                    $spare_data['partner_id'] = $partner_id;
                    $spare_data['is_micro_wh'] = $value->is_micro_wh;
                    $spare_data['quantity'] = $value->quantity;

                    $data = array();
                    $entity_type = $value->entity_type;
                    $is_micro_wh = $value->is_micro_wh;
                    $spare_id = $value->id;
                    if (!isset($partner_id) || empty($partner_id)) {
                        $partner_id = $value->partner_id;
                    }

                    $partner_details = $this->partner_model->getpartner_details("is_def_spare_required,is_wh, is_defective_part_return_wh,is_micro_wh", array('partners.id' => $partner_id));
                    $sf_state = $this->vendor_model->getVendorDetails("service_centres.state", array('service_centres.id' => $service_center_id));
                    $is_warehouse = false;
                    if (!empty($partner_details[0]['is_wh'])) {

                        $is_warehouse = TRUE;
                    } else if (!empty($partner_details[0]['is_micro_wh'])) {
                        $is_warehouse = TRUE;
                    }
                    if (!empty($is_warehouse)) {

                        $warehouse_details = $this->get_warehouse_details(array('inventory_id' => $value->requested_inventory_id, 'state' => $sf_state[0]['state'], 'service_center_id' => $service_center_id, 'model_number' => $value->model_number), $partner_id);
                        if (!empty($warehouse_details)) {
                            $data['partner_id'] = $warehouse_details['entity_id'];
                            $data['entity_type'] = $warehouse_details['entity_type'];
                            $data['defective_return_to_entity_type'] = $warehouse_details['defective_return_to_entity_type'];
                            $data['defective_return_to_entity_id'] = $warehouse_details['defective_return_to_entity_id'];
                            $data['is_micro_wh'] = $warehouse_details['is_micro_wh'];
                            $data['challan_approx_value'] = round(($warehouse_details['challan_approx_value'] * $spare_data['quantity']), 2);
                            $data['invoice_gst_rate'] = $warehouse_details['gst_rate'];
                            $data['parts_requested'] = $warehouse_details['part_name'];
                            $data['parts_requested_type'] = $warehouse_details['type'];
                            $entity_type = $warehouse_details['entity_type'];
                            $data['requested_inventory_id'] = $warehouse_details['inventory_id'];
                            $is_micro_wh = $warehouse_details['is_micro_wh'];
                        } else {
                            $data['partner_id'] = $partner_id;
                            $data['entity_type'] = _247AROUND_PARTNER_STRING;
                            $entity_type = _247AROUND_PARTNER_STRING;
                            $data['is_micro_wh'] = 0;
                            if (isset($warehouse_details['challan_approx_value'])) {
                                $data['challan_approx_value'] = round(($warehouse_details['challan_approx_value'] * $spare_data['quantity']), 2);
                            }
                            $data['defective_return_to_entity_type'] = _247AROUND_SF_STRING;
                            $data['defective_return_to_entity_id'] = DEFAULT_WAREHOUSE_ID;
                            $is_micro_wh = 0;
                        }
                    } else {
                        $data['partner_id'] = $partner_id;
                        $data['entity_type'] = _247AROUND_PARTNER_STRING;
                        $data['is_micro_wh'] = 0;
                        $data['defective_return_to_entity_type'] = _247AROUND_SF_STRING;
                        $data['defective_return_to_entity_id'] = DEFAULT_WAREHOUSE_ID;
                        $entity_type = _247AROUND_PARTNER_STRING;
                        $is_micro_wh = 0;
                    }

                    if ($entity_type == _247AROUND_SF_STRING) {
                        if ($is_micro_wh == 1) {
                            $actor = "vendor";
                            $next_action = "Visit Customer";

                            $sc['current_status'] = _247AROUND_PENDING;
                            $sc['update_date'] = date('Y-m-d H:i:s');
                            $sc['internal_status'] = _247AROUND_PENDING;

                            $data['status'] = SPARE_SHIPPED_BY_PARTNER;
                            $data['date_of_request'] = date('Y-m-d');
                            $data['model_number_shipped'] = $value->model_number;
                            $data['parts_shipped'] = $data['parts_requested'];
                            $data['shipped_parts_type'] = $data['parts_requested_type'];
                            $data['shipped_date'] = date('Y-m-d');
                            $data['shipped_inventory_id'] = $data['requested_inventory_id'];
                            $data['quantity'] = $value->quantity;
                            $data['shipped_quantity'] = $data['quantity'];

                            $flag = false;
                            
                            
                            /* Insert Spare Tracking Details */
                            if (!empty($value->id)) {
                                $tracking_details = array('spare_id' => $value->id, 'action' => $data['status'], 'remarks' => ESTIMATE_APPROVED_BY_CUSTOMER, 'agent_id' => $agent_id, 'entity_id' => $track_entity_id, 'entity_type' => $track_entity_type);
                                $this->service_centers_model->insert_spare_tracking_details($tracking_details);
                            }

                            $this->notify->insert_state_change($booking_id, ESTIMATE_APPROVED_BY_CUSTOMER, ESTIMATE_APPROVED_BY_CUSTOMER, ESTIMATE_APPROVED_BY_CUSTOMER, $agent_id, $agent_name, $actor, $next_action, $l_partner, $service_center_id);

                            $where = array('id' => $value->id);
                            $this->service_centers_model->update_spare_parts($where, $data);

                            $spare_data['spare_id'] = $spare_id;
                            $spare_data['shipped_inventory_id'] = $data['shipped_inventory_id'];
                            $spare_data['model_number'] = $data['model_number_shipped'];
                            $spare_data['parts_requested_type'] = $data['shipped_parts_type'];
                            $spare_data['parts_requested'] = $data['parts_shipped'];
                            $spare_data['date_of_request'] = $data['date_of_request'];
                            $spare_data['requested_inventory_id'] = $data['requested_inventory_id'];
                            array_push($delivered_sp, $spare_data);
                            //$delivered_sp[] = $spare_data;
                            $this->auto_delivered_for_micro_wh($delivered_sp, $partner_id);
                            unset($data['spare_id']);
                        } else if ($is_micro_wh == 2) {
                            $actor = "warehouse";
                            $next_action = "Send OOW Part";
                            $sc['current_status'] = "InProcess";
                            $sc['update_date'] = date('Y-m-d H:i:s');
                            $sc['internal_status'] = SPARE_PARTS_REQUIRED;
                            $status = SPARE_PARTS_REQUESTED;
                            $data['status'] = $status;
                            $data['date_of_request'] = date('Y-m-d');
                            $this->service_centers_model->update_spare_parts(array('id' => $value->id), $data);

                        }
                    } else {

                        log_message("info", __METHOD__ . "Spare parts Not found" . $booking_id);
                        $actor = "partner";
                        $next_action = "Send OOW Part";
                        $sc['current_status'] = "InProcess";
                        $sc['update_date'] = date('Y-m-d H:i:s');
                        $sc['internal_status'] = SPARE_PARTS_REQUIRED;
                        $status = SPARE_PARTS_REQUESTED;
///////  Coming here ////
                        $this->service_centers_model->update_spare_parts(array('id' => $value->id), array("status" => $status, 'date_of_request' => date('Y-m-d')));
                    }

                    /* Insert Spare Tracking Details */
                    if (!empty($value->id) && !empty($status)) {
                        $tracking_details = array('spare_id' => $value->id, 'action' => $status, 'remarks' => ESTIMATE_APPROVED_BY_CUSTOMER, 'agent_id' => $agent_id, 'entity_id' => $track_entity_id, 'entity_type' => $track_entity_type);
                        $this->service_centers_model->insert_spare_tracking_details($tracking_details);
                    }

                    // UPDATE SC Action Table                    
                    if ($flag == TRUE) {
                        $this->notify->insert_state_change($booking_id, ESTIMATE_APPROVED_BY_CUSTOMER, ESTIMATE_APPROVED_BY_CUSTOMER, ESTIMATE_APPROVED_BY_CUSTOMER, $agent_id, $agent_name, $actor, $next_action, $l_partner, $service_center_id, $value->id);
                    }
                }
                
                if ($flag == TRUE) {
                    if(empty($status)) {
                        $status = ESTIMATE_APPROVED_BY_CUSTOMER;
                    }
                    $this->service_centers_model->update_service_centers_action_table($booking_id, $sc);
                    $this->update_booking_internal_status($booking_id, $status, $partner_id);
                }
                $userSession = array('success' => 'Booking Updated');
            } else {
                log_message("info", __METHOD__ . "Spare Not not found " . $booking_id);
                $userSession = array('error' => 'Booking Not Updated');
            }
        } else {
            log_message("info", __METHOD__ . "Booking ID not found " . $booking_id);
            $userSession = array('error' => 'Booking Not Updated');
        }

        if (!empty($l_partner)) {
            echo json_encode($userSession, true);
        } else {
            if (!$this->input->post("call_from_api")) {
                $this->session->set_userdata($userSession);
                redirect(base_url() . "service_center/pending_booking");
            }
        }
    }

    /*
     * This Function send SMS to Customer When SF request For Booking Reschedule (To Know is Reschedule Fake)
     */

    function send_reschedule_confirmation_sms($booking_id) {
        $join["users"] = "users.user_id = booking_details.user_id";
        $join["services"] = "services.id = booking_details.service_id";
        $join["service_center_booking_action"] = "service_center_booking_action.booking_id = booking_details.booking_id";
        $data = $this->reusable_model->get_search_result_data("booking_details", "users.user_id,users.phone_number,services.services,booking_details.booking_date, service_center_booking_action.booking_date as reschedual_date", array("booking_details.booking_id" => $booking_id), $join, NULL, NULL, NULL, NULL, array());
        if (!empty($data[0])) {
            $sms['tag'] = BOOKING_RESCHEDULED_CONFIRMATION_SMS;
            $sms['phone_no'] = $data[0]['phone_number'];
            $sms['smsData']['service'] = $data[0]['services'];
            $sms['smsData']['booking_id'] = $booking_id;
            $sms['smsData']['booking_date'] = date("d-M-Y", strtotime($data[0]['reschedual_date']));
            $sms['booking_id'] = $booking_id;
            $sms['type'] = "user";
            $sms['type_id'] = $data[0]['user_id'];
            $this->notify->send_sms_msg91($sms);
        }
    }

    function get_learning_collateral_for_bookings() {
        $booking_id = $this->input->post('booking_id');
        $data = $this->service_centers_model->get_collateral_for_service_center_bookings($booking_id);
        if (!empty($data)) {
            $finalString = '<table class="table">
            <thead>
              <tr>
              <th>S.N</th>
                <th>Document Type</th>
                <th>File</th>
              </tr>
            </thead>
            <tbody>';
            $index = 0;
            foreach ($data as $collatralData) {
                if ($collatralData['is_file']) {
                    $url = "https://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/vendor-partner-docs/" . $collatralData['file'];
                } else {
                    $url = $collatralData['file'];
                }
                $index++;
                $finalString .= '<tr><td>' . $index . '</td>';
                $finalString .= '<td>' . $collatralData['collateral_type'] . '</td>';
                $finalString .= '<td>' . $this->miscelleneous->get_reader_by_file_type($collatralData['document_type'], $url, "400") . '</td>';
                $finalString .='</tr>';
            }
            $finalString .='</tbody></table>';
        } else {
            $finalString = "<p style='text-align:center;'>Brand Collateral is not available</p>";
        }

        echo $finalString;
    }

    function customer_invoice_details() {
        $this->load->view('service_centers/header');
        $this->load->view('service_centers/customer_invoice_details');
    }

    /**
     * @desc: This function is used to check warehouse session.
     * @param: void
     * @return: true if details matches else session is destroyed.
     */
    function check_WH_UserSession() {
        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'service_center') && !empty($this->session->userdata('service_center_id')) && (!empty($this->session->userdata('is_wh')) || !empty($this->session->userdata('is_micro_wh')))) {
            return TRUE;
        } else {
            log_message('info', __FUNCTION__ . " Session Expire for Service Center");
            $this->session->sess_destroy();
            redirect(base_url() . "service_center/login");
        }
    }
    
    
    /**
     * @desc: This function is used to check warehouse or inventory manager session.
     * @param: void
     * @return: true if details matches else session is destroyed.
     */
    function check_WH_InventoryMN_UserSession() {

        if (($this->session->userdata('loggedIn') == TRUE) && (($this->session->userdata('userType') == 'service_center') || ($this->session->userdata('userType') == 'employee')) && (!empty($this->session->userdata('service_center_id')) || !empty($this->session->userdata('id'))) && (!empty($this->session->userdata('is_wh')) || !empty($this->session->userdata('is_micro_wh')) || $this->session->userdata('user_group') == 'inventory_manager')) {
            return TRUE;
        } else {
            log_message('info', __FUNCTION__ . " Session Expire for Service Center");
            $this->session->sess_destroy();
            redirect(base_url() . "service_center/login");
        }
    }

    function warehouse_default_page() {
        
        if(!empty($this->session->userdata('warehouse_id'))) {
            $this->checkEmployeeUserSession();
            $this->miscelleneous->load_nav_header();
        } else { 
            $this->check_WH_UserSession();
            $this->load->view('service_centers/header');
        }
        
        $data['courier_details'] = $this->inventory_model->get_courier_services('*');
        $this->load->view('service_centers/warehouse_default_page', $data);
    }

    /**
     *  @desc : This function is used to show the current stock of partner inventory.
     *          By using this method SF can can only see their current stock of their warehouses.
     *  @param : void
     *  @return : void
     */
    function inventory_stock_list() {
        //$this->check_WH_UserSession();
        $this->load->view('service_centers/header');
        $this->load->view('service_centers/inventory_stock_list');
    }

    /**
     *  @desc : This function is used to show the current alternate spare parts stock of partner inventory.
     *          By using this method SF can can only see their current stock of their warehouses.
     *  @param : $partner_id
     *  @param :$inventory_id
     * @param :$service_id
     *  @return : void
     */
    function alternate_inventory_stock_list($partner_id, $inventory_id, $service_id) {
        //$this->check_WH_UserSession();
        $where = array(
            'inventory_master_list.entity_id' => $partner_id,
            'inventory_master_list.entity_type' => _247AROUND_PARTNER_STRING,
            'inventory_master_list.inventory_id' => $inventory_id,
            'inventory_master_list.service_id' => $service_id,
        );

        $inventory_list = $this->inventory_model->get_inventory_master_list_data('inventory_master_list.part_name', $where, array());
        $data = array();
        $data['partner_id'] = $partner_id;
        $data['inventory_id'] = $inventory_id;
        $data['service_id'] = $service_id;
        if (!empty($inventory_list)) {
            $data['part_name'] = $inventory_list[0]['part_name'];
        }
        $this->load->view('service_centers/header');
        $this->load->view('service_centers/alternate_inventory_stock_list', $data);
    }

    /**
     *  @desc : This function is used to show the current alternate spare parts stock of partner inventory.
     *          By using this method SF can can only see their current stock of their warehouses.
     *  @return : void
     */
    function alternate_parts_inventory_list() {
        //$this->check_WH_UserSession();
        $this->load->view('service_centers/header');
        $this->load->view('service_centers/alternate_parts_list');
    }
    
    /**
     * @desc: This function is used to get those booking who has request to ship spare parts to SF
     * @param: void
     * @return void
     */
    function generate_challan_send_to_sf($offset = 0) {
        log_message('info', __FUNCTION__ . " sf Id: " . $this->session->userdata('service_center_id'));

        if (!empty($this->session->userdata('warehouse_id'))) {
            $this->checkEmployeeUserSession();
            $data['sf_id'] = $this->session->userdata('warehouse_id');
        } else {
            $this->check_WH_UserSession();
            $data['sf_id'] = $this->session->userdata('service_center_id');
        }

        $where = "spare_parts_details.partner_id = '" . $data['sf_id'] . "' AND  spare_parts_details.entity_type =  '" . _247AROUND_SF_STRING . "' AND status = '" . SPARE_PARTS_REQUESTED . "' "
                . " AND booking_details.current_status IN ('" . _247AROUND_PENDING . "', '" . _247AROUND_RESCHEDULED . "') "
                . " AND wh_ack_received_part != 0 AND spare_parts_details.partner_challan_number IS NULL AND spare_parts_details.partner_challan_file IS NULL";

        $select = "spare_parts_details.id, spare_parts_details.booking_id, spare_parts_details.partner_id, spare_parts_details.entity_type, spare_parts_details.service_center_id,spare_parts_details.partner_challan_file,spare_parts_details.partner_challan_number,GROUP_CONCAT(DISTINCT spare_parts_details.parts_requested) as parts_requested, purchase_invoice_id, users.name, "
                . "booking_details.booking_primary_contact_no, booking_details.partner_id as booking_partner_id, booking_details.flat_upcountry,"
                . "booking_details.booking_address,booking_details.initial_booking_date, booking_details.is_upcountry, "
                . "booking_details.upcountry_paid_by_customer,booking_details.amount_due,booking_details.state, service_centres.name as vendor_name, "
                . "service_centres.address, service_centres.state, service_centres.gst_no, service_centres.pincode, "
                . "service_centres.district,service_centres.id as sf_id,service_centres.is_gst_doc,service_centres.signature_file, "
                . " GROUP_CONCAT(DISTINCT inventory_stocks.stock) as stock, DATEDIFF(CURRENT_TIMESTAMP,  STR_TO_DATE(date_of_request, '%Y-%m-%d')) AS age_of_request,"
                . " GROUP_CONCAT(DISTINCT spare_parts_details.model_number) as model_number, "
                . " GROUP_CONCAT(DISTINCT spare_parts_details.serial_number) as serial_number,"
                . " spare_parts_details.quantity,"
                . " spare_parts_details.shipped_quantity,"
                . " GROUP_CONCAT(DISTINCT spare_parts_details.remarks_by_sc) as remarks_by_sc, spare_parts_details.partner_id, "
                . " GROUP_CONCAT(DISTINCT spare_parts_details.id) as spare_id, serial_number_pic, GROUP_CONCAT(DISTINCT spare_parts_details.inventory_invoice_on_booking) as inventory_invoice_on_booking, i.part_number, service_centres.active, service_centres.on_off";
        $data['spare_parts'] = $this->service_centers_model->get_spare_parts_on_group($where, $select, "spare_parts_details.booking_id", $data['sf_id']);
        $data['is_ajax'] = $this->input->post('is_ajax');
        $data['is_send_to_sf'] = false;
        $data['is_generate_challan'] = true;
        if (empty($this->input->post('is_ajax'))) {
            $this->load->view('service_centers/header');
            $this->load->view('service_centers/generate_challan_send_to_sf', $data);
        } else {
            $this->load->view('service_centers/generate_challan_send_to_sf', $data);
        }
    }

    /**
     * @desc: This function is used to get those booking who has request to ship spare parts to SF
     * @param: void
     * @return void
     */
    function get_spare_parts_booking($offset = 0) {
        log_message('info', __FUNCTION__ . " sf Id: " . $this->session->userdata('service_center_id'));
        
        if(!empty($this->session->userdata('warehouse_id'))) { 
            $this->checkEmployeeUserSession();
            $data['sf_id'] = $this->session->userdata('warehouse_id');
        } else {
            $this->check_WH_UserSession();
            $data['sf_id'] = $this->session->userdata('service_center_id');
        }
        
        $where = "spare_parts_details.partner_id = '" . $data['sf_id'] . "' AND  spare_parts_details.entity_type =  '" . _247AROUND_SF_STRING . "' AND status = '" . SPARE_PARTS_REQUESTED . "' "
                . " AND booking_details.current_status IN ('" . _247AROUND_PENDING . "', '" . _247AROUND_RESCHEDULED . "') "
                . " AND wh_ack_received_part != 0 AND spare_parts_details.partner_challan_number IS NOT NULL AND spare_parts_details.partner_challan_file IS NOT NULL";

        $select = "spare_parts_details.id, spare_parts_details.booking_id, spare_parts_details.partner_id, spare_parts_details.entity_type, spare_parts_details.service_center_id,spare_parts_details.partner_challan_file,spare_parts_details.partner_challan_number,GROUP_CONCAT(DISTINCT spare_parts_details.parts_requested) as parts_requested, purchase_invoice_id, users.name, "
                . "booking_details.booking_primary_contact_no, booking_details.partner_id as booking_partner_id, booking_details.flat_upcountry,"
                . "booking_details.booking_address,booking_details.initial_booking_date, booking_details.is_upcountry, "
                . "booking_details.upcountry_paid_by_customer,booking_details.amount_due,booking_details.state, service_centres.name as vendor_name, "
                . "service_centres.address, service_centres.state, service_centres.gst_no, service_centres.pincode, "
                . "service_centres.district,service_centres.id as sf_id,service_centres.is_gst_doc,service_centres.signature_file, "
                . " GROUP_CONCAT(DISTINCT inventory_stocks.stock) as stock, DATEDIFF(CURRENT_TIMESTAMP,  STR_TO_DATE(date_of_request, '%Y-%m-%d')) AS age_of_request,"
                . " GROUP_CONCAT(DISTINCT spare_parts_details.model_number) as model_number, "
                . " GROUP_CONCAT(DISTINCT spare_parts_details.serial_number) as serial_number,"
                . " spare_parts_details.quantity,"
                . " spare_parts_details.shipped_quantity,"
                . " GROUP_CONCAT(DISTINCT spare_parts_details.remarks_by_sc) as remarks_by_sc, spare_parts_details.partner_id, "
                . " GROUP_CONCAT(DISTINCT spare_parts_details.id) as spare_id, serial_number_pic, GROUP_CONCAT(DISTINCT spare_parts_details.inventory_invoice_on_booking) as inventory_invoice_on_booking, i.part_number, service_centres.active, service_centres.on_off";

        $data['spare_parts'] = $this->service_centers_model->get_spare_parts_on_group($where, $select, "spare_parts_details.booking_id", $data['sf_id']);

        $data['is_ajax'] = $this->input->post('is_ajax');
        $data['is_send_to_sf'] = true;
        $data['is_generate_challan'] = false;
        if (empty($this->input->post('is_ajax'))) {
            $this->load->view('service_centers/header');
            $this->load->view('service_centers/spare_parts_booking', $data);
        } else {
            $this->load->view('service_centers/spare_parts_booking', $data);
        }
    }

    /**
     * @desc: This function is used to get those booking who has defective parts shipped by SF to partner
     * @param: void
     * @return void
     */
    function get_defective_parts_shipped_by_sf() {
        
        if(!empty($this->session->userdata('warehouse_id'))) { 
            $this->checkEmployeeUserSession();
        } else {
            $this->check_WH_UserSession();
        }
        $this->load->view('service_centers/defective_parts_shipped_by_sf');
    }

    function get_defective_parts_shipped_by_sf_list($page = 0, $offset = 0) {
        
        if(!empty($this->session->userdata('warehouse_id'))) { 
            $this->checkEmployeeUserSession();
            $sf_id = $this->session->userdata('warehouse_id');
        } else {
            $this->check_WH_UserSession();
            $sf_id = $this->session->userdata('service_center_id');
        }
        
        $post = $this->get_post_view_data();
        log_message('info', __FUNCTION__ . " SF ID: " . $sf_id);
        if (!empty($post['search_value'])) {

            $where = array(
                "spare_parts_details.defective_part_required" => 1,
                "approved_defective_parts_by_admin" => 1,
                "(spare_lost is null or spare_lost = 0)" => NULL,
                "spare_parts_details.defective_return_to_entity_type" => _247AROUND_SF_STRING,
                "status IN ('" . DEFECTIVE_PARTS_SHIPPED . "','" . OK_PARTS_SHIPPED . "','" . DAMAGE_PARTS_SHIPPED . "')" => NULL,
            );
        } else {
        $where = array(
                "spare_parts_details.defective_part_required" => 1,
                "approved_defective_parts_by_admin" => 1,
                "(spare_lost is null or spare_lost = 0)" => NULL,
                "spare_parts_details.defective_return_to_entity_id" => $sf_id,
                "spare_parts_details.defective_return_to_entity_type" => _247AROUND_SF_STRING,
                "status IN ('" . DEFECTIVE_PARTS_SHIPPED . "','" . OK_PARTS_SHIPPED . "','" . DAMAGE_PARTS_SHIPPED . "')" => NULL,
            );
        }
        
        $select = "spare_parts_details.id,defective_part_shipped,spare_parts_details.defective_part_rejected_by_partner, spare_parts_details.shipped_quantity,spare_parts_details.id, spare_consumption_status.consumed_status, spare_consumption_status.is_consumed, spare_consumption_status.reason_text,spare_parts_details.defective_return_to_entity_id, "
                . " spare_parts_details.booking_id, users.name as 'user_name', courier_name_by_sf, awb_by_sf,defective_part_shipped_date,"
                . "remarks_defective_part_by_sf,booking_details.partner_id,service_centres.name as 'sf_name',service_centres.district as 'sf_city',s.part_number, spare_parts_details.defactive_part_received_date_by_courier_api, spare_parts_details.status, spare_parts_details.defective_part_rejected_by_wh";
        $group_by = "spare_parts_details.id";
        $limit = $post['length'];
        $offset = $post['start'];
        $post['column_search'] = array('spare_parts_details.booking_id', 'spare_parts_details.awb_by_sf','service_centres.name');
        $order_by = "spare_parts_details.defective_part_shipped_date DESC, spare_parts_details.booking_id";
        $list = $this->service_centers_model->get_spare_parts_booking($where, $select, $group_by, $order_by, $offset, $limit, 0, NULL, $post);
        
        $no = $post['start'];
        $data=array();
        //$no =0;
        $ware_house_name_array = array();
        foreach ($list as $spare_list) {
            $no++;
            $warehouse_name = '';
            $wh_id = $spare_list['defective_return_to_entity_id'];
            if(!empty($whare_house_name_array[$wh_id])){
                $warehouse_name = $whare_house_name_array[$wh_id];
            }else{
                $select_wh = "service_centres.id,service_centres.name,service_centres.is_wh";
                $post_where['where']['(service_centres.is_wh = 1)'] = null;
                $post_where['where']['service_centres.id'] = $wh_id;
                $post_where['length'] = -1;
                $post_where['start'] = 0;
                $list_wh = $this->vendor_model->viewallvendor($post_where, $select_wh);
                if(!empty($list_wh)){
                   $warehouse_name = $list_wh[0]['name']; 
                   $whare_house_name_array[$wh_id] = $warehouse_name;
                }
            }
            $row = $this->defective_parts_shipped_by_sf_table_data($spare_list, $no, $warehouse_name, $sf_id);
            $data[] = $row;
        }


        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->service_centers_model->count_all_defective_parts_shipped_by_sf_list($where, $group_by, $order_by, $post),
            "recordsFiltered" => $this->service_centers_model->count_defective_parts_shipped_by_sf_list($where, $group_by, $order_by, $post),
            "data" => $data
        );

        echo json_encode($output);


        // $this->load->view('employee/get_spare_parts', $data);
    }

    function defective_parts_shipped_by_sf_table_data($spare_list, $no, $warehouse_name = '', $sf_id = '') {

        $row = array();
        
        if ($spare_list['defective_part_rejected_by_wh'] == 1) {
            $color_class = 'rejected_by_wh';
        } else {
            $color_class = '';
        }

        $spareStatus = DELIVERED_SPARE_STATUS;
        if (!$spare_list['defactive_part_received_date_by_courier_api']) {
            $spareStatus = $spare_list['status'];
        }
        $row[] = "<span class='".$color_class."'>". $no ."</span>";
        if (!empty($this->session->userdata('service_center_id'))) {
            $row[] = "<a href='" . base_url() . "service_center/booking_details/" . urlencode(base64_encode($spare_list['booking_id'])) . "'target='_blank'>" . $spare_list['booking_id'] . "</a>";
        } else if ($this->session->userdata('id')) {
            $row[] = "<a href='" . base_url() . "employee/booking/viewdetails/" . $spare_list['booking_id'] . "'target='_blank'>" . $spare_list['booking_id'] . "</a>";
        } 
        $row[] = "<span class='".$color_class."'>". $spare_list['id'] ."</span>";

        $row[] = "<span class='".$color_class."'>". $spare_list['user_name'] ."</span>";
        $row[] = "<span class='".$color_class."'>". $spare_list['sf_name'] ."</span>";
        $row[] = "<span class='".$color_class."'>". $spare_list['sf_city'] ."</span>";
        if(empty($warehouse_name)){
        $row[] = "<span class='".$color_class."'>". $spare_list['defective_part_shipped'] ."</span>";
        }else{
        $row[] = "<span class='".$color_class."'>". $spare_list['defective_part_shipped'] ."<br>($warehouse_name)</span>";   
        }
        $row[] = "<span class='".$color_class."'>". $spare_list['shipped_quantity'] ."</span>";
        $row[] = "<span class='".$color_class."'>". $spare_list['part_number'] ."</span>";
        $row[] = "<span class='".$color_class."'>". $spare_list['courier_name_by_sf'] ."</span>";

        $c = "<a href='javascript:void(0);' onclick='";
        $c .= "get_awb_details(" . '"' . $spare_list['courier_name_by_sf'] . '"';
        $c .= ', "' . $spare_list['awb_by_sf'] . ',"' . $spareStatus . ',"awb_loader_"' . $no;
        $c .= ")'>" . $spare_list['awb_by_sf'] . "</a><span id='awb_loader_" . $no . "' style='display:none;'><i class='fa fa-spinner fa-spin'></i></span>";

        $row[] = $c;

        $row[] =  "<span class='".$color_class."'>". date("d-M-Y", strtotime($spare_list['defective_part_shipped_date']))."</span>";
        $row[] = "<span class='".$color_class."'>". $spare_list['remarks_defective_part_by_sf'] ."</span>";
        if ($spare_list['is_consumed'] == 1) {
            $row[] = "<span class='".$color_class."'>". "Yes" ."</span>";
        } else {
            $row[] = "<span class='".$color_class."'>". "No" ."</span>";
        }


        $row[] = "<span class='".$color_class."'>". $spare_list['reason_text'] ."</span>";

        //If Defective part is already shipped or Different warehouse search data then disable receive button
        if (!empty($spare_list['defective_part_shipped']) && $sf_id == $spare_list['defective_return_to_entity_id']) {

            $a = "<a href='javascript:void(0);' id='defective_parts_' class='btn btn-sm btn-primary recieve_defective' onclick='";
            $a .= "open_spare_consumption_model(this.id," . '"' . $spare_list['booking_id'] . '"';
            $a .= ', "' . $spare_list['id'] . '"';
            $a .= ")'>Receive</a>";
            $a .= "<input type='checkbox' class='checkbox_revieve_class' name='revieve_checkbox'";
            $a .=" data-docket_number='" . $spare_list['awb_by_sf'] . "' data-spare-id='" . $spare_list['id'] . "'  data-consumption_status='" . $spare_list['consumed_status'] . "' data-url='" . base_url() . "service_center/acknowledge_received_defective_parts/" . $spare_list['id'] . "/" . $spare_list['booking_id'] . "/" . $spare_list['partner_id'] . "'   />";
            

            $row[] = $a;
        } else {

            $row[] = '<a class="btn btn-sm btn-primary disabled"  >Received</a> ';
        }


        $b = "<a href='javascript:void(0);' id='reject_defective_' class='btn btn-sm btn-danger reject_defective' onclick='";
        $b .= "open_reject_spare_consumption_model(this.id," . '"' . $spare_list['booking_id'] . '"';
        $b .= ', "' . $spare_list['id'] . '"';
        $b .= ")'>Reject</a>";

        $row[] = $b;

        return $row;
    }

    /**
     * @desc: This function is used to get those booking whose spare parts shipped by 247around warehouse to SF
     * @param: Integer $offset
     * @return void
     */
    function get_shipped_parts_list_by_warehouse($offset = 0) {
        log_message('info', __FUNCTION__ . " SF ID: " . $this->session->userdata('service_center_id'));
        $this->check_WH_UserSession();
        $sf_id = $this->session->userdata('service_center_id');
        $where = "spare_parts_details.partner_id = '" . $sf_id . "' AND spare_parts_details.entity_type = '" . _247AROUND_SF_STRING . "'"
                . " AND status NOT IN ('" . _247AROUND_CANCELLED . "')  "
                . " AND spare_parts_details.shipped_date IS NOT NULL ";

        $config['base_url'] = base_url() . 'service_center/get_shipped_parts_list';
        $total_rows = $this->partner_model->get_spare_parts_booking_list($where, false, false, false);
        $config['total_rows'] = $total_rows[0]['total_rows'];

        $config['per_page'] = 100;
        $config['uri_segment'] = 3;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['count'] = $config['total_rows'];
        $data['spare_parts'] = $this->partner_model->get_spare_parts_booking_list($where, $offset, $config['per_page'], true);


        $this->load->view('service_centers/header');
        $this->load->view('service_centers/shipped_spare_part_booking', $data);
    }

    /**
     * @desc: This function is used to update spare parts details by warehouse
     * @param: void
     * @return void
     */
    function update_spare_parts_form($booking_id, $wh = 0) {
        
        if(!empty($this->session->userdata('warehouse_id'))) { 
            $this->checkEmployeeUserSession();
            $sf_id = $this->session->userdata('warehouse_id');
        } else {
            $this->check_WH_UserSession();
            $sf_id = $this->session->userdata('service_center_id');
        }
        
        log_message('info', __FUNCTION__ . " SF ID: " . $sf_id . " Spare Parts ID: " . $booking_id);
        
        $where['length'] = -1;
        if ($wh) {
            $where['where'] = array('spare_parts_details.booking_id' => $booking_id, "status" => SPARE_PARTS_REQUESTED, 'wh_ack_received_part' => 1, 'spare_parts_details.entity_type' => _247AROUND_PARTNER_STRING, 'requested_inventory_id > 0' => NULL);
        } else {
            $where['where'] = array('spare_parts_details.booking_id' => $booking_id, "status" => SPARE_PARTS_REQUESTED, "spare_parts_details.entity_type" => _247AROUND_SF_STRING, 'spare_parts_details.partner_id' => $sf_id, 'wh_ack_received_part' => 1);
        }

        $where['select'] = $where['select'] = "booking_details.booking_id, users.name, defective_back_parts_pic,booking_primary_contact_no,parts_requested, model_number,serial_number,date_of_purchase, invoice_pic,"
                . "serial_number_pic,defective_parts_pic,spare_parts_details.id,requested_inventory_id,parts_requested_type,spare_parts_details.part_warranty_status, booking_details.request_type, purchase_price, estimate_cost_given_date,booking_details.partner_id,booking_details.service_id,booking_details.assigned_vendor_id,booking_details.amount_due,parts_requested_type, inventory_invoice_on_booking";

        if (!empty($booking_id)) {
            $data['spare_parts'] = $this->inventory_model->get_spare_parts_query($where);
        }



        if (!empty($data['spare_parts'])) {

//        $where = array('entity_id' => $data['spare_parts'][0]->partner_id, 'entity_type' => _247AROUND_PARTNER_STRING, 'service_id' => $data['spare_parts'][0]->service_id,'active' => 1);
//        $data['inventory_details'] = $this->inventory_model->get_appliance_model_details('id,model_number',$where);

            $where = array('entity_id' => $data['spare_parts'][0]->partner_id, 'entity_type' => _247AROUND_PARTNER_STRING, 'service_id' => $data['spare_parts'][0]->service_id, 'inventory_model_mapping.active' => 1, 'appliance_model_details.active' => 1, 'appliance_model_details.active' => 1);
            $data['inventory_details'] = $this->inventory_model->get_inventory_mapped_model_numbers('appliance_model_details.id,appliance_model_details.model_number', $where);
            $data['courier_details'] = $this->inventory_model->get_courier_services('*');
            $data['is_wh'] = $this->partner_model->getpartner_details('is_wh', array('partners.id' => $data['spare_parts'][0]->partner_id))[0]['is_wh'];
            $data['wh_ship'] = $wh;
        }

        if(!empty($this->session->userdata('warehouse_id'))) {  
            $this->miscelleneous->load_nav_header();
        } else {
            $this->load->view('service_centers/header');
        }
        $this->load->view('service_centers/update_spare_parts_form', $data);
    }

    /**
     * @desc: This method is used to update spare parts by warehouse. If gets input from form.
     *        Insert data into booking state change and update sc action table
     * @param String $booking_id
     * @param String $id
     * @return void
     */
    function process_update_spare_parts($booking_id, $wh = 0) {
        
        if(!empty($this->session->userdata('warehouse_id'))) { 
            $this->checkEmployeeUserSession();
            $sf_id = $this->session->userdata('warehouse_id');
        } else {
            $this->check_WH_UserSession();
            $sf_id = $this->session->userdata('service_center_id');
        }
        
        log_message('info', __FUNCTION__ . " Sf ID: " . $sf_id);
        log_message("info", __METHOD__ . " POST Data " . json_encode($this->input->post()));
        
        //$this->form_validation->set_rules('courier_name', 'Courier Name', 'trim|required');
        //$this->form_validation->set_rules('awb', 'AWB', 'trim|required');
        //$this->form_validation->set_rules('incoming_invoice', 'Invoice', 'callback_spare_incoming_invoice');

//        if ($this->form_validation->run() == FALSE) {
//            log_message('info', __FUNCTION__ . '=> Form Validation is not updated by SF ' . $sf_id .
//                    " Spare id " . $booking_id . " Data" . print_r($this->input->post(), true));
//            $this->update_spare_parts_form($booking_id);
//        } else {
            $exist_awb = $this->input->post('exist_courier_image');
            if (!empty($exist_awb)) {
                $courier_image['message'] = $exist_awb;
                $courier_image['status'] = true;
            } else {
                $courier_image = $this->upload_courier_image_file($booking_id);
            }
            $part_details_challan_bulk = array();
            $generate_bulk_challan = false;
            //$courier_image['status']           
            if (1) {

                $part = $this->input->post("part");
                //$sf_id = $this->session->userdata('service_center_id');
                $partner_id = $this->input->post('partner_id');
                $amount_due = $this->input->post('amount_due');
                $service_center_id = $this->input->post('assigned_vendor_id');

                $awb = $this->input->post('awb');
                $kilo_gram = $this->input->post('spare_parts_shipped_kg');
                $gram = $this->input->post('spare_parts_shipped_gram');
                $billable_weight = $kilo_gram . "." . $gram;

                $status = false;
                $can_status = false;

                foreach ($part as $key => $part_details) {
                    if ($part_details['shippingStatus'] == 1) {

                        $is_shipped_stock_available = $this->reusable_model->get_search_query('inventory_stocks', 'inventory_stocks.id', array('entity_id' => $sf_id, 'entity_type' => _247AROUND_SF_STRING, 'inventory_id' => $part_details['inventory_id'], 'inventory_stocks.stock > 0' => NULL), NULL, NULL, NULL, NULL, NULL)->result_array();

                        if (!empty($is_shipped_stock_available) && !empty($is_shipped_stock_available[0]['id'])) {

                            $status = SPARE_PARTS_SHIPPED_BY_WAREHOUSE;

                            $data = array();
                            $data['courier_pic_by_partner'] = (!empty($courier_image['status'])) ? $courier_image['message'] : NULL;
                            $data['shipped_inventory_id'] = $part_details['inventory_id'];
                            $data['model_number_shipped'] = $part_details['shipped_model_number'];
                            $data['shipped_parts_type'] = $part_details['shipped_part_type'];
                            $data['parts_shipped'] = $part_details['shipped_parts_name'];
                            $data['parts_shipped'] = $part_details['shipped_parts_name'];
                            $data['invoice_gst_rate'] = $part_details['gst_rate'];

                            /**
                             * change defective part required flag in spare part details on the basis of shipped inventory id
                             * @modifiedBy Ankit Rajvanshi
                             */
                            if ($part_details['part_warranty_status'] == SPARE_PART_IN_OUT_OF_WARRANTY_STATUS) { 
                                $data['defective_part_required'] = 0;
                            } else {
                                $data['defective_part_required'] = $this->inventory_model->is_defective_part_required($booking_id, $data['shipped_inventory_id'], $partner_id, $data['shipped_parts_type']);
                            }
                            
                            $data['courier_name_by_partner'] = $this->input->post('courier_name');
                            $data['awb_by_partner'] = $this->input->post('awb');
                            if ($key == 0) {
                                $data['courier_price_by_partner'] = $this->input->post('courier_price_by_partner');
                            } else {
                                $data['courier_price_by_partner'] = 0;
                            }

                            $data['remarks_by_partner'] = $part_details['remarks_by_partner'];
                            $data['shipped_date'] = $this->input->post('shipment_date');
                            $data['shipped_quantity'] = $part_details['shipped_quantity'];
                            $data['defective_return_to_entity_type'] = _247AROUND_SF_STRING;
                            $data['partner_id'] = $sf_id;
                            $data['defective_return_to_entity_id'] = $sf_id;
                            $data['entity_type'] = _247AROUND_SF_STRING;
                            $data['is_micro_wh'] = 2;
                            $price_with_gst = round($part_details['approx_value'] * ( 1 + $part_details['gst_rate'] / 100), 0);
                            $price_with_around_margin = round($price_with_gst * ( 1 + $part_details['oow_around_margin'] / 100), 0);
                            $data['challan_approx_value'] = ($price_with_around_margin * $part_details['shipped_quantity']);
                            $data['status'] = SPARE_PARTS_SHIPPED_BY_WAREHOUSE;

                            if ($part_details['spare_id'] == "new") {

                                $sp_details = $this->partner_model->get_spare_parts_by_any("spare_parts_details.*", array('booking_id' => $booking_id));

                                //  $data['entity_type'] = _247AROUND_SF_STRING;
                                //$data['defective_return_to_entity_type'] = _247AROUND_SF_STRING;
                                $data['booking_id'] = $booking_id;
                                // $data['partner_id'] = $sf_id;
                                // $data['defective_return_to_entity_id'] = $sf_id;
                                $data['service_center_id'] = $service_center_id;
                                $data['model_number'] = $part_details['shipped_model_number'];
                                $data['serial_number'] = $sp_details[0]['serial_number'];
                                $data['requested_inventory_id'] = $part_details['inventory_id'];
                                $data['date_of_purchase'] = $sp_details[0]['date_of_purchase'];
                                $data['date_of_request'] = date("Y-m-d");
                                $data['create_date'] = date('Y-m-d H:i:s');
                                $data['invoice_pic'] = $sp_details[0]['invoice_pic'];
                                $data['defective_parts_pic'] = $sp_details[0]['defective_parts_pic'];
                                $data['defective_back_parts_pic'] = $sp_details[0]['defective_back_parts_pic'];
                                $data['serial_number_pic'] = $sp_details[0]['serial_number_pic'];
                                $data['part_warranty_status'] = $part_details['part_warranty_status'];

                                $data['partner_id'] = $sf_id;
                                $data['defective_return_to_entity_type'] = _247AROUND_SF_STRING;
                                $data['defective_return_to_entity_id'] = $sf_id;
                                if (!empty($part_details['shipped_part_type'])) {
                                    $data['parts_requested_type'] = $part_details['shipped_part_type'];
                                } else {
                                    $data['parts_requested_type'] = $part_details['shipped_parts_name'];
                                }
                                $data['parts_requested'] = $part_details['shipped_parts_name'];
                                $data['quantity'] = $part_details['quantity'];
                                $data['shipped_quantity'] = $part_details['shipped_quantity'];
                                $response = $this->service_centers_model->insert_data_into_spare_parts($data);

                                //  $this->inventory_model->update_pending_inventory_stock_request(_247AROUND_SF_STRING, $sf_id, $data['requested_inventory_id'], $data['shipped_quantity']);
                                //  $this->inventory_model->update_pending_inventory_stock_request(_247AROUND_SF_STRING, $sf_id, $data['requested_inventory_id'], $data['shipped_quantity']);


                                $spare_id = $response;
                                /* field part_warranty_status value 1 means in-warranty and 2 means out-warranty */
                                if ($part_details['part_warranty_status'] == SPARE_PART_IN_OUT_OF_WARRANTY_STATUS) {

                                    $inventory_master_list = $this->inventory_model->get_inventory_master_list_data('*', array('inventory_id' => $data['requested_inventory_id']));

                                    if (!empty($inventory_master_list)) {

                                        $cb_url = base_url() . "apiDataRequest/update_estimate_oow";
                                        $pcb['booking_id'] = $booking_id;
                                        $pcb['assigned_vendor_id'] = $service_center_id;
                                        $pcb['amount_due'] = $amount_due;
                                        $pcb['partner_id'] = $partner_id;
                                        $pcb['sp_id'] = $spare_id;
                                        $pcb['gst_rate'] = $inventory_master_list[0]['gst_rate'];

                                        $pcb['estimate_cost'] = ($inventory_master_list[0]['price'] + ( $inventory_master_list[0]['price'] * $inventory_master_list[0]['gst_rate']) / 100);
                                        
                                        $pcb['agent_id'] = $sf_id;
                                        $this->asynchronous_lib->do_background_process($cb_url, $pcb);
                                    }
                                }
                            } else {
                                $where = array('id' => $part_details['spare_id']);
                                $response = $this->service_centers_model->update_spare_parts($where, $data);
                                $spare_id = $part_details['spare_id'];
                            }


                            if (!empty($spare_id)) {
                                /* Insert Spare Tracking Details */
                                $tracking_details = array('spare_id' => $spare_id, 'action' => $data['status'], 'remarks' => SPARE_PARTS_SHIPPED_BY_WAREHOUSE);
                                if(!empty($this->session->userdata('warehouse_id'))) {
                                    $tracking_details['agent_id'] = $this->session->userdata('id');
                                    $tracking_details['entity_id'] = _247AROUND;
                                    $tracking_details['entity_type'] = _247AROUND_EMPLOYEE_STRING;
                                } else { 
                                    $tracking_details['agent_id'] = $this->session->userdata('service_center_agent_id');
                                    $tracking_details['entity_id'] = $this->session->userdata('service_center_id');
                                    $tracking_details['entity_type'] = _247AROUND_SF_STRING;
                                }                                
                                $this->service_centers_model->insert_spare_tracking_details($tracking_details);
                                $this->insert_details_in_state_change($booking_id, SPARE_PARTS_SHIPPED_BY_WAREHOUSE, "Warehouse acknowledged to shipped spare parts, spare id : $spare_id", "", "", $spare_id);
                                $post = array();
                                $where_clause = array("spare_parts_details.id" => $spare_id, 'spare_parts_details.entity_type' => _247AROUND_SF_STRING);
                                $post['where_in'] = array();
                                $post['is_inventory'] = true;
                                $select = 'booking_details.booking_id, booking_details.service_id, spare_parts_details.id,spare_parts_details.partner_challan_number,spare_parts_details.requested_inventory_id,spare_parts_details.shipped_parts_type, spare_parts_details.shipped_inventory_id, spare_parts_details.partner_id,spare_parts_details.entity_type,spare_parts_details.part_warranty_status, spare_parts_details.parts_requested,spare_parts_details.parts_shipped, spare_parts_details.challan_approx_value, spare_parts_details.quantity, spare_parts_details.shipped_quantity, im.part_number,im.price,im.gst_rate, spare_parts_details.partner_id,booking_details.assigned_vendor_id,spare_consumption_status.consumed_status';
                                $part_details_challan = $this->partner_model->get_spare_parts_by_any($select, $where_clause, true, false, false, $post);
                                //Recreate Challan file if shipped part is different from requested part
                               if (!empty($part_details_challan) && ($part_details_challan[0]['partner_challan_number']=='' || $part_details_challan[0]['requested_inventory_id']!=$part_details_challan[0]['shipped_inventory_id'])) {
                                    if($part_details_challan[0]['requested_inventory_id']!=$part_details_challan[0]['shipped_inventory_id'] && $part_details['part_warranty_status'] == SPARE_PART_IN_OUT_OF_WARRANTY_STATUS){
                                        $margin = $this->inventory_model->get_oow_margin($part_details_challan[0]['shipped_inventory_id'], array('part_type' => $part_details_challan[0]['shipped_parts_type'],'inventory_parts_type.service_id' => $part_details_challan[0]['service_id']));
                                        $estimate_cost = round((($part_details_challan[0]['price'] + ( $part_details_challan[0]['price'] * $part_details_challan[0]['gst_rate']) / 100) * $part_details_challan[0]['shipped_quantity']), 2);
                                        $spare_oow_est_margin = $margin['oow_est_margin']/100;
                                        $spare_oow_around_margin=$margin['oow_around_margin']/100;
                                        $part_details_challan['gst_rate'] = $margin['gst_rate'];
                                        $data_price_update['purchase_price'] = $estimate_cost;
                                        $data_price_update['sell_price'] = ($estimate_cost + $estimate_cost * $spare_oow_est_margin );
                                        $data_price_update['challan_approx_value'] = ($estimate_cost + $estimate_cost * $spare_oow_around_margin);
                                        $where_price_update = array('id' => $spare_id);
                                        $this->service_centers_model->update_spare_parts($where_price_update, $data_price_update);
                                    }
                                    $this->generate_challan_to_sf($part_details_challan);
                                }
                                if (!empty($part_details_challan) && ($part_details_challan[0]['partner_challan_number']!='' && $part_details_challan[0]['requested_inventory_id']!=$part_details_challan[0]['shipped_inventory_id'])) {
                                  $generate_bulk_challan = true;
                                }
                                $part_details_challan_bulk[]=$part_details_challan[0];
                            }

                            if ($response) {
                                
                                if ($this->input->post('is_wh') && !empty($data['shipped_inventory_id'])) {
                                    //update inventory stocks
                                    $data['receiver_entity_id'] = $this->input->post('assigned_vendor_id');
                                    $data['receiver_entity_type'] = _247AROUND_SF_STRING;
                                    $data['sender_entity_id'] = $sf_id;
                                    $data['sender_entity_type'] = _247AROUND_SF_STRING;
                                    $data['stock'] = -$data['shipped_quantity'];
                                    $data['booking_id'] = $booking_id;
                                    $data['agent_id'] = $sf_id;
                                    $data['agent_type'] = _247AROUND_SF_STRING;
                                    $data['is_wh'] = TRUE;
                                    $data['inventory_id'] = $data['shipped_inventory_id'];
                                    $data['spare_id'] = $spare_id;
                                    $this->miscelleneous->process_inventory_stocks($data);
                                }

                                if ($part_details['part_warranty_status'] == SPARE_PART_IN_OUT_OF_WARRANTY_STATUS) {
                                    // Send OOW invoice to aditya
                                    $url = base_url() . "employee/invoice/generate_oow_parts_invoice/" . $spare_id;
                                    $async_data['booking_id'] = $booking_id;
                                    $this->asynchronous_lib->do_background_process($url, $async_data);
                                    if(count($part) > 1){
                                    sleep(20);
                                    }
                                }
                            }
                        } else {
                            log_message('info', __FUNCTION__ . '=> Stock is not available. Spare parts booking is not updated by SF ' . $sf_id .
                                    " booking id " . $booking_id . " Data" . print_r($this->input->post(), true));

                            $userSession = array('stock_not_exist' => 'Shipped Inventory stocks not available on warehouse for Part Name ' . $part_details['shipped_parts_name']);
                            $this->session->set_userdata($userSession);
                        }
                    } else if ($part_details['shippingStatus'] == 0) {
                        
                        $can_status = SPARE_PARTS_CANCELLED;
                        $this->insert_details_in_state_change($booking_id, SPARE_PARTS_CANCELLED, "Warehouse Reject Spare Part", "", "", $part_details['spare_id']);
                        $response = $this->service_centers_model->update_spare_parts(array("id" => $part_details['spare_id']), array('status' => _247AROUND_CANCELLED, "old_status" => SPARE_PARTS_REQUESTED));
                        if ($response) {
                            $spare_data = $this->inventory_model->get_spare_parts_details('spare_parts_details.booking_unit_details_id', array("spare_parts_details.id" => $part_details['spare_id']), false, false);
                            if (!empty($spare_data)) {
                                $this->booking_model->update_booking_unit_details_by_any(array("booking_unit_details.id" => $spare_data[0]['booking_unit_details_id']), array("booking_unit_details.booking_status" => _247AROUND_CANCELLED));
                            }

                            /* Insert Spare Tracking Details */
                            if (!empty($part_details['spare_id'])) {
                                $tracking_details = array('spare_id' => $part_details['spare_id'], 'action' => $can_status, 'remarks' => 'Warehouse Reject Spare Part', 'agent_id' => $this->session->userdata("service_center_agent_id"), 'entity_id' => $this->session->userdata('service_center_id'), 'entity_type' => _247AROUND_SF_STRING);
                                $this->service_centers_model->insert_spare_tracking_details($tracking_details);
                            }
                        }
                        $data['quantity'] = $part_details['quantity'];

                    } else if ($part_details['shippingStatus'] == -1) {
                        /* Insert Spare Tracking Details */
                        $shipped_parts_name = "";
                        if(!empty($part_details['shipped_parts_name'])){
                            $shipped_parts_name = $part_details['shipped_parts_name'];
                        }
                            if (!empty($part_details['spare_id'])) {
                                $tracking_details = array('spare_id' => $part_details['spare_id'], 'action' => "SPARE TO BE SHIP", 'remarks' => "Warehouse Update - " . $shipped_parts_name . " To Be Shipped", 'agent_id' => $this->session->userdata("service_center_agent_id"), 'entity_id' => $this->session->userdata('service_center_id'), 'entity_type' => _247AROUND_SF_STRING);
                                $this->service_centers_model->insert_spare_tracking_details($tracking_details);
                            }
                        $this->insert_details_in_state_change($booking_id, "SPARE TO BE SHIP", "Warehouse Update - " . $shipped_parts_name . " To Be Shipped", "", "", $part_details['spare_id']);
                    }
                }
                //If challan was already generated and some part shipped different from requested then regenerate challan
                if(!empty($part_details_challan_bulk) && $generate_bulk_challan == true){
                    $this->generate_challan_to_sf($part_details_challan_bulk);
                }

                if ($status) {

                    if (!empty($awb)) {
                        $sc_details = $this->vendor_model->getVendorDetails("district, state", array('service_centres.id' => $sf_id));
                        $from_city = $sc_details[0]['district'];
                        $from_state = $sc_details[0]['state'];
                        
                        $vendor_details = $this->vendor_model->getVendorDetails("district, state", array('service_centres.id' => $this->input->post('assigned_vendor_id')));
                        $to_city = $vendor_details[0]['district'];
                        $to_state = $vendor_details[0]['state'];
                        
                        $exist_courier_details = $this->inventory_model->get_generic_table_details('courier_company_invoice_details', 'courier_company_invoice_details.id,courier_company_invoice_details.awb_number', array('awb_number' => $awb), array());
                        if (empty($exist_courier_details[0])) {
                            $awb_data = array(
                                'awb_number' => trim($awb),
                                'company_name' => trim($this->input->post('courier_name')),
                                'partner_id' => trim($partner_id),
                                'courier_charge' => trim($this->input->post('courier_price_by_partner')),
                                'box_count' => trim($this->input->post('shipped_spare_parts_boxes_count')),
                                'billable_weight' => trim($billable_weight),
                                'actual_weight' => trim($billable_weight),
                                'basic_billed_charge_to_partner' => trim($this->input->post('courier_price_by_partner')),
                                'booking_id' => trim($this->input->post('booking_id')),
                                'courier_invoice_file' => trim($courier_image['message']),
                                'shippment_date' => trim($this->input->post('shipment_date')),
                                'created_by' => 3,
                                'is_exist' => 0,
                                'sender_city' => $from_city,
                                'receiver_city' => $to_city,
                                'sender_state' => $from_state,
                                'receiver_state' => $to_state
                            );

                            $this->service_centers_model->insert_into_awb_details($awb_data);
                        }
                    }

                    $sc_data['current_status'] = "InProcess";
                    $sc_data['internal_status'] = SPARE_PARTS_SHIPPED_BY_WAREHOUSE;
                    $this->vendor_model->update_service_center_action($booking_id, $sc_data);

                    /***
                     * Check spare part pending in request.
                     * If not then update booking internal status and dependency.
                     */
                    $check_pending_spare = $this->partner_model->get_spare_parts_by_any('spare_parts_details.*', array('spare_parts_details.booking_id' => $booking_id, 'status IN ("'. SPARE_PARTS_REQUESTED . '", "' . SPARE_PART_ON_APPROVAL . '", "' . SPARE_OOW_EST_REQUESTED . '") ' => NULL), TRUE, false, false);
                    $actor = $next_action = 'not_define';
                    if(empty($check_pending_spare)) {
                        $booking['internal_status'] = SPARE_PARTS_SHIPPED_BY_WAREHOUSE;
                        $partner_status = $this->booking_utilities->get_partner_status_mapping_data(_247AROUND_PENDING, $booking['internal_status'], $partner_id, $booking_id);
                        if (!empty($partner_status)) {
                            $booking['partner_current_status'] = $partner_status[0];
                            $booking['partner_internal_status'] = $partner_status[1];
                            $actor = $booking['actor'] = $partner_status[2];
                            $next_action = $booking['next_action'] = $partner_status[3];
                        }
                        $this->booking_model->update_booking($booking_id, $booking);
                    } else {
                        /**
                         * Check booking internal status is spare parts requested 
                         * then update actor (partner) if part requested pending on partner
                         */
                        // fetch booking details
                        $booking_internal_details = $this->booking_model->get_booking_details('*',['booking_id' => $booking_id])[0]['internal_status'];
                        if(!empty($booking_internal_details) && in_array($booking_internal_details, [SPARE_PARTS_REQUIRED, SPARE_PARTS_REQUESTED])) { 
                            // fetch all requested parts
                            $pending_spare_parts_details = $this->partner_model->get_spare_parts_by_any('spare_parts_details.*', array('spare_parts_details.booking_id' => $booking_id,'spare_parts_details.status' => SPARE_PARTS_REQUESTED), TRUE, TRUE, false);
                            if(!empty($pending_spare_parts_details)) {
                                $entity_types = array_unique(array_column($pending_spare_parts_details, 'entity_type'));
                                // if no part request pending on warehouse then set Partner.
                                if(in_array(_247AROUND_PARTNER_STRING, $entity_types)) {
                                    $this->booking_model->update_booking($booking_id, ['actor' => _247AROUND_PARTNER_STRING]);
                                }
                            }
                        }
                    }
                    
                    $userSession = array('success' => 'Parts Updated');
                    $this->session->set_userdata($userSession);
                    if(!empty($this->session->userdata('warehouse_id'))) {
                        redirect(base_url() . "service_center/inventory");
                    } else {
                        if ($wh) {
                            redirect(base_url() . "service_center/inventory");
                        } else {
                            redirect(base_url() . "service_center/spare_parts");
                        }
                    }
                } else {
                    if ($can_status == SPARE_PARTS_CANCELLED) {
                        $sc_data['current_status'] = _247AROUND_PENDING;
                        $sc_data['internal_status'] = _247AROUND_PENDING;
                        $this->vendor_model->update_service_center_action($booking_id, $sc_data);

                        $booking['internal_status'] = SPARE_PARTS_CANCELLED;
                        $actor = $next_action = 'not_define';
                        $partner_status = $this->booking_utilities->get_partner_status_mapping_data(_247AROUND_PENDING, $booking['internal_status'], $partner_id, $booking_id);
                        if (!empty($partner_status)) {
                            $booking['partner_current_status'] = $partner_status[0];
                            $booking['partner_internal_status'] = $partner_status[1];
                            $actor = $booking['actor'] = $partner_status[2];
                            $next_action = $booking['next_action'] = $partner_status[3];
                        }

                        $this->booking_model->update_booking($booking_id, $booking);

                        $userSession = array('success' => 'Parts Cancelled Successfully');
                        $this->session->set_userdata($userSession);
                        redirect(base_url() . "service_center/spare_parts");
                    }
                }

                log_message('info', __FUNCTION__ . '=> Spare parts booking is not updated by SF ' . $sf_id .
                        " booking id " . $booking_id . " Data" . print_r($this->input->post(), true));
                $userSession = array('error' => 'Parts Not Updated');
                $this->session->set_userdata($userSession);
                redirect(base_url() . "service_center/update_spare_parts_form/" . $booking_id . "/" . $wh);
            } else {
                log_message('info', __FUNCTION__ . '=> Spare parts booking is not updated by SF ' . $sf_id .
                        " booking id " . $booking_id . " Data" . print_r($this->input->post(), true));
                $userSession = array('error' => $courier_image['message']);
                $this->session->set_userdata($userSession);
                redirect(base_url() . "service_center/update_spare_parts_form/" . $booking_id . "/" . $wh);
            }
        //}
    }

    /**
     * @desc: This function is used to generate SF challan if partner challan not exist in spare_parts_details.
     * @param array $part_details
     * @return Void
     */
    function generate_challan_to_sf($part_details) {
        $delivery_challan_file_name_array = array();
        if (!empty($part_details)) {
            $spare_details = array();
            foreach ($part_details as $value) {
                $spare_parts = array();
                //if ($value['part_warranty_status'] !== SPARE_PART_IN_OUT_OF_WARRANTY_STATUS) {
                    $spare_parts['spare_id'] = $value['id'];
                    $spare_parts['booking_id'] = $value['booking_id'];
                    if(!empty($value['parts_shipped'])){
                        $spare_parts['parts_shipped'] = $value['parts_shipped']; // Generate challan on Shipped Part.
                        $spare_parts['shipped_quantity'] = $value['shipped_quantity'];
                    }else{
                        $spare_parts['parts_shipped'] = $value['parts_requested'];
                        $spare_parts['shipped_quantity'] = $value['quantity'];
                    }
                    $spare_parts['challan_approx_value'] = $value['challan_approx_value'];
                    $spare_parts['part_number'] = $value['part_number'];
                    $spare_parts['inventory_id'] = $value['shipped_inventory_id'];
                    $spare_parts['consumption'] = $value['consumed_status']; 
                    $spare_parts['gst_rate'] = $value['gst_rate'];
                //}
                $spare_details[][] = $spare_parts;
            }
            $assigned_vendor_id = $part_details[0]['partner_id'];
            $service_center_id = $part_details[0]['assigned_vendor_id'];
        }

        $sf_details = $this->vendor_model->getVendorDetails('name as company_name,address,district, pincode, state,sc_code,is_gst_doc,owner_name,signature_file,gst_no,is_signature_doc,primary_contact_name as contact_person_name, primary_contact_phone_1 as contact_number, service_centres.gst_no as gst_number, state', array('id' => $service_center_id));

        if (!empty($part_details)) {
            $select = "concat('C/o ',contact_person.name,',', warehouse_address_line1,',',warehouse_address_line2,',',warehouse_details.warehouse_city,' Pincode -',warehouse_pincode, ',',warehouse_details.warehouse_state) as address,contact_person.name as contact_person_name,contact_person.official_contact_number as contact_number,service_centres.gst_no as gst_number, warehouse_state as state";

            $where = array('contact_person.entity_id' => $part_details[0]['partner_id'],
                'contact_person.entity_type' => $part_details[0]['entity_type']);
            $wh_address_details = $this->inventory_model->get_warehouse_details($select, $where, false, true, true);

            $partner_details = array();

            if ($part_details[0]['entity_type'] == _247AROUND_PARTNER_STRING) {
                $partner_details = $this->partner_model->getpartner_details('company_name, address,gst_number,primary_contact_name as contact_person_name ,primary_contact_phone_1 as contact_number, state', array('partners.id' => $part_details[0]['partner_id']));
            } else if ($part_details[0]['entity_type'] === _247AROUND_SF_STRING) {
                $partner_details = $this->vendor_model->getVendorDetails('name as company_name,address,owner_name,gst_no as gst_number, state', array('id' => $part_details[0]['partner_id']));
            }

            if (!empty($wh_address_details)) {
                $partner_details[0]['address'] = $wh_address_details[0]['address'];
                $partner_details[0]['contact_person_name'] = $wh_address_details[0]['contact_person_name'];
                $partner_details[0]['contact_number'] = $wh_address_details[0]['contact_number'];
                $partner_details[0]['state'] = $wh_address_details[0]['state'];
            }
        }


        $data = array();
        if (!empty($sf_details)) {
            $data['partner_challan_number'] = $this->miscelleneous->create_sf_challan_id($sf_details[0]['sc_code'], true);
            $sf_details[0]['address'] = $sf_details[0]['address'] . ", " . $sf_details[0]['district'] . ", Pincode -" . $sf_details[0]['pincode'] . ", " . $sf_details[0]['state'];
        }

        if (!empty($spare_details)) {
            $data['partner_challan_file'] = $this->invoice_lib->process_create_sf_challan_file($sf_details, $partner_details, $data['partner_challan_number'], $spare_details,'','',false,true);
            array_push($delivery_challan_file_name_array, $data['partner_challan_file']);
            if (!empty($data['partner_challan_file'])) {
                if (!empty($spare_details)) {
                    foreach ($spare_details as $val) {
                        $this->service_centers_model->update_spare_parts(array('id' => $val[0]['spare_id']), $data);
                    }
                }
            }
        }
        
        foreach ($delivery_challan_file_name_array as $value_unlink) {
            unlink(TMP_FOLDER . $value_unlink);
        }
    }

    /**
     * @desc: This function is used to received the defective parts shipped by SF to 247around warehouse
     * @param String $booking_id
     * @param String $partner_id
     * @param String $is_cron
     * @return Void
     */
    function acknowledge_received_defective_parts($spare_id, $booking_id, $partner_id, $is_cron = "") {
        $sf_id = "";
        if (empty($is_cron)) {
            if (!empty($this->session->userdata('warehouse_id'))) { 
                $this->checkEmployeeUserSession();
                $sf_id = $this->session->userdata('warehouse_id');
            } else {
                $this->check_WH_UserSession();
                $sf_id = $this->session->userdata('service_center_id');
            }
        }
        log_message('info', __FUNCTION__ . " SF ID: " . $sf_id . " Booking Id " . $booking_id);        
        
        // We will return this array instead of sending mail from here, we will fetch this data to send mail later at once
        $email_content = array();
        $post_data = $this->input->post();
        $this->validate_received_defective_part_pic_file();
        $receive_defective_pic_by_wh = $this->input->post("receive_defective_pic_by_wh");
        $defective_parts_shipped_kg = $this->input->post("defective_parts_shipped_kg") ? : 0;
        $defective_parts_shipped_gram = $this->input->post("defective_parts_shipped_gram") ? : 000;
        $received_weight = $defective_parts_shipped_kg.".".$defective_parts_shipped_gram;
        
        if (!empty($post_data['consumption_data'])) { // if you receive multiple part.
            $consumption_data = json_decode($post_data['consumption_data'], true);
            $post_data['remarks'] = $consumption_data['remarks'];
            $post_data['spare_consumption_status'][$spare_id] = $consumption_data['consumed_status_id'];
            unset($post_data['consumption_data']);
        }
        // fetch record from booking details of $booking_id.
        $booking_details = $this->booking_model->get_booking_details('*',['booking_id' => $booking_id])[0];
        $spare_part_detail = $this->reusable_model->get_search_result_data('spare_parts_details', '*', ['id' => $spare_id], NULL, NULL, NULL, NULL, NULL)[0];
        //Return false is Spare is already defective return acknowledged
        $defective_part_received_date_by_wh = $spare_part_detail['defective_part_received_date_by_wh'];
        if(!empty($defective_part_received_date_by_wh)){
            echo json_encode(array('This Defective / OK part is already acknowledged.', ''));
            return false;
        }
        if(!empty($post_data['spare_consumption_status'][$spare_id]) && $post_data['spare_consumption_status'][$spare_id]!=1 && $spare_part_detail['defective_return_to_entity_id']!=$sf_id){
            echo json_encode(array('You can not acknowledge this part as ok, as original spare was not shipped from this warehouse.', ''));
            return false;
        }
      
       
        //
        if (!empty($post_data['spare_consumption_status'][$spare_id]) && ($post_data['spare_consumption_status'][$spare_id] != $spare_part_detail['consumed_part_status_id'])) {
            $this->miscelleneous->change_consumption_by_warehouse($post_data, $booking_id);
        }
        $spare_part_detail = $this->reusable_model->get_search_result_data('spare_parts_details', '*', ['id' => $spare_id], NULL, NULL, NULL, NULL, NULL)[0];
        $spare_status = DEFECTIVE_PARTS_RECEIVED_BY_WAREHOUSE;
        if (!empty($spare_part_detail['consumed_part_status_id'])) {
            $spare_consumption_status_tag = $this->reusable_model->get_search_result_data('spare_consumption_status', '*', ['id' => $spare_part_detail['consumed_part_status_id']], NULL, NULL, NULL, NULL, NULL)[0];
            /**
             * check whether stock is handled by central warehouse or not.
             * @modifiedBy Ankit Rajvanshi
             */
            $is_inventory_handled_by_247 = $this->inventory_model->check_stock_handled_by_central_wh($booking_details, $spare_part_detail);
            if (!empty($is_inventory_handled_by_247) && !empty($spare_part_detail['shipped_inventory_id']) && in_array($spare_consumption_status_tag['tag'], [PART_SHIPPED_BUT_NOT_USED_TAG, WRONG_PART_RECEIVED_TAG, DAMAGE_BROKEN_PART_RECEIVED_TAG])) {
               //send email
                $email_content = $this->send_mail_for_parts_received_by_warehouse($booking_id, $spare_id);
                //update inventory stocks
                $is_entity_exist = $this->reusable_model->get_search_query('inventory_stocks', 'inventory_stocks.id', array('entity_id' => $sf_id, 'entity_type' => _247AROUND_SF_STRING, 'inventory_id' => $spare_part_detail['shipped_inventory_id']), NULL, NULL, NULL, NULL, NULL)->result_array();
                if (!empty($is_entity_exist)) {
                    $stock = "stock + '" . $spare_part_detail['shipped_quantity'] . "'";
                    $update_stocks = $this->inventory_model->update_inventory_stock(array('id' => $is_entity_exist[0]['id']), $stock);
                } else {
                    $insert_data = [];
                    $insert_data['entity_id'] = $sf_id;
                    $insert_data['entity_type'] = _247AROUND_SF_STRING;
                    $insert_data['inventory_id'] = $spare_part_detail['shipped_inventory_id'];
                    $insert_data['stock'] = $spare_part_detail['shipped_quantity'];
                    $insert_data['create_date'] = date('Y-m-d H:i:s');

                    $this->inventory_model->insert_inventory_stock($insert_data);
                }
                
                /* 
                 * Update receiver entity id in inventory ledger.
                 */
                $ledger_data = [
                    'receiver_entity_id' => $sf_id,
                    'receiver_entity_type' => _247AROUND_SF_STRING,
                    'is_wh_ack' => 1,
                    'wh_ack_date' => date('Y-m-d H:i:s')
                ];
                
                $ledger_where = [
                    'spare_id' => $spare_id,
                    'is_defective' => 1
                ];
                
                $this->inventory_model->update_ledger_details($ledger_data, $ledger_where);                
            }
            
            $is_spare_consumed = $this->reusable_model->get_search_result_data('spare_consumption_status', '*', ['id' => $spare_part_detail['consumed_part_status_id']], NULL, NULL, NULL, NULL, NULL)[0]['is_consumed'];
            if(!empty($is_spare_consumed) && $is_spare_consumed == 1) {
                $spare_status = DEFECTIVE_PARTS_RECEIVED_BY_WAREHOUSE;
            } else {
                $spare_status = Ok_PARTS_RECEIVED_BY_WAREHOUSE;
            }
        }
        
        $response = $this->service_centers_model->update_spare_parts(array('id' => $spare_id), array('status' => $spare_status,
            'defective_return_to_entity_id' => $sf_id, 'defective_return_to_entity_type' => _247AROUND_SF_STRING,
            'defective_part_received_by_wh' => 1, 'remarks_defective_part_by_wh' => $spare_status,
            'defective_part_received_date_by_wh' => date("Y-m-d H:i:s"), 'received_defective_part_pic_by_wh' => $receive_defective_pic_by_wh));

        /* Insert Spare Tracking Details */
        if (!empty($spare_id)) {
            $tracking_details = array('spare_id' => $spare_id, 'action' => $spare_status, 'remarks' => $post_data['remarks']);
            if(!empty($this->session->userdata('warehouse_id'))) {
                $tracking_details['agent_id'] = $this->session->userdata('id');
                $tracking_details['entity_id'] = _247AROUND;
                $tracking_details['entity_type'] = _247AROUND_EMPLOYEE_STRING;
            } else { 
                $tracking_details['agent_id'] = $this->session->userdata('service_center_agent_id');
                $tracking_details['entity_id'] = $this->session->userdata('service_center_id');
                $tracking_details['entity_type'] = _247AROUND_SF_STRING;
            }            
            $this->service_centers_model->insert_spare_tracking_details($tracking_details);
        }
        if ($response) {
            
            if(!empty($spare_part_detail['awb_by_sf'])){
                $this->inventory_model->update_courier_company_invoice_details(array('awb_number' => $spare_part_detail['awb_by_sf'], 'delivered_date IS NULL' => NULL), array('delivered_date' => date('Y-m-d H:i:s'), 'actual_weight' => $received_weight, "billable_weight" => $received_weight));
            }

            log_message('info', __FUNCTION__ . " Received Defective Spare Parts " . $booking_id
                    . " SF Id" . $sf_id);

            $sendUrl = base_url() . 'employee/invoice/generate_micro_reverse_sale_invoice/' . $spare_id;
            $this->asynchronous_lib->do_background_process($sendUrl, array());

            $is_exist = $this->partner_model->get_spare_parts_by_any("spare_parts_details.id, spare_parts_details.status", array('spare_parts_details.booking_id' => $booking_id, 'spare_parts_details.defective_part_required' => 1, "status NOT IN  ('" . _247AROUND_CANCELLED . "', '" . _247AROUND_COMPLETED
                . "', '" . DEFECTIVE_PARTS_RECEIVED . "', '" . DEFECTIVE_PARTS_RECEIVED_BY_WAREHOUSE . "', '" .Ok_PARTS_RECEIVED_BY_WAREHOUSE . "', '" . Ok_PARTS_RECEIVED . "') " => NULL));

            $actor = $next_action = 'not_define';
            if (empty($is_exist)) {
                $booking_internal_status = $spare_status;
            } else {
                $booking_internal_status = $is_exist[0]['status'];
            }
            
            // Change booking internal status if booking is completed.
            if($booking_details['current_status'] == _247AROUND_COMPLETED) {
                $booking = [];
                $booking['internal_status'] = $spare_status;
                $partner_status = $this->booking_utilities->get_partner_status_mapping_data(_247AROUND_COMPLETED, $booking['internal_status'], $partner_id, $booking_id);
                if (!empty($partner_status)) {
                    $booking['partner_current_status'] = $partner_status[0];
                    $booking['partner_internal_status'] = $partner_status[1];
                    $actor = $booking['actor'] = $partner_status[2];
                    $next_action = $booking['next_action'] = $partner_status[3];
                }
                $this->booking_model->update_booking($booking_id, $booking);
            }

            // "Warehouse Received Defective Spare Parts"
            $this->insert_details_in_state_change($booking_id, $spare_status, $post_data['remarks'], $actor, $next_action, $spare_id, $is_cron);

            $is_oow_return = $this->partner_model->get_spare_parts_by_any("booking_unit_details_id, purchase_price, sell_price, sell_invoice_id", array('spare_parts_details.id' => $spare_id,
                'booking_unit_details_id IS NOT NULL' => NULL,
                'sell_price > 0 ' => NULL,
                'sell_invoice_id IS NOT NULL' => NULL,
                'estimate_cost_given_date IS NOT NULL' => NULL,
                'spare_parts_details.part_warranty_status' => 2,
                'defective_part_required' => 1,
                '(approved_defective_parts_by_partner = 1 or defective_part_received_by_wh = 1)' => NULL,
                'status IN ("' . DEFECTIVE_PARTS_RECEIVED_BY_WAREHOUSE . '", "' . DEFECTIVE_PARTS_RECEIVED . '", "'.Ok_PARTS_RECEIVED.'", "'.Ok_PARTS_RECEIVED_BY_WAREHOUSE.'")' => NULL,
                '(reverse_sale_invoice_id IS NULL OR reverse_purchase_invoice_id IS NULL)' => NULL), true);
            if (!empty($is_oow_return)) {
                sleep(40);
                $url = base_url() . "employee/invoice/generate_reverse_oow_invoice/" . $spare_id;
                $async_data['booking_id'] = $booking_id;
                $this->asynchronous_lib->do_background_process($url, $async_data);
            }

            $message = '';
            if (empty($is_cron)) {
                $userSession = array('success' => ' Received Defective Spare Parts');
                $message = 'Parts have been received successfully.';
            //    exit;
//                $this->session->set_userdata($userSession);
//                redirect(base_url() . "service_center/defective_spare_parts");
            }
            echo json_encode(array($message, $email_content));
        } else { //if($response){
            log_message('info', __FUNCTION__ . '=> Defective Spare Parts not updated  by SF ' . $sf_id .
                    " booking id " . $booking_id);
            if (empty($is_cron)) {
                $userSession = array('success' => 'There is some error. Please try again.');
                $message = 'There is some error. Please try again.';
               // exit;
                //return json_encode($userSession);
//                $this->session->set_userdata($userSession);
//                redirect(base_url() . "service_center/defective_spare_parts");
            }
            echo json_encode(array($message, $email_content));
        }
        
    }
    
    
     /**
     * @desc This function is send mail when warehouse receive parts from SF

     */
    function send_mail_for_parts_received_by_warehouse($booking_id, $spare_id, $reason_text = '') {
        log_message('info', __FUNCTION__ . '=> trying to send email for booking id=' . $booking_id);
        $email_template = $this->booking_model->get_booking_email_template(WAREHOUSE_RECEIVE_PART_FROM_SF);
        $query = "SELECT spd.create_date, sc.name as service_centre_name, spd.parts_requested, spd.model_number,  spd.quantity, spd.defective_parts_pic, spd.remarks_by_sc as consumption_reason, case when il.sender_entity_type = 'vendor' then sc.name  else p.public_name end as shipped_by, scs.reason_text".
                 " FROM booking_details as bd, spare_parts_details as spd, service_centres as sc, inventory_ledger as il, partners as p, spare_consumption_status as scs".
                 " WHERE bd.booking_id = spd.booking_id and bd.assigned_vendor_id = sc.id and spd.booking_id = il.booking_id and spd.consumed_part_status_id = scs.id and p.id=bd.partner_id and bd.booking_id = ? and spd.id=? order by il.id limit 1";
        $params = array($booking_id, $spare_id);
        $results = execute_paramaterised_query($query, $params);
        if (!empty($email_template) && $results) {
            if($reason_text == ''){
                $reason_text = $results[0]['reason_text'];
            }
            $to = $email_template[1];
            $cc = $email_template[3];
            $bcc = $email_template[5];
            $subject = vsprintf($email_template[4], array());
            $emailBody = vsprintf($email_template[0], array($booking_id, $results[0]['service_centre_name'], $results[0]['create_date'], $results[0]['shipped_by'], $results[0]['parts_requested'], $results[0]['model_number'], $results[0]['quantity'], $reason_text, $this->session->userdata('wh_name'), "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/misc-images/".$results[0]['defective_parts_pic']));
            return array($email_template[2], $to, $cc, $bcc, $subject, $emailBody, "", WAREHOUSE_RECEIVE_PART_FROM_SF, "", $booking_id);
        //    $this->notify->sendEmail($email_template[2], $to, $cc, $bcc, $subject, $emailBody, "", WAREHOUSE_RECEIVE_PART_FROM_SF, "", $booking_id);
        }
        else{
            log_message('info', __FUNCTION__ . '=> Email details not found for booking id=' . $booking_id);
        }
            
    }
    
    /*
     * @desc: This function is used to send mail when warehouse receive parts from SF
     * @params: String $from
     * @params: String $to
     * @params: String $cc
     * @params: String $bcc
     * @params: String $subject
     * @params: String $email_body
     * @params: String $template
     * @params: String $booking_id
     * @return: Void
     */
    function send_email_acknowledge_received_defective_parts(){
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $cc = $this->input->post('cc');
        $bcc = $this->input->post('bcc');
        $subject = $this->input->post('subject');
        $emailBody = $this->input->post('email_body');
        $template = $this->input->post('template');
        $booking_id = $this->input->post('booking_id');
        $this->notify->sendEmail($from, $to, $cc, $bcc, $subject, $emailBody, "", $template, "", $booking_id);
    }

    /*
     * @desc: This function is used to validate Received defective part on WH.
     * @params: void
     * @return: boolean
     */

    function validate_received_defective_part_pic_file() {
        $received_defective_pic_exist = $this->input->post('received_defective_part_pic_by_wh_exist');
        if (!empty($_FILES['received_defective_part_pic_by_wh']['tmp_name'])) {
            $allowedExts = array("png", "jpg", "jpeg", "JPG", "JPEG", "PNG", "PDF", "pdf");
            $random_number = rand(0, 9);
            $part_image_receipt = $this->miscelleneous->upload_file_to_s3($_FILES["received_defective_part_pic_by_wh"], "receive_defective_pic_by_wh", $allowedExts, $random_number, "misc-images", "receive_defective_pic_by_wh");
            if ($part_image_receipt) {
                return true;
            } else {
                $this->form_validation->set_message('validate_received_defective_part_pic_file', 'Received defective pic by WH, File size or file type is not supported. Allowed extentions are "png", "jpg", "jpeg" and "pdf". '
                        . 'Maximum file size is 5 MB.');
                return false;
            }
        } else if (!empty($received_defective_pic_exist)) {
            $_POST['receive_defective_pic_by_wh'] = $received_defective_pic_exist;
            return true;
        } else {
            $this->form_validation->set_message('validate_received_defective_part_pic_file', 'Please Upload Received defective Image');
            return FALSE;
        }
    }

    /**
     * @desc: this function is used to reject the defective parts shipped by SF to 247around warehouse
     * @param String $booking_id
     * @param String $partner_id
     * @param String $status
     * @return void
     */
    function reject_defective_part($spare_id, $booking_id, $partner_id) {
        
        if(!empty($this->session->userdata('warehouse_id'))) {
            $this->checkEmployeeUserSession();
            $sf_id = $this->session->userdata('warehouse_id');
        } else {
            $this->check_WH_UserSession();
            $sf_id = $this->session->userdata('service_center_id');
        }
        
        log_message('info', __FUNCTION__ . " Spare ID " . $spare_id . " SF ID: " . $sf_id  . " Booking Id " . $booking_id);
        
        $post_data = $this->input->post();
        $status = $post_data['reject_reason'][$spare_id];
        $rejection_reason = base64_decode(urldecode($status));
        $decode_partner_id = base64_decode(urldecode($partner_id));

        $this->validate_reject_defective_part_pic_file();

        // fetch record from booking details of $booking_id.
        $booking_details = $this->booking_model->get_booking_details('*',['booking_id' => $booking_id])[0];
        // feth record from spare parts details of $spare_id.
        $spare_part_detail = $this->reusable_model->get_search_result_data('spare_parts_details', '*', ['id' => $spare_id], NULL, NULL, NULL, NULL, NULL)[0];
        
        $spare_status = DEFECTIVE_PARTS_REJECTED_BY_WAREHOUSE;
        if(!empty($spare_part_detail['consumed_part_status_id'])) {
            $is_spare_consumed = $this->reusable_model->get_search_result_data('spare_consumption_status', '*', ['id' => $spare_part_detail['consumed_part_status_id']], NULL, NULL, NULL, NULL, NULL)[0]['is_consumed'];
            if(!empty($is_spare_consumed) && $is_spare_consumed == 1) {
                $spare_status = DEFECTIVE_PARTS_REJECTED_BY_WAREHOUSE;
            } else {
                $spare_status = OK_PARTS_REJECTED_BY_WAREHOUSE;
            }
        }
        
        $data = array(
            'status' => $spare_status,
            'remarks_defective_part_by_wh' => $rejection_reason,
            'defective_part_rejected_by_wh' => 1,
            'defective_part_received_by_wh' => '0',
            'rejected_defective_part_pic_by_wh' => $this->input->post('rejected_defective_part_pic_by_wh'),
            'defective_part_shipped_date'=> NULL
        );

        $response = $this->service_centers_model->update_spare_parts(array('id' => $spare_id), $data);
        /* Insert Spare Tracking Details */
        if (!empty($spare_id)) {
            $tracking_details = array('spare_id' => $spare_id, 'action' => $spare_status, 'remarks' => $rejection_reason);
            if(!empty($this->session->userdata('warehouse_id'))) {
                $tracking_details['agent_id'] = $this->session->userdata('id');
                $tracking_details['entity_id'] = _247AROUND;
                $tracking_details['entity_type'] = _247AROUND_EMPLOYEE_STRING;
            } else { 
                $tracking_details['agent_id'] = $this->session->userdata('service_center_agent_id');
                $tracking_details['entity_id'] = $this->session->userdata('service_center_id');
                $tracking_details['entity_type'] = _247AROUND_SF_STRING;
            }
            
            $this->service_centers_model->insert_spare_tracking_details($tracking_details);
        }
        if ($response) {
            log_message('info', __FUNCTION__ . " Sucessfully updated Table " . $booking_id
                    . " SF Id" . $this->session->userdata('service_center_id'));

            $actor = $next_action = 'not_define';
            if ($booking_details['current_status'] == _247AROUND_COMPLETED) {
                $booking['internal_status'] = $spare_status;

                $partner_status = $this->booking_utilities->get_partner_status_mapping_data(_247AROUND_COMPLETED, $booking['internal_status'], $decode_partner_id, $booking_id);
                if (!empty($partner_status)) {
                    $booking['partner_current_status'] = $partner_status[0];
                    $booking['partner_internal_status'] = $partner_status[1];
                    $actor = $booking['actor'] = $partner_status[2];
                    $next_action = $booking['next_action'] = $partner_status[3];
                }
                $this->booking_model->update_booking($booking_id, $booking);
            }

            //DEFECTIVE/OK_PARTS_REJECTED_BY_WAREHOUSE
            $this->insert_details_in_state_change($booking_id, $rejection_reason, $post_data['remarks'], $actor, $next_action, "", $spare_id);
            
            echo 'Part Rejected By Warehouse';
            exit;
//            $userSession = array('success' => 'Defective Parts Rejected To SF');
//            $this->session->set_userdata($userSession);
//            redirect(base_url() . "service_center/defective_spare_parts");
        } else { //if($response){
            log_message('info', __FUNCTION__ . '=> Defective Spare Parts Not Updated by SF' . $sf_id .
                    " booking id " . $booking_id);
            echo 'There is some error. Please try again.';
            exit;
//            $userSession = array('success' => 'There is some error. Please try again.');
//            $this->session->set_userdata($userSession);
//            redirect(base_url() . "service_center/defective_spare_parts");
        }
    }

    /*
     * @desc: This function is used to validate Received defective part on WH.
     * @params: void
     * @return: boolean
     */

    function validate_reject_defective_part_pic_file() {
        $rejected_defective_pic_exist = $this->input->post('rejected_defective_part_pic_by_wh_exist');
        if (!empty($_FILES['rejected_defective_part_pic_by_wh']['tmp_name'])) {
            $allowedExts = array("png", "jpg", "jpeg", "JPG", "JPEG", "PNG", "PDF", "pdf");
            $random_number = rand(0, 9);
            $part_image_receipt = $this->miscelleneous->upload_file_to_s3($_FILES["rejected_defective_part_pic_by_wh"], "rejected_defective_part_pic_by_wh", $allowedExts, $random_number, "misc-images", "rejected_defective_part_pic_by_wh");
            if ($part_image_receipt) {
                return true;
            } else {
                $this->form_validation->set_message('validate_reject_defective_part_pic_file', 'Received defective pic by WH, File size or file type is not supported. Allowed extentions are "png", "jpg", "jpeg" and "pdf". '
                        . 'Maximum file size is 5 MB.');
                return false;
            }
        } else if (!empty($rejected_defective_pic_exist)) {
            $_POST['receive_defective_pic_by_wh'] = $received_defective_pic_exist;
            return true;
        } else {
            $this->form_validation->set_message('validate_reject_defective_part_pic_file', 'Please Upload Received defective Image');
            return FALSE;
        }
    }

    /**
     * @desc: This function is used to download courier manifest/address for selected bookings 
     *        from spare parts page
     * @param void
     * @return void
     */
    function print_all() {
        if (!empty($this->session->userdata('warehouse_id'))) {
            $this->checkEmployeeUserSession();
        } else {
            $this->check_WH_UserSession();
        }
        log_message('info', __FUNCTION__ . " SF ID: " . $this->session->userdata('service_center_id'));
        $booking_address = $this->input->post('download_address');
        $booking_manifest = $this->input->post('download_courier_manifest');
        $declaration_detail = $this->input->post('coueriers_declaration');
        $generate_challan = $this->input->post('generate_challan');
        $assign_to_partner = $this->input->post('generate_challan_assign_to_partner');
        

        if (!empty($booking_address)) {

            $this->download_shippment_address($booking_address);
        } else if (!empty($booking_manifest)) {

            $this->download_mainfest($booking_manifest);
        } else if (!empty($generate_challan)) {

            $this->generate_sf_challan($generate_challan, false);
        } else if (!empty($assign_to_partner)) {

            $this->generate_sf_challan($assign_to_partner, true);
        } else if (!empty($declaration_detail)) {
            $this->print_declaration_detail();
        } else if (empty($booking_address) && empty($booking_manifest) && empty($declaration_detail) && empty($assign_to_partner)) {
            echo "Please Select Any Checkbox";
        }
    }

    /**
     * @desc: This is used to print  address for selected booking
     * @param Array $booking_address
     * @return void 
     */
    function forcefully_generate_sf_challan($booking_id, $sf_id) {
        $generate_challan = array("$sf_id" => $booking_id);

        $this->generate_sf_challan($generate_challan, false);
    }

    /**
     * @desc: This is used to print  address for selected booking
     * @param Array $booking_address
     * @return void 
     */
    function download_shippment_address($booking_address) {
        if(!empty($this->session->userdata('warehouse_id'))) {
            $this->checkEmployeeUserSession();
            $sf_id = $this->session->userdata('warehouse_id');
        } else {
            $this->check_WH_UserSession();
            $sf_id = $this->session->userdata('service_center_id');
        }

        log_message('info', __FUNCTION__ . " SF ID: " . $sf_id);

        $partner_on_saas = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        $main_partner = $this->partner_model->get_main_partner_invoice_detail($partner_on_saas);
        if (!empty($main_partner)) {
            $main_company_public_name = $main_partner['main_company_public_name'];
            $main_company_logo = $main_partner['main_company_logo'];
        } else {
            $main_company_public_name = "";
            $main_company_logo = "";
        }

        $booking_history['details'] = array();
        foreach ($booking_address as $key => $value) {
            $select = "contact_person.name as  primary_contact_name,contact_person.official_contact_number as primary_contact_phone_1,contact_person.alternate_contact_number as primary_contact_phone_2,"
                    . "concat(warehouse_address_line1,',',warehouse_address_line2) as address,warehouse_details.warehouse_city as district,"
                    . "warehouse_details.warehouse_pincode as pincode,"
                    . "warehouse_details.warehouse_state as state";

            $where = array('contact_person.entity_id' => $sf_id, 'contact_person.entity_type' => _247AROUND_SF_STRING);

            $wh_address_details = $this->inventory_model->get_warehouse_details($select, $where, FALSE);

            $wh_sf_details = $this->vendor_model->getVendorDetails('name as company_name,address,district,state,pincode,primary_contact_phone_1', array('id' => $sf_id))[0];

            $booking_history['details'][$key] = $this->booking_model->getbooking_history($value, "join")[0];
            $b_spare = $this->partner_model->get_spare_parts_by_any("Distinct parts_requested", array("booking_id" => $value, "entity_type" => "vendor", "partner_id" => $sf_id));
            if (!empty($b_spare)) {
                $part_name = implode(", ", array_unique(array_map(function ($k) {
                                    return $k['parts_requested'];
                                }, $b_spare)));

                $booking_history['details'][$key]['part_name'] = $part_name;
            } else {
                $booking_history['details'][$key]['part_name'] = "";
            }
            $b_unit = $this->booking_model->get_unit_details(array('booking_id' => $value), false, "appliance_brand");
            if (!empty($b_unit)) {
                $brand_name = implode(", ", array_unique(array_map(function ($k) {
                                    return $k['appliance_brand'];
                                }, $b_unit)));
                $booking_history['details'][$key]['brand_name'] = $brand_name;
            } else {
                $booking_history['details'][$key]['brand_name'] = "";
            }
            if (!empty($wh_address_details)) {
                $wh_address_details[0]['company_name'] = $wh_sf_details['company_name'];
                $booking_history['details'][$key]['partner'] = $wh_address_details[0];
            } else {
                $booking_history['details'][$key]['partner'] = $wh_sf_details;
            }

            $booking_history['details'][$key]['main_company_public_name'] = $main_company_public_name;
            $booking_history['details'][$key]['main_company_logo'] = $main_company_logo;
        }

        $this->load->view('partner/print_address', $booking_history);
    }

    /**
     * @desc: This is used to print courier manifest for selected booking
     * @param Array $booking_manifest
     * @return void
     */
    function download_mainfest($booking_manifest) {
        if(!empty($this->session->userdata('warehouse_id'))) {
            $this->checkEmployeeUserSession();
            $sf_id = $this->session->userdata('warehouse_id');
        } else {
            $this->check_WH_UserSession();
            $sf_id = $this->session->userdata('service_center_id');
        }

        log_message('info', __FUNCTION__ . " SF ID: " . $sf_id);
        $spare_parts_details['courier_manifest'] = array();
        foreach ($booking_manifest as $key => $value) {

            $where = "spare_parts_details.booking_id = '" . $value . "' AND status = '" . SPARE_PARTS_REQUESTED . "' "
                    . " AND booking_details.current_status IN ('" . _247AROUND_PENDING . "', '" . _247AROUND_RESCHEDULED . "') ";
            $spare_parts_details['courier_manifest'][$key] = $this->partner_model->get_spare_parts_booking($where)[0];
            $spare_parts_details['courier_manifest'][$key]['brand'] = $this->booking_model->get_unit_details(array('booking_id' => $value))[0]['appliance_brand'];
        }

        $this->load->view('partner/courier_manifest', $spare_parts_details);
    }

    /**
     * @desc this function is used to get the warehouse details
     * @param array $data this array contains the data for which we want warehouse details;
     * @return array $response
     */
    function get_warehouse_details($data, $partner_id) {
        $response = array();

        if (!empty($data['service_center_id'])) {
            $data['service_center_id'] = $data['service_center_id'];
        } else {
            $data['service_center_id'] = $this->session->userdata('service_center_id');
        }
        if (!empty($data['inventory_id'])) {
            return $this->miscelleneous->check_inventory_stock($data['inventory_id'], $partner_id, $data['state'], $data['service_center_id'], $data['model_number'], $data['quantity']);
        } else {
            $response = array();
        }
        return $response;
    }

    /**
     * @desc: This function is used to upload the challan file when partner shipped spare parts
     * @params: void
     * @return: $res
     */
    function upload_challan_file($id) {
        if (empty($_FILES['challan_file']['error'])) {
            $challan_file = "partner_challan_file_" . $this->input->post('booking_id') . "_" . $id . "_" . str_replace(" ", "_", $_FILES['challan_file']['name']);
            //Upload files to AWS
            $bucket = BITBUCKET_DIRECTORY;
            $directory_xls = "vendor-partner-docs/" . $challan_file;
            $this->s3->putObjectFile($_FILES['challan_file']['tmp_name'], $bucket, $directory_xls, S3::ACL_PUBLIC_READ);

            $res = $challan_file;
        } else {
            $res = FALSE;
        }

        return $res;
    }

    /**
     * @desc: This method is used to display list of booking which received by Partner
     * @param Integer $offset
     */
//    function get_approved_defective_parts_booking_by_warehouse($offset = 0) {
//        $this->check_WH_UserSession();
//        log_message('info', __FUNCTION__ . " SF ID: " . $this->session->userdata('service_center_id'));
//
//        //check if call from form submission or direct url
//        //used to filter the page by partner id
//
//        $config['per_page'] = 500;
//        $config['uri_segment'] = 3;
//        $config['first_link'] = 'First';
//        $config['last_link'] = 'Last';
//
//        if ($this->input->post('partner_id')) {
//
//            $partner_id = $this->input->post('partner_id');
//            $data['filtered_partner'] = $this->input->post('partner_id');
//            $sf_id = $this->session->userdata('service_center_id');
//
//            $where = "spare_parts_details.defective_return_to_entity_id = '" . $sf_id . "' AND spare_parts_details.defective_return_to_entity_type = '" . _247AROUND_SF_STRING . "'"
//                    . " AND defective_part_required = '1' AND status IN ('" . _247AROUND_COMPLETED . "') ";
//
//
//            $where .= " AND booking_details.partner_id = " . $partner_id;
//            $total_rows = $this->partner_model->get_spare_parts_booking_list($where, false, false, false);
//            $data['spare_parts'] = $this->partner_model->get_spare_parts_booking_list($where, $offset, $config['per_page'], true, 0, null, false, " ORDER BY status = spare_parts_details.booking_id ");
//        } else {
//
//            $data['spare_parts'] = array();
//            $total_rows = array(array("total_rows" => 0));
//        }
//
//        $config['base_url'] = base_url() . 'service_center/approved_defective_parts_booking_by_warehouse';
//
//        $config['total_rows'] = $total_rows[0]['total_rows'];
//
//
//        $this->pagination->initialize($config);
//        $data['links'] = $this->pagination->create_links();
//
//        $data['count'] = $config['total_rows'];
//
//
//        if (empty($this->input->post('is_ajax'))) {
//            $this->load->view('service_centers/header');
//            $this->load->view('service_centers/approved_defective_parts_by_warehouse', $data);
//        } else {
//            $this->load->view('service_centers/approved_defective_parts_by_warehouse', $data);
//        }
//    }

    function warehouse_ack_partner_list() {
        if(!empty($this->session->userdata('warehouse_id'))) {
            $sf_id = $this->session->userdata('warehouse_id');
        } else {
            $sf_id = $this->session->userdata('service_center_id');
        }
        $where = array("spare_parts_details.defective_return_to_entity_id" => $sf_id,
            "spare_parts_details.defective_return_to_entity_type" => _247AROUND_SF_STRING,
            "spare_parts_details.entity_type" => _247AROUND_SF_STRING,
            "spare_parts_details.defective_part_required" => 1,
            "spare_parts_details.is_micro_wh IN (1,2)" => NULL,
            "status IN ('" . _247AROUND_COMPLETED . "', '" . DEFECTIVE_PARTS_RECEIVED_BY_WAREHOUSE . "', '".Ok_PARTS_RECEIVED_BY_WAREHOUSE."') " => NULL);

        $partner_id = $this->partner_model->get_spare_parts_by_any(' Distinct booking_details.partner_id', $where, true);
        if (!empty($partner_id)) {
            $partners = array_unique(array_map(function ($k) {
                        return $k['partner_id'];
                    }, $partner_id));

            $data = $this->reusable_model->get_search_result_data("partners", "partners.id, partners.public_name", array(), NULL, NULL, array(), array('partners.id' => $partners), NULL, array());
            if (!empty($data)) {
                $option = '<option selected="" disabled="">Select Partner</option>';

                foreach ($data as $value) {
                    $option .= "<option value='" . $value['id'] . "'";
                    $option .= " > ";
                    $option .= $value['public_name'] . "</option>";
                }
                echo $option;
            } else {
                echo "Error";
            }
        } else {
            echo "Error";
        }
    }

    /**
     * @desc: This method is used partner list
     * @param Integer $offset
     */
    function get_partner_list() {
        if(!empty($this->session->userdata('warehouse_id'))) {
            $sf_id = $this->session->userdata('warehouse_id');
        } else {
            $sf_id = $this->session->userdata('service_center_id');
        }
        $where = array("spare_parts_details.defective_return_to_entity_id" => $sf_id,
            "spare_parts_details.defective_return_to_entity_type" => _247AROUND_SF_STRING,
            "spare_parts_details.entity_type" => _247AROUND_PARTNER_STRING,
            "spare_parts_details.defective_part_required" => 1,
            "spare_parts_details.is_micro_wh IN (0)" => NULL,
            "status IN ('" . _247AROUND_COMPLETED . "','" . DEFECTIVE_PARTS_RECEIVED_BY_WAREHOUSE . "','" . DEFECTIVE_PARTS_REJECTED_BY_WAREHOUSE . "','".Ok_PARTS_RECEIVED_BY_WAREHOUSE."') " => NULL
        );
//  For rejected also partner should come //
        $partner_id = $this->partner_model->get_spare_parts_by_any(' Distinct booking_details.partner_id', $where, true);
        if (!empty($partner_id)) {
            $partners = array_unique(array_map(function ($k) {
                        return $k['partner_id'];
                    }, $partner_id));

            $data = $this->reusable_model->get_search_result_data("partners", "partners.id, partners.public_name", array(), NULL, NULL, array(), array('partners.id' => $partners), NULL, array());
            if (!empty($data)) {
                $option = '<option selected="" disabled="">Select Partner</option>';

                foreach ($data as $value) {
                    $option .= "<option value='" . $value['id'] . "'";
                    $option .= " > ";
                    $option .= $value['public_name'] . "</option>";
                }
                echo $option;
            } else {
                echo "Error";
            }
        } else {
            echo "Error";
        }
    }

    /**
     * @desc: This method is used to display list of Received Defective Parts by Partner id
     * @param Integer $offset
     */
    function warehouse_task_list_tab_send_to_partner($offset = 0) {
        
        if(!empty($this->session->userdata('warehouse_id'))) { 
            $this->checkEmployeeUserSession();
            $sf_id = $this->session->userdata('warehouse_id');
        } else {
            $this->check_WH_UserSession();
            $sf_id = $this->session->userdata('service_center_id');
        }
        $data['sf_id'] = $sf_id;
        log_message('info', __FUNCTION__ . " SF ID: " . $sf_id);

        //check if call from form submission or direct url
        //used to filter the page by partner id

        if ($this->input->post('partner_id')) {
            $partner_id = $this->input->post('partner_id');
            $data['filtered_partner'] = $this->input->post('partner_id');
            
            $where = "spare_parts_details.defective_return_to_entity_id = '" . $sf_id . "' AND spare_parts_details.defective_return_to_entity_type = '" . _247AROUND_SF_STRING . "'"
                    . " AND defective_part_required = '1' AND reverse_purchase_invoice_id IS NULL AND status IN ('" . _247AROUND_COMPLETED . "','" . DEFECTIVE_PARTS_RECEIVED_BY_WAREHOUSE . "', '".Ok_PARTS_RECEIVED_BY_WAREHOUSE."') AND spare_parts_details.is_micro_wh IN (1,2) AND spare_parts_details.awb_by_wh IS NULL AND spare_parts_details.consumed_part_status_id IN (".PART_CONSUMED_STATUS_ID.") ";
            $where .= " AND booking_details.partner_id = " . $partner_id . " AND spare_parts_details.wh_challan_number IS NULL ";

            $data['spare_parts'] = $this->partner_model->get_spare_parts_booking_list($where, $offset, '', true, 0, null, false, " ORDER BY status = spare_parts_details.booking_id ");

            $to_gst_where = array(
                "entity_type" => _247AROUND_PARTNER_STRING,
                "entity_id" => $this->input->post("partner_id")
            );
            $data['to_gst_number'] = $this->inventory_model->get_entity_gst_data("entity_gst_details.id as id, gst_number, state_code.state as state", $to_gst_where);
        } else {
            $data['spare_parts'] = array();
        }
        $gst_where = array(
            "entity_type" => _247AROUND_PARTNER_STRING,
            "entity_id" => _247AROUND,
        );
        $data['from_gst_number'] = $this->inventory_model->get_entity_gst_data("entity_gst_details.id as id, gst_number, state_code.state as state", $gst_where);
        
        if (empty($this->input->post('is_ajax'))) {
            $this->load->view('service_centers/header');
        } 
       
        $this->load->view('service_centers/warehouse_task_list_tab_send_to_partner', $data);
    }

    /**
     * @desc: This method is used to display list of Received Defective Parts by Partner id
     * @param Integer $offset
     */
    function warehouse_send_to_partner_on_challan($offset = 0) {
        if(!empty($this->session->userdata('warehouse_id'))) {
            $this->checkEmployeeUserSession();
            $sf_id = $this->session->userdata('warehouse_id');
        } else {
            $this->check_WH_UserSession();
            $sf_id = $this->session->userdata('service_center_id');
        }
        $data['sf_id'] = $sf_id;
        log_message('info', __FUNCTION__ . " SF ID: " . $sf_id);

        if ($this->input->post('partner_id')) {
            $partner_id = $this->input->post('partner_id');
            $data['filtered_partner'] = $this->input->post('partner_id');
            $where = "spare_parts_details.defective_return_to_entity_id = '" . $sf_id . "' AND spare_parts_details.defective_return_to_entity_type = '" . _247AROUND_SF_STRING . "'"
                    . "AND spare_parts_details.wh_to_partner_defective_shipped_date IS NULL AND defective_part_required = '1' AND status IN ('" . DEFECTIVE_PARTS_RECEIVED_BY_WAREHOUSE . "','" . _247AROUND_COMPLETED . "','".Ok_PARTS_RECEIVED_BY_WAREHOUSE."') AND spare_parts_details.consumed_part_status_id <> 2";
            $where .= " AND spare_parts_details.wh_challan_file is not null AND spare_parts_details.wh_challan_number is not null AND spare_parts_details.entity_type = '" . _247AROUND_PARTNER_STRING . "' AND booking_details.partner_id = " . $partner_id;
            $data['spare_parts'] = $this->partner_model->get_spare_parts_booking_list($where, $offset, '', true, 0, null, false, " ORDER BY status = spare_parts_details.booking_id ");
        } else {
            $data['spare_parts'] = array();
        }
        $data['courier_details'] = $this->inventory_model->get_courier_services('*');
        $gst_where = array(
            "entity_type" => _247AROUND_PARTNER_STRING,
            "entity_id" => _247AROUND,
        );
        $data['from_gst_number'] = $this->inventory_model->get_entity_gst_data("entity_gst_details.id as id, gst_number, state_code.state as state", $gst_where);
        $data['is_send_to_partner'] = true;
        $data['is_generate_challan'] = false;
        
        $this->load->view('service_centers/send_to_partner_on_challan', $data);
    }

    /**
     * @desc: This method is used to display list of Received Defective Parts by Partner id
     * @param Integer $offset
     */
    function generate_defective_ok_part_challan($offset = 0) {
        if(!empty($this->session->userdata('warehouse_id'))) {
            $this->checkEmployeeUserSession();
            $sf_id = $this->session->userdata('warehouse_id');
        } else {
            $this->check_WH_UserSession();
            $sf_id = $this->session->userdata('service_center_id');
        }
        $data['sf_id'] = $sf_id;
        log_message('info', __FUNCTION__ . " SF ID: " . $sf_id);

        if ($this->input->post('partner_id')) {
            $partner_id = $this->input->post('partner_id');
            $data['filtered_partner'] = $this->input->post('partner_id');
            $where = "spare_parts_details.defective_return_to_entity_id = '" . $sf_id . "' AND spare_parts_details.defective_return_to_entity_type = '" . _247AROUND_SF_STRING . "'"
                    . "AND spare_parts_details.wh_to_partner_defective_shipped_date IS NULL AND defective_part_required = '1' AND status IN ('" . DEFECTIVE_PARTS_RECEIVED_BY_WAREHOUSE . "','" . _247AROUND_COMPLETED . "','".Ok_PARTS_RECEIVED_BY_WAREHOUSE."') AND spare_parts_details.consumed_part_status_id <> 2";
            $where .= " AND spare_parts_details.wh_challan_file is null AND spare_parts_details.wh_challan_number is null AND spare_parts_details.entity_type = '" . _247AROUND_PARTNER_STRING . "' AND booking_details.partner_id = " . $partner_id;
            $data['spare_parts'] = $this->partner_model->get_spare_parts_booking_list($where, $offset, '', true, 0, null, false, " ORDER BY status = spare_parts_details.booking_id ");
        } else {
            $data['spare_parts'] = array();
        }
        $data['courier_details'] = $this->inventory_model->get_courier_services('*');
        $gst_where = array(
            "entity_type" => _247AROUND_PARTNER_STRING,
            "entity_id" => _247AROUND,
        );
        $data['from_gst_number'] = $this->inventory_model->get_entity_gst_data("entity_gst_details.id as id, gst_number, state_code.state as state", $gst_where);
        $data['is_generate_challan'] = true;
        $data['is_send_to_partner'] = false;
        $this->load->view('service_centers/send_to_partner_on_challan', $data);
    }
    
    
    /**
     * @desc: This method is used to display list of Received Defective Parts by Partner id
     * @param Integer $offset
     */
    function warehouse_rejected_by_partner_on_challan($offset = 0) {
        
        if(!empty($this->session->userdata('warehouse_id'))) {
            $this->checkEmployeeUserSession();
            $sf_id = $this->session->userdata('warehouse_id');
        } else {
            $this->check_WH_UserSession();
            $sf_id = $this->session->userdata('service_center_id');
        }
        $data['sf_id'] = $sf_id;
        log_message('info', __FUNCTION__ . " SF ID: " . $sf_id);

        if ($this->input->post('partner_id')) {
            $partner_id = $this->input->post('partner_id');
            $data['filtered_partner'] = $this->input->post('partner_id');
           
            $where = "spare_parts_details.defective_return_to_entity_id = '" . $sf_id . "' AND spare_parts_details.defective_return_to_entity_type = '" . _247AROUND_SF_STRING . "'"
                    . "AND spare_parts_details.wh_to_partner_defective_shipped_date IS NOT NULL AND defective_part_required = '1' AND defective_part_rejected_by_partner = '1'  AND status IN ('" . DEFECTIVE_PARTS_REJECTED . "', '".OK_PARTS_REJECTED."') ";
            $where .= "  AND spare_parts_details.entity_type = '" . _247AROUND_PARTNER_STRING . "' AND booking_details.partner_id = " . $partner_id;
            $data['spare_parts'] = $this->partner_model->get_spare_parts_booking_list($where, $offset, '', true, 0, null, false, " ORDER BY status = spare_parts_details.booking_id ");
        } else {
            $data['spare_parts'] = array();
        }
        $data['courier_details'] = $this->inventory_model->get_courier_services('*');
        $gst_where = array(
            "entity_type" => _247AROUND_PARTNER_STRING,
            "entity_id" => _247AROUND,
        );
        $data['from_gst_number'] = $this->inventory_model->get_entity_gst_data("entity_gst_details.id as id, gst_number, state_code.state as state", $gst_where);

        $this->load->view('service_centers/reject_partner_on_challan', $data);
    }

    /**
     * @desc: This method is used to display list of rejected Defective Parts by Partner
     * @param Integer $offset
     */
    function warehouse_rejected_by_partner_on_invoice($offset = 0) {
        if(!empty($this->session->userdata('warehouse_id'))) {
            $this->checkEmployeeUserSession();
            $sf_id = $this->session->userdata('warehouse_id');
        } else {
            $this->check_WH_UserSession();
            $sf_id = $this->session->userdata('service_center_id');
        }
        $data['sf_id'] = $sf_id;
        log_message('info', __FUNCTION__ . " SF ID: " . $sf_id);

        if ($this->input->post('partner_id')) {
            $partner_id = $this->input->post('partner_id');
            $data['filtered_partner'] = $this->input->post('partner_id');
          
            $where = "spare_parts_details.defective_return_to_entity_id = '" . $sf_id . "' AND spare_parts_details.defective_return_to_entity_type = '" . _247AROUND_SF_STRING . "'"
                    . "AND spare_parts_details.wh_to_partner_defective_shipped_date IS NOT NULL AND defective_part_required = '1' AND defective_part_rejected_by_partner = '1' AND spare_parts_details.is_micro_wh IN (1,2) AND status IN ('" . DEFECTIVE_PARTS_REJECTED . "', '".OK_PARTS_REJECTED."') AND spare_parts_details.consumed_part_status_id IN (".PART_CONSUMED_STATUS_ID.")";
            $where .= "  AND booking_details.partner_id = " . $partner_id;
            $data['spare_parts'] = $this->partner_model->get_spare_parts_booking_list($where, $offset, '', true, 0, null, false, " ORDER BY status = spare_parts_details.booking_id ");
        } else {
            $data['spare_parts'] = array();
        }
        $data['courier_details'] = $this->inventory_model->get_courier_services('*');
        $gst_where = array(
            "entity_type" => _247AROUND_PARTNER_STRING,
            "entity_id" => _247AROUND,
        );
        $data['from_gst_number'] = $this->inventory_model->get_entity_gst_data("entity_gst_details.id as id, gst_number, state_code.state as state", $gst_where);

        $this->load->view('service_centers/reject_partner_on_invoice', $data);
    }
    
    /**
     * @desc: This function is used to download SF declaration who don't have GST number hen Partner update spare parts
     * @params: String $sf_id
     * @return: void
     */
    function download_sf_declaration($sf_id) {
        log_message("info", __METHOD__ . " SF Id " . $sf_id);
        $this->check_WH_InventoryMN_UserSession();
        ob_start();
        $pdf_details = $this->miscelleneous->generate_sf_declaration($sf_id);
        ob_end_clean(); 
        if ($pdf_details['status']) {
            if (!empty($pdf_details['file_name'])) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . basename(TMP_FOLDER . $pdf_details['file_name']) . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize(TMP_FOLDER . $pdf_details['file_name']));
                readfile(TMP_FOLDER . $pdf_details['file_name']);

                unlink(TMP_FOLDER . $pdf_details['file_name']);
            }
            log_message("info", __METHOD__ . " file details  " . print_r($pdf_details, true));
        } else {
            log_message("info", __METHOD__ . " file details  " . print_r($pdf_details, true));
            echo $pdf_details['message'];
        }
    }

    function get_defective_parts_count() {
        $this->checkUserSession();
        log_message('info', __FUNCTION__ . ' Used by :' . $this->session->userdata('service_center_name'));
        $service_center_id = $this->session->userdata('service_center_id');

        $select = "spare_parts_details.id";
        $where = array(
            "spare_parts_details.defective_part_required" => 1, // no need to check removed coloumn //
            "spare_parts_details.service_center_id" => $service_center_id,
            "status IN ('" . DEFECTIVE_PARTS_PENDING . "', '" . DEFECTIVE_PARTS_REJECTED_BY_WAREHOUSE . "', '" . OK_PART_TO_BE_SHIPPED . "', '" . OK_PARTS_REJECTED_BY_WAREHOUSE . "')  " => NULL
        );
        
        $group_by = "spare_parts_details.id";
        $order_by = "status = '" . DEFECTIVE_PARTS_REJECTED_BY_WAREHOUSE . "', spare_parts_details.booking_id ASC";        
        $total_rows = $this->service_centers_model->get_spare_parts_booking($where, $select, $group_by, $order_by);
        if (!empty($total_rows)) {
            echo json_encode(array("count" => count($total_rows)), true);
        } else {
            echo json_encode(array("count" => 0), true);
        }
    }

    /**
     * @desc: This function is used to validate uploaded spare invoice file
     * @params: void
     * @return: boolean
     */
    function validate_invoice_image_upload_file() {
        if (!empty($_FILES['invoice_image']['tmp_name'])) {
            $allowedExts = array("png", "jpg", "jpeg", "JPG", "JPEG", "PNG", "PDF", "pdf");
            $booking_id = $this->input->post("booking_id");
            $defective_courier_receipt = $this->miscelleneous->upload_file_to_s3($_FILES["invoice_image"], "invoice_pic", $allowedExts, $booking_id, "purchase-invoices", "invoice_pic");
            if ($defective_courier_receipt) {

                return true;
            } else {
                $this->form_validation->set_message('validate_invoice_image_upload_file', 'Invoice Image, File size or file type is not supported. Allowed extentions are "png", "jpg", "jpeg" and "pdf". '
                        . 'Maximum file size is 5 MB.');
                return false;
            }
        } else {
            return TRUE;
        }
    }


        /**
     * @desc: This function is used to validate uploaded defect pic 
     * @params: void
     * @Author : Abhishek Awasthi
     * @return: boolean
     */
    function validate_defect_pic_upload_file() {
        if (!empty($_FILES['defect_pic']['tmp_name'])) {
            $allowedExts = array("png", "jpg", "jpeg", "JPG", "JPEG", "PNG");
            $booking_id = $this->input->post("booking_id");
            $defect_pic = $this->miscelleneous->upload_file_to_s3($_FILES["defect_pic"], "defect_pic", $allowedExts, $booking_id, "misc-images", "defect_pic");
            if ($defect_pic) {

                return true;
            } else {
                $this->form_validation->set_message('validate_defect_pic_upload_file', 'Defect Pic, File size or file type is not supported. Allowed extentions are "png", "jpg", "jpeg"'
                        . 'Maximum file size is 5 MB.');
                return false;
            }
        } else {
            $this->form_validation->set_message('validate_defect_pic_upload_file', 'Please Upload Defect Pic');
            return TRUE;
        }
    }




    /**
     * @desc: This function is used to validate uploaded serial number pic 
     * @params: void
     * @return: boolean
     */
    function validate_serial_number_pic_upload_file() {
        $serial_number_pic_exist = $this->input->post('serial_number_pic_exist');
        if (!empty($_FILES['serial_number_pic']['tmp_name'])) {
            $allowedExts = array("png", "jpg", "jpeg", "JPG", "JPEG", "PNG", "PDF", "pdf");
            $booking_id = $this->input->post("booking_id");
            $defective_courier_receipt = $this->miscelleneous->upload_file_to_s3($_FILES["serial_number_pic"], "serial_number_pic", $allowedExts, $booking_id, SERIAL_NUMBER_PIC_DIR, "serial_number_pic");
            if ($defective_courier_receipt) {

                return true;
            } else {
                $this->form_validation->set_message('validate_serial_number_pic_upload_file', 'Serial Number, File size or file type is not supported. Allowed extentions are "png", "jpg", "jpeg" and "pdf". '
                        . 'Maximum file size is 5 MB.');
                return false;
            }
        } else if (!empty($serial_number_pic_exist)) {
            $_POST['serial_number_pic'] = $serial_number_pic_exist;
            return true;
        } else {
            $this->form_validation->set_message('validate_serial_number_pic_upload_file', 'Please Upload Serial Number Image');
            return FALSE;
        }
    }

    function validate_part_data() {
        $allowedExts = array("png", "jpg", "jpeg", "JPG", "JPEG", "PNG", "PDF", "pdf");
        $booking_id = $this->input->post("booking_id");
        $defective_parts = array();
        $defective_back_parts_pic = array();
        $defect_pic = array();
        if (!empty($_FILES['defective_parts_pic'])) {
            foreach ($_FILES['defective_parts_pic']['name'] as $key1 => $val) {
                $a = array();
                $a['name'] = $_FILES['defective_parts_pic']['name'][$key1];
                $a['type'] = $_FILES['defective_parts_pic']['type'][$key1];
                $a['tmp_name'] = $_FILES['defective_parts_pic']['tmp_name'][$key1];
                $a['error'] = $_FILES['defective_parts_pic']['error'][$key1];
                $a['size'] = $_FILES['defective_parts_pic']['size'][$key1];
                $defective_parts[$key1] = $a;
                //array_push($defective_parts, $a);
            }
        }

        if (!empty($_FILES['defective_back_parts_pic'])) {
            foreach ($_FILES['defective_back_parts_pic']['name'] as $key => $val) {
                $a = array();
                $a['name'] = $_FILES['defective_back_parts_pic']['name'][$key];
                $a['type'] = $_FILES['defective_back_parts_pic']['type'][$key];
                $a['tmp_name'] = $_FILES['defective_back_parts_pic']['tmp_name'][$key];
                $a['error'] = $_FILES['defective_back_parts_pic']['error'][$key];
                $a['size'] = $_FILES['defective_back_parts_pic']['size'][$key];
                $defective_back_parts_pic[$key] = $a;
                //array_push($defective_back_parts_pic, $a);
            }
        }

/*  Defect Pic upload */
        if (!empty($_FILES['defect_pic'])) {
            foreach ($_FILES['defect_pic']['name'] as $key => $val) {
                $a = array();
                $a['name'] = $_FILES['defect_pic']['name'][$key];
                $a['type'] = $_FILES['defect_pic']['type'][$key];
                $a['tmp_name'] = $_FILES['defect_pic']['tmp_name'][$key];
                $a['error'] = $_FILES['defect_pic']['error'][$key];
                $a['size'] = $_FILES['defect_pic']['size'][$key];
                $defect_pic[$key] = $a;
            }
        }


        $message['code'] = true;
        if (!empty($defective_parts)) {
            foreach ($defective_parts as $key => $value) {
                $d = $this->miscelleneous->upload_file_to_s3($value, "defective_parts", $allowedExts, $booking_id, "misc-images", "defective_parts");
                if (!empty($d)) {
                    $_POST['part'][$key]['defective_parts'] = $d;
                } else {
                    $message['code'] = false;
                    $message['message'] = "Defective Front Parts Image is not supported. Allow maximum file size is 2 MB. It supported only PNG/JPG";
                    break;
                }
            }
        } else {
            $message['code'] = false;
            $message['message'] = "Please upload Defective Front Parts Image";
        }

        if (!empty($defective_back_parts_pic)) {
            foreach ($defective_back_parts_pic as $key => $value) {
                $d = $this->miscelleneous->upload_file_to_s3($value, "defective_back_parts_pic", $allowedExts, $booking_id, "misc-images", "defective_back_parts_pic");
                if (!empty($d)) {
                    $_POST['part'][$key]['defective_back_parts_pic'] = $d;
                } else {
                    $message['code'] = false;
                    $message['message'] = "Defective Back Parts Image is not supported. Allow maximum file size is 2 MB. It supported only PNG/JPG";
                    break;
                }
            }
        } else {
            $message['code'] = false;
            $message['message'] = "Please upload Defective Back Parts Image";
        }


        if (!empty($defect_pic)) {
            foreach ($defect_pic as $key => $value) {
                $d = $this->miscelleneous->upload_file_to_s3($value, "defect_pic", $allowedExts, $booking_id, "misc-images", "defect_pic");
                if (!empty($d)) {
                    $_POST['part'][$key]['defect_pic'] = $d;
                } else {
                   // $message['code'] = false;
                   // $message['message'] = "Defect Image is not supported. Allow maximum file size is 2 MB. It supported only PNG/JPG";
                  //  break;
                }
            }
        }  
        return $message;
    }

    /**
     * @desc: This function is used to validate uploaded defective parts pic 
     * @params: void
     * @return: boolean
     */
    function validate_defective_parts_pic() {
        if (!empty($_FILES['defective_parts_pic']['tmp_name'])) {

            $allowedExts = array("png", "jpg", "jpeg", "JPG", "JPEG", "PNG", "PDF", "pdf");
            $booking_id = $this->input->post("booking_id");
            $defective_courier_receipt = $this->miscelleneous->upload_file_to_s3($_FILES["defective_parts_pic"], "defective_parts", $allowedExts, $booking_id, "misc-images", "defective_parts");
            if ($defective_courier_receipt) {

                return true;
            } else {
                $this->form_validation->set_message('validate_defective_parts_pic', 'Defective Front Parts, File size or file type is not supported. Allowed extentions are "png", "jpg", "jpeg" and "pdf". '
                        . 'Maximum file size is 5 MB.');
                return false;
            }
        } else {
            $this->form_validation->set_message('validate_defective_parts_pic', 'Please Upload Defective Front Parts Image');
            return FALSE;
        }
    }

    /**
     * @desc This function is used to validate and upload defective back part image.
     * @return boolean
     */
    function validate_defective_parts_back_pic() {
        if (!empty($_FILES['defective_back_parts_pic']['tmp_name'])) {

            $allowedExts = array("png", "jpg", "jpeg", "JPG", "JPEG", "PNG", "PDF", "pdf");
            $booking_id = $this->input->post("booking_id");
            $defective_courier_receipt = $this->miscelleneous->upload_file_to_s3($_FILES["defective_back_parts_pic"], "defective_back_parts_pic", $allowedExts, $booking_id, "misc-images", "defective_back_parts_pic");
            if ($defective_courier_receipt) {

                return true;
            } else {
                $this->form_validation->set_message('validate_defective_parts_back_pic', 'Defective Back Parts, File size or file type is not supported. Allowed extentions are "png", "jpg", "jpeg" and "pdf". '
                        . 'Maximum file size is 5 MB.');
                return false;
            }
        } else {
            $this->form_validation->set_message('validate_defective_parts_back_pic', 'Please Upload Defective Back Parts Image');
            return FALSE;
        }
    }

    function acknowledge_spares_send_by_partner() {
        $this->check_WH_UserSession();
        $this->load->view('service_centers/header');
        $this->load->view('service_centers/acknowledge_spares_send_by_partner');
    }

    function holiday_list() {
        $data['data'] = $this->employee_model->get_holiday_list();
        $this->load->view('service_centers/header');
        $this->load->view('employee/show_holiday_list', $data);
    }

    /**
     * @desc: This function is used to upload the courier file when warehouse shipped spare parts to sf
     * @params: void
     * @return: $res
     */
    function upload_courier_image_file() {

        $MB = 1048576;
        //check if upload file is empty or not
        if (!empty($_FILES['courier_image']['name'])) {
            //check upload file size. it should not be greater than 2mb in size
            if ($_FILES['courier_image']['size'] <= 2 * $MB) {
                $allowed = array('pdf', 'jpg', 'png', 'jpeg', 'JPG', 'JPEG', 'PNG', 'PDF');
                $ext = pathinfo($_FILES['courier_image']['name'], PATHINFO_EXTENSION);
                //check upload file type. it should be pdf.
                if (in_array($ext, $allowed)) {
                    $file_name = $wh_courier_image = "wh_courier_image_" . $this->input->post('booking_id') . "_" . rand(10, 100) . "." . $ext;
                    //Upload files to AWS
                    $directory_xls = "vendor-partner-docs/" . $file_name;
                    $this->s3->putObjectFile($_FILES['courier_image']['tmp_name'], BITBUCKET_DIRECTORY, $directory_xls, S3::ACL_PUBLIC_READ);

                    $res['status'] = true;
                    $res['message'] = $file_name;
                } else {
                    $res['status'] = false;
                    $res['message'] = 'Upload file type not valid. Only PDF/JPG/PNG/JPEG format allow';
                }
            } else {
                $res['status'] = false;
                $res['message'] = 'Uploaded file size can not be greater than 2 mb';
            }
        } else {
            $res['status'] = false;
            $res['message'] = 'Couries Image is required';
        }

        return $res;
    }

    function get_booking_contacts($bookingID) {
        $data = $this->miscelleneous->get_booking_contacts($bookingID);
        if (empty($data)) {
            $state_check = 0;
            $data = $this->miscelleneous->get_booking_contacts($bookingID, $state_check);
        }
        echo json_encode($data);
    }

    function process_booking_internal_conversation_email() {
        log_message('info', __FUNCTION__ . " Booking ID: " . $this->input->post('booking_id'));
        if ($this->session->userdata('service_center_id')) {
            if ($this->input->post('booking_id')) {
                $to = explode(",", $this->input->post('to'));
                $row_id = $this->miscelleneous->send_and_save_booking_internal_conversation_email("Vendor", $this->input->post('booking_id'), implode(",", $to), $this->input->post('cc'), $this->input->post('cc'), $this->input->post('subject'), $this->input->post('msg'), $this->session->userdata('service_center_agent_id'), $this->session->userdata('service_center_id'));
                if ($row_id) {
                    echo "Successfully Sent";
                } else {
                    echo "Please Try Again";
                }
            } else {
                echo "Please Try Again";
            }
        }
    }

    /**
     * @desc: This function is used to update spare courier details form
     * @params: $id
     * @return: view
     * 
     */
    function update_spare_courier_details($id) {
        if (!empty($id)) {
            $this->miscelleneous->load_nav_header();
            $select = "spare_parts_details.id, spare_parts_details.partner_id, spare_parts_details.service_center_id, spare_parts_details.entity_type, spare_parts_details.booking_id, spare_parts_details.defective_part_shipped, spare_parts_details.courier_name_by_sf, spare_parts_details.awb_by_sf, spare_parts_details.courier_charges_by_sf, spare_parts_details.defective_courier_receipt, spare_parts_details.defective_part_shipped_date, spare_parts_details.remarks_defective_part_by_sf, spare_parts_details.sf_challan_number, spare_parts_details.sf_challan_file,spare_parts_details.partner_challan_number,spare_parts_details.challan_approx_value";
            $where = array('spare_parts_details.id' => $id);
            $data['data'] = $this->partner_model->get_spare_parts_by_any($select, $where);
            $data['courier_details'] = $this->inventory_model->get_courier_services('*');
            $this->load->view('employee/update_spare_courier_details', $data);
        } else {
            $this->miscelleneous->load_nav_header();
            echo 'Invalid Request';
        }
    }

    /**
     * @desc: This function is used to update spare parts courier details along with generating sf challan file
     * @params: $id
     * @return: prints message whether data already exists or updated
     * 
     */
    function process_update_spare_courier_details($id) {
        log_message('info', __METHOD__ . ' update spare courier details of spare id ' . $id);
        //$this->form_validation->set_rules('shipped_parts', 'shipped_parts', 'trim|required');
        $this->form_validation->set_rules('courier_name', 'courier_name', 'trim|required');
        $this->form_validation->set_rules('awb', 'awb', 'required');
        $this->form_validation->set_rules('courier_charge', 'courier_charge', 'trim|required');
        //$this->form_validation->set_rules('shipped_date', 'shipped_date', 'required');
        $this->form_validation->set_rules('remarks_by_sf', 'remarks_by_sf', 'trim|required');
        if ($this->form_validation->run() == TRUE) {
            $booking_id = $this->input->post('booking_id');
            $agent_id = $this->session->userdata("id");
            $entity_id = _247AROUND;
            $entity_type = _247AROUND_EMPLOYEE_STRING;
            $data = array();
            $courier_name_by_sf = trim($this->input->post('courier_name'));
            $awb_number = trim($this->input->post('awb'));
            $pre_awb_by_sf = trim($this->input->post('pre_awb_by_sf'));
            $courier_charges = trim($this->input->post('courier_charge'));
            $remarks_defective_part_by_sf = trim($this->input->post('remarks_by_sf'));

            if (!empty($_FILES['defective_courier_receipt']['name'])) {
                $courier_image = $this->upload_defective_spare_pic();
                if (!empty($courier_image)) {
                    $data['defective_courier_receipt'] = $this->input->post('sp_parts');
                }
            }
            
            if ($pre_awb_by_sf != $awb_number && empty($this->input->post('sp_parts'))) {
                $this->session->set_userdata('failed', "Courier file should not empty or check your file size less than 5MB.");
                redirect(base_url() . 'employee/service_centers/update_spare_courier_details/' . $id);
            } else {

                $courier_company_detail = $this->inventory_model->get_courier_company_invoice_details('courier_company_invoice_details.id, courier_company_invoice_details.awb_number', array('awb_number' => $awb_number));

                if (empty($courier_company_detail)) {
                    $courier_company_data = array(
                        'awb_number' => trim($this->input->post('awb')),
                        'company_name' => strtolower(trim($this->input->post('courier_name'))),
                        'courier_charge' => trim($this->input->post('courier_charge')),
                    );

                    if (!empty($this->input->post('sp_parts'))) {
                        $courier_company_data['courier_invoice_file'] = $this->input->post('sp_parts');
                    }

                    $courier_company_detail[0]['id'] = $this->inventory_model->insert_courier_company_invoice_details($courier_company_data);
                    $updateCharge = TRUE;
                } else {

                    if (!empty($courier_company_detail)) {
                        $courier_company_data_update = array(
                            'company_name' => strtolower(trim($this->input->post('courier_name'))),
                            'courier_charge' => trim($this->input->post('courier_charge'))
                        );

                        if (!empty($this->input->post('sp_parts'))) {
                            $courier_company_data_update['courier_invoice_file'] = $this->input->post('sp_parts');
                        }
                        
                        $this->inventory_model->update_courier_company_invoice_details(array('id' => $courier_company_detail[0]['id']), $courier_company_data_update);
                        $updateCharge = TRUE;
                    }
                }

                if ($updateCharge === TRUE) {

                    $this->inventory_model->update_spare_courier_details($id, array('spare_parts_details.awb_by_sf' => $awb_number));

                    $data_spare_part_detail = $this->partner_model->get_spare_parts_by_any('spare_parts_details.id, spare_parts_details.awb_by_sf, spare_parts_details.courier_name_by_sf', array('spare_parts_details.awb_by_sf = "' . $awb_number . '"  AND status != "' . _247AROUND_CANCELLED . '"' => null), false);

                    if (!empty($data_spare_part_detail)) {
                        $spare_part_reverse_data = $this->partner_model->get_spare_parts_by_any('spare_parts_details.id, spare_parts_details.awb_by_sf, spare_parts_details.courier_name_by_sf', array('spare_parts_details.awb_by_sf = "' . $pre_awb_by_sf . '"  AND status != "' . _247AROUND_CANCELLED . '"' => null), false);
                        if (!empty($spare_part_reverse_data)) {
                            $couir_invoice_details = $this->inventory_model->get_courier_company_invoice_details('courier_company_invoice_details.id, courier_company_invoice_details.awb_number,courier_company_invoice_details.company_name, courier_company_invoice_details.courier_charge', array('awb_number' => $pre_awb_by_sf));
                            if (!empty($couir_invoice_details)) {
                                $courier_charge = ($couir_invoice_details[0]['courier_charge'] / count($spare_part_reverse_data));
                                $this->service_centers_model->update_spare_parts(array('spare_parts_details.awb_by_sf' => $pre_awb_by_sf), array("spare_parts_details.courier_charges_by_sf" => $courier_charge));
                            }
                        }

                        $courier_amount = ($courier_charges / count($data_spare_part_detail));
                        $data['spare_parts_details.awb_by_sf'] = $awb_number;
                        $data['spare_parts_details.courier_name_by_sf'] = $courier_name_by_sf;
                        $data['spare_parts_details.courier_charges_by_sf'] = $courier_amount;

                        foreach ($data_spare_part_detail as $value) {

                            $this->inventory_model->update_spare_courier_details($value['id'], $data);
                            /* Insert in Spare Tracking Details */
                            if ($pre_awb_by_sf != $awb_number) {
                                $remarks = " Docket replaced from " . $pre_awb_by_sf . " To " . $awb_number . "," . $remarks_defective_part_by_sf;
                                $new_state = "New Awb Number updated";
                                $action = 'New Awb Number ' . $awb_number;
                            } else {
                                $remarks = $remarks_defective_part_by_sf;
                                $new_state = "Courier Charges Or Courier name Updated";
                                $action = 'Courier Charges Or Courier name Of Awb Number ' . $awb_number;
                            }
                            $tracking_details = array('spare_id' => $value['id'], 'action' => $action, 'remarks' => $remarks, 'agent_id' => $agent_id, 'entity_id' => $entity_id, 'entity_type' => $entity_type);
                            $this->service_centers_model->insert_spare_tracking_details($tracking_details);
                        }
                    }
                }

                redirect(base_url() . 'employee/booking/viewdetails/' . $booking_id);
            }
        } else {
            log_message('info', __METHOD__ . ' validation failed');
            $this->update_spare_courier_details($id);
        }
    }

    /**
     * @desc: This function is used to remove uploaded image
     * @params: void
     * @return: prints message if removed successfully
     * 
     */
    function remove_uploaded_image() {
        $courier[$this->input->post('type')] = '';
        //Making Database Entry as Empty for selected file
        $status = $this->inventory_model->update_spare_courier_details($this->input->post('id'), $courier);

        //Logging 
        if ($status == true) {
            log_message('info', __FUNCTION__ . $this->input->post('type') . '  File has been removed sucessfully for id ' . $this->input->post('id'));
            echo TRUE;
        }
    }

    /**
     * @desc: This Function is used to search the docket number
     * @param: void
     * @return : void
     */
    function search_docket_number() {
        $this->checkUserSession();
        $this->load->view('service_centers/header');
        $this->load->view('service_centers/search_docket_number');
    }

    function sf_dashboard() {
        $this->checkUserSession();
        $rating_data = $this->service_centers_model->get_vendor_rating_data($this->session->userdata('service_center_id'));
        if (!empty($rating_data[0]['rating'])) {
            $data['rating'] = $rating_data[0]['rating'];
            $data['count'] = $rating_data[0]['count'];
        } else {
            $data['rating'] = 0;
            $data['count'] = $rating_data[0]['count'];
        }
        $join['services'] = "services.id = vendor_pincode_mapping.Appliance_ID";
        $data['services'] = $this->reusable_model->get_search_result_data("vendor_pincode_mapping", "DISTINCT vendor_pincode_mapping.Appliance_ID as id,services.services", array("Vendor_ID" => $this->session->userdata('service_center_id')), $join, NULL, array("services.services" => "ASC"), NULL, NULL, array());
        $data['msl'] = $this->miscelleneous->get_msl_amounts($this->session->userdata('service_center_id'));
        $data['brands'] = $this->vendor_model->get_mapped_brands($this->session->userdata('service_center_id'), 1);
        $this->load->view('service_centers/header');
        $this->load->view('service_centers/dashboard', $data);
    }

    /**
     * msl summary details -> page to show MSL Security deposits of SF till now
     */
    function msl_security_details() {
        $this->checkUserSession();
        $data = array();
        $select = "invoice_id, type, date_format(invoice_date,'%d-%b-%Y') as 'invoice_date',invoice_file_main, parts_count, vertical, category, sub_category,total_amount_collected,(total_amount_collected-amount_paid) as 'amount'";
        $data['msl_security'] = $this->reusable_model->get_search_result_data(
                'vendor_partner_invoices', $select, array(
            "vendor_partner" => "vendor",
            "vendor_partner_id" => $this->session->userdata('service_center_id')
                ), NULL, NULL, NULL, array(
            "sub_category" => array(
                MSL_SECURITY_AMOUNT
            )
                ), NULL, array()
        );
        $this->load->view('service_centers/header');
        $this->load->view('service_centers/msl_summary', $data);
    }

    /**
     * [[Description-> page to show MSL sent to SF and parts returned by SF]]
     */
    function msl_spare_details() {
        $this->checkUserSession();
        $data = array();
        $data['msl_spare'] = true;
        $this->load->view('service_centers/header');
        $this->load->view('service_centers/msl_summary', $data);
    }

    /**
     * @method function to get MSL Spare details via ajax
     * @return json response
     */
    function ajax_get_msl_spare_details() {
        $res = array();
        $draw = $this->input->post('draw');
        $start = $this->input->post('start');
        $limit = $this->input->post('length');

        //if session is empty return blank data
        if (empty($this->session->userdata('service_center_id'))) {
            $output = array(
                "draw" => $draw,
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => array(),
            );
            echo json_encode($output);
            die;
        }

        $spareCountData = $this->service_centers_model->get_msl_spare_details($this->session->userdata('service_center_id'), true);
        //echo $this->db->last_query();die();
        if (!empty($spareCountData['error'])) {
            $output = array(
                "draw" => $draw,
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => array(),
            );
            echo json_encode($output);
            die;
        }
        $data = array();
        $spareData = $this->service_centers_model->get_msl_spare_details($this->session->userdata('service_center_id'), false, $start, $limit);
        foreach ($spareData['payload'] as $key => $spare) {
            $data[$key] = array();
            $amount = 0;
            if ($spare['sub_category'] == MSL || $spare['sub_category'] == MSL_Debit_Note || $spare['sub_category'] == IN_WARRANTY ) {
                if ($spare['amount'] == 0) {
                    $amount = $spare['amount'];
                } else {
                    $amount = -1 * $spare['amount'];
                }
            } else {
                $amount = $spare['amount'];
            }
            $data[$key][] = $start + $key + 1;
            $data[$key][] = $spare['category'];
            $data[$key][] = $spare['sub_category'];
            $data[$key][] = $spare['parts_count'];
            $data[$key][] = '<a title="click to get more details" data-toggle="tooltip" href="'.S3_WEBSITE_URL.'invoices-excel/'.$spare['invoice_file_main'].'">' . $spare['invoice_id'] . '</a>';
            $data[$key][] = $spare['total_amount_collected'];
            $data[$key][] = $amount;
            $data[$key][] = $spare['invoice_date'];
        }

        $output = array(
            "draw" => $draw,
            "recordsTotal" => $spareCountData['payload']['count'],
            "recordsFiltered" => $spareCountData['payload']['count'],
            "data" => $data
        );
        echo json_encode($output);
        die;
    }

    function ajax_get_msl_parts_consumed_in_oow() {
        $res = array();
        $draw = $this->input->post('draw');
        $start = $this->input->post('start');
        $limit = $this->input->post('length');

        //if session is empty return blank data
        if (empty($this->session->userdata('service_center_id'))) {
            $output = array(
                "draw" => $draw,
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => array(),
            );
            echo json_encode($output);
            die;
        }

        $spareCountData = $this->service_centers_model->get_oow_parts_used_from_micro($this->session->userdata('service_center_id'), true);
        //echo $this->db->last_query();die();
        if (!empty($spareCountData['error'])) {
            $output = array(
                "draw" => $draw,
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => array(),
            );
            echo json_encode($output);
            die;
        }
        $data = array();
        $spareData = $this->service_centers_model->get_oow_parts_used_from_micro($this->session->userdata('service_center_id'), false, $start, $limit);
        foreach ($spareData['payload'] as $key => $spare) {
            $data[$key] = array();
            $data[$key][] = $start + $key + 1;
            $data[$key][] = $spare['booking_id'];
            $data[$key][] = $spare['parts_requested_type'];
            $data[$key][] = $spare['parts_requested'];
            $data[$key][] = $spare['model_number'];
            $data[$key][] = $spare['date_of_request'];
            $data[$key][] = $spare['sell_price'];
            $data[$key][] = $spare['quantity'];
        }

        $output = array(
            "draw" => $draw,
            "recordsTotal" => $spareCountData['payload']['count'],
            "recordsFiltered" => $spareCountData['payload']['count'],
            "data" => $data
        );
        echo json_encode($output);
        die;
    }

    /**
     * @desc : This is used when CWH ship part first time.
     */
    function check_warehouse_shipped_awb_exist() {
        $awb = $this->input->post('awb');

        if (!empty($awb)) {
            $data = $this->partner_model->get_spare_parts_by_any("awb_by_partner, courier_price_by_partner, "
                    . "courier_name_by_partner, courier_pic_by_partner, shipped_date", array('awb_by_partner' => $awb, 'status !="' . _247AROUND_CANCELLED . '" ' => NULL));

            $courier_boxes_weight_details = $this->inventory_model->get_courier_company_invoice_details('*', array('awb_number' => $awb));

            if (!empty($data)) {
                /**
                 * Check when shipment done.
                 * @modifiedBy Ankit Rajvanshi
                 */
                $part_shipped_date = date_diff(date_create(date('Y-m-d')), date_create($data[0]['shipped_date']));
                $part_shipped_days = (int) $part_shipped_date->format("%a");

                
                /**
                 * check shippment date in courier_company_invoice_details.
                 */
                $courier_shipped_days = 0;
                if(!empty($courier_boxes_weight_details)) {
                    $courier_shipped_date = date_diff(date_create(date('Y-m-d')), date_create($courier_boxes_weight_details[0]['shippment_date']));
                    $courier_shipped_days = (int) $courier_shipped_date->format("%a");
                }
                
                if($part_shipped_days <= UPDATE_AWB_NUMBER_DAYS && $courier_shipped_days <= UPDATE_AWB_NUMBER_DAYS) {
                    $data[0]['partcount'] = count($data);
                    if (!empty($courier_boxes_weight_details)) {
                        $data[0]['spare_part_shipped_date'] = $courier_boxes_weight_details[0]['shippment_date'];
                        $data[0]['billable_weight'] = $courier_boxes_weight_details[0]['billable_weight'];
                        $data[0]['box_count'] = $courier_boxes_weight_details[0]['box_count'];
                        $data[0]['courier_charge'] = $courier_boxes_weight_details[0]['courier_charge'];  //defective_courier_receipt
                        $data[0]['courier_invoice_file'] = $courier_boxes_weight_details[0]['courier_invoice_file'];
                    } else {

                        $data[0]['billable_weight'] = '0.00';
                        $data[0]['box_count'] = 0;
                        $data[0]['courier_charge'] = (array_sum(array_column($data, 'courier_price_by_partner')));  //defective_courier_receipt
                    }
                    echo json_encode(array('code' => 247, "message" => $data));
                } else {
                    // if shipment done more than 7 days ago.
                    echo json_encode(array('code' => 777, "message" => $data));
                }
            } else {
                echo json_encode(array('code' => -247));
            }
        }
    }

    /**
     * @desc : This is used when CWH ship defective part to partner.
     */
    function check_warehouse_shipped_defective_awb_exist() {
        $awb = $this->input->post('awb');

        if (!empty($awb)) {
            $data = $this->partner_model->get_spare_parts_by_any("awb_by_wh, courier_price_by_wh, "
                    . "courier_name_by_wh, defective_parts_shippped_courier_pic_by_wh, date(defective_parts_shippped_date_by_wh) as defective_parts_shippped_date_by_wh", array('awb_by_wh' => $awb, 'status !="' . _247AROUND_CANCELLED . '" ' => NULL));

            $courier_boxes_weight_details = $this->inventory_model->get_courier_company_invoice_details('*', array('awb_number' => $awb));

            if (!empty($data)) {
                /**
                 * Check when shipment done.
                 * @modifiedBy Ankit Rajvanshi
                 */
                $part_shipped_date = date_diff(date_create(date('Y-m-d')), date_create($data[0]['defective_parts_shippped_date_by_wh']));
                $part_shipped_days = (int) $part_shipped_date->format("%a");

                /**
                 * check shippment date in courier_company_invoice_details.
                 */
                $courier_shipped_days = 0;
                if(!empty($courier_boxes_weight_details)) {
                    $courier_shipped_date = date_diff(date_create(date('Y-m-d')), date_create($courier_boxes_weight_details[0]['shippment_date']));
                    $courier_shipped_days = (int) $courier_shipped_date->format("%a");
                }
                if($part_shipped_days <= UPDATE_AWB_NUMBER_DAYS && $courier_shipped_days <= UPDATE_AWB_NUMBER_DAYS) {
                    $data[0]['partcount'] = count($data);
                    if (!empty($courier_boxes_weight_details)) {
                        $data[0]['spare_part_shipped_date'] = $courier_boxes_weight_details[0]['shippment_date'];
                        $data[0]['billable_weight'] = $courier_boxes_weight_details[0]['billable_weight'];
                        $data[0]['box_count'] = $courier_boxes_weight_details[0]['box_count'];
                        $data[0]['courier_charge'] = $courier_boxes_weight_details[0]['courier_charge'];  //defective_courier_receipt
                        $data[0]['courier_invoice_file'] = $courier_boxes_weight_details[0]['courier_invoice_file'];
                    } else {

                        $data[0]['billable_weight'] = '0.00';
                        $data[0]['box_count'] = 0;
                        $data[0]['courier_charge'] = (array_sum(array_column($data, 'courier_price_by_wh')));  //defective_courier_receipt
                    }
                    echo json_encode(array('code' => 247, "message" => $data));
                } else {
                    // if shipment done more than 7 days ago.
                    echo json_encode(array('code' => 777, "message" => $data));
                }
            } else {
                echo json_encode(array('code' => -247));
            }
        }
    }
    
    
    /**
     * @desc This is used to check awb exist or not when Sf will be updating Awb( defective Parts)
     */
    function check_sf_shipped_defective_awb_exist() {
        $awb = $this->input->post('awb');
        if (!empty($awb)) {
            $data = $this->partner_model->get_spare_parts_by_any("awb_by_sf, courier_charges_by_sf, "
                    . "courier_name_by_sf, defective_courier_receipt, defective_part_shipped_date", array('awb_by_sf' => $awb,  'status !="' . _247AROUND_CANCELLED . '" ' => NULL));

            $courier_boxes_weight_details = $this->inventory_model->get_courier_company_invoice_details('*', array('awb_number' => $awb, 'IFNULL(DATEDIFF(CURDATE(), shippment_date), 0) <= 7' => NULL));

            if (!empty($data)) {
                /**
                 * Check when shipment done.
                 * @modifiedBy Ankit Rajvanshi
                 */
                $defective_part_shipped_date = date_diff(date_create(date('Y-m-d')), date_create($data[0]['defective_part_shipped_date']));
                $defective_part_shipped_days = (int) $defective_part_shipped_date->format("%a");

                /**
                 * check shippment date in courier_company_invoice_details.
                 */
                $courier_shipped_days = 0;
                if(!empty($courier_boxes_weight_details)) {
                    $courier_shipped_date = date_diff(date_create(date('Y-m-d')), date_create($courier_boxes_weight_details[0]['shippment_date']));
                    $courier_shipped_days = (int) $courier_shipped_date->format("%a");
                }
                
                if($defective_part_shipped_days <= UPDATE_AWB_NUMBER_DAYS && $courier_shipped_days <= UPDATE_AWB_NUMBER_DAYS) {
                    $data[0]['partcount'] = count($data);
                    if (!empty($courier_boxes_weight_details)) {
                        $data[0]['defective_part_shipped_date'] = $courier_boxes_weight_details[0]['shippment_date'];
                        $data[0]['billable_weight'] = $courier_boxes_weight_details[0]['billable_weight'];
                        $data[0]['box_count'] = $courier_boxes_weight_details[0]['box_count'];
                        $data[0]['courier_charge'] = $courier_boxes_weight_details[0]['courier_charge'];  //defective_courier_receipt
                    } else {
                        $data[0]['billable_weight'] = '0.00';
                        $data[0]['box_count'] = 0;
                        $data[0]['courier_charge'] = (array_sum(array_column($data, 'courier_charges_by_sf')));  //defective_courier_receipt
                    }
                    echo json_encode(array('code' => 247, "message" => $data));
                } else {
                    // if shipment done more than 7 days ago.
                    echo json_encode(array('code' => 777, "message" => $data));
                }
            } else {
                echo json_encode(array('code' => -247));
            }
        }
    }

    function check_wh_shipped_defective_awb_exist() {
        log_message('info', __METHOD__ . " AWB NO " . $this->input->post('awb'));
        $awb = $this->input->post('awb');
        if (!empty($awb)) {
            $data = $this->inventory_model->get_courier_details("AWB_no, courier_name, "
                    . "courier_file, shipment_date, courier_charge", array('AWB_no' => $awb, 'sender_entity_id' =>
                $this->session->userdata('service_center_id'), "sender_entity_type" => _247AROUND_SF_STRING));
            if (!empty($data)) {
                echo json_encode(array('code' => 247, "message" => $data));
            } else {
                echo json_encode(array("code" => -247));
            }
        }
    }

    /**
     * @desc This is used to check unit line item exist in the service center action table.
     * If not then insert new line item in action table.
     * @param String $booking_id
     * @param Strng $unit_id
     */
    function check_unit_exist_action_table($booking_id, $unit_id) {
        log_message("info", __METHOD__ . " Booking ID " . $booking_id . " Unit ID " . $unit_id);
        if (strpos($unit_id, 'new') !== false) {
            $remove_string_new = explode('new', $unit_id);
            $unit_tmp_id = $remove_string_new[0];
            $service_charges_id = $remove_string_new[1];
            $data['booking_id'] = $booking_id;
            $data['booking_status'] = _247AROUND_PENDING;
            $data['customer_paid_parts'] = 0;
            $data['customer_paid_basic_charges'] = 0;
            $data['customer_paid_extra_charges'] = 0;
            $data['added_by_sf'] = 1;
            log_message('info', __FUNCTION__ . " New unit selected, previous unit " . print_r($unit_id, true)
                    . " Service charges id: "
                    . print_r($service_charges_id, true)
                    . " Data: " . print_r($data, true));
            $unit_id = $this->booking_model->insert_new_unit_item($unit_tmp_id, $service_charges_id, $data, "");
        }

        $data = $this->service_centers_model->get_service_center_action_details("*", array('unit_details_id' => $unit_id, "booking_id" => $booking_id));
        if (empty($data)) {
            log_message("info", __METHOD__ . " Unit is not exist for booking id " . $booking_id . " Unit ID " . $unit_id);
            $data1 = $this->service_centers_model->get_service_center_action_details("*", array("booking_id" => $booking_id));
            if (!empty($data1)) {
                $a = $data1[0];
                $a['id'] = NULL;
                $a['create_date'] = date("Y-m-d H:i:s");
                $a['unit_details_id'] = $unit_id;
                log_message("info", __METHOD__ . " data unit Insert " . print_r($a, true));
                $this->vendor_model->insert_service_center_action($a);
            }
        }



        return $unit_id;
    }

    /**
     * @desc This function is used to check same part already requested or not.
     * DO Not allow to sf to request part if same part already requested
     * @return Array
     */
    function is_part_already_requested() {
        $parts_requested = $this->input->post('part');
        $booking_id = $this->input->post('booking_id');
        $array = array();
        foreach ($parts_requested as $value) {
            if (isset($value['parts_type'])) {
                $data = $this->partner_model->get_spare_parts_by_any("spare_parts_details.parts_requested_type", array("booking_id" => $booking_id,
                    "status IN ('" . SPARE_PART_ON_APPROVAL . "','" . SPARE_PARTS_REQUESTED . "', '" . SPARE_OOW_EST_REQUESTED . "', '" . SPARE_OOW_EST_GIVEN . "') " => NULL,
                    "parts_requested_type" => $value['parts_type']));
                if (!empty($data)) {
                    $array = array("status" => false, "parts_requested_type" => $value['parts_type']);
                    break;
                }
            } else {
                $array = array("status" => false, "parts_requested_type" => '');
                break;
            }
        }
        return $array;
    }

    /**
     * @desc: This function is used to show the payment details page to Service Centers
     * @params: void
     * @return: void
     */
    function payment_details() {
        $this->checkUserSession();
        //$this->load->view('partner/header');
        $this->load->view('service_centers/header');
        $this->load->view('paytm_gateway/sf_payment_details');
    }

    /**
     * @desc This function is used to display the list of spare parts in current stock.
     * get details using partner id
     * @return Array 
     */
//    function requested_spare_on_sf(){
//        $this->load->view('service_centers/header');      
//        $this->load->view('service_centers/requested_spare_on_sf');
//    }

    /**
     * @desc: This function is used to get those booking who has request to ship spare parts to SF
     * @param: void
     * @return void
     */
//    function get_spare_requested_spare_on_sf($offset = 0){
//       
//        log_message('info', __FUNCTION__ . " sf Id: " . $this->session->userdata('service_center_id'));
//        $this->check_WH_UserSession();
//        $sf_id = $this->session->userdata('service_center_id');
//        $where = "spare_parts_details.partner_id = '" . $sf_id . "' AND  spare_parts_details.entity_type =  '"._247AROUND_SF_STRING."' AND status = '" . SPARE_PARTS_REQUESTED . "' "
//                . " AND booking_details.current_status IN ('"._247AROUND_PENDING."', '"._247AROUND_RESCHEDULED."') "
//                . " AND wh_ack_received_part != 0 ";
//        
//        $select = "spare_parts_details.booking_id, GROUP_CONCAT(DISTINCT spare_parts_details.parts_requested) as parts_requested, purchase_invoice_id, users.name, "
//                . "booking_details.booking_primary_contact_no, booking_details.partner_id as booking_partner_id, "
//                . "booking_details.booking_address,booking_details.initial_booking_date, booking_details.is_upcountry, "
//                . "booking_details.upcountry_paid_by_customer,booking_details.amount_due,booking_details.state, service_centres.name as vendor_name, "
//                . "service_centres.address, service_centres.state, service_centres.gst_no, service_centres.pincode, "
//                . "service_centres.district,service_centres.id as sf_id,service_centres.is_gst_doc,service_centres.signature_file, "
//                . " GROUP_CONCAT(DISTINCT inventory_stocks.stock) as stock, DATEDIFF(CURRENT_TIMESTAMP,  STR_TO_DATE(date_of_request, '%Y-%m-%d')) AS age_of_request,"
//                . " GROUP_CONCAT(DISTINCT spare_parts_details.model_number) as model_number, "
//                . " GROUP_CONCAT(DISTINCT spare_parts_details.serial_number) as serial_number,"
//                . " GROUP_CONCAT(DISTINCT spare_parts_details.remarks_by_sc) as remarks_by_sc, spare_parts_details.partner_id, "
//                . " GROUP_CONCAT(DISTINCT spare_parts_details.id) as spare_id, serial_number_pic, GROUP_CONCAT(DISTINCT spare_parts_details.inventory_invoice_on_booking) as inventory_invoice_on_booking ";
//
//        $data['spare_parts'] = $this->service_centers_model->get_spare_parts_on_group($where, $select, "spare_parts_details.booking_id", $sf_id);             
//        if(!empty($data)){
//             $this->load->view('service_centers/requested_spare_on_sf_booking', $data);
//        }
//      
//    }

    /**
     * @desc: This function is used to get micro warehouse history details
     * @param: void
     * @return void
     */
    function micro_warehouse_history_list() {
        $micro_wh_mp_id = $this->input->post("micro_wh_mp_id");
        if (!empty($micro_wh_mp_id)) {
            $micro_warehouse = $this->vendor_model->get_micro_warehouse_history($micro_wh_mp_id);
            if (!empty($micro_warehouse)) {
                echo json_encode($micro_warehouse);
            }
        }
    }

    /**
     * @desc This is used to update micro warehouse related field. Just pass field name, value and table primary key id
     */
    function update_micro_warehouse_column() {
        ob_clean();
        $this->form_validation->set_rules('data', 'Data', 'required');
        $this->form_validation->set_rules('id', 'id', 'required');
        $this->form_validation->set_rules('column', 'column', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post('data');
            $id = $this->input->post('id');
            $column = $this->input->post('column');

            $this->service_centers_model->update_micro_warehouse(array('id' => $id), array($column => $data));
            echo "Success";
        } else {
            echo "Error";
        }
    }

    /*
     * @desc - This function is used to get distinct category from service_center_price table
     * @param - $service_id
     * @return - Selct HTML
     */

    function get_service_price_category() {
        $categories = $this->service_centre_charges_model->get_service_charge_details(array('service_id' => $this->input->post("service_id")), 'category', 'category');
        $html = "<option disabled Selected>Select Category</option>";
        foreach ($categories as $key => $value) {
            $html .= "<option value='" . $value['category'] . "'>" . $value['category'] . "</option>";
        }
        echo $html;
    }

    /*
     * @desc - This function is used to get distinct capacity from service_center_price table
     * @param - $service_id, $category
     * @return - Selct HTML
     */

    function get_service_price_capacity() {
        $capacities = $this->service_centre_charges_model->get_service_charge_details(array('service_id' => $this->input->post("service_id"), 'category' => $this->input->post("category")), 'capacity', 'capacity');
        $html = "<option disabled Selected>Select Capacity</option>";
        foreach ($capacities as $key => $value) {
            $html .= "<option value='" . $value['capacity'] . "'>" . $value['capacity'] . "</option>";
        }
        echo $html;
    }

    /*
     * @desc - This function is used to get distinct service category from service_center_price table
     * @param - $service_id, $category, $capacity
     * @return - Selct HTML
     */

    function get_service_price_service_category() {
        $service_categories = $this->service_centre_charges_model->get_service_charge_details(array('service_id' => $this->input->post("service_id"), 'category' => $this->input->post("category"), 'capacity' => $this->input->post("capacity")), 'service_category', 'service_category');
        $html = "<option disabled Selected>Select Capacity</option>";
        foreach ($service_categories as $value) {
            $html .= "<option value='" . $value['service_category'] . "'>" . $value['service_category'] . "</option>";
        }
        echo $html;
    }

    /**
     * @desc This function is used to load defective part summary number on the SF Dashboard
     */
    function get_defective_part_header_summary() {
        $this->checkUserSession();
        $data['defective_part'] = $this->invoices_model->get_pending_defective_parts($this->session->userdata('service_center_id'));
        $data['oot_shipped'] = $this->invoices_model->get_oot_shipped_defective_parts($this->session->userdata('service_center_id'));
        $data['shipped_parts'] = $this->invoices_model->get_intransit_defective_parts($this->session->userdata('service_center_id'));
        $this->load->view('service_centers/defective_part_header_summary', $data);
    }

    /**
     * @desc This function is used to get defect based on symptoms
     */
    function get_defect_on_symptom() {
        $symptom_id = $this->input->post('technical_problem');

        $data = array();
        if (!is_null($symptom_id)) {
            $data = $this->booking_request_model->get_defect_of_symptom('defect_id,defect', array('symptom_id' => $symptom_id));
        }
        if (count($data) <= 0) {
            $data[0] = array('defect_id' => 0, 'defect' => 'Default');
        }
        echo json_encode($data);
    }

    /**
     * @desc This function is used to get solution based on symptoms & defects
     */
    function get_solution_on_symptom_defect() {
        $symptom_id = $this->input->post('technical_symptom');
        $defect_id = $this->input->post('technical_defect');

        $data = array();
        if (!is_null($symptom_id) && !is_null($defect_id)) {
            $data = $this->booking_request_model->get_solution_of_symptom('solution_id,technical_solution', array('symptom_id' => $symptom_id, 'defect_id' => $defect_id));
        }
        if (count($data) <= 0) {
            $data[0] = array('solution_id' => 0, 'technical_solution' => 'Default');
        }
        echo json_encode($data);
    }

    /**
     * @desc function change password of service center entity.
     * @author Ankit Rajvanshi
     * @since 17-May-2019
     */
    function change_password() {

        if ($this->input->is_ajax_request()) { // verify old password.
            echo $this->user_model->verify_entity_password(_247AROUND_SF_STRING, $this->session->userdata['service_center_id'], $this->input->post('old_password'));
            exit;
        } elseif ($this->input->post()) {

            // Update password.
            $this->user_model->change_entity_password(_247AROUND_SF_STRING, $this->session->userdata['service_center_id'], $this->input->post('new_password'));
            // Send mail.
            $vendor = $this->vendor_model->getVendorContact($this->session->userdata['service_center_id']);
            // set To.
            //$to_email = (!empty($service_center[0]['email']) ? $service_center[0]['email'] : NULL);
            $to = (!empty($vendor[0]['primary_contact_email']) ? $vendor[0]['primary_contact_email'] : NULL); //POC
            // set CC.
            $arr_cc = [];
            // owner
            if (!empty($vendor[0]['owner_email'])) {
                $arr_cc[] = $vendor[0]['owner_email'];
            }
            // RM
            $rm = $this->vendor_model->get_rm_sf_relation_by_sf_id($this->session->userdata['service_center_id']);
            if (!empty($rm[0]['official_email'])) {
                $arr_cc[] = $rm[0]['official_email'];
            }
            // subject
            $subject = "Password changed for : {$this->session->userdata['service_center_name']}";
            if (!empty($to)) {
                $cc = "";
                if(!empty($arr_cc))
                {
                    $cc = implode(',', $arr_cc);
                }
                $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, "", $subject, "Password has been changed successfully.", "", CHANGE_PASSWORD);
                log_message('info', __FUNCTION__ . 'Change password mail sent.');
            }

            // setting feedback message for user.
            $this->session->set_userdata(['success' => 'Password has been changed successfully.']);
            redirect(base_url() . "employee/service_centers/change_password");
        }

        $this->load->view('service_centers/header');
        $this->load->view('service_centers/change_password');
    }

    /*
      This function is for search booking to transfer
     */

//    function spare_transfer() {
//        $this->load->view('service_centers/header');
//        $this->load->view('service_centers/spare_part_transfer');
//    }

//    function booking_spare_list() {
//        $data = array();
//        $from = trim($this->input->post('frombooking'));
//        $to = trim($this->input->post('tobooking'));
//        // $where=array('booking_id',$from);
//        $from_details = $this->partner_model->get_spare_parts_by_any("spare_parts_details.*", array('booking_id' => $from, 'entity_type' => _247AROUND_SF_STRING, 'wh_ack_received_part' => 1,
//            'status' => SPARE_PARTS_REQUESTED));
//        $frominventory_req_id = $from_details[0]['requested_inventory_id'];
//        $to_details = $this->partner_model->get_spare_parts_by_any("*", array('booking_id' => $to,
//            'entity_type' => _247AROUND_PARTNER_STRING, 'purchase_invoice_id' => NULL, 'wh_ack_received_part' => 1, 'status' => SPARE_PARTS_REQUESTED));
//
//        // print_r($this->db->last_query());exit;
//        $toinventory_req_id = $to_details[0]['requested_inventory_id'];
//        if (empty($from_details) || empty($to_details)) {
//            $this->session->set_flashdata('error_msg', "Spare transfer for this  is not allowed");
//            redirect('service_center/spare_transfer');
//        } else {
//            // $this->load->view('service_centers/spare_part_transfer');
//            $data['from_booking'] = $from_details;
//            $data['to_booking'] = $to_details;
//            $data['frombooking'] = $from_details[0]['booking_id'];
//            $data['tobooking'] = $to_details[0]['booking_id'];
//            $this->load->view('service_centers/header');
//            // $this->load->view('service_centers/booking_spare_list',$data);
//            $this->load->view('service_centers/spare_part_transfer', $data);
//        }
//    }

    /*
      This function is for spare transfer
     */

//    function do_spare_transfer() {
//        // NEED TO RECHECK 
//        $frominventory = $this->input->post('frominventry');
//        $toinventory = $this->input->post('toinventory');
//        $frombooking = $this->input->post('frombooking');
//        $tobooking = $this->input->post('tobooking');
//        $fromspdetailid = $this->input->post('inventoryidfrom');
//        $tospdetailid = $this->input->post('inventoryidto');
//        $data['frombooking'] = $frombooking;
//        $data['tobooking'] = $tobooking;
//        // print_r($_POST);exit;
//        if (empty($frombooking) || empty($tobooking) || $fromspdetailid != $tospdetailid) {
//
//            $this->session->set_flashdata('error_msg', "Spare transfer for this  is not allowed");
//            redirect('service_center/spare_transfer');
//        } else {
//            $form_details = $this->partner_model->get_spare_parts_by_any("spare_parts_details.*", array('spare_parts_details.id' => $frominventory));
//            $to_details = $this->partner_model->get_spare_parts_by_any("spare_parts_details.*", array('spare_parts_details.id' => $toinventory));
//            if (empty($form_details) || empty($to_details)) {
//                $this->session->set_flashdata('error_msg', "Booking spare details not found. Spare transfer not allowed");
//                redirect('service_center/spare_transfer');
//            } else {
//                $fromservicecenter_id = $form_details[0]['service_center_id'];
//                $inventory_stock = $this->inventory_model->get_inventory_stock_count_details('*', array('inventory_id' => $fromspdetailid, 'entity_id' => $fromservicecenter_id));
//                // print_r($inventory_stock);echo $fromservicecenter_id;exit;
//                $inventory_stockcount = $inventory_stock[0]['stock'];
//                $remaining_inventory = ($inventory_stockcount);
//                $data_update = array(
//                    'defective_return_to_entity_id' => $form_details[0]['defective_return_to_entity_id'],
//                    'defective_return_to_entity_type' => $form_details[0]['defective_return_to_entity_type'],
//                    'entity_type' => _247AROUND_SF_STRING,
//                    'purchase_invoice_id' => $form_details[0]['purchase_invoice_id'],
//                    'partner_id' => $form_details[0]['partner_id']
//                );
//                $this->service_centers_model->update_spare_parts(array('id' => $toinventory), $data_update);
//                if ($remaining_inventory < 1) {
//                    $data_from = array(
//                        'entity_type' => _247AROUND_PARTNER_STRING,
//                        'partner_id' => $to_details[0]['defective_return_to_entity_id'],
//                        'purchase_invoice_id' => NULL,
//                        'defective_return_to_entity_id' => $to_details[0]['defective_return_to_entity_id'],
//                        'defective_return_to_entity_type' => $to_details[0]['defective_return_to_entity_type']
//                    );
//                    $this->service_centers_model->update_spare_parts(array('id' => $frominventory), $data_from);
//                }
//                if ($this->db->affected_rows() > 0) {
//                    $this->session->set_flashdata('success', "Spare Successfully transfered ");
//                    redirect('service_center/spare_transfer');
//                } else {
//                    $this->session->set_flashdata('error_msg', "Spare not  transfered ");
//                    redirect('service_center/spare_transfer');
//                }
//            }
//        }
//    }

    /*
      This function is for list of defective part shipped by SF
     */

    function defective_part_shipped_by_sf($offset = 0) {
        $this->checkUserSession();
        log_message('info', __FUNCTION__ . ' Used by :' . $this->session->userdata('service_center_name'));
        $service_center_id = $this->session->userdata('service_center_id');
        $where = "spare_parts_details.service_center_id = '" . $service_center_id . "' "
                . "  AND status IN ('" . DEFECTIVE_PARTS_SHIPPED . "', '" . OK_PARTS_SHIPPED . "')";

        $config['base_url'] = base_url() . 'service_center/defective_part_shipped_by_sf';
        $total_rows = $this->partner_model->get_spare_parts_booking_list($where, false, false, false);
        $config['total_rows'] = $total_rows[0]['total_rows'];
        $config['per_page'] = 50;
        $config['uri_segment'] = 3;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();
        $data['count'] = $config['total_rows'];
        $data['spare_parts'] = $this->partner_model->get_spare_parts_booking_list($where, $offset, $config['per_page'], true);
        $this->load->view('service_centers/header');
        $this->load->view('service_centers/defective_part_shipped_by_sf', $data);
    }

    function get_sf_edit_booking_form($booking_id, $redirect_url = null) {
        $this->checkUserSession();
        log_message('info', __FUNCTION__ . " Booking ID: " . print_r($booking_id, true));
        $booking_id = base64_decode(urldecode($booking_id));
        $redirect_url = !empty($redirect_url) ? base64_decode(urldecode($redirect_url)) : "";
        $booking = $this->booking_creation_lib->get_edit_booking_form_helper_data($booking_id, NULL, NULL, true);
        $booking['booking_history']['redirect_url'] = $redirect_url;
        if ($booking) {
            if (($booking['booking_history'][0]['assigned_vendor_id'] == $this->session->userdata('service_center_id'))) {
                $is_spare_requested = $this->is_spare_requested($booking);
                $booking['booking_history']['is_spare_requested'] = $is_spare_requested;
                // Check if any line item against booking is invoiced to partner or not
                $arr_booking_unit_details = !empty($booking['unit_details'][0]['quantity']) ? $booking['unit_details'][0]['quantity'] : [];
                $booking['booking_history']['is_partner_invoiced'] = $this->booking_utilities->is_partner_invoiced($arr_booking_unit_details);                                       
                $this->load->view('service_centers/header');
                if (isset($booking['booking_files'])) {
                    $amc_file_array = array();
                    foreach ($booking['booking_files'] as $value) {
                        if ($value['file_description_id'] == ANNUAL_MAINTENANCE_CONTRACT) {
                            array_push($amc_file_array, $value['file_name']);
                        }
                    }
                    $booking['amc_file_lists'] = $amc_file_array;
                }
                
                // check is booking warranty type
                foreach ($booking["unit_details"][0]['quantity'] as $val) {
                    if ($val['price_tags'] == AMC_PRICE_TAGS) {
                        $booking['amc_warranty_tag'] = TRUE;
                    } else {
                        $booking['amc_warranty_tag'] = FALSE;
                    }
                }

                $this->load->view('service_centers/update_booking', $booking);
            } else {
                echo "<p style='text-align: center;font: 20px sans-serif;background: #df6666; padding: 10px;color: #fff;'>Booking Id Not Exist</p>";
            }
        } else {
            echo "<p style='text-align: center;font: 20px sans-serif;background: #df6666; padding: 10px;color: #fff;'>Booking Id Not Exist</p>";
        }
    }

    function is_spare_requested($booking) {
        if (array_key_exists('spare_parts', $booking['booking_history'])) {
            foreach ($booking['booking_history']['spare_parts'] as $values) {
                if ($values['status'] != _247AROUND_CANCELLED) {
                    return true;
                }
            }
        }
        return false;
    }

    function spare_assigned_to_partner() {
        
        if(!empty($this->session->userdata('warehouse_id'))) {
            $this->checkEmployeeUserSession();
            $sf_id = $this->session->userdata('warehouse_id');
        } else {
            $this->check_WH_UserSession();
            $sf_id = $this->session->userdata('service_center_id');
        }
        
        log_message('info', __FUNCTION__ . " sf Id: " . $sf_id);
        
        //$sf_states = $this->service_centers_model->get_warehouse_state($sf_id);
        $sf_states = "";
        //echo"<pre>";print_r($sf_states);exit;
        $where = "spare_parts_details.entity_type =  '" . _247AROUND_PARTNER_STRING . "' AND status = '" . SPARE_PARTS_REQUESTED . "' "
                . " AND inventory_stocks.stock > 0  AND  booking_details.current_status IN ('" . _247AROUND_PENDING . "', '" . _247AROUND_RESCHEDULED . "') "
                . " AND wh_ack_received_part != 0  "
                . (!empty($sf_states) ? " AND booking_details.state IN ('" . implode("','", $sf_states) . "')" : "");

        $select = "spare_parts_details.id,spare_parts_details.partner_challan_file, spare_parts_details.booking_id, spare_parts_details.partner_id, spare_parts_details.entity_type, spare_parts_details.service_center_id, spare_parts_details.partner_challan_number,spare_parts_details.parts_requested as parts_requested, purchase_invoice_id, users.name, "
                . "booking_details.booking_primary_contact_no, booking_details.partner_id as booking_partner_id, booking_details.flat_upcountry,"
                . "booking_details.booking_address,booking_details.initial_booking_date, booking_details.is_upcountry, "
                . "booking_details.upcountry_paid_by_customer,booking_details.amount_due,booking_details.state, service_centres.name as vendor_name, "
                . "service_centres.address, service_centres.state, service_centres.gst_no, service_centres.pincode, "
                . "service_centres.district,service_centres.id as sf_id,service_centres.is_gst_doc,service_centres.signature_file, "
                . "  inventory_stocks.stock as stock, DATEDIFF(CURRENT_TIMESTAMP,  STR_TO_DATE(date_of_request, '%Y-%m-%d')) AS age_of_request,"
                . "  spare_parts_details.model_number as model_number, "
                . "  spare_parts_details.serial_number as serial_number,"
                . " spare_parts_details.quantity,"
                . " spare_parts_details.shipped_quantity,"
                . "spare_parts_details.remarks_by_sc as remarks_by_sc, spare_parts_details.partner_id, "
                . " spare_parts_details.id as spare_id, serial_number_pic, spare_parts_details.inventory_invoice_on_booking, i.part_number, service_centres.active, service_centres.on_off ";

        $data['spare_parts'] = $this->service_centers_model->spare_assigned_to_partner($where, $select, "spare_parts_details.booking_id", $sf_id, -1, -1, 0, ['column' => 'age_of_request', 'sorting' => 'desc']);

        $data['is_ajax'] = $this->input->post('is_ajax');
        if (empty($this->input->post('is_ajax'))) {
            $this->load->view('service_centers/header');
        } 
        
        $this->load->view('service_centers/spare_assigned_to_partner', $data);
    }

    /*
     * Delivered spare transfer view and list of inventories
     * 
     * 
     */

 function delivered_spare_transfer() {
        $data = array();
        $from = trim($this->input->post('frombooking'));
        $to = trim($this->input->post('tobooking'));
        $sf_id = '';
        if(!empty($this->session->userdata('service_center_id'))) {
            $sf_id = $this->session->userdata('service_center_id');
        }
        
        if (isset($from) && isset($to) && !empty($from) && !empty($to)) {
            $where_from = array('spare_parts_details.booking_id' => $from,
                'wh_ack_received_part' => 1,
                "status in ('" . SPARE_DELIVERED_TO_SF . "','" . OK_PART_TO_BE_SHIPPED . "')" => null,
                'part_warranty_status' => SPARE_PART_IN_WARRANTY_STATUS,
                "`booking_details.service_center_closed_date` is null"  => null
            );
            if (!empty($sf_id)) {
                $where_from['service_center_id'] = $sf_id;
            }
            $from_details = $this->partner_model->get_spare_parts_by_any("spare_parts_details.*", $where_from,true);
            if (!empty($from_details)) {
                $frominventory_req_id = $from_details[0]['requested_inventory_id'];
                if(empty($sf_id)){
                    $sf_id = $from_details[0]['service_center_id'];
                }
            }
            $to_details = $this->partner_model->get_spare_parts_by_any("spare_parts_details.*", array('booking_id' => $to, 'wh_ack_received_part' => 1, 'status' => SPARE_PARTS_REQUESTED, 'part_warranty_status' => SPARE_PART_IN_WARRANTY_STATUS, 'service_center_id' => $sf_id));
            if (!empty($to_details)) {
                $toinventory_req_id = $to_details[0]['requested_inventory_id'];
            }
            if (empty($from_details) || empty($to_details) || ($from==$to)) { /// Stop searching parts to transfer if both booking are same //
                $this->session->set_flashdata('error_msg', "Spare transfer for this is not allowed. Either out warranty parts involved in the bookings or no part is requested in any of two bookings.");
                if ($this->session->userdata('userType') == 'employee') {
                    $this->miscelleneous->load_nav_header();
                    redirect(base_url() . 'service_center/delivered_spare_transfer');
                } else {
                    redirect(base_url() . 'service_center/delivered_spare_transfer');
                }
            } else {
                $data['from_booking'] = $from_details;
                $data['to_booking'] = $to_details;
                $data['frombooking'] = $from_details[0]['booking_id'];
                $data['tobooking'] = $to_details[0]['booking_id'];
                 if ($this->session->userdata('userType') == 'employee') {
                    $this->miscelleneous->load_nav_header();
                    $this->load->view('service_centers/delivered_spare_transfer', $data);
                } else {
                    $this->load->view('service_centers/header');
                    $this->load->view('service_centers/delivered_spare_transfer', $data);
                }
            }
        } else {
            if ($this->session->userdata('userType') == 'employee') {
                $this->miscelleneous->load_nav_header();
                $this->load->view('service_centers/delivered_spare_transfer', $data);
            } else {
                $this->load->view('service_centers/header');
                $this->load->view('service_centers/delivered_spare_transfer', $data);
            }
        }
    }

    /*
     * Delivered spare transfer process
     * 
     * 
     */

   
function do_delivered_spare_transfer() {

        if ($this->session->userdata('userType') == 'employee') {
            $agent_id = _247AROUND_DEFAULT_AGENT;
            $partner_id  = $entity_id = _247AROUND;
            $service_center_id = NULL;
            $entity_type = _247AROUND_EMPLOYEE_STRING;
            $agent_name = _247AROUND_DEFAULT_AGENT_NAME;
        } else {
            $agent_id = $this->session->userdata("service_center_agent_id");
            $service_center_id = $entity_id = $this->session->userdata('service_center_id');
            $entity_type = _247AROUND_SF_STRING;
            $partner_id = NULL;
            $agent_name = $this->session->userdata('service_center_name');
        }
        
        $from_spare_id = $this->input->post('fromspareid');
        $to_spare_id = $this->input->post('tospareid');
        $frombooking = $this->input->post('frombooking');
        $tobooking = $this->input->post('tobooking');
        $inventory_id_from = $this->input->post('inventoryidfrom');
        $inventory_id_to = $this->input->post('inventoryidto');
        $data['frombooking'] = $frombooking;
        $data['tobooking'] = $tobooking;
        $to_update = false;
        $from_update = false;
        if (empty($frombooking) || empty($tobooking) || ($inventory_id_from != $inventory_id_to) || ($tobooking == $frombooking)) {   //// DO not transfer in between same booking spares ///
            echo 'fail';
        } else {
            $form_details = $this->partner_model->get_spare_parts_by_any("spare_parts_details.*, booking_details.partner_id as booking_partner_id", array('spare_parts_details.id' => $from_spare_id),true);
            $to_details = $this->partner_model->get_spare_parts_by_any("spare_parts_details.*, booking_details.partner_id as booking_partner_id", array('spare_parts_details.id' => $to_spare_id),true);
            if (empty($form_details) || empty($to_details) || ($form_details[0]['service_center_id'] != $to_details[0]['service_center_id'])) {
                echo 'fail';
            } else {
                $to_details_array = array(
                    'status' => SPARE_DELIVERED_TO_SF,
                    'entity_type' => $form_details[0]['entity_type'],
                    'partner_id' => $form_details[0]['partner_id'],
                    'is_micro_wh' => $form_details[0]['is_micro_wh'],
                    'purchase_invoice_id' => $form_details[0]['purchase_invoice_id'],
                    'model_number_shipped' => $form_details[0]['model_number_shipped'],
                    'parts_shipped' => $form_details[0]['parts_shipped'],
                    'shipped_parts_type' => $form_details[0]['shipped_parts_type'],
                    'shipped_date' => $form_details[0]['shipped_date'],
                    'defective_part_shipped' => $form_details[0]['defective_part_shipped'],
                    'defective_part_shipped_date' => $form_details[0]['defective_part_shipped_date'],
                    'shipped_inventory_id' => $form_details[0]['shipped_inventory_id'],
                    'defective_return_to_entity_type' => $form_details[0]['defective_return_to_entity_type'],
                    'defective_return_to_entity_id' => $form_details[0]['defective_return_to_entity_id'],
                    'courier_name_by_partner' => $form_details[0]['courier_name_by_partner'],
                    'awb_by_partner' => $form_details[0]['awb_by_partner'],
                    'courier_price_by_partner' => $form_details[0]['courier_price_by_partner'],
                    'courier_pic_by_partner' => $form_details[0]['courier_pic_by_partner'],
                    'wh_ack_received_part' => $form_details[0]['wh_ack_received_part'],
                    'acknowledge_date' => $form_details[0]['acknowledge_date'],
                    'auto_acknowledeged' => $form_details[0]['auto_acknowledeged'],
                    'remarks_by_partner' => $form_details[0]['remarks_by_partner'],
                    'partner_challan_number' => $form_details[0]['partner_challan_number'],
                    'partner_challan_file' => $form_details[0]['partner_challan_file'],
                    'spare_request_symptom' => $form_details[0]['spare_request_symptom'],
                );

                $from_details_array = array(
                    'status' => $to_details[0]['status'],
                    'entity_type' => $to_details[0]['entity_type'],
                    'partner_id' => $to_details[0]['partner_id'],
                    'is_micro_wh' => $to_details[0]['is_micro_wh'],
                    'purchase_invoice_id' => $to_details[0]['purchase_invoice_id'],
                    'model_number_shipped' => $to_details[0]['model_number'], ////  during part requested shipped data NA
                    'parts_shipped' => $to_details[0]['parts_shipped'],
                    'shipped_parts_type' => $to_details[0]['shipped_parts_type'],
                    'shipped_date' => $to_details[0]['shipped_date'],
                    'defective_part_shipped' => $to_details[0]['defective_part_shipped'],
                    'defective_part_shipped_date' => $to_details[0]['defective_part_shipped_date'],
                    'shipped_inventory_id' => $to_details[0]['shipped_inventory_id'],
                    'defective_return_to_entity_type' => $to_details[0]['defective_return_to_entity_type'],
                    'defective_return_to_entity_id' => $to_details[0]['defective_return_to_entity_id'],
                    'courier_name_by_partner' => $to_details[0]['courier_name_by_partner'],
                    'awb_by_partner' => $to_details[0]['awb_by_partner'],
                    'courier_price_by_partner' => $to_details[0]['courier_price_by_partner'],
                    'courier_pic_by_partner' => $to_details[0]['courier_pic_by_partner'],
                    'wh_ack_received_part' => $to_details[0]['wh_ack_received_part'],
                    'acknowledge_date' => $to_details[0]['acknowledge_date'],
                    'auto_acknowledeged' => $to_details[0]['auto_acknowledeged'],
                    'remarks_by_partner' => $to_details[0]['remarks_by_partner'],
                    'partner_challan_number' => $to_details[0]['partner_challan_number'],
                    'partner_challan_file' => $to_details[0]['partner_challan_file'],
                    'spare_request_symptom' => $to_details[0]['spare_request_symptom'],
                    'consumed_part_status_id' => null,
                    'consumption_remarks' => null,
                );                                                  
                $this->service_centers_model->update_spare_parts(array('id' => $to_spare_id), $to_details_array);
                
                /* Get details of spare parts that pening in requested and approval */
               
                 $where = array(
                    "spare_parts_details.part_warranty_status" => SPARE_PART_IN_WARRANTY_STATUS,
                      "spare_parts_details.booking_id" => $tobooking,
                    "spare_parts_details.status IN ('" . SPARE_PARTS_REQUESTED . "', '" . SPARE_PART_ON_APPROVAL . "', '" . SPARE_SHIPPED_BY_PARTNER . "', '" . SPARE_PARTS_SHIPPED_BY_WAREHOUSE . "')" => NULL,
                );
                 
                $to_booking_spare_details = $this->partner_model->get_spare_parts_by_any("spare_parts_details.*", $where);
                
                                
                $bd_data = array();
                if (empty($to_booking_spare_details)) {
                    $b = $this->booking_model->get_booking_details('current_status, partner_id', ['booking_id' => $tobooking])[0];
                    $b['internal_status'] = SPARE_DELIVERED_TO_SF;
                    $partner_status = $this->booking_utilities->get_partner_status_mapping_data($b['current_status'], $b['internal_status'], $b['partner_id'], $tobooking);
                    $bd_data['internal_status'] = SPARE_DELIVERED_TO_SF;
                    $bd_data['partner_current_status'] = $partner_status[0];
                    $bd_data['partner_internal_status'] = $partner_status[1];
                    $bd_data['actor'] = $partner_status[2];
                    $bd_data['next_action'] = $partner_status[3];
                    $this->booking_model->update_booking($tobooking, $bd_data);
                }
                
                /* Insert Spare Tracking Details */
                if (!empty($to_spare_id)) {
                    $tracking_details = array('spare_id' => $to_spare_id, 'action' => $form_details[0]['status'], 'remarks' => "Spare Part Transfer from " . $frombooking . " to " . $tobooking, 'agent_id' => $agent_id, 'entity_id' => $entity_id, 'entity_type' => $entity_type);
                    $this->service_centers_model->insert_spare_tracking_details($tracking_details);
                }

                $this->notify->insert_state_change($frombooking, SPARE_PARTS_REQUESTED, "", "Spare Part Transfer from " . $frombooking . " to " . $tobooking, $agent_id, $agent_name, "", "", $partner_id, $service_center_id, $to_spare_id);

                $sc_data['current_status'] = _247AROUND_PENDING;
                $sc_data['internal_status'] = SPARE_DELIVERED_TO_SF;
                $sc_data['update_date'] = date("Y-m-d H:i:s");
                $this->vendor_model->update_service_center_action($frombooking, $sc_data);

                if ($this->db->affected_rows() > 0) {
                    $to_update = true;
                }
                $this->service_centers_model->update_spare_parts(array('id' => $from_spare_id), $from_details_array);
                
                $from_booking_spare_details = $this->partner_model->get_spare_parts_by_any("spare_parts_details.*", array("spare_parts_details.part_warranty_status" => SPARE_PART_IN_WARRANTY_STATUS,"spare_parts_details.booking_id" => $frombooking,"spare_parts_details.status IN ('" . SPARE_PARTS_REQUESTED . "')" => NULL,));            
                $bd = array();
                if (!empty($from_booking_spare_details)) {
                    $booking = $this->booking_model->get_booking_details('current_status, partner_id', ['booking_id' => $frombooking])[0];
                    $booking['internal_status'] = SPARE_PARTS_REQUIRED;
                    $partner_status_data = $this->booking_utilities->get_partner_status_mapping_data($booking['current_status'], $booking['internal_status'], $booking['partner_id'], $frombooking);
                    $bd['internal_status'] = SPARE_PARTS_REQUIRED;
                    $bd['partner_current_status'] = $partner_status_data[0];
                    $bd['partner_internal_status'] = $partner_status_data[1];
                    $bd['actor'] = $partner_status_data[2];
                    $bd['next_action'] = $partner_status_data[3];
                    $this->booking_model->update_booking($frombooking, $bd);
                }

                /* Insert Spare Tracking Details */
                if (!empty($from_spare_id)) {
                    $tracking_details = array('spare_id' => $from_spare_id, 'action' => $to_details[0]['status'], 'remarks' => "Spare Part Transfer from " . $frombooking . " to " . $tobooking, 'agent_id' => $agent_id, 'entity_id' => $entity_id, 'entity_type' => $entity_type);
                    $this->service_centers_model->insert_spare_tracking_details($tracking_details);
                }

                $this->notify->insert_state_change($tobooking, SPARE_DELIVERED_TO_SF, "", "Spare Part Transfer from " . $frombooking . " to " . $tobooking, $agent_id, $agent_name, "", "", $partner_id, $service_center_id, $from_spare_id);

                $sc_data1['current_status'] = _247AROUND_PENDING;
                $sc_data1['internal_status'] = SPARE_PARTS_REQUESTED;
                $sc_data1['update_date'] = date("Y-m-d H:i:s");
                $this->vendor_model->update_service_center_action($tobooking, $sc_data1);


                if ($this->db->affected_rows() > 0) {
                    $from_update = true;
                }
                if ($to_update && $from_update) {
                    echo 'success';
                } else {
                    echo 'fail';
                }
            }
        }
    }

    /**
     * @desc: this is used to check warranty data
     * @return: void
     */
    function check_warranty($partner_id = null, $service_id = null, $brand = null) {
        $partners = $this->partner_model->getpartner();
        foreach ($partners as $partnersDetails) {
            $partnerArray[$partnersDetails['id']] = $partnersDetails['public_name'];
        }
        $this->load->view('service_centers/header');
        $this->load->view('warranty/check_warranty', ['partnerArray' => $partnerArray, 'partner_id' => $partner_id, 'service_id' => $service_id, 'brand' => $brand]);
    }

    /**
     * @desc: get defective parts shipped by sf
     * Created by -Abhishek Awasthi on July 18 ,2019
     * @return: void
     */
    function defective_parts_sent($offset = 0) {
        $this->checkUserSession();
        log_message('info', __FUNCTION__ . ' Used by :' . $this->session->userdata('service_center_name'));
        $service_center_id = $this->session->userdata('service_center_id');

        $where = array(
            "spare_parts_details.defective_part_required" => 1,
            "spare_parts_details.service_center_id" => $service_center_id,
            "spare_parts_details.status IN ('" . OK_PARTS_SHIPPED . "', '" . DEFECTIVE_PARTS_SHIPPED . "')" => NULL,
        );

        $select = "booking_details.service_center_closed_date,booking_details.booking_primary_contact_no as mobile, parts_shipped, "
                . " spare_parts_details.booking_id,booking_details.partner_id as booking_partner_id, users.name, "
                . " sf_challan_file as challan_file, "
                . " remarks_defective_part_by_partner, "
                . " remarks_by_partner,spare_parts_details.remarks_defective_part_by_sf,spare_parts_details.awb_by_sf,spare_parts_details.courier_charges_by_sf,spare_parts_details.courier_name_by_sf,spare_parts_details.defective_part_shipped, spare_parts_details.defective_part_shipped_date,spare_parts_details.partner_id,spare_parts_details.service_center_id,spare_parts_details.defective_return_to_entity_id,spare_parts_details.entity_type,"
                . " spare_parts_details.id,spare_parts_details.challan_approx_value ,i.part_number ";

        $group_by = "spare_parts_details.id";
        $order_by = "status = '" . DEFECTIVE_PARTS_REJECTED_BY_WAREHOUSE . "', spare_parts_details.booking_id ASC";


        $config['base_url'] = base_url() . 'service_center/defective_parts_sent';
        $config['total_rows'] = $this->service_centers_model->count_spare_parts_booking($where, $select);

        $config['per_page'] = 50;
        $config['uri_segment'] = 3;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();
        $data['count'] = $config['total_rows'];
        $data['spare_parts'] = $this->service_centers_model->get_spare_parts_booking($where, $select, $group_by, $order_by, $offset, $config['per_page']);
        $data['courier_details'] = $this->inventory_model->get_courier_services('*');
        $this->load->view('service_centers/header');
        $this->load->view('service_centers/defective_parts_sent', $data);
    }

    /*
      @Desc - This function is used to download SF completed bookings csv
     */

    function download_service_center_completed_bookings() {
        $list = $this->service_centers_model->download_service_center_completed_bookings();
        $headings = array("Booking ID", "Customer Name", "Mobile", "Product", "Request Type", "Closing Date", "Closing Remarks", "SF Earned", "Rating", "Consumed Parts", "Engineer", "TAT");
        $this->miscelleneous->downloadCSV($list, $headings, "SF_completed_bookings");
    }

    /**
     * Booking summary report for service center.
     */
    function summary_report() {

        $data['states'] = $this->reusable_model->get_search_result_data("state_code", "state", array(), array(), NULL, array('state' => 'ASC'), NULL, array(), array());
        $data['services'] = $this->booking_model->selectservice();
        $data['summaryReportData'] = $this->reusable_model->get_search_result_data("reports_log", "filters,date(create_date) as create_date,url", array("entity_type" => _247AROUND_SF_STRING, "entity_id" => $this->session->userdata('service_center_id')), NULL, array("length" => 50, "start" => ""), array('id' => 'DESC'), NULL, NULL, array());
        $this->load->view('service_centers/header');
        $this->load->view('service_centers/summary_report', $data);
    }

    function get_summary_report_data($partnerID) {

        $summaryReportData = $this->reusable_model->get_search_result_data("reports_log", "filters,date(create_date) as create_date,url", array("entity_type" => _247AROUND_PARTNER_STRING, "entity_id" => $partnerID), NULL, array("length" => 50, "start" => ""), array('id' => 'DESC'), NULL, NULL, array());

        $str_body = '';
        if (!empty($summaryReportData)) {
            foreach ($summaryReportData as $summaryReport) {
                $finalFilterArray = array();
                $filterArray = json_decode($summaryReport['filters'], true);
                foreach ($filterArray as $key => $value) {
                    if ($key == "Date_Range" && is_array($value) && !empty(array_filter($value))) {
                        $dArray = explode(" - ", $value);
                        $key = "Registration Date";
                        $startTemp = strtotime($dArray[0]);
                        $endTemp = strtotime($dArray[1]);
                        $startD = date('d-F-Y', $startTemp);
                        $endD = date('d-F-Y', $endTemp);
                        $value = $startD . " To " . $endD;
                    }
                    if ($key == "Completion_Date_Range" && is_array($value) && !empty(array_filter($value))) {
                        $dArray = explode(" - ", $value);
                        $key = "Completion Date";
                        $startTemp = strtotime($dArray[0]);
                        $endTemp = strtotime($dArray[1]);
                        $startD = date('d-F-Y', $startTemp);
                        $endD = date('d-F-Y', $endTemp);
                        $value = $startD . " To " . $endD;
                    }
                    $finalFilterArray[] = $key . " : " . $value;
                }

                $str_body .= '<tr>';
                $str_body .= '<td>' . implode(", ", $finalFilterArray) . '</td>';
                $str_body .= '<td>' . $summaryReport['create_date'] . '</td>';
                $str_body .= '<td><a class="btn btn-success" style="background: #2a3f54;" href="' . base_url() . "employee/partner/download_custom_summary_report/" . $summaryReport['url'] . '">Download</a></td>';
                $str_body .= '</tr>';
            }
        }

        echo $str_body;
    }

    /**
     * this function is used to get the warranty status of booking, called from AJAX
     * function returns output in two formats : 
     * CASE 1 => return warranty status against booking 
     * CASE 2 => returns true/false after matching booking request type with warranty status and a response Message 
     * @author Prity Sharma
     * @date 20-08-2019
     * @return JSON
     */
    public function get_warranty_data($case = 1, $checkInstallationDate = 0) {
        $post_data = $this->input->post();
        $arrBookings = $post_data['bookings_data'];
        $arrBookingsWarrantyStatus = $this->warranty_utilities->get_warranty_status_of_bookings($arrBookings, $checkInstallationDate);
        switch ($case) {
            case 1:
                echo json_encode($arrBookingsWarrantyStatus);
                break;
            case 2:
                $warranty_result = $this->warranty_utilities->match_warranty_status_with_request_type($arrBookings, $arrBookingsWarrantyStatus);
                echo $warranty_result;
                break;
        }
    }

    /**
     * 
     * @param type $booking_id
     * @param type $service_center_id
     * @param type $id
     * @param type $partner_id
     */
    function wrong_spare_part($booking_id) {
        $this->checkUserSession();
        log_message('info', __FUNCTION__ . ' Used by :' . $this->session->userdata('service_center_name'));
        $service_center_id = $this->session->userdata('service_center_id');

        $post_data = $this->input->post();
        $data['booking_id'] = $booking_id;
        $data['spare_part_detail_id'] = $post_data['spare_part_detail_id'];
        $data['part_name'] = $post_data['part_name'];
        $data['service_id'] = $post_data['service_id'];
        $data['shipped_inventory_id'] = $post_data['shipped_inventory_id'];
        $data['parts'] = $this->inventory_model->get_inventory_master_list_data('inventory_id, part_name, part_number', ['service_id' => $data['service_id'], 'inventory_id not in (1,2)' => NULL]);

        if (!empty($post_data['wrong_flag'])) {

            $wrong_part_detail = [];
            $wrong_part_detail['spare_id'] = $data['spare_part_detail_id'];
            $wrong_part_detail['part_name'] = $post_data['wrong_part_name'];
            if (!empty($data['shipped_inventory_id'])) {
                $wrong_part_detail['inventory_id'] = $post_data['wrong_part'];
            } else {
                $wrong_part_detail['inventory_id'] = NULL;
            }
            $wrong_part_detail['remarks'] = $post_data['remarks'];
            echo json_encode($wrong_part_detail);
            exit;
        }

        $this->load->view('service_centers/wrong_spare_part', $data);
    }

    function change_multiple_consumption() {
        $data['spare_consumed_status'] = $this->reusable_model->get_search_result_data('spare_consumption_status', 'id, consumed_status,reason_text,status_description,tag', ['active' => 1], NULL, NULL, ['reason_text' => SORT_ASC], NULL, NULL);
        $data['consumption_status_selected'] = $this->input->post()['status_selected'];
        $this->load->view('service_centers/change_multiple_part_consumption', $data);
    }

    function change_consumption() {
        $post_data = $this->input->post();
        $data['spare_id'] = $post_data['spare_part_detail_id'];
        $data['booking_id'] = $post_data['booking_id'];
        $data['booking_details'] = $this->reusable_model->get_search_result_data('booking_details', '*', ['booking_id' => $data['booking_id']], NULL, NULL, NULL, NULL, NULL)[0];
        $data['spare_part_detail'] = $this->partner_model->get_spare_parts_by_any('spare_parts_details.*, inventory_master_list.part_number', ['spare_parts_details.id' => $data['spare_id'], 'spare_parts_details.status != "' . _247AROUND_CANCELLED . '"' => NULL, 'parts_shipped is not null' => NULL], FALSE, FALSE, FALSE, ['is_inventory' => true])[0];
        $data['spare_consumed_status'] = $this->reusable_model->get_search_result_data('spare_consumption_status', 'id, consumed_status,reason_text,status_description,tag', ['active' => 1, 'tag <> "'.PART_NOT_RECEIVED_COURIER_LOST_TAG.'"' => NULL], NULL, NULL, ['reason_text' => SORT_ASC], NULL, NULL);
        $this->load->view('service_centers/change_consumption', $data);
    }

    function reject_spare_part() {
        $post_data = $this->input->post();
        $data['spare_id'] = $post_data['spare_part_detail_id'];
        $data['booking_id'] = $post_data['booking_id'];
        $data['booking_details'] = $this->reusable_model->get_search_result_data('booking_details', '*', ['booking_id' => $data['booking_id']], NULL, NULL, NULL, NULL, NULL)[0];
        $data['spare_part_detail'] = $this->partner_model->get_spare_parts_by_any('spare_parts_details.*, inventory_master_list.part_number', ['spare_parts_details.id' => $data['spare_id'], 'spare_parts_details.status != "' . _247AROUND_CANCELLED . '"' => NULL, 'parts_shipped is not null' => NULL], FALSE, FALSE, FALSE, ['is_inventory' => true])[0];

        $reject_options = [];
        $where_internal_status = array("page" => "defective_parts", "active" => '1');
        $data['internal_status'] = $this->booking_model->get_internal_status($where_internal_status);

        $this->load->view('service_centers/reject_spare_part', $data);
    }

    /*
     * It's function used to get service centers list
     * @echo option
     */

    function get_service_centers_list() {

        $vendor_list = $this->vendor_model->getVendorDetails("service_centres.id, service_centres.name, service_centres.company_name", array("service_centres.active" => 1));

        $option = '<option selected="" disabled="">Select Service Centres</option>';
        foreach ($vendor_list as $value) {
            $option .= "<option value='" . $value['id'] . "'";
            if (count($vendor_list) == 1) {
                $option .= " selected> ";
            } else {
                $option .= "> ";
            }

            $option .= $value['name'] . "</option>";
        }
        echo $option;
    }

    /*
     * It's function used to get partner wise SF list
     * @echo option
     */

    function get_partners_wise_sf_list() {

        $partner_id = $this->input->post('partner_id');
        $vendor_list = $this->inventory_model->get_micro_wh_lists_by_partner_id("service_centres.id, service_centres.name, service_centres.company_name", array('micro_wh_mp.partner_id' => $partner_id));

        $option = '<option selected="" disabled="">Select Service Centres</option>';
        foreach ($vendor_list as $value) {
            $option .= "<option value='" . $value['id'] . "'";
            if (count($vendor_list) == 1) {
                $option .= " selected> ";
            } else {
                $option .= "> ";
            }

            $option .= $value['name'] . "</option>";
        }
        echo $option;
    }

    // function to get rejected MSL ///
    function get_rejected_msl($offset = 0) {
        
        $data = array();
        if(!empty($this->session->userdata('warehouse_id'))) {
            $this->checkEmployeeUserSession();
            log_message('info', __FUNCTION__ . " Employee ID: " . $this->session->userdata('id'));
            $data['sf_id'] = $this->session->userdata('warehouse_id');
        } else {
            $this->check_WH_UserSession();
            log_message('info', __FUNCTION__ . " SF ID: " . $this->session->userdata('service_center_id'));
            $data['sf_id'] = $this->session->userdata('service_center_id');
        }

        $this->load->view('service_centers/rejected_spares_send_by_partner', $data);
    }
    
    /**
     * @desc : this method is use to updated consumption from defective/ok to be shipped spare parts page.
     * @author Ankit Rajvanshi
     */
    function update_spare_consumption_reason() {
        //return true;
        $post_data = $this->input->post();
        if(!empty($post_data)) {
            $data = [];
            $data['spare_consumption_status'][$post_data['spare_id']] = $post_data['consumption_reason'];
            $data['consumption_remarks'][$post_data['spare_id']] = $post_data['consumption_remarks'];
            $this->miscelleneous->update_spare_consumption_status($data, $post_data['booking_id']);
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * @desc : This method is use to show list of spare parts delivered to sf.
     * @author Ankit Rajvanshi
     */
    function parts_delivered_to_sf($offset = '') {
        $this->checkUserSession();
        log_message('info', __FUNCTION__ . ' Used by :' . $this->session->userdata('service_center_name'));
        $service_center_id = $this->session->userdata('service_center_id');

        $where = array (
            "status NOT IN ('" . _247AROUND_CANCELLED . "')  " => NULL,
            "spare_parts_details.parts_shipped is not null and spare_parts_details.shipped_date is not null" => NULL,
            "((spare_parts_details.defective_part_shipped is null and spare_parts_details.defective_part_shipped_date is null) or spare_parts_details.status in('".DEFECTIVE_PARTS_REJECTED_BY_WAREHOUSE."','".OK_PARTS_REJECTED_BY_WAREHOUSE."'))" => NULL,
            "spare_parts_details.service_center_id" => $service_center_id
        );

        $select = "booking_details.service_center_closed_date,booking_details.create_date,booking_details.booking_primary_contact_no as mobile, spare_parts_details.*, "
                . " i.part_number, i.part_number as shipped_part_number, spare_consumption_status.consumed_status,  spare_consumption_status.is_consumed, users.name";

        $group_by = "spare_parts_details.id";
        $order_by = "booking_details.create_date desc, spare_parts_details.booking_id ASC";


        $config['base_url'] = base_url() . 'service_center/parts_delivered_to_sf';
        $config['total_rows'] = $this->service_centers_model->count_spare_parts_booking($where, $select);

        $config['per_page'] = 100;
        $config['uri_segment'] = 3;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['count'] = $config['total_rows'];
        $data['spare_parts'] = $this->service_centers_model->get_spare_parts_booking($where, $select, $group_by, $order_by, $offset, $config['per_page']);
        
        
        $this->load->view('service_centers/header');
        $this->load->view('service_centers/delivered_parts', $data);
    }
    
    /**
     * @desc : Method is used to update consumption reason by sf
     * @author Ankit Rajvanshi
     */
    function change_consumption_by_sf() {
        $post_data = $this->input->post();
        $data['spare_id'] = $post_data['spare_id'];
        
        $data['spare_part_detail'] = $this->partner_model->get_spare_parts_by_any('spare_parts_details.*, inventory_master_list.part_number', ['spare_parts_details.id' => $data['spare_id'], 'spare_parts_details.status != "' . _247AROUND_CANCELLED . '"' => NULL, 'parts_shipped is not null' => NULL], FALSE, FALSE, FALSE, ['is_inventory' => true])[0];
        $data['spare_consumed_status'] = $this->reusable_model->get_search_result_data('spare_consumption_status', 'id, consumed_status,reason_text,status_description,tag', ['active' => 1], NULL, NULL, ['consumed_status' => SORT_ASC], NULL, NULL);
        $booking_id = $data['spare_part_detail']['booking_id'];
        if (!empty($post_data['change'])) {
            $data = [];
            $data['spare_consumption_status'][$post_data['spare_id']] = $post_data['spare_consumption_status'][$post_data['spare_id']];
            $data['consumption_remarks'][$post_data['spare_id']] = $post_data['change_consumption_remarks'];
            $this->miscelleneous->update_spare_consumption_status($data, $booking_id);

            return true;
        }
        
        $this->load->view('service_centers/change_consumption_by_sf', $data);
    }
    
    /**
     * @desc : this method marks parts as courier lost by sf.
     * @param type $spare_id
     * @author Ankit Rajvanshi
     */
    function update_courier_lost($spare_id) {

        /* Fetch spare part detail of $spare_id. */
        $spare_parts_details = $this->partner_model->get_spare_parts_by_any('spare_parts_details.booking_id, spare_parts_details.status, spare_parts_details.partner_id', ['spare_parts_details.id' => $spare_id, 'spare_parts_details.status != "' . _247AROUND_CANCELLED . '"' => NULL], FALSE, FALSE, FALSE, ['is_inventory' => true])[0];
        /* update spare status. */
        $this->service_centers_model->update_spare_parts(['id' => $spare_id], 
            [
                'consumed_part_status_id' => '2', 
                'status' => InProcess_Courier_Lost, 
                'old_status' => $spare_parts_details['status']
            ]);
        /* Insert Spare Tracking Details */
        if (!empty($spare_id)) {
            $this->service_centers_model->insert_spare_tracking_details([
                'spare_id' => $spare_id, 
                'action' => InProcess_Courier_Lost, 'remarks' =>  "Courier lost marked by service center",  
                'agent_id' => $this->session->userdata("service_center_agent_id"), 
                'entity_id' => $this->session->userdata('service_center_id'), 'entity_type' => _247AROUND_SF_STRING
            ]);
        }        
        /* Log this in state change table. */

        $this->insert_details_in_state_change($spare_parts_details['booking_id'], InProcess_Courier_Lost, "Courier lost marked by service center", "247Around", "Review Courier Lost Parts", "", $spare_id);

        /* Check status of other parts if not delivered then do not update booking status others update booking internal status.*/
        $check_spare_part_pending = $this->partner_model->get_spare_parts_by_any("spare_parts_details.*", array("spare_parts_details.status IN ('" . SPARE_PART_ON_APPROVAL . "','" . SPARE_PARTS_REQUESTED . "','" . SPARE_PARTS_SHIPPED_BY_WAREHOUSE . "','" . SPARE_SHIPPED_BY_PARTNER . "','".SPARE_DELIVERED_TO_SF."','".SPARE_OOW_EST_REQUESTED."', '".SPARE_OOW_EST_GIVEN."', '".ESTIMATE_APPROVED_BY_CUSTOMER."')" => NULL, 'spare_parts_details.booking_id' => $spare_parts_details['booking_id']), true, false);
        if (empty($check_spare_part_pending)) {
            // update booking.
            $partner_status = $this->booking_utilities->get_partner_status_mapping_data(_247AROUND_PENDING, InProcess_Courier_Lost, $spare_parts_details['partner_id'], $spare_parts_details['booking_id']);
            $booking_detail_data = [];
            if (!empty($partner_status)) {
                $booking_detail_data['partner_current_status'] = $partner_status[0];
                $booking_detail_data['partner_internal_status'] = $partner_status[1];
                $booking_detail_data['actor'] = $partner_status[2];
                $booking_detail_data['next_action'] = $partner_status[3];
            }

            $booking_detail_data['internal_status'] = InProcess_Courier_Lost;
            $this->booking_model->update_booking($spare_parts_details['booking_id'], $booking_detail_data);
        }
        
        redirect(base_url().'service_center/parts_delivered_to_sf'); 
    }
    
    function checkEmployeeUserSession() {
        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee')) {
            return TRUE;
        } else {
            $this->session->sess_destroy();
            redirect(base_url() . "employee/login");
        }
    }
    
    /*
     * @desc : This function is load the file upload history
     * @param: void
     */
    
    function get_uploda_file_history(){
        
        $post_data = array('length' => $this->input->post('length'),
            'start' => $this->input->post('start'),
            'file_type' => trim($this->input->post('file_type')),
            'search_value' => trim($this->input->post('search')['value']),
            'partner_id' => $this->input->post('partner_id')
        );
        
        if(!empty($this->input->post('partner_id'))){
            $post_data['partner_id'] = $this->input->post("partner_id");
            $filtered_post_data['partner_id'] = $this->input->post("partner_id");
        }
        
        $filtered_post_data = array(
                'length' =>NULL,
                'start' =>NULL,
                'file_type' =>trim($this->input->post('file_type')),
                'search_value' => trim($this->input->post('search')['value'])
        );
        
        $list = $this->reporting_utils->get_uploaded_file_history($post_data);

        $table_data = array();
        $no = $post_data['start'];
        foreach ($list as $file_list) {
            $no++;
            $file_list->file_source = $this->input->post('file_source');
            if($this->input->post("show_amt_paid")) {
                $file_list->show_amt_paid = $this->input->post('show_amt_paid');
            }
            $row =  $this->upload_file_table_data($file_list, $no);
            $table_data[] = $row;
        }

        $allRecords = $this->reporting_utils->get_uploaded_file_history();
        $allFilteredRecords = $this->reporting_utils->get_uploaded_file_history($filtered_post_data);
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => count($allRecords),
            "recordsFiltered" =>  count($allFilteredRecords),
            "data" => $table_data,
        );
        unset($post_data);
        echo json_encode($output);
    }
    
    /**
     * @Desc: This function is used to make the table data for upload file history
     * @params: void
     * @return: void
     * 
     */
    private function upload_file_table_data($file_list, $no) {
        if ($file_list->result === FILE_UPLOAD_SUCCESS_STATUS) {
            $result = "<div class='label label-success'>$file_list->result</div>";
        } else if ($file_list->result === FILE_UPLOAD_FAILED_STATUS) {
            $result = "<div class='label label-danger'>$file_list->result</div>";
        } else {
            $result = $file_list->result;
        }

        $row = array();
        $row[] = $no;
        $row[] = "<a target='_blank' href='https://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/vendor-partner-docs/" . $file_list->file_name . "'>" . $file_list->file_name . "</a>";
        $row[] = $file_list->agent_name;
        $row[] = date('d M Y H:i:s', strtotime($file_list->upload_date));
        if ($file_list->file_source == 'partner_file_upload') {
            if (!empty($file_list->revert_file_name)) {
                $row[] = '<button type="button" onclick="view_revert_file(' . $file_list->id . ')" class="btn btn-xs btn-warning" data-toggle="modal" data-target="#revert_file_model">View Revert File</button>';
            } else {
                $row[] = '';
            }
        }
        if (isset($file_list->show_amt_paid) && $file_list->show_amt_paid) {
            $row[] = $file_list->amount_paid;
        }
        $row[] = $result;

        return $row;
    }

    /**
     * @desc : Method is used to send otp for booking cancellation & booking reschedule.
     * @author Ankit Rajvanshi
     */
    function send_otp_customer() {
        $post_data = $this->input->post();
        $booking_id = $post_data['booking_id'];
        $tag = $post_data['sms_template'];
        $sms = [];
        
        // get booking contact number.
        $booking_deatils = $this->booking_model->get_booking_details('booking_primary_contact_no, user_id', ['booking_id' => $booking_id])[0];
        $booking_primary_contact_number = $booking_deatils['booking_primary_contact_no'];
        $user_id = $booking_deatils['user_id'];
        // prepare data for sms template.
        $otp = rand(1000,9999);
        $this->session->unset_userdata('cancel_booking_otp');
        $this->session->set_userdata('cancel_booking_otp', $otp);
        $sms['tag'] = $tag;
        $sms['phone_no'] = $booking_primary_contact_number;
        $sms['booking_id'] = $booking_id;
        $sms['type'] = "user";
        $sms['type_id'] = $user_id;
        $sms['smsData']['otp'] = $otp;
        $this->notify->send_sms_msg91($sms);
        echo $this->session->userdata('cancel_booking_otp');
    }   
    
    /**
     * @desc : Method is used to reverse defective/ok parts which are acknowledged by warehouse  
     * @author : Ankit Rajvanshi
     */
    function reverse_acknowledged_from_sf() {

        $post_data = $this->input->post();
        $spare_id = $post_data['spare_id'];

        /* get spare part details of $spare_id */
        $spare_part_detail = $this->reusable_model->get_search_result_data('spare_parts_details', '*', ['id' => $spare_id], NULL, NULL, NULL, NULL, NULL)[0];
        
        /*Initialize variables*/
        $is_spare_consumed = $this->reusable_model->get_search_result_data('spare_consumption_status', '*', ['id' => $spare_part_detail['consumed_part_status_id']], NULL, NULL, NULL, NULL, NULL)[0]['is_consumed'];
        $booking_id = $spare_part_detail['booking_id'];
        

        // fetch record from booking details of $booking_id.
        $booking_details = $this->booking_model->get_booking_details('*',['booking_id' => $booking_id])[0];
        $partner_id = $booking_details['partner_id'];
        
        /**
         * Update spare parts.
         */
        $spare_data = array();
        $spare_data['old_status'] = $spare_part_detail['status'];
        
        // If part consumed status should defective part otherwise ok part.
        if(!empty($is_spare_consumed) && $is_spare_consumed == 1) {
            $action = $spare_data['status'] = DEFECTIVE_PARTS_SHIPPED;
        } else {
            $action = $spare_data['status'] = OK_PARTS_SHIPPED;
        }                    

        $spare_data['defective_part_received_by_wh'] = 0;
        $spare_data['remarks_defective_part_by_wh'] = NULL;
        $spare_data['defective_part_received_date_by_wh'] = NULL;
        $spare_data['received_defective_part_pic_by_wh'] = NULL;
                
        $this->service_centers_model->update_spare_parts(array('id' => $spare_id), $spare_data);
        
        /**
         * Log this in spare tracking.
         */
        $tracking_details = array('spare_id' => $spare_id, 'action' => $action, 'remarks' => 'Wrongly Acknowledged');
        if(!empty($this->session->userdata('warehouse_id'))) {
            $tracking_details['agent_id'] = $this->session->userdata('id');
            $tracking_details['entity_id'] = _247AROUND;
            $tracking_details['entity_type'] = _247AROUND_EMPLOYEE_STRING;
        } else { 
            $tracking_details['agent_id'] = $this->session->userdata('service_center_agent_id');
            $tracking_details['entity_id'] = $this->session->userdata('service_center_id');
            $tracking_details['entity_type'] = _247AROUND_SF_STRING;
        }

        $this->service_centers_model->insert_spare_tracking_details($tracking_details);
        
        /**
         * Log this change in booking state change & update booking internal status.
         */
        $actor = ACTOR_NOT_DEFINE;
        $next_action = NEXT_ACTION_NOT_DEFINE;
                    
        $is_exist = $this->partner_model->get_spare_parts_by_any("spare_parts_details.id, spare_parts_details.status", array('spare_parts_details.booking_id' => $booking_id, "status IN  ('".OK_PART_TO_BE_SHIPPED."', '".DEFECTIVE_PARTS_PENDING."') " => NULL));

        if (empty($is_exist)) {
            $booking_internal_status = $action;
        } else {
            $booking_internal_status = $is_exist[0]['status'];
        }

        // Change booking internal status if booking is completed.
        if($booking_details['current_status'] == _247AROUND_COMPLETED) {
            $booking = [];
            $booking['internal_status'] = $booking_internal_status;
            $partner_status = $this->booking_utilities->get_partner_status_mapping_data(_247AROUND_COMPLETED, $booking['internal_status'], $partner_id, $booking_id);
            if (!empty($partner_status)) {
                $booking['partner_current_status'] = $partner_status[0];
                $booking['partner_internal_status'] = $partner_status[1];
                $actor = $booking['actor'] = $partner_status[2];
                $next_action = $booking['next_action'] = $partner_status[3];
            }
            $this->booking_model->update_booking($booking_id, $booking);
        }

        if(!empty($this->session->userdata('warehouse_id'))) {
            $this->notify->insert_state_change($booking_id, $action, $spare_part_detail['status'], 'Wrongly Acknowledged', $this->session->userdata('id'), $this->session->userdata('employee_id'), $actor, $next_action, _247AROUND, NULL, $spare_id);
        } else {
            $this->notify->insert_state_change($booking_id, $action, $spare_part_detail['status'], 'Wrongly Acknowledged', $this->session->userdata('service_center_agent_id'), $this->session->userdata('service_center_name'), $actor, $next_action, NULL, $this->session->userdata('service_center_id'), $spare_id);
        }
        
        return true;
    }
    
    function cancel_wh_challan() {
        $post_data = $this->input->post();
        $spare_id = $post_data['spare_id'];

        $this->service_centers_model->update_spare_parts(array('id' => $spare_id), ['wh_challan_file' => NULL, 'wh_challan_number' => NULL]);
        
        return true;
    }
    
    
    /**
     * @Desc: This function is used removed partner challan
     * @params: void
     * @return: true
     * 
     */
    
    function cancel_partner_challan() {
        $post_data = $this->input->post();
        $spare_id = $post_data['spare_id'];
        $this->service_centers_model->update_spare_parts(array('id' => $spare_id), ['partner_challan_number' => NULL, 'partner_challan_file' => NULL]);
        return true;
    }
    /**
     * @Desc: This function is used to check if part already acknowledged
     * @params: void
     * @return: true
     *
     */
    function check_part_alredy_acknowledge(){
        $array['status'] = '';
        $array['message'] = '';
        if(!empty($this->input->post('spare_ids_to_check'))){
            $post_data = $this->input->post();
            $spare_id_array = $post_data['spare_ids_to_check'];
            $spare_id_list = implode(',',$spare_id_array);
            $spare_part_detail = $this->reusable_model->get_search_result_data('spare_parts_details', 'id,defective_part_received_date_by_wh',array("id in ($spare_id_list)" => null, 'defective_part_received_date_by_wh is not null' => null), NULL, NULL, NULL, NULL, NULL);
            if(!empty($spare_part_detail)){
                $array['status'] = 'error';
                $array['message'] = 'Some parts already acknowledged, Please refresh page to continue.';
            }
        }
        echo json_encode($array);
    }
    
    /**
     @desc: This method loads the iframe to add booking form for WalkIns / SFs
     * @param $sf_id Vendor Id
     * @return View of add booking form
     * @author: Prity Sharma
     * @created_on 21-01-2021 
     */
    function add_booking_walkin($vendor_id) {
        $this->checkUserSession();
        $data['vendor_id'] = $vendor_id;
        $this->load->view('service_centers/header');
        $this->load->view('service_centers/add_booking_walkin', $data);
    }
    
    /**
    * This function is used to auto-approve engineer completed bookings
    * This function is called from CRON
    * @author Prity Sharma
    * @create_date 12-02-2021
    */
   function auto_approve_engineer_bookings() {

        $this->db->_protect_identifiers = FALSE;

        // Fetch all Bookings that are completed by Engineer        
        $where['where'] = array(
            "engineer_booking_action.current_status" => "InProcess",
            "DATEDIFF(CURDATE(), engineer_booking_action.closed_date) > 2" => NULL,
            "engineer_booking_action.internal_status IN ('" . _247AROUND_CANCELLED . "', '" . _247AROUND_COMPLETED . "')" => NULL,
            "booking_details.current_status IN ('" . _247AROUND_PENDING . "', '" . _247AROUND_RESCHEDULED . "')" => NULL
        );

        $select = " booking_details.booking_id";
        $engg_booking_ids = $this->engineer_model->get_engineer_action_table_list($where, $select);
        if (empty($engg_booking_ids)) {
            return;
        }

        foreach ($engg_booking_ids as $engg_booking_id) {
            $where['where']['booking_details.booking_id'] = $engg_booking_id->booking_id;
            $select = 'engineer_booking_action.*,'
                    . 'engineer_table_sign.upcountry_charges,'
                    . 'engineer_table_sign.cancellation_reason,'
                    . 'engineer_table_sign.mismatch_pincode,'
                    . 'DATEDIFF(CURDATE(), engineer_booking_action.closed_date) as days_diff,'
                    . ' booking_details.partner_id';
            $engg_bookings = $this->engineer_model->get_engineer_action_table_list($where, $select);

            // Initially set Booking status as InProces_Cancelled, if any 1 line item is Found as Completed, Mark booking status as InProcess_Completed 
            $sf_booking_status = SF_BOOKING_CANCELLED_STATUS;
            $booking_status = _247AROUND_CANCELLED;
            foreach ($engg_bookings as $engg_completed_booking) {
                $booking_id = $engg_completed_booking->booking_id;
                $partner_id = $engg_completed_booking->partner_id;
                $closed_date = $engg_completed_booking->closed_date;
                $internal_status_engg = $engg_completed_booking->internal_status;
                if ($engg_completed_booking->internal_status == _247AROUND_COMPLETED) {
                    $sf_booking_status = SF_BOOKING_COMPLETE_STATUS;
                    $booking_status = _247AROUND_COMPLETED;
                }

                // Update Model , Serial & DOP details in booking_unit_details
                $ud_data = [
                    'sf_model_number' => $engg_completed_booking->model_number,
                    'serial_number' => $engg_completed_booking->serial_number,
                    'serial_number_pic' => $engg_completed_booking->serial_number_pic,
                    'sf_purchase_date' => $engg_completed_booking->sf_purchase_date,
                ];
                $where_ud = [
                    'booking_id' => $engg_completed_booking->booking_id
                ];
                $this->booking_model->update_booking_unit_details_by_any($where_ud, $ud_data);

                // Update Charges in service_center_booking_action
                $ssba_data = [
                    'service_charge' => $engg_completed_booking->service_charge,
                    'additional_service_charge' => $engg_completed_booking->additional_service_charge,
                    'parts_cost' => $engg_completed_booking->parts_cost,
                    'upcountry_charges' => $engg_completed_booking->upcountry_charges,
                    'serial_number' => $engg_completed_booking->serial_number,
                    'model_number' => $engg_completed_booking->model_number,
                    'amount_paid' => $engg_completed_booking->amount_paid,
                    'service_center_remarks' => $engg_completed_booking->closing_remark,
                    'cancellation_reason' => $engg_completed_booking->cancellation_reason,
                    'current_status' => SF_BOOKING_INPROCESS_STATUS,
                    'internal_status' => $engg_completed_booking->internal_status,
                    'mismatch_pincode' => $engg_completed_booking->mismatch_pincode,
                    'closed_date' => $closed_date,
                    'serial_number_pic' => $engg_completed_booking->serial_number_pic,
                    'is_broken' => $engg_completed_booking->is_broken,
                    'sf_purchase_date' => $engg_completed_booking->sf_purchase_date,
                    'sf_purchase_invoice' => $engg_completed_booking->purchase_invoice,
                    'technical_solution' => $engg_completed_booking->solution,
                    'technical_problem' => $engg_completed_booking->defect,
                    'unit_details_id' => $engg_completed_booking->unit_details_id
                ];
                $this->vendor_model->update_service_center_action($booking_id, $ssba_data);

                // Update Statuses in engineer_booking_action
                $this->engineer_model->update_engineer_table(array("current_status" => $engg_completed_booking->internal_status, "internal_status" => $engg_completed_booking->internal_status), ['id' => $engg_completed_booking->id]);
            }
            // Update Booking Statuses
            $this->update_booking_internal_status($booking_id, $sf_booking_status, $partner_id);

            // Update SF Closed date
            $this->booking_model->update_booking($booking_id, ['service_center_closed_date' => $closed_date]);

            // Insert data into booking state change
            $this->insert_details_in_state_change($booking_id, $sf_booking_status, "Booking Auto Approved", "247Around", "Review the Booking", NULL, true);

            //Update spare consumption as entered by engineer Booking Completed
            if ($booking_status == _247AROUND_COMPLETED) {
                $update_consumption = false;
                $spare_Consumption_details = $this->service_centers_model->get_engineer_consumed_details('*', array('booking_id' => $booking_id), array('coloum' => 'engineer_consumed_spare_details.id', 'order' => 'ASC'));
                if (!empty($spare_Consumption_details)) {
                    $array_consumption = array();
                    foreach ($spare_Consumption_details as $key => $value) {
                        $spare_select = 'spare_parts_details.*';
                        $spare_details = $this->partner_model->get_spare_parts_by_any($spare_select, array('spare_parts_details.id' => $value['spare_id'], 'status !=' => _247AROUND_CANCELLED));
                        if (!empty($spare_details)) {
                            if (($spare_details[0]['consumed_part_status_id'] == OK_PART_BUT_NOT_USED_CONSUMPTION_STATUS_ID || empty($spare_details[0]['consumed_part_status_id'])) && empty($spare_details[0]['defective_part_shipped_date'])) {
                                $array_consumption['spare_consumption_status'][$value['spare_id']] = $value['consumed_part_status_id'];
                                $array_consumption['consumption_remarks'][$value['spare_id']] = $value['remarks'];
                                $update_consumption = true;
                            }
                        }
                    }
                    if (!empty($update_consumption)) {
                        $is_update_spare_parts = $this->miscelleneous->update_spare_consumption_status($array_consumption, $booking_id);
                    }
                }
            }
        }
    }

    function bb_otp_list($cp_id = '') {

        if ($this->session->userdata('service_center_id')) {
            $this->check_BB_UserSession();
            $cp_id = $this->session->userdata('service_center_id');
        } else {
            
        }
        $data = array();
        $post['where']['cp_id'] = $cp_id;
        $post['order']['column'] = 'id';
        $post['order']['order_by'] = 'desc';
        $post['length'] = -1;
        $post['start'] = 0;
        $otp_detail = $this->bb_model->fetch_buyback_otp($post);
        if (!empty($otp_detail)) {
            $data['otp_detail'] = $otp_detail;
        }
        $this->load->view('service_centers/header');
        $this->load->view('service_centers/bb_otp_list', $data);
    }

    /**
     * This function is used to get part wise warranty data
     * @param : $data (Array of Booking's data => Partner Id, Purchase Date)
     * @param : part_types (Array of Parts Requested)
     * @author : Prity Sharma
     * @created_on 24-03-2021
     */
    function get_part_warranty_data($data)
    {
        $part_types = array_column($data['part'], 'parts_type');
        if(empty($part_types)){
            return;
        }
        // create booking wise Array
        $data['warranty'][$data['booking_id']]['purchase_date'] = $data['purchase_date'];
        $data['warranty'][$data['booking_id']]['model_number'] = $data['model_number'];
        $data['warranty'][$data['booking_id']]['partner_id'] = $data['partner_id'];
        $data['warranty'][$data['booking_id']]['part'] = $part_types;
        $arrBookingsWarrantyStatus = $this->warranty_utilities->get_warranty_status_of_parts($data, $part_types);
        
    }
}
