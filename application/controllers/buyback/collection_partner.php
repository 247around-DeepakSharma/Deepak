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
        $this->load->model('bb_model');
        $this->load->library('buyback');
        $this->load->library('booking_utilities');


        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee')) {
            return TRUE;
        } else {
            echo PHP_EOL . 'Terminal Access Not Allowed' . PHP_EOL;
            redirect(base_url() . "employee/login");
        }
    }
    
    function get_cp_shop_address(){
        $data['saas_flag'] = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'),$data);
        $this->load->view('buyback/get_cp_partner');
        $this->load->view('dashboard/dashboard_footer');
    }
    
    function get_cp_shop_address_data(){
       // log_message("info", print_r(json_encode($_POST, TRUE), TRUE));
       // $string  = '{"draw":"1","columns":[{"data":"0","name":"","searchable":"true","orderable":"false","search":{"value":"","regex":"false"}},{"data":"1","name":"","searchable":"true","orderable":"true","search":{"value":"","regex":"false"}},{"data":"2","name":"","searchable":"true","orderable":"true","search":{"value":"","regex":"false"}},{"data":"3","name":"","searchable":"true","orderable":"true","search":{"value":"","regex":"false"}},{"data":"4","name":"","searchable":"true","orderable":"true","search":{"value":"","regex":"false"}},{"data":"5","name":"","searchable":"true","orderable":"true","search":{"value":"","regex":"false"}},{"data":"6","name":"","searchable":"true","orderable":"true","search":{"value":"","regex":"false"}},{"data":"7","name":"","searchable":"true","orderable":"true","search":{"value":"","regex":"false"}},{"data":"8","name":"","searchable":"true","orderable":"true","search":{"value":"","regex":"false"}},{"data":"9","name":"","searchable":"true","orderable":"true","search":{"value":"","regex":"false"}},{"data":"10","name":"","searchable":"true","orderable":"true","search":{"value":"","regex":"false"}}],"start":"0","length":"50","search":{"value":"","regex":"false"}}';
       //  $_POST = json_encode($string);
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $search = $this->input->post('search');
        $post['search_value'] = $search['value'];
        $post['order'] = $this->input->post('order');
        $draw = $this->input->post('draw');
        
        $list = $this->cp_model->get_cp_shop_address_list($post);
        $data = array();
        $no = $post['start'];
        foreach ($list as $cp_address) {

            $no++;
            $row = array();
            $row[] = $no;
            $json_data = json_encode($cp_address);
           
            $a = "<a href='javascript:void(0)' class='btn btn-info btn-sm' onclick='";
            $a .= "get_cp_history(".$cp_address->id;
            $a .= ', "'.$cp_address->name.'"';
            $a .= ")' ><span><i class='fa fa-eye'></i></span></a>";
            
            $row[] = "<a  href='javascript:void(0)' class='open-AddBookDialog' data-id='$json_data' data-toggle='modal' data-target='#update_form'> "
                    . "$cp_address->name</a>";
            $row[] = $cp_address->contact_person;
            $row[] = "<b>".$cp_address->primary_contact_number."</b><br> <br> ".$cp_address->alternate_conatct_number."</b>";
            $row[] = "<b>".$cp_address->shop_address_line1."</b><br> <br>".$cp_address->shop_address_line2;
            $row[] = $cp_address->shop_address_pincode;
            $row[] = $cp_address->shop_address_city." / ".$cp_address->shop_address_region;

            if($cp_address->active == 1){
                 $row[] = "<div class='btn btn-sm btn-danger' onclick='activate_deactivate($cp_address->id,0)'  >De-Activate</div>";
            } else {
                 $row[] = "<div  class='btn btn-sm btn-success' onclick='activate_deactivate($cp_address->id,1)' >Activate</div>";
            }
            $row[] = $a;
            if($cp_address->active == 1){
                $disabled_option = "class='download_single_cp_price'";
            }else{
                $disabled_option = "style = 'display:none;'";
            }
            $row[] = "<input type='checkbox' id = cp_price_download_".$cp_address->cp_id." data-cp_id = '".$cp_address->cp_id."' data-cp_name = '".$cp_address->name."' $disabled_option>";
            

            $data[] = $row;
        }
        
        $last_row[] = "";
        $last_row[] = "";
        $last_row[] = "";
        $last_row[] = "";
        $last_row[] = "";
        $last_row[] = "";
        $last_row[] = "";
        $last_row[] = "";
        $last_row[] = "";
        $last_row[] = "<input class='btn btn-success' title = 'Download File' id='download_file' type = 'submit' value= 'Download' onclick='return check_validation()'>";
        $data[] = $last_row;
        
        $output = array(
            "draw" => $draw,
            "recordsTotal" => $this->cp_model->count_all_shop_address(),
            "recordsFiltered" => $this->cp_model->count_filtered_shop_address($post),
            "data" => $data,
        );

        //output to json format
        echo json_encode($output);
    }
    
    function activate_deactivate_cp($shop_id, $is_active){
        $status = $this->cp_model->update_cp_shop_address(array('id'=> $shop_id), array('active' => $is_active));
        if($status){
            $log['entity'] = BB_CP_ADDRESS;
            $log['entity_id'] = $shop_id;
            $log['agent_id'] = $this->session->userdata('id');
            if($is_active ==0){
                $log['action'] = SHOP_ADDRESS_DEACTIVATED;
            } else {
                $log['action'] = SHOP_ADDRESS_ACTIVATED;
            }
            
            $this->vendor_model->insert_log_action_on_entity($log);
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
        $data['shop_address_region'] = implode(',', $this->input->post('shop_address_region'));
        $data['shop_address_city'] = $this->input->post('shop_address_city');
        $data['shop_address_pincode'] = $this->input->post('shop_address_pincode');
        $data['shop_address_state'] = $this->input->post('shop_address_state');
        $data['cp_capacity'] = $this->input->post('cp_capacity');

        $status = $this->cp_model->update_cp_shop_address(array('id' => $primary_id), $data);
        if ($status) {
            echo "Success";
        } else {
            echo "Error";
        }
    }
    
    function get_city_for_cp() {
        $dis = $this->input->post('city');
        $pincode = $this->input->post('pincode');
        $region_post = $this->input->post('region');
        $data = $this->vendor_model->getDistrict_from_india_pincode("", $pincode);
        if (!empty($data)) {
            $city = "<option selected='selected' value='' disabled>Select City</option>";
            $region = "<option  disabled>Select Region</option>";
            $flag = false;
            $flag1 = false;
            foreach ($data as $district) {
                if (strtolower(trim($dis)) == strtolower(trim($district['district']))) {
                    $city .= "<option selected value='$district[district]'>$district[district]</option>";
                    $flag = true;
                } else {
                    $city .= "<option value='$district[district]'>$district[district]</option>";
                }

                if (!empty($region_post) && in_array(strtolower(trim($district['district'])), $region_post)) {
                    $region .= "<option selected value='$district[district]'>$district[district]</option>";
                    $flag1 = true;
                } else {
                    $region .= "<option value='$district[district]'>$district[district]</option>";
                }
            }

            if (!$flag && !empty($dis)) {
                $city .= "<option selected value='$dis' >$dis</option>";
            }

            if (!$flag1 && !empty($region_post)) {
                foreach($region_post as $key => $val){
                    $region .= "<option selected value='$val'>$val</option>";
                }
            }

            $output['city'] = $city;
            $output['region'] = $region;
            echo json_encode($output, true);
            
        } else {
            echo "Not Exist";
        }
    }

    function add_cp_shop_address(){
       
        $data['saas_flag'] = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'),$data);
        $this->load->view('buyback/add_cp_shop_address');
        $this->load->view('dashboard/dashboard_footer');
    }
    
    function process_add_cp_shop_address(){
        
        if(!empty($this->input->post('data'))){
                $userData = array(
                    'cp_id' => $this->input->post('data')['cp_id']['id'],
                    'partner_id' => '247024',
                    'shop_address_line1' => $this->input->post('data')['shop_address_line1'],
                    'shop_address_line2' => isset($this->input->post('data')['shop_address_line2'])?$this->input->post('data')['shop_address_line2']:'',
                    'shop_address_city' => $this->input->post('data')['shop_address_city'],
                    'shop_address_region' => isset($this->input->post('data')['shop_address_region'])?implode(',', $this->input->post('data')['shop_address_region']):'',
                    'shop_address_pincode' => $this->input->post('data')['shop_address_pincode'],
                    'shop_address_state' => $this->input->post('data')['shop_address_state'],
                    'contact_person' => $this->input->post('data')['name'],
                    'primary_contact_number' => $this->input->post('data')['phone_number'],
                    'contact_email' => $this->input->post('data')['email'],
                    'alternate_conatct_number' => isset($this->input->post('data')['alt_phone_number_1'])?$this->input->post('data')['alt_phone_number_1']:'',
                    'alternate_conatct_number2' => isset($this->input->post('data')['alt_phone_number_2'])?$this->input->post('data')['alt_phone_number_2']:'',
                    'active' => '1',
                    'tin_number' => isset($this->input->post('data')['tin_number'])?$this->input->post('data')['tin_number']:'',
                    'cp_capacity' => isset($this->input->post('data')['cp_capacity'])?$this->input->post('data')['cp_capacity']:'',
                    'create_date' => date('Y-m-d H-i-s'),
                );
                $insert_id = $this->cp_model->insert_cp_shop_address($userData);
                if($insert_id){
                    $log = array(
                        "entity" => "cp",
                        "entity_id" => $userData['cp_id'],
                        "agent_id" => $this->session->userdata('id'),
                        "action" =>  NEW_SHOP_ADDRESS_ADDED,
                        "remarks" =>  NEW_SHOP_ADDRESS_ADDED
                        );
                    $log_insert_id = $this->vendor_model->insert_log_action_on_entity($log);
                    if($log_insert_id){
                        $data['status'] = 'OK';
                        $data['msg'] = 'Shop Address details has been added successfully.';
                    }else{
                        $data['status'] = 'ERR';
                        $data['msg'] = 'Some problem occurred, please try again.';
                    }
                    
                }else{
                    $data['status'] = 'ERR';
                    $data['msg'] = 'Some problem occurred, please try again.';
                }
            }else{
                $data['status'] = 'ERR';
                $data['msg'] = 'Invalid Request';
            }
            echo json_encode($data);
    }
    
    function get_active_cp_sf(){
        $where = array('service_centres.active' => '1', 'service_centres.is_cp' => '1');
        $select = "service_centres.name, service_centres.id,service_centres.on_off,service_centres.active, account_holders_bank_details.is_verified, service_centres.is_cp ";
        $data = $this->vendor_model->get_vendor_with_bank_details($select, $where);
        echo json_encode($data);
    }
    
    
    /**
     * @desc Used to get cp histroy from log_entity_action table
     * @param $shop_id string
     * @return void();
     */
    function get_cp_history($shop_id){
        
        $select = 'log_entity_action.* , employee.full_name as agent_name';
        $where = array('entity_id' => $shop_id,'entity'=>BB_CP_ADDRESS);
        
        $data['cp_history'] = $this->cp_model->get_cp_history($select,$where);
        
        echo $this->load->view('buyback/show_cp_history',$data);
    }
    /**
     * @desc Process Assign CP & Shop Process
     */
    function process_assign_order(){
        log_message("info", __METHOD__);
        $assigned_cp_data = $this->input->post("assign_cp_id");
        $error = array();
        $agent = $this->session->userdata('id');
        $array['where_in'] = array();$array['column_order'] = array();$array['column_search'] = array();$array['length'] = -1;$array['search_value'] = "";
        foreach ($assigned_cp_data as $order_id => $shop_id) {
            $cp_shop = $this->bb_model->get_cp_shop_address_details(array('bb_shop_address.id' => $shop_id), 'cp_id, shop_address_region');
            
            $array['where'] = array();
            $array['where'] = array('bb_unit_details.partner_order_id' => $order_id);
            
            $bb_details = $this->bb_model->get_bb_order_list($array)[0];
            $s_order_key = str_replace(":","",$bb_details->order_key);
            $s_order_key1 = str_replace("_","",$s_order_key);
            $where = array('cp_id' => $cp_shop[0]['cp_id'], 
                'partner_id'=> $bb_details->partner_id, 
                'order_key' => $s_order_key1 );
            $b[0] = $where;
            
            $status = $this->buyback->update_assign_cp_process($b, $order_id, $agent, $bb_details->internal_status);
            if(!$status['status']){
                array_push($error, array('order_id' =>$order_id,"msg" => $status['msg']));
            }
        }
        if(!empty($error)){
            $output = array('status' => -247, 'error' =>$error);
        } else {
            $output = array('status' => 247);
        }
        
        echo json_encode($output);
    }

}
