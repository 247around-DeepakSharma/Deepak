<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Warranty extends CI_Controller {

    /**
     * load list modal and helpers
     */
    function __Construct() {

        parent::__Construct();
        $this->load->model('warranty_model');
        $this->load->library("session");
        $this->load->library('miscelleneous');
        $this->load->library('warranty_utilities');
        if ($this->session->userdata('loggedIn') == TRUE) {
            return TRUE;
        } else {
            redirect(base_url() . "employee/login");
        }
    }

    /**
     *  @desc : This function will load warranty plans
     *  @param: void
     *  @return : print warranty on warranty Page
     */
    public function index($partner_id = null, $service_id = null, $brand = null) {
        $partners = $this->partner_model->getpartner();
        foreach ($partners as $partnersDetails) {
            $partnerArray[$partnersDetails['id']] = $partnersDetails['public_name'];
        }

        $this->miscelleneous->load_nav_header();
        $this->load->view('warranty/check_warranty', ['partnerArray' => $partnerArray, 'partner_id' => $partner_id, 'service_id' => $service_id, 'brand' => $brand]);
    }

    public function get_warranty_list_data() {
        $post = $this->get_post_data();
        $post_data = $this->input->post();
        $list = $this->warranty_model->check_warranty($post_data);
        $data = array();
        $no = $post['start'];
        $date_period_start = $post_data['purchase_date'];
        $create_date = $post_data['create_date'];
        $InWarrantyTimePeriod = 12; // Make it 0 if want to take in-warranty data from table.
        $InWarrantyGracePeriod = 0;
        $activeInWarrantyPlans = 0;
        $activeExtendedWarrantyPlans = 0;
        
        // get In-Warrenty Period        
        if(!empty($list[0]['warranty_type']) && ($list[0]['warranty_type'] == 1))
        {
            $InWarrantyTimePeriod = !empty($list[0]['warranty_period']) ? $list[0]['warranty_period'] : 0;
            $InWarrantyGracePeriod = !empty($list[0]['warranty_grace_period']) ? $list[0]['warranty_grace_period'] : 0;
        }
        else
        {
            $no++;
            $row =  $this->in_warranty_data($no, $post_data['purchase_date'], $activeInWarrantyPlans, $create_date);
            $data[] = $row;
        }
             
        foreach ($list as $key => $value) {
            $no++;
            $row =  $this->warranty_data($list[$key], $no, $date_period_start, $InWarrantyTimePeriod, $InWarrantyGracePeriod, $activeInWarrantyPlans, $activeExtendedWarrantyPlans, $create_date);
            $data[] = $row;
        }
        
        $new_post['length'] = -1;
        $count = count($data);
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $count,
            "recordsFiltered" =>  $count,
            "data" => $data,
            "activeInWarrantyPlans" => $activeInWarrantyPlans,
            "activeExtendedWarrantyPlans" => $activeExtendedWarrantyPlans
        );
        echo json_encode($output);
    }
    
    function get_post_data(){
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $search = $this->input->post('search');
        $post['search_value'] = $search['value'];
        $post['order'] = $this->input->post('order');
        $post['draw'] = $this->input->post('draw');
        $post['status'] = $this->input->post('status');

        return $post;
    }
    
    function warranty_data($warranty_list, $no, $period_start, $InWarrantyTimePeriod, $InWarrantyGracePeriod, &$activeInWarrantyPlans,&$activeExtendedWarrantyPlans, $create_date){
        $warranty_end_period = $period_start;
        $in_warranty_end_period = $period_start;
        $warranty_period = $warranty_list['warranty_period'];
        $warranty_grace_period = $warranty_list['warranty_grace_period'];
        if($warranty_list['warranty_type'] == 2)
        {
            $warranty_period += $InWarrantyTimePeriod;
            $warranty_grace_period += $InWarrantyGracePeriod;
        }
        
        if(!empty($warranty_period)){
            $warranty_end_period = strtotime(date("Y-m-d", strtotime($warranty_end_period)) . " +".$warranty_period." months");
            $in_warranty_end_period = strtotime(date("Y-m-d", strtotime($in_warranty_end_period)) . " +".$InWarrantyTimePeriod." months");
        }
        if(!empty($warranty_grace_period)){
            $warranty_end_period = strtotime(date("Y-m-d", strtotime($warranty_end_period)) . " +".$warranty_grace_period." days");
            $in_warranty_end_period = strtotime(date("Y-m-d", strtotime($in_warranty_end_period)) . " +".$InWarrantyGracePeriod." days");
        }
        $warranty_end_period = strtotime(date("Y-m-d", $warranty_end_period) . " -1 day");
        $in_warranty_end_period = strtotime(date("Y-m-d", $in_warranty_end_period) . " -1 day");
        $date_booking_creation = strtotime(date("Y-m-d", strtotime($create_date)));
        
        $row = array();
        $row[] = $no;
        $row[] = $warranty_list['plan_name'];
        $row[] = date('d-M-Y', strtotime($warranty_list['period_start']));
        $row[] = date('d-M-Y', strtotime($warranty_list['period_end']));
        $row[] = $warranty_list['states'];
        $row[] = $warranty_list['part_types'];
        $row[] = !empty($warranty_list['inclusive_svc_charge']) ? "No" : "Yes";
        $row[] = !empty($warranty_list['inclusive_gas_charge']) ? "No" : "Yes";
        $row[] = (!empty($warranty_list['warranty_type']) && $warranty_list['warranty_type'] == 1) ? "In Warranty" : "Extended Warranty";
        $row[] = $warranty_list['warranty_period']. " Month(s)";
        $row[] = $warranty_list['warranty_grace_period']. " Day(s)";
        if($warranty_end_period < $date_booking_creation)
        {
            $row[] = date('d-M-Y', $warranty_end_period)."<p style='color: #f30;font-weight:bold;'>Expired</p>";
        }
        else
        {
            if(($warranty_list['warranty_type'] == 1) || ($in_warranty_end_period >= $date_booking_creation))
            {
                $activeInWarrantyPlans++;
            }
            else {
                $activeExtendedWarrantyPlans++;
            }
            $row[] = date('d-M-Y', $warranty_end_period)."<p style='color: green;font-weight:bold;'>Active</p>";
        }        
        return $row;        
    }
    
    function in_warranty_data($no, $purchase_date, &$activeInWarrantyPlans, $create_date){  
        $warranty_start_period = date('d-M-Y', strtotime($purchase_date));
        $warranty_end_period = date('d-m-Y', strtotime(date("Y-m-d", strtotime($purchase_date)) . " +1 year"));
        $warranty_end_period = date('d-M-Y', strtotime(date("Y-m-d", strtotime($warranty_end_period)) . " -1 day"));
        $date_booking_creation = strtotime(date("Y-m-d", strtotime($create_date)));
        $row = array();
        $row[] = $no;
        $row[] = 'In Warranty';
        $row[] = $warranty_start_period;
        $row[] = $warranty_end_period;
        $row[] = "All";
        $row[] = "All";
        $row[] = "Yes";
        $row[] = "Yes";
        $row[] = "In Warranty";
        $row[] = "12 Month(s)";
        $row[] = "0 Day(s)";
        if(strtotime($warranty_end_period) < $date_booking_creation)
        {
            $row[] = date('d-M-Y', strtotime($warranty_end_period))."<p style='color: #f30;font-weight:bold;'>Expired</p>";
        }
        else
        {
            $activeInWarrantyPlans++;
            $row[] = date('d-M-Y', strtotime($warranty_end_period))."<p style='color: green;font-weight:bold;'>Active</p>";
        }        
        return $row;        
    }
    
    public function get_warranty_specific_data_from_booking_id()
    {
        $booking_id = $this->input->post('booking_id');
        $arrBookings = $this->warranty_utilities->get_warranty_specific_data_of_bookings([$booking_id]);
        if(!empty($arrBookings[0]['model_number'])){
            $arr_model = $this->reusable_model->get_search_result_data("appliance_model_details","id",array("model_number"=>  stripslashes($arrBookings[0]['model_number']), "active" => 1),NULL,NULL,NULL,NULL,NULL,array());        
            $model_id = !empty($arr_model[0]['id']) ? $arr_model[0]['id'] : "";
            $arrBookings[0]['model_id'] = $model_id;
        }
        echo json_encode($arrBookings);
    }
 
}
