<?php

class Reporting_utils extends CI_Model {

    /**
     * @desc load both db
     */
    function __construct() {
        parent::__Construct();

        $this->db = $this->load->database('default', TRUE, TRUE);
    }

    //Get today's pending bookings to generate summary mail for Admin
    //Ignore queries and bookings having age < 3 days
    function get_pending_bookings() {
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
                users.phone_number AS user_phone,
                service_centres.name AS sc_name,
                service_centres.primary_contact_name AS sc_contact,
                service_centres.primary_contact_phone_1 AS sc_phone
                FROM (booking_details)
                JOIN  `users` ON  `users`.`user_id` =  `booking_details`.`user_id`
                JOIN  `services` ON  `services`.`id` =  `booking_details`.`service_id`
                LEFT JOIN  `service_centres` ON  `booking_details`.`assigned_vendor_id` = `service_centres`.`id`
                WHERE booking_details.current_status IN ('Pending', 'Rescheduled') AND
		DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_details.booking_date, '%d-%m-%Y')) > 2
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
    function get_num_pending_bookings_for_all_sc() {
	$query = $this->db->query("SELECT service_centres.name AS sc_name, COUNT(booking_details.booking_id) AS num_bookings FROM `booking_details` LEFT JOIN service_centres ON booking_details.assigned_vendor_id = service_centres.id WHERE booking_details.current_status IN ('Pending', 'Rescheduled') AND  DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_details.booking_date, '%d-%m-%Y')) > 2 GROUP BY sc_name ORDER BY num_bookings DESC");

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
        
        log_message('info', __FUNCTION__. " SQL: ". $this->db->last_query());

        //echo $this->db->last_query();
    }

    function get_pending_bookings2() {
        //log_message('info', __METHOD__);

        $this->db->select("booking_details.booking_id, booking_details.create_date, "
            . "booking_details.items_selected, booking_details.total_price,"
            . "DATEDIFF (CURRENT_TIMESTAMP, booking_details.create_date) as booking_age,"
            . "services.services as service_name,"
            . "users.name as user_name, users.phone_number as user_phone,"
            . "service_centres.name as sc_name, "
            . "service_centres.primary_contact_name as sc_contact,"
            . "service_centres.primary_contact_phone_1 as sc_phone");
        $this->db->from("booking_details");
        $this->db->join('services', 'services.id = booking_details.service_id');
        $this->db->join('users', 'users.user_id = booking_details.user_id');
        $this->db->join('service_centres', 'service_centres.id = booking_details.assigned_vendor_id');

        $query = $this->db->get();

        //$result = (bool) ($this->db->affected_rows() > 0);
        //log_message('info', __METHOD__ . " => SQL: " . $this->db->last_query() . ", Result: " . $result);
        //log_message('info', print_r($query->result_array(), TRUE));

        return $query->result_array();
    }

    function find_service_centers() {
	$query = $this->db->query("Select * from service_centres where active = 1");
	return $query->result_array();
    }

    function find_all_service_centers() {
	$query = $this->db->get("service_centres");
	return $query->result_array();
    }

    function installation_request_leads($partner_id, $date =""){
        //Count y'day leads
        print_r($date);
        if($date !="")
            $this->db->where('ReferenceDate', $date);
        $this->db->where("PartnerID",$partner_id);
        return $this->db->count_all_results('partner_leads');
    }

    function scheduled_installation($partner_id, $date=""){
        //Count y'day installations scheduled
        $this->db->where("PartnerID", $partner_id);
        $this->db->where_in('247aroundBookingStatus', array('Completed', 'Pending', 'Rescheduled'));
        if($date !="")
            $this->db->like('ReferenceDate', $date);
        return $this->db->count_all_results('partner_leads');
    }
    function installation_completed($partner_id, $date =""){
            //Count y'day installations completed
        $this->db->where("PartnerID", $partner_id);
        $this->db->where_in('247aroundBookingStatus', 'Completed');
        if($date !="")
            $this->db->where('ReferenceDate', $date);
        return $this->db->count_all_results('partner_leads');
    }
    function phone_unreachable($partner_id, $date=""){
        if($date !="")
           $this->db->where('ReferenceDate', $date);
        $this->db->where("PartnerID", $partner_id);
        $this->db->where_in('247aroundBookingStatus', 'FollowUp');
        return $this->db->count_all_results('partner_leads');
    }
    function already_installed($partner_id, $date=""){
        //Count total - Already Installed
        if($date !="")
           $this->db->where('ReferenceDate', $date);
        $this->db->where("PartnerID", $partner_id);
        $this->db->where_in('247aroundBookingStatus', 'Already Installed');
        return  $this->db->count_all_results('partner_leads');
    }
    function cancelled_installation($partner_id, $date=""){
            //Count total installations Cancelled
        $this->db->where("PartnerID", $partner_id);
        if($date !="")
           $this->db->where('ReferenceDate', $date);
        $this->db->where_in('247aroundBookingStatus', 'Cancelled');
        return $this->db->count_all_results('partner_leads');
    }
    function pending_installation($partner_id, $date=""){
        //Count total - Already Installed
        if($date !="")
           $this->db->where('ReferenceDate', $date);
        $this->db->where("PartnerID", $partner_id);
        $this->db->where_in('247aroundBookingStatus', array('Pending', 'Rescheduled'));
        return  $this->db->count_all_results('partner_leads');
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
//	$this->db->where('Rating_Stars !=', '');
//	$this->db->select_avg('Rating_Stars');
//	$query = $this->db->get('snapdeal_leads');
//	$avg_rating = $query->result_array()[0]['Rating_Stars'];

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
//	$query = $this->db->query("SELECT * FROM snapdeal_leads");
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

    function get_all_partner_leads($partner_id){
        $this->db->select('*');
        $this->db->where("PartnerID",$partner_id);
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
        $this->db->where('partner_id',$partner_id);
        $query = $this->db->get('bookings_sources');
        return $query->result_array();
    }

    function booking_report(){
        for($i = 1; $i < 3; $i++){
            $where = "" ;

            if($i == 2){
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
            $result['data'.$i] = $data->result_array();
        }

        return $result;
    }

    /**
     * @desc: This method is used to return all partner booking summary details for today,
     * within month and total
     * @return array
     */
    function get_partner_summary_data(){
        $partner = $this->get_partner_to_send_mail();
        $summary_data =  array();
        foreach ($partner as $key => $value){
            for($i = 0; $i < 3; $i++){
            $where = "" ;
            // In this if clause, we set date for today
            if($i == 0){
                $where = " AND create_date >= CURDATE() AND create_date < CURDATE() + INTERVAL 1 DAY ";
            } else if($i == 1) {
                $where = " AND create_date >=  DATE_SUB(NOW(), INTERVAL 1 MONTH) ";
            } else if($i == 2) {
                $where = "";
            }
            $sql = "SELECT source, partner_source, "
                    . " SUM(CASE WHEN `current_status` LIKE '%FollowUp%' THEN 1 ELSE 0 END) AS queries,"
                    . " SUM(CASE WHEN `current_status` LIKE '%Cancelled%' THEN 1 ELSE 0 END) AS cancelled,"
                    . " SUM(CASE WHEN `current_status` LIKE '%Completed%' THEN 1 ELSE 0 END) AS completed,"
                    . " SUM(CASE WHEN `current_status` LIKE '%Pending%' OR `current_status` LIKE '%Rescheduled%' THEN 1 ELSE 0 END) as scheduled,"
                    . " SUM(CASE WHEN `current_status` LIKE '%FollowUp%' OR `current_status` LIKE '%Completed%' OR `current_status` LIKE '%Cancelled%' OR `current_status` LIKE '%Pending%' OR `current_status` LIKE '%Rescheduled%' THEN 1 ELSE 0 END) AS total_booking "
                    . " From booking_details Where partner_id = '". $value['partner_id']."' $where  Group By partner_source ";
            $data = $this->db->query($sql);
            $partner[$key]['data'.$i] = $data->result_array();
            }


        }
        return $partner;
    }
    /**
     *  @desc: This Method returns all partner details who have email id in the
     *  bookings_sources table
     */
    function get_partner_to_send_mail(){
        $this->db->select('*');
        $this->db->where_not_in('partner_email_for_to','');
        $query = $this->db->get('bookings_sources');
        return $query->result_array();
    }
    
    function get_report_data(){
        for($i = 1; $i < 3; $i++){
            $where = "where DATE_FORMAT(booking_state_change.create_date,'%y-%m-%d') = CURDATE() " ;
            
            if($i == 2){
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
            
            foreach($result_data as $value){
                if($i == 1){
                    
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
                    
                }else{
                    
                    //Count this month installations Completed
                    $sql = "SELECT * FROM booking_details WHERE partner_id = '" . $value['partner_id'] . "'"
                    . " AND month(closed_date) = month(CURRENT_DATE) "
                    . "AND current_status = '"._247AROUND_COMPLETED."'";
                    $can_query = $this->db->query($sql);
                    $month_install_can = $can_query->result_array();
                    $month_install_comp = count($month_install_can);
                    $result['month_completed'][] = $month_install_comp;
                    
                    //Count this month installations Cancelled
                    $sql = "SELECT * FROM booking_details WHERE partner_id = '" . $value['partner_id'] . "'"
                    . " AND month(closed_date) = month(CURRENT_DATE) "
                    . "AND current_status = '"._247AROUND_CANCELLED."'";
                    $can_query1 = $this->db->query($sql);
                    $month_can = $can_query1->result_array();
                    $month_cancl = count($month_can);
                    $result['month_cancelled'][] = $month_cancl;

                }
            }
            $result['data'.$i] = $result_data;
        }
        return $result;
    }
    /**
     * @desc: This function is used to get data of Agents doing current date's booking 
     *        from 247Around CRM. It is used to send details in daily_report mail.
     * params:void
     * return: array
     */
    function get_247_agent_report_data(){
        $where = "where DATE_FORMAT(booking_state_change.create_date,'%y-%m-%d') = CURDATE() AND partner_id = "._247AROUND ;
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
    
    /*
     * @desc: This function is used to get reporting data acc to service center
     * params: void
     * return: Array
     */

    function get_booking_by_service_center() {

        $where_today = "where DATE_FORMAT(service_center_booking_action.closed_date,'%y-%m-%d') = CURDATE()";
        $where_yesterday = "where DATE_FORMAT(service_center_booking_action.closed_date,'%y-%m-%d') = CURDATE() - INTERVAL 1 day";
        $where_month = "where DATE_FORMAT(service_center_booking_action.closed_date,'%m') = MONTH(CURDATE())";
        $where_last_3_days = "where DATE_FORMAT(service_center_booking_action.closed_date,'%y-%m-%d') >= CURDATE() - INTERVAL 3 day";
        $where_greater_than_5_days = "where DATE_FORMAT(service_center_booking_action.closed_date,'%y-%m-%d') <= CURDATE() - INTERVAL 5 day";

        //Sql query for Completed, Cancelled, Pending state
        $sql = "SELECT service_center_booking_action.service_center_id, service_centres.state, service_centres.district,
                 SUM(CASE WHEN `current_status` LIKE '%Cancelled%' AND `booking_id` LIKE 'S%' THEN 1 ELSE 0 END) AS cancelled,
                 SUM(CASE WHEN `current_status` LIKE '%Completed%' AND `booking_id` LIKE 'S%' THEN 1 ELSE 0 END) AS completed,
                 SUM(CASE WHEN `current_status` LIKE '%Pending%' AND `booking_id` LIKE 'S%' THEN 1 ELSE 0 END) AS pending
                 from service_center_booking_action
                 JOIN service_centres ON service_centres.id = service_center_booking_action.service_center_id";



        $sql_today = $sql . ' ' . $where_today . " GROUP BY service_centres.state";
        $sql_yesterday = $sql . ' ' . $where_yesterday . " GROUP BY  service_centres.state";
        $sql_month = $sql . ' ' . $where_month . " GROUP BY  service_centres.state";
        $sql_last_3_day = $sql . ' ' . $where_last_3_days . " GROUP BY service_centres.state";
        $sql_greater_than_5_days = $sql . ' ' . $where_greater_than_5_days . " GROUP BY  service_centres.state";

        $data_today = $this->db->query($sql_today);
        $data_yesterday = $this->db->query($sql_yesterday);
        $data_month = $this->db->query($sql_month);
        $data_last_3_day = $this->db->query($sql_last_3_day);
        $data_greater_than_5_days = $this->db->query($sql_greater_than_5_days);

        //Setting $result array with all values
        $result['today'] = $data_today->result_array();
        $result['yesterday'] = $data_yesterday->result_array();
        $result['month'] = $data_month->result_array();
        $result['last_3_day'] = $data_last_3_day->result_array();
        $result['greater_than_5_days'] = $data_greater_than_5_days->result_array();

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
        $max_length = max(sizeof($result['today']), sizeof($result['yesterday']), sizeof($result['month']), sizeof($result['last_3_day']), sizeof($result['greater_than_5_days']));
        //Getting service_center_id array
        $service_center_id = $this->get_service_center_id_array($result, $max_length);

        for ($i = 0; $i < $max_length; $i++) {

            // Setting value in final data array according to service center id
            if (!empty($result['today'][$i])) {
                $data['today'] = $this->search_service_center_id($service_center_id, $result['today'][$i]);
            }
            if (!empty($result['yesterday'][$i])) {
                $data['yesterday'] = $this->search_service_center_id($service_center_id, $result['yesterday'][$i]);
            }
            if (!empty($result['month'][$i])) {
                $data['month'] = $this->search_service_center_id($service_center_id, $result['month'][$i]);
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

        return $this->get_final_array($service_center_id, $data_final);
    }

    /**
     * @desc: This fucntion is used to match array values to the set of service_center_id's
     * params: Array, Array
     *         sevice_center_id array, array to be searched
     * return: Array or void 
     */
    private function search_service_center_id($service_center, $data) {
        foreach ($service_center as $value) {
            if ($data['service_center_id'] == $value) {
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
            if (isset($result['today'][$i]['service_center_id'])) {
                $service_center_id[] = $result['today'][$i]['service_center_id'];
            }
            if (isset($result['month'][$i]['service_center_id'])) {
                $service_center_id[] = $result['month'][$i]['service_center_id'];
            }
            if (isset($result['last_3_day'][$i]['service_center_id'])) {
                $service_center_id[] = $result['last_3_day'][$i]['service_center_id'];
            }
            if (isset($result['yesterday'][$i]['service_center_id'])) {
                $service_center_id[] = $result['yesterday'][$i]['service_center_id'];
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
                    if ($v['service_center_id'] == $value) {
                        $final_data[$value][$k] = $v;
                    }
                }
            }
        }
        return $final_data;
    }

}
