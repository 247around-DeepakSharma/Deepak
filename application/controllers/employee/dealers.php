<?php

if (!defined('BASEPATH')){
    exit('No direct script access allowed');
}

class Dealers extends CI_Controller {

    /**
     * load list modal and helpers
     */
    function __Construct() {
        parent::__Construct();
        $this->load->model('booking_model');
        $this->load->model('dealer_model');
        $this->load->model('user_model');
        $this->load->model('invoices_model');
        $this->load->model('partner_model');
        $this->load->model('service_centre_charges_model');
        $this->load->library('miscelleneous');
        $this->load->library("initialized_variable");
        $this->load->model('user_model');
        $this->load->library("session");
        $this->load->library('form_validation');
        $this->load->helper(array('form', 'url'));
        
    }
    /**
     * @desc this is used to load add booking form
     */
    function add_booking() {
        log_message("info", __METHOD__);
        $this->checkDealerSession();
        $this->load->view('dealers/header');
        $this->load->view('dealers/addbooking');
    }
    /**
     * @desc Get user details by phone number. This method is called by Ajax
     */
    function get_users_details(){
        log_message("info", __METHOD__);
        $this->checkDealerSession();
        $phone = $this->input->post("phone_number");
        $user_data = $this->user_model->get_users_by_any(array('phone_number'=> $phone));
        if(!empty($user_data)){
            $appliance_data = $this->get_appliance_data();
            $array = array('code'=>'0001', 'user_data' => $user_data, 'appliance_data' => $appliance_data);
            print_r(json_encode($array));
            
        } else {
            $appliance_data = $this->get_appliance_data();
            $array = array('code'=>'0000', 'appliance_data' => $appliance_data);
            print_r(json_encode($array));
        }
    }
    /**
     * @desc get Appliance name called by Ajax
     * @return string
     */
    function get_appliance_data(){
         log_message("info", __METHOD__);
        $this->checkDealerSession();
        $partner_data= $this->session->userdata('partners');
        $dealer_id = $this->session->userdata('dealer_id');
        $condition = array(
            "where" => array('dealer_brand_mapping.dealer_id' => $dealer_id, "partners.is_active" => 1),
            "where_in" => array('partner_id' => $partner_data),
            "search" => array(),
            "order_by" => "services");
        $select = "services, service_id";
        $appliance = $this->dealer_model->get_dealer_mapping_details($condition, $select);
        if($appliance){
            $option = "<option selected disabled>Select Appliance</option>";
            
            foreach($appliance as $value){
                $option .= "<option  ";
                 
                if(count($appliance) == 1){
                    $option .=" selected ";
                }
                $option .= "value = '".$value['service_id']."' data-id='".$value['services']."' >".$value['services']."</option>";
            }
            return $option;
        } else {
            return "<option selected disabled>Select Appliance</option>";
        }
    }
    /**
     * @desc get brand called by ajax
     */
    function get_brands(){
         log_message("info", __METHOD__);
        $this->checkDealerSession();
        $brand = $this->input->post("brand");
        $service_id = $this->input->post("service_id");
        $dealer_id = $this->session->userdata('dealer_id');
        $partner_data= $this->session->userdata('partners');
       
        $select = "brand, partner_id";
        $condition = array(
            "where" => array('dealer_brand_mapping.dealer_id' => $dealer_id, 'service_id' => $service_id, "partners.is_active" => 1),
            "where_in" => array('partner_id' => $partner_data),
            "search" => array(),
            "order_by" => "brand");
        $brand_data = $this->dealer_model->get_dealer_mapping_details($condition, $select);
        if(!empty($brand_data)){
            $option = "<option selected disabled>Select Brand</option>";
            foreach($brand_data as $value){
                $option .= "<option  ";
                if(count($brand_data) == 1){
                    $option .=" selected ";
                } else if($value['brand'] == $brand){
                    $option .="selected ";
                }
                $option .= " value = '".$value['brand']."' data-id='".$value['partner_id']."' >".$value['brand']."</option>";
            }
            
            $array = array("code" => '0001', 'brand' =>$option);

        } else {
             $array = array("code" => '0000');
        }
        print_r(json_encode($array));
    }
    
    function get_category() {
        log_message("info", __METHOD__ . json_encode($_POST, true));
        $this->checkDealerSession();
        $brand = $this->input->post("brand");
        $category = $this->input->post("category");
        $service_id = $this->input->post("service_id");
        $partner_id = $this->input->post('partner_id');

        if (!empty($partner_id) && !empty($brand)) {
            $partner_details = $this->partner_model->getpartner_details("partners.id, public_name, "
                    . "postpaid_credit_period, is_active, postpaid_notification_limit, postpaid_grace_period, is_prepaid,partner_type, "
                    . "invoice_email_to,invoice_email_cc", array('partners.id' => $partner_id));

            if (!empty($partner_details)) {
                if ($partner_details[0]['is_prepaid'] == 1) {
                    $prepaid = $this->miscelleneous->get_partner_prepaid_amount($partner_id);
                    $message = $prepaid['prepaid_msg'];
                } else if ($partner_details[0]['is_prepaid'] == 0) {
                    $prepaid = $this->invoice_lib->get_postpaid_partner_outstanding($partner_details[0]);
                    $message = $prepaid['notification_msg'];
                }

                if (!empty($prepaid)) {
                    if ($prepaid['active'] == 1) {
                        $where = array('service_id' => $service_id, "brand" => $brand,
                            'product_or_services' => 'Service', 'partner_net_payable > 0' => NULL, 'partner_id' => $partner_id);
                        $select = "category";
                        $order_by = "category";
                        $category_data = $this->service_centre_charges_model->get_service_charge_details($where, $select, $order_by);
                        if (!empty($category_data)) {
                            $option = "<option selected disabled>Select Category</option>";
                            foreach ($category_data as $value) {
                                $option .= "<option  ";
                                if (count($category_data) == 1) {
                                    $option .= " selected ";
                                } else if ($value['category'] == $category) {
                                    $option .= "selected ";
                                }
                                $option .= " value = '" . $value['category'] . "' >" . $value['category'] . "</option>";
                            }

                            $array = array("code" => '0001', 'category' => $option);
                        } else {
                            $array = array("code" => '0002');
                        }
                    } else {
                        $array = array("code" => '0003', "msg" => PREPAID_LOW_AMOUNT_MSG_FOR_DEALER);
                    }
                } else {
                    $array = array("code" => '0004');
                }
            } else {
                $array = array("code" => '0004');
            }
        } else {
            $array = array("code" => '0004');
        }

        print_r(json_encode($array));
    }

    function get_capacity(){
         log_message("info", __METHOD__);
        $this->checkDealerSession();
        $capacity = $this->input->post("capacity");
        $brand = $this->input->post("brand");
        $category = $this->input->post("category");
        $service_id = $this->input->post("service_id");
        $partner_id= $this->input->post('partner_id');
        $where = array('service_id' => $service_id, "brand" => $brand,"category" => $category,
            'product_or_services' => 'Service', 'partner_net_payable > 0' => NULL, 'partner_id' => $partner_id);
        $select = "capacity"; $order_by = "capacity";
        $capacity_data = $this->service_centre_charges_model->get_service_charge_details($where, $select, $order_by);
        if(!empty($capacity_data)){
            $option = "<option selected disabled>Select Category</option>";
            foreach($capacity_data as $value){
                $option .= "<option  ";
                if(count($capacity_data) == 1){
                    $option .=" selected ";
                } else if($value['capacity'] == $capacity){
                    $option .="selected ";
                }
                $option .= " value = '".$value['capacity']."' >".$value['capacity']."</option>";
            }
            
            $array = array("code" => '0001', 'capacity' =>$option);

        } else {
             $array = array("code" => '0000');
        }
        print_r(json_encode($array));
    }
    
    function get_service_tag(){
         log_message("info", __METHOD__);
        $this->checkDealerSession();
        $service_id  = $this->input->post('service_id');
        $brand = $this->input->post('brand');
        $category = $this->input->post('category');
        $capacity = $this->input->post('capacity');
        $city = $this->input->post('city');
        $pincode = $this->input->post('pincode');
        $service_category = $this->input->post('service_category');
        $partner_id= $this->input->post('partner_id');

        $where = array('service_id' => $service_id, "brand" => $brand,"category" => $category, 'capacity' =>$capacity,
            'product_or_services' => 'Service', 'partner_net_payable > 0' => NULL, 'partner_id' => $partner_id, "service_category LIKE '%installation%' " => NULL);
        $select = "id, service_category, partner_id, is_upcountry, customer_total,partner_net_payable"; $order_by = "service_category";
        $result = $this->service_centre_charges_model->get_service_charge_details($where, $select, $order_by);
        if(!empty($result)){
            $p_where = array('id' => $result[0]['partner_id']);
            $partner_details = $this->partner_model->get_all_partner($p_where);

            $data = $this->miscelleneous->check_upcountry_vendor_availability($city, $pincode,$service_id, $partner_details, NULL);
            
            
            $html = "<table class='table priceList table-striped table-bordered'><thead><tr><th class='text-center'>Service Category</th>"
                    . "<th class='text-center'>Final Charges</th>"
                    . "<th class='text-center' id='selected_service'>Selected Services</th>"
                    . "</tr></thead><tbody>";
	    $i = 0;
            $explode = array();
            if(!empty($service_category)){
                $explode = explode(",", $service_category);
            }
	    foreach ($result as $prices) {
                
		$html .="<tr class='text-center'><td>" . $prices['service_category'] . "</td>";
                $html .= "<td>0.00</td>";
		$html .= "<td><input type='hidden'name ='is_up_val' id='is_up_val_" . $i . "' value ='".$prices['is_upcountry']."' /><input class='price_checkbox'";
		$html .=" type='checkbox' id='checkbox_" . $i . "'";
		$html .= "name='prices[]'";
                if(in_array($prices['service_category'], $explode)){
                     $html .= " checked ";
                }

                if($prices['service_category'] == REPAIR_OOW_PARTS_PRICE_TAGS){
                     $html .= " disabled ";
  
                }
		$html .= "  onclick='final_price(),get_symptom(),set_upcountry()'" .
		    "value=" . $prices['id'] . "_" . intval($prices['customer_total'])."_".intval($prices['partner_net_payable'])."_".$i . " ></td><tr>";

		$i++;
            }
            $html .= "<tr class='text-center'><td>Upcountry Services</td>";
            $html .= "<td id='upcountry_charges'>0.00</td>";
            $html .= "<td><input type='checkbox' id='checkbox_upcountry' onclick='final_price()'"
                    . " name='upcountry_checkbox' value='upcountry_0_0' disabled ></td></tbody></table>";
            $form_data['table'] = $html;
            $form_data['upcountry_data'] = json_encode($data, TRUE);
            
            print_r(json_encode($form_data, TRUE));

         } else {
             echo "ERROR";
         }
    }
    
    function process_addbooking(){
         log_message("info", __METHOD__);
        $this->checkDealerSession();
        log_message('info', "Entering: " . __METHOD__);
        $partner_id = $this->input->post('partner_id');
        $this->initialized_variable->fetch_partner_data($partner_id);
        $post = $this->get_input_form();

        $authToken = $this->initialized_variable->get_partner_data()[0]['auth_token'];
       
        $postData = json_encode($post, true);

        $ch = curl_init(base_url() . 'partner/insertBookingByPartner');
        curl_setopt_array($ch, array(
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_HTTPHEADER => array(
                'Authorization: ' . $authToken,
                'Content-Type: application/json'
            ),
            CURLOPT_POSTFIELDS => $postData
        ));

        // Send the request
        $response = curl_exec($ch);
        $responseData = json_decode($response, TRUE);
        if (isset($responseData['data']['code'])) {
            if($responseData['data']['code'] == -1003){
                 $output_msg = "Order ID Already Exists, Booking ID: ".$responseData['data']['response']['247aroundBookingID'] ;
                 $output = array('msg' => $output_msg);
                
             } else if ($responseData['data']['code'] == 247) {
                 $output_msg = "Booking Inserted Successfully, \n Booking ID: ".$responseData['data']['response']['247aroundBookingID'];
                 $output = array('msg' => $output_msg);
                 

                 log_message('info', 'Partner ' . $this->session->userdata('partner_name') . "  booking Inserted " . print_r($postData, true));
               

             } else {
                 log_message('info', ' Partner ' . $this->session->userdata('partner_name') . "  booking not Inserted " . print_r($postData, true) . " error mgs" . print_r($responseData['data'], true));
                 $this->insertion_failure($postData);

                 $output_msg = "Sorry, Booking Could Not be Inserted. Please Try Again.";
                 $output = array('msg' => $output_msg);
                                      
             }

             
        } else {
            $this->insertion_failure($postData);

            $output_msg = "Sorry, Booking Could Not Be Inserted. 247around Team Is Looking Into This.";
            $output = array('msg' => $output_msg);
            
        }
        
        print_r(json_encode($output));
        
    }
    
    function insertion_failure($post){
         log_message("info", __METHOD__);
        $this->checkDealerSession();
        $to = DEVELOPER_EMAIL;
        $cc = "";
        $bcc = "";
        $subject = "Booking Insertion Failure By Dealer".$this->input->post('dealer_name');
        $message = $post;
        $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, $bcc, $subject, $message, "",BOOKING_INSERTION_FAILURE_BY_DEALER);

    }
    
    function get_input_form(){
        $this->checkDealerSession();
        $booking_date = date('d-m-Y', strtotime($this->input->post('booking_date')));
        $post['partnerName'] = $this->initialized_variable->get_partner_data()[0]['public_name'];
        $post['partner_id'] = $this->input->post('partner_id');
        $post['agent_id'] = $this->session->userdata('agent_id');
        $post['name'] = $this->input->post('user_name');
        $post['mobile'] = $this->input->post('booking_primary_contact_no');
        $post['email'] = $this->input->post('user_email');
        $post['address'] = $this->input->post('booking_address');
        $post['pincode'] = $this->input->post('booking_pincode');
        $post['city'] = $this->input->post('city');
        $post['requestType'] = $this->input->post('prices');
        $post['landmark'] = '';
        $post['service_id'] = $this->input->post('service_id');
        $post['brand'] = $this->input->post('appliance_brand');
        $post['productType'] = '';
        $post['category'] = $this->input->post('appliance_category');
        $post['capacity'] = $this->input->post('appliance_capacity');
        $post['model'] = $this->input->post('model_number');
        $post['serial_number'] = $this->input->post('serial_number');
        $post['purchase_date'] = $this->input->post('purchase_date');
        $post['partner_source'] = "Dealer";
        $post['remarks'] = $this->input->post('booking_remarks');
        $post['orderID'] = $this->input->post('order_id');
        $post['assigned_vendor_id'] = '';
        $post['upcountry_data'] = $this->input->post('upcountry_data');
        $post['alternate_phone_number'] = $this->input->post('alternate_phone_number');
        $post['booking_date'] = $booking_date;
        $post['partner_type'] = "OEM";
        
        $post['partner_code'] = $this->initialized_variable->get_partner_data()[0]['code'];
        $post['amount_due'] = $this->input->post('grand_total');
        $post['product_type'] = "Delivered";
        $post['appliance_name'] = $this->input->post('appliance_name');
        $post['dealer_name'] = $this->session->userdata('dealer_name');
        $post['dealer_phone_number'] = $this->session->userdata('dealer_phone_number');
        $post['dealer_id'] = $this->session->userdata('dealer_id');
        $post['appliance_unit'] = $this->input->post("appliance_unit");
        $post['booking_request_symptom'] = $this->input->post('booking_request_symptom');
        
        return $post;
    }
    
    function checkDealerSession(){
        log_message("info", __METHOD__);
        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'dealers') 
                && !empty($this->session->userdata('dealer_id')) ) {
            return TRUE;
        } else {
            log_message('info', __FUNCTION__. " Session Expire for Service Center");
            $this->session->sess_destroy();
            redirect(base_url() . "dealers");
        }
    }
    
    function add_dealers_form(){
         log_message("info", __METHOD__);
        $this->checkAdminSession();
        $this->miscelleneous->load_nav_header();
        $this->load->view('dealers/add_dealer_form');
    }
    
    function checkAdminSession(){
         log_message("info", __METHOD__);
        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee')) {
            return TRUE;
        } else {
            redirect(base_url() . "employee/login");
        }
    }
    /**
     * @desc This is used to get city and partner list. It called by Js
     */
    function getpartner_city_list(){
        log_message("info", __METHOD__);
        $data = $this->booking_model->get_city_source();
        $data['state'] = $this->vendor_model->getall_state();
        echo json_encode($data, TRUE);
    }
    /**
     * @desc process to add dealer
     */
    function process_add_dealer(){
        log_message("info", __METHOD__);
        $this->checkAdminSession();
        $postData = $this->input->post("data");
        //check delaer exist
        $condition = array(
            "where" => array('dealer_details.dealer_phone_number_1' => $postData['dealer_phone_number_1']));
        $select = " dealer_details.dealer_id";
        
        $dealer_status = $this->dealer_model->get_dealer_mapping_details($condition, $select);
        if(empty($dealer_status)){
            $dealer_details['dealer_name'] = $postData['dealer_name'];
            $dealer_details['city'] = $postData['city'];
            $dealer_details['dealer_phone_number_1'] = $postData['dealer_phone_number_1'];
            $dealer_details['dealer_email'] = (isset($postData['dealer_email']) ? $postData['dealer_email']: NULL);
            $dealer_details['owner_name'] = (isset($postData['owner_name']) ? $postData['owner_name']: NULL);
            $dealer_details['owner_phone_number_1'] = (isset($postData['owner_phone_number_1']) ? $postData['owner_phone_number_1']: NULL);
            $dealer_details['owner_email'] =(isset($postData['owner_email']) ? $postData['owner_email']: NULL);
            $dealer_details['create_date'] = date("Y-m-d H:i:s");
            $dealer_details['state'] = $postData['state'];

            $dealer_id = $this->dealer_model->insert_dealer_details($dealer_details);
            if(!empty($dealer_id)){
               $status =  $this->add_dealer_mapping($postData, $dealer_id);
               if($status){
                    $this->create_dealer_login($postData, $dealer_id);
                    $output = array("code" => 247, "msg" => $postData['dealer_name']. " Added Sucessfully.");
                    echo json_encode($output, true);
               } else {
                   $output = array("code" => -247, "msg" => "Warning! Dealer Added but there is issues in Mapping. Please contact To 247Around Dev Team");
                   echo json_encode($output, true);
               }
            } else {
                $output = array("code" => -247, "msg" => "Warning! Dealer is not created. Please contact To 247Around Dev Team");
                echo json_encode($output, true);
            }
        } else {
            $output = array("code" => -247, "msg" => "Warning! Dealer Already Exist");
            echo json_encode($output, true);
        }
    }
    /**
     * @desc Add dealer brand mapping 
     * @param Array $postData
     * @param int $dealer_id
     * @return boolean
     */
    function add_dealer_mapping($postData, $dealer_id){
        log_message("info", __METHOD__);
        $status = "";
        $where_in = array("partner_id"=> $postData['partner_id']);
        $select = "partner_id, service_id, brand";
        $partner_data = $this->partner_model->get_partner_specific_details(array("active" => 1), $select, "service_id",$where_in );
        // don not remove $value
        for($i=0; $i < count($partner_data); $i++){
            $partner_data[$i]['dealer_id'] = $dealer_id;
            $partner_data[$i]['city'] = $postData['city'];
            $partner_data[$i]['create_date'] = date("Y-m-d H:i:s");

        }
        if(!empty($partner_data)){
            $status = $this->dealer_model->insert_dealer_mapping_batch($partner_data);
        }
        if($status){
            return true;
        } else {
            return false;
        }
    }
    /**
     * @desc Create dealer login
     * @param Array $posData
     * @return boolean
     */
    function create_dealer_login($posData, $dealer_id){
        log_message("info", __METHOD__);
        $login['user_id']  = $posData['dealer_phone_number_1'];
        $login['password'] = md5($posData['dealer_phone_number_1']."247");
        $login['clear_password'] = $posData['dealer_phone_number_1']."247";
        $login['entity'] = "dealer";
        $login['agent_name'] = $posData['dealer_name'];
        $login['entity_name'] = $posData['dealer_name'];
        $login['email'] = (isset($posData['dealer_email']) ? $posData['dealer_email']: NULL);
        $login['entity_id'] = $dealer_id;
        $login['create_date'] = date('Y-m-d H:i:s');
        $this->dealer_model->insert_entity_login($login);
        
        return true;
        
    }
    
    /**
     * @desc: This is used to get the dealer details
     * @param $dealer_id string
     * @return void
     */
    function show_dealer_list($dealer_id = ""){
        $data['dealer_id'] = $dealer_id;
        $this->miscelleneous->load_nav_header();
        $this->load->view('dealers/show_dealers_list',$data);
    }
    
    function get_dealers(){
        $post = $this->get_post_data();
        $new_post = $this->get_filtered_data($post);
        $select = "dealer_details.dealer_id,dealer_details.dealer_name,dealer_details.dealer_phone_number_1,dealer_details.city,"
                . "dealer_details.state,dealer_details.active";
        $list = $this->dealer_model->get_dealer_mapping_details($new_post,$select);
        $data = array();
        $no = $post['start'];
        foreach ($list as $dealer_list) {
            $no++;
            $dealer_list['dealer_mapping_data'] = $this->dealer_model->get_dealer_brand_mapping_details($dealer_list['dealer_id']);
            $row =  $this->dealer_table_data($dealer_list, $no);
            $data[] = $row;
        }
        $new_post['length'] = -1;
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->dealer_model->get_dealer_mapping_details($new_post,'count(distinct(dealer_details.dealer_id)) as numrows')[0]['numrows'],
            "recordsFiltered" =>  $this->dealer_model->get_dealer_mapping_details($new_post,'count(distinct(dealer_details.dealer_id)) as numrows')[0]['numrows'],
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
    
    function get_filtered_data($data){
        $dealer_id = $this->input->post('dealer_id');
        
        if(!empty($dealer_id)){
            $data['where']['dealer_details.dealer_id'] =  $dealer_id;
        }
        
//        if ($this->session->userdata('user_group') == 'regionalmanager') {
//            $states = $this->reusable_model->get_state_for_rm($this->session->userdata('id'));
//            $finalStateArray = array_column($states, 'state');
//            $data['where_in'] = array("dealer_details.state" => $finalStateArray);
//        }
        
        $data['column_order'] = array(NULL,'dealer_details.dealer_name','dealer_details.dealer_phone_number_1',NULL,NULL,NULL,NULL);
        $data['column_search'] = array('dealer_details.dealer_name','dealer_details.dealer_phone_number_1','dealer_details.city', 'public_name', 'brand');
        
        return $data;
    }
    
    function dealer_table_data($dealer_list, $no){
        $row = array();
        $dealer_partner_brand = "";
        if (!empty($dealer_list['dealer_mapping_data'])) {
            foreach ($dealer_list['dealer_mapping_data'] as $val) {
                $dealer_partner_brand .= ' <b>' . $val['public_name'] . '</b> - ' . $val['services'] . '</b> - ' . $val['brand'] . ' ,';
            }
            
            $dealer_partner_brand = rtrim($dealer_partner_brand, ",");
        }
        $row[] = $no;
        $row[] = "<a href='".base_url()."employee/dealers/edit_dealer_details/".$dealer_list['dealer_id']."'>".$dealer_list['dealer_name']."</a>";
        $row[] = $dealer_list['dealer_phone_number_1'];
        $row[] = $dealer_list['city'];
        $row[] = $dealer_list['state'];
        $row[] = $dealer_partner_brand;
        if($dealer_list['active'] === '1') {
            $row[] = "<span class='label label-success'>Active</span>";
        }else{
            $row[] = "<span class='label label-danger'>Deactivate</span>";
        }
        
        return $row;
        
    }
    
    function edit_dealer_details($dealer_id){
        $this->checkAdminSession();
        $condition = array('dealer_details.dealer_id' => $dealer_id);
        $select = "*";
        
        $data['dealer_details'] = $this->reusable_model->get_search_query('dealer_details', $select,$condition,NULL,NULL,NULL,NULL,NULL)->result_array();
        
        if(!empty($data['dealer_details'])){
            $data['dealer_city_source'] = $this->booking_model->get_city_source();
            $data['state'] = $this->vendor_model->getall_state();
            $data['dealer_partner_mapping'] = $this->dealer_model->get_dealer_brand_mapping_details($dealer_id);
            $data['dealer_partner_mapping_id']= array_unique(array_column($data['dealer_partner_mapping'], 'id'));
            $this->miscelleneous->load_nav_header();
            $this->load->view('dealers/edit_dealer_details',$data);
        }else{
            echo "No Dealer found";
        }
    }
    
    function process_edit_dealer($dealer_id){
        log_message("info", __METHOD__);
        $this->checkAdminSession();
        if(!empty($dealer_id)){
            $dealer_details['dealer_name'] = $this->input->post('dealer_name');
            $dealer_details['city'] = !empty($this->input->post('city'))?$this->input->post('city'):NULL;
            $dealer_details['dealer_phone_number_1'] = $this->input->post('dealer_phone_number_1');
            $dealer_details['dealer_email'] = !empty($this->input->post('dealer_email'))?$this->input->post('dealer_email'):NULL;
            $dealer_details['owner_name'] = !empty($this->input->post('owner_name'))?$this->input->post('owner_name'):NULL;
            $dealer_details['owner_phone_number_1'] = !empty($this->input->post('owner_phone_number_1'))?$this->input->post('owner_phone_number_1'):NULL;
            $dealer_details['owner_email'] =!empty($this->input->post('owner_email'))?$this->input->post('owner_email'):NULL;
            $dealer_details['state'] = !empty($this->input->post('state'))?$this->input->post('state'):NULL;
            //update dealer details
            $update_res = $this->dealer_model->update_dealer($dealer_details,array('dealer_id'=>$dealer_id));
            
            if(!empty($update_res)){
                //dealer partner mapping to be updated
                $dealer_partner_mapping_data = !empty($this->input->post('partner_id'))?$this->input->post('partner_id'):array();
                //initial dealer partner mapping data
                $ini_dealer_partner_mapping_data = explode(',', $this->input->post('ini_delaer_partner_mapping'));
                //get dealer partner mapping data which need to be deleted
                $del_partner_dealer_mapping_data = array_diff($ini_dealer_partner_mapping_data, $dealer_partner_mapping_data); 
                //get new dealer partner mapping data 
                $new_partner_dealer_mapping_data = array_diff($dealer_partner_mapping_data, $del_partner_dealer_mapping_data);
                
                //add new dealer partner mapping data if it is not exist in the table
                if (!empty($new_partner_dealer_mapping_data)) {
                    foreach ($new_partner_dealer_mapping_data as $value) {
                        if (!in_array($value, $ini_dealer_partner_mapping_data)) {
                            $data['partner_id'] = $value;
                            $data['city'] = $dealer_details['city'];
                            $status = $this->add_dealer_mapping($data, $dealer_id);
                            if (!empty($status)) {
                                log_message("info", "Delaer Partner Mapping Created successfully");
                            } else {
                                log_message("info", "Error In creating Delaer Partner Mapping");
                            }
                        }
                    }
                }
                
                //delete partner mapping data
                if(!empty($del_partner_dealer_mapping_data)){
                    foreach ($del_partner_dealer_mapping_data as $value){
                            $status =  $this->dealer_model->delete_dealer_brand_mapping(array('partner_id' => $value,'dealer_id' => $dealer_id));
                            if(!empty($status)){
                                log_message("info", "Delaer Partner Mapping Deleted successfully");
                            }else{
                                log_message("info", "Error In deleting Delaer Partner Mapping");
                            }
                        }      
                }
                
                $this->session->set_flashdata('success_msg','Details has been updated successfully.');
                redirect(base_url() . "employee/dealers/show_dealer_list");
            }else{
                $this->session->set_flashdata('error_msg','Error In updating Details!!! Please Try Again');
                redirect(base_url() . "employee/dealers/edit_dealer_details/$dealer_id");
            }
        }else{
            $this->session->set_flashdata('error_msg','Dealer Details Not Found');
            redirect(base_url() . "employee/dealers/show_dealer_list");
        }
    }
    
    function get_dealer_data($id = null){
        
        $this->checkAdminSession();
        if(!empty($id)){
            $where = array('dealer_id'=> trim($id));
        }else{
            $where = array();
        }
        
        $select = "dealer_name,dealer_phone_number_1";
        $data = $this->dealer_model->get_dealer_details($select,$where);
        
        if(!empty($data)){
            $res['msg'] = TRUE;
            $res['data'] = $data;
        } else {
            $res['msg'] = FALSE;
            $res['data'] = $data;
        }
        
        echo json_encode($res);
    }
   

}