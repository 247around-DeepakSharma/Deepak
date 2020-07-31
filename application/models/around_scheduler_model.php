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
    
    function get_status_changes_booking_with_in_hour($hour){
       
        $sql  = "SELECT DISTINCT bd.order_id, bd.partner_current_status, bd.booking_date, booking_cancellation_reasons.reason as cancellation_reason, amount_paid"
                . " FROM booking_details as bd, "
                . " booking_state_change as bs "
                . " LEFT JOIN booking_cancellation_reasons ON (bd.cancellation_reason = booking_cancellation_reasons.id) WHERE "
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
    
    /**
     * @desc : Get all spares which are pending now for greater than 15 days after booking completion.
     * @author : Ankit Rajvanshi
     */
    function get_parts_to_be_billed_pending_for_15_days() {

        $sql = "SELECT
                    booking_details.booking_id,
                    spare_parts_details.id,
                    parts_shipped,
                    inventory_master_list.part_number,
                    challan_approx_value
                FROM
                    spare_parts_details
                    JOIN booking_details ON (spare_parts_details.booking_id = booking_details.booking_id)
                    JOIN inventory_master_list ON(spare_parts_details.shipped_inventory_id = inventory_master_list.inventory_id)
                WHERE
                    DATEDIFF(CURDATE(), booking_details.service_center_closed_date) > ".SF_SPARE_OOT_DAYS." 
                    AND spare_parts_details.defective_part_required = 1
                    AND spare_parts_details.status IN ('" . DEFECTIVE_PARTS_PENDING . "', '" . DEFECTIVE_PARTS_REJECTED_BY_WAREHOUSE . "', '" . OK_PART_TO_BE_SHIPPED . "', '" . OK_PARTS_REJECTED_BY_WAREHOUSE . "')
                    AND spare_parts_details.consumed_part_status_id is not null ";
        
        $sql .= " UNION ";
        
        $sql .= "SELECT
                    booking_details.booking_id,
                    spare_parts_details.id,
                    parts_shipped,
                    inventory_master_list.part_number,
                    challan_approx_value
                FROM
                    spare_parts_details
                    JOIN booking_details ON (spare_parts_details.booking_id = booking_details.booking_id)
                    JOIN inventory_master_list ON(spare_parts_details.shipped_inventory_id = inventory_master_list.inventory_id)
                WHERE
                    DATEDIFF(CURDATE(), spare_parts_details.shipped_date) >= ".SPARE_PARTS_OOT_DAYS." 
                    and spare_parts_details.shipped_date is not null
                    and booking_details.service_center_closed_date is null 
                    and spare_parts_details.defective_part_shipped_date is null
                    and spare_parts_details.is_micro_wh != 1
                    and spare_parts_details.defective_part_required = 1
                    and spare_parts_details.part_warranty_status = ".SPARE_PART_IN_WARRANTY_STATUS." 
                    and spare_parts_details.status NOT IN ('"._247AROUND_CANCELLED."', '".OK_PART_TO_BE_SHIPPED."','".DEFECTIVE_PARTS_PENDING."')
                    and (spare_parts_details.consumed_part_status_id is null or spare_parts_details.consumed_part_status_id != 2);";
        
        $data = $this->db->query($sql)->result_array();        
        
        if(!empty($data)) {
            $table = "<table border='1' style='border-collapse:collapse'>";
            $table .= "<thead><tr>";
            $table .= "<th>S. No.</th>";
            $table .= "<th>Booking ID</th>";
            $table .= "<th>Spare ID</th>";
            $table .= "<th>Part Name.</th>";
            $table .= "<th>Part Number</th>";
            $table .= "<th>Amount</th>";
            $table .= "</tr></thead>";
            
            $table .= "<tbody>";
            foreach($data as $key => $spare_data) {
                $table .= "<tr>";
                $table .= "<td>".++$key."</td>";
                $table .= "<td>".$spare_data['booking_id']."</td>";
                $table .= "<td>".$spare_data['id']."</td>";
                $table .= "<td>".$spare_data['parts_shipped']."</td>";
                $table .= "<td>".$spare_data['part_number']."</td>";
                $table .= "<td>".$spare_data['challan_approx_value']."</td>";
                $table .= "</tr>";
            }
            $table .= "</tbody>";
            $table .= "</table>";
        }
        
        return $table;
    }

    /* Get SF Owner's details who did not recieve agreement email for signing agreement
     * @param $sf_email
     * @return Array
     */

//    function get_sf_details($sf_email = NULL, $email_sent = 0, $reminder = 0, $reminder_date = NULL) {
//        $sql = 'select service_centres.*,e1.full_name as rm_full_name,e1.official_email as rm_email,e2.full_name as asm_full_name,e2.official_email as asm_email from service_centres '
//                . 'LEFT JOIN employee as e1 on e1.id = service_centres.rm_id '
//                . 'LEFT JOIN employee as e2 on e2.id = service_centres.asm_id ';
//        if ($reminder == 0) {
//            if (!is_null($sf_email)) {
//                $sql .= " where service_centres.owner_email = '" . $sf_email . "' and service_centres.active = 1 and service_centres.is_sf = 1 and service_centres.agreement_email_sent = $email_sent and service_centres.is_sf_agreement_signed = 0";
//            } else {
//                $sql .= ' where service_centres.active = 1 and service_centres.is_sf = 1 and service_centres.is_sf_agreement_signed = 0 and service_centres.agreement_email_sent = ' . $email_sent;
//            }
//        } else if ($sf_email == NULL && $reminder == 1 && $reminder_date != NULL && $reminder_date != '0000-00-00') {
//            $sql .= " where service_centres.active = 1 and service_centres.is_sf = 1 and service_centres.agreement_email_sent = 1 and service_centres.is_sf_agreement_signed = 0 and service_centres.agreement_email_sent = 1 and agreement_email_reminder_date = '$reminder_date'";
//        }
//        $query = $this->db->query($sql);
//        return $query->result_array();
//    }
    
    function get_sf_details($sf_id) {
        $sql = 'select service_centres.*,e1.full_name as rm_full_name,e1.official_email as rm_email,e2.full_name as asm_full_name,e2.official_email as asm_email from service_centres '
                . 'LEFT JOIN employee as e1 on e1.id = service_centres.rm_id '
                . 'LEFT JOIN employee as e2 on e2.id = service_centres.asm_id ';
       
        $sql .= " where service_centres.id = '" . $sf_id . "' and service_centres.active = 1 and service_centres.is_sf = 1";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    /**
     * Method reassign booking to new service_center.
     * @param type $booking_id
     * @param type $new_service_center_id
     * @param type $remarks
     */
    function vendor_reassignment_process($booking_id, $new_service_center_id, $reason, $is_rm_responsible, $remarks = NULL) {
        
        $spare_data = $this->inventory_model->get_spare_parts_details("id, status,partner_id,service_center_id,shipped_inventory_id,shipped_quantity,booking_id,parts_shipped", array("booking_id"=>$booking_id, "status != '"._247AROUND_CANCELLED."'" => NULL));
        
        $service_center_id = $new_service_center_id;
        $remarks = $remarks;
        
        $select = "service_center_booking_action.id, service_center_booking_action.booking_id, service_center_booking_action.current_status,service_center_booking_action.internal_status";
        $where = array("service_center_booking_action.booking_id"=>$booking_id);
        $booking_action_details = $this->vendor_model->get_service_center_booking_action_details($select, $where);
        $previous_sf_id = $this->reusable_model->get_search_query('booking_details','booking_details.assigned_vendor_id,booking_details.id, booking_details.partner_id, booking_details.request_type',array('booking_id'=>$booking_id),NULL,NULL,NULL,NULL,NULL)->result_array();
//            if (IS_DEFAULT_ENGINEER == TRUE) {
//                $b['assigned_engineer_id'] = DEFAULT_ENGINEER;
//            } else {
//                $engineer = $this->vendor_model->get_engineers($service_center_id);
//                if (!empty($engineer)) {
//                    $b['assigned_engineer_id'] = $engineer[0]['id'];
//                }
//            }
        //Assign service centre and engineer
        $assigned_data = array('assigned_vendor_id' => $service_center_id,
            'assigned_engineer_id' => NULL,
            'is_upcountry' => 0,
            'upcountry_pincode' => NULL,
            'sub_vendor_id' => NULL,
            'sf_upcountry_rate' => NULL,
            'partner_upcountry_rate' => NULL,
            'is_penalty' => 0,
            'upcountry_partner_approved' => 1,
            'upcountry_paid_by_customer' => 0,
            'service_center_closed_date' => NULL,
            'cancellation_reason' => NULL,
            'upcountry_distance' => NULL,
            'internal_status' => _247AROUND_PENDING);

        $actor = $next_action = 'not_define';
        if(empty($spare_data)){
            $partner_status = $this->booking_utilities->get_partner_status_mapping_data(_247AROUND_PENDING, ASSIGNED_VENDOR, $previous_sf_id[0]['partner_id'], $booking_id);

            if (!empty($partner_status)) {
                $assigned_data['partner_current_status'] = $partner_status[0];
                $assigned_data['partner_internal_status'] = $partner_status[1];
                $actor = $assigned_data['actor'] = $partner_status[2];
                $next_action = $assigned_data['next_action'] = $partner_status[3];
            }
        }

        $this->booking_model->update_booking($booking_id, $assigned_data);

        $this->vendor_model->delete_previous_service_center_action($booking_id);
        $unit_details = $this->booking_model->getunit_details($booking_id);

        $this->engineer_model->delete_booking_from_engineer_table($booking_id);

        $vendor_data = $this->vendor_model->getVendorDetails("isEngineerApp", array("id" =>$service_center_id, "isEngineerApp" => 1));

        $curr_status = (!empty($booking_action_details[0]['current_status'])?$booking_action_details[0]['current_status']:'Pending');
        $internal_status = (!empty($booking_action_details[0]['internal_status'])?$booking_action_details[0]['internal_status']:'Pending');

        if(($curr_status === 'InProcess') && (($internal_status === 'Completed') || ($internal_status === 'Cancelled'))) {
            $internal_status = 'Pending';
            $curr_status = 'Pending';
        }

        foreach ($unit_details[0]['quantity'] as $value) {

            $data['current_status'] = $curr_status;
            $data['internal_status'] = $internal_status;
            $data['service_center_id'] = $service_center_id;
            $data['booking_id'] = $booking_id;
            $data['create_date'] = date('Y-m-d H:i:s');
            $data['update_date'] = date('Y-m-d H:i:s');
            $data['unit_details_id'] = $value['unit_id'];
            $this->vendor_model->insert_service_center_action($data);

            if(!empty($vendor_data)){
                $engineer_action['unit_details_id'] = $value['unit_id'];
                $engineer_action['service_center_id'] = $service_center_id;
                $engineer_action['booking_id'] = $booking_id;
                $engineer_action['current_status'] = _247AROUND_PENDING;
                $engineer_action['internal_status'] = _247AROUND_PENDING;
                $engineer_action["create_date"] = date("Y-m-d H:i:s");

                $enID = $this->engineer_model->insert_engineer_action($engineer_action);
                if(!$enID){
                     $this->notify->sendEmail(NOREPLY_EMAIL_ID, DEVELOPER_EMAIL, "", "", 
                        "BUG in Enginner Table ". $booking_id, "SF Assigned but Action table not updated", "",SF_ASSIGNED_ACTION_TABLE_NOT_UPDATED, "", $booking_id);
                }
            }

            /* update inventory stock for reassign sf
             * First increase stock for the previous sf and after that decrease stock 
             * for the new assigned sf
             */
            $inventory_data = array();
            $inventory_data['receiver_entity_type'] = _247AROUND_SF_STRING;
            $inventory_data['booking_id'] = $booking_id;
            $inventory_data['agent_id'] = $this->session->userdata('id');
            $inventory_data['agent_type'] = _247AROUND_EMPLOYEE_STRING;
            if ($value['price_tags'] == _247AROUND_WALL_MOUNT__PRICE_TAG) {
                $match = array();
                preg_match('/[0-9]+/', $unit_details[0]['capacity'], $match);
                if (!empty($match)) {
                    if ($match[0] <= 32) {
                        $inventory_data['part_number'] = LESS_THAN_32_BRACKETS_PART_NUMBER;
                    } else if ($match[0] > 32) {
                        $inventory_data['part_number'] = GREATER_THAN_32_BRACKETS_PART_NUMBER;
                    }

                    //increase stock for previous assigned vendor
                    $inventory_data['receiver_entity_id'] = $previous_sf_id[0]['assigned_vendor_id'];
                    $inventory_data['stock'] = 1 ;
                    $this->miscelleneous->process_inventory_stocks($inventory_data);
                    //decrease stock for new assigned vendor
                    $inventory_data['receiver_entity_id'] = $service_center_id;
                    $inventory_data['stock'] = -1 ;
                    $this->miscelleneous->process_inventory_stocks($inventory_data);
                }
            }
        }
        $rm_responsible = $is_rm_responsible;
        $reason = $reason;
        $reason_row =  $this->vendor_model->getReassignReason("*",array('id'=>$reason));
        $str_reason = !empty($reason_row[0]->reason) ? $reason_row[0]->reason : "";
        $this->notify->insert_state_change($booking_id, RE_ASSIGNED_VENDOR, ASSIGNED_VENDOR, "Re-Assigned SF ID: " . $service_center_id . " ". $remarks." - ".$str_reason, $this->session->userdata('id'), 
                $this->session->userdata('employee_id'), $actor,$next_action, _247AROUND);

        foreach($spare_data as $spare){

            if($spare['service_center_id']==$spare['partner_id']){

                 $in['receiver_entity_id'] = $previous_sf_id[0]['assigned_vendor_id'];
                 $in['receiver_entity_type'] = _247AROUND_SF_STRING;
                 $in['sender_entity_id'] = $previous_sf_id[0]['assigned_vendor_id'];
                 $in['sender_entity_type'] = _247AROUND_SF_STRING;
                 $in['stock'] = $spare['shipped_quantity'];
                 $in['booking_id'] = $spare['booking_id'];
                 $in['agent_id'] = $this->session->userdata('id');
                 $in['agent_type'] = _247AROUND_EMPLOYEE_STRING;
                 $in['is_wh'] = TRUE;
                 $in['inventory_id'] = $spare['shipped_inventory_id'];
                 $this->miscelleneous->process_inventory_stocks($in);
                $sp['status'] = SPARE_PARTS_CANCELLED;
                $sp['consumed_part_status_id'] = NULL;
                $sp['consumption_remarks'] = NULL;
                $this->service_centers_model->update_spare_parts(array('id' => $spare['id']), $sp);
                $tracking_details = array('spare_id' => $spare['id'], 'action' => "Spare Part Cancelled", 'remarks' => "Booking Reassign - Micro Stock In", 'agent_id' => $this->session->userdata("id"), 'entity_id' => _247AROUND, 'entity_type' => _247AROUND_EMPLOYEE_STRING);
                $this->service_centers_model->insert_spare_tracking_details($tracking_details);



            }else{
                if(isset($rm_responsible) && !empty($rm_responsible)){
                 $sp['service_center_id'] = $service_center_id;
                $this->service_centers_model->update_spare_parts(array('id' => $spare['id']), $sp);
                        $tracking_details = array('spare_id' => $spare['id'], 'action' => "Spare Part Reassign", 'remarks' => "Booking Reassign - Part Reassign", 'agent_id' => $this->session->userdata("id"), 'entity_id' => _247AROUND, 'entity_type' => _247AROUND_EMPLOYEE_STRING);
                $this->service_centers_model->insert_spare_tracking_details($tracking_details);


                }else{

                if(!empty($spare['parts_shipped'])){
                $sp['service_center_id'] = $previous_sf_id[0]['assigned_vendor_id'];
                $sp['status'] = OK_PART_TO_BE_SHIPPED;
                $sp['consumed_part_status_id'] = 5;
                $sp['consumption_remarks'] = OK_PART_TO_BE_SHIPPED;
                }else{
                $sp['status'] = _247AROUND_CANCELLED;
                $sp['consumed_part_status_id'] = NULL;
                $sp['consumption_remarks'] = NULL;
                }
                $this->service_centers_model->update_spare_parts(array('id' => $spare['id']), $sp);
                        $tracking_details = array('spare_id' => $spare['id'], 'action' => OK_PART_TO_BE_SHIPPED, 'remarks' => "Booking Reassign - ".OK_PART_TO_BE_SHIPPED, 'agent_id' => $this->session->userdata("id"), 'entity_id' => _247AROUND, 'entity_type' => _247AROUND_EMPLOYEE_STRING);
                $this->service_centers_model->insert_spare_tracking_details($tracking_details);


                }

            }  


        }

        $reassign= array(
            'booking_details_id'=>$previous_sf_id[0]['id'],
            'reason'=>$reason,
            'remark'=>$remarks,
            'old_sf'=>$previous_sf_id[0]['assigned_vendor_id'],
            'new_sf'=>$service_center_id,
            'rm_responsible_flag'=>$rm_responsible,
            'create_date'=>date("d-m-Y h:i:s")
        );
        $this->vendor_model->saveReassignVendor($reassign);

       $default_id =_247AROUND_DEFAULT_AGENT;
       $defaultagent_name =_247AROUND_DEFAULT_AGENT_NAME ; 
       if (!empty($this->session->userdata('id'))  &&  !empty($this->session->userdata('employee_id'))) {
            $default_id =$this->session->userdata('id');
            $defaultagent_name =$this->session->userdata('employee_id') ; 
       }

        //Mark Upcountry & Create Job Card
        $url = base_url() . "employee/vendor/mark_upcountry_booking/" . $booking_id . "/" . $default_id
                . "/" . $defaultagent_name;

        $async_data['data'] = array();
        $this->asynchronous_lib->do_background_process($url, $async_data);

        $this->booking_utilities->lib_send_mail_to_vendor($booking_id, "");

        log_message('info', "Reassigned - Booking id: " . $booking_id . "  By " .
                $this->session->userdata('employee_id') . " service center id " . $service_center_id);

        //Send sms to customer for new service center address if request type is repair service center visit 
        if ($previous_sf_id[0]['request_type'] == HOME_THEATER_REPAIR_SERVICE_TAG || $previous_sf_id[0]['request_type'] == HOME_THEATER_REPAIR_SERVICE_TAG_OUT_OF_WARRANTY) {
            $query = $this->booking_model->getbooking_history($booking_id, "1");
            $services = $unit_details[0]['brand'] . " " . $query[0]['services'];
            $sf_phone = $query[0]['phone_1'] . ", " . $query[0]['primary_contact_phone_1'] . ", " . $query[0]['owner_phone_1'];
            $sf_address = $query[0]['address'].", ".$query[0]['sf_district'];
            $this->miscelleneous->sms_sf_address_to_customer($services, $sf_phone, $sf_address, $query[0]['booking_id'], $query[0]['user_id'],  $query[0]['booking_primary_contact_no']);
        }
        //End
        
        return true;
    }
}

