<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

error_reporting(E_ALL);
ini_set('display_errors', '1');

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 36000);


class Convert_query_to_booking extends CI_Controller {
    
    function __Construct() {
        parent::__Construct();
        
        $this->load->model('booking_model');
        $this->load->helper(array('form', 'url','array'));
        $this->load->library("initialized_variable");
        $this->load->library("miscelleneous");
    }
    
    function convert($partner_id) {
        log_message('info', __METHOD__);
        if (!empty($partner_id)) {
            $query = $this->booking_model->get_bookings_count_by_any("booking_details.city, booking_date, booking_id, order_id, booking_pincode, service_id, partner_id", array('current_status' => _247AROUND_FOLLOWUP,
                'partner_id' => $partner_id));
            
            $this->initialized_variable->fetch_partner_data($partner_id);
            $partner_data = $this->initialized_variable->get_partner_data();
            if (!empty($query)) {
                foreach ($query as $value) {
                    log_message('info', __METHOD__. " Booking ID ". $value['booking_id']);                        
                    echo $value['booking_id']; 
                    $booking = array();
                    if (strpos($value['booking_id'], "Q-") !== FALSE) {
                        $booking_id_array = explode("Q-", $value['booking_id']);
                        $booking['booking_id'] = $booking_id_array[1];
                        $booking['internal_status'] = 'Scheduled';
                        $booking['current_status'] = _247AROUND_PENDING;
                        $booking['type'] = "Booking";
                        $actor = $next_action = NULL;
                        
                        $partner_status = $this->booking_utilities->get_partner_status_mapping_data($booking['current_status'], $booking['internal_status'], $value['partner_id'], $value['booking_id']);
                        if (!empty($partner_status)) {               
                            $booking['partner_current_status'] = $partner_status[0];
                            $booking['partner_internal_status'] = $partner_status[1];
                            $actor = $booking['actor'] = $partner_status[2];
                            $next_action = $booking['next_action'] = $partner_status[3];
                        }
                        
                        if(empty($value['booking_date'])){
                            if(date('H' > 12)){
                                $booking_date = date('d-m-Y', strtotime("+1 day"));
                            } else {
                                $booking_date = date('d-m-Y');
                            }
                            $booking['booking_date'] =  $booking['initial_booking_date'] = date('d-m-Y', strtotime($booking_date));
                        }
                        
                        $s = $this->booking_model->update_booking($value['booking_id'], $booking);
                        
                        if(!empty($s)){
                            echo 'Bookings Converted';
                            log_message('info', __METHOD__. " Bookings Converted Booking ID ". $value['booking_id']);

                            $this->notify->insert_state_change($booking['booking_id'], _247AROUND_PENDING, _247AROUND_FOLLOWUP, "Booking Converted", _247AROUND_DEFAULT_AGENT, _247AROUND_DEFAULT_AGENT_NAME,
                    $actor,$next_action,_247AROUND);
                            
                            $unit_num = $this->booking_model->update_booking_unit_details_by_any(array('booking_id' => $value['booking_id']), 
                                    array('booking_status' => _247AROUND_PENDING, 'booking_id' => $booking['booking_id']));

                            if($unit_num){
                                log_message('info', __METHOD__. " Unit Bookings Converted Booking ID ". $value['booking_id']);
                                echo 'Unit Bookings Converted';
                                $upcountry_data = $this->miscelleneous->check_upcountry_vendor_availability($value['city'], $value['booking_pincode'], $value['service_id'], $partner_data, "");
                                if (!empty($upcountry_data)) {
                                    if(isset($upcountry_data['vendor_id'])){
                                        switch ($upcountry_data['message']) {
                                            case UPCOUNTRY_BOOKING:
                                            case UPCOUNTRY_LIMIT_EXCEED:
                                            case NOT_UPCOUNTRY_BOOKING:
                                            case UPCOUNTRY_DISTANCE_CAN_NOT_CALCULATE:
                                                 echo 'Assign Vendor';
                                                log_message('info', __METHOD__. " Assign Vendor Booking ID ". $value['booking_id']);
                                                $url = base_url() . "employee/vendor/process_assign_booking_form/";
                                                $async_data['service_center'] = array($booking['booking_id'] => $upcountry_data['vendor_id']);
                                                $async_data['agent_id'] = _247AROUND_DEFAULT_AGENT;
                                                $async_data['agent_name'] = _247AROUND_DEFAULT_AGENT_NAME;
                                                $async_data['agent_type'] = _247AROUND_EMPLOYEE_STRING;
                                                $b_id = $booking['booking_id'];
                                                $async_data["partner_id[$b_id]"] = $partner_id;
                                                $async_data["order_id"] = array($booking['booking_id'] =>$value['order_id']);
                                                $this->asynchronous_lib->do_background_process($url, $async_data);

                                                break;
                                            case SF_DOES_NOT_EXIST:
                                                break;
                                        }

                                    }
                                }
                            }
                        }
                    } 
                }
            }
        }
    }

}