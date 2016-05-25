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

    function get_all_sd_leads() {
	$query = $this->db->query("SELECT * FROM snapdeal_leads");

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
     * Get completed bookings to generate invoice for service center.
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
	    SELECT partner_leads_for_foc_invoicing.*, booking_details.assigned_vendor_id
	  FROM partner_leads_for_foc_invoicing, booking_details
	  WHERE partner_leads_for_foc_invoicing.booking_id = booking_details.booking_id
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
                 SUM(CASE WHEN `current_status` = 'FollowUp' THEN 1 ELSE 0 END) AS queries,
                 SUM(CASE WHEN `current_status` = 'Cancelled' THEN 1 ELSE 0 END) AS cancelled,
                 SUM(CASE WHEN `current_status` = 'Pending' OR `current_status` = 'Rescheduled' THEN 1 ELSE 0 END) as scheduled,
                 SUM(CASE WHEN `current_status` = 'FollowUp' OR  `current_status` = 'Cancelled' OR `current_status` = 'Pending' OR `current_status` = 'Rescheduled' THEN 1 ELSE 0 END) AS total 
  
                from booking_details $where Group By source ;

                 "; 
                
            $data = $this->db->query($sql);
            $result['data'.$i] = $data->result_array();
        }

        return $result;
    }
}
