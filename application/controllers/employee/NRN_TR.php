<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

//error_reporting(E_ERROR);
//ini_set('display_errors', '1');


class NRN_TR extends CI_Controller {

    function __Construct() {
        parent::__Construct();

        $this->load->model('partner_model');
        $this->load->model('user_model');
        $this->load->model('booking_model');
        $this->load->model('vendor_model');
        $this->load->model('dealer_model');
        $this->load->model('reusable_model');
        $this->load->model('service_centers_model');
        $this->load->library("miscelleneous");
        $this->load->library('email');
        $this->load->library('notify');
        $this->load->library('partner_utilities');
        $this->load->model('upcountry_model');
        $this->load->library("asynchronous_lib");
        $this->load->library('booking_utilities');
        $this->load->library('initialized_variable');
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('PHPReport');
        $this->load->library('push_notification_lib');
        $this->load->model('service_centre_charges_model');
        $this->load->model('nrn_model');
        // $this->load->library('push_inbuilt_function_lib');
    }

    /*
     * List all NRN records for AKAI partner
     */

    function list_nrn_records() {
        $data = array();
        $this->load->model('nrn_model');
        $all_nrn_records = $this->nrn_model->get_all_nrn_records();
        $data['records'] = ($all_nrn_records !== NULL) ? $all_nrn_records : array();
        $this->miscelleneous->load_partner_nav_header();
        $this->load->view('partner/list_nrn_records', $data);
        $this->load->view('partner/partner_footer');
    }

    /*
     * Add NRN deatails by Partner
     */

    function add_nrn_details() {
        $data = array();

        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $this->form_validation->set_rules('nrn_details', 'nrn_details', 'callback_insert_nrn_details[]');
            if ($this->form_validation->run() !== FALSE) {
                $this->session->set_flashdata('success', 'NRN details added succesfully.');
                redirect('partner/list_nrn_records');
            }
        }
        $data['title'] = 'Add NRN details form';

        $partner_details = $this->session->userdata;
        if (empty($partner_details['partner_id']) || $partner_details['partner_id'] == '') {
            $this->session->set_flashdata('error', 'Invalid partner');
            redirect('partner/list_nrn_records');
        }
        //$data['service_id'] = 46;
        $data['partner_id'] = $partner_details['partner_id'];
        $data['brand'] = $partner_details['partner_name'];
        $data['partner_type'] = 'OEM';

        
        //$result = $this->nrn_model->get_category_capacity_model($service_id, $partner_id);

        
        //foreach ($result as $category) {
//            $model = array('id'=>$category['id'],'model'=>$category['model'],'model_number'=>$category['model_number']);
//            $data['products'][$category['category']][$category['capacity']]['models'][] = $model;
//            
//        }


        $data['crm_name'] = array('' => 'Select CRM', '247' => '247', 'AKAI' => 'AKAI');
        $data['owners'] = array('' => 'Select Owner', 'Customer' => 'Customer', 'Sub-Dealer' => 'Sub-Dealer', 'Dealer' => 'Dealer');
        $data['physical_status'] = array('' => 'Select Physical Status', 'Defective' => 'Defective', 'DOA' => 'DOA', 'Damage' => 'Damage');
        $data['make'] = array('' => 'Select Make', 'MEPL' => 'MEPL', 'VEIRA' => 'VEIRA', 'CHANGHONG' => 'CHANGHONG', 'HISENS' => 'HISENS', 'JPE' => 'JPE', 'KTC' => 'KTC', 'CHIGO' => 'CHIGO', 'TCL' => 'TCL', 'AMBER' => 'AMBER', 'E-VISION' => 'E-VISION', 'DIXON' => 'DIXON', 'VIMALPLAST' => 'VIMALPLAST', 'SUN INDUSTRIES' => 'SUN INDUSTRIES');
        $data['approval_status'] = array('' => 'Select Approval Status', 'Approved' => 'Approved', 'Rejected' => 'Rejected', 'Special Approval' => 'Special Approval');
        $data['warranty_status'] = array('' => 'Select Warranty Status', 'IW' => 'IW', 'OW' => 'OW');
        $data['service_partner'] = array('' => 'Select Service Partner', 'AKAI' => 'AKAI', '247' => '247');
        $data['action_plan'] = array('' => 'Select Action Plan', 'Customer + Sub-dealer' => 'Customer + Sub-dealer', 'Distributor' => 'Distributor');
        $data['replacement_status'] = array('' => 'Select Replacement Status', 'Dispatched' => 'Dispatched', 'Pending' => 'Pending', 'NA' => 'NA');
        $data['replacement_with_accessory'] = array('' => 'Select Replacement Accessory', 'Yes' => 'Yes', 'No' => 'No', 'NA' => 'NA');
        $data['defective_pickup_status'] = array('' => 'Select Status', 'Pending due to address' => 'Pending due to address', 'ODA Location' => 'ODA Location', 'Pickup Aligned' => 'Pickup Aligned');
        $data['tr_status'] = array('' => 'Select Status', 'Open' => 'Open', 'Close' => 'Close');
        $data['replacement_action_plan'] = array('' => 'Select Action Plan', 'Yes' => 'Yes', 'No' => 'No');
        $data['tr_physical_receiving_status'] = array('' => 'Select Status', 'Yes' => 'Yes', 'No' => 'No');
        $data['gap_received'] = array('' => 'Select Status', 'Done' => 'Done', 'Pending' => 'Pending');
        $data['fca_category_pdi1'] = array('' => 'Select Category', 'FG' => 'FG', 'D1' => 'D1', 'D2' => 'D2', 'D3' => 'D3', 'D4' => 'D4');
        $data['fca_category_pdi2'] = array('' => 'Select Category', 'FG' => 'FG', 'D1' => 'D1', 'D2' => 'D2', 'D3' => 'D3', 'D4' => 'D4');
        $data['vendor_reversal_category'] = array('' => 'Select Category', 'FG' => 'FG', 'D1' => 'D1', 'D2' => 'D2', 'D3' => 'D3', 'D4' => 'D4');
        $data['final_defective_status'] = array('' => 'Select Status', 'Dispatched to Vendor' => 'Dispatched to Vendor', 'Cannabalised' => 'Cannabalised', 'Liquidate' => 'Liquidate');
        $data['vendor_reversal_status'] = array('' => 'Select Status', 'Received' => 'Received', 'Pending' => 'Pending');
        // add template and view files
        $this->miscelleneous->load_partner_nav_header();
        $this->load->view('partner/add_nrn_details', $data);
        $this->load->view('partner/partner_footer');
    }

    /*
     * Insert new NRN entry for AKAI
     */

    function insert_nrn_details() {
        $nrn_details = $this->input->post();
        $nrn_details['physical_status_remark_date'] = date("Y-m-d", strtotime(str_replace('/', '-', $nrn_details['physical_status_remark_date'])));
        $nrn_details['tr_reporting_date'] = date("Y-m-d", strtotime(str_replace('/', '-', $nrn_details['tr_reporting_date'])));
        $nrn_details['booking_date'] = date("Y-m-d", strtotime(str_replace('/', '-', $nrn_details['booking_date'])));
        $nrn_details['purchase_date'] = date("Y-m-d", strtotime(str_replace('/', '-', $nrn_details['purchase_date'])));
        $nrn_details['approval_rejection_date'] = date("Y-m-d", strtotime(str_replace('/', '-', $nrn_details['approval_rejection_date'])));
        $nrn_details['defective_receiving_date'] = date("Y-m-d", strtotime(str_replace('/', '-', $nrn_details['defective_receiving_date'])));
        $nrn_details['replacement_dispatch_date'] = date("Y-m-d", strtotime(str_replace('/', '-', $nrn_details['replacement_dispatch_date'])));
        $nrn_details['replacement_delivery_date'] = date("Y-m-d", strtotime(str_replace('/', '-', $nrn_details['replacement_delivery_date'])));
        $nrn_details['category_after_inspection_date'] = date("Y-m-d", strtotime(str_replace('/', '-', $nrn_details['category_after_inspection_date'])));
        $nrn_details['final_pdi_category_after_inspection_date'] = date("Y-m-d", strtotime(str_replace('/', '-', $nrn_details['final_pdi_category_after_inspection_date'])));
        $nrn_details['vendor_warranty_expire_month'] = date("Y-m-t", strtotime(str_replace('/', '-', '1/' . $nrn_details['vendor_warranty_expire_month'])));
        $nrn_details['final_defective_status_date'] = date("Y-m-d", strtotime(str_replace('/', '-', $nrn_details['final_defective_status_date'])));
        $nrn_details['vendor_reversal_date'] = date("Y-m-d", strtotime(str_replace('/', '-', $nrn_details['vendor_reversal_date'])));

        $this->load->model('nrn_model');
        $this->nrn_model->insert_nrn_details($nrn_details);
        return TRUE;
    }

    /*
     * Edit/Update NRN record
     */

    function edit_nrn_details($nrn_id = '') {

        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $this->form_validation->set_rules('update_nrn_details', 'update_nrn_details', 'callback_update_nrn_details[nrn_id]');
            if ($this->form_validation->run() !== FALSE) {
                $this->session->set_flashdata('success', 'NRN details updated succesfully.');
                redirect('partner/list_nrn_records');
            }
        }


        if ($nrn_id != '') {
            $nrn_record = array();
            $this->load->model('nrn_model');
            $nrn_record = $this->nrn_model->get_nrn_records($nrn_id);
            $data['nrn_record'] = ($nrn_record !== NULL) ? $nrn_record[0] : array();
            $data['crm_name'] = array('' => 'Select CRM', '247' => '247', 'AKAI' => 'AKAI');
            $data['products'] = array('' => 'Select Product', 'LED' => 'LED', 'AC' => 'AC', 'Washing Machine' => 'Washing Machine');
            $data['owners'] = array('' => 'Select Owner', 'Customer' => 'Customer', 'Sub-Dealer' => 'Sub-Dealer', 'Dealer' => 'Dealer');
            $data['physical_status'] = array('' => 'Select Physical Status', 'Defective' => 'Defective', 'DOA' => 'DOA', 'Damage' => 'Damage');
            $data['make'] = array('' => 'Select Make', 'MEPL' => 'MEPL', 'VEIRA' => 'VEIRA', 'CHANGHONG' => 'CHANGHONG', 'HISENS' => 'HISENS', 'JPE' => 'JPE', 'KTC' => 'KTC', 'CHIGO' => 'CHIGO', 'TCL' => 'TCL', 'AMBER' => 'AMBER', 'E-VISION' => 'E-VISION', 'DIXON' => 'DIXON', 'VIMALPLAST' => 'VIMALPLAST', 'SUN INDUSTRIES' => 'SUN INDUSTRIES');
            $data['approval_status'] = array('' => 'Select Approval Status', 'Approved' => 'Approved', 'Rejected' => 'Rejected', 'Special Approval' => 'Special Approval');
            $data['warranty_status'] = array('' => 'Select Warranty Status', 'IW' => 'IW', 'OW' => 'OW');
            $data['service_partner'] = array('' => 'Select Service Partner', 'AKAI' => 'AKAI', '247' => '247');
            $data['action_plan'] = array('' => 'Select Action Plan', 'Customer + Sub-dealer' => 'Customer + Sub-dealer', 'Distributor' => 'Distributor');
            $data['replacement_status'] = array('' => 'Select Replacement Status', 'Dispatched' => 'Dispatched', 'Pending' => 'Pending', 'NA' => 'NA');
            $data['replacement_with_accessory'] = array('' => 'Select Replacement Accessory', 'Yes' => 'Yes', 'No' => 'No', 'NA' => 'NA');
            $data['defective_pickup_status'] = array('' => 'Select Status', 'Pending due to address' => 'Pending due to address', 'ODA Location' => 'ODA Location', 'Pickup Aligned' => 'Pickup Aligned');
            $data['tr_status'] = array('' => 'Select Status', 'Open' => 'Open', 'Close' => 'Close');
            $data['replacement_action_plan'] = array('' => 'Select Action Plan', 'Yes' => 'Yes', 'No' => 'No');
            $data['tr_physical_receiving_status'] = array('' => 'Select Status', 'Yes' => 'Yes', 'No' => 'No');
            $data['gap_received'] = array('' => 'Select Status', 'Done' => 'Done', 'Pending' => 'Pending');
            $data['fca_category_pdi1'] = array('' => 'Select Category', 'FG' => 'FG', 'D1' => 'D1', 'D2' => 'D2', 'D3' => 'D3', 'D4' => 'D4');
            $data['fca_category_pdi2'] = array('' => 'Select Category', 'FG' => 'FG', 'D1' => 'D1', 'D2' => 'D2', 'D3' => 'D3', 'D4' => 'D4');
            $data['vendor_reversal_category'] = array('' => 'Select Category', 'FG' => 'FG', 'D1' => 'D1', 'D2' => 'D2', 'D3' => 'D3', 'D4' => 'D4');
            $data['final_defective_status'] = array('' => 'Select Status', 'Dispatched to Vendor' => 'Dispatched to Vendor', 'Cannabalised' => 'Cannabalised', 'Liquidate' => 'Liquidate');
            $data['vendor_reversal_status'] = array('' => 'Select Status', 'Received' => 'Received', 'Pending' => 'Pending');

            $this->miscelleneous->load_partner_nav_header();
            $this->load->view('partner/edit_nrn_details', $data);
            $this->load->view('partner/partner_footer');
        }
    }

    /*
     * Update NRN entry for AKAI
     */

    function update_nrn_details() {
        $nrn_details = $this->input->post();
        $nrn_details['physical_status_remark_date'] = date("Y-m-d", strtotime(str_replace('/', '-', $nrn_details['physical_status_remark_date'])));
        $nrn_details['tr_reporting_date'] = date("Y-m-d", strtotime(str_replace('/', '-', $nrn_details['tr_reporting_date'])));
        $nrn_details['booking_date'] = date("Y-m-d", strtotime(str_replace('/', '-', $nrn_details['booking_date'])));
        $nrn_details['purchase_date'] = date("Y-m-d", strtotime(str_replace('/', '-', $nrn_details['purchase_date'])));
        $nrn_details['approval_rejection_date'] = date("Y-m-d", strtotime(str_replace('/', '-', $nrn_details['approval_rejection_date'])));
        $nrn_details['defective_receiving_date'] = date("Y-m-d", strtotime(str_replace('/', '-', $nrn_details['defective_receiving_date'])));
        $nrn_details['replacement_dispatch_date'] = date("Y-m-d", strtotime(str_replace('/', '-', $nrn_details['replacement_dispatch_date'])));
        $nrn_details['replacement_delivery_date'] = date("Y-m-d", strtotime(str_replace('/', '-', $nrn_details['replacement_delivery_date'])));
        $nrn_details['category_after_inspection_date'] = date("Y-m-d", strtotime(str_replace('/', '-', $nrn_details['category_after_inspection_date'])));
        $nrn_details['final_pdi_category_after_inspection_date'] = date("Y-m-d", strtotime(str_replace('/', '-', $nrn_details['final_pdi_category_after_inspection_date'])));
        $nrn_details['vendor_warranty_expire_month'] = date("Y-m-t", strtotime(str_replace('/', '-', '1/' . $nrn_details['vendor_warranty_expire_month'])));
        $nrn_details['final_defective_status_date'] = date("Y-m-d", strtotime(str_replace('/', '-', $nrn_details['final_defective_status_date'])));
        $nrn_details['vendor_reversal_date'] = date("Y-m-d", strtotime(str_replace('/', '-', $nrn_details['vendor_reversal_date'])));
        $nrn_id = $nrn_details['nrn_id'];

        $this->load->model('nrn_model');
        $this->nrn_model->update_nrn_details($nrn_details, $nrn_id);
        return TRUE;
    }

    /*
     * Get booking deatils by booking ID 
     * return JSON data on AJAX request
     */

    function finduser() {
        if ($this->input->is_ajax_request()) {

            $booking_id = preg_replace('/[^A-Za-z0-9\-]/', '', trim($this->input->get('search_value')));
            $post['length'] = -1;
            $select = "services.services, service_centres.name as service_centre_name,
            service_centres.primary_contact_phone_1, service_centres.primary_contact_name,
            users.phone_number, users.name as customername,booking_details.type,
            users.phone_number, booking_details.*,penalty_on_booking.active as penalty_active, users.user_id";
            if (!empty($booking_id)) {
                $post['search_value'] = $booking_id;
                $post['column_search'] = array('booking_details.booking_id');
                $post['order'] = array(array('column' => 0, 'dir' => 'asc'));
                $post['order_performed_on_count'] = TRUE;
                $post['column_order'] = array('booking_details.booking_id');
                $post['unit_not_required'] = true;
            }
            $data['Bookings'] = $this->booking_model->get_bookings_by_status($post, $select);
            $data['booking_status'] = $this->booking_model->get_booking_cancel_complete_status_from_scba($booking_id);

            if (!empty($data['Bookings'])) {
                echo json_encode($data, true);
            }
        }
    }

    /*
     * Get appliance category 
     * return HTML data on AJAX request
     */

    function getCategoryForService() {

        $service_id = $this->input->post('service_id');
        $brand = $this->input->post('brand');
        $partner_type = $this->input->post('partner_type');

        $partner_id = $this->input->post('partner_id');
        if ($partner_type == OEM) {
            $result = $this->booking_model->getCategoryForService($service_id, $partner_id, $brand);
        } else {
            $isWbrand = "";
            $whiteListBrand = $this->partner_model->get_partner_blocklist_brand(array("partner_id" => $partner_id, "brand" => $brand,
                "service_id" => $service_id, "whitelist" => 1), "*");
            if (!empty($whiteListBrand)) {
                $whiteListBrand = $brand;
            }
            $result = $this->booking_model->getCategoryForService($service_id, $partner_id, $isWbrand);
        }

        echo "<option selected disabled>Select Appliance Category</option>";
        foreach ($result as $category) {
            echo "<option>$category[category]</option>";
        }
    }

    /*
     * Get appliance capacity by category
     * return HTML data on AJAX request
     */

    function getCapacityForCategory() {
        $service_id = $this->input->post('service_id');
        $category = $this->input->post('category');
        $brand = $this->input->post('brand');
        $partner_id = $this->input->post('partner_id');
        $partner_type = $this->input->post('partner_type');


        if ($partner_type == OEM) {
            $result = $this->booking_model->getCapacityForCategory($service_id, $category, $brand, $partner_id);
        } else {
            $isWbrand = "";
            $whiteListBrand = $this->partner_model->get_partner_blocklist_brand(array("partner_id" => $partner_id, "brand" => $brand,
                "service_id" => $service_id, "whitelist" => 1), "*");
            if (!empty($whiteListBrand)) {
                $isWbrand = $brand;
            }
            $result = $this->booking_model->getCapacityForCategory($service_id, $category, $isWbrand, $partner_id);
        }

        foreach ($result as $capacity) {
            echo "<option>$capacity[capacity]</option>";
        }
    }

    /*
     * Get appliance models by category and capacity
     * return HTML data on AJAX request
     */

    function getModelForService() {
        $service_id = $this->input->post('service_id');
        $category = $this->input->post('category');
        $brand = $this->input->post('brand');
        $partner_id = $this->input->post('partner_id');
        $capacity = $this->input->post('capacity');
        $partner_type = $this->input->post('partner_type');

        $where = array('partner_appliance_details.service_id' => $service_id,
            'partner_appliance_details.partner_id' => $partner_id,
            'partner_appliance_details.category' => $category,
            'partner_appliance_details.active' => 1,
            'appliance_model_details.active' => 1,
        );

        if (!empty($capacity)) {
            $where['partner_appliance_details.capacity'] = $capacity;
        }

        if ($partner_type == OEM) {
            $where['partner_appliance_details.brand'] = $brand;
            $result = $this->partner_model->get_model_number('appliance_model_details.id, appliance_model_details.model_number, model', $where);
        } else {
            $result = $this->partner_model->get_model_number('appliance_model_details.id, appliance_model_details.model_number, model', $where);
        }

        if (!empty($result)) {
            $flag = false;
            $option = "<option selected disabled>Select Model Number</option>";
            foreach ($result as $value) {
                if (!empty(trim($value['model']))) {
                    $flag = true;
                    $option .= "<option value='" . $value['model_number'] . "'>" . $value['model_number'] . "</option>";
                }
            }
            if ($flag) {
                $res['status'] = TRUE;
                $res['msg'] = $option;
            } else {
                $res['status'] = FALSE;
                $res['msg'] = 'no data found';
            }
        } else {
            $res['status'] = FALSE;
            $res['msg'] = 'no data found';
        }
        echo $res;
    }

    /*
     * Get appliance types AKAI Brand
     * return HTML data on AJAX request
     */

    function get_appliances($selected_service_id = NULL) {
        $partner_id = $this->input->post('partner_id');
        $partner_details = $this->partner_model->getpartner_details("partners.id, public_name, "
                . "postpaid_credit_period, is_active, postpaid_notification_limit, postpaid_grace_period, is_prepaid,partner_type, "
                . "invoice_email_to,invoice_email_cc", array('partners.id' => $partner_id));

        $prepaid['active'] = true;
        $prepaid['is_notification'] = false;

        $data = array();

        if (!empty($partner_details)) {
            if ($partner_details[0]['is_prepaid'] == 1) {
                $prepaid = $this->miscelleneous->get_partner_prepaid_amount($partner_id);
            } else if ($partner_details[0]['is_prepaid'] == 0) {

                $prepaid = $this->invoice_lib->get_postpaid_partner_outstanding($partner_details[0]);
            }

            if ($partner_details[0]['partner_type'] == OEM) {
                $services = $this->partner_model->get_partner_specific_services($partner_id);
            } else {
                $services = $this->booking_model->selectservice();
            }

            $data['partner_type'] = $partner_details[0]['partner_type'];
            $data['partner_id'] = $partner_id;
            $data['active'] = $prepaid['active'];
            if ($prepaid['is_notification']) {
                $data['prepaid_msg'] = PREPAID_LOW_AMOUNT_MSG_FOR_ADMIN;
            } else {
                $data['prepaid_msg'] = "";
            }
            $data['services'] = "<option selected disabled value=''>Select Product</option>";
            foreach ($services as $appliance) {
                $data['services'] .= "<option ";
                if ($selected_service_id == $appliance->id) {
                    $data['services'] .= " selected ";
                } else if (count($services) == 1) {
                    $data['services'] .= " selected ";
                }
                $data['services'] .= " value='" . $appliance->id . "'>$appliance->services</option>";
            }
        }

        echo $data['services'];
    }

}
