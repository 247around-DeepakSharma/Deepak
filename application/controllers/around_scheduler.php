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
        $this->load->model('partner_model');
        $this->load->model('vendor_model');
        $this->load->model('invoices_model');
        $this->load->model('employee_model');
        $this->load->model('reusable_model');
        $this->load->model('dashboard_model');
        $this->load->model('paytm_payment_model');
        $this->load->library('partner_cb');
        $this->load->library('partner_sd_cb');
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
        $this->load->library('miscelleneous');
        $this->load->library('paytm_payment_lib');
        $this->load->library('push_notification_lib');
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

        foreach ($data1 as $value) {
            $status = $this->notify->sendTransactionalSmsMsg91($value->booking_primary_contact_no, $value->content);
            log_message('info', __METHOD__ . print_r($status, 1));
            if (ctype_alnum($status['content']) && strlen($status['content']) == 24) {
                $this->notify->add_sms_sent_details($value->type_id, $value->type, $value->booking_primary_contact_no, $value->content, $value->booking_id, $tag, $status['content']);

                $this->booking_model->increase_escalation_reschedule($value->booking_id, "sms_count");
            } else {
                $this->notify->add_sms_sent_details($value->type_id, $value->type, $value->booking_primary_contact_no, $value->content, $value->booking_id, $status['content']);

                log_message('info', "Message Not Sent - Booking id: " . $value->booking_id . ",
                please recheck tag: '" . $tag . "' & Phone Number - " . $value->booking_primary_contact_no);

                $subject = 'SMS Sending Failed';
                $message = "Please check SMS tag and phone number. Booking id is : " .
                        $value->booking_id . " Tag is '" . $tag . "' & phone number is :" . $value->booking_primary_contact_no . " Result:"
                        . " " . $status['content'];
                $to = ANUJ_EMAIL_ID;

                $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, "", "", $subject, $message, "",SMS_SENDING_FAILED);
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

            $this->notify->insert_state_change($value['booking_id'], "Cancelled", "FollowUp", "Customer not reachable / Customer not picked phone", _247AROUND_DEFAULT_AGENT, 
                    _247AROUND_DEFAULT_AGENT_NAME, ACTOR_BOOKING_CANCELLED,NEXT_ACTION_CANCELLED_BOOKING,_247AROUND);
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
            $this->notify->insert_state_change($booking_id, $data['current_status'], _247AROUND_FOLLOWUP, $data['cancellation_reason'], '1', '247around', ACTOR_BOOKING_CANCELLED,
                    NEXT_ACTION_CANCELLED_BOOKING,_247AROUND);

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
            $this->notify->insert_state_change($booking_id, $data['current_status'], _247AROUND_FOLLOWUP, $data['cancellation_reason'], '1', '247around',ACTOR_BOOKING_CANCELLED,
                    NEXT_ACTION_CANCELLED_BOOKING, _247AROUND);

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
            'new_send_service_center_report_mail', 'send_leads_summary_mail_to_partners',
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
            $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, "", "", $subject, $html, "",ERROR_IN_CRONE);
        }
    }

    /**
     * @desc: Checks balance ACL credits, used to send SMSes
     */
    function check_acl_credits() {
        $subject = "ACL Balance Credits - " . date("d-M-Y");
        $message = system('elinks -dump "https://push3.maccesssmspush.com/servlet/com.aclwireless.pushconnectivity.listeners.ConfigurationListener?action=prepaid&userid=blackmalt&pass=blackmalt67&appid=blackmalt&subappid=blackmalt"');

        $to = ANUJ_EMAIL_ID;
        $this->notify->sendEmail(SYS_HEALTH_EMAIL, $to, "", "", $subject, $message, "",ACL_BALANCE_CREDIT);
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
                $rm_email .= $value['official_email'];
                $rm_email .= ",";
            }

            $admin_gp = $this->employee_model->get_employee_email_by_group('admin');
            $admin_email = '';
            foreach ($admin_gp as $key => $value) {
                $admin_email .= $value['official_email'];
                $admin_email .= ",";
            }

            $to = substr($rm_email, 0, -1);
            $cc = substr($admin_email, 0, -1);

            $message = "Please find attached bookings list where SF is not available in the respective pincode.";

            $this->email->clear(TRUE);
            $this->email->from(NOREPLY_EMAIL_ID, '247around Team');
            $this->email->to($to);
            $this->email->cc($cc);
            $this->email->subject("SF NOT AVAILABLE IN PINCODES LIST");
            $this->email->message($message);
            $this->email->attach($csv, 'attachment');

            if ($this->email->send()) {
                $this->notify->add_email_send_details(NOREPLY_EMAIL_ID, $to, $cc, "", "SF NOT AVAILABLE IN PINCODES LIST", $message, $csv,SF_NOT_FOUND);
                log_message('info', __METHOD__ . ": Mail sent successfully for PinCode Not Available To RM ");
            } else {
                log_message('info', __METHOD__ . ": Mail could not be sent to RM");
            }
        } else {
            $to = ANUJ_EMAIL_ID;
            $message = "No booking found which pincode is not available";

            $this->email->clear(TRUE);
            $this->email->from(NOREPLY_EMAIL_ID, '247around Team');
            $this->email->to("$to");
            $this->email->cc(ANUJ_EMAIL_ID);
            $this->email->subject("Pincode Not Available Booking Data");
            $this->email->message($message);
            $this->email->attach($csv, 'attachment');
            $this->email->send();
            $this->notify->add_email_send_details(NOREPLY_EMAIL_ID, $to, ANUJ_EMAIL_ID, "", "Pincode Not Available Booking Data", $message, $csv,"no_booking_for_pincode_not_available");
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
        $smsTag_arr1 = array(1, 3, 5, 7, 9, 11);
        $smsTag_arr2 = array(2, 4, 6, 8, 12);
        $cur_month = date('n');
        switch ($booking_status) {
            case 'Completed':
                if (in_array($cur_month, $smsTag_arr1)) {
                    $smsTag = COMPLETED_PROMOTINAL_SMS_1;
                } else if (in_array($cur_month, $smsTag_arr2)) {
                    $smsTag = COMPLETED_PROMOTINAL_SMS_2;
                }
                break;
            case 'Cancelled':
                if (in_array($cur_month, $smsTag_arr1)) {
                    $smsTag = CANCELLED_PROMOTINAL_SMS_1;
                } else if (in_array($cur_month, $smsTag_arr2)) {
                    $smsTag = CANCELLED_PROMOTINAL_SMS_2;
                }
                break;
            case 'Query':
                if (in_array($cur_month, $smsTag_arr1)) {
                    $smsTag = CANCELLED_QUERY_PROMOTINAL_SMS_1;
                } else if (in_array($cur_month, $smsTag_arr2)) {
                    $smsTag = CANCELLED_QUERY_PROMOTINAL_SMS_2;
                }
                break;
            case 'no_status':
                if (in_array($cur_month, $smsTag_arr1)) {
                    $smsTag = BOOKING_NOT_EXIST_PROMOTINAL_SMS_1;
                } else if (in_array($cur_month, $smsTag_arr2)) {
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
    function send_notification_mail_to_jeeves() {
        $hour = 3;
        if (date('H') < 12) {
            $hour = 18;
        }

        $data = $this->around_scheduler_model->get_status_changes_booking_with_in_hour($hour);
        $template = array(
            'table_open' => '<table border="1" cellpadding="2" cellspacing="1" class="mytable">'
        );

        $this->table->set_template($template);

        $this->table->set_heading(array('Order ID', 'Jeeves New Status', 'Rescheduled Booking Date', 'Remarks', 'Amount Collected'));
        foreach ($data as $value) {

            $this->table->add_row($value['order_id'], $value['partner_current_status'], $value['booking_date'], $value['cancellation_reaoson'], $value['amount_paid']);
        }

        $to = NITS_ANUJ_EMAIL_ID;
        $cc = "";

        $subject = "Jeeves Booking Update Status";
        $message = "Dear Partner,<br/> Attached is the status of the last " . $hour . " hour <br/><br/><br/>";
        $message .= $this->table->generate();

        $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, "", $subject, $message, "",JEEVES_BOOKING_STATUS_UPDATE);
    }

    /**
     * @desc: This function is used to send SMS to those users who don't give ratings but booking is completed
     * @param:$from String(optional)
     * @param:$to String(optional)
     * @retun:void()
     */
    public function send_sms_for_rating_on_booking_completed($from = "", $to = "") {

        $data = $this->around_scheduler_model->get_data_for_bookings_without_rating($from, $to);

        if (!empty($data)) {
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
    public function send_missed_call_rating_sms_again() {
        $data = $this->around_scheduler_model->get_missed_call_data_without_rating();
        if (!empty($data)) {
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
    function sent_mail_for_gst_notification() {
        log_message('info', __METHOD__ . '=> Entering...');
        $data = $this->around_scheduler_model->get_vendor_email_contact_no();
        if (!empty($data['email'])) {
            $template = $this->booking_model->get_booking_email_template("gst_notification");
            $body = $template[0];
            $to = $template[1];
            $from = $template[2];
            $cc = $template[3];
            $subject = $template[4];

            $bcc = $data['email'];

            $this->notify->sendEmail($from, $to, $cc, $bcc, $subject, $body, "",'gst_notification');
        }
        if (!empty($data['id'])) {
            $idArray = explode(",", $data['id']);
            //Send Push Notification
            $receiverArray['vendor'] = $idArray;
            $this->push_notification_lib->create_and_send_push_notiifcation(GST_DETAILS_REQUEST, $receiverArray, array());
            //End Push Notification
        }
        log_message('info', __METHOD__ . '=> EXIT...');
    }

    function send_mail_for_pan_notification() {
        $idArray = array();
        log_message('info', __METHOD__ . '=> Entering...');
        $select = "id,name,CONCAT(primary_contact_email,',',owner_email) as email";
        $where = array("(pan_no IS Null OR pan_no = '')" => null, 'active' => 1);
        $data = $this->vendor_model->getVendorDetails($select, $where);
        if (!empty($data)) {
            foreach ($data as $val) {
                $idArray[] = $val['id'];
                $rm_mail = $this->vendor_model->get_rm_sf_relation_by_sf_id($val['id'])[0]['official_email'];
                $template = $this->booking_model->get_booking_email_template("pan_notification");
                $body = $template[0];
                $to = $val['email'];
                $from = $template[2];
                $cc = $template[3] . ',' . $rm_mail;
                $subject = vsprintf($template[4], $val['name']);
                $this->notify->sendEmail($from, $to, $cc, '', $subject, $body, "",'pan_notification');
            }
        }
        if (!empty($idArray)) {
            //Send Push Notification
            $receiverArray['vendor'] = $idArray;
            $this->push_notification_lib->create_and_send_push_notiifcation(PAN_DETAILS_REQUEST, $receiverArray, array());
            //End Push Notification
        }
    }

    function send_mail_for_bank_details_notification() {
        $idArray = array();
        log_message('info', __METHOD__ . '=> Entering...');
        $where = array("(account_holders_bank_details.bank_name IS NULL
                         OR account_holders_bank_details.bank_account IS NULL
                         OR account_holders_bank_details.ifsc_code IS NULL
                         OR account_holders_bank_details.cancelled_cheque_file IS NULL
                         OR account_holders_bank_details.beneficiary_name IS NULL
                         OR account_holders_bank_details.account_type IS NULL
                         OR (account_holders_bank_details.entity_id IS NULL))" => NULL, 'service_centres.active' => 1, 'account_holders_bank_details.entity_type' => 'SF', 'account_holders_bank_details.is_active' => 1);
        $join = array("service_centres" => "account_holders_bank_details.entity_id=service_centres.id");
        $JoinTypeTableArray = array("service_centres" => 'right');
        $data_1 = $this->reusable_model->get_search_result_data("account_holders_bank_details", "service_centres.id,service_centres.name,CONCAT(service_centres.primary_contact_email,',',service_centres.owner_email) as email", $where, $join, NULL, NULL, NULL, $JoinTypeTableArray);
        $sql = "SELECT service_centres.id,service_centres.name,CONCAT(service_centres.primary_contact_email,',',service_centres.owner_email) as email FROM service_centres WHERE "
                . "service_centres.id NOT IN (SELECT account_holders_bank_details.entity_id FROM account_holders_bank_details WHERE account_holders_bank_details.entity_type='SF' "
                . "AND account_holders_bank_details.is_active=1) AND service_centres.active=1";
        $data_2 = $this->reusable_model->execute_custom_select_query($sql);
        $data = array_merge($data_1, $data_2);
        if (!empty($data)) {
            foreach ($data as $val) {
                $idArray[] = $val['id'];
                $rm_mail = $this->vendor_model->get_rm_sf_relation_by_sf_id($val['id'])[0]['official_email'];
                $template = $this->booking_model->get_booking_email_template("bank_details_notification");
                $body = $template[0];
                $to = $val['email'];
                $from = $template[2];
                $cc = $template[3] . ',' . $rm_mail;
                $subject = vsprintf($template[4], $val['name']);
                $this->notify->sendEmail($from, $to, $cc, '', $subject, $body, "",'bank_details_notification');
            }
        }
        if (!empty($idArray)) {
            //Send Push Notification
            $receiverArray['vendor'] = $idArray;
            $this->push_notification_lib->create_and_send_push_notiifcation(BANK_DETAILS_REQUEST, $receiverArray, array());
            //End Push Notification
        }
    }

    function send_mail_for_bank_details_not_verified_notification() {
        log_message('info', __METHOD__ . '=> Entering...');
        $select = "service_centres.id,service_centres.name";
        $where = array("(account_holders_bank_details.bank_name IS NOT NULL
                         AND account_holders_bank_details.bank_account IS NOT NULL
                         AND account_holders_bank_details.ifsc_code IS NOT NULL
                          AND account_holders_bank_details.account_type IS NOT NULL
                         AND account_holders_bank_details.cancelled_cheque_file IS NOT NULL
                         AND account_holders_bank_details.is_verified = 0 )" => null, 'service_centres.active' => 1, 'account_holders_bank_details.entity_type' => 'SF', 'account_holders_bank_details.is_active' => 1);
        $join = array("service_centres" => "account_holders_bank_details.entity_id=service_centres.id");
        $orderBYArray = array('service_centres.name' => 'ASC');
        $data = $this->reusable_model->get_search_result_data("account_holders_bank_details", $select, $where, $join, NULL, $orderBYArray, NULL, NULL);
        if (!empty($data)) {

            $table_template = array(
                'table_open' => '<table border="1" cellpadding="4" cellspacing="0">'
            );

            $this->table->set_template($table_template);
            $this->table->set_heading(array('ID', 'SF Name'));
            $table = $this->table->generate($data);
            $template = $this->booking_model->get_booking_email_template("bank_details_not_verified_notification");
            $body = vsprintf($template[0], $table);
            $to = $template[1];
            $from = $template[2];
            $subject = $template[4];

            $this->notify->sendEmail($from, $to, '', '', $subject, $body, "",'bank_details_not_verified_notification');
        }
    }

    /**
     * @desc: This function tags orders for TAT Breach if CP has not claimed them within 45 days.
     */
    function assign_tat_breach_order() {
        $post['length'] = -1;
        $post['where_in'] = array('current_status' => array('In-Transit', 'New Item In-transit', 'Attempted', 'Lost'),
            'internal_status' => array('In-Transit', 'New Item In-transit', 'Attempted', 'Lost'));
        $post['column_order'] = array(NULL, NULL, 'services', 'city', 'order_date', 'current_status');
        $post['where'] = array('order_date <= ' => date('Y-m-d', strtotime(TAT_BREACH_DAYS)));
        $post['column_search'] = array();
        $select = "bb_order_details.id, bb_order_details.partner_order_id";
        $list = $this->bb_model->get_bb_order_list($post, $select);

        foreach ($list as $value) {
            log_message("info", __METHOD__ . " TAT BREACH ORDER ID =>" . $value->partner_order_id);
            $where['partner_order_id'] = $value->partner_order_id;
            $this->bb_model->update_bb_order_details($where, array('current_status' => _247AROUND_BB_TO_BE_CLAIMED,
                'internal_status' => _247AROUND_BB_ORDER_TAT_BREACH));
            $this->buyback->insert_bb_state_change($value->partner_order_id, _247AROUND_BB_TO_BE_CLAIMED, _247AROUND_BB_ORDER_TAT_BREACH, _247AROUND_DEFAULT_AGENT, _247AROUND, NULL);

            $this->cp_model->update_bb_cp_order_action($where, array('current_status' => _247AROUND_BB_NOT_DELIVERED, 'internal_status' => _247AROUND_BB_247APPROVED_STATUS));
        }
    }

    /*
     * @desc: This function is use to process sms deactivation request from user
     * This Function fetch deactivation request from email, and update user table for requested numbers
     * This Function calls by cron
     */

    function process_sms_deactivation_request() {
        $to_date = date('Y-m-d');
        $from_date = date('Y-m-d', (strtotime(SMS_DEACTIVATION_SCRIPT_RUNNING_DAYS, strtotime($to_date))));
        //create connection for email
        $conn = $this->email_data_reader->create_email_connection(SMS_DEACTIVATION_MAIL_SERVER, SMS_DEACTIVATION_EMAIL, SMS_DEACTIVATION_PASSWORD);
        if ($conn != 'FALSE') {
            //get emails for previous day
            $email_data = $this->email_data_reader->fetch_emails_between_two_dates($to_date, $from_date);
            //get numbers array, from which we get deactivation request before 1 day
            $count = count($email_data);
            $numbers = array();
            for ($i = 0; $i < $count; $i++) {
                if (strpos($email_data[$i][0]->subject, SMS_DEACTIVATION_EMAIL_SUBJECT) !== false) {
                    //get numbers
                    $numbers[] = substr(trim(explode(SMS_DEACTIVATION_EMAIL_SUBJECT, $email_data[$i][0]->subject)[0]), -10);
                }
            }
            //update sms_activation status for requested numbers
            if (!empty($numbers)) {
                $updated_rows = $this->user_model->update_sms_deactivation_status($numbers);
                if ($updated_rows > 0) {
                    $length = count($numbers);
                    for ($j = 0; $j < $length; $j++) {
                        log_message('info', 'NDNC has been activated for ' . $numbers[$j]);
                    }
                } else {
                    log_message('info', 'NDNC already activated');
                }
            } else {
                log_message('info', 'There is not any new request for NDNC');
            }
            //close email connection
            $this->email_data_reader->close_email_connection();
        } else {
            log_message('info', 'Connection is not created');
        }
    }

    /**
     * @desc: This function is used to send those appliance data which are not verified yet
     * @param:void()
     * @return void()
     */
    function get_non_verified_appliance_description_data() {
        $data = $this->around_scheduler_model->get_non_verified_appliance_description_data();
        if (!empty($data)) {
            $this->table->set_heading('Product Description', 'Service Id', 'Category', 'Capacity', 'Brand', 'Is Verified');
            $template = array(
                'table_open' => '<table border="1" cellpadding="4" cellspacing="0">'
            );

            foreach ($data as $val) {
                $this->table->add_row($val['product_description'], $val['service_id'], $val['category'], $val['capacity'], $val['brand'], $val['is_verified']);
            }

            $this->table->set_template($template);
            $html_table = $this->table->generate();

            //send email
            $email_template = $this->booking_model->get_booking_email_template("non_verified_appliance_mail");
            $to = DEVELOPER_EMAIL;
            $subject = $email_template[4];
            $message = vsprintf($email_template[0], $html_table);

            $sendmail = $this->notify->sendEmail($email_template[2], $to, '', "", $subject, $message, "",'non_verified_appliance_mail');
            if ($sendmail) {
                log_message('info', __FUNCTION__ . ' Report Mail has been send successfully');
            } else {
                log_message('info', __FUNCTION__ . ' Error in Sending Mail to partner ');
            }
        }
    }

    /**
     * @desc: This function is used to send reminder mail when partner contract expired
     * @param: void()
     * @return: void()
     */
    function send_partner_contract_expiry_notification() {
        $data = $this->partner_model->getpartner_details('public_name,agreement_end_date', array('datediff(curdate(),agreement_end_date)  >= -30' => null, 'is_active' => 1));
        if (!empty($data)) {

            $template = array(
                'table_open' => '<table border="1" cellpadding="4" cellspacing="0">'
            );

            $this->table->set_template($template);
            $this->table->set_heading('Partner Name', 'Agreement End Date');
            $html_table = $this->table->generate($data);
            //send email

            $email_template = $this->booking_model->get_booking_email_template("partner_contract_expiry_reminder");
            $to = NITS_ANUJ_EMAIL_ID;
            $subject = $email_template[4];
            $message = vsprintf($email_template[0], $html_table);

            $sendmail = $this->notify->sendEmail($email_template[2], $to, "", "", $subject, $message, "",'partner_contract_expiry_reminder');

            if ($sendmail) {
                log_message('info', __FUNCTION__ . 'Report Mail has been send successfully');
            } else {
                log_message('info', __FUNCTION__ . 'Error in Sending Mail to partner ');
            }
        }
    }

    /**
     * @desc This method is used to send notification email to partner whose account type is prepaid and have low balance.
     */
    function send_notification_for_low_balance() {
        log_message("info", __METHOD__ . " Entering...");
        $partner_details = $this->partner_model->getpartner_details("partners.id, public_name, "
                . "is_active,invoice_email_to, invoice_email_cc, owner_phone_1 ,account_manager_id", array('is_prepaid' => 1, 'is_active' => 1));
        log_message("info", __METHOD__ . " All Active Prepaid Partner " . print_r($partner_details, true));

        foreach ($partner_details as $value) {
            log_message("info", __METHOD__ . " Active Prepaid Partner ID" . $value['id']);
            $am_email = "";
            if (!empty($value['account_manager_id'])) {
                $am_email = $this->employee_model->getemployeefromid($value['account_manager_id'])[0]['official_email'];
            }
            $final_amount = $this->miscelleneous->get_partner_prepaid_amount($value['id']);
            if ($final_amount['is_notification']) {

                if ($final_amount['active'] > 0) {

                    $sms['tag'] = "prepaid_low_balance";
                    $email_template = $this->booking_model->get_booking_email_template("low_prepaid_amount");
                } else {

                    $sms['tag'] = "prepaid_negative_balance";
                    $email_template = $this->booking_model->get_booking_email_template("negative_prepaid_balance");
                }

                $receiverArray['partner'] = array($value['id']);
                $notificationTextArray['msg'] = array($final_amount['prepaid_amount']);
                $this->push_notification_lib->create_and_send_push_notiifcation(LOW_PREPAID_AMOUNT, $receiverArray, $notificationTextArray);

                $message = vsprintf($email_template[0], array("Rs. " . $final_amount["prepaid_amount"]));
                $to = $value['invoice_email_to'];
                $cc = $value['invoice_email_cc'] . ", " . $email_template[3] . "," . $am_email;
                $subject = vsprintf($email_template[4], array($value["public_name"], "Rs. " . $final_amount["prepaid_amount"]));

                $sms['smsData']['prepaid_amount'] = "Rs. " . $final_amount["prepaid_amount"];
                $sms['booking_id'] = "";
                $sms['type'] = "partner";
                $sms['type_id'] = $value["id"];

                $sms['phone_no'] = $value['owner_phone_1'];
                //Send SMS
                $this->notify->send_sms_msg91($sms);
                log_message("info", __METHOD__ . " SMS Sent Active Prepaid Partner ID" . $value['id']);
                //Send tempalte
                $sendmail = $this->notify->sendEmail($email_template[2], $to, $cc, "", $subject, $message, "",'negative_prepaid_balance');
                if ($sendmail) {
                    log_message('info', __METHOD__ . 'Mail has been send successfully. Partner id => ' . $value['id']);
                } else {
                    log_message('info', __METHOD__ . 'Error in Sending Mail to partner Partner Id => ' . $value['id']);
                }
            }
        }
        log_message("info", __METHOD__ . " EXit...");
    }

    /**
     * @desc This funnction is used to calculate upcountry from India Pincode File
     */
    function get_upcountry_details_from_india_pincode() {
        $pincode_array = $this->vendor_model->getPincode_from_india_pincode();
        $partner_data = array();
        $partner_data[0]['is_upcountry'] = 0;
        $partner_data[0]['upcountry_approval_email'] = '';
        $services = $this->booking_model->selectservice();
        foreach ($services as $service_id) {
            $upcountry_data = array();
            foreach ($pincode_array as $key => $pincode) {
                $up_details = $this->miscelleneous->check_upcountry_vendor_availability("", $pincode['pincode'], $service_id->id, $partner_data);
                $data = array();
                $data['pincode'] = $pincode['pincode'];
                $data['service_id'] = $service_id->id;
                $data['hq_pincode'] = NULL;
                $data['sub_vendor_id'] = 0;
                $data['distance'] = 0;
                $data['vendor_id'] = 0;
                $data['sf_upcountry_rate'] = 0;

                switch ($up_details['message']) {
                    case UPCOUNTRY_BOOKING:
                    case UPCOUNTRY_LIMIT_EXCEED:
                        $data['is_upcountry'] = 1;
                        $data['hq_pincode'] = $up_details['upcountry_pincode'];
                        $data['sub_vendor_id'] = $up_details['sub_vendor_id'];
                        $data['distance'] = $up_details['upcountry_distance'];
                        $data['vendor_id'] = $up_details['vendor_id'];
                        $data['sf_upcountry_rate'] = $up_details['sf_upcountry_rate'];
                        $data['remarks'] = UPCOUNTRY_BOOKING;

                        break;
                    case NOT_UPCOUNTRY_BOOKING:
                        $data['is_upcountry'] = 0;
                        $data['vendor_id'] = $up_details['vendor_id'];
                        $data['remarks'] = NOT_UPCOUNTRY_BOOKING;
                        break;
                    case UPCOUNTRY_DISTANCE_CAN_NOT_CALCULATE:
                        $data['is_upcountry'] = 0;
                        $data['hq_pincode'] = $up_details['upcountry_pincode'];
                        $data['sub_vendor_id'] = $up_details['sub_vendor_id'];
                        $data['sf_upcountry_rate'] = $up_details['sf_upcountry_rate'];
                        $data['remarks'] = UPCOUNTRY_DISTANCE_CAN_NOT_CALCULATE;
                        break;
                    case SF_DOES_NOT_EXIST:
                        $data['is_upcountry'] = 0;
                        if (isset($up_details['vendor_not_found'])) {
                            $data['remarks'] = SF_DOES_NOT_EXIST;
                        } else {
                            $data['remarks'] = NOT_UPCOUNTRY_BOOKING;
                        }
                        break;
                }

                $data['response'] = json_encode($up_details, TRUE);
                echo "No -" . $key . PHP_EOL;
                print_r($data);
                array_push($upcountry_data, $data);
            }

            $this->upcountry_model->upcountry_pincode_services_sf_level($upcountry_data);
        }
    }

    /**
     * @desc     Get Buyback QC Balance From Email
     * @param    void()
     * @return   void()
     */
    function get_bb_balance_from_email() {
        log_message('info', __METHOD__);
        $data = array();
        $mail_server = SMS_DEACTIVATION_MAIL_SERVER;
        $email = QC_BALANCE_READ_EMAIL;
        $password = QC_BALANCE_READ_EMAIL_PASSWORD;
        //create connection for email
        $conn = $this->email_data_reader->create_email_connection($mail_server, $email, $password);
        if ($conn != 'FALSE') {
            //get emails for TV Balance
            $tv_condition = 'SINCE "' . date("d M Y", strtotime(date("Y-m-d"))) . '" SUBJECT "' . TV_BALANCE_EMAIL_SUBJECT . '"';
            $tv_email_data = $this->email_data_reader->get_emails($tv_condition);
            if (!empty($tv_email_data)) {
                $email_body = $tv_email_data[0]['body'];
                $match = array();
                $pattern_new = '/\bRs. (\d*\.?\d+)/';
                preg_match_all($pattern_new, $email_body, $match);
                if (!empty($match) && isset($match[1][0])) {
                    $data['tv_balance'] = $match[1][0];
                }
            }

            //get emails for LA Balance
            $la_condition = 'SINCE "' . date("d M Y", strtotime(date("Y-m-d"))) . '" SUBJECT "' . LA_BALANCE_EMAIL_SUBJECT . '"';
            $la_email_data = $this->email_data_reader->get_emails($la_condition);
            if (!empty($la_email_data)) {
                $email_body = $la_email_data[0]['body'];
                $match = array();
                $pattern_new = '/\bRs. (\d*\.?\d+)/';
                preg_match_all($pattern_new, $email_body, $match);
                if (!empty($match) && isset($match[1][0])) {
                    $data['la_balance'] = $match[1][0];
                }
            }

            //get emails for Mobile Balance
            $mobile_condition = 'SINCE "' . date("d M Y", strtotime(date("Y-m-d"))) . '" SUBJECT "' . MOBILE_BALANCE_EMAIL_SUBJECT . '"';
            $mobile_email_data = $this->email_data_reader->get_emails($mobile_condition);
            if (!empty($mobile_email_data)) {
                $email_body = $mobile_email_data[0]['body'];
                $match = array();
                $pattern_new = '/\bRs. (\d*\.?\d+)/';
                preg_match_all($pattern_new, $email_body, $match);
                if (!empty($match) && isset($match[1][0])) {
                    $data['mobile_balance'] = $match[1][0];
                }
            }

            //if balance is not empty then insert it into datatbase
            if (!empty($data)) {
                $insert_id = $this->around_scheduler_model->add_bb_svc_balance($data);
                if ($insert_id) {
                    log_message('info', "Amazon SVC balance has been inserted successfully. New Balance = " . print_r($data, true));
                } else {
                    log_message('info', "Error in inserting Amazon SVC balance." . print_r($data, true));
                }
            } else {
                log_message('info', "Mails Not Found For Today ");
            }
            //close email connection
            $this->email_data_reader->close_email_connection();
        } else {
            log_message('info', 'Connection is not created');
        }
    }

    function update_india_pincode_table() {
        $sql = "INSERT INTO india_pincode(area,pincode,division,region,taluk,district,state) SELECT booking_details.city as area,sf.pincode,booking_details.city as division,booking_details.taluk as region,"
                . "booking_details.taluk,booking_details.district,booking_details.state FROM "
                . "sf_not_exist_booking_details sf INNER JOIN booking_details  ON sf.booking_id=booking_details.booking_id WHERE sf.rm_id IS NULL  GROUP BY sf.pincode";
        $affectedRows = $this->reusable_model->execute_custom_insert_update_delete_query($sql);
        if ($affectedRows > 0) {
            $getRmSql = "SELECT india_pincode.pincode,employee_relation.agent_id as rm_id,india_pincode.state FROM india_pincode INNER JOIN state_code ON state_code.state=india_pincode.state LEFT JOIN employee_relation ON
FIND_IN_SET(state_code.state_code,employee_relation.state_code) WHERE india_pincode.pincode IN (SELECT sf.pincode FROM sf_not_exist_booking_details sf WHERE sf.rm_id IS NULL GROUP BY sf.pincode)
      GROUP BY india_pincode.pincode";
            $result = $this->reusable_model->execute_custom_select_query($getRmSql);
            if ($result) {
                foreach ($result as $data) {
                    $this->reusable_model->update_table("sf_not_exist_booking_details", $data, array('pincode' => $data['pincode']));
                }
            }
        }
    }

    /**
     * @desc: It called from Ajax
     * This is used to to auto acknowledge those booking whose deliver date less than 10 days(Compare current date)
     */
    function auto_acknowledge_buyback_order() {
        log_message("info", __METHOD__);
        $where['where'] = array("DATEDIFF( CURRENT_TIMESTAMP , delivery_date ) > 10 " => NULL, 'bb_order_details.current_status' => "Delivered",
            'bb_order_details.internal_status' => "Delivered", "bb_cp_order_action.current_status" => 'Pending');
        $where['select'] = "bb_order_details.partner_order_id";
        $where['length'] = -1;
        $data = $this->cp_model->get_bb_cp_order_list($where);
        if (!empty($data)) {
            foreach ($data as $value) {
                // Update bb order details
                $this->bb_model->update_bb_order_details(array('partner_order_id' => $value->partner_order_id), array("acknowledge_date" => date("Y-m-d H:i:s"), "current_status" => "Completed", "internal_status" => "Completed"));
                // Update Unit Details
                $this->bb_model->update_bb_unit_details(array('partner_order_id' => $value->partner_order_id), array("order_status" => "Delivered"));

                $cp['current_status'] = "Delivered";
                $cp['internal_status'] = 'Delivered';
                $cp['admin_remarks'] = AUTO_ACK_ADMIN_REMARKS;
                // Update Cp Action Table
                $this->cp_model->update_bb_cp_order_action(array('partner_order_id' => $value->partner_order_id), $cp);
                // Insert State Change
                $this->buyback->insert_bb_state_change($value->partner_order_id, "Auto Acknowledge", AUTO_ACK_ADMIN_REMARKS, _247AROUND_DEFAULT_AGENT, _247AROUND, NULL);
            }
        }
    }

    /**
     * @desc: This is used to call from cron to detect issues in booking for invoice purpose.
     */
    function developer_invoice_check() {
        log_message("info", "Enterring...");
        echo "Enterring..";
        $pendingData = $this->booking_model->get_unit_details(array("booking_status" => _247AROUND_PENDING, 'create_date >= ' => date('Y-m-01', strtotime("-2 months"))));
        echo "Peinding.." . count($pendingData);
        $partnerData = $this->booking_model->get_unit_details(array('booking_status' => _247AROUND_COMPLETED, "partner_invoice_id IS NULL" => NULL,
            "ud_closed_date >=" => date('Y-m-01', strtotime("-2 months"))));
        echo "Partner.." . count($partnerData);
        $focData = $this->booking_model->get_unit_details(array('booking_status' => _247AROUND_COMPLETED, "vendor_foc_invoice_id IS NULL" => NULL,
            "ud_closed_date >=" => date('Y-m-01', strtotime("-2 months")), 'around_to_vendor >' => 0));
        echo "FOC.." . count($partnerData);
        $data = array_merge($pendingData, $partnerData, $focData);
        $data1 = array_map("unserialize", array_unique(array_map("serialize", $data)));
        echo "DATA " . count($data1);
        $partner = $this->partner_model->get_all_partner_source();
        $partners = array();
        foreach ($partner as $value) {
            $partners[$value['partner_id']] = $value['partner_type'];
        }
        unset($partner);
        $incorrectData = array();
        foreach ($data1 as $value) {
            echo ".";
            if ($partners[$value['partner_id']] == OEM) {
                $prices = $this->partner_model->getPrices($value['service_id'], $value['appliance_category'], $value['appliance_capacity'], $value['partner_id'], $value['price_tags'], $value['appliance_brand']);
            } else {
                $prices = $this->partner_model->getPrices($value['service_id'], $value['appliance_category'], $value['appliance_capacity'], $value['partner_id'], $value['price_tags'], "");
            }

            if (!empty($prices)) {
                if ($value['customer_total'] != $prices[0]['customer_total']) {
                    array_push($incorrectData, array('booking_id' => $value['booking_id'], "status" => 'Customer Total Not Match'));
                }
                if ($value['vendor_basic_percentage'] != $prices[0]['vendor_basic_percentage']) {
                    array_push($incorrectData, array('booking_id' => $value['booking_id'], "status" => 'SF % Not Match'));
                }
                if ($value['partner_net_payable'] != $prices[0]['partner_net_payable']) {
                    array_push($incorrectData, array('booking_id' => $value['booking_id'], "status" => 'Partner Payable Not Match'));
                }
            } else {
                array_push($incorrectData, array('booking_id' => $value['booking_id'], "status" => 'Price Does Not Exist'));
            }
        }

        $this->dashboard_model->update_query_report(array('role' => 'developer', 'type' => 'invoice_check'), array('result' => json_encode($incorrectData, true)));
        log_message("info", "EXIT...");
    }

    /**
     * @desc This is used to send email with those booking who has pending to send defective parts
     */
    function send_notfication_to_send_defective_parts() {
        log_message("info", __METHOD__);
        $select = "id,name,CONCAT(primary_contact_email,',',owner_email) as sf_email";
        $where1 = array('active' => 1);
        $data = $this->vendor_model->getVendorDetails($select, $where1);
        foreach ($data as $value) {
            $where = array(
                "spare_parts_details.defective_part_required" => 1,
                "spare_parts_details.service_center_id" => $value['id'],
                "status IN ('" . DEFECTIVE_PARTS_PENDING . "', '" . DEFECTIVE_PARTS_REJECTED . "')  " => NULL,
                "DATEDIFF(CURRENT_TIMESTAMP, STR_TO_DATE(spare_parts_details.update_date, '%Y-%m-%d')) > '" . SEND_DEFECTIVE_SPARE_PARTS_NOTIFICATION . "'" => NULL
            );

            $select = "CONCAT( '', GROUP_CONCAT((parts_shipped ) ) , '' ) as parts_shipped, "
                    . " spare_parts_details.booking_id, name, spare_parts_details.update_date, "
                    . " DATEDIFF(CURRENT_TIMESTAMP, STR_TO_DATE(spare_parts_details.update_date, '%Y-%m-%d')) AS age_of_part_pending ";

            $group_by = "spare_parts_details.booking_id";
            $order_by = "status = '" . DEFECTIVE_PARTS_REJECTED . "', spare_parts_details.create_date ASC";
            $spare_parts = $this->service_centers_model->get_spare_parts_booking($where, $select, $group_by, $order_by);
            if (!empty($spare_parts)) {
                log_message("info", __METHOD__ . " " . $value['name'] . " has Defective Part Pending");
                $template1 = array(
                    'table_open' => '<table border="1" cellpadding="2" cellspacing="1" class="mytable">'
                );

                $this->table->set_template($template1);

                $this->table->set_heading(array('Booking ID', 'Customer Name', 'Parts Name', 'Age of Part Pending'));
                foreach ($spare_parts as $sp) {

                    $this->table->add_row($sp['booking_id'], $sp['name'], $sp['parts_shipped'], $sp['age_of_part_pending'] . " Days");
                }

                $this->table->set_template($template1);
                $html_table = $this->table->generate();

                $rm_details = $this->vendor_model->get_rm_sf_relation_by_sf_id($value['id']);
                $to = $value['sf_email'];
                $bcc = "";

                $template = $this->booking_model->get_booking_email_template("notification_to_send_defective_parts");
                $body = vsprintf($template[0], $html_table);

                $from = $template[2];
                $cc = $template[3] . ", " . $rm_details[0]['official_email'];
                $subject = vsprintf($template[4], $value['name']);

                $this->notify->sendEmail($from, $to, $cc, $bcc, $subject, $body, "",'notification_to_send_defective_parts');
                log_message("info", __METHOD__ . " " . $value['name'] . " Email Sent");
                //Send Push Notification
                $receiverArray['vendor'] = array($value['id']);
                $notificationTextArray['msg'] = array($sp['booking_id'], $sp['age_of_part_pending']);
                $this->push_notification_lib->create_and_send_push_notiifcation(PENDING_SPARE_VENDOR, $receiverArray, $notificationTextArray);
                //End Push Notification
            }
        }
    }

    /*
     * This function is used to Approve rescheduled requests Automatically
     * If we did'nt get any miss call for fake reschedule within 4 hours of reschedule request then we will automatically approve that rescheduled request
     */

    function auto_approval_for_booking_rescheduled_request() {
        date_default_timezone_set('Asia/Calcutta');
        //Get Current Rescheduled request Bookings
        $reviewBookingsArray = $this->booking_model->review_reschedule_bookings_request();
        $id = _247AROUND_DEFAULT_AGENT;
        $employeeID = _247AROUND_DEFAULT_AGENT_NAME;
        $partner_id_array = array();
        if (!empty($reviewBookingsArray)) {
            foreach ($reviewBookingsArray as $bookingData) {
                $rescheduledTime = date_create($bookingData['reschedule_request_date']);
                $currentTime = date_create();
                $diff = date_diff($rescheduledTime, $currentTime);
                $timeDiffInHours = $diff->h;
                // IF request Time is greater then 4 hours then approvad the rescheduled
                if ($timeDiffInHours > 4) {
                    $reschedule_booking_id[] = $bookingData['booking_id'];
                    $reschedule_booking_date[$bookingData['booking_id']] = $bookingData['reschedule_date_request'];
                    $reschedule_reason[$bookingData['booking_id']] = $bookingData['reschedule_reason'];
                }
                
                $partner_id_array[$bookingData['booking_id']] = $bookingData['partner_id'];
                
            }
            $this->miscelleneous->approved_rescheduled_bookings($reschedule_booking_id, $reschedule_booking_date, $reschedule_reason, $partner_id_array, $id, $employeeID);
        }
    }

    /**
     * @desc     used to get all the unread email's from installations@247around.com
     * @param    void()
     * @return   void()
     */
    function get_unread_email_details() {
        log_message('info', __METHOD__ . " Entering...");

        $mail_server = SMS_DEACTIVATION_MAIL_SERVER;
        $email = EMAIL_ATTACHMENT_READER_EMAIL;
        $password = EMAIL_ATTACHMENT_READER_PASSWORD;

        //create email connection
        $conn = $this->email_data_reader->create_email_connection($mail_server, $email, $password);
        if ($conn != 'FALSE') {
            log_message('info', __METHOD__ . " Email connection created successfully.");
            //get the email list according to search condition
            $email_search_condition = 'UNSEEN';
            $email_list = $this->email_data_reader->get_emails($email_search_condition);

            if (!empty($email_list)) {
                log_message("info", __METHOD__ . " Emails Found");

                $template1 = array(
                    'table_open' => '<table border="1" cellpadding="2" cellspacing="0" class="mytable">'
                );

                $this->table->set_template($template1);

                $this->table->set_heading(array('From', 'Subject', 'attachment'));
                foreach ($email_list as $email) {
                    $attachments = array();
                    if (!empty($email['attachments'])) {
                        foreach ($email['attachments'] as $attachment) {
                            if (!empty($attachment['file_name'])) {
                                array_push($attachments, $attachment['file_name']);
                            }
                        }
                    }

                    $this->table->add_row($email['from'], $email['subject'], implode(',', $attachments));
                }

                $html_table = $this->table->generate();

                //get template from database
                $template = $this->booking_model->get_booking_email_template("get_unread_email_template");
                $body = vsprintf($template[0], $html_table);

                $to = DEVELOPER_EMAIL;
                $from = $template[2];
                $cc = $template[3];
                $subject = $template[4];

                $this->notify->sendEmail($from, $to, $cc, "", $subject, $body, "",'get_unread_email_template');
            }
        } else {
            log_message('info', __METHOD__ . "Error in creating email connection");
            $subject = "Error in creating email connection for reading email";
            $msg = "There was some error in creating connection to email server to get the details of unread emails";
            $msg .= "<br><b>File Name: </b> " . __CLASS__;
            $msg .= "<br><b>Function Name: </b> " . __METHOD__;
            $this->notify->sendEmail(NOREPLY_EMAIL_ID, DEVELOPER_EMAIL, '', "", $subject, $msg, "",ERROR_IN_CREATING_EMAIL_CONNECTION);
        }

        //close email connection
        $this->email_data_reader->close_email_connection();
        log_message('info', __METHOD__ . " Existing...");
    }

    /**
     * @desc     used to send the reminder to sf and rm to update signature file
     * @param    void()
     * @return   void()
     */
    function notification_for_sf_signature() {
        log_message('info', __METHOD__ . '=> Entering...');
        $rm_details = $this->employee_model->get_rm_details();
        foreach ($rm_details as $rm) {
            $sf_list = $this->vendor_model->get_employee_relation($rm['id']);
            $select = "group_concat(name) as name,group_concat(primary_contact_email,',',owner_email) as email";
            $where = array('is_gst_doc' => 0, 'active' => 1, '(is_signature_doc IS null OR is_signature_doc = 0)' => NULL, '(signature_file IS Null OR signature_file = "")' => NULL, "id IN(" . $sf_list[0]['service_centres_id'] . ")" => NULL);
            $data = $this->vendor_model->getVendorDetails($select, $where);
            if (!empty($data[0]['email'])) {
                log_message("info", __METHOD__ . " Data Found " . print_r($data, true));
                //send mail to sf
                $sf_template = $this->booking_model->get_booking_email_template("sf_signature_notification");
                $sf_body = $sf_template[0];
                $sf_to = $sf_template[1];
                $sf_from = $sf_template[2];
                $sf_cc = $sf_template[3];
                $sf_bcc = $data[0]['name'];
                $sf_subject = $sf_template[4];
                $this->notify->sendEmail($sf_from, $sf_to, $sf_cc, $sf_bcc, $sf_subject, $sf_body, "",'sf_signature_notification');
                //send sf_list to rm
                $template1 = array(
                    'table_open' => '<table border="1" cellpadding="2" cellspacing="0" class="mytable">'
                );

                $this->table->set_template($template1);

                $this->table->set_heading(array('SF Name'));
                foreach (explode(',', $data[0]['name']) as $value) {
                    $this->table->add_row($value);
                }

                $html_table = $this->table->generate();
                $rm_template = $this->booking_model->get_booking_email_template("sf_signature_notification_for_rm");
                $rm_body = vsprintf($rm_template[0], $html_table);
                $rm_to = $rm['official_email'] . "," . $rm_template[1];
                $rm_from = $rm_template[2];
                $rm_cc = $rm_template[3];
                $rm_subject = $rm_template[4];
                $this->notify->sendEmail($rm_from, $rm_to, $rm_cc, '', $rm_subject, $rm_body, "",'sf_signature_notification_for_rm');
            }else{
                log_message("info",__METHOD__." No Data Found For RM ".$rm['full_name']);
            } 
        }
    }

    /**
     * @desc     used to send the reminder to sf and rm to update GST file
     * @param    void()
     * @return   void()
     */
    function notification_for_sf_gst() {
        log_message('info', __METHOD__ . '=> Entering...');
        $rm_details = $this->employee_model->get_rm_details();
        foreach ($rm_details as $rm) {
            $sf_list = $this->vendor_model->get_employee_relation($rm['id']);
            $select = "group_concat(name) as name,group_concat(primary_contact_email,',',owner_email) as email";
            $where = array('is_gst_doc IS NULL' => NULL, 'active' => 1, "id IN(" . $sf_list[0]['service_centres_id'] . ")" => NULL);
            $data = $this->vendor_model->getVendorDetails($select, $where);
            if (!empty($data[0]['email'])) {
                log_message("info", __METHOD__ . " Data Found " . print_r($data, true));
                //send mail to sf
                $sf_template = $this->booking_model->get_booking_email_template("gst_notification");
                $sf_body = $sf_template[0];
                $sf_to = $sf_template[1];
                $sf_from = $sf_template[2];
                $sf_cc = $sf_template[3];
                $sf_bcc = $data[0]['name'];
                $sf_subject = $sf_template[4];
                $this->notify->sendEmail($sf_from, $sf_to, $sf_cc, $sf_bcc, $sf_subject, $sf_body, "",'gst_notification');
                //send sf_list to rm
                $template1 = array(
                    'table_open' => '<table border="1" cellpadding="2" cellspacing="0" class="mytable">'
                );

                $this->table->set_template($template1);

                $this->table->set_heading(array('SF Name'));
                foreach (explode(',', $data[0]['name']) as $value) {
                    $this->table->add_row($value);
                }

                $html_table = $this->table->generate();
                $rm_template = $this->booking_model->get_booking_email_template("sf_gst_notification_for_rm");
                $rm_body = vsprintf($rm_template[0], $html_table);
                $rm_to = $rm['official_email'] . "," . $rm_template[1];
                $rm_from = $rm_template[2];
                $rm_cc = $rm_template[3];
                $rm_subject = $rm_template[4];
                $this->notify->sendEmail($rm_from, $rm_to, $rm_cc, '', $rm_subject, $rm_body, "",'sf_gst_notification_for_rm');
            }else{
                log_message("info",__METHOD__." No Data Found For RM ".$rm['full_name']);
            }
        }
    }

    /**
     * @desc This is used to refund cashback for those customer who had paid through Paytm
     */
    function paytm_payment_cashback() {
        //get Cashback Rules
        $rules = $this->paytm_payment_model->get_paytm_cashback_rules(array("active" => 1, "tag" => PAYTM_CASHBACK_TAG));
        if (!empty($rules)) {
            $transactionArray = $this->paytm_payment_model->get_without_cashback_transactions();
                foreach ($transactionArray as $transaction) {
                    $finalCashbackAmount = ($transaction['paid_amount'] * $rules[0]['cashback_amount_percentage']) / 100;
                    if ($finalCashbackAmount > 0) {
                        $status = $this->paytm_payment_lib->paytm_cashback($transaction['txn_id'], $transaction['order_id'], $finalCashbackAmount, CASHBACK_REASON_DISCOUNT, CASHBACK_CRONE);
                        $statusArray = json_decode($status, true);
                        if ($statusArray['status'] ==  'SUCCESS') 
                        {
                            log_message("info", __METHOD__ . "Cashback Processed Successfully For " . $transaction['txn_id']);
                            $this->reusable_model->update_table("paytm_transaction_callback", array("discount_flag" => 1), array('txn_id' => $transaction['txn_id']));
                        } 
                        else 
                        {
                            log_message("error", __METHOD__ . "Cashback Process Failed For " . $transaction['txn_id']);
                        }
                   }  
                }
            }
    }

    /**
     * @desc: This method is used to send qr sms to customer.
     * It will send sms only those customer whose booking is assigned to sf.
     * Booking must be Pending/Reschedule
     */
    function send_qrCode_sms_to_customer() {
        log_message("info", __METHOD__ . " Entering.....");
        $booking = $this->booking_model->get_bookings_count_by_any("services, assigned_vendor_id, booking_id, "
                . "user_id, booking_primary_contact_no", array('current_status IN ("' . _247AROUND_PENDING . '", "' . _247AROUND_RESCHEDULED . '") ' => NULL,
            'amount_due > 0' => NULL, 'assigned_vendor_id IS NOT NULL' => NULL));

        if (!empty($booking)) {
            foreach ($booking as $value) {
                $sf = $this->vendor_model->getVendorContact($value['assigned_vendor_id']);
                $userDownload = $this->paytm_payment_lib->generate_qr_code($value['booking_id'], QR_CHANNEL_SMS, 0, $sf[0]['primary_contact_phone_1']);
                log_message("info", __METHOD__ . " Booking id " . $value['booking_id'] . " User QR Response " . print_r($userDownload, true));

                $user = json_decode($userDownload, TRUE);
                if ($user['status'] == SUCCESS_STATUS) {

                    $url = S3_WEBSITE_URL . $user['qr_url'];
                    $tinyUrl = $this->miscelleneous->getShortUrl($url);
                    if ($tinyUrl) {

                        $sms['type'] = "user";
                        $sms['type_id'] = $value['user_id'];
                        $sms['tag'] = "customer_qr_download";
                        $sms['smsData']['services'] = $value['services'];
                        $sms['smsData']['url'] = $tinyUrl;

                        $sms['phone_no'] = $value['booking_primary_contact_no'];
                        $sms['booking_id'] = $value['booking_id'];

                        $this->notify->send_sms_msg91($sms);
                    } else {
                        log_message("info", __METHOD__ . " Booking id " . $value['booking_id'] . " Tiny url Not generated");
                    }
                } else {
                    log_message("info", __METHOD__ . " QR Not generated for booking id " . $value['booking_id']);
                }
            }
        }
    }
    /*
     * This function is used to cancel SF Not found queries after a threshold limit
     */
    function cancel_sf_not_found_query_after_threshold_limit(){
        log_message('info', __FUNCTION__ . " Function Start  ");
        $select = " booking_id,partner_id ";
        $data = $this->around_scheduler_model->get_vendor_pincode_unavailable_queries_by_days($select,THRESHOLD_LIMIT_TO_CANCEL_NOT_FOUND_SF_QUERIES);
        log_message('info', __FUNCTION__ . " Below Queries Needs to Cancel  ". print_r($data,true));
        if(!empty($data)){
            foreach($data as $bookingData){
                $booking_id = $bookingData['booking_id'];
                $status = _247AROUND_FOLLOWUP;
                $cancellation_reason = SF_NOT_FOUND_BOOKING_CANCELLED_REASON;
                $cancellation_text = SF_NOT_FOUND_BOOKING_CANCELLED_REASON_TEXT;
                $agent_id = _247AROUND_DEFAULT_AGENT;
                $agent_name = _247AROUND_DEFAULT_AGENT_NAME;
                $partner_id = $bookingData['partner_id'];
                $this->miscelleneous->process_cancel_form($booking_id, $status, $cancellation_reason, $cancellation_text, $agent_id, $agent_name, $partner_id);
            }
        }
        log_message('info', __FUNCTION__ . " Function End  ");
    }
    
    /**
     * @desc This is used to trigger partner callback manually
     * @param String $booking_id
     */
    function triggerPartnerCallBack($booking_id){
         $this->partner_cb->partner_callback($booking_id);
    }
}
