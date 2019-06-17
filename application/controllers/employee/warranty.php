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
        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee')) {
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
    public function index() {
        $partners = $this->partner_model->getpartner();
        foreach ($partners as $partnersDetails) {
            $partnerArray[$partnersDetails['id']] = $partnersDetails['public_name'];
        }

        $this->miscelleneous->load_nav_header();
        $this->load->view('warranty/check_warranty', ['partnerArray' => $partnerArray]);
    }

    public function get_warranty_list_data() {
        $post = $this->get_post_data();
        $post_data = $this->input->post();
        $list = $this->warranty_model->check_warranty($post_data);
        $data = array();
        $no = $post['start'];
        //echo '<pre>';print_R($list);exit;
        
        foreach ($list as $key => $value) {
            $no++;
            $row =  $this->warranty_data($list[$key], $no, $post_data['purchase_date']);
            $data[] = $row;
        }
        
        $new_post['length'] = -1;
        $count = count($data);
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $count,
            "recordsFiltered" =>  $count,
            "data" => $data,
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
    
    function warranty_data($warranty_list, $no, $purchase_date){
        $warranty_end_period = $purchase_date;
        if(!empty($warranty_list['warranty_period'])){
            $warranty_end_period = strtotime(date("Y-m-d", strtotime($warranty_end_period)) . " +".$warranty_list['warranty_period']." months");
        }
        if(!empty($warranty_list['warranty_grace_period'])){
            $warranty_end_period = strtotime(date("Y-m-d", strtotime($warranty_end_period)) . " +".$warranty_list['warranty_grace_period']." days");
        }        
        
        $row = array();
        $row[] = $no;
        $row[] = $warranty_list['plan_name'];
        $row[] = date('d-m-Y', strtotime($warranty_list['period_start']));
        $row[] = date('d-m-Y', strtotime($warranty_list['period_end']));
        $row[] = $warranty_list['states'];
        $row[] = $warranty_list['part_types'];
        $row[] = !empty($warranty_list['inclusive_svc_charge']) ? "No" : "Yes";
        $row[] = !empty($warranty_list['inclusive_gas_charge']) ? "No" : "Yes";
        $row[] = (!empty($warranty_list['warranty_type']) && $warranty_list['warranty_type'] == 1) ? "In Warranty" : "Extended Warranty";
        $row[] = $warranty_list['warranty_period']. " Month(s)";
        $row[] = $warranty_list['warranty_grace_period']. " Day(s)";      
        $row[] = date('d-m-Y', $warranty_end_period);;
        return $row;        
    }
}
