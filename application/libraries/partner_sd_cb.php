<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

//Test Data
//define('SD_CB_URL', 'http://stg-apigateway-1119639262.ap-southeast-1.elb.amazonaws.com:80/sts/api/sts/updateServiceStatus');
//define('X_AUTH_TOKEN', '8f28b7d068f642858bca0f0af574ad55');
//define('X_SELLER_AUTHZ_TOKEN', '9d002821-b47d-4763-9375-50c78ea0bcd5');
//define('VENDOR_CODE', '0a247b');
//Actual Data - pull them from DB later
//define('SD_CB_URL', 'https://apigateway.snapdeal.com:443/sts/api/sts/updateServiceStatus');
//define('X_AUTH_TOKEN', '869fcb5e31ce4a94b96b372a2fbc583c');
//define('X_SELLER_AUTHZ_TOKEN', '2ed5c582-eaef-4e2e-9a2c-f2459271c8db');
//define('VENDOR_CODE', 'S9f330');
//
//
//define('ERR_ORDER_ID_NOT_FOUND_CODE', -1007);
//define('ERR_ORDER_ID_NOT_FOUND_MSG', 'Order ID Not Found');

/**
 * Partner Snapdeal Callback APIs for Status Updates
 *
 * This will be called from our Controllers like Booking when an action is
 * performed on a booking so that the status update happens in the Partner
 * CRM as well.
 *
 * @author anujaggarwal
 */
class partner_sd_cb {

    private $My_CI;
    private $header = null;
    private $partner = null;
    private $requestUrl = '';
    private $jsonRequestData = null;
    private $jsonResponseString;

    function __Construct() {
        $this->My_CI = & get_instance();

        $this->My_CI->load->model('partner_model');
    }

    //Call SD status update API when booking is scheduled
    public function update_status_schedule_booking($data) {
        log_message('info', __METHOD__ . "=> Booking ID: " . $data['booking_id']);
        $this->requestUrl = __METHOD__;

        if ($data['order_id'] != NULL) {
            $this->partner = $data['partner_id'];

            //Update status, remarks, start & end date
            //Get start and end dates
            $postData = array(
                "vendorCode" => VENDOR_CODE,
                "caseId" => $data['booking_id'],
                "orderId" => $data['order_id'],
                "vendorStatus" => $data['partner_current_status'],
                "remarks" => $data['internal_status'],
                "caseStatus" =>$data['partner_internal_status']
            );
            
            if($data['current_status'] == 'Pending'){
                $delDate = $this->getStartEndDate(date('Y-m-d H:i:s', strtotime($data['booking_date'])), $data['booking_timeslot']);
                $postData['startDate'] = $delDate['tsStart'];
                $postData['endDate'] = $delDate['tsEnd'];
            }

            return $this->post_data($postData);
        } else {
            log_message('info', __METHOD__ . "=> Order ID Not Found");

            $this->jsonRequestData = json_encode($data);
            $this->jsonResponseString['response'] = ERR_ORDER_ID_NOT_FOUND_MSG;
            $this->jsonResponseString['error'] = ERR_ORDER_ID_NOT_FOUND_CODE;

            $responseData = array("data" => $this->jsonResponseString);

            $activity = array(
                'activity' => $this->requestUrl,
                'json_request_data' => $this->jsonRequestData,
                'json_response_string' => json_encode($responseData, JSON_UNESCAPED_SLASHES));

            $this->My_CI->partner_model->log_partner_activity($activity);

            return FALSE;
        }
    }

    //Call SD status update API when query/booking is cancelled
    public function update_status_cancel_booking($data) {
        log_message('info', __METHOD__ . "=> Booking ID: " . $data['booking_id']);
        $this->requestUrl = __METHOD__;

        if ($data['order_id'] != NULL) {
            $this->partner = $data['partner_id'];
            
            
            //Update status, remarks
            $postData = array(
                "vendorCode" => VENDOR_CODE,
                "caseId" => $data['booking_id'],
                "orderId" => $data['order_id'],
                "vendorStatus" => $data['partner_current_status'],
                "caseStatus" =>$data['partner_internal_status'],
                'remarks' => $data['cancellation_reason']
            );
            
            return $this->post_data($postData);
        } else {
            log_message('info', __METHOD__ . "=> Order ID Not Found");

            $this->jsonRequestData = json_encode($data);
            $this->jsonResponseString['response'] = ERR_ORDER_ID_NOT_FOUND_MSG;
            $this->jsonResponseString['error'] = ERR_ORDER_ID_NOT_FOUND_CODE;

            $responseData = array("data" => $this->jsonResponseString);

            $activity = array(
                'activity' => $this->requestUrl,
                'json_request_data' => $this->jsonRequestData,
                'json_response_string' => json_encode($responseData, JSON_UNESCAPED_SLASHES));

            $this->My_CI->partner_model->log_partner_activity($activity);

            return FALSE;
        }
    }

    //Call SD status update API when booking is completed
    public function update_status_complete_booking($data) {
        log_message('info', __METHOD__ . "=> Booking ID: " . $data['booking_id']);
        $this->requestUrl = __METHOD__;

        if ($data['order_id'] != NULL) {
            $this->partner = $data['order_id'];

            //Find completion date
            $delDate = $this->getServiceCompletionDate(date('Y-m-d H:i:s', strtotime($data['booking_date'])));

            //Update status, remarks
            $postData = array(
                "vendorCode" => VENDOR_CODE,
                "caseId" => $data['booking_id'],
                "orderId" => $data['order_id'],
                "caseStatus" =>$data['partner_internal_status'],
                "vendorStatus" => $data['partner_current_status'],
                "callType" => $data['internal_status'],
                "startDate" => $delDate
            );
        
            return $this->post_data($postData);
        } else {
            log_message('info', __METHOD__ . "=> Order ID Not Found");

            $this->jsonRequestData = json_encode($data);
            $this->jsonResponseString['response'] = ERR_ORDER_ID_NOT_FOUND_MSG;
            $this->jsonResponseString['error'] = ERR_ORDER_ID_NOT_FOUND_CODE;

            $responseData = array("data" => $this->jsonResponseString);

            $activity = array(
                'activity' => $this->requestUrl,
                'json_request_data' => $this->jsonRequestData,
                'json_response_string' => json_encode($responseData, JSON_UNESCAPED_SLASHES));

            $this->My_CI->partner_model->log_partner_activity($activity);

            return FALSE;
        }
    }

    //Call SD status update API when booking is rescheduled
    public function update_status_reschedule_booking($data) {
        log_message('info', __METHOD__ . "=> Booking ID: " . $data['booking_id']);
        $this->requestUrl = __METHOD__;

        if ($data['order_id'] != NULL) {
            $this->partner = $data['partner_id'];

            //Update status, remarks, start & end date
            //Get start and end dates
            $delDate = $this->getStartEndDate(date('Y-m-d H:i:s', strtotime($data['booking_date'])), $data['booking_timeslot']);

            $postData = array(
                "vendorCode" => VENDOR_CODE,
                "caseId" => $data['booking_id'],
                "orderId" => $data['order_id'],
                "caseStatus" =>$data['partner_internal_status'],
                "vendorStatus" => $data['partner_current_status'],
                "remarks" => $data['internal_status'],
                "startDate" => $delDate['tsStart'],
                "endDate" => $delDate['tsEnd']
            );
          
            return $this->post_data($postData);
        } else {
            log_message('info', __METHOD__ . "=> Order ID Not Found");

            $this->jsonRequestData = json_encode($data);
            $this->jsonResponseString['response'] = ERR_ORDER_ID_NOT_FOUND_MSG;
            $this->jsonResponseString['error'] = ERR_ORDER_ID_NOT_FOUND_CODE;

            $responseData = array("data" => $this->jsonResponseString);

            $activity = array(
                'activity' => $this->requestUrl,
                'json_request_data' => $this->jsonRequestData,
                'json_response_string' => json_encode($responseData, JSON_UNESCAPED_SLASHES));

            $this->My_CI->partner_model->log_partner_activity($activity);

            return FALSE;
        }
    }

    function post_data($postData) {
        $curl = curl_init();

        $this->header = array(
            "x-auth-token: " . X_AUTH_TOKEN,
            "x-seller-authz-token: " . X_SELLER_AUTHZ_TOKEN,
            "content-type: application/json"
        );

        $this->jsonRequestData = json_encode($postData);

        curl_setopt_array($curl, array(
            CURLOPT_URL => SD_CB_URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $this->jsonRequestData,
            CURLOPT_HTTPHEADER => $this->header,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        //Capture both response as well as error messages
        $this->jsonResponseString['response'] = $response;
        $this->jsonResponseString['error'] = $err;

        $responseData = array("data" => $this->jsonResponseString);

        $activity = array(
            'partner_id' => $this->partner,
            'activity' => $this->requestUrl,
            'header' => json_encode($this->header),
            'json_request_data' => $this->jsonRequestData,
            'json_response_string' => json_encode($responseData, JSON_UNESCAPED_SLASHES));

        $this->My_CI->partner_model->log_partner_activity($activity);

        if ($err) {
            log_message('info', "cURL Error #:" . $err);
            return "cURL Error #:" . $err;
        } else {
            log_message('info', "cURL Response #:" . $response);
            return $response;
        }

        //TODO: Parse return codes and take appropriate actions
    }

    function getStartEndDate($date, $time) {
        $dt = date_create($date);
        //Extract year
        $start['year'] = $end['year'] = intval(date_format($dt, "Y"));
        //Extract month
        $start['month'] = $end['month'] = intval(date_format($dt, "m"));
        //Extract day
        $start['day'] = $end['day'] = intval(date_format($dt, "d"));

        switch ($time) {
            case "10AM-1PM":
                $start['hour'] = 10;
                $end['hour'] = 13;
                $start['minute'] = $end['minute'] = 0;
                break;

            case "1PM-4PM":
                $start['hour'] = 13;
                $end['hour'] = 16;
                $start['minute'] = $end['minute'] = 0;
                break;

            case "4PM-7PM":
                $start['hour'] = 16;
                $end['hour'] = 19;
                $start['minute'] = $end['minute'] = 0;
                break;

            default:
                break;
        }

        return array("tsStart" => $start, "tsEnd" => $end);
    }

    function getServiceCompletionDate($date) {
        $dt = date_create($date);
        //Extract year
        $start['year'] = date_format($dt, "Y");
        //Extract month
        $start['month'] = date_format($dt, "m");
        //Extract day
        $start['day'] = date_format($dt, "d");
        //Set other fields
        $start['hour'] = $start['minute'] = $end['minute'] = 0;

        return $start;
    }
    
    //Call Jeeves status update API when booking is scheduled
    public function update_jeeves_status_schedule_booking($data) {
        log_message('info', __METHOD__ . "=> Booking ID: " . $data['booking_id']);
        $this->requestUrl = __METHOD__;
        if (!empty($data['order_id'])) {

            $this->partner = $data['partner_id'];
            $serial_number = "";
            $CallCompletedDate = "";
            $StatusReason = "";
            $appointmentDate = "";

            $isTrigger = true;
            //if current status is not cancelled then check this is open booking or not  
            if ($data['current_status'] != _247AROUND_CANCELLED) {
                $bstateChange = $this->My_CI->booking_model->getbooking_state_change_by_any(array('booking_id LIKE "%' . $data['booking_id'] . '%"' => NULL,
                    'new_state' => _247AROUND_CANCELLED));
                if (!empty($bstateChange)) {
                    $isTrigger = false;
                }
            }
            if ($isTrigger) {
                if ($data['current_status'] == _247AROUND_COMPLETED) {

                    $unit = $this->My_CI->booking_model->get_unit_details(array("booking_id" => $data['booking_id']), false, "serial_number");
                    if (!empty($unit)) {
                        foreach ($unit as $unit_details) {
                            if (!empty($unit_details['serial_number'])) {
                                $serial_number = $unit_details['serial_number'];
                            }
                        }
                    }

                    $CallCompletedDate = array(
                        "year" => date('Y', strtotime($data['service_center_closed_date'])),
                        "month" => date('m', strtotime($data['service_center_closed_date'])),
                        "day" => date('d', strtotime($data['service_center_closed_date'])),
                        "hour" => "08",
                        "minute" => date('i', strtotime($data['service_center_closed_date'])));
                    $is_valid = false;
                    if (!empty($data['service_promise_date'])) {

                        if (date('Y') == date('Y', strtotime($data['service_promise_date']))) {

                            $is_valid = TRUE;
                        } else if (date('Y') == (date('Y', strtotime($data['service_promise_date'])) - 1)) {
                            $is_valid = TRUE;
                        } else if (date('Y') == (date('Y', strtotime($data['service_promise_date'])) + 1))
                            $is_valid = TRUE;
                    }

                    if ($is_valid) {
                        if (date('Y-m-d', strtotime($data['service_center_closed_date'])) >
                                date('Y-m-d', strtotime($data['service_promise_date']))) {

                            if (!empty($data['partner_call_status_on_completed'])) {
                                log_message('info', __METHOD__ . "Status  " . $data['partner_call_status_on_completed'] . " "
                                        . $data['service_center_closed_date'] .
                                        " SPD date " . date('Y-m-d', strtotime($data['service_promise_date'])));

                                $StatusReason = $data['partner_call_status_on_completed'];
                            } else {
                                log_message('info', __METHOD__ . " Delay By 247Around " . $data['service_center_closed_date'] .
                                        " SPD date " . date('Y-m-d', strtotime($data['service_promise_date'])));
                                $StatusReason = JEEVES_BOOKING_DELAY_BY_AROUND;
                            }
                        } else {
                            log_message('info', __METHOD__ . " WITH In TAT closed_date " . $data['service_center_closed_date'] .
                                    " SPD date " . date('Y-m-d', strtotime($data['service_promise_date'])));
                            $StatusReason = JEEVES_CALL_COMPLETED_WITH_IN_TAT;
                        }
                    } else {
                        log_message('info', __METHOD__ . " Invalid SPD date");
                        $StatusReason = JEEVES_CALL_COMPLETED_WITH_IN_TAT;
                    }
                }

                $postData = array(
                    "CaseId" => $data['order_id'], //Mandatory in All status
                    "CallStatus" => $data['partner_current_status'], //Mandatory in All status
                    "Remarks" => $data['partner_internal_status']
                );

                if (!empty($CallCompletedDate)) {
                    //Mandatory in  only Call complete Status /other status its optional
                    $postData['CallCompletedDate'] = $CallCompletedDate;
                    //Mandatory for Completed Call
                    $postData["StatusReason"] = $StatusReason; 
                    //Mandatory in  only Call complete Status /other status its optional
                    $postData["SerialNo"] = $serial_number;
                }

                //Mandatory in  only Call complete Status /other status its optional
                if (!empty($data['booking_date'])) {
                    $postData['AppointmentDate'] = array(
                        "year" => date('Y', strtotime($data['booking_date'])),
                        "month" => date('m', strtotime($data['booking_date'])),
                        "day" => date('d', strtotime($data['booking_date'])),
                        "hour" => date('H', strtotime($data['booking_date'])),
                        "minute" => date('i', strtotime($data['booking_date'])));
                }
                
                return $this->post_jeeves_data($postData);
                
            } else {
                 log_message('info', __METHOD__ . "=> Call already updated cancelled");
                $this->jsonRequestData = json_encode($data);
                $this->jsonResponseString['response'] = ERR_CALL_ALREADY_CANCELLED_MSG;
                $this->jsonResponseString['error'] = ERR_CALL_ALREADY_CANCELLED_CODE;
                
                $this->log_callback();
                return FALSE;

            }
            
        } else {
            log_message('info', __METHOD__ . "=> Order ID Not Found");

            $this->jsonRequestData = json_encode($data);
            $this->jsonResponseString['response'] = ERR_ORDER_ID_NOT_FOUND_MSG;
            $this->jsonResponseString['error'] = ERR_ORDER_ID_NOT_FOUND_CODE;
            $this->log_callback();
            return FALSE;
        }
    }
    /**
     * @desc This is used to insert log when we did not call jeeves aoi to update
     */
    function log_callback(){
        
        $responseData = array("data" => $this->jsonResponseString);
        
        $activity = array(
            'activity' => $this->requestUrl,
            'json_request_data' => $this->jsonRequestData,
            'json_response_string' => json_encode($responseData, JSON_UNESCAPED_SLASHES));

        $this->My_CI->partner_model->log_partner_activity($activity);
    }

    function post_jeeves_data($postData){
         $curl = curl_init();

        $this->header = array(
            "Auth-Key: " . JEEVES_AUTH_KEY,
            "content-type: application/json"
        );

       $this->jsonRequestData = json_encode($postData);


        curl_setopt_array($curl, array(
            CURLOPT_URL => JEEVES_CB_URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 600,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $this->jsonRequestData,
            CURLOPT_HTTPHEADER => $this->header,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        
        //Capture both response as well as error messages
        $this->jsonResponseString['response'] = $response;
        $this->jsonResponseString['error'] = $err;

        $responseData = array("data" => $this->jsonResponseString);

        $activity = array(
            'partner_id' => $this->partner,
            'activity' => $this->requestUrl,
            'header' => json_encode($this->header),
            'json_request_data' => $this->jsonRequestData,
            'json_response_string' => json_encode($responseData, JSON_UNESCAPED_SLASHES));

        $this->My_CI->partner_model->log_partner_activity($activity);
        
        $res = json_decode($response, true);
        if (isset($res['ResponseCode'])) {
            if ($res['ResponseCode'] != 201) {
                $this->jeevesCallbackAPIFailed();
            }
        } else {
            $this->jeevesCallbackAPIFailed();
        }

        if ($err) {
            log_message('info', "cURL Error #:" . $err);
            $this->jeevesCallbackAPIFailed();
            return false;
        } else {
            log_message('info', "cURL Response #:" . $response);
            return $response;
        }
    }
    /**
     * @desc this is used to send email when JEEVES callback failed.
     */
    function jeevesCallbackAPIFailed(){
        log_message("info", __METHOD__. " JEEVES Callback failed ". $this->jsonRequestData);
        $subject = "Jeeves CallBack API failed. Please Check";
        $message = " POST Data ".$this->jsonRequestData. "<br/> Jeeves Callback Response ". $this->jsonResponseString['response'];
        $this->My_CI->notify->sendEmail(NOREPLY_EMAIL_ID, "abhaya@247around.com", "", "", $subject, $message, "","JEEVES CALLBACK Failed");
    }

}
