<?php

class Reporting_utils extends CI_Model {

    /**
     * @desc load both db
     */
    function __construct() {
        parent::__Construct();
    }

    //Get today's pending bookings to generate summary mail for Admin
    //Ignore queries and bookings having age < 3 days
    //@params: id - ID of Employee logged in
    //@return: user_group - Group of logged in user

    function get_pending_bookings($sf_list = "") {
        if ($sf_list != "") {
            $where = "booking_details.current_status IN ('Pending', 'Rescheduled') 
                AND service_centres.id  
                                IN ("
                    . $sf_list .
                    ")
                AND
        DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_details.booking_date, '%d-%m-%Y')) > 2
                AND booking_details.assigned_vendor_id IS NOT NULL";
        } else {
            $where = "booking_details.current_status IN ('Pending', 'Rescheduled') AND 
        DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_details.booking_date, '%d-%m-%Y')) > 2
                AND booking_details.assigned_vendor_id IS NOT NULL";
        }
        $query = $this->db->query("SELECT DISTINCT booking_details.booking_id,
                booking_details.booking_address,
                booking_details.booking_pincode,
                booking_details.booking_date,
                booking_details.booking_timeslot,
                booking_details.booking_remarks,
                booking_details.request_type,
                booking_unit_details.appliance_brand,
                booking_unit_details.appliance_category,
                booking_unit_details.appliance_capacity,
                booking_details.amount_due,
                booking_details.current_status,
                DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_details.booking_date, '%d-%m-%Y')) AS booking_age,
                services.services AS service_name,
                users.name AS user_name,
                users.phone_number AS user_phone,
                service_centres.name AS sc_name,
                service_centres.id AS sc_id,
                service_centres.primary_contact_name AS sc_contact,
                service_centres.primary_contact_phone_1 AS sc_phone,
                employee.full_name as RM
                FROM (booking_details)
                JOIN  `users` ON  `users`.`user_id` =  `booking_details`.`user_id`
                JOIN  `services` ON  `services`.`id` =  `booking_details`.`service_id`
                JOIN employee_relation ON FIND_IN_SET(booking_details.assigned_vendor_id,employee_relation.service_centres_id)
                JOIN employee ON employee.id = employee_relation.agent_id
                LEFT JOIN  `service_centres` ON  `booking_details`.`assigned_vendor_id` = `service_centres`.`id`
                LEFT JOIN  `booking_unit_details` ON  `booking_unit_details`.`booking_id` = `booking_details`.`booking_id`
                WHERE " . $where . "
        ORDER BY booking_age DESC"
        );

        //$result = (bool) ($this->db->affected_rows() > 0);

        return $query->result_array();
    }

    //Get pending bookings to generate summary mail for service center
    function get_pending_bookings_by_sc($id) {
        $query = $this->db->query("SELECT booking_details.booking_id,
                booking_details.booking_address,
                booking_details.booking_pincode,
                booking_details.booking_date,
                booking_details.booking_timeslot,
                booking_details.booking_remarks,
                booking_details.appliance_brand,
                booking_details.appliance_category,
                booking_details.appliance_capacity,
                booking_details.items_selected,
                booking_details.amount_due,
                booking_details.current_status,
                DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_details.booking_date, '%d-%m-%Y')) AS booking_age,
                services.services AS service_name,
                users.name AS user_name,
                users.phone_number AS user_phone
                FROM (booking_details)
                JOIN  `users` ON  `users`.`user_id` =  `booking_details`.`user_id`
                JOIN  `services` ON  `services`.`id` =  `booking_details`.`service_id`
                LEFT JOIN  `service_centres` ON `booking_details`.`assigned_vendor_id` = `service_centres`.`id`
                WHERE booking_details.assigned_vendor_id = '$id' AND
                (DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_details.booking_date, '%d-%m-%Y')) >= 0) AND
                booking_details.current_status IN ('Pending', 'Rescheduled')"
        );

        //$result = (bool) ($this->db->affected_rows() > 0);

        return $query->result_array();
    }

    //Get num of pending bookings for each vendor
    //Ignore queries and bookings having age < 3 days
    function get_num_pending_bookings_for_all_sc($sf_list = "") {
        if ($sf_list != "") {
            $where = "booking_details.current_status IN ('Pending', 'Rescheduled') "
                    . "AND service_centres.id  
                                IN ("
                    . $sf_list .
                    ")
                AND
        DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_details.booking_date, '%d-%m-%Y')) > 2 ";
        } else {
            $where = "booking_details.current_status IN ('Pending', 'Rescheduled') "
                    . "AND  DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_details.booking_date, '%d-%m-%Y')) > 2 ";
        }
        $query = $this->db->query("SELECT service_centres.name AS sc_name, "
                . "COUNT(booking_details.booking_id) AS num_bookings "
                . "FROM `booking_details` LEFT JOIN service_centres "
                . "ON booking_details.assigned_vendor_id = service_centres.id "
                . "WHERE " . $where . ""
                . "GROUP BY sc_name ORDER BY num_bookings DESC");

        return $query->result_array();
    }

    //Get today's bookings for which job cards need to be generated
    function get_todays_bookings_for_job_cards($date = "") {
        if ($date == "") {
            $query = $this->db->query(
                    "SELECT booking_details.booking_id FROM booking_details
                    WHERE `current_status` IN ('Pending', 'Rescheduled', 'FollowUp') AND
                    DATEDIFF (CURRENT_TIMESTAMP,
                        STR_TO_DATE (booking_details.booking_date, '%d-%m-%Y')) = 0"
            );
        } else {
            $query = $this->db->query(
                    "SELECT booking_details.booking_id
                FROM booking_details
                WHERE `current_status` IN ('Pending', 'Rescheduled', 'FollowUp') AND
                DATEDIFF( STR_TO_DATE(  '" . $date .
                    "',  '%d-%m-%Y' ) , STR_TO_DATE( booking_details.booking_date,  '%d-%m-%Y' ) ) = 0"
            );
        }

        //$result = (bool) ($this->db->affected_rows() > 0);

        return $query->result_array();
    }

    //Get booking details
    function get_booking_details($booking_id) {
        $query = $this->db->query(
                "SELECT booking_details.id,
                        booking_details.booking_id,
                        booking_details.booking_primary_contact_no,
                        booking_details.booking_alternate_contact_no,
                        booking_details.booking_address,
                        booking_details.booking_pincode,
                        booking_details.booking_date,
                        booking_details.booking_timeslot,
                        booking_details.booking_remarks,
                        booking_details.current_status,
                        booking_details.discount_amount AS discount,
                        booking_details.amount_due AS amount,
                        services.services AS service_name,
                        users.name AS user_name,
                        users.user_email AS user_email
                        FROM (booking_details, services, users)
        where booking_details.booking_id = '" . $booking_id . "' AND
        services.id = booking_details.service_id AND
        users.user_id = booking_details.user_id"
        );

        //$result = (bool) ($this->db->affected_rows() > 0);
        //log_message('info', __METHOD__ . " => SQL: " . $this->db->last_query() . ", Result: " . $result);
        //log_message('info', print_r($query->result_array(), TRUE));

        return $query->result_array();
    }

    //Get Units details for particular booking
    function get_unit_details($booking_id) {
        //log_message('info', __METHOD__);

        $query = $this->db->query(
                "SELECT u.booking_id, u.appliance_brand, u.appliance_category,
                u.appliance_capacity, u.model_number, u.price_tags, u.total_price
                FROM booking_unit_details AS u
                WHERE u.booking_id = '" . $booking_id . "'"
        );

        //$result = (bool) ($this->db->affected_rows() > 0);
        //log_message('info', __METHOD__ . " => SQL: " . $this->db->last_query() . ", Result: " . $result);
        //log_message('info', print_r($query->result_array(), TRUE));

        return $query->result_array();
    }

    //Get all Units details for Pending bookings
    function get_all_unit_details() {
        $query = $this->db->query(
                "SELECT u.booking_id, u.appliance_brand, u.appliance_category,
                u.appliance_capacity, u.price_tags, u.total_price
                FROM booking_unit_details AS u, booking_details AS b
                WHERE b.current_status IN ('Pending', 'Rescheduled', 'FollowUp')
                AND u.booking_id = b.booking_id"
        );

        //$result = (bool) ($this->db->affected_rows() > 0);

        return $query->result_array();
    }

    function update_booking_jobcard($id, $output_file_pdf) {
        $sql = "UPDATE booking_details SET booking_jobcard_filename='$output_file_pdf' "
                . "WHERE id='$id';";
        $this->db->query($sql);

        log_message('info', __FUNCTION__ . " SQL: " . $this->db->last_query());

        //echo $this->db->last_query();
    }

    function find_service_centers() {
        $query = $this->db->query("Select * from service_centres where active = 1");
        return $query->result_array();
    }

    function find_all_service_centers() {
        $query = $this->db->get("service_centres");
        return $query->result_array();
    }

    function installation_request_leads($partner_id, $date = "") {
        //Count y'day leads
        print_r($date);
        if ($date != "")
            $this->db->where('ReferenceDate', $date);
        $this->db->where("PartnerID", $partner_id);
        return $this->db->count_all_results('partner_leads');
    }

    function scheduled_installation($partner_id, $date = "") {
        //Count y'day installations scheduled
        $this->db->where("PartnerID", $partner_id);
        $this->db->where_in('247aroundBookingStatus', array('Completed', 'Pending', 'Rescheduled'));
        if ($date != "")
            $this->db->like('ReferenceDate', $date);
        return $this->db->count_all_results('partner_leads');
    }

    function installation_completed($partner_id, $date = "") {
        //Count y'day installations completed
        $this->db->where("PartnerID", $partner_id);
        $this->db->where_in('247aroundBookingStatus', 'Completed');
        if ($date != "")
            $this->db->where('ReferenceDate', $date);
        return $this->db->count_all_results('partner_leads');
    }

    function phone_unreachable($partner_id, $date = "") {
        if ($date != "")
            $this->db->where('ReferenceDate', $date);
        $this->db->where("PartnerID", $partner_id);
        $this->db->where_in('247aroundBookingStatus', 'FollowUp');
        return $this->db->count_all_results('partner_leads');
    }

    function already_installed($partner_id, $date = "") {
        //Count total - Already Installed
        if ($date != "")
            $this->db->where('ReferenceDate', $date);
        $this->db->where("PartnerID", $partner_id);
        $this->db->where_in('247aroundBookingStatus', 'Already Installed');
        return $this->db->count_all_results('partner_leads');
    }

    function cancelled_installation($partner_id, $date = "") {
        //Count total installations Cancelled
        $this->db->where("PartnerID", $partner_id);
        if ($date != "")
            $this->db->where('ReferenceDate', $date);
        $this->db->where_in('247aroundBookingStatus', 'Cancelled');
        return $this->db->count_all_results('partner_leads');
    }

    function pending_installation($partner_id, $date = "") {
        //Count total - Already Installed
        if ($date != "")
            $this->db->where('ReferenceDate', $date);
        $this->db->where("PartnerID", $partner_id);
        $this->db->where_in('247aroundBookingStatus', array('Pending', 'Rescheduled'));
        return $this->db->count_all_results('partner_leads');
    }

    function get_snapdeal_summary_params() {
        //Count all leads
        $total_install_req = $this->db->count_all_results('snapdeal_leads');

        //Count today leads
        $today = date("d") . "/" . date("m");
        $this->db->like('Referred_Date_and_Time', $today);
        $today_install_req = $this->db->count_all_results('snapdeal_leads');

        //Count y'day leads
        $yday = date("d", strtotime("-1 days")) . "/" . date("m", strtotime("-1 days"));
        $this->db->like('Referred_Date_and_Time', $yday);
        $yday_install_req = $this->db->count_all_results('snapdeal_leads');

        //Count total installations scheduled
        $this->db->where_in('Status_by_247around', array('Completed', 'Pending', 'Rescheduled'));
        $total_install_sched = $this->db->count_all_results('snapdeal_leads');

        //Count today installations scheduled
        $this->db->where_in('Status_by_247around', array('Completed', 'Pending', 'Rescheduled'));
        $this->db->like('Referred_Date_and_Time', $today);
        $today_install_sched = $this->db->count_all_results('snapdeal_leads');

        //Count y'day installations scheduled
        $this->db->where_in('Status_by_247around', array('Completed', 'Pending', 'Rescheduled'));
        $this->db->like('Referred_Date_and_Time', $yday);
        $yday_install_sched = $this->db->count_all_results('snapdeal_leads');

        //Count total installations completed
        $this->db->where_in('Status_by_247around', array('Completed'));
        $total_install_compl = $this->db->count_all_results('snapdeal_leads');

        //Count today installations completed
        $this->db->where_in('Status_by_247around', array('Completed'));
        $this->db->like('Referred_Date_and_Time', $today);
        $today_install_compl = $this->db->count_all_results('snapdeal_leads');

        //Count y'day installations completed
        $this->db->where_in('Status_by_247around', array('Completed'));
        $this->db->like('Referred_Date_and_Time', $yday);
        $yday_install_compl = $this->db->count_all_results('snapdeal_leads');

        //Count total installations pending
        $total_install_pend = $total_install_sched - $total_install_compl;
        //Count today installations pending
        $today_install_pend = $today_install_sched - $today_install_compl;
        //Count y'day installations pending
        $yday_install_pend = $yday_install_sched - $yday_install_compl;

        //Count phone not reachable and to be followed up - Total
        $this->db->where_in('Status_by_247around', array('FollowUp'));
        $this->db->where_in('Remarks_by_247around', array('Customer Not Reachable'));
        $total_ph_unreach = $this->db->count_all_results('snapdeal_leads');

        //Count phone not reachable and to be followed up - Today
        $this->db->where_in('Status_by_247around', array('FollowUp'));
        $this->db->where_in('Remarks_by_247around', array('Customer Not Reachable'));
        $this->db->like('Referred_Date_and_Time', $today);
        $today_ph_unreach = $this->db->count_all_results('snapdeal_leads');

        //Count phone not reachable and to be followed up - Y'day
        $this->db->where_in('Status_by_247around', array('FollowUp'));
        $this->db->where_in('Remarks_by_247around', array('Customer Not Reachable'));
        $this->db->like('Referred_Date_and_Time', $yday);
        $yday_ph_unreach = $this->db->count_all_results('snapdeal_leads');

        //Count total installations Cancelled
        $this->db->where_in('Status_by_247around', array('Cancelled'));
        $total_install_cancl = $this->db->count_all_results('snapdeal_leads');

        //Count today installations Cancelled
        $this->db->where_in('Status_by_247around', array('Cancelled'));
        $this->db->like('Referred_Date_and_Time', $today);
        $today_install_cancl = $this->db->count_all_results('snapdeal_leads');

        //Count y'day installations Cancelled
        $this->db->where_in('Status_by_247around', array('Cancelled'));
        $this->db->like('Referred_Date_and_Time', $yday);
        $yday_install_cancl = $this->db->count_all_results('snapdeal_leads');

        //Count total - Already Installed
        $this->db->where_in('Remarks_by_247around', array('Already Installed'));
        $total_already_inst = $this->db->count_all_results('snapdeal_leads');

        //Count today - Already Installed
        $this->db->where_in('Remarks_by_247around', array('Already Installed'));
        $this->db->like('Referred_Date_and_Time', $today);
        $today_already_inst = $this->db->count_all_results('snapdeal_leads');

        //Count y'day - Already Installed
        $this->db->where_in('Remarks_by_247around', array('Already Installed'));
        $this->db->like('Referred_Date_and_Time', $yday);
        $yday_already_inst = $this->db->count_all_results('snapdeal_leads');

        //Count - Cancelled - Other reasons
        $total_cancel_other = $total_install_cancl - $total_already_inst;
        $today_cancel_other = $today_install_cancl - $today_already_inst;
        $yday_cancel_other = $yday_install_cancl - $yday_already_inst;

        //TAT calculation
        $tat = "100";
        //SELECT DATEDIFF(`closed_date`, STR_TO_DATE(`booking_date`,"%d-%m-%Y")) FROM `booking_details` where source='SS' AND current_status='Completed'
        //Average Rating
        $this->db->where('Rating_Stars !=', '');
        $this->db->select_avg('Rating_Stars');
        $query = $this->db->get('snapdeal_leads');
        $avg_rating = $query->result_array()[0]['Rating_Stars'];

        $result = array(
            "total_install_req" => $total_install_req,
            "today_install_req" => $today_install_req,
            "yday_install_req" => $yday_install_req,
            "total_install_sched" => $total_install_sched,
            "today_install_sched" => $today_install_sched,
            "yday_install_sched" => $yday_install_sched,
            "total_install_compl" => $total_install_compl,
            "today_install_compl" => $today_install_compl,
            "yday_install_compl" => $yday_install_compl,
            "total_install_pend" => $total_install_pend,
            "today_install_pend" => $today_install_pend,
            "yday_install_pend" => $yday_install_pend,
            "total_ph_unreach" => $total_ph_unreach,
            "today_ph_unreach" => $today_ph_unreach,
            "yday_ph_unreach" => $yday_ph_unreach,
            "total_install_cancl" => $total_install_cancl,
            "today_install_cancl" => $today_install_cancl,
            "yday_install_cancl" => $yday_install_cancl,
            "total_already_inst" => $total_already_inst,
            "today_already_inst" => $today_already_inst,
            "yday_already_inst" => $yday_already_inst,
            "total_cancel_other" => $total_cancel_other,
            "today_cancel_other" => $today_cancel_other,
            "yday_cancel_other" => $yday_cancel_other,
            "tat" => $tat,
            "avg_rating" => round(floatval($avg_rating), 1)
        );

        return $result;
    }

    function get_snapdeal_summary_params_new() {
        //Count all Snapdeal leads
        $this->db->like('source', 'SS');
        $total_install_req = $this->db->count_all_results('booking_details');

        //Count today leads which has create_date as today
        $this->db->where('source', 'SS');
        $this->db->where('create_date >= ', date('Y-m-d'));
        $today_install_req = $this->db->count_all_results('booking_details');

        //Count y'day leads
        $this->db->where('source', 'SS');
        $this->db->where('create_date >= ', date('Y-m-d', strtotime("-1 days")));
        $this->db->where('create_date < ', date('Y-m-d'));
        $yday_install_req = $this->db->count_all_results('booking_details');

        //Count total installations scheduled
        $this->db->where('source', 'SS');
        $this->db->where_in('current_status', array('Pending', 'Rescheduled'));
        $total_install_sched = $this->db->count_all_results('booking_details');

        //Count today installations scheduled
        $this->db->like('booking_id', 'SS');
        $this->db->where('new_state', 'Pending');
        $this->db->where('create_date >= ', date('Y-m-d'));
        $today_install_sched = $this->db->count_all_results('booking_state_change');

        //Count y'day installations scheduled
        $this->db->like('booking_id', 'SS');
        $this->db->where('new_state', 'Pending');
        $this->db->where('create_date >= ', date('Y-m-d', strtotime("-1 days")));
        $this->db->where('create_date < ', date('Y-m-d'));
        $yday_install_sched = $this->db->count_all_results('booking_state_change');

        //Count total installations completed
        $this->db->where('source', 'SS');
        $this->db->where_in('current_status', array('Completed'));
        $total_install_compl = $this->db->count_all_results('booking_details');

        //Count today installations completed
        $this->db->where('source', 'SS');
        $this->db->where_in('current_status', array('Completed'));
        $this->db->where('closed_date >= ', date('Y-m-d'));
        $today_install_compl = $this->db->count_all_results('booking_details');

        //Count y'day installations completed
        $this->db->where('source', 'SS');
        $this->db->where_in('current_status', array('Completed'));
        $this->db->where('closed_date >= ', date('Y-m-d', strtotime("-1 days")));
        $this->db->where('closed_date < ', date('Y-m-d'));
        $yday_install_compl = $this->db->count_all_results('booking_details');

        //Count total follow-ups pending
        $this->db->where('source', 'SS');
        $this->db->where('current_status', 'FollowUp');
        $total_followup_pend = $this->db->count_all_results('booking_details');

        //Count today follow-ups pending
        $today = date("d-m-Y");
        $where_today = "`source` LIKE '%SS%' AND `current_status`='FollowUp' AND (`booking_date`='' OR `booking_date`=$today)";
        $this->db->where($where_today);
        $today_followup_pend = $this->db->count_all_results('booking_details');

        //Count yday follow-ups pending
        $yday = date("d-m-Y", strtotime("-1 days"));
        $where_yday = "`source` LIKE '%SS%' AND `current_status`='FollowUp' AND `booking_date`=$yday";
        $this->db->where($where_yday);
        $yday_followup_pend = $this->db->count_all_results('booking_details');

        //Count total installations Cancelled
        $this->db->where('source', 'SS');
        $this->db->where('current_status', 'Cancelled');
        $total_install_cancl = $this->db->count_all_results('booking_details');

        //Count today installations Cancelled
        $this->db->like('booking_id', 'SS');
        $this->db->where('new_state', 'Cancelled');
        $this->db->where('create_date >= ', date('Y-m-d'));
        $today_install_cancl = $this->db->count_all_results('booking_state_change');

        //Count y'day installations Cancelled
        $this->db->like('booking_id', 'SS');
        $this->db->where('new_state', 'Cancelled');
        $this->db->where('create_date >= ', date('Y-m-d', strtotime("-1 days")));
        $this->db->where('create_date < ', date('Y-m-d'));
        $yday_install_cancl = $this->db->count_all_results('booking_state_change');

        //TAT calculation
        $tat = "100";
        //SELECT DATEDIFF(`closed_date`, STR_TO_DATE(`booking_date`,"%d-%m-%Y")) FROM `booking_details` where source='SS' AND current_status='Completed'
        //Average Rating
//  $this->db->where('Rating_Stars !=', '');
//  $this->db->select_avg('Rating_Stars');
//  $query = $this->db->get('snapdeal_leads');
//  $avg_rating = $query->result_array()[0]['Rating_Stars'];

        $result = array(
            "total_install_req" => $total_install_req,
            "today_install_req" => $today_install_req,
            "yday_install_req" => $yday_install_req,
            "total_install_sched" => $total_install_sched,
            "today_install_sched" => $today_install_sched,
            "yday_install_sched" => $yday_install_sched,
            "total_install_compl" => $total_install_compl,
            "today_install_compl" => $today_install_compl,
            "yday_install_compl" => $yday_install_compl,
            "total_followup_pend" => $total_followup_pend,
            "today_followup_pend" => $today_followup_pend,
            "yday_followup_pend" => $yday_followup_pend,
            "total_install_cancl" => $total_install_cancl,
            "today_install_cancl" => $today_install_cancl,
            "yday_install_cancl" => $yday_install_cancl,
            "tat" => $tat,
        );

        return $result;
    }

    function get_all_sd_leads() {
//  $query = $this->db->query("SELECT * FROM snapdeal_leads");
        $query = $this->db->query("SELECT BD.booking_id, order_id, booking_date, booking_timeslot,
            BD.current_status, BD.internal_status, rating_stars,
            DATE_FORMAT(BD.create_date, '%d/%M') as create_date,
            services,
            UD.appliance_brand, UD.appliance_description,
            name, phone_number, home_address, pincode, users.city
            FROM booking_details as BD, users, services, booking_unit_details as UD
            WHERE BD.booking_id = UD.booking_id AND
            BD.service_id = services.id AND
            BD.user_id = users.user_id AND
            BD.source = 'SS' AND
            BD.create_date > (CURDATE() - INTERVAL 1 MONTH)");

        //$result = (bool) ($this->db->affected_rows() > 0);

        return $query->result_array();
    }

    function get_all_partner_leads($partner_id) {
        $this->db->select('*');
        $this->db->where("PartnerID", $partner_id);
        $query = $this->db->get("partner_leads");
        return $query->result_array();
    }

    /*
     * Get completed bookings to generate CASH invoice for service center.
     * These are the bookings for which vendor has collected money on 247around behalf.
     * These could be partner bookings where partner doesn't pay us or general
     * repair bookings which 247around gets from non-partner channels.
     * Either partner id should be NULL or for SD as partner, appliance should
     * be AC or Chimney.
     * Later on, this logic should come naturally to this query and should not
     * be hard-coded.
     * Partner ID = 1 (Snapdeal)
     * Service ID = 44 (Chimney) & 50 (AC)
     */

    function get_completed_bookings_by_sc($id, $s_date, $e_date) {
        $query = $this->db->query("
        SELECT booking_details.booking_id,
        services.services AS service_name,
        booking_details.booking_date,
        Date_Format(booking_details.closed_date,'%d-%m-%Y') AS closed_date,
        booking_details.service_charge,
        booking_details.additional_service_charge,
        booking_details.parts_cost,
        booking_details.amount_paid,
        booking_details.rating_stars AS rating
        FROM booking_details, services
        WHERE booking_details.current_status =  'Completed'
        AND booking_details.closed_date >=  '$s_date'
        AND booking_details.closed_date <=  '$e_date'
        AND
        ((booking_details.partner_id IS NULL) OR
        (booking_details.partner_id = 1 AND booking_details.service_id IN (44,50)) OR
        (booking_details.partner_id = 1 AND booking_details.service_id NOT IN (44,50) AND booking_details.parts_cost!='0'))
        AND booking_details.service_id = services.id
        AND booking_details.assigned_vendor_id = $id");

        //$result = (bool) ($this->db->affected_rows() > 0);

        return $query->result_array();
    }

    /*
     * Get completed Website (SW) bookings to generate CASH invoice for service center.
     * These are the bookings for which vendor has collected money on 247around behalf.
     *
     * This is a temp routine to generate SW invoices which got missed for May
     * 2016, Not to be used typically.
     */

    function get_sw_completed_bookings_by_sc($id, $s_date, $e_date) {
        $query = $this->db->query("
        SELECT booking_details.booking_id,
        services.services AS service_name,
        booking_details.booking_date,
        Date_Format(booking_details.closed_date,'%d-%m-%Y') AS closed_date,
        booking_details.service_charge,
        booking_details.additional_service_charge,
        booking_details.parts_cost,
        booking_details.amount_paid,
        booking_details.rating_stars AS rating
        FROM booking_details, services
        WHERE booking_details.current_status =  'Completed'
        AND booking_details.closed_date >=  '$s_date'
        AND booking_details.closed_date <=  '$e_date'
        AND booking_details.partner_id LIKE 247001
        AND booking_details.service_id = services.id
        AND booking_details.assigned_vendor_id = $id");

        //$result = (bool) ($this->db->affected_rows() > 0);

        return $query->result_array();
    }

    /*
     * Get completed Snapdeal bookings to generate invoice for service center.
     * These are the bookings for which vendor has NOT collected money and done the
     * job free of cost.
     */
    /*
      function get_completed_sd_bookings_by_sc($id, $s_date, $e_date) {
      $query = $this->db->query("
      SELECT SD.`CRM_Remarks_SR_No` as booking_id, SD.`Product` as service_name,
      SD.`Scheduled_Appointment_DateDDMMYYYY` as booking_date, BD.`closed_date`,
      SD.`Total` as total_ic, SD.`Rating_Stars` as rating
      FROM `snapdeal_leads` SD, `booking_details` BD
      WHERE `Status_by_247around`='Completed'
      AND SD.`CRM_Remarks_SR_No`=BD.`booking_id`
      AND SD.`Product` NOT IN ('Air Conditioner', 'Chimney')
      AND BD.`closed_date` >=  '$s_date'
      AND BD.`closed_date` <  '$e_date'
      AND BD.`assigned_vendor_id`=$id");

      return $query->result_array();
      }
     *
     */

    /*
     * Get completed Partner (SS & SP) bookings to generate invoice for service center.
     * These are the bookings for which vendor has NOT collected money and done the
     * job free of cost.
     */

    function get_completed_partner_bookings_by_sc($id) {
        /*
         *
         * SELECT partner_leads_for_foc_invoicing.*, booking_details.assigned_vendor_id
          FROM partner_leads_for_foc_invoicing, booking_details
          WHERE partner_leads_for_foc_invoicing.booking_id = booking_details.booking_id
          AND booking_details.assigned_vendor_id = $id
         */

        $query = $this->db->query("
        SELECT partner_leads_for_foc_invoicing_may.*, booking_details.assigned_vendor_id
      FROM partner_leads_for_foc_invoicing_may, booking_details
      WHERE partner_leads_for_foc_invoicing_may.booking_id = booking_details.booking_id
      AND booking_details.assigned_vendor_id = $id");

        return $query->result_array();
    }

    function get_email_for_partner($partner_id) {
        $this->db->select('partner_email_for_to, partner_email_for_cc');
        $this->db->where('partner_id', $partner_id);
        $query = $this->db->get('bookings_sources');
        return $query->result_array();
    }

    function booking_report() {
        for ($i = 1; $i < 3; $i++) {
            $where = "";

            if ($i == 2) {
                $where = " where create_date >= CURDATE() AND create_date < CURDATE() + INTERVAL 1 DAY ";
            }

            $sql = "SELECT source,
                 SUM(CASE WHEN `current_status` LIKE '%FollowUp%' THEN 1 ELSE 0 END) AS queries,
                 SUM(CASE WHEN `current_status` LIKE '%Cancelled%' THEN 1 ELSE 0 END) AS cancelled,
                 SUM(CASE WHEN `current_status` LIKE '%Completed%' THEN 1 ELSE 0 END) AS completed,
                 SUM(CASE WHEN `current_status` LIKE '%Pending%' OR `current_status` LIKE '%Rescheduled%' THEN 1 ELSE 0 END) as scheduled,
                 SUM(CASE WHEN `current_status` LIKE '%FollowUp%' OR `current_status` LIKE '%Completed%' OR `current_status` LIKE '%Cancelled%' OR `current_status` LIKE '%Pending%' OR `current_status` LIKE '%Rescheduled%' THEN 1 ELSE 0 END) AS total
                from booking_details $where Group By source ;
                 ";

            $data = $this->db->query($sql);
            $result['data' . $i] = $data->result_array();
        }

        return $result;
    }

    /**
     * @desc: This method is used to return all partner booking summary details for today,
     * within month and total
     * @return array
     */
    function get_partner_summary_data() {
        $partner = $this->get_partner_to_send_mail();
        $summary_data = array();
        foreach ($partner as $key => $value) {
            for ($i = 0; $i < 3; $i++) {
                $where = "";
                // In this if clause, we set date for today
                if ($i == 0) {
                    $where = " AND create_date >= CURDATE() AND create_date < CURDATE() + INTERVAL 1 DAY ";
                } else if ($i == 1) {
                    $where = " AND create_date >=  DATE_SUB(NOW(), INTERVAL 1 MONTH) ";
                } else if ($i == 2) {
                    $where = "";
                }
                $sql = "SELECT source, partner_source, "
                        . " SUM(CASE WHEN `current_status` LIKE '%FollowUp%' THEN 1 ELSE 0 END) AS queries,"
                        . " SUM(CASE WHEN `current_status` LIKE '%Cancelled%' THEN 1 ELSE 0 END) AS cancelled,"
                        . " SUM(CASE WHEN `current_status` LIKE '%Completed%' THEN 1 ELSE 0 END) AS completed,"
                        . " SUM(CASE WHEN `current_status` LIKE '%Pending%' OR `current_status` LIKE '%Rescheduled%' THEN 1 ELSE 0 END) as scheduled,"
                        . " SUM(CASE WHEN `current_status` LIKE '%FollowUp%' OR `current_status` LIKE '%Completed%' OR `current_status` LIKE '%Cancelled%' OR `current_status` LIKE '%Pending%' OR `current_status` LIKE '%Rescheduled%' THEN 1 ELSE 0 END) AS total_booking "
                        . " From booking_details Where partner_id = '" . $value['partner_id'] . "' $where  Group By partner_source ";
                $data = $this->db->query($sql);
                $partner[$key]['data' . $i] = $data->result_array();
            }
        }
        return $partner;
    }

    /**
     *  @desc: This Method returns all partner details who have email id in the
     *  bookings_sources table
     */
    function get_partner_to_send_mail() {
        $this->db->select('*');
        $this->db->where_not_in('partner_email_for_to', '');
        $query = $this->db->get('bookings_sources');
        return $query->result_array();
    }

    function get_report_data() {
        for ($i = 1; $i < 3; $i++) {
            $where = "where DATE_FORMAT(booking_state_change.create_date,'%y-%m-%d') = CURDATE() ";

            if ($i == 2) {
                $where = " where DATE_FORMAT(booking_state_change.create_date,'%m') = MONTH(CURDATE()) ";
            }

            $sql = "SELECT distinct booking_details.source,booking_details.partner_id,
                 SUM(CASE WHEN `new_state` LIKE '%FollowUp%' THEN 1 ELSE 0 END) AS queries,
                 SUM(CASE WHEN `new_state` LIKE '%Pending%' OR `new_state` LIKE '%Rescheduled%' THEN 1 ELSE 0 END) as scheduled,
                 SUM(CASE WHEN `new_state` LIKE '%FollowUp%' OR `new_state` LIKE '%Pending%' OR `new_state` LIKE '%Rescheduled%' THEN 1 ELSE 0 END) AS total
                    from booking_state_change 
                        JOIN booking_details ON booking_state_change.booking_id = booking_details.booking_id
                 $where GROUP BY booking_details.source ;";

            $data = $this->db->query($sql);
            $result_data = $data->result_array();

            foreach ($result_data as $value) {
                if ($i == 1) {

                    //Today completed Bookings
                    $this->db->where('partner_id', $value['partner_id']);
                    $this->db->where_in('current_status', array(_247AROUND_COMPLETED));
                    $this->db->like('closed_date ', date('Y-m-d'));
                    $today_install_comp = $this->db->count_all_results('booking_details');
                    $result['today_completed'][] = $today_install_comp;

                    //Today cancelled Bookings
                    $this->db->where('partner_id', $value['partner_id']);
                    $this->db->where_in('current_status', array(_247AROUND_CANCELLED));
                    $this->db->like('closed_date ', date('Y-m-d'));
                    $today_install_cancelled = $this->db->count_all_results('booking_details');
                    $result['today_cancelled'][] = $today_install_cancelled;
                } else {

                    //Count this month installations Completed
                    $sql = "SELECT * FROM booking_details WHERE partner_id = '" . $value['partner_id'] . "'"
                            . " AND month(closed_date) = month(CURRENT_DATE) "
                            . "AND current_status = '" . _247AROUND_COMPLETED . "'";
                    $can_query = $this->db->query($sql);
                    $month_install_can = $can_query->result_array();
                    $month_install_comp = count($month_install_can);
                    $result['month_completed'][] = $month_install_comp;

                    //Count this month installations Cancelled
                    $sql = "SELECT * FROM booking_details WHERE partner_id = '" . $value['partner_id'] . "'"
                            . " AND month(closed_date) = month(CURRENT_DATE) "
                            . "AND current_status = '" . _247AROUND_CANCELLED . "'";
                    $can_query1 = $this->db->query($sql);
                    $month_can = $can_query1->result_array();
                    $month_cancl = count($month_can);
                    $result['month_cancelled'][] = $month_cancl;
                }
            }
            $result['data' . $i] = $result_data;
        }
        return $result;
    }

    /**
     * @desc: This function is used to get data of Agents doing current date's booking 
     *        from 247Around CRM. It is used to send details in daily_report mail.
     * params:void
     * return: array
     */
    function get_247_agent_report_data() {
        $where = "where DATE_FORMAT(booking_state_change.create_date,'%y-%m-%d') = CURDATE() AND partner_id = " . _247AROUND;
        $sql = "SELECT booking_state_change.agent_id,employee.employee_id,
                 SUM(CASE WHEN `new_state` LIKE '%FollowUp%' THEN 1 ELSE 0 END) AS queries,
                 SUM(CASE WHEN `new_state` LIKE '%Cancelled%' AND `booking_id` LIKE 'Q-%' THEN 1 ELSE 0 END) AS cancelled_query,
                 SUM(CASE WHEN `new_state` LIKE '%Cancelled%' AND `booking_id` LIKE 'S%' THEN 1 ELSE 0 END) AS cancelled_booking,
                 SUM(CASE WHEN `new_state` LIKE '%Completed%' THEN 1 ELSE 0 END) AS completed,
                 SUM(CASE WHEN `new_state` LIKE '%Pending%'  THEN 1 ELSE 0 END) as scheduled,
                 SUM(CASE WHEN `new_state` LIKE '%Rescheduled%'  THEN 1 ELSE 0 END) as rescheduled,
                 SUM(CASE WHEN `new_state` LIKE '%FollowUp%' OR `new_state` LIKE '%Completed%' OR `new_state` LIKE '%Cancelled%' OR `new_state` LIKE '%Pending%' OR `new_state` LIKE '%Rescheduled%' THEN 1 ELSE 0 END) AS total
                 from booking_state_change 
                 JOIN employee ON booking_state_change.agent_id = employee.id
                 $where GROUP BY booking_state_change.agent_id ;";

        $data = $this->db->query($sql);
        return $data->result_array();
    }
    function get_pending_booking_by_service_center_query_data($where,$groupBY,$actor=NULL){
        $actorWhere = '';
        if($actor){
            $actorWhere = "AND booking_details.actor='".$actor."' ";
        }
        $queries['sql_last_2_day'] = "SELECT GROUP_CONCAT(booking_details.booking_id) as booking_id_list,GROUP_CONCAT(booking_details.partner_internal_status) as partner_internal_status,
            GROUP_CONCAT(booking_details.booking_remarks) as booking_remarks,service_centres.state, service_centres.district as city, service_centres.id AS service_center_id, 
            service_centres.name AS service_center_name, COUNT(booking_id ) AS booked , service_centres.active as active, service_centres.on_off as temporary_on_off  
                            FROM booking_details
                            JOIN service_centres ON service_centres.id = booking_details.assigned_vendor_id
                            WHERE 
                            DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_details.booking_date, '%d-%m-%Y')) >= 0
                            AND 
                            DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_details.booking_date, '%d-%m-%Y')) <= 2
                            " . $where . "
                            AND booking_details.service_center_closed_date is null AND current_status
                            IN (
                            'Pending', 'Rescheduled'
                            ) ".$actorWhere.$groupBY;

       $queries['sql_last_3_day'] = "SELECT GROUP_CONCAT(booking_details.booking_id) as booking_id_list,GROUP_CONCAT(booking_details.partner_internal_status) as partner_internal_status,
            GROUP_CONCAT(booking_details.booking_remarks) as booking_remarks,service_centres.state, service_centres.district as city, service_centres.id AS service_center_id, service_centres.name 
            AS service_center_name, COUNT(booking_id ) AS booked , service_centres.active as active, service_centres.on_off as temporary_on_off 
                            FROM booking_details
                            JOIN service_centres ON service_centres.id = booking_details.assigned_vendor_id
                            WHERE 
                            DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_details.booking_date, '%d-%m-%Y')) >= 3
                            AND 
                            DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_details.booking_date, '%d-%m-%Y')) <= 5
                            " . $where . "
                            AND booking_details.service_center_closed_date is null AND current_status
                            IN (
                            'Pending', 'Rescheduled'
                            ) ".$actorWhere.$groupBY;

        $queries['sql_greater_than_5_days'] = "SELECT GROUP_CONCAT(booking_details.booking_id) as booking_id_list,GROUP_CONCAT(booking_details.partner_internal_status) as partner_internal_status,
            GROUP_CONCAT(booking_details.booking_remarks) as booking_remarks,service_centres.state, service_centres.district as city, service_centres.id AS service_center_id, service_centres.name 
            AS service_center_name, COUNT(booking_id ) AS booked , service_centres.active as active, service_centres.on_off as temporary_on_off 
                            FROM booking_details
                            JOIN service_centres ON service_centres.id = booking_details.assigned_vendor_id
                            WHERE 
                DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_details.booking_date, '%d-%m-%Y')) > 5
                            " . $where . "
                            AND booking_details.service_center_closed_date is null AND current_status
                            IN (
                            'Pending', 'Rescheduled'
                            ) ".$actorWhere.$groupBY;
        $data_last_2_day = $this->db->query($queries['sql_last_2_day'])->result_array();
        $data_last_3_day = $this->db->query($queries['sql_last_3_day'])->result_array();
        $data_greater_than_5_days = $this->db->query($queries['sql_greater_than_5_days'])->result_array();
        return array('data_last_2_day'=>$data_last_2_day,'data_last_3_day'=>$data_last_3_day,'data_greater_than_5_days'=>$data_greater_than_5_days);
    }
function get_booking_by_service_center_query_data($where,$groupBY, $interval_in_days = 1, $sf_closed_date = NULL){
   
        if(empty($sf_closed_date)) {
            $sf_closed_date = date('Y-m-d'). ' - '. date('Y-m-d');
        }
        
        $date = explode(' - ', $sf_closed_date);
        $startDate = $date[0];
        $endDate = $date[1];
        
        $queries['sql_yesterday_booked'] = "SELECT count(distinct(`booking_details`.booking_id)) as booked, service_centres.name as service_center_name, service_centres.state, 
            service_centres.district as city ,service_centres.id as service_center_id , service_centres.active as active, service_centres.on_off as temporary_on_off   
                                FROM   `booking_details` , service_centres
                                WHERE booking_details.create_date BETWEEN '{$startDate}' AND '{$endDate}' " . $where . "
                                AND `service_centres`.id = `booking_details`.assigned_vendor_id ".$groupBY;

        $queries['sql_yesterday_completed'] = "SELECT COUNT( DISTINCT (
                                    `booking_details`.booking_id
                                    ) ) AS completed, service_centres.name AS service_center_name, service_centres.state, service_centres.district as city ,service_centres.id as service_center_id , service_centres.active as active, service_centres.on_off as temporary_on_off 
                                    FROM `booking_details` , service_centres
                                    WHERE !(current_status = 'Cancelled' OR internal_status ='InProcess_Cancelled') 
                                    " . $where . "
                                    AND booking_details.service_center_closed_date BETWEEN '{$startDate}' AND '{$endDate}'
                                    AND `service_centres`.id = `booking_details`.assigned_vendor_id ".$groupBY;

        $queries['sql_yesterday_cancelled'] = "SELECT COUNT( DISTINCT (
                                    `booking_details`.booking_id
                                    ) ) AS cancelled, service_centres.name AS service_center_name, service_centres.state, service_centres.district as city, service_centres.id as service_center_id , service_centres.active as active, service_centres.on_off as temporary_on_off 
                                    FROM `booking_details` , service_centres
                                    WHERE (current_status = 'Cancelled' OR internal_status ='InProcess_Cancelled') 
                                    " . $where . "
                                    AND booking_details.service_center_closed_date BETWEEN '{$startDate}' AND '{$endDate}'
                                    AND `service_centres`.id = `booking_details`.assigned_vendor_id ".$groupBY;

        $queries['sql_month_completed'] = "SELECT COUNT( DISTINCT (
                                            `booking_details`.booking_id
                                            ) ) AS completed, service_centres.name AS service_center_name, service_centres.state, service_centres.district as city , service_centres.id as service_center_id , service_centres.active as active, service_centres.on_off as temporary_on_off 
                                            FROM `booking_details` , service_centres
                                            WHERE !(current_status = 'Cancelled' OR internal_status ='InProcess_Cancelled') AND booking_details.service_center_closed_date is not null 
                                            " . $where . "
                                            AND DATE_FORMAT( booking_details.service_center_closed_date, '%m' ) = MONTH( CURDATE() ) 
                                            AND DATE_FORMAT( booking_details.service_center_closed_date, '%Y' ) = YEAR( CURDATE() )
                                            AND `service_centres`.id = `booking_details`.assigned_vendor_id ".$groupBY;
        
        $queries['sql_month_cancelled'] = "SELECT COUNT( DISTINCT (
                                            `booking_details`.booking_id
                                            ) ) AS cancelled, service_centres.name AS service_center_name, service_centres.state, service_centres.district as city , service_centres.id as service_center_id , service_centres.active as active, service_centres.on_off as temporary_on_off 
                                            FROM `booking_details` , service_centres
                                            WHERE (current_status = 'Cancelled' OR internal_status ='InProcess_Cancelled') AND booking_details.service_center_closed_date is not null 
                                            " . $where . "
                                            AND DATE_FORMAT( booking_details.service_center_closed_date, '%m' ) = MONTH( CURDATE() ) 
                                            AND DATE_FORMAT( booking_details.service_center_closed_date, '%Y' ) = YEAR( CURDATE() )
                                            AND `service_centres`.id = `booking_details`.assigned_vendor_id ".$groupBY;
        $pendingBookingArray = $this->get_pending_booking_by_service_center_query_data($where,$groupBY);
        $data_yesterday['booked'] = $this->db->query($queries['sql_yesterday_booked'])->result_array();
        $data_yesterday['completed'] = $this->db->query($queries['sql_yesterday_completed'])->result_array();
        $data_yesterday['cancelled'] = $this->db->query($queries['sql_yesterday_cancelled'])->result_array();
        $data_month['completed'] = $this->db->query($queries['sql_month_completed'])->result_array();
        $data_month['cancelled'] = $this->db->query($queries['sql_month_cancelled'])->result_array();
        return array('data_yesterday'=>$data_yesterday,'data_month'=>$data_month,'data_last_2_day'=>$pendingBookingArray['data_last_2_day'],
            'data_last_3_day'=>$pendingBookingArray['data_last_3_day'],'data_greater_than_5_days'=>$pendingBookingArray['data_greater_than_5_days']);
}
    /*
     * @desc: This function is used to get reporting data acc to service center
     * params: String sf_list
     * return: Array
     */

    function get_booking_by_service_center($sf_list = "", $interval_in_days = 1, $sf_closed_date = NULL) {
        if ($sf_list != "") {
            $where = " AND service_centres.id  IN (" . $sf_list . ") ";
        } else {
            $where = "";
        }
        $groupBY = "GROUP BY service_centres.state, service_centres.name";
        $finalArray = $this->get_booking_by_service_center_query_data($where,$groupBY, $interval_in_days, $sf_closed_date);
        //Setting $result array with all values
        $result['yesterday_booked'] = $this->remove_no_state_error($finalArray['data_yesterday']['booked'], 'booked');
        $result['yesterday_completed'] = $this->remove_no_state_error($finalArray['data_yesterday']['completed'], 'completed');
        $result['yesterday_cancelled'] = $this->remove_no_state_error($finalArray['data_yesterday']['cancelled'], 'cancelled');
        $result['month_completed'] = $this->remove_no_state_error($finalArray['data_month']['completed'], 'completed');
        $result['month_cancelled'] = $this->remove_no_state_error($finalArray['data_month']['cancelled'], 'cancelled');
        $result['last_2_day'] = $this->remove_no_state_error($finalArray['data_last_2_day'], 'booked');
        $result['last_3_day'] = $this->remove_no_state_error($finalArray['data_last_3_day'], 'booked');
        $result['greater_than_5_days'] = $this->remove_no_state_error($finalArray['data_greater_than_5_days'], 'booked');
        //Genearting Final Array 
        return $this->make_final_array($result);
    }

    /**
     * @desc: This function is used to make Final Array to Service Center Report Mail
     * params: Array
     * return : Array
     */
    function make_final_array($result) {

        //Getting max length of array from array's
        $max_length = max(sizeof($result['yesterday_booked']), sizeof($result['yesterday_completed']), sizeof($result['yesterday_cancelled']), sizeof($result['month_completed']), sizeof($result['month_cancelled']), sizeof($result['last_3_day']), sizeof($result['greater_than_5_days']));
        //Getting service_center_id array
        $service_center_id = $this->get_service_center_id_array($result, $max_length);
        for ($i = 0; $i < $max_length; $i++) {

            // Setting value in final data array according to service center id
            if (!empty($result['yesterday_booked'][$i])) {
                $data['yesterday_booked'] = $this->search_service_center_id($service_center_id, $result['yesterday_booked'][$i]);
            }
            if (!empty($result['yesterday_completed'][$i])) {
                $data['yesterday_completed'] = $this->search_service_center_id($service_center_id, $result['yesterday_completed'][$i]);
            }
            if (!empty($result['yesterday_cancelled'][$i])) {
                $data['yesterday_cancelled'] = $this->search_service_center_id($service_center_id, $result['yesterday_cancelled'][$i]);
            }
            if (!empty($result['month_completed'][$i])) {
                $data['month_completed'] = $this->search_service_center_id($service_center_id, $result['month_completed'][$i]);
            }
            if (!empty($result['month_cancelled'][$i])) {
                $data['month_cancelled'] = $this->search_service_center_id($service_center_id, $result['month_cancelled'][$i]);
            }
            if (!empty($result['last_2_day'][$i])) {
                $data['last_2_day'] = $this->search_service_center_id($service_center_id, $result['last_2_day'][$i]);
            }
            if (!empty($result['last_3_day'][$i])) {
                $data['last_3_day'] = $this->search_service_center_id($service_center_id, $result['last_3_day'][$i]);
            }
            if (!empty($result['greater_than_5_days'][$i])) {
                $data['greater_than_5_days'] = $this->search_service_center_id($service_center_id, $result['greater_than_5_days'][$i]);
            }

            //Pushing each data value to data_final array
            $data_final[$i] = $data;
        }
        $return_array['data'] = $this->get_final_array($service_center_id, $data_final);
        $return_array['service_center_id'] = $service_center_id;

        return $return_array;
    }

    /**
     * @desc: This fucntion is used to match array values to the set of service_center_id's
     * params: Array, Array
     *         sevice_center_id array, array to be searched
     * return: Array or void 
     */
    private function search_service_center_id($service_center, $data) {
        foreach ($service_center as $value) {
            if (isset($data['service_center_id']) && $data['service_center_id'] == $value) {
                return $data;
            }
        }
    }

    /**
     * @desc: This function is used to get service center id's from following arrays
     * params: Array, INT
     *         array of values and Looping max value limit for searching value
     * return: Array
     * 
     */
    private function get_service_center_id_array($result, $max_length) {
        for ($i = 0; $i < $max_length; $i++) {

            //Setting Service Center ID
            if (isset($result['month_completed'][$i]['service_center_id'])) {
                $service_center_id[] = $result['month_completed'][$i]['service_center_id'];
            }
            if (isset($result['month_cancelled'][$i]['service_center_id'])) {
                $service_center_id[] = $result['month_cancelled'][$i]['service_center_id'];
            }
            if (isset($result['last_3_day'][$i]['service_center_id'])) {
                $service_center_id[] = $result['last_3_day'][$i]['service_center_id'];
            }
            if (isset($result['last_2_day'][$i]['service_center_id'])) {
                $service_center_id[] = $result['last_2_day'][$i]['service_center_id'];
            }
            if (isset($result['yesterday_booked'][$i]['service_center_id'])) {
                $service_center_id[] = $result['yesterday_booked'][$i]['service_center_id'];
            }
            if (isset($result['yesterday_cancelled'][$i]['service_center_id'])) {
                $service_center_id[] = $result['yesterday_cancelled'][$i]['service_center_id'];
            }
            if (isset($result['yesterday_completed'][$i]['service_center_id'])) {
                $service_center_id[] = $result['yesterday_completed'][$i]['service_center_id'];
            }
            if (isset($result['greater_than_5_days'][$i]['service_center_id'])) {
                $service_center_id[] = $result['greater_than_5_days'][$i]['service_center_id'];
            }
        }
        return array_unique($service_center_id);
    }

    /**
     * @desc: Making Final array according to service_center_id
     * parmas: Array, Array
     * return :Array
     * 
     */
    private function get_final_array($service_center_id, $data_final) {
        $final_data = [];
        foreach ($service_center_id as $value) {
            foreach ($data_final as $val) {
                foreach ($val as $k => $v) {
                    if (isset($v['service_center_id']) && $v['service_center_id'] == $value) {
                        $final_data[$value][$k] = $v;
                    }
                }
            }
        }
        return $final_data;
    }

    /**
     * @desc: This function is used to removed err in bookings with No state
     * params: Array, status like completed, cancelled, pending etc
     *  return: Array
     */
    private function remove_no_state_error($data, $status) {
        //For removing No State present err
        $array_final = [];
        $return_array = [];
        //Getting all Vendors enabled and disabled both
        $vendor = $this->vendor_model->getAllVendor();

        foreach ($vendor as $value) {
            $sum = 0;
            $flag = 0;
            foreach ($data as $v) {
                if ($v['service_center_id'] == $value['id']) {
                    $flag = 1;
                    $sum += $v[$status];
                    $array_final[$status] = $sum;
                    $array_final['service_center_name'] = $v['service_center_name'];
                    $array_final['state'] = $v['state'];
                    $array_final['city'] = $v['city'];
                    $array_final['service_center_id'] = $v['service_center_id'];
                    $array_final['active'] = $v['active'];
                    $array_final['temporary_on_off'] = $v['temporary_on_off'];
                }
            }
            if ($flag == 1)
                $return_array[] = $array_final;
        }

        return $return_array;
    }

    /**
     * @Desc: This function is used to get completed bookings of current month
     * @params: void
     * @return: Array
     * 
     */
    function get_completed_month_bookings() {
        $sql = "SELECT `booking_details`.booking_id, booking_details.booking_pincode AS booking_details_pincode
                ,service_centres.name AS service_center_name, booking_details.state AS booking_details_state, booking_details.city AS booking_details_city
                ,service_centres.id as service_center_id , service_centres.district as service_center_district
                ,service_centres.state as service_center_state, service_centres.pincode as service_center_pincode
                FROM `booking_details` , service_centres
                WHERE `partner_id` = '247010'
                AND  `current_status` LIKE  'Completed'
                AND  `closed_date` >=  '2016-09-01 00:00:00'
                AND  `closed_date` <  '2016-11-01 00:00:00'
                AND `service_centres`.id = `booking_details`.assigned_vendor_id;";

        $data_month = $this->db->query($sql)->result_array();
        return $data_month;
    }

    /**
     * @desc: First gets all service center id who has is_update column value 1.
     * Count those bookings who have not assigned(Booking date Past & Today).
     * Count thpose bookings who have not any entry in booking state change, means not updated( except Assigned Engineer)
     * It stores crime when this script excute after 8 PM
     */
    function get_sc_crimes($where, $is_insert = false, $groups = "") {
        log_message('info', __FUNCTION__);

        // Get All Service center who has is_update filed is 1.
        $sql = "SELECT id, name,active, on_off FROM service_centres WHERE "
                . " active = '1' AND is_update = '1' $where ORDER BY name ";
        $query = $this->db->query($sql);
        $sc = $query->result_array();


        $data = array();
        foreach ($sc as $value) {
            $un_assigned = 0;
            $not_update = 0;

            $total_bookings = 0;
            //  Count,  booking is not assigned
//            $sql1 = "SELECT count(booking_id) as unassigned_engineer FROM booking_details as BD "
//                    . " WHERE BD.current_status = 'Pending' AND assigned_engineer_id IS  NULL "
//                    . " AND assigned_vendor_id = '$value[id]' AND "
//                    . " DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(BD.booking_date, '%d-%m-%Y')) >= -1";
//            $query1 = $this->db->query($sql1);
//            $result1 = $query1->result_array();
            //Count, Booking is not updated
            $sql2 = "SELECT count(distinct(BD.booking_id)) as not_update FROM booking_details as BD, 
                      service_center_booking_action as sc 
                      WHERE BD.Current_status IN ('Pending', 'Rescheduled') 
                      AND assigned_vendor_id = '" . $value['id'] . "' 
                      
                      AND sc.current_status = 'Pending' 
                      AND sc.booking_id = BD.booking_id 
                      AND NOT EXISTS (SELECT booking_id FROM booking_state_change WHERE booking_id =BD.booking_id 
                      AND service_center_id = '" . $value['id'] . "' 
                      AND DATEDIFF(CURRENT_TIMESTAMP , create_date) = 1) 
                      AND DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(BD.booking_date, '%d-%m-%Y')) >= 1";
            $query2 = $this->db->query($sql2);
            $result2 = $query2->result_array();

            //Count Total Bookings Present
            $sql3 = "SELECT count(distinct(BD.booking_id)) as total_bookings FROM booking_details as BD,
                      service_center_booking_action AS sb
                      WHERE BD.Current_status IN ('Pending', 'Rescheduled') 
                      AND DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(BD.booking_date, '%d-%m-%Y')) >= 1
                      AND assigned_vendor_id = '" . $value['id'] . "' "
                    . " AND BD.booking_id = sb.booking_id AND sb.current_status = 'Pending' ";

            $query3 = $this->db->query($sql3);
            $result3 = $query3->result_array();

            //Getting Monthly Crimes for each SF
            $sql_total_crimes = "Select SUM(`total_missed_target`) as monthly_total_crimes "
                    . "from sc_crimes WHERE MONTH(`create_date`)= MONTH(CURRENT_DATE) AND"
                    . " service_center_id = " . $value['id'];
            $query4 = $this->db->query($sql_total_crimes);
            $result4 = $query4->result_array();

            //Getting Monthly Escalations for each SF
            $escalations_monthly = "SELECT COUNT(id) as monthly_escalations "
                    . "from vendor_escalation_log "
                    . "WHERE MONTH(`create_date`) = MONTH(CURRENT_DATE) AND"
                    . " vendor_id = " . $value['id'];
            $query5 = $this->db->query($escalations_monthly);
            $result5 = $query5->result_array();

            if (!empty($result2)) {

                $un_assigned = 0;
                $not_update = $result2[0]['not_update'];
                $total_bookings = $result3[0]['total_bookings'];


                $where = array('service_center_id' => $value['id']);
                // Get Old Crimes
                $old_crimes = $this->get_crimes($where);
                if (!empty($old_crimes)) {
                    $data1['old_crimes'] = $old_crimes[0]['total_missed_target'];
                } else {
                    $data1['old_crimes'] = 0;
                }

                $data1['service_center_id'] = $value['id'];
                $data1['service_center_name'] = $value['name'];
                $data1['active'] = $value['active'];
                $data1['on_off'] = $value['on_off'];
                $data1['un_assigned'] = $un_assigned;
                $data1['not_update'] = $not_update;
                $data1['update'] = $total_bookings - $not_update;
                $data1['monthly_total_crimes'] = $result4[0]['monthly_total_crimes'];
                $data1['total_booking'] = $total_bookings;
                $data1['monthly_escalations'] = $result5[0]['monthly_escalations'];

                array_push($data, $data1);
                unset($data1);
            }
        }
        return $data;
    }
    /**
     * @desc this is used to get data to generate sc crime report
     * @param String $where_sf
     * @return array
     */
    function send_sc_crimes_report_mail_data($where_sf){
        // Get All Service center who has is_update filed is 1.
        $sql = "SELECT id, name,active, on_off FROM service_centres WHERE "
                . " active = '1' AND is_update = '1' $where_sf ORDER BY name ";
        $query = $this->db->query($sql);
        $sc = $query->result_array();
        $data = array();
        foreach ($sc as $value) {
            $where = array('service_center_id' => $value['id'], 'create_date >=' =>  date('Y-m-d', strtotime("-1 days")));
           
            $result = $this->get_crimes($where);
             
            //Getting Monthly Crimes for each SF
            $sql_total_crimes = "Select SUM(`total_missed_target`) as monthly_total_crimes "
                    . "from sc_crimes WHERE MONTH(`create_date`)= MONTH(CURRENT_DATE) AND"
                    . " service_center_id = " . $value['id'];
            $query4 = $this->db->query($sql_total_crimes);
            $result4 = $query4->result_array();

            //Getting Monthly Escalations for each SF
            $escalations_monthly = "SELECT COUNT(id) as monthly_escalations "
                    . "from vendor_escalation_log "
                    . "WHERE MONTH(`create_date`) = MONTH(CURRENT_DATE) AND"
                    . " vendor_id = " . $value['id'];
            $query5 = $this->db->query($escalations_monthly);
            $result5 = $query5->result_array();
            
            if(!empty($result)){
                $data1['service_center_id'] = $value['id'];
                $data1['service_center_name'] = $value['name'];
                $data1['active'] = $value['active'];
                $data1['on_off'] = $value['on_off'];
                $data1['un_assigned'] = 0;
                $data1['not_update'] = $result[0]['booking_not_updated'];
                $data1['update'] = $result[0]['total_pending_booking'] - $result[0]['booking_not_updated'];
                $data1['monthly_total_crimes'] = $result4[0]['monthly_total_crimes'];
                $data1['total_booking'] = $result[0]['total_pending_booking'];
                $data1['monthly_escalations'] = $result5[0]['monthly_escalations'];

                array_push($data, $data1);
                unset($data1);
            } 
        }
         return $data;
    }

    /**
     * @desc: store SF missed target
     * @param Array $sc_crimes
     */
    function store_old_sc_crimes($sc_crimes) {

        $this->db->insert('sc_crimes', $sc_crimes);
    }

    /**
     * @desc: This meyhod returns crimes details as array
     * @param Array $where
     * @return Array
     */
    function get_crimes($where) {
        $this->db->where($where);
        $this->db->order_by('id', 'desc');
        $query = $this->db->get('sc_crimes');
       
        return $query->result_array();
    }

    /**
     * @desc:This method returns count of un-assigned today and past booking 
     * @param String $sc_id
     * @return array
     */
    function get_unassigned_crimes() {

        $sql = "SELECT id, name, primary_contact_email, owner_email FROM service_centres WHERE "
                . " active = '1' AND is_update = '1' "
                . " ORDER BY name ";
        $query = $this->db->query($sql);
        $sc = $query->result_array();

        $data = array();
        foreach ($sc as $value) {
            //  Count,  Today booking is not assigned
            $sql1 = "SELECT count(booking_id) as unassigned_engineer FROM booking_details as BD "
                    . " WHERE BD.current_status = 'Pending' AND assigned_engineer_id IS  NULL "
                    . " AND assigned_vendor_id = '$value[id]' AND "
                    . " DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(BD.booking_date, '%d-%m-%Y')) = 0";
            $query1 = $this->db->query($sql1);
            $result1 = $query1->result_array();

            //  Count,  Past booking is not assigned
            $sql2 = "SELECT count(booking_id) as unassigned_engineer FROM booking_details as BD "
                    . " WHERE BD.current_status = 'Pending' AND assigned_engineer_id IS  NULL "
                    . " AND assigned_vendor_id = '$value[id]' AND "
                    . " DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(BD.booking_date, '%d-%m-%Y')) > 0";
            $query2 = $this->db->query($sql2);
            $result2 = $query2->result_array();

            $data1['today_unassigned'] = !empty($result1) ? $result1[0]['unassigned_engineer'] : 0;
            $data1['past_unassigned'] = !empty($result2) ? $result2[0]['unassigned_engineer'] : 0;
            $data1['service_center_name'] = $value['name'];
            $data1['primary_contact_email'] = $value['primary_contact_email'];
            $data1['owner_email'] = $value['owner_email'];
            $data1['sf_id'] = $value['id'];

            array_push($data, $data1);
        }

        return $data;
    }

    /**
     * @Desc: This function is used to get New Vendors Data
     * @params: string sf_list for RM
     * @return : void
     */
    function get_booking_for_new_service_center($sf_list = "") {
        if ($sf_list != "") {
            $where = " AND service_centres.id  IN (" . $sf_list . ") ";
        } else {
            $where = "";
        }
        $yesterday_bookings_gone = "SELECT count(distinct(`booking_state_change`.booking_id)) as booked, service_centres.id as service_center_id 
                                FROM  `booking_state_change`, `booking_details` , service_centres
                                WHERE  `new_state`
                                IN (
                                'Pending',  'Rescheduled'
                                )
                                " . $where . "
                                AND booking_state_change.create_date >= DATE_SUB( CURDATE( ) , INTERVAL 1
                                DAY )
                                AND booking_state_change.create_date < CURDATE()
                                AND service_centres.create_date BETWEEN CURDATE() - INTERVAL 60 DAY AND CURDATE()
                                AND `booking_details`.booking_id = `booking_state_change`.booking_id
                                AND `service_centres`.id = `booking_details`.assigned_vendor_id
                                GROUP BY service_centres.state, service_centres.name";

        $booking_gone_mtd = "SELECT COUNT( DISTINCT (
                                            `booking_details`.booking_id
                                            ) ) AS completed, service_centres.id as service_center_id 
                                            FROM `booking_details` , service_centres
                                            WHERE 
                                                DATE_FORMAT( booking_details.create_date, '%m' ) = MONTH( CURDATE() ) 
                                                AND DATE_FORMAT( booking_details.create_date, '%Y' ) = YEAR( CURDATE() )
                                            " . $where . "
                                            AND service_centres.create_date BETWEEN CURDATE() - INTERVAL 60 DAY AND CURDATE()
                                            AND `service_centres`.id = `booking_details`.assigned_vendor_id
                                            GROUP BY service_centres.state, service_centres.name";

        $bookings_cancelled = "SELECT COUNT( DISTINCT (
                                            `booking_details`.booking_id
                                            ) ) AS cancelled, service_centres.id as service_center_id
                                            FROM `booking_details` , service_centres
                                            WHERE `current_status` = 'Cancelled'
                                            " . $where . "
                                            AND DATE_FORMAT( booking_details.create_date, '%m' ) = MONTH( CURDATE() ) 
                                            AND DATE_FORMAT( booking_details.create_date, '%Y' ) = YEAR( CURDATE() )
                                            AND service_centres.create_date BETWEEN CURDATE() - INTERVAL 60 DAY AND CURDATE()
                                            AND `service_centres`.id = `booking_details`.assigned_vendor_id
                                            GROUP BY service_centres.state, service_centres.name";
        $bookings_completed = "SELECT COUNT( DISTINCT (
                                            `booking_details`.booking_id
                                            ) ) AS completed, service_centres.id as service_center_id 
                                            FROM `booking_details` , service_centres
                                            WHERE `current_status` = 'Completed'
                                            " . $where . "
                                            AND DATE_FORMAT( booking_details.create_date, '%m' ) = MONTH( CURDATE() ) 
                                            AND DATE_FORMAT( booking_details.create_date, '%Y' ) = YEAR( CURDATE() )
                                             AND service_centres.create_date BETWEEN CURDATE() - INTERVAL 60 DAY AND CURDATE()
                                            AND `service_centres`.id = `booking_details`.assigned_vendor_id
                                            GROUP BY service_centres.state, service_centres.name";

        $pending_bookings_last_2_days = "SELECT service_centres.id AS service_center_id, COUNT(booking_id ) AS pending 
                                            FROM booking_details
                                            JOIN service_centres ON service_centres.id = booking_details.assigned_vendor_id
                                            WHERE 
                                            DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_details.booking_date, '%d-%m-%Y')) >= 0
                                            " . $where . "
                                            AND 
                                            DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_details.booking_date, '%d-%m-%Y')) <= 2
                                            AND current_status
                                            IN (
                                            'Pending', 'Rescheduled'
                                            )
                                            AND service_centres.create_date BETWEEN CURDATE() - INTERVAL 60 DAY AND CURDATE()
                                            GROUP BY service_centres.state, service_centres.name";

        $pending_bookings_last_3_5_days = "SELECT service_centres.id AS service_center_id, COUNT(booking_id ) AS pending 
                                            FROM booking_details
                                            JOIN service_centres ON service_centres.id = booking_details.assigned_vendor_id
                                            WHERE 
                                            DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_details.booking_date, '%d-%m-%Y')) >= 3
                                            AND 
                                            DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_details.booking_date, '%d-%m-%Y')) <= 5
                                            " . $where . "
                                            AND current_status
                                            IN (
                                            'Pending', 'Rescheduled'
                                            )
                                                                        AND service_centres.create_date BETWEEN CURDATE() - INTERVAL 60 DAY AND CURDATE()
                                            GROUP BY service_centres.state, service_centres.name";

        $pending_bookings_greater_than_5_days = "SELECT service_centres.id AS service_center_id, COUNT(booking_id ) AS pending 
                                                    FROM booking_details
                                                    JOIN service_centres ON service_centres.id = booking_details.assigned_vendor_id
                                                    WHERE 
                                                                DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_details.booking_date, '%d-%m-%Y')) > 5
                                                    " . $where . "
                                                    AND current_status
                                                    IN (
                                                    'Pending', 'Rescheduled'
                                                    )
                                                    AND service_centres.create_date BETWEEN CURDATE() - INTERVAL 60 DAY AND CURDATE()
                                                    GROUP BY service_centres.state, service_centres.name";

        $data['yesterday_bookings_gone'] = $this->make_sc_array($this->db->query($yesterday_bookings_gone)->result_array());
        $data['booking_gone_mtd'] = $this->make_sc_array($this->db->query($booking_gone_mtd)->result_array());
        $data['bookings_cancelled'] = $this->make_sc_array($this->db->query($bookings_cancelled)->result_array());
        $data['bookings_completed'] = $this->make_sc_array($this->db->query($bookings_completed)->result_array());
        $data['pending_bookings_last_2_days'] = $this->make_sc_array($this->db->query($pending_bookings_last_2_days)->result_array());
        $data['pending_bookings_last_3_5_days'] = $this->make_sc_array($this->db->query($pending_bookings_last_3_5_days)->result_array());
        $data['pending_bookings_greater_than_5_days'] = $this->make_sc_array($this->db->query($pending_bookings_greater_than_5_days)->result_array());

        return $data;
    }

    /**
     * @Desc: This is used to make array acc to vendor id
     * @params: Array
     * 
     * @return: Array
     */
    private function make_sc_array($data) {
        $sc_array = [];
        foreach ($data as $key => $value) {
            $sc_array[$value['service_center_id']] = $value;
        }
        return $sc_array;
    }

    /**
     * @Desc : This function is used to insert values in scheduler_tasks_log table for each execution of 
     *          Scheduler Tasks that has been executed.
     * @params: Array of Data
     * @return : void
     * 
     */
    function insert_scheduler_tasks_log($tasks) {
        $data['task_name'] = $tasks;
        $this->db->insert('scheduler_tasks_log', $data);
    }

    function insert_scheduler_tasks_status($data) {
        $this->db->insert('scheduler_tasks_status', $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    function update_scheduler_task_status($scheduler_id) {
        $data = array('end_time' => date('Y-m-d-H-i-s'));
        $this->db->where('id', $scheduler_id);
        $this->db->update('scheduler_tasks_status', $data);
    }

    /**
     * @Desc: This function is used to get values from scheduler tasks log table by Date
     * @params: date
     * @return: Array
     * 
     */
    function get_scheduler_tasks_log($date) {
        $this->db->select('*');
        $this->db->like('executed_on', $date);
        $query = $this->db->get('scheduler_tasks_log');
        return $query->result_array();
    }

    /**
     * @Desc: This is used to get agent daily reports
     * @params: void
     * 
     * @return: Array
     */
    function get_agent_daily_reports($flag ,$startDate = "" , $endDate= "" ) {

        $data = array();
        if($flag == "" && $startDate != "" && $endDate != ""){
            $where1 = "AND booking_state_change.create_date >=". "'$startDate'" . " AND booking_state_change.create_date <=" ."'$endDate'";
            $where2 = "AND agent_outbound_call_log.create_date >=". "'$startDate'" . " AND agent_outbound_call_log.create_date <=" ."'$endDate'";
            $where3 = "AND CallType = 'completed' AND DialCallDuration >0 AND passthru_misscall_log.create_date >=". "'$startDate'" . " AND passthru_misscall_log.create_date <=" ."'$endDate'";
        }
        else if ($flag == 'month') {
            $where1 = 'and month(booking_state_change.create_date) = month(CURDATE()) and 
                      YEAR(booking_state_change.create_date) = YEAR(CURDATE())';
            $where2 = 'and month(agent_outbound_call_log.create_date) = month(CURDATE()) and 
                      YEAR(agent_outbound_call_log.create_date) = YEAR(CURDATE())';
            $where3 = "and CallType = 'completed' and DialCallDuration >0 and month(passthru_misscall_log.create_date) = month(CURDATE()) and 
                      YEAR(passthru_misscall_log.create_date) = YEAR(CURDATE())";
        } else if ($flag == 'yesterday') {
            $where1 = 'and DATE(booking_state_change.create_date) = DATE(CURDATE())-1';
            $where2 = 'and DATE(agent_outbound_call_log.create_date) = DATE(CURDATE())-1';
            $where3 = "and CallType = 'completed' and DialCallDuration >0 and DATE(passthru_misscall_log.create_date) = DATE(CURDATE())-1";
        } else if ($flag == 'week') {
            $where1 = 'and DATE(booking_state_change.create_date) > DATE_SUB(NOW(), INTERVAL 1 WEEK)';
            $where2 = 'and DATE(agent_outbound_call_log.create_date) > DATE_SUB(NOW(), INTERVAL 1 WEEK)';
            $where3 = "and CallType = 'completed' and DialCallDuration >0 and DATE(passthru_misscall_log.create_date) > DATE_SUB(NOW(), INTERVAL 1 WEEK)";
        } else {
            $where1 = 'and DATE(booking_state_change.create_date) = DATE(CURDATE())';
            $where2 = 'and DATE(agent_outbound_call_log.create_date) = DATE(CURDATE())';
            $where3 = "and CallType = 'completed' and DialCallDuration >0 and DATE(passthru_misscall_log.create_date) = DATE(CURDATE())";
        }


        $this->db->select('full_name , id');
        $this->db->from('employee');
        $this->db->where('active' ,'1');
        $this->db->where_in('groups', array('callcenter', 'closure'));
        $this->db->order_by('full_name');
        $query = $this->db->get();

        $employee_id = $query->result_array();
        foreach ($employee_id as $value) {
            
            $cancel_query = "SELECT count(booking_id) AS query_cancel FROM booking_state_change
                             WHERE booking_state_change.old_state = 'FollowUP' 
                             AND booking_state_change.new_state='Cancelled' 
                             AND partner_id = '"._247AROUND."' AND agent_id= '" . $value['id'] . "' $where1";
            //getting booking query data
            $booking_query = "SELECT count(booking_id) AS query_booking FROM booking_state_change
                              WHERE ((booking_state_change.old_state = 'FollowUP' 
                              AND booking_state_change.new_state='Pending') OR (booking_state_change.old_state = 'New_Booking' 
                              AND booking_state_change.new_state='Pending')) 
                              AND partner_id = '"._247AROUND."' AND agent_id= '" . $value['id'] . "' $where1";
            
            //getting outgoing calls data
            $calls_placed = "SELECT count(agent_id) AS calls_placed FROM agent_outbound_call_log
                             WHERE agent_outbound_call_log.agent_id= '" . $value['id'] . "'  $where2";
            //getting received incomming calls data
           $calls_recevied = "SELECT COUNT(DialWhomNumber) AS incomming , full_name 
                               FROM passthru_misscall_log JOIN employee ON passthru_misscall_log.DialWhomNumber 
                               = concat('0' , employee.phone ) 
                               WHERE callType = 'completed' AND employee. phone !='' AND employee.id='" . $value['id'] . "' $where3";
            
            //getting agent rating data
            $rating_query = "SELECT count(old_state) AS rating FROM booking_state_change 
                              WHERE  new_state = '".RATING_NEW_STATE."' 
                              AND partner_id = '"._247AROUND."' AND agent_id = '" . $value['id'] . "' $where1";


            $cancel_query_data = $this->db->query($cancel_query);
            $booking_query_data = $this->db->query($booking_query);
            $calls_placed_data = $this->db->query($calls_placed);
            $calls_recevied_data = $this->db->query($calls_recevied);
            $rating_query_data = $this->db->query($rating_query);


            $cancel_query_result = $cancel_query_data->result_array();
            $booking_query_result = $booking_query_data->result_array();
            $calls_placed_result = $calls_placed_data->result_array();
            $calls_recevied_result = $calls_recevied_data->result_array();
            $rating_query_result = $rating_query_data->result_array();
            
            //storing key value from $result to $data_details  
            $data_details['employee_id'] = $value['full_name'];
            $data_details['followup_to_cancel'] = $cancel_query_result[0]['query_cancel'];
            $data_details['followup_to_pending'] = $booking_query_result[0]['query_booking'];
            $data_details['calls_placed'] = $calls_placed_result[0]['calls_placed'];
            $data_details['calls_recevied'] = $calls_recevied_result[0]['incomming'];
            $data_details['rating'] = $rating_query_result[0]['rating'];

            //store all data into array $data
            array_push($data, $data_details);
        }

        return $data;
    }

    function insert_agent_daily_reports($data) {
        $this->db->insert_batch('agent_daily_report_stats', $data);
        $result = (bool) ($this->db->affected_rows() > 0);
        return $result;
    }

    /**
     * @Desc: This function is used to dump data into sf_snapshot table
     * @params: Array
     * @return: Boolean
     * 
     */
    function insert_batch_sf_snapshot($data) {
        $this->db->insert_batch('sf_snapshot', $data);
        $result = (bool) ($this->db->affected_rows() > 0);
        return $result;
    }

    function get_pincode_not_available_bookings() {
        $sql = "SELECT bd.booking_id, services.services,
                bd.booking_pincode,bd.city, bd.state
                from booking_details as bd
                JOIN  `users` ON  `users`.`user_id` =  `bd`.`user_id`
                JOIN  `services` ON  `services`.`id` =  `bd`.`service_id`
                WHERE `bd`.booking_id LIKE '%Q-%' AND (DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(bd.booking_date, '%d-%m-%Y')) >= 0 OR
                bd.booking_date='') AND `bd`.current_status='FollowUp'
                AND NOT EXISTS 
                (SELECT 1
                FROM (`vendor_pincode_mapping`)
                JOIN `service_centres` ON `service_centres`.`id` = `vendor_pincode_mapping`.`Vendor_ID`
                WHERE `vendor_pincode_mapping`.`Appliance_ID` = bd.service_id
                AND `vendor_pincode_mapping`.`Pincode` = bd.booking_pincode
                AND `service_centres`.`active` = '1' AND `service_centres`.on_off = '1') 
                AND bd.booking_pincode!=''
                AND bd.booking_pincode!=0;";
        $query = $this->db->query($sql);
        return $query;
    }

    /**
     * @Desc: This function is used to get Partner completed bookings reports
     * @params: string
     * @return: array()
     * 
     */
    function get_partners_booking_report_chart_data($startDate,$endDate,$bookingStatus) {
        $where="";
        if($bookingStatus == 'ALL'){
            $where .= "bd.create_date >=". "'$startDate'" . " AND bd.create_date <=" ."'$endDate'";
        }else if($bookingStatus == 'Completed' || $bookingStatus == 'Cancelled'){
            $where .= "bd.current_status = '$bookingStatus' AND bd.closed_date >=". "'$startDate'" . " AND bd.closed_date <=" ."'$endDate'";
        }else if ( $bookingStatus == 'Pending' || $bookingStatus == 'Rescheduled'){
            $where .= "bd.current_status = '$bookingStatus' AND bd.create_date >=". "'$startDate'" . " AND bd.create_date <=" ."'$endDate'";
        }
        else if($bookingStatus == 'FollowUp'){
            $where .= "bd.current_status = '$bookingStatus' AND SUBSTR(bd.booking_id ,1,2) = 'Q-' AND bd.create_date >=". "'$startDate'" . " AND bd.create_date <=" ."'$endDate'";
        }
        
        $this->db->distinct();
        $this->db->select('bd.partner_id,p.public_name,count(*) as count');
        $this->db->from('booking_details as bd');
        $this->db->join('partners as p', 'bd.partner_id=p.id', 'left');
        $this->db->where($where);
        $this->db->group_by('partner_id');
        $query = $this->db->get();
        return  $query->result_array();
    }
    
    function get_partners_booking_unit_report_chart_data($startDate,$endDate,$bookingStatus) {
        $where="";
        if($bookingStatus == 'ALL'){
            $where .= "bu.create_date >=". "'$startDate'" . " AND bu.create_date <=" ."'$endDate' AND p.is_active= '1' ";
        }else if($bookingStatus == 'Completed' || $bookingStatus == 'Cancelled'){
            $where .= "bu.booking_status = '$bookingStatus' AND bu.ud_closed_date >=". "'$startDate'" . " AND bu.ud_closed_date <=" ."'$endDate' AND p.is_active= '1' ";
        }else if ( $bookingStatus == 'Pending' ){
            $where .= "bu.booking_status = '$bookingStatus' AND bu.create_date >=". "'$startDate'" . " AND bu.create_date <=" ."'$endDate' AND p.is_active= '1' ";
        }

        $this->db->select('bu.partner_id,p.public_name,count(*) as count');
        $this->db->from('booking_unit_details as bu');
        $this->db->join('partners as p', 'bu.partner_id = p.id', 'left');
        $this->db->where($where);
        $this->db->group_by('partner_id');
        $query = $this->db->get();
        return  $query->result_array();
    }

    /**
     * @Desc: This function is used to get all latest file uploaded in s3 with agent name
     * @params:void
     * @return:array
     * 
     */
    function get_all_latest_uploaded_file($file_type) {
        $where = "where file_type IN ($file_type)";
        $sql = "SELECT a.file_type, b.full_name,a.upload_date FROM (SELECT file_type, agent_id,create_date as upload_date
                FROM file_uploads
                WHERE create_date IN (
                SELECT MAX(create_date)
                FROM file_uploads
                GROUP BY file_type
                ) ORDER BY create_date DESC, file_type) as a JOIN employee as b ON a.agent_id=b.id $where";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    /**
     * @Desc: This function is used to get the latest file name uploaded in s3 for downloading
     * @params:array
     * @return:array
     * 
     */
    function get_latest_file($where) {
        $this->db->select('file_name');
        if (isset($where['bucket_name'])) {
            $this->db->from('pincode_mapping_s3_upload_details');
        } else {
            $this->db->from('file_uploads');
        }

        $this->db->where($where);
        $this->db->order_by('create_date', 'desc');
        $this->db->limit('1', '0');
        $query = $this->db->get();

        return $query->result_array();
    }
    
    function get_uploaded_file_history($post_data=NULL)
    {
      
        $sql = "SELECT IF(e.full_name IS NULL,entity_login_table.agent_name,e.full_name) as agent_name,p.file_name,p.create_date AS upload_date,p.result, p.id, p.revert_file_name FROM file_uploads AS p  left JOIN employee AS e ON p.agent_id = e.id LEFT JOIN entity_login_table ON p.agent_id = entity_login_table.agent_id ";
        
        if(!empty($post_data['file_type'])){
            $sql .=  " WHERE file_type = '".trim($post_data['file_type'])."'";
        }
        
        if(!empty($post_data['file_type_not_equal_to'])){
             $sql .=  " AND file_type != '".trim($post_data['file_type_not_equal_to'])."' ";
        }
        
        if(!empty($post_data['partner_id'])){
            $sql .=  " AND entity_type = 'partner' AND p.entity_id = '".$post_data['partner_id']."'";
        }
        
        if(!empty($post_data['search_value'])){
            $sql .= " AND file_name LIKE '%".$post_data['search_value']."%' ";
        }
        
        if(!empty($post_data['result'])){
            $sql .= " AND result = '".$post_data['result']."' ";
        }
        
        if(!empty($post_data['from_date'])){
            $sql .= " AND ".$post_data['from_date'] ;
        }
        
        if(!empty($post_data['to_date'])){
            $sql .= " AND ".$post_data['to_date'] ;
        }
        
        $sql .= " ORDER BY p.create_date DESC"; 
        
        if(($post_data['start'] !== NULL)  && ($post_data['length'] !== NULL)){
            $sql .=" LIMIT ".$post_data['start'].",".$post_data['length'];
        }
        
 

        
        $query = $this->db->query($sql);
        return $query->result();
    }
    
    

}
