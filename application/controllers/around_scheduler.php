<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');


ini_set('memory_limit', '-1');
//3600 seconds = 60 minutes
ini_set('max_execution_time', 360000);

class Around_scheduler extends CI_Controller {

    function __Construct() {
        parent::__Construct();

        $this->load->model('around_scheduler_model');
        $this->load->model('user_model');
        $this->load->model('booking_model');
        $this->load->model('vendor_model');
        $this->load->model('employee_model');
        $this->load->model('bb_model');
        $this->load->model('cp_model');
        $this->load->model('reporting_utils');
        $this->load->library('s3');
        $this->load->library('email');
        $this->load->library('notify');
        $this->load->library('PHPReport');
        $this->load->library('table');
        $this->load->library('buyback');
        $this->load->library('email_data_reader');
        $this->load->helper(array('form', 'url', 'file'));
        $this->load->dbutil();
    }

    /**
     * @desc: This is used to send SMS to customers for whom delivery is scheduled for Today
     * to inform them about free installation when they give a missed call.
     */
    function send_reminder_installation_sms_today() {
        log_message('info', __METHOD__ . '=> Entering...');

        //Get all queries where SMS needs to be sent
        $data1 = $this->around_scheduler_model->get_reminder_installation_sms_data_today();
        $tag = "sd_edd_missed_call_reminder";

        foreach ($data1 as  $value) {
            $status = $this->notify->sendTransactionalSmsMsg91($value->booking_primary_contact_no, $value->content);
            log_message('info', __METHOD__ . print_r($status, 1));
            if (ctype_alnum($status['content']) && strlen($status['content']) == 24) {
                $this->notify->add_sms_sent_details($value->type_id, $value->type, $value->booking_primary_contact_no, 
                         $value->content, $value->booking_id, $tag, $status['content']);
                 
                $this->booking_model->increase_escalation_reschedule($value->booking_id, "sms_count");
            } else {
                $this->notify->add_sms_sent_details($value->type_id,  $value->type, $value->booking_primary_contact_no, 
                         $value->content, $value->booking_id, $status['content']);
                
                log_message('info', "Message Not Sent - Booking id: " . $value->booking_id . ",
        		please recheck tag: '" . $tag . "' & Phone Number - " . $value->booking_primary_contact_no);

                $subject = 'SMS Sending Failed';
                $message = "Please check SMS tag and phone number. Booking id is : " .
                       $value->booking_id . " Tag is '" . $tag . "' & phone number is :" . $value->booking_primary_contact_no . " Result:"
                        . " " . $status['content'];
                $to = ANUJ_EMAIL_ID . ", abhaya@247around.com";

                $this->notify->sendEmail("booking@247around.com", $to, "", "", $subject, $message, "");
            }
        }
        // Inserting values in scheduler tasks log
        $this->reporting_utils->insert_scheduler_tasks_log(__FUNCTION__);

        log_message('info', __METHOD__ . '=> Exiting...');
    }

    /**
     * @desc: This is used to send SMS to customers for whom delivery is scheduled for tomorrow 
     * or later to inform them about free installation when they give a missed call.
     */
    function send_reminder_installation_sms_future() {
        log_message('info', __METHOD__ . '=> Entering...');

        //Get all queries where SMS needs to be sent
        $data1 = $this->around_scheduler_model->get_reminder_installation_sms_data_future();

        //Tag queries where Vendor is not available
        $data2 = $this->booking_model->searchPincodeAvailable($data1, PINCODE_AVAILABLE);

        //Send SMS to only customers where Vendor is Available
        $sms['tag'] = "sd_edd_missed_call_reminder";

        foreach ($data2 as $value) {
            $sms['phone_no'] = $value->booking_primary_contact_no;

            $category = '';
            if ($value->services == 'Geyser') {

                $where = array('booking_id' => $value->booking_id);
                $unit_details = $this->booking_model->get_unit_details($where);
                if (!empty($unit_details)) {
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

        log_message('info', __METHOD__ . '=> Exiting...');
    }

    /**
     * @desc: This is used to send SMS to customers for whom delivery was scheduled for past 
     * or earlier to inform them about free installation when they give a missed call.
     */
    function send_reminder_installation_sms_past() {
        log_message('info', __METHOD__ . '=> Entering...');

        //Get all queries where SMS needs to be sent
        $data1 = $this->around_scheduler_model->get_reminder_installation_sms_data_past();

        //Tag queries where Vendor is not available
        $data2 = $this->booking_model->searchPincodeAvailable($data1, PINCODE_AVAILABLE);

        //Send SMS to only customers where Vendor is Available
        $sms['tag'] = "sd_edd_missed_call_reminder";

        foreach ($data2 as $value) {
            $sms['phone_no'] = $value->booking_primary_contact_no;

            $category = '';
            if ($value->services == 'Geyser') {
                $where = array('booking_id' => $value->booking_id);
                $unit_details = $this->booking_model->get_unit_details($where);
                if (!empty($unit_details)) {
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

        log_message('info', __METHOD__ . '=> Exiting...');
    }

    /**
     * @desc: This is used to send SMS to customers for whom Geyser delivery was scheduled for today 
     * and yesterday to inform them about free installation when they give a missed call.
     */
    function send_reminder_installation_sms_geyser_in_delhi() {
        log_message('info', __METHOD__ . '=> Entering...');

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

            $this->notify->send_sms_msg91($sms);
        }

        log_message('info', __METHOD__ . '=> Exiting...');
    }

    /**
     * @desc: Cancel bookings, When booking date is empty, then getting those bookings which has 
     * difference between delivery date and current date are greater than 2.
     * AND When booking date is not empty, then getting those bookings which has difference between 
     *  delivery date and current date are greater than 5.
     */
    function cancel_old_pending_query() {
        log_message('info', __METHOD__ . '=> Entering...');
        $data = $this->around_scheduler_model->get_old_pending_query();

        foreach ($data as $value) {
            echo ".." . PHP_EOL;
            $this->booking_model->update_booking($value['booking_id'], array('current_status' => 'Cancelled',
                'internal_status' => 'Cancelled', 'cancellation_reason' => "Customer not reachable / Customer not picked phone"));
            $this->booking_model->update_booking_unit_details($value['booking_id'], array('booking_status' => 'Cancelled'));

            $this->notify->insert_state_change($value['booking_id'], "Cancelled", "FollowUp", "Customer not reachable / Customer not picked phone", "1", "247Around", _247AROUND);
        }
        log_message('info', __METHOD__ . '=> Exit...');
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

        $status = $this->booking_model->getbooking_history(trim($booking_id));

        if ($status[0]['current_status'] == "FollowUp") {
            $data['cancellation_reason'] = 'Customer Not Responded to 247around Communication';
            $data['closed_date'] = $data['update_date'] = date("Y-m-d H:i:s");
            $data['current_status'] = $data['internal_status'] = _247AROUND_CANCELLED;
            $data_vendor['cancellation_reason'] = $data['cancellation_reason'];

            log_message('info', __FUNCTION__ . " Update booking  " . print_r($data, true));

            $this->booking_model->update_booking($booking_id, $data);

            //Update this booking in vendor action table
            $data_vendor['update_date'] = date("Y-m-d H:i:s");
            $data_vendor['current_status'] = $data_vendor['internal_status'] = _247AROUND_CANCELLED;

            log_message('info', __FUNCTION__ . " Update Service center action table  " . print_r($data_vendor, true));
            $this->vendor_model->update_service_center_action($booking_id, $data_vendor);

            $unit_details['booking_status'] = _247AROUND_CANCELLED;
            $unit_details['vendor_to_around'] = $unit_details['around_to_vendor'] = 0;

            log_message('info', __FUNCTION__ . " Update unit details  " . print_r($unit_details, true));

            $this->booking_model->update_booking_unit_details($booking_id, $unit_details);

            //Log this state change as well for this booking
            $this->notify->insert_state_change($booking_id, $data['current_status'], _247AROUND_FOLLOWUP, $data['cancellation_reason'], '1', '247around', _247AROUND);

            echo $booking_id . ' Cancelled ................' . PHP_EOL;
        } else {
            echo $booking_id . ' Query State Changed to => ' . $status[0]['current_status'] . PHP_EOL;
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
        $data['current_status'] = $data['internal_status'] = _247AROUND_CANCELLED;
        $data_vendor['cancellation_reason'] = $data['cancellation_reason'];

        log_message('info', __FUNCTION__ . " Update query  " . print_r($data, true));

        $booking_id = $this->booking_model->update_booking_by_order_id($order_id, $data);
        echo 'Booking ID: ' . $booking_id . PHP_EOL;

        if ($booking_id !== FALSE) {
            //Update this booking in vendor action table
            $data_vendor['update_date'] = date("Y-m-d H:i:s");
            $data_vendor['current_status'] = $data_vendor['internal_status'] = _247AROUND_CANCELLED;

            log_message('info', __FUNCTION__ . " Update Service center action table  " . print_r($data_vendor, true));
            $this->vendor_model->update_service_center_action($booking_id, $data_vendor);

            $unit_details['booking_status'] = _247AROUND_CANCELLED;
            $unit_details['vendor_to_around'] = $unit_details['around_to_vendor'] = 0;

            log_message('info', __FUNCTION__ . " Update unit details  " . print_r($unit_details, true));

            $this->booking_model->update_booking_unit_details($booking_id, $unit_details);

            //Log this state change as well for this booking
            $this->notify->insert_state_change($booking_id, $data['current_status'], _247AROUND_FOLLOWUP, $data['cancellation_reason'], '1', '247around', _247AROUND);

            echo 'Cancelled ................' . PHP_EOL;
        }
    }

    /**
     * @desc: This function is used to send SMS for all Snapdeal pending queries 
     */
    function send_sms_to_query() {
        log_message('info', __METHOD__ . "Entering");
        //Get all queries
        $data1 = $this->around_scheduler_model->get_all_query();
        //Tag queries where Vendor is not available
        $data2 = $this->booking_model->searchPincodeAvailable($data1, PINCODE_AVAILABLE);

        log_message('info', __METHOD__ . "=> Count  All Pincode AV Query " . count($data2));

        //Send SMS to only customers where Vendor is Available
        $sms['tag'] = "sd_edd_missed_call_reminder";

        foreach ($data2 as $value) {
            $sms['phone_no'] = $value->booking_primary_contact_no;

            $category = '';
            if ($value->services == 'Geyser') {

                $where = array('booking_id' => $value->booking_id);
                $unit_details = $this->booking_model->get_unit_details($where);
                if (!empty($unit_details)) {
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

            // print_r($sms);

            $this->notify->send_sms_msg91($sms);
            echo "...<br/>";
        }

        log_message('info', __METHOD__ . '=> Exiting...');
    }

    /**
     * @Desc: This function is used to check all the Tasks that has been executed in CRON
     * @params: void
     * @return: void
     * 
     */
    function check_cron_tasks() {
        //Defining array for Cron Jobs
        $CRON_JOBS = ['get_pending_bookings', 'send_service_center_report_mail',
            'new_send_service_center_report_mail', 'send_summary_mail_to_partners',
            'send_reminder_installation_sms_today', 'send_reminder_installation_sms_today',
            'DatabaseTesting', 'send_error_file',
            'convert_updated_booking_to_pending', 'penalty_on_service_center',
            'get_sc_crimes', 'get_sc_crimes_for_sf'];

        $previous_date = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d'))));
        $tasks_log = $this->reporting_utils->get_scheduler_tasks_log($previous_date);
        $tasks_array = [];
        if (!empty($tasks_log)) {
            foreach ($tasks_log as $value) {
                $tasks_array[] = $value['task_name'];
            }
        }

        //Finding Diff in Task Array and CRON ARRAY
        $diff = array_diff($CRON_JOBS, $tasks_array);
        if (!empty($diff)) {
            //Some cron jobs has not been executed
            $html = "<html xmlns='http://www.w3.org/1999/xhtml'>
                      <head>
                        <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
                      </head>
                      <body>
                      <p>Following list contains CRON tasks that has <b>NOT</b> Executed on : <b>" . $previous_date . "
                        </b></p><ol>";
            foreach ($diff as $value) {
                $html .= "<li>" . $value . "</li>";
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
    function check_acl_credits() {
        $subject = "ACL Balance Credits - " . date("d-M-Y");
        $message = system('elinks -dump "https://push3.maccesssmspush.com/servlet/com.aclwireless.pushconnectivity.listeners.ConfigurationListener?action=prepaid&userid=blackmalt&pass=blackmalt67&appid=blackmalt&subappid=blackmalt"');

        $to = ANUJ_EMAIL_ID;
        $this->notify->sendEmail(SYS_HEALTH_EMAIL, $to, "", "", $subject, $message, "");
    }

    /**
     * @desc: This function is used to send RM weekly pincode not available booking data via cron
     * @param:void()
     * @retun:void()
     */
    function send_rm_pincode_not_available_booking() {
        log_message('info', __FUNCTION__ . ' => Entering ..');

        $pincode_not_available_bookings = $this->reporting_utils->get_pincode_not_available_bookings();
        if (!empty($pincode_not_available_bookings)) {
            $newCSVFileName = "Pincode_Not_Available_BookingSummary_" . date('j-M-Y') . ".csv";
            $csv = TMP_FOLDER . $newCSVFileName;
            $delimiter = ",";
            $newline = "\r\n";
            $new_report = $this->dbutil->csv_from_result($pincode_not_available_bookings, $delimiter, $newline);
            write_file($csv, $new_report);
            log_message('info', __FUNCTION__ . ' => Rendered CSV');

            $rm = $this->employee_model->get_employee_email_by_group('regionalmanager');
            $rm_email = '';
            foreach ($rm as $key => $value) {
                $rm_email .=$value['official_email'];
                $rm_email .=",";
            }

            $admin_gp = $this->employee_model->get_employee_email_by_group('admin');
            $admin_email = '';
            foreach ($admin_gp as $key => $value) {
                $admin_email .=$value['official_email'];
                $admin_email .=",";
            }

            $to = substr($rm_email, 0, -1);
            $cc = substr($admin_email, 0, -1);

            $message = "Please find attached bookings list where SF is not available in the respective pincode.";

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
                log_message('info', __METHOD__ . ": Mail could not be sent to RM");
            }
        } else {
            $to = ANUJ_EMAIL_ID;
            $message = "No booking found which pincode is not available";

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

    /**
     * @desc: This function is used to send promotional sms to users on daily basis 
     * @param:void()
     * @retun:void()
     */
    function get_phone_number_to_send_sms($case) {
        log_message('info', __METHOD__ . ": Entering....");

        $phn_number_data = $this->around_scheduler_model->get_user_phone_number($case);
        //send filtered phone number data to this function to sent the messages to the user
        $this->send_promotional_sms_to_user($phn_number_data);

        log_message('info', __METHOD__ . ": Existing....");
    }

    /**
     * @desc: This function is used to prepare the sms data to send promotional sms to users on daily basis 
     * @param:$filtered_phn_number_data array()
     * @retun:void()
     */
    function send_promotional_sms_to_user($filtered_phn_number_data) {
        $smsTag = "";
        //preparing sms data to send
        foreach ($filtered_phn_number_data as $value) {

            //send sms according to booking status
            $smsTag = $this->get_sms_tag($value['current_status']);
            $smsData['price'] = "50";

            $this->prepare_sms_data_to_send($smsTag, $value['phn_number'], $smsData, "", "User", $value['user_id']);
        }

        // Inserting values in scheduler tasks log
        $this->reporting_utils->insert_scheduler_tasks_log(__FUNCTION__);
    }
    
    
    /**
     * @desc: This function is used to get the corresponding SMS TAG for booking_status and phone_number 
     * @param:$filtered_phn_number_data array()
     * @retun:void()
     */
    function get_sms_tag($booking_status) {
        $smsTag_arr1 = array(1,3,5,7,9,11);
        $smsTag_arr2 = array(2,4,6,8,12);
        $cur_month = date('n');
        switch ($booking_status) {
            case 'Completed':
                if (in_array($cur_month, $smsTag_arr1)){
                    $smsTag = COMPLETED_PROMOTINAL_SMS_1;
                } else if(in_array($cur_month, $smsTag_arr2)){
                    $smsTag = COMPLETED_PROMOTINAL_SMS_2;
                }
                break;
            case 'Cancelled':
                if (in_array($cur_month, $smsTag_arr1)){
                    $smsTag = CANCELLED_PROMOTINAL_SMS_1;
                } else if(in_array($cur_month, $smsTag_arr2)){
                    $smsTag = CANCELLED_PROMOTINAL_SMS_2;
                }
                break;
            case 'Query':
                if (in_array($cur_month, $smsTag_arr1)){
                    $smsTag = CANCELLED_QUERY_PROMOTINAL_SMS_1;
                } else if(in_array($cur_month, $smsTag_arr2)){
                    $smsTag = CANCELLED_QUERY_PROMOTINAL_SMS_2;
                }
                break;
            case 'no_status':
                if (in_array($cur_month, $smsTag_arr1)){
                    $smsTag = BOOKING_NOT_EXIST_PROMOTINAL_SMS_1;
                } else if(in_array($cur_month, $smsTag_arr2)){
                    $smsTag = BOOKING_NOT_EXIST_PROMOTINAL_SMS_2;
                }
                break;
        }
        
        return $smsTag;
    }

    /**
     * @desc: This is general function to sent the sms's 
     * @param:$smsTag String
     * @param:$smsPhnNumber String
     * @param:$smsData Array()
     * @param:$booking_id String
     * @param:$smsType String
     * @param:$smsTypeId String
     * @retun:void()
     */
    function prepare_sms_data_to_send($smsTag, $smsPhnNumber, $smsData, $booking_id, $smsType, $smsTypeId) {

        $sms['tag'] = $smsTag;
        $sms['phone_no'] = $smsPhnNumber;
        $sms['smsData'] = $smsData;
        $sms['booking_id'] = $booking_id;
        $sms['type'] = $smsType;
        $sms['type_id'] = $smsTypeId;

        $this->notify->send_sms_msg91($sms);
    }
    /**
     * @desc This call from cron. AT 12:15, 15:15, 6:15 to send updated jeeves booking in the  Mail
     */
    function send_notification_mail_to_jeeves(){
        $hour = 3;
        if(date('H') < 12){
            $hour = 18;
        }

        $data = $this->around_scheduler_model->get_status_changes_booking_with_in_hour($hour);
        $template = array(
        'table_open' => '<table border="1" cellpadding="2" cellspacing="1" class="mytable">'
        );

        $this->table->set_template($template);

        $this->table->set_heading(array('Order ID', 'Jeeves New Status', 'Rescheduled Booking Date', 'Remarks', 'Amount Collected'));
        foreach($data as $value){
            
            $this->table->add_row($value['order_id'], $value['partner_current_status'], 
                    $value['booking_date'], $value['cancellation_reaoson'], $value['amount_paid']);

        }

        $to = NITS_ANUJ_EMAIL_ID;
        $cc = "abhaya@247around.com";
        
        $subject = "Jeeves Booking Update Status";
        $message = "Dear Partner,<br/> Attached is the status of the last ".$hour. " hour <br/><br/><br/>";
        $message .= $this->table->generate();

        $this->notify->sendEmail("booking@247around.com", $to, $cc, "", $subject, $message, "");
    }
    
    /**
     * @desc: This function is used to send SMS to those users who don't give ratings but booking is completed
     * @param:$from String(optional)
     * @param:$to String(optional)
     * @retun:void()
     */
    public function send_sms_for_rating_on_booking_completed($from="",$to=""){
        
        $data = $this->around_scheduler_model->get_data_for_bookings_without_rating($from,$to);
        
        if(!empty($data)){
            foreach ($data as $value) {

                //send sms according to booking status
                $smsTag = MISSED_CALL_RATING_SMS;
                $smsData['good_rating_number'] = GOOD_MISSED_CALL_RATING_NUMBER;
                $smsData['poor_rating_number'] = POOR_MISSED_CALL_RATING_NUMBER;
                $this->prepare_sms_data_to_send($smsTag, $value['phn_number'], $smsData, $value['booking_id'], "User", $value['user_id']);
            }
        }
    }
    
    /**
     * @desc: This function is used to send SMS to those users who did bot give missed call
     * after sending completed rating sms and rating is also null
     * @param:void
     * @retun:void()
     */
    public function send_missed_call_rating_sms_again(){
        $data = $this->around_scheduler_model->get_missed_call_data_without_rating();
        if(!empty($data)){
            foreach ($data as $value) {

                //send sms according to booking status
                $smsTag = MISSED_CALL_RATING_SMS;
                $smsData['good_rating_number'] = GOOD_MISSED_CALL_RATING_NUMBER;
                $smsData['poor_rating_number'] = POOR_MISSED_CALL_RATING_NUMBER;
                $this->prepare_sms_data_to_send($smsTag, $value['phn_number'], $smsData, $value['booking_id'], "User", $value['user_id']);
            }
        }
    }
    /**
     * @desc This is used to send GST notification email
     */
    function sent_mail_for_gst_notification(){
        log_message('info', __METHOD__ . '=> Entering...');
        $data = $this->around_scheduler_model->get_vendor_email_contact_no();
        if(!empty($data['email'])){
            $template =  $this->booking_model->get_booking_email_template("gst_notification");
            $body = $template[0];
            $to = $template[1];
            $from = $template[2];
            $cc = $template[3];
            $subject = $template[4];

            $bcc = $data['email'];
           
            $this->notify->sendEmail($from, $to, $cc, $bcc, $subject, $body, "");
        }
        log_message('info', __METHOD__ . '=> EXIT...');
        
    }
    
    function send_mail_for_pan_notification() {
        log_message('info', __METHOD__ . '=> Entering...');

        $select = "id,name,CONCAT(primary_contact_email,',',owner_email) as email";
        $where = array("(pan_no IS Null OR pan_no = '')" => null, 'active' => 1);
        $data = $this->vendor_model->getVendorDetails($select, $where);
        if (!empty($data)) {
            foreach ($data as $val) {
                $rm_mail = $this->vendor_model->get_rm_sf_relation_by_sf_id($val['id'])[0]['official_email'];

                $template = $this->booking_model->get_booking_email_template("pan_notification");
                $body = $template[0];
                $to = $val['email'];
                $from = $template[2];
                $cc = $template[3] . ',' . $rm_mail;
                $subject = vsprintf($template[4], $val['name']);
                
                $this->notify->sendEmail($from, $to, $cc, '', $subject, $body, "");
            }
        }
    }
    
    function send_mail_for_bank_details_notification() {
        log_message('info', __METHOD__ . '=> Entering...');

        $select = "id,name,CONCAT(primary_contact_email,',',owner_email) as email";
        $where = array("(bank_name IS Null OR bank_name = ''
                         OR bank_account IS Null OR bank_account = '' 
                         OR ifsc_code IS Null OR ifsc_code = ''
                         OR cancelled_cheque_file IS Null OR cancelled_cheque_file = ''  )" => null, 'active' => 1);
        $data = $this->vendor_model->getVendorDetails($select, $where);
        if (!empty($data)) {
            foreach ($data as $val) {
                $rm_mail = $this->vendor_model->get_rm_sf_relation_by_sf_id($val['id'])[0]['official_email'];

                $template = $this->booking_model->get_booking_email_template("bank_details_notification");
                $body = $template[0];
                $to = $val['email'];
                $from = $template[2];
                $cc = $template[3] . ',' . $rm_mail;
                $subject = vsprintf($template[4], $val['name']);
                
                $this->notify->sendEmail($from, $to, $cc, '', $subject, $body, "");
            }
        }
    }
    
    function send_mail_for_bank_details_not_verified_notification() {
        log_message('info', __METHOD__ . '=> Entering...');

        $select = "id,name";
        $where = array("(bank_name IS NOT NULL AND bank_name <> ''
                         AND bank_account IS NOT NULL AND bank_account <> '' 
                         AND ifsc_code IS NOT NULL AND ifsc_code <> ''
                         AND cancelled_cheque_file IS NOT NULL AND cancelled_cheque_file <> ''  
                         AND is_verified = 0 )" => null, 'active' => 1);
        $data = $this->vendor_model->getVendorDetails($select, $where);
        if (!empty($data)) {
            
            $table_template = array(
                    'table_open' => '<table border="1" cellpadding="4" cellspacing="0">'
                );
           
            $this->table->set_template($table_template);
            $this->table->set_heading(array('ID','SF Name'));
            $table = $this->table->generate($data);
            $template = $this->booking_model->get_booking_email_template("bank_details_not_verified_notification");
            $body = vsprintf($template[0], $table);
            $to = $template[1];
            $from = $template[2];
            $subject = $template[4];
                
            $this->notify->sendEmail($from, $to, '', '', $subject, $body, "");
        }
    }
    /**
     * @desc: This function is used to call to assign tat breach. It called from Cron
     */
    function assign_tat_breach_order(){
        $post['length'] = -1;
        $post['where_in'] = array('current_status' => array('In-Transit', 'New Item In-transit', 'Attempted','Lost', 'Unknown'),
            'internal_status' => array('In-Transit', 'New Item In-transit', 'Attempted','Lost', 'Unknown'));
        $post['column_order'] = array( NULL, NULL,'services', 'city','order_date', 'current_status');
        $post['where'] = array('order_date <= ' => date('Y-m-d', strtotime("-30 days")));
        $post['column_search'] = array();
        $select = "bb_order_details.id, bb_order_details.partner_order_id";
        $list = $this->bb_model->get_bb_order_list($post, $select);
        
        foreach($list as $value){
            log_message("info", __METHOD__." TAT BREACH ORDER ID =>".$value->partner_order_id);
            $where['partner_order_id'] = $value->partner_order_id;
            $this->bb_model->update_bb_order_details($where, array('current_status' =>_247AROUND_BB_TO_BE_CLAIMED, 
                'internal_status' => _247AROUND_BB_ORDER_TAT_BREACH));
            $this->buyback->insert_bb_state_change($value->partner_order_id, _247AROUND_BB_TO_BE_CLAIMED, _247AROUND_BB_ORDER_TAT_BREACH, _247AROUND_DEFAULT_AGENT, _247AROUND, NULL);
            
            $this->cp_model->update_bb_cp_order_action($where, array('current_status' => _247AROUND_BB_NOT_DELIVERED, 'internal_status' =>_247AROUND_BB_247APPROVED_STATUS));
        }
    }
    
     /*
      *@desc: This function is use to process sms deactivation request from user 
      *This Function fetch deactivation request from email, and update user table for requested numbers
      *This Function calls by cron
     */
    function process_sms_deactivation_request(){
        $to_date = date('Y-m-d');
        $from_date = date('Y-m-d',(strtotime (SMS_DEACTIVATION_SCRIPT_RUNNING_DAYS , strtotime($to_date))));
        //create connection for email
        $conn = $this->email_data_reader->create_email_connection(SMS_DEACTIVATION_MAIL_SERVER,SMS_DEACTIVATION_EMAIL,SMS_DEACTIVATION_PASSWORD);
        if($conn != 'FALSE'){
            //get emails for previous day
            $email_data = $this->email_data_reader->fetch_emails_between_two_dates($to_date,$from_date);
            //get numbers array, from which we get deactivation request before 1 day
            $count = count($email_data);
            $numbers = array();
            for($i=0;$i<$count;$i++){
                if (strpos($email_data[$i][0]->subject, SMS_DEACTIVATION_EMAIL_SUBJECT) !== false) {
                    //get numbers
                    $numbers[] = substr(trim(explode(SMS_DEACTIVATION_EMAIL_SUBJECT,$email_data[$i][0]->subject)[0]),-10);
                }
            }
            //update sms_activation status for requested numbers
            if(!empty($numbers)){
                $updated_rows = $this->user_model->update_sms_deactivation_status($numbers);
                if($updated_rows>0){
                    $length = count($numbers);
                    for($j=0;$j<$length;$j++){
                        log_message('info', 'NDNC has been activated for '.$numbers[$j]);
                    }
                }
                else{
                    log_message('info', 'NDNC already activated');
                }
            }
            else{
                log_message('info', 'There is not any new request for NDNC');
            }
            //close email connection
            $this->email_data_reader->close_email_connection();
        }
        else{
            log_message('info', 'Connection is not created');
        }
    }
}
