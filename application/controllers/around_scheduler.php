<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

//error_reporting(E_ERROR);
//ini_set('display_errors', '-1');

class Around_scheduler extends CI_Controller {
	 function __Construct() {
        parent::__Construct();

        $this->load->model('around_scheduler_model');
        $this->load->model('booking_model');
        $this->load->model('vendor_model');
        $this->load->model('employee_model');
        $this->load->model('reporting_utils');
        $this->load->library('s3');
        $this->load->library('email');
        $this->load->library('notify');
        $this->load->library('PHPReport');
        $this->load->helper(array('form', 'url','file'));
        $this->load->dbutil();
    }

    /**
     * @desc: This is used to send SMS to customers for whom delivery is scheduled for Today
     * to inform them about free installation when they give a missed call.
     */
    function send_reminder_installation_sms_today() {
        log_message ('info', __METHOD__ . '=> Entering...');
        
        //Get all queries where SMS needs to be sent
	$data1 = $this->around_scheduler_model->get_reminder_installation_sms_data_today();
        
        //Tag queries where Vendor is not available
        $data2 = $this->booking_model->searchPincodeAvailable($data1, PINCODE_AVAILABLE);
        
        //Send SMS to only customers where Vendor is Available
	$sms['tag'] = "sd_edd_missed_call_reminder";
        
	foreach ($data2 as $value) {
            if($value->sms_count < 3){
                $sms['phone_no'] = $value->booking_primary_contact_no;
                $category = '';
                if($value->services == 'Geyser'){
                    $where = array('booking_id'=> $value->booking_id);
                    $unit_details = $this->booking_model->get_unit_details($where);
                    if(!empty($unit_details)){
                        $category = $unit_details[0]['appliance_category'];
                    }
                }

                //Ordering of SMS data is important, check SMS template before changing it
                $sms['smsData']['service'] = $value->services;
                $sms['smsData']['missed_call_number'] = SNAPDEAL_MISSED_CALLED_NUMBER;
                $sms['smsData']['message'] = $this->notify->get_product_free_not($value->services, $category);


                $sms['booking_id'] = $value->booking_id;
                $sms['type'] = "user";
                $sms['type_id'] = $value->user_id;

                $this->notify->send_sms_msg91($sms);   
                // This nis used to increase sms count
                $this->booking_model->increase_escalation_reschedule($value->booking_id, "sms_count");
            } else {
                 log_message ('info', __METHOD__ . '=> SMS not Sent because SMS Count greater or equal than 2');
            }
            
	}
        // Inserting values in scheduler tasks log
        $this->reporting_utils->insert_scheduler_tasks_log(__FUNCTION__); 
        
        log_message ('info', __METHOD__ . '=> Exiting...');
    }

    /**
     * @desc: This is used to send SMS to customers for whom delivery is scheduled for tomorrow 
     * or later to inform them about free installation when they give a missed call.
     */
    function send_reminder_installation_sms_future() {
        log_message ('info', __METHOD__ . '=> Entering...');
        
        //Get all queries where SMS needs to be sent
	$data1 = $this->around_scheduler_model->get_reminder_installation_sms_data_future();
        
        //Tag queries where Vendor is not available
        $data2 = $this->booking_model->searchPincodeAvailable($data1, PINCODE_AVAILABLE);
        
        //Send SMS to only customers where Vendor is Available
	$sms['tag'] = "sd_edd_missed_call_reminder";
        
	foreach ($data2 as $value) {
            $sms['phone_no'] = $value->booking_primary_contact_no;
            
            $category = '';
            if($value->services == 'Geyser'){
                
                $where = array('booking_id'=> $value->booking_id);
                $unit_details = $this->booking_model->get_unit_details($where);
                if(!empty($unit_details)){
                    $category = $unit_details[0]['appliance_category'];
                }
            }

            //Ordering of SMS data is important, check SMS template before changing it
            $sms['smsData']['service'] = $value->services;
            $sms['smsData']['missed_call_number'] = SNAPDEAL_MISSED_CALLED_NUMBER;
            $sms['smsData']['message'] = $this->notify->get_product_free_not($value->services, $category);
            

            $sms['booking_id'] = $value->booking_id;
            $sms['type'] = "user";
            $sms['type_id'] = $value->user_id;

            $this->notify->send_sms_msg91($sms);                
	}
        
        log_message ('info', __METHOD__ . '=> Exiting...');
    }

    /**
     * @desc: This is used to send SMS to customers for whom delivery was scheduled for past 
     * or earlier to inform them about free installation when they give a missed call.
     */
    function send_reminder_installation_sms_past() {
        log_message ('info', __METHOD__ . '=> Entering...');
        
        //Get all queries where SMS needs to be sent
	$data1 = $this->around_scheduler_model->get_reminder_installation_sms_data_past();
        
        //Tag queries where Vendor is not available
        $data2 = $this->booking_model->searchPincodeAvailable($data1, PINCODE_AVAILABLE);
        
        //Send SMS to only customers where Vendor is Available
	$sms['tag'] = "sd_edd_missed_call_reminder";
        
	foreach ($data2 as $value) {            
            $sms['phone_no'] = $value->booking_primary_contact_no;
            
            $category = '';
            if($value->services == 'Geyser'){
                $where = array('booking_id'=> $value->booking_id);
                $unit_details = $this->booking_model->get_unit_details($where);
                if(!empty($unit_details)){
                    $category = $unit_details[0]['appliance_category'];
                }
            }

            //Ordering of SMS data is important, check SMS template before changing it
            $sms['smsData']['service'] = $value->services;
            $sms['smsData']['missed_call_number'] = SNAPDEAL_MISSED_CALLED_NUMBER;
            $sms['smsData']['message'] = $this->notify->get_product_free_not($value->services, $category);
            
            $sms['booking_id'] = $value->booking_id;
            $sms['type'] = "user";
            $sms['type_id'] = $value->user_id;

            $this->notify->send_sms_msg91($sms);                
	}
        
        log_message ('info', __METHOD__ . '=> Exiting...');
    }

    /**
     * @desc: This is used to send SMS to customers for whom Geyser delivery was scheduled for today 
     * and yesterday to inform them about free installation when they give a missed call.
     */
    function send_reminder_installation_sms_geyser_in_delhi() {
        log_message ('info', __METHOD__ . '=> Entering...');
        
        //Get all queries where SMS needs to be sent
	$data1 = $this->around_scheduler_model->get_reminder_installation_sms_data_geyser_delhi();
        
        //Send SMS to only customers where Vendor is Available
	$sms['tag'] = "sd_edd_missed_call_reminder";
        
	foreach ($data1 as $value) {
            $sms['phone_no'] = $value->booking_primary_contact_no;

            //Ordering of SMS data is important, check SMS template before changing it
            $sms['smsData']['message'] = "Free";
            $sms['smsData']['missed_call_number'] = SNAPDEAL_MISSED_CALLED_NUMBER;
            $sms['smsData']['service'] = "Geyser";

            $sms['booking_id'] = $value->booking_id;
            $sms['type'] = "user";
            $sms['type_id'] = $value->user_id;

            $this->notify->send_sms_msg91($sms) ;               
	}
        
        log_message ('info', __METHOD__ . '=> Exiting...');
    }
    
    /**
     * @desc: Cancel bookings, When booking date is empty, then getting those bookings which has 
     * difference between delivery date and current date are greater than 2.
     * AND When booking date is not empty, then getting those bookings which has difference between 
     *  delivery date and current date are greater than 5.
     */
    function cancel_old_pending_query(){
        log_message ('info', __METHOD__ . '=> Entering...');
        $data = $this->around_scheduler_model->get_old_pending_query();
        
        foreach ($data as $value) {
            echo "..".PHP_EOL;
            $this->booking_model->update_booking($value['booking_id'], array('current_status'=> 'Cancelled', 
                'internal_status' => 'Cancelled', 'cancellation_reason' => "Customer Not Reachable"));
            $this->booking_model->update_booking_unit_details($value['booking_id'], array('booking_status' => 'Cancelled'));
            
            $this->notify->insert_state_change($value['booking_id'], 
                    "Cancelled" , "FollowUp" , 
                    "Customer Not Reachable" , 
                   "1", "247Around",
                    _247AROUND);
            
        }
        log_message ('info', __METHOD__ . '=> Exit...');
    }

    /**
     *  @desc : This function is to cancels the booking/Query
     *
     * Accepts the cancellation reason provided in cancel booking/Query form and then cancels booking with the reason provided.
     *
     *  @param : booking id
     *  @return : cancels the booking and load view
     */
    function cancel_pending_query($booking_id) {
	log_message('info', __METHOD__ . " => Booking ID: " . $booking_id);
        
        $status = $this->booking_model->get_booking_status(trim($booking_id));
        
        if ($status['current_status'] == "FollowUp") {
            $data['cancellation_reason'] = 'Customer Not Responded to 247around Communication';
            $data['closed_date'] = $data['update_date'] = date("Y-m-d H:i:s");        
            $data['current_status'] = $data['internal_status'] = _247AROUND_CANCELLED ;
            $data_vendor['cancellation_reason'] = $data['cancellation_reason'];

            log_message('info', __FUNCTION__ . " Update booking  " . print_r($data, true));

            $this->booking_model->update_booking($booking_id, $data);

            //Update this booking in vendor action table
            $data_vendor['update_date'] = date("Y-m-d H:i:s");
            $data_vendor['current_status'] = $data_vendor['internal_status'] = _247AROUND_CANCELLED ;

            log_message('info', __FUNCTION__ . " Update Service center action table  " . print_r($data_vendor, true));
            $this->vendor_model->update_service_center_action($booking_id, $data_vendor);

            $unit_details['booking_status'] = _247AROUND_CANCELLED;
            $unit_details['vendor_to_around'] = $unit_details['around_to_vendor'] = 0;

            log_message('info', __FUNCTION__ . " Update unit details  " . print_r($unit_details, true));

            $this->booking_model->update_booking_unit_details($booking_id, $unit_details);

            //Log this state change as well for this booking
            $this->notify->insert_state_change($booking_id, $data['current_status'], _247AROUND_FOLLOWUP, 
                    $data['cancellation_reason'] , '1', '247around', _247AROUND);

            echo 'Cancelled ................' . PHP_EOL;
        } else {
            echo $booking_id. ' Query State Changed to => ' . $status['current_status'] . PHP_EOL;
        }
    }
    
    
    /**
     *  @desc : This function is to cancels wrong orders inserted by incorrect snapdeal file
     *
     *  @param : order id
     *  @return : 
     */
    function cancel_wrong_orders($order_id) {
	log_message('info', __METHOD__ . " => Order ID: " . $order_id);
        
        $data['cancellation_reason'] = 'Installation Not Required';
	$data['closed_date'] = $data['update_date'] = date("Y-m-d H:i:s");        
	$data['current_status'] = $data['internal_status'] = _247AROUND_CANCELLED ;
	$data_vendor['cancellation_reason'] = $data['cancellation_reason'];

	log_message('info', __FUNCTION__ . " Update query  " . print_r($data, true));

	$booking_id = $this->booking_model->update_booking_by_order_id($order_id, $data);
        echo 'Booking ID: ' . $booking_id . PHP_EOL;

        if ($booking_id !== FALSE) {
            //Update this booking in vendor action table
            $data_vendor['update_date'] = date("Y-m-d H:i:s");
            $data_vendor['current_status'] = $data_vendor['internal_status'] = _247AROUND_CANCELLED ;

            log_message('info', __FUNCTION__ . " Update Service center action table  " . print_r($data_vendor, true));
            $this->vendor_model->update_service_center_action($booking_id, $data_vendor);

            $unit_details['booking_status'] = _247AROUND_CANCELLED;
            $unit_details['vendor_to_around'] = $unit_details['around_to_vendor'] = 0;

            log_message('info', __FUNCTION__ . " Update unit details  " . print_r($unit_details, true));

            $this->booking_model->update_booking_unit_details($booking_id, $unit_details);

            //Log this state change as well for this booking
            $this->notify->insert_state_change($booking_id, $data['current_status'], _247AROUND_FOLLOWUP, 
                    $data['cancellation_reason'] , '1', '247around', _247AROUND);

            echo 'Cancelled ................' . PHP_EOL;
        }
    }

    
    /**
     * @desc: This function is used to send SMS for all Snapdeal pending queries 
     */
    function send_sms_to_query(){
        log_message('info', __METHOD__ ."Entering");
        //Get all queries
        $data1 = $this->around_scheduler_model->get_all_query();
         //Tag queries where Vendor is not available
        $data2 = $this->booking_model->searchPincodeAvailable($data1, PINCODE_AVAILABLE);

        log_message ('info', __METHOD__ . "=> Count  All Pincode AV Query ". count($data2));
        
        //Send SMS to only customers where Vendor is Available
	$sms['tag'] = "sd_edd_missed_call_reminder";
        
	foreach ($data2 as $value) {            
            $sms['phone_no'] = $value->booking_primary_contact_no;
            
             $category = '';
            if($value->services == 'Geyser'){
                
                $where = array('booking_id'=> $value->booking_id);
                $unit_details = $this->booking_model->get_unit_details($where);
                if(!empty($unit_details)){
                    $category = $unit_details[0]['appliance_category'];
                }
            }


            //Ordering of SMS data is important, check SMS template before changing it
            $sms['smsData']['service'] = $value->services;
            $sms['smsData']['missed_call_number'] = SNAPDEAL_MISSED_CALLED_NUMBER;
            $sms['smsData']['message'] = $this->notify->get_product_free_not($value->services,$category );
            
            $sms['booking_id'] = $value->booking_id;
            $sms['type'] = "user";
            $sms['type_id'] = $value->user_id;

           // print_r($sms);

            $this->notify->send_sms_msg91($sms);  
            echo "...<br/>";              
	}
        
        log_message ('info', __METHOD__ . '=> Exiting...');
    }
    
    /**
     * @Desc: This function is used to check all the Tasks that has been executed in CRON
     * @params: void
     * @return: void
     * 
     */
    function check_cron_tasks(){
        //Defining array for Cron Jobs
        $CRON_JOBS = ['get_pending_bookings','send_service_center_report_mail',
            'new_send_service_center_report_mail','send_summary_mail_to_partners',
            'send_reminder_installation_sms_today','send_reminder_installation_sms_today',
            'DatabaseTesting','send_error_file',
            'convert_updated_booking_to_pending','penalty_on_service_center',
            'get_sc_crimes','get_sc_crimes_for_sf'];
        
        $previous_date = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d'))));
        $tasks_log = $this->reporting_utils->get_scheduler_tasks_log($previous_date);
        $tasks_array = [];
        if(!empty($tasks_log)){
            foreach($tasks_log as $value){
                $tasks_array[] = $value['task_name'];
            }
        }
            
            //Finding Diff in Task Array and CRON ARRAY
            $diff = array_diff($CRON_JOBS, $tasks_array);
            if(!empty($diff)){
                //Some cron jobs has not been executed
                $html = "<html xmlns='http://www.w3.org/1999/xhtml'>
                      <head>
                        <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
                      </head>
                      <body>
                      <p>Following list contains CRON tasks that has <b>NOT</b> Executed on : <b>".$previous_date."
                        </b></p><ol>";
                foreach ($diff as $value){
                    $html .= "<li>".$value."</li>";
                }
                $html .= "</ol></body></html>";
                
                //Sending Details in Mail
                $to = DEVELOPER_EMAIL;
                $subject = " ERROR IN CRON TASK EXECUTION " . date("d-M-Y");
                $this->notify->sendEmail("booking@247around.com", $to, "", "", $subject, $html, "");
        }
    }
    
    
    /**
     * @desc: Checks balance ACL credits, used to send SMSes
     */
    function check_acl_credits () {
        $subject = "ACL Balance Credits - " . date("d-M-Y");
        $message = system('elinks -dump "https://push3.maccesssmspush.com/servlet/com.aclwireless.pushconnectivity.listeners.ConfigurationListener?action=prepaid&userid=blackmalt&pass=blackmalt67&appid=blackmalt&subappid=blackmalt"');
        
        $to = ANUJ_EMAIL_ID;
        $this->notify->sendEmail("booking@247around.com", $to, "", "", $subject, $message, "");
    }
    /**
     * @desc: This function is used to send RM weekly pincode not available booking data via cron
     * @param:void()
     * @retun:void()
     */
    function send_rm_pincode_not_available_booking(){
        log_message('info', __FUNCTION__ . ' => Entering ..');
        
        $pincode_not_available_bookings = $this->reporting_utils->get_pincode_not_available_bookings();
        if(!empty($pincode_not_available_bookings)){
            $newCSVFileName = "Pincode_Not_Available_BookingSummary_".date('j-M-Y').".csv";
            $csv = TMP_FOLDER.$newCSVFileName;
            $delimiter = ",";
            $newline = "\r\n";
            $new_report = $this->dbutil->csv_from_result($pincode_not_available_bookings, $delimiter, $newline);
            write_file( $csv, $new_report);        
            log_message('info', __FUNCTION__ . ' => Rendered CSV');

            $rm = $this->employee_model->get_employee_email_by_group('regionalmanager');
            $rm_email ='';
            foreach ($rm as $key => $value){
                $rm_email .=$value['official_email'];
                $rm_email .=",";
            }
            
            $admin_gp = $this->employee_model->get_employee_email_by_group('admin');
            $admin_email ='';
            foreach ($admin_gp as $key => $value){
                $admin_email .=$value['official_email'];
                $admin_email .=",";
            }
            
            $to = substr($rm_email,0,-1);
            $cc = substr($admin_email,0,-1);
            
            $message="Please find attached bookings list where SF is not available in the respective pincode.";

            $this->email->clear(TRUE);
            $this->email->from('booking@247around.com', '247around Team');
            $this->email->to($to);
            $this->email->cc($cc);
            $this->email->subject("SF NOT AVAILABLE IN PINCODES LIST");
            $this->email->message($message);
            $this->email->attach($csv, 'attachment');

            if ($this->email->send()) {
            log_message('info', __METHOD__ . ": Mail sent successfully for PinCode Not Available To RM ");
            } else {
            log_message('info', __METHOD__ . ": Mail could not be sent for RM: " . $p['public_name']);
        }
            
        }
        else{
            $to = ANUJ_EMAIL_ID;
            $message="No booking found which pincode is not available";

            $this->email->clear(TRUE);
            $this->email->from('booking@247around.com', '247around Team');
            $this->email->to("$to");
            $this->email->cc(ANUJ_EMAIL_ID);
            $this->email->subject("Pincode Not Available Booking Data");
            $this->email->message($message);
            $this->email->attach($csv, 'attachment');
            $this->email->send();
            log_message('info', __METHOD__ . ": No booking found for pincode not available ");
        }
    }

}