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
    function  send_api_failed_email($email_body_data,$error_type){
        log_message('info', __METHOD__. " email_body". print_r($email_body_data, TRUE). " error type ".$error_type);
        $template = $this->booking_model->get_booking_email_template("courier_api_failed_mail");
        if (!empty($template)) {
            $subject = $template[4];
            $email_body_data .= "<br/> <br/>". json_encode($error_type, TRUE);
            $emailBody = vsprintf($template[0], $email_body_data);
            $this->notify->sendEmail($template[2], 'abhaya@247around.com,gorakhn@247around.com', '', '', $subject, $emailBody, "", 'courier_api_failed_mail');
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
        $select = "spare_parts_details.id as 'spare_id',"
                . "spare_parts_details.awb_by_partner as 'awb',spare_parts_details.courier_name_by_partner as 'courier_name',"
                . "spare_parts_details.shipped_date as 'shipped_date',spare_parts_details.booking_id,booking_details.partner_id";
        $this->create_awb_number_data($select,SPARE_SHIPPED_BY_PARTNER);
        //getting awb list from the api and process on delivered status
        $awb_number_list = $this->trackingmore_api->getTrackingsList();
        echo $awb_number_list->meta->code;
        if(!empty($awb_number_list) && $awb_number_list->meta->code == 200 ){
            //check if data is empty
            if(!empty($awb_number_list->data)){
                //do background process on api data to save it into database
                $this->insert_api_data($awb_number_list);
                
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
                        if(!empty($awb_number_to_be_deleted_from_api)){
                            $delete_status = $this->delete_awb_data_from_api($awb_number_to_be_deleted_from_api);
                            echo "DELETE AWB BY API";
                            print_r($delete_status);
                            if($delete_status['status']){
                                log_message('info','Spare details updated and awb deleted from tracking more api Delete API Response: '. print_r($delete_status,true));
                            }else{
                                log_message('info','Spare details updated but awb not deleted from tracking more Delete API Response: '. print_r($delete_status,true));
                            }

                            $awb_number_to_be_deleted_from_api = array();
                        } 
                    }
                }
                
                
            }
            log_message('info',__METHOD__.' Exit...');
        }else{
            log_message('info','api did not return success response '. print_r($awb_number_list,true));
            //send mail to developer
            $this->send_api_failed_email(json_encode($awb_number_list), array("Method" => __METHOD__));
        }
        
    }
    
    /** @desc: get real time tracking result
     * @param string $carrierCode Carrier code
     * @param string $awb_number  Tracking number 
     * @param string $spare_status spare status
     * @return array 
     */
    function get_awb_real_time_tracking_details(){
        log_message('info', __METHOD__. " POST DATA ". json_encode($this->input->post(), TRUE));
        //$this->checkUserSession();
        $carrier_code = $this->input->post('courier_code');
        $awb_number = $this->input->post('awb_number');
        $spare_status = $this->input->post('status');
        if(!empty($carrier_code) && !empty($awb_number)){
            if($spare_status){
                $api_data = $this->trackingmore_api->getRealtimeTrackingResults($carrier_code,$awb_number);
                if(!empty($api_data['data'])){
                    $data['awb_details_by_api'] = $api_data['data'];
                    $data['awb_number'] = $awb_number;
                    
                }else{
                    log_message('info',  'no data found from API for awb number '.print_r($api_data,true));
                    
                    //send mail to developer
//                    $this->send_api_failed_email(json_encode($api_data), array("Method" => __METHOD__,
//                        " AWB Number " =>$awb_number, 
//                        " CODE "=>$carrier_code, 
//                        "Status"=>$spare_status ));
                    
                    $data['awb_details_by_db'] = $this->get_awb_details($carrier_code,$awb_number);
                    $data['awb_number'] = $awb_number;
                    
                }
            }else{
                if(empty($data['awb_details_by_db'])){
                    $tracking_details = $this->trackingmore_api->getRealtimeTrackingResults($carrier_code,$awb_number);
                    $this->insert_api_data($tracking_details);
                    $data['awb_details_by_db'] = $this->get_awb_details($carrier_code,$awb_number);                    
                }
                $data['awb_number'] = $awb_number;                               
            }
            
        }else{
            $data['awb_details'] = array();
            $data['awb_number'] = $awb_number;
        }
        
        $res = $this->load->view("employee/show_awb_real_time_status",$data);
        
        echo $res;
        
    }
     /** @desc: get real time tracking result
     * @param string $carrierCode Carrier code
     * @param string $awb_number  Tracking number 
     * @param string $spare_status spare status
     * @return array 
     */
    function get_msl_awb_real_time_tracking_details(){     
       
        log_message('info', __METHOD__. " POST DATA ". json_encode($this->input->post(), TRUE));
        //$this->checkUserSession();
        $carrier_code = $this->input->post('courier_code');
        $awb_number = $this->input->post('awb_number');
        $status = $this->input->post('status');          
        if(!empty($carrier_code) && !empty($awb_number)){
            if($status){
                $api_data = $this->trackingmore_api->getRealtimeTrackingResults($carrier_code,$awb_number);
                if(!empty($api_data['data'])){
                    $data['awb_details_by_api'] = $api_data['data'];
                    $data['awb_number'] = $awb_number;
                    
                }else{
                    log_message('info',  'no data found from API for awb number '.print_r($api_data,true));
                    
                    //send mail to developer
//                    $this->send_api_failed_email(json_encode($api_data), array("Method" => __METHOD__,
//                        " AWB Number " =>$awb_number, 
//                        " CODE "=>$carrier_code, 
//                        "Status"=>$spare_status ));
                    
                    $data['awb_details_by_db'] = $this->get_awb_details($carrier_code,$awb_number);
                    $data['awb_number'] = $awb_number;
                    
                }
            }else{
                $tracking_details = $this->trackingmore_api->getRealtimeTrackingResults($carrier_code,$awb_number);
                $this->insert_api_data($tracking_details);
                $data['awb_details_by_db'] = $this->get_awb_details($carrier_code,$awb_number);
                $data['awb_number'] = $awb_number;
            }
            
        }else{
            $data['awb_details'] = array();
            $data['awb_number'] = $awb_number;
        }
        
        $res = $this->load->view("employee/show_msl_awb_real_time_status",$data);
        
        echo $res;
        
    }
    /** @desc: this function is used to create courier data on trackingMore api so that we can get updated data when we call thier api
     * @param void
     * @return void 
     */
    function create_awb_number_data($select,$status){
        $post['length'] = 2000;
        $post['start'] = 0;
        $post['select'] = $select;
        $post['where'] = array('spare_parts_details.status' => $status);
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
                $this->trackingmore_api->createTracking($val->courier_name,$val->awb,$extra_info);
            }
        }
    }
    
    /** @desc: this function is used to create courier data on trackingMore api so that we can get updated data when we call thier api
     * @param Array $data
     * @return boolean 
     */
    function delete_awb_data_from_api($data){
        log_message('info',__METHOD__.' Entering...'. print_r($data, true));
        $count = count($data);
        $x = 0;
        while($x <= $count) {
            $response = array();
            $deleteData = array_slice($data,$x,50);
            if(!empty($deleteData)){
                $api_response = $this->trackingmore_api->deleteMultipleTracking($deleteData);
                log_message('info',__METHOD__.' delete api response '. print_r($api_response,true));
                if($api_response['meta']['code'] === 200){
                    $response['status'] = TRUE;
                    $response['msg'] = $api_response['meta']['message'];
                }else{
                    $response['status'] = FALSE;
                    $response['msg'] = $api_response['meta']['message'];
                    //send mail to developer
                    $this->send_api_failed_email(json_encode($api_response), array("Method" => __METHOD__));
                }
            }else{
                $response['status'] = FALSE;
                $response['msg'] = 'Empty Data Found';
            }
            log_message('info',__METHOD__.' Return Response '. print_r($response));
            $x = $x+50;
        }
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
    function insert_api_data($api_data1) {
        log_message('info', __METHOD__ . ' Entering...'. print_r($api_data1, TRUE));
        //get api data which called in async
        $api_data = json_decode(json_encode($api_data1, true), true);
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
        print_r($data_to_insert);
        //insert data into database
        if (!empty($data_to_insert)) {
            $insert_data = $this->inventory_model->insert_courier_api_data($data_to_insert);
            
            if ($insert_data) {
                log_message('info', __METHOD__ . ' api data inserted successfully');
            } else {
                log_message('info', __METHOD__ . ' error in inserting api data : ' . print_r($data_to_insert, true));
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
        log_message('info', __METHOD__. " Courier Code ". $carrier_code. " AWB NO ". $awb_number);
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
        log_message('info', __METHOD__. " ". print_r($data, TRUE));
        $res = FALSE;

        $parts_details = explode('/', $data->order_id);
        if (!empty($parts_details)) {

            $tmp_arr = array();
            $tmp_arr['status'] = SPARE_DELIVERED_TO_SF;
            $tmp_arr['acknowledge_date'] = date('Y-m-d');
            $tmp_arr['auto_acknowledeged'] = 2;

            $getsparedata = $this->partner_model->get_spare_parts_by_any("spare_parts_details.id, spare_parts_details.booking_id, spare_parts_details.status", array("spare_parts_details.id" => $parts_details[0], "spare_parts_details.status" => SPARE_SHIPPED_BY_PARTNER));
            print_r($getsparedata);
            if (!empty($getsparedata)) {
                echo "update Data";
                //auto acknowledge spare by updating status in spare parts table and setting auto_acknowledge flag 2 for the api
                $update_status = $this->service_centers_model->update_spare_parts(array('id' => $parts_details[0]), $tmp_arr);

                if ($update_status) {
                    $actor = $next_action = NULL;
                    log_message('info', ' Spare Details updated for spare id ' . $parts_details[0]);
                    $is_requested = $this->partner_model->get_spare_parts_by_any("spare_parts_details.id, spare_parts_details.status, spare_parts_details.booking_id", array('booking_id' => $parts_details[0], 'status' => SPARE_SHIPPED_BY_PARTNER));
                    if (empty($is_requested)) {
                        if (date('l' == 'Sunday')) {
                            $booking['booking_date'] = date('d-m-Y', strtotime("+1 days"));
                        } else if (date('H') > 12) {
                            $booking['booking_date'] = date('d-m-Y', strtotime("+1 days"));
                        } else {
                            $booking['booking_date'] = date('d-m-Y');
                        }
                        $booking['update_date'] = date("Y-m-d H:i:s");
                        $booking['internal_status'] = SPARE_DELIVERED_TO_SF;

                        $partner_status = $this->booking_utilities->get_partner_status_mapping_data(_247AROUND_PENDING, SPARE_DELIVERED_TO_SF, $parts_details[1], $parts_details[2]);
                        
                        if (!empty($partner_status)) {
                            $booking['partner_current_status'] = $partner_status[0];
                            $booking['partner_internal_status'] = $partner_status[1];
                            $actor = $booking['actor'] = $partner_status[2];
                            $next_action = $booking['next_action'] = $partner_status[3];
                        }
                        $this->booking_model->update_booking($parts_details[2], $booking);
                        $this->miscelleneous->send_spare_delivered_sms_to_customer($parts_details[0], $parts_details[2]);

                         $this->notify->insert_state_change($parts_details[2], SPARE_DELIVERED_TO_SF, _247AROUND_PENDING, DELIVERY_CONFIRMED_WITH_COURIER, _247AROUND_DEFAULT_AGENT, _247AROUND, $actor, $next_action, _247AROUND);

                         $sc_data['current_status'] = _247AROUND_PENDING;
                         $sc_data['internal_status'] = SPARE_DELIVERED_TO_SF;
                         $sc_data['update_date'] = date("Y-m-d H:i:s");
                         $this->vendor_model->update_service_center_action($parts_details[2], $sc_data);
                         if($parts_details[2]){
                            $cb_url = base_url() . "employee/do_background_process/send_request_for_partner_cb/" . $parts_details[2];
                            $pcb = array();
                            $this->asynchronous_lib->do_background_process($cb_url, $pcb);
                         }
                         $res = TRUE;
                    } else {
                        $this->notify->insert_state_change($parts_details[2], SPARE_DELIVERED_TO_SF, _247AROUND_PENDING, DELIVERY_CONFIRMED_WITH_COURIER, _247AROUND_DEFAULT_AGENT, _247AROUND, $actor, $next_action, _247AROUND);
                        if($parts_details[2]){
                            $cb_url = base_url() . "employee/do_background_process/send_request_for_partner_cb/" . $parts_details[2];
                            $pcb = array();
                            $this->asynchronous_lib->do_background_process($cb_url, $pcb);
                        }
                        $res = TRUE;

                    }
                }
            } else {
                $res = TRUE;
                echo " STATUS CHANGED ";
                print_r($parts_details);
            }
        }

        return $res;
    }
    /*
     * This function is used to update data for recieved defactive part by partner
     */
    function update_defactive_part_status($data){
        log_message('info', __FUNCTION__ ."start with data".print_r($data,FALSE));
        $res = FALSE;
        $parts_details = explode('/', $data->order_id);
        if (!empty($parts_details)) {
            $booking_id = $parts_details[2];
            $spare_id = $parts_details[0];
            $awb_number = $data->tracking_number;
            $getsparedata = $this->partner_model->get_spare_parts_by_any("spare_parts_details.id, booking_id, status", array("spare_parts_details.id" => $spare_id, "status" => DEFECTIVE_PARTS_SHIPPED, 
                "defactive_part_received_date_by_courier_api IS NULL"=>NULL));
            if (!empty($getsparedata)) {
                $response = $this->service_centers_model->update_spare_parts(array('booking_id' => $booking_id,"awb_by_sf"=>$awb_number), array('defactive_part_received_date_by_courier_api' => date("Y-m-d H:i:s")));
                if ($response) {
                    $this->notify->insert_state_change($booking_id, DEFECTIVE_PARTS_RECEIVED_API_CONFORMATION, DEFECTIVE_PARTS_SHIPPED, DELIVERY_CONFIRMED_WITH_COURIER, _247AROUND_DEFAULT_AGENT, _247AROUND, "Partner", "Approve or Reject the part", _247AROUND);
                    $res = TRUE;
                }
                else{
                    log_message('info', __FUNCTION__ ."Combination of booking_id and awb_by_sf was not available".$booking_id."_".$awb_number);
                }
            }
        }
        else{
           log_message('info', __FUNCTION__ ."order_id is empty"); 
        }
        return $res;
    }
       /** @desc:List all details of the courier awb number
     *  @param int $numbers Tracking numbers,eg:$awb_numbers = LY044217709CN,UG561422482CN (optional)
     *  @param int $orders Tracking order,eg:$orders = #123 (optional)
     *  @return: view
     */
    function auto_acknowledge_defactive_part_shipped_by_sf(){
        log_message('info',__METHOD__.' Entering...');
        //update trackingmore data by creating new awb number from spare part details
        $select = "spare_parts_details.id as 'spare_id',"
                . "spare_parts_details.awb_by_sf as 'awb',spare_parts_details.courier_name_by_sf as 'courier_name',"
                . "spare_parts_details.defective_part_shipped_date as 'shipped_date',spare_parts_details.booking_id,booking_details.partner_id";
        $this->create_awb_number_data($select,DEFECTIVE_PARTS_SHIPPED);
        //getting awb list from the api and process on delivered status
        $awb_number_list = $this->trackingmore_api->getTrackingsList();
        //echo $awb_number_list->meta->code;
        if(!empty($awb_number_list) && $awb_number_list->meta->code == 200 ){
            //check if data is empty
            if(!empty($awb_number_list->data)){
                //do background process on api data to save it into database
                $this->insert_api_data($awb_number_list);
                $awb_number_to_be_deleted_from_api = array();
                //make array of all delivered data so that we can update status of that spare
                foreach ($awb_number_list->data->items as $key => $value){
                    if($value->status == 'delivered'){
                        $update_status = $this->update_defactive_part_status($value);
                        if($update_status){
                            log_message('info','Spare Status Updated Successfully for awb number '.$value->tracking_number);
                            $deleted_awb_number_tmp_arr = array();
                            $deleted_awb_number_tmp_arr['tracking_number'] = $value->tracking_number;
                            $deleted_awb_number_tmp_arr['carrier_code'] = $value->carrier_code;
                            $awb_number_to_be_deleted_from_api[] = $deleted_awb_number_tmp_arr;
                        }
                        if(!empty($awb_number_to_be_deleted_from_api)){
                            $delete_status = $this->delete_awb_data_from_api($awb_number_to_be_deleted_from_api);
                            echo "DELETE AWB BY API";
                            print_r($delete_status);
                            if($delete_status['status']){
                                log_message('info','Spare details updated and awb deleted from tracking more api Delete API Response: '. print_r($delete_status,true));
                            }else{
                                log_message('info','Spare details updated but awb not deleted from tracking more Delete API Response: '. print_r($delete_status,true));
                            }

                            $awb_number_to_be_deleted_from_api = array();
                        } 
                    }
                }
            }
            log_message('info',__METHOD__.' Exit...');
        }else{
            log_message('info','api did not return success response '. print_r($awb_number_list,true));
            //send mail to developer
            $this->send_api_failed_email(json_encode($awb_number_list), array("Method" => __METHOD__));
        }
        
    }
    
    /** @desc:List all details of the courier awb number
     *  @param int $numbers Tracking numbers,eg:$awb_numbers = LY044217709CN,UG561422482CN (optional)
     *  @param int $orders Tracking order,eg:$orders = #123 (optional)
     *  @return: view
     */
    
    function process_msl_courier_tracking() {
        log_message('info', __METHOD__ . ' Entering...');
        //update trackingmore data by creating new awb number from spare part details
        $select = "courier_details.id as 'courier_id',"
                . "courier_details.AWB_no as 'awb',courier_details.courier_name,"
                . "courier_details.shipment_date as 'shipped_date',courier_details.booking_id,courier_details.sender_entity_id as 'partner_id'";
        $this->create_awb_number_for_msl($select, COURIER_DETAILS_STATUS);
        //getting awb list from the api and process on pick-up status
        $awb_number_list = $this->trackingmore_api->getTrackingsList();
        echo $awb_number_list->meta->code;

        if (!empty($awb_number_list) && $awb_number_list->meta->code == 200) {
            //check if data is empty    

            if (!empty($awb_number_list->data)) {
                //do background process on api data to save it into database
                $this->insert_api_data($awb_number_list);
                $awb_number_to_be_deleted_from_api = array();
                //make array of all delivered data so that we can update status of that spare
                foreach ($awb_number_list->data->items as $key => $value) {
                    $order_id_detail_arr = explode('/', $value->order_id);
                    $tracking_type = $order_id_detail_arr[0];
                    $courier_details_id = $order_id_detail_arr[1];
                    $partner_id = $order_id_detail_arr[2];
                    if ($tracking_type == 'MSL') {
                        echo " FOr each update " . $key . PHP_EOL;
                        $data = array('status' => $value->status);
                        $where = array('id' => $courier_details_id);
                        $update_status = $this->inventory_model->update_courier_detail($where, $data);
                        if ($update_status) {
                            log_message('info', 'Courier Status Updated Successfully for awb number ' . $value->tracking_number);
                            if ($value->status == 'delivered') {
                                $deleted_awb_number_tmp_arr = array();
                                $deleted_awb_number_tmp_arr['tracking_number'] = $value->tracking_number;
                                $deleted_awb_number_tmp_arr['carrier_code'] = $value->carrier_code;
                                $awb_number_to_be_deleted_from_api[] = $deleted_awb_number_tmp_arr;
                            }
                        }
                        if (!empty($awb_number_to_be_deleted_from_api)) {
                            $delete_status = $this->delete_awb_data_from_api($awb_number_to_be_deleted_from_api);
                            echo "DELETE AWB BY API";
                            print_r($delete_status);
                            if ($delete_status['status']) {
                                log_message('info', 'Courier details updated and awb deleted from tracking more api Delete API Response: ' . print_r($delete_status, true));
                            } else {
                                log_message('info', 'Courier details updated but awb not deleted from tracking more Delete API Response: ' . print_r($delete_status, true));
                            }

                            $awb_number_to_be_deleted_from_api = array();
                        }
                    }
                }
            }
            log_message('info', __METHOD__ . ' Exit...');
        } else {
            log_message('info', 'api did not return success response ' . print_r($awb_number_list, true));
            //send mail to developer
            $this->send_api_failed_email(json_encode($awb_number_list), array("Method" => __METHOD__));
        }
    }

    /** @desc: this function is used to create courier data on trackingMore api so that we can get updated data when we call thier api
     * @param void
     * @return void 
     */
    function create_awb_number_for_msl($select, $status) {
        $where = array('courier_details.status' => $status);
        $courier_data = $this->inventory_model->get_courier_details($select, $where);
        if (!empty($courier_data)) {
            foreach ($courier_data as $key => $val) {
                $extra_info = array();
                echo $key . PHP_EOL;
                $extra_info['order_id'] = 'MSL/' . $val['courier_id'] . '/' . $val['partner_id'];
                $extra_info['tracking_ship_date'] = $val['shipped_date'];
                $this->trackingmore_api->createTracking($val['courier_name'], $val['awb'], $extra_info);
            }
        }
    }
    
}
