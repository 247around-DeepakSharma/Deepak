<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Courier_tracking extends CI_Controller {
    
    /**
     * load list modal and helpers
     */
    function __Construct() {
        parent::__Construct();
        
        $this->load->library("trackingmore_api");
        $this->load->library("miscelleneous");
        $this->load->library("notify");
        $this->load->library("asynchronous_lib");
        
        //load model
        $this->load->model('inventory_model');
        $this->load->model('service_centers_model');
        $this->load->model("partner_model");
    }
    
    /**
     * @desc: Check user Seession
     * @return boolean
     */
    function checkUserSession(){
        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee')) {
	    return TRUE;
	} else {
	    redirect(base_url() . "employee/login");
	}
    }
    
    /**
     * @desc: send email to developer when api failed or return unexpected response
     * @return void
     */
    function  send_api_failed_email($email_body_data){
        $template = $this->booking_model->get_booking_email_template("courier_api_failed_mail");
        if (!empty($template)) {
            $subject = $template[4];
            $emailBody = vsprintf($template[0], $email_body_data);
            $this->notify->sendEmail($template[2], DEVELOPER_EMAIL, '', '', $subject, $emailBody, "", 'courier_api_failed_mail');
        }
    }


    /** @desc:List all courier supported by trackon api
     *  @param: void
     *  @return: view
     */
    function get_all_courier_list(){
        $this->checkUserSession();
        $data['courier_data'] = $this->trackingmore_api->getCarrierList();
        $this->miscelleneous->load_nav_header();
        $this->load->view("employee/show_all_courier_list", $data);
    }
    
    /** @desc:List all details of the courier awb number
     *  @param int $numbers Tracking numbers,eg:$awb_numbers = LY044217709CN,UG561422482CN (optional)
     *  @param int $orders Tracking order,eg:$orders = #123 (optional)
     *  @return: view
     */
    function auto_acknowledge_spare_shipped_by_partner(){
        log_message('info',__METHOD__.' Entering...');
        //update trackingmore data by creating new awb number from spare part details
        $this->create_awb_number_data();
        //getting awb list from the api and process on delivered status
        $awb_number_list = $this->trackingmore_api->getTrackingsList();
        echo $awb_number_list->meta->code;
        if(!empty($awb_number_list) && $awb_number_list->meta->code == 200 ){
            //check if data is empty
            if(!empty($awb_number_list->data)){
                //do background process on api data to save it into database
                $this->do_background_process_on_api_response($awb_number_list);
                
                $awb_number_to_be_deleted_from_api = array();
                //make array of all delivered data so that we can update status of that spare
                foreach ( $awb_number_list->data->items as $key => $value){
                    if($value->status == 'delivered'){
                        echo " FOr each update ". $key.PHP_EOL;
                        $update_status = $this->process_partner_shipped_auto_acknowledge_data($value);
                       
                        if($update_status){
                            log_message('info','Spare Status Updated Successfully for awb number '.$value->tracking_number);
                            $deleted_awb_number_tmp_arr = array();
                            $deleted_awb_number_tmp_arr['tracking_number'] = $value->tracking_number;
                            $deleted_awb_number_tmp_arr['carrier_code'] = $value->carrier_code;
                            $awb_number_to_be_deleted_from_api[] = $deleted_awb_number_tmp_arr;
                        }
                    }
                }
                
                if(!empty($awb_number_to_be_deleted_from_api)){
                    //delete all the delivered data from the trackingmore api
                    $delete_status = $this->delete_awb_data_from_api($awb_number_to_be_deleted_from_api);
                    print_r($delete_status);
                    if($delete_status['status']){
                        log_message('info','Spare details updated and awb deleted from tracking more api Delete API Response: '. print_r($delete_status,true));
                    }else{
                        log_message('info','Spare details updated but awb not deleted from tracking more Delete API Response: '. print_r($delete_status,true));
                    }
                }
            }
            log_message('info',__METHOD__.' Exit...');
        }else{
            log_message('info','api did not return success response '. print_r($awb_number_list,true));
            //send mail to developer
            $this->send_api_failed_email(json_encode($awb_number_list));
        }
        
    }
    
    /** @desc: get real time tracking result
     * @param string $carrierCode Carrier code
     * @param string $awb_number  Tracking number 
     * @param string $spare_status spare status
     * @return array 
     */
    function get_awb_real_time_tracking_details($carrier_code,$awb_number,$spare_status){
        $this->checkUserSession();
        if(!empty($carrier_code) && !empty($awb_number) && !empty($spare_status)){
            
            if($spare_status === SPARE_SHIPPED_BY_PARTNER){
                $api_data = $this->trackingmore_api->getRealtimeTrackingResults($carrier_code,$awb_number);
                if(!empty($api_data['data'])){
                    $data['awb_details_by_api'] = $api_data['data'];
                    $data['awb_number'] = $awb_number;
                }else{
                    log_message('info',  'no data found from API for awb number '.print_r($api_data,true));
                    
                    //send mail to developer
                    $this->send_api_failed_email(json_encode($api_data));
                    
                    $data['awb_details_by_db'] = $this->get_awb_details($carrier_code,$awb_number);
                    $data['awb_number'] = $awb_number;
                    
                }
            }else{
                $data['awb_details_by_db'] = $this->get_awb_details($carrier_code,$awb_number);
                $data['awb_number'] = $awb_number;
            }
            
        }else{
            $data['awb_details'] = array();
            $data['awb_number'] = $awb_number;
        }
        
        $res = $this->load->view("employee/show_awb_real_time_status",$data);
        
        echo $res;
        
    }
    
    /** @desc: this function is used to create courier data on trackingMore api so that we can get updated data when we call thier api
     * @param void
     * @return void 
     */
    function create_awb_number_data(){
        $post['length'] = 200;
        $post['start'] = 0;
        $post['select'] = "spare_parts_details.id as 'spare_id',"
                . "spare_parts_details.awb_by_partner,spare_parts_details.courier_name_by_partner,"
                . "spare_parts_details.shipped_date,spare_parts_details.booking_id,booking_details.partner_id";
        $post['where'] = array('spare_parts_details.status' => SPARE_SHIPPED_BY_PARTNER);
        $spare_data = $this->inventory_model->get_spare_parts_query($post);
        if(!empty($spare_data)){
            foreach ($spare_data as $key => $val) {
                $extra_info = array();
                echo $key.PHP_EOL;
                //here we create temp order id by using spareid,partnerid,booking_id
                //when we get the list of tracking while auto acknowledging parts then
                //at that time we don't need to go to database to get the details of the parts
                $extra_info['order_id'] = $val->spare_id.'/'.$val->partner_id.'/'.$val->booking_id;
                $extra_info['tracking_ship_date'] = $val->shipped_date;
                $this->trackingmore_api->createTracking($val->courier_name_by_partner,$val->awb_by_partner,$extra_info);
            }
        }
    }
    
    /** @desc: this function is used to create courier data on trackingMore api so that we can get updated data when we call thier api
     * @param Array $data
     * @return boolean 
     */
    function delete_awb_data_from_api($data){
        log_message('info',__METHOD__.' Entering...');
        $response = array();
        if(!empty($data)){
            $api_response = $this->trackingmore_api->deleteMultipleTracking($data);
            log_message('info',__METHOD__.' delete api response '. print_r($api_response,true));
            if($api_response['meta']['code'] === 200){
                $response['status'] = TRUE;
                $response['msg'] = $api_response['meta']['message'];
            }else{
                $response['status'] = FALSE;
                $response['msg'] = $api_response['meta']['message'];
                //send mail to developer
                $this->send_api_failed_email(json_encode($api_response));
            }
        }else{
            $response['status'] = FALSE;
            $response['msg'] = 'Empty Data Found';
        }
        
        log_message('info',__METHOD__.' Return Response '. print_r($response));
        return $response;
        
    }
    
    /** @desc: this function is used to call function in background
     * @param Array $data
     * @return void 
     */
    function do_background_process_on_api_response($data){
        log_message('info',__METHOD__.' Entering...');
        $url = base_url() . "courier_tracking/insert_api_data";
        $params['api_data'] = $data;
        $this->asynchronous_lib->do_background_process($url, $params);
    }
    
    
    /** @desc: this function is used to process the api data and save the new details of api into database
     * @param void
     * @return void 
     */
    function insert_api_data() {
        log_message('info', __METHOD__ . ' Entering...');
        //get api data which called in async
        $api_data = $this->input->post('api_data');
        $data_to_insert = array();
        
        //process api_data to insert it into database
        foreach ($api_data['data']['items'] as $value) {
            $tmp_arr = array();
            $tmp_arr['api_id'] = $value['id'];
            $tmp_arr['awb_number'] = $value['tracking_number'];
            $tmp_arr['carrier_code'] = $value['carrier_code'];
            $tmp_arr['spare_id'] = $value['order_id'];
            $tmp_arr['final_status'] = $value['status'];
            $tmp_arr['create_date'] = date('Y-m-d H:i:s');
            if (isset($value['origin_info']['trackinfo']) && !empty($value['origin_info']['trackinfo'])) {
                foreach ($value['origin_info']['trackinfo'] as $val) {
                    $status_tmp_array = array();
                    $status_tmp_array['checkpoint_status'] = $val['checkpoint_status'];
                    $status_tmp_array['checkpoint_status_details'] = $val['Details'];
                    $status_tmp_array['checkpoint_status_description'] = $val['StatusDescription'];
                    $status_tmp_array['checkpoint_status_date'] = $val['Date'];
                    $status_tmp_array['checkpoint_item_node'] = isset($val['ItemNode'])?$val['ItemNode']:NULL;
                    $merge_array = array_merge($tmp_arr, $status_tmp_array);
                    array_push($data_to_insert, $merge_array);
                }
            }
        }

        //insert data into database
        if (!empty($data_to_insert)) {
            $insert_data = $this->inventory_model->insert_courier_api_data($data_to_insert);
            
            if ($insert_data) {
                log_message('info', __METHOD__ . ' api data inserted successfully');
            } else {
                log_message('info', __METHOD__ . ' error in inserting api data : ' . print_r($data_to_insert));
            }
        }else{
            log_message('info', __METHOD__ . ' No new data found to insert...');
        }
    }
    
    
    /** @desc: this function is used to get awb_number details from database
     * @param string $carrierCode Carrier code
     * @param string $awb_number  Tracking number 
     * @return array 
     */
    function get_awb_details($carrier_code,$awb_number){
        $return_data = array();
        
        if(!empty($carrier_code) && !empty($awb_number)){
            $select = "carrier_code,checkpoint_status,checkpoint_status_details,checkpoint_status_description,checkpoint_status_date,final_status,checkpoint_item_node";
            $where = array('awb_number' => $awb_number,
                           'carrier_code' => $carrier_code
                );
            
            $data = $this->inventory_model->get_awb_shippment_details($select,$where);
            
            if(!empty($data)){
                $return_data = $data;
            }
        }
        
        return $return_data;
    }
    
    
    /**
     * @desc: This function is used to get courier services details like courier name, courier code
     * @params: void
     * @return: JSON $res
     */
    function get_courier_services_details(){
        $select = '*';
        $data = $this->inventory_model->get_get_courier_services($select);
        
        if(!empty($data)){
            $res['status'] = TRUE;
            $res['msg'] = $data[0];
        }else{
            $res['status'] = FALSE;
            $res['msg'] = 'No Data Found';
        }
        
        echo json_encode($res);
    }
    
    
    /**
     * @desc: This function is used to process the auto acknowledge parts shipped by partner
     * @params: objects array()
     * @return: boolean
     */
    function process_partner_shipped_auto_acknowledge_data($data) {
        $res = FALSE;

        $parts_details = explode('/', $data->order_id);
        if (!empty($parts_details)) {

            $tmp_arr = array();
            $tmp_arr['status'] = SPARE_DELIVERED_TO_SF;
            $tmp_arr['acknowledge_date'] = date('Y-m-d');
            $tmp_arr['auto_acknowledeged'] = 2;

            $getsparedata = $this->partner_model->get_spare_parts_by_any("spare_parts_details.id, booking_id, status",array("spare_parts_details.id" => $parts_details[0], "status" => SPARE_SHIPPED_BY_PARTNER));
             print_r($getsparedata);
            if (!empty($getsparedata)) {
                echo "update Data";
                //auto acknowledge spare by updating status in spare parts table and setting auto_acknowledge flag 2 for the api
                $update_status = $this->service_centers_model->update_spare_parts(array('id' => $parts_details[0]), $tmp_arr);

                if ($update_status) {
                    log_message('info', ' Spare Details updated for spare id ' . $parts_details[0]);
                    $booking['booking_date'] = date('d-m-Y', strtotime('+1 days'));
                    $booking['update_date'] = date("Y-m-d H:i:s");
                    $booking['internal_status'] = SPARE_PARTS_DELIVERED;

                    $partner_status = $this->booking_utilities->get_partner_status_mapping_data(_247AROUND_PENDING, SPARE_PARTS_DELIVERED, $parts_details[1], $parts_details[2]);
                    $actor = $next_action = 'not_define';
                    if (!empty($partner_status)) {
                        $booking['partner_current_status'] = $partner_status[0];
                        $booking['partner_internal_status'] = $partner_status[1];
                        $actor = $booking['actor'] = $partner_status[2];
                        $next_action = $booking['next_action'] = $partner_status[3];
                    }
                    $b_status = $this->booking_model->update_booking($parts_details[2], $booking);
                    $state_change['actor'] = $actor;
                    $state_change['next_action'] = $next_action;
                    if ($b_status) {
                        $state_change['booking_id'] = $parts_details[2];
                        $state_change['new_state'] = SPARE_PARTS_DELIVERED;

                        $booking_state_change = $this->booking_model->get_booking_state_change($state_change['booking_id']);

                        if ($booking_state_change > 0) {
                            $state_change['old_state'] = $booking_state_change[count($booking_state_change) - 1]['new_state'];
                        } else { //count($booking_state_change)
                            $state_change['old_state'] = "Pending";
                        }

                        $state_change['agent_id'] = _247AROUND_DEFAULT_AGENT;
                        $state_change['partner_id'] = _247AROUND;
                        $state_change['remarks'] = DELIVERY_CONFIRMED_WITH_COURIER;

                        // Insert data into booking state change
                        $this->booking_model->insert_booking_state_change($state_change);

                        $sc_data['current_status'] = "Pending";
                        $sc_data['internal_status'] = SPARE_PARTS_DELIVERED;
                        $sc_data['update_date'] = date("Y-m-d H:i:s");
                        $this->vendor_model->update_service_center_action($parts_details[2], $sc_data);
                        $cb_url = base_url() . "employee/do_background_process/send_request_for_partner_cb/" . $parts_details[2];
                        $pcb = array();
                        $this->asynchronous_lib->do_background_process($cb_url, $pcb);
                    } else {

                        //reverse the change in the spare part table
                        $tmp_arr = array();
                        $tmp_arr['status'] = SPARE_SHIPPED_BY_PARTNER;
                        $tmp_arr['acknowledge_date'] = NULL;
                        $tmp_arr['auto_acknowledeged'] = NULL;

                        $this->service_centers_model->update_spare_parts(array('id' => $parts_details[0]), $tmp_arr);
                    }
                }
            }
        }

        return $res;
    }

}