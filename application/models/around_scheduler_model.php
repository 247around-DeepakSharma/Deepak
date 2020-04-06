<?php

class Around_scheduler_model extends CI_Model {

    Private $BIG_MAINDATA = array();

    /**
     * @desc load both db
     */
    function __construct() {
        parent::__Construct();
    }

    /*
     * @desc: This method is used to send reminder SMS to users for whom:
     * Partner source => Snapdeal-shipped-excel, Snapdeal-delivered-excel
     * Booking Date => Today / ''
     * Current status => FollowUp
     * Vendor => Available
     */

    function get_reminder_installation_sms_data_today() {
        //Filter using booking_date instead of EDD
        $sql = "SELECT DISTINCT ss1.type_id,ss1.type,bd.booking_primary_contact_no,
                ss1.booking_id, ss1.content FROM booking_details AS bd 
                JOIN sms_sent_details AS ss1 ON (ss1.booking_id = bd.booking_id ) 
                
                WHERE booking_date IN (
                DATE_FORMAT( CURDATE(),  '%d-%m-%Y' ),
                ''
                )
                AND current_status = 'FollowUp' AND internal_status != 'Missed_call_confirmed'
                AND sms_count < 3
                AND ss1.sms_tag IN ('sd_delivered_missed_call_initial',  'sd_shipped_missed_call_initial',
                'missed_call_initial_prod_desc_not_found', 'partner_missed_call_for_installation')";

        $query = $this->db->query($sql);

        log_message('info', __METHOD__ . "=> Booking  SQL " . $this->db->last_query());

        return $query->result();
    }

    /*
     * @desc: This method is used to send reminder SMS to users for whom:
     * Partner source => Snapdeal-shipped-excel, Snapdeal-delivered-excel
     * EDD => Tommorrow (T+1), T+2, T+3 days
     * Current status => FollowUp
     * Vendor => Available
     */

    function get_reminder_installation_sms_data_future() {
        /*
          $sql = " SELECT booking_details.*, `services`.services from booking_details, services "
          . " where partner_source = 'Snapdeal-shipped-excel' AND internal_status = 'Missed_call_not_confirmed' "
          . " AND estimated_delivery_date > CURDATE() AND estimated_delivery_date = (CURDATE() + INTERVAL 1 DAY) "
          . " AND current_status= 'FollowUp' AND `booking_details`.service_id = `services`.id "
          . " AND booking_pincode In (Select vendor_pincode_mapping.Pincode from vendor_pincode_mapping, "
          . " service_centres where service_centres.id = vendor_pincode_mapping.Vendor_ID AND service_centres.active = '1' "
          . " AND vendor_pincode_mapping.active = '1' );";
         * 
         */

        //Filter using booking_date instead of EDD
        $sql = "SELECT booking_details.*, `services`.services from booking_details, services 
              WHERE partner_source IN ('Snapdeal-shipped-excel', 'Snapdeal-delivered-excel' )
	      AND booking_date IN (
              DATE_FORMAT( CURDATE() + INTERVAL 1 DAY ,  '%d-%m-%Y' ),
              DATE_FORMAT( CURDATE() + INTERVAL 2 DAY ,  '%d-%m-%Y' ),
              DATE_FORMAT( CURDATE() + INTERVAL 3 DAY ,  '%d-%m-%Y' )
              )
	      AND current_status= 'FollowUp' AND internal_status != 'Missed_call_confirmed'
              AND `booking_details`.service_id = `services`.id;";

        $query = $this->db->query($sql);

        log_message('info', __METHOD__ . "=> Booking  SQL " . $this->db->last_query());

        return $query->result();
    }

    /*
     * @desc: This method is used to send reminder SMS to users for whom:
     * Partner source => Snapdeal-shipped-excel, Snapdeal-delivered-excel
     * EDD => Past (T-1), T-2, T-3 days
     * Current status => FollowUp
     * Vendor => Available
     */

    function get_reminder_installation_sms_data_past() {
        //Filter using booking_date instead of EDD
        $sql = "SELECT booking_details.*, `services`.services from booking_details, services 
              WHERE partner_source IN ('Snapdeal-shipped-excel', 'Snapdeal-delivered-excel' )
	      AND booking_date IN (
              DATE_FORMAT( CURDATE() - INTERVAL 1 DAY ,  '%d-%m-%Y' ),
              DATE_FORMAT( CURDATE() - INTERVAL 2 DAY ,  '%d-%m-%Y' ),
              DATE_FORMAT( CURDATE() - INTERVAL 3 DAY ,  '%d-%m-%Y' ),
              DATE_FORMAT( CURDATE() - INTERVAL 4 DAY ,  '%d-%m-%Y' ),
              DATE_FORMAT( CURDATE() - INTERVAL 5 DAY ,  '%d-%m-%Y' ),
              DATE_FORMAT( CURDATE() - INTERVAL 6 DAY ,  '%d-%m-%Y' ),
              DATE_FORMAT( CURDATE() - INTERVAL 7 DAY ,  '%d-%m-%Y' ),
              DATE_FORMAT( CURDATE() - INTERVAL 8 DAY ,  '%d-%m-%Y' ),
              DATE_FORMAT( CURDATE() - INTERVAL 9 DAY ,  '%d-%m-%Y' ),
              DATE_FORMAT( CURDATE() - INTERVAL 10 DAY ,  '%d-%m-%Y' )
              )
	      AND current_status= 'FollowUp' AND internal_status != 'Missed_call_confirmed'
              AND `booking_details`.service_id = `services`.id;";

        $query = $this->db->query($sql);

        log_message('info', __METHOD__ . "=> Booking  SQL " . $this->db->last_query());

        return $query->result();
    }

    function get_reminder_installation_sms_data_geyser_delhi() {
        //Filter using booking_date instead of EDD
        $sql = "SELECT booking_details.* from booking_details 
                WHERE partner_source IN ('Snapdeal-shipped-excel', 'Snapdeal-delivered-excel' )
                AND booking_date IN (
                    DATE_FORMAT( CURDATE() - INTERVAL 1 DAY,  '%d-%m-%Y' ),
                    DATE_FORMAT( CURDATE(),  '%d-%m-%Y' ),
                    ''
                )
                AND current_status = 'FollowUp' AND internal_status != 'Missed_call_confirmed'
                AND service_id=32 and booking_pincode regexp '^11';";

        $query = $this->db->query($sql);

        log_message('info', __METHOD__ . "=> Booking  SQL " . $this->db->last_query());

        return $query->result();
    }

    /**
     * @desc: Get bookings, When booking date is empty, then getting those bookings which has 
     * difference between delivery date and current date are greater than 2.
     * AND When booking date is not empty, then getting those bookings which has difference between 
     *  delivery date and current date are greater than 5
     * @return Array
     */
    function get_old_pending_query() {
        $sql = " SELECT booking_id FROM booking_details WHERE booking_id LIKE '%Q-%' "
                . " AND partner_id = '1' "
                . " AND current_status = 'FollowUp' "
                . " AND CASE WHEN booking_date='' THEN DATEDIFF(CURRENT_TIMESTAMP , delivery_date) > 4 "
                . " WHEN booking_date !='' THEN DATEDIFF(CURRENT_TIMESTAMP , delivery_date) > 4 "
                . " END ";
        $query = $this->db->query($sql);
        $result = $query->result_array();

        log_message('info', __METHOD__ . "=> Count  Query to be Cancelled " . count($result));
        return $result;
    }

    /**
     * @desc: Get All bookings, who has not given Missed Call
     */
    function get_all_query() {
        $sql = "SELECT booking_details.* ,`services`.services from booking_details, services 
                    WHERE  `booking_id` LIKE  '%Q-%'
                    AND  `partner_id` =1
                    AND  `current_status` LIKE  'FollowUp'
                    AND `services`.id = `booking_details`.service_id ";

        $query = $this->db->query($sql);
        $result = $query->result();

        log_message('info', __METHOD__ . "=> Count  All Query " . count($result));
        return $result;
    }

    /**
     * @desc: This function is used get the user phone number to send promotional sms
     * @param:void()
     * @retun:void()
     */
    function get_user_phone_number($case) {

        switch ($case) {
            case 'completed' :
                $where = "current_status = '" . _247AROUND_COMPLETED . "'";
                $data = $this->get_completed_cancelled_booking_user_phn_number($where);
                break;
            case 'cancelled' :
                $where = "current_status = '" . _247AROUND_CANCELLED . "'";
                $data = $this->get_completed_cancelled_booking_user_phn_number($where);
                break;
            case 'query':
                $data = $this->get_cancelled_query_booking_user_phn_number();
                break;
            case 'not_exist':
                $data = $this->get_user_booking_not_exist_phn_number();
                break;
            case 'all':
                $data = $this->get_all_user_booking_phn_number();
                break;
            case 'promotion':
                $data = $this->get_all_promotional_active_user_phone_number();
                break;
        }

        if (!empty($data)) {
            return $data;
        } else {
            return FALSE;
        }
    }

    /**
     * @desc: This function is used get the user phone number for completed booking
     * @param: $where array();
     * @retun:array();
     */
    function get_completed_cancelled_booking_user_phn_number($where) {

        $sql = "SELECT DISTINCT booking_primary_contact_no as phn_number, current_status,booking_details.user_id
                FROM booking_details JOIN partners 
                ON booking_details.partner_id = partners.id
                JOIN users ON users.user_id = booking_details.user_id
                WHERE users.ndnc=0 AND booking_primary_contact_no REGEXP '^[6-9]{1}[0-9]{9}$' 
                AND partners.is_sms_allowed = '1'
                AND $where AND DAY(closed_date) = DAY(CURDATE()) 
                UNION 
                SELECT DISTINCT booking_alternate_contact_no as phn_number,current_status,booking_details.user_id
                FROM booking_details JOIN partners 
                ON booking_details.partner_id = partners.id
                JOIN users ON users.user_id = booking_details.user_id
                WHERE booking_alternate_contact_no REGEXP '^[6-9]{1}[0-9]{9}$'
                AND partners.is_sms_allowed = '1'
                AND $where AND DAY(closed_date) = DAY(CURDATE())";

        $query = $this->db->query($sql);
        return $query->result_array();
    }

    /**
     * @desc: This function is used get the user phone number for cancelled query
     * @param: void();
     * @retun:array();
     */
    function get_cancelled_query_booking_user_phn_number() {
        $sql = "SELECT DISTINCT booking_primary_contact_no as phn_number, 'Query' as current_status,booking_details.user_id
                FROM booking_details JOIN partners 
                ON booking_details.partner_id = partners.id
                JOIN users ON users.user_id = booking_details.user_id
                WHERE users.ndnc=0 AND booking_primary_contact_no REGEXP '^[6-9]{1}[0-9]{9}$' 
                AND partners.is_sms_allowed = '1'
                AND booking_details.type = 'Query' AND booking_details.current_status = '" . _247AROUND_CANCELLED . "' AND DAY(closed_date) = DAY(CURDATE())
                UNION 
                SELECT DISTINCT booking_alternate_contact_no as phn_number, 'Query' as current_status,booking_details.user_id
                FROM booking_details JOIN partners 
                ON booking_details.partner_id = partners.id
                JOIN users ON users.user_id = booking_details.user_id
                WHERE users.ndnc=0 AND booking_alternate_contact_no REGEXP '^[6-9]{1}[0-9]{9}$'
                AND partners.is_sms_allowed = '1'
                AND booking_details.type = 'Query' AND booking_details.current_status = '" . _247AROUND_CANCELLED . "' AND DAY(closed_date) = DAY(CURDATE())";

        $query = $this->db->query($sql);
        return $query->result_array();
    }

    /**
     * @desc: This function is used get the user phone number which booking does not 
     * exist in booking_details table
     * @param: void();
     * @retun:array();
     */
    function get_user_booking_not_exist_phn_number() {
        $sql = "SELECT users.user_id, users.phone_number as phn_number,'no_status' as 'current_status'
                FROM users LEFT JOIN booking_details ON users.user_id = booking_details.user_id
                WHERE users.ndnc=0 AND booking_details.user_id IS NULL AND users.phone_number REGEXP '^[6-9]{1}[0-9]{9}$'
                UNION
                SELECT users.user_id, users.alternate_phone_number as phn_number,'no_status' as 'current_status'
                FROM users LEFT JOIN booking_details ON users.user_id = booking_details.user_id 
                WHERE users.ndnc=0 AND booking_details.user_id IS NULL AND users.alternate_phone_number REGEXP '^[6-9]{1}[0-9]{9}$'";

        $query = $this->db->query($sql);
        return $query->result_array();
    }

    /**
     * @desc: This function is used get the all user phone number for all cases
     * @param: void();
     * @retun:array();
     */
    function get_all_user_booking_phn_number() {
        //get the data
        $completed_cancelled = "current_status = '" . _247AROUND_COMPLETED . "' OR current_status = '" . _247AROUND_CANCELLED . "' ";
        $completed_cancelled_data = $this->get_completed_cancelled_booking_user_phn_number($completed_cancelled);
        $cancelled_query_data = $this->get_cancelled_query_booking_user_phn_number();
        $not_exist_booking_data = $this->get_user_booking_not_exist_phn_number();

        //merge data to form an array BIG_MAINDATA
        $this->BIG_MAINDATA = array_merge($completed_cancelled_data, $cancelled_query_data);

        //get the unique phone number from BIG_MAINDATA
        $unique_phn_number = array_unique(array_column($this->BIG_MAINDATA, 'phn_number'));

        $serach_phn_number = array();
        $i = 0;
        foreach ($unique_phn_number as $value) {
            $serach_phn_number[$i] = $this->search_phn_number_index($value);
            $i++;
        }
        $this->BIG_MAINDATA = array();

        //make a final data array to return
        $final_unique_phn_number_data = array_merge($serach_phn_number, $not_exist_booking_data);
        return $final_unique_phn_number_data;
    }

    /**
     * @desc: This function is used get the only unique number of all user and bookings
     * @param: $value_to_search string
     * @retun: array();
     */
    function search_phn_number_index($value_to_search) {

        $temp_arr = array();
        $return_arr = array();
        $i = 0;

        //get the index of those number which exist more than 1 times
        foreach ($this->BIG_MAINDATA as $key => $val) {
            if ($val['phn_number'] === $value_to_search) {
                $temp_arr[$i] = $key;
                $i++;
            }
        }

        $com = false;
        $can = false;
        $q_can = false;
        foreach ($temp_arr as $val) {
            if ($this->BIG_MAINDATA[$val]['current_status'] === 'Completed') {
                $com = $val;
            }
            if ($this->BIG_MAINDATA[$val]['current_status'] === 'Cancelled') {
                $can = $val;
            }
            if ($this->BIG_MAINDATA[$val]['current_status'] === 'Query') {
                $q_can = $val;
            }
        }

        // return data based on the booking status
        if ($com) {
            $key = $com;
        } else if ($can) {
            $key = $can;
        } else if ($q_can) {
            $key = $q_can;
        }

        $return_arr['phn_number'] = $this->BIG_MAINDATA[$key]['phn_number'];
        $return_arr['current_status'] = $this->BIG_MAINDATA[$key]['current_status'];
        $return_arr['user_id'] = $this->BIG_MAINDATA[$key]['user_id'];

        return $return_arr;
    }

    function get_status_changes_booking_with_in_hour($hour) {

        $sql = "SELECT DISTINCT bd.order_id, bd.partner_current_status, bd.booking_date, cancellation_reason, amount_paid"
                . " FROM booking_details as bd, "
                . " booking_state_change as bs WHERE "
                . " replace('Q-','',bd.booking_id) =  replace('Q-','',bs.booking_id) "
                . " AND bd.update_date >= DATE_ADD(NOW(), INTERVAL -$hour HOUR) AND bd.partner_id = '" . JEEVES_ID . "'"
                . " AND old_state IN ('" . _247AROUND_COMPLETED . "', '" . _247AROUND_FOLLOWUP . "', "
                . " '" . _247AROUND_PENDING . "', '" . _247AROUND_CANCELLED . "', '" . _247AROUND_NEW_QUERY . "', "
                . " '" . _247AROUND_NEW_BOOKING . "', 'Rescheduled') "
                . " AND new_state IN ('" . _247AROUND_COMPLETED . "', '" . _247AROUND_FOLLOWUP . "', "
                . " '" . _247AROUND_PENDING . "', '" . _247AROUND_CANCELLED . "', '" . _247AROUND_NEW_QUERY . "', "
                . " '" . _247AROUND_NEW_BOOKING . "', 'Rescheduled')  AND new_state != old_state ";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    /**
     * @desc: This function is used get phone number for those user whom booking 
     * is completed but rating is not taken yet 
     * @param: $from string
     * @param $to string
     * @retun:array();
     */
    function get_data_for_bookings_without_rating($from, $to) {
        $where = "";
        if ($from !== "" && $to !== "") {
            $from = date('Y-m-d', strtotime('-1 day', strtotime($from)));
            $to = date('Y-m-d', strtotime('+1 day', strtotime($to)));
            $where = "AND closed_date > '$from' AND closed_date < '$to'";
        }
        $sql = "SELECT DISTINCT booking_primary_contact_no as phn_number,user_id,booking_id
                FROM booking_details 
                WHERE booking_primary_contact_no REGEXP '^[6-9]{1}[0-9]{9}$' 
                AND rating_stars IS NULL AND current_status= '" . _247AROUND_COMPLETED . "' $where
                UNION
                SELECT DISTINCT booking_alternate_contact_no as phn_number,user_id,booking_id
                FROM booking_details 
                WHERE booking_alternate_contact_no REGEXP '^[6-9]{1}[0-9]{9}$' 
                AND rating_stars IS NULL AND current_status= '" . _247AROUND_COMPLETED . "' $where";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    /**
     * @desc: This function is used to send SMS to those users who did bot give missed call
     * after sending completed rating sms and rating is also null
     * @param:void
     * @retun:array()
     */
    function get_missed_call_data_without_rating() {
        $date = date('Y-m-d', strtotime('-3 day'));
        $sql = "SELECT bd.booking_primary_contact_no as phn_number,bd.booking_id,bd.user_id
                FROM booking_details as bd 
                WHERE bd.current_status = 'completed' 
                AND bd.rating_stars IS Null 
                AND bd.closed_date LIKE '%$date%' 
                AND bd.booking_primary_contact_no NOT IN (SELECT rp.from_number FROM rating_passthru_misscall_log as rp
                WHERE rp.create_date >= bd.closed_date)";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    /**
     * @desc This is used to get SC email address with comma sepreated for active and not update GST form  
     * @return Array
     */
    function get_vendor_email_contact_no() {
        $sql1 = "SELECT  GROUP_CONCAT(DISTINCT primary_contact_email,  ',', owner_email ) AS email,GROUP_CONCAT(id) as id  "
                . " FROM  `service_centres` WHERE is_gst_doc IS NULL "
                . " AND active = 1 ";
        //$sql2 =  "SELECT  GROUP_CONCAT(DISTINCT owner_phone_1 ) AS email FROM  `service_centres` ";
        $query1 = $this->db->query($sql1);
        // $query2 = $this->db->query($sql2);

        return array(
            'email' => $query1->result_array()[0]['email'],
            'id' => $query1->result_array()[0]['id']
                //'phone' => $query2->return_array()[0],
        );
    }

    function get_non_verified_appliance_description_data() {
        $this->db->select('*');
        $this->db->where('is_verified', 0);
        $query = $this->db->get('appliance_product_description');
        return $query->result_array();
    }

    /*
     * 
     */

    function get_all_promotional_active_user_phone_number() {
        $this->db->select('*');
        $this->db->where('ndnc', 0);
        $query = $this->db->get('users');
        return $query->result_array();
    }

    /**
     * @desc     Insert Buyback SVC Balance
     * @param    $data array
     * @return   insert_id 
     */
    function add_bb_svc_balance($data) {
        $this->db->insert('bb_svc_balance', $data);
        return $this->db->insert_id();
    }

    /**
     * @desc     This function is used to get the data to read email attachment
     * @param    $where array
     * @return   $query array
     */
    function get_data_for_parsing_email_attachments($where) {
        $this->db->select('*');
        if (!empty($where)) {
            $this->db->where($where);
        }
        $this->db->from('email_attachment_parser');
        $query = $this->db->get();
        return $query->result_array();
    }

    function get_vendor_pincode_unavailable_queries_by_days($select, $days) {
        $sql = "SELECT" . $select . "FROM booking_details WHERE current_status='" . _247AROUND_FOLLOWUP . "' AND DATEDIFF(CURDATE(),date(create_date))>" . $days . " AND "
                . "NOT EXISTS (SELECT 1
                                FROM (vendor_pincode_mapping)
                                JOIN service_centres ON service_centres.id = vendor_pincode_mapping.Vendor_ID
                                WHERE vendor_pincode_mapping.Appliance_ID = booking_details.service_id
                                AND vendor_pincode_mapping.Pincode = booking_details.booking_pincode
                                AND service_centres.active = '1' AND service_centres.on_off = '1') AND booking_details.booking_pincode != ' ' AND booking_details.booking_pincode IS NOT NULL";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    /**
     * @desc:This method is used to get all spare parts which are pending on service center to return for more than 45 days.
     * @author Ankit Rajvanshi
     * @return type Array
     */
    function get_spares_pending_for_more_than_45_days_after_shipment() {
        $sql = "SELECT
                    spare_parts_details.booking_id,
                    spare_parts_details.id,
                    spare_parts_details.service_center_id,
                    spare_parts_details.consumed_part_status_id,
                    DATEDIFF(CURDATE(), spare_parts_details.shipped_date) as days
                FROM
                    spare_parts_details
                    JOIN booking_details ON (spare_parts_details.booking_id = booking_details.booking_id)
                WHERE
                    DATEDIFF(CURDATE(), spare_parts_details.shipped_date) >= ".SPARE_PARTS_OOT_DAYS." 
                    and spare_parts_details.shipped_date is not null
                    and booking_details.service_center_closed_date is null 
                    and spare_parts_details.defective_part_shipped_date is null
                    and spare_parts_details.is_micro_wh != 1
                    and spare_parts_details.defective_part_required = 1
                    and spare_parts_details.part_warranty_status = " . SPARE_PART_IN_WARRANTY_STATUS . " 
                    and spare_parts_details.status NOT IN ('" . _247AROUND_CANCELLED . "', '" . OK_PART_TO_BE_SHIPPED . "','" . DEFECTIVE_PARTS_PENDING . "')
                    and (spare_parts_details.consumed_part_status_id is null or spare_parts_details.consumed_part_status_id != 2);";

        return $this->db->query($sql)->result_array();
    }

    /*  Save CRON LOG IN DB Abhishek Awasthi */

    function save_cron_log($data) {
        $this->db->insert('cron_logs', $data);
        return $this->db->insert_id();
    }

    /**
     * Method returns those parts whose challan not generated.
     * @author Ankit Rajvanshi
     * @return type
     */
    function generate_challan_of_to_be_shipped_parts() {

        $sql = "SELECT
                    id, service_center_id, status, sf_challan_file
                FROM
                    `spare_parts_details`
               WHERE
                    defective_part_required = 1
                    and STATUS IN('" . DEFECTIVE_PARTS_PENDING . "', '" . OK_PART_TO_BE_SHIPPED . "')
                    and sf_challan_file IS NULL";
        return $this->db->query($sql)->result_array();
    }

}
