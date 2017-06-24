<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 36000000);

class Collection_partner extends CI_Controller {

    /**
     * load list modal and helpers
     */
    function __Construct() {
        parent::__Construct();

        $this->load->helper(array('form', 'url'));
        $this->load->model('cp_model');
        $this->load->model('vendor_model');


        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee')) {
            return TRUE;
        } else {
            echo PHP_EOL . 'Terminal Access Not Allowed' . PHP_EOL;
            redirect(base_url() . "employee/login");
        }
    }
    
    function get_cp_shop_address(){
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'));
        $this->load->view('buyback/get_cp_partner');
        $this->load->view('dashboard/dashboard_footer');
    }
    
    function get_cp_shop_address_data(){
       // log_message("info", print_r(json_encode($_POST, TRUE), TRUE));
       // $string  = '{"draw":"1","columns":[{"data":"0","name":"","searchable":"true","orderable":"false","search":{"value":"","regex":"false"}},{"data":"1","name":"","searchable":"true","orderable":"true","search":{"value":"","regex":"false"}},{"data":"2","name":"","searchable":"true","orderable":"true","search":{"value":"","regex":"false"}},{"data":"3","name":"","searchable":"true","orderable":"true","search":{"value":"","regex":"false"}},{"data":"4","name":"","searchable":"true","orderable":"true","search":{"value":"","regex":"false"}},{"data":"5","name":"","searchable":"true","orderable":"true","search":{"value":"","regex":"false"}},{"data":"6","name":"","searchable":"true","orderable":"true","search":{"value":"","regex":"false"}},{"data":"7","name":"","searchable":"true","orderable":"true","search":{"value":"","regex":"false"}},{"data":"8","name":"","searchable":"true","orderable":"true","search":{"value":"","regex":"false"}},{"data":"9","name":"","searchable":"true","orderable":"true","search":{"value":"","regex":"false"}},{"data":"10","name":"","searchable":"true","orderable":"true","search":{"value":"","regex":"false"}}],"start":"0","length":"50","search":{"value":"","regex":"false"}}';
       //  $_POST = json_encode($string);
        $length = $this->input->post('length');
        $start = $this->input->post('start');
        $search = $this->input->post('search');
        $search_value = $search['value'];
        $order = $this->input->post('order');
        $draw = $this->input->post('draw');
        
        $list = $this->cp_model->get_cp_shop_address_list($length, $start, $search_value, $order);
        $data = array();
        $no = $start;
        foreach ($list as $cp_address) {

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $cp_address->public_name;
            $row[] = $cp_address->name;
            $row[] = $cp_address->contact_person;
            $row[] = $cp_address->primary_contact_number;
            $row[] = $cp_address->alternate_conatct_number;
            $row[] = $cp_address->shop_address_line1;
            $row[] = $cp_address->shop_address_line2;
            $row[] = $cp_address->shop_address_city;
            $row[] = $cp_address->shop_address_pincode;
            if($cp_address->active == 1){
                 $row[] = "<button class='btn btn-sm btn-danger' onclick='activate_deactivate($cp_address->id,0)'  >De-Activate</button>";
            } else {
                 $row[] = "<button  class='btn btn-sm btn-success' onclick='activate_deactivate($cp_address->id,1)' >Activate</button>";
            }
            $json_data = json_encode($cp_address);
            $row[] = "<button type='button' class='btn btn-info btn-md open-AddBookDialog' data-id='$json_data' data-toggle='modal' data-target='#update_form'> "
                    . "<span class='glyphicon glyphicon-edit' aria-hidden='true'></span></button>";
            

            $data[] = $row;
        }
        
        $output = array(
            "draw" => $draw,
            "recordsTotal" => $this->cp_model->count_all_shop_address(),
            "recordsFiltered" => $this->cp_model->count_filtered_shop_address($search_value, $order),
            "data" => $data,
        );

        //output to json format
        echo json_encode($output);
    }
    
    function activate_deactivate_cp($shop_id, $is_active){
        $status = $this->cp_model->update_cp_shop_address(array('id'=> $shop_id), array('active' => $is_active));
        if($status){
            echo "Success";
        } else {
            echo "Error";
        }
    }
    
    function update_cp_shop_address() {
        $primary_id = $this->input->post('primary_id');
        $data['contact_person'] = $this->input->post('contact_person');
        $data['contact_email'] = $this->input->post('contact_email');
        $data['primary_contact_number'] = $this->input->post('primary_contact_number');
        $data['alternate_conatct_number'] = $this->input->post('alternate_conatct_number');
        $data['alternate_conatct_number2'] = $this->input->post('alternate_conatct_number2');
        $data['tin_number'] = $this->input->post('tin_number');
        $data['shop_address_line1'] = $this->input->post('shop_address_line1');
        $data['shop_address_line2'] = $this->input->post('shop_address_line2');
        $data['shop_address_city'] = $this->input->post('shop_address_city');
        $data['shop_address_pincode'] = $this->input->post('shop_address_pincode');
        $data['shop_address_state'] = $this->input->post('shop_address_state');

        $status = $this->cp_model->update_cp_shop_address(array('id' => $primary_id), $data);
        if ($status) {
            echo "Success";
        } else {
            echo "Error";
        }
    }
    
    function get_city_for_cp(){
        $dis = $this->input->post('city');
        $pincode = $this->input->post('pincode');
        $data = $this->vendor_model->getDistrict_from_india_pincode("", $pincode);
        echo "<option selected='selected' value='' disabled>Select City</option>";
        $flag = false;
        foreach ($data as $district) {
            if (strtolower(trim($dis)) == strtolower(trim($district['district']))) {
                echo "<option selected value='$district[district]'>$district[district]</option>";
                $flag = true;
            } else {
                echo "<option value='$district[district]'>$district[district]</option>";
            }
        }
        
        if(!$flag && !empty($dis)){
             echo "<option selected value='$dis' >$dis</option>";
        }
    }

}