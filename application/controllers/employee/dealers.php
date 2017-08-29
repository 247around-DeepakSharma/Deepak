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
            "where" => array('dealer_brand_mapping.dealer_id' => $dealer_id),
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
            "where" => array('dealer_brand_mapping.dealer_id' => $dealer_id, 'service_id' => $service_id),
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
    
    function get_category(){
         log_message("info", __METHOD__);
        $this->checkDealerSession();
        $brand = $this->input->post("brand");
        $category = $this->input->post("category");
        $service_id = $this->input->post("service_id");
        $partner_id= $this->input->post('partner_id');
        $where = array('service_id' => $service_id, "brand" => $brand, 
            'product_or_services' => 'Service', 'partner_net_payable > 0' => NULL, 'partner_id' => $partner_id);
        $select = "category"; $order_by = "category";
        $category_data = $this->service_centre_charges_model->get_service_charge_details($where, $select, $order_by);
        if(!empty($category_data)){
            $option = "<option selected disabled>Select Category</option>";
            foreach($category_data as $value){
                $option .= "<option  ";
                if(count($category_data) == 1){
                    $option .=" selected ";
                } else if($value['category'] == $category){
                    $option .="selected ";
                }
                $option .= " value = '".$value['category']."' >".$value['category']."</option>";
            }
            
            $array = array("code" => '0001', 'category' =>$option);

        } else {
             $array = array("code" => '0000');
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
		$html .= "  onclick='final_price(),set_upcountry()'" .
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
        $this->notify->sendEmail("booking@247around.com", $to, $cc, $bcc, $subject, $message, "");

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
        $post['purchase_month'] = date("M");
        $post['purchase_year'] = date("Y");
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
        $post['dealer_name'] = $this->input->post('dealer_name');
        $post['dealer_phone_number'] = $this->input->post('dealer_phone_number');
        $post['dealer_id'] = $this->session->userdata('dealer_id');
        $post['appliance_unit'] = $this->input->post("appliance_unit");
        
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
        $this->load->view('employee/header/' . $this->session->userdata('user_group'));
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
        $where_in = array("partner_id"=> $postData['partner_id']);
        $select = "partner_id, service_id, brand";
        $partner_data = $this->partner_model->get_partner_specific_details(array(), $select, "service_id",$where_in );
        // don not remove $value
        for($i=0; $i < count($partner_data); $i++){
            $partner_data[$i]['dealer_id'] = $dealer_id;
            $partner_data[$i]['city'] = $postData['city'];
            $partner_data[$i]['create_date'] = date("Y-m-d H:i:s");

        }
        $status = $this->dealer_model->insert_dealer_mapping_batch($partner_data);
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
        $select = '*';
        if($dealer_id !== ''){
            $where = array('dealer_id'=> $dealer_id);
        }else{
            $where = '';
        }
        $dealer_data = $this->dealer_model->get_dealer_details($select,$where);
        
        foreach ($dealer_data as $value){
            //Getting Appliances and Brands details for dealer
            $dealer_mapping_data[] = $this->dealer_model->get_dealer_brand_mapping_details($value['dealer_id']);
        }
        
        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
        $this->load->view('dealers/show_dealers_list',array('dealers'=>$dealer_data,'dealers_mapping'=>$dealer_mapping_data));
    }
   

}