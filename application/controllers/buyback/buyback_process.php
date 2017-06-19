<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Buyback_process extends CI_Controller {

    /**
     * load list modal and helpers
     */
    function __Construct() {
        parent::__Construct();

        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('buyback');


        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee')) {
            return TRUE;
        } else {
            echo PHP_EOL . 'Terminal Access Not Allowed' . PHP_EOL;
            redirect(base_url() . "employee/login");
        }
    }
    /**
     * @desc Used to load order details view
     */
    function view_bb_order_details() {
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'));
        $this->load->view('buyback/get_order_details');
        $this->load->view('dashboard/dashboard_footer');
    }
    /**
     * @desc Used to get data as requested and also search 
     */
    function get_bb_order_details() {
        // log_message("info", print_r(json_encode($_POST, TRUE), TRUE));
//        $string = '{"draw":"3","columns":[{"data":"0","name":"","searchable":"true","orderable":"false","search":{"value":"","regex":"false"}},{"data":"1","name":"","searchable":"true","orderable":"true","search":{"value":"","regex":"false"}},{"data":"2","name":"","searchable":"true","orderable":"true","search":{"value":"","regex":"false"}},{"data":"3","name":"","searchable":"true","orderable":"true","search":{"value":"","regex":"false"}},{"data":"4","name":"","searchable":"true","orderable":"true","search":{"value":"","regex":"false"}},{"data":"5","name":"","searchable":"true","orderable":"true","search":{"value":"","regex":"false"}},{"data":"6","name":"","searchable":"true","orderable":"true","search":{"value":"","regex":"false"}}],"start":"0","length":"50","search":{"value":"pu","regex":"false"},"status":"0"}';
//        $_POST = json_decode($string, true);
        $length = $this->input->post('length');
        $start = $this->input->post('start');
        $search = $this->input->post('search');
        $search_value = $search['value'];
        $order = $this->input->post('order');
        $draw = $this->input->post('draw');
        $status = $this->input->post('status');
        $list = $this->bb_model->get_bb_order_list($length, $start, $search_value, $order, $status);

        $data = array();
        $no = $start;
        foreach ($list as $order_list) {

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $order_list->partner_order_id;
            $row[] = $order_list->services;
            $row[] = $order_list->city;
            $row[] = $order_list->order_date;
            $row[] = $order_list->delivery_date;
            $row[] = $order_list->current_status;
            $row[] = $order_list->partner_basic_charge;
            $row[] = ($order_list->cp_basic_charge + $order_list->cp_tax_charge);

            $data[] = $row;
        }


        $output = array(
            "draw" => $draw,
            "recordsTotal" => $this->bb_model->count_all($status),
            "recordsFiltered" => $this->bb_model->count_filtered($search_value, $order, $status),
            "data" => $data,
        );

        //output to json format
        echo json_encode($output);
    }

}
