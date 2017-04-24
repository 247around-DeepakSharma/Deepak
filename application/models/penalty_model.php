<?php

class Penalty_model extends CI_Model {

    /**
     * @desc load both db
     */
    function __construct() {
	parent::__Construct();

	$this->db = $this->load->database('default', TRUE, TRUE);
    }
    /**
     * @desc: This is
     */
    function penalty_on_service_center_for_assigned_engineer() {
//	log_message('info', __FUNCTION__);
//	$sql = "SELECT distinct(BD.booking_id), assigned_vendor_id, BD.partner_id, assigned_engineer_id, "
//	    . " SC.create_date, BD.booking_date FROM booking_details as BD,  "
//	    . " service_center_booking_action as SC, service_centres as SCS "
//	    . " WHERE BD.assigned_vendor_id IS NOT NUll "
//	    . " AND (BD.current_status='Pending' OR BD.current_status='Rescheduled') "
//	    . " AND SC.booking_id = BD.booking_id "
//	    . " AND SC.service_center_id = BD. assigned_vendor_id "
//	    . " AND (SC.current_status='Pending' OR SC.current_status='InProcess') "
//            . " AND SCS.id = SC.service_center_id "
//            . " AND SCS.is_update = 1 ";
//
//	$query = $this->db->query($sql);
//	$assigned_engineer = $query->result_array();
//
//	foreach ($assigned_engineer as $value) {
//	    $engineer = $this->check_engineer_assigned($value['booking_id'], $value['assigned_vendor_id']);
//	    $date_1 = date_create(date('Y-m-d ', strtotime($value['booking_date'])));
//	    $date_2 = date_create(date('Y-m-d', strtotime($value['create_date'])));
//
//	    $date_diff = date_diff($date_1, $date_2);
//           
//            if ($date_diff->days  == 1){
//                if(date('H', strtotime($value['create_date'])) <18 ){
//                    log_message('info', __FUNCTION__ . " Days = 1");
//                    if (empty($engineer)) {
//                        log_message('info', __FUNCTION__ . " Engineer is not assign");
//                        // If engineer is not assign till 2 PM then service center will pay penalty
//                        // Current Time is greater than 12 PM
//                        if (date('H') > Max_TIME_TO_BE_ASSIGNED_ENGINEER) {
//                            log_message('info', __FUNCTION__ . " Current Time is greater than 12 PM");
//                            $where = array('partner_id' => $value['partner_id'], 'criteria' => ENGG_NOT_ASSIGN, 'active' => '1');
//                            $this->get_data_penalty_on_booking($value, $where);
//                        }
//                    } else {
//                        log_message('info', __FUNCTION__ . " Engineer assigned");
//                        log_message('info', __FUNCTION__ . " Assigned Engineer Time " . date('H', strtotime($engineer['create_date'])));
//                        if (date('H', strtotime($engineer['create_date'])) > Max_TIME_TO_BE_ASSIGNED_ENGINEER) {
//                            $where = array('partner_id' => $value['partner_id'], 'criteria' => ENGG_LATE_ASSIGN, 'active' => '1');
//                            $this->get_data_penalty_on_booking($value, $where);
//                        }
//                    }
//                    
//                }
//                
//            } else if ($date_diff->days > 1) {
//		log_message('info', __FUNCTION__ . " Days > 1");
//		if (empty($engineer)) {
//		    log_message('info', __FUNCTION__ . " Engineer is not assign");
//		    // If engineer is not assign till 2 PM then service center will pay penalty
//		    // Current Time is greater than 12 PM
//		    if (date('H') > Max_TIME_TO_BE_ASSIGNED_ENGINEER) {
//			log_message('info', __FUNCTION__ . " Current Time is greater than 12 PM");
//			$where = array('partner_id' => $value['partner_id'], 'criteria' => ENGG_NOT_ASSIGN, 'active' => '1');
//			$this->get_data_penalty_on_booking($value, $where);
//		    }
//		} else {
//		    log_message('info', __FUNCTION__ . " Engineer assigned");
//		    log_message('info', __FUNCTION__ . " Assigned Engineer Time " . date('H', strtotime($engineer['create_date'])));
//		    if (date('H', strtotime($engineer['create_date'])) > Max_TIME_TO_BE_ASSIGNED_ENGINEER) {
//			$where = array('partner_id' => $value['partner_id'], 'criteria' => ENGG_LATE_ASSIGN, 'active' => '1');
//			$this->get_data_penalty_on_booking($value, $where);
//		    }
//		}
//	    } else if ($date_diff->days == 0) {
//		// Assigned Engineer for same day booking
//		log_message('info', __FUNCTION__ . " Days == 0");
//		$date3 = date('H', strtotime($value['create_date']));
//
//		if (10 >= $date3) {
//		    // Assgined Engineer befor 10AM.
//		    // Service center will not assigned till 2PM, then they will pay penalty
//		    log_message('info', __FUNCTION__ . " Assgined Engineer till 10 AM");
//		    if (empty($engineer)) {
//			log_message('info', __FUNCTION__ . " Engineer is not assign");
//			if (date('H') > Max_TIME_TO_BE_ASSIGNED_ENGINEER) {
//			    $where = array('partner_id' => $value['partner_id'], 'criteria' => ENGG_NOT_ASSIGN, 'active' => '1');
//			    $this->get_data_penalty_on_booking($value, $where);
//			}
//		    } else {
//			log_message('info', __FUNCTION__ . " Engineer assigned");
//			$date4 = date('H', strtotime($engineer['create_date']));
//			if ($date4 > Max_TIME_TO_BE_ASSIGNED_ENGINEER) {
//			    $where = array('partner_id' => $value['partner_id'], 'criteria' => ENGG_LATE_ASSIGN, 'active' => '1');
//			    $this->get_data_penalty_on_booking($value, $where);
//			}
//		    }
//		} else {
//		    log_message('info', __FUNCTION__ . " Assgined Engineer after 10 AM");
//		    // Assgined Engineer after 10AM.
//		    // Service centers need to be assign engineer in the next 4 hours from assigned time
//		    $date5 = date('H', strtotime($value['create_date'] . " +".Max_TIME_WITH_IN_ASSIGNED_ENGINEER." hours"));
//		    if (empty($engineer)) {
//			log_message('info', __FUNCTION__ . " Engineer is not assign");
//			if (date('H') > $date5) {
//			    $where = array('partner_id' => $value['partner_id'], 'criteria' => ENGG_NOT_ASSIGN, 'active' => '1');
//			    $this->get_data_penalty_on_booking($value, $where);
//			}
//		    } else {
//			log_message('info', __FUNCTION__ . " Engineer assigned");
//			$date4 = date('H', strtotime($engineer['create_date']));
//			if ($date4 > $date5) {
//			    $where = array('partner_id' => $value['partner_id'], 'criteria' => ENGG_LATE_ASSIGN, 'active' => '1');
//			    $this->get_data_penalty_on_booking($value, $where);
//			}
//		    }
//		}
//	    }
//	}
    }
    /**
     *
     * @param Array $value
     * @param Array $where
     */
    function get_data_penalty_on_booking($value, $where) {
	log_message('info', __FUNCTION__ . " value: " . print_r($value, TRUE) . " where: " . print_r($where, TRUE));
	$penalty_details = $this->get_penalty_details($where);
	if ($penalty_details) {
	    $data['booking_id'] = $value['booking_id'];
	    $data['service_center_id'] = $value['assigned_vendor_id'];
	    $data['agent_id'] = isset($value['agent_id']) && !empty($value['agent_id'])?$value['agent_id']:NULL;
	    $data['remarks'] = isset($value['remarks']) && !empty($value['remarks'])?$value['remarks']:NULL;
	    $data['criteria_id'] = $penalty_details['id'];
	    $data['penalty_amount'] = $penalty_details['penalty_amount'];
            $data['active'] = 1;
            $data['create_date'] = date('Y-m-d H:i:s');
            $this->insert_penalty_on_booking($data);
            if($data['criteria_id']  == '2')
            {
                $this->booking_model->update_booking($data['booking_id'],array('is_penalty'=> '1'));
            }
	    
            return $data;
	}else{
            log_message('info',__FUNCTION__.'Unable to get Penalty Details for provided values of where '.print_r($where,TRUE));
            return FALSE;

        }
    }
    /**
     *
     * @param String $booking_id
     * @param string $service_center_id
     * @return boolean
     */
    function check_engineer_assigned($booking_id, $service_center_id) {
	log_message('info', __FUNCTION__ . " booking_id: " . print_r($booking_id, true) . " Service center id: "
	    . print_r($service_center_id, true));
	$this->db->select('*');
	$this->db->where('booking_id', $booking_id);
	$this->db->where('service_center_id', $service_center_id);
	$this->db->where('current_state', ENGG_ASSIGNED);
	$query = $this->db->get('assigned_engineer');
	if ($query->num_rows > 0) {
	    return $query->result_array()[0];
	} else {
	    return false;
	}
    }
    /**
     *
     * @param Array $where
     * @return boolean
     */
    function get_penalty_details($where) {
	log_message('info', __FUNCTION__ . " Where: " . print_r($where, TRUE));
	$this->db->select('*');
	$this->db->where($where);
	$query = $this->db->get('penalty_details');
	if ($query->num_rows > 0) {
	    return $query->result_array()[0];
	} else {
	    return FALSE;
	}
    }
    /**
     *
     * @param Array $data
     * @return type
     */
    function insert_penalty_on_booking($data) {
	log_message('info', __FUNCTION__);
	$this->db->insert('penalty_on_booking', $data);
	return $this->db->insert_id();
    }
    /**
     * Applies penalty on SF for bookings which have not been updated today
     * This is triggered from CRON.
     * 
     * @return boolean
     */
    function penalty_on_service_center_for_update_booking() {
        log_message('info', __FUNCTION__);
        $sql = "SELECT SC.service_center_id AS assigned_vendor_id, CONCAT(  '', GROUP_CONCAT( (
                BD.booking_id
                ) ) ,  '' ) AS booking_group
                FROM service_center_booking_action AS SC, booking_details AS BD, service_centres AS SCS
                WHERE (
                SC.current_status =  'Pending'
                )
                AND SC.booking_id = BD.booking_id
                AND (
                DATEDIFF( 
                CURRENT_TIMESTAMP , STR_TO_DATE( BD.booking_date,  '%d-%m-%Y' ) ) >=0
                )
                AND SCS.id = SC.service_center_id
                AND SCS.is_update =1
                GROUP BY assigned_vendor_id
                ";
        $query = $this->db->query($sql);

        if ($query->num_rows > 0) {
            $result = $query->result_array();
            foreach ($result as $value) {
                $booking_id_array = explode(",", $value['group_booking_id']);
                $booking_not_update = 0;
                foreach ($booking_id_array as $booking_id) {
                    $data = $this->check_any_update_in_state_change($booking_id, $value['assigned_vendor_id']);
                    if (empty($data)) {
                        $data1['agent_id'] = _247AROUND_DEFAULT_AGENT;
                        $data1['remarks'] = 'Booking Not Updated On Time';
                        $where = array('criteria' => BOOKING_NOT_UPDATED_BY_SERVICE_CENTER, 'active' => '1');
                        $data1['booking_id'] = $booking_id;
                        $data1['assigned_vendor_id'] = $value['assigned_vendor_id'];
                        $this->get_data_penalty_on_booking($data1, $where);
                        $booking_not_update++;
                    }
                }

                $sc_crimes['service_center_id'] = $value['assigned_vendor_id'];
                $sc_crimes['engineer_not_assigned'] = 0;
                $sc_crimes['booking_not_updated'] = $booking_not_update;
                $sc_crimes['total_pending_booking'] = count($booking_id_array);
                $sc_crimes['total_missed_target'] = $booking_not_update;
                $sc_crimes['update_date'] = date('Y-m-d H:i:s');
                $sc_crimes['create_date'] = date('Y-m-d H:i:s');
                $this->db->insert('sc_crimes', $sc_crimes);
            }
        } else {
            return FALSE;
        }
    }

    function test(){
        $service_center_booking_action = array(
        array('assigned_vendor_id' => '1','group_booking_id' => 'SV-1109261704241,SC-72541703182,SS-1084451704091,SV-1070681704242,SC-72541703182,SC-1085021704103,SS-373171704223,SX-1109071704231,SY-1105681704211,SW-1106031704221,SF-1106291704221,SC-72541703182,SF-1106301704221,SS-156431609212,SC-72541703182,SV-1108801704231,SC-72541703182,SS-1098631704221,SS-1105951704211449,SC-72541703182,SS-1107901704232200,SC-72541703182,SS-1099341704221,SF-1106291704221,SC-72541703182,SC-72541703182,SY-1087081704081,SV-1108801704231,SC-72541703182,SZ-1105501704221,SC-72541703182,SS-1107901704232200,SS-1107621704232921,SC-72541703182,SS-1099341704222,SS-373171704222,SZ-1108611704231,SW-1106031704221,SC-72541703182,SW-1106041704221,SF-1106301704221,SS-156431609213,SC-72541703182'),
        array('assigned_vendor_id' => '3','group_booking_id' => 'SS-1108161704241,SS-1101241704231,SV-1107931704241,SW-129521704094,SM-1106271704221,SS-1099331704211,SS-1095781704141914,SF-1106311704231,SS-1102681704251,SV-1103711704201,SS-1099331704211,SF-1106311704231,SS-1102681704251,SV-1107931704241,SV-1105151704241,SZ-1105511704221,SY-1105841704211'),
        array('assigned_vendor_id' => '4','group_booking_id' => 'SP-1096151704151,SS-1103921704201720,SC-1100471704181,SS-1107041704222325,SM-1102431704191,SS-1109091704231227,SS-1106551704232229,SP-1096151704151,SS-1091541704132561'),
        array('assigned_vendor_id' => '5','group_booking_id' => 'SV-1105331704241,SC-12571704225,SY-1105541704211'),
        array('assigned_vendor_id' => '6','group_booking_id' => 'SM-1106261704221,SS-1105941704211743,SC-1105411704211,SS-1104141704261,SS-666911612011,SV-1104701704231,SS-1104141704261,SS-666911612011'),
        array('assigned_vendor_id' => '7','group_booking_id' => 'SS-1082021704041631,SA-986701704153,SW-1105641704211,SA-1102481704231,SW-1109031704231'),
        array('assigned_vendor_id' => '9','group_booking_id' => 'SS-1108881704221225'),
        array('assigned_vendor_id' => '10','group_booking_id' => 'SS-1108861704221234,SW-1089141704101,SS-1102651704213637,SS-1102651704214422'),
        array('assigned_vendor_id' => '11','group_booking_id' => 'SS-634101611272,SS-634101611272,SP-1106331704231,SP-1106331704231'),
        array('assigned_vendor_id' => '13','group_booking_id' => 'SS-644491612022,SV-351381704172,SV-1109221704241'),
        array('assigned_vendor_id' => '16','group_booking_id' => 'ST-1109311704241'),
        array('assigned_vendor_id' => '17','group_booking_id' => 'SS-1087551704181,SV-1105581704241,SS-1087551704181,SV-1109351704241,SV-1097351704161,SC-1102441704191,SS-1108831704221272,SV-1109351704241,SP-1090831704111'),
        array('assigned_vendor_id' => '40','group_booking_id' => 'SS-663041611301,SW-1050291704221,SS-492931611041,SW-1050291704221,SS-648861611281,SS-616321611191,SS-1108641704221999'),
        array('assigned_vendor_id' => '42','group_booking_id' => 'SS-345921704173262,SS-1090521704202406,SY-526491703043,SS-1103501704191778'),
        array('assigned_vendor_id' => '44','group_booking_id' => 'SV-1103681704201'),
        array('assigned_vendor_id' => '46','group_booking_id' => 'SS-1109011704231411,SW-1050281703131,SS-238991704212768,SS-1099311704211,SA-1050281704232,SW-1050281703131,SS-238991704212768'),
        array('assigned_vendor_id' => '49','group_booking_id' => 'SS-1102041704181633,SK-1101991704181,SW-1095451704131,SX-1105431704211,SX-1105441704211,SS-1099271704221,SS-1108651704221169'),
        array('assigned_vendor_id' => '52','group_booking_id' => 'SS-648571611282'),
        array('assigned_vendor_id' => '57','group_booking_id' => 'SS-645871611271,SY-641791611241,SS-645871611271,SY-658001612011,SS-644781611271'),
        array('assigned_vendor_id' => '58','group_booking_id' => 'SV-1109411704241,SX-1100781704181,SW-1090421704101,ST-1100521704171,SS-1099811704221,SC-1073341703311,SS-1098721704221,SV-1109411704241,SY-1103971704201,SS-1109121704231984'),
        array('assigned_vendor_id' => '63','group_booking_id' => 'SS-634291611241,SC-998491702101,SS-634291611241,SS-640011611281,SW-268431611142,SY-518051611073'),
        array('assigned_vendor_id' => '64','group_booking_id' => 'SS-1107381704241,SS-1107381704241'),
        array('assigned_vendor_id' => '65','group_booking_id' => 'SS-1098091704272,SP-1104721704211,SP-1097901704171,SP-1104721704211,SS-1094051704121768,SS-1094891704211'),
        array('assigned_vendor_id' => '68','group_booking_id' => 'SC-1105161704211,SS-1094871704211,SC-1101161704191'),
        array('assigned_vendor_id' => '70','group_booking_id' => 'SC-1105471704221,SS-1099151704211,SY-1104871704201,SY-1105651704211,SS-1098831704241'),
        array('assigned_vendor_id' => '71','group_booking_id' => 'SP-1090751704111,SS-1087421704131,SP-1090751704111,SY-1103421704191,SS-503891611292,SY-1108771704221,SS-1105911704211773'),
        array('assigned_vendor_id' => '75','group_booking_id' => 'SS-640131612011,SS-640131612011,SS-1098411704271'),
        array('assigned_vendor_id' => '80','group_booking_id' => 'SV-1106321704221,SX-1085271704071,SW-1108721704221,SW-1095671704141,SS-1101201704231,SS-1100061704251,SW-1108931704231'),
        array('assigned_vendor_id' => '81','group_booking_id' => 'SS-1104821704201429'),
        array('assigned_vendor_id' => '82','group_booking_id' => 'SY-111721703162,SW-494211611117'),
        array('assigned_vendor_id' => '83','group_booking_id' => 'SS-1099631704221,SX-1105791704211'),
        array('assigned_vendor_id' => '84','group_booking_id' => 'SY-1066371703251,SS-1102581704191210,SS-1095651704141359,SS-1086741704112263,SS-1108811704221964,ST-1093301704121,SC-1097121704171,SS-1096321704281,SY-1109321704241,SS-1086481704192509,SS-1091871704221,SS-1059351704021,SS-1092451704231,SS-1102581704191210,SS-1086481704192509,SS-1067761704091'),
        array('assigned_vendor_id' => '86','group_booking_id' => 'SS-645271611272,SW-1108921704221,SS-634691612022,SS-645271611272,SS-645271611271,SS-1099291704202529,SS-1092251704153184,SS-645271611271'),
        array('assigned_vendor_id' => '87','group_booking_id' => 'SS-1072451704061,ST-1103771704201,SS-1072451704061'),
        array('assigned_vendor_id' => '88','group_booking_id' => 'ST-153131704223,SS-1099621704221'),
        array('assigned_vendor_id' => '91','group_booking_id' => 'SS-1099771704221,SS-1104261704271,SS-1098961704212920,SS-1099771704221,SS-1098911704231,SS-1102831704212626,SS-1099761704221,SS-1098961704212920,SS-1099741704222108,SS-1100651704171312,SS-1099761704221,SK-1105461704211,SS-1109131704231231,SS-1099741704222108'),
        array('assigned_vendor_id' => '92','group_booking_id' => 'SV-1109281704241'),
        array('assigned_vendor_id' => '93','group_booking_id' => 'SS-1087461704142775,SS-1096651704231,SS-1087461704142775'),
        array('assigned_vendor_id' => '94','group_booking_id' => 'SY-760201704142'),
        array('assigned_vendor_id' => '96','group_booking_id' => 'SS-605401611151,SS-1070071704192,SS-1104441704261,SS-1100131704211,SS-1104391704221,SS-1106601704251,SV-1089391704212,SS-1104441704261,SS-1106601704251'),
        array('assigned_vendor_id' => '97','group_booking_id' => 'SS-1099841704201'),
        array('assigned_vendor_id' => '101','group_booking_id' => 'SS-1102551704191915'),
        array('assigned_vendor_id' => '103','group_booking_id' => 'SY-211781609081,SP-1087141704081,SP-1087141704081,SS-1075771704111'),
        array('assigned_vendor_id' => '104','group_booking_id' => 'SX-1104651704201'),
        array('assigned_vendor_id' => '109','group_booking_id' => 'SS-1082771704121'),
        array('assigned_vendor_id' => '111','group_booking_id' => 'SY-1075171704192'),
        array('assigned_vendor_id' => '118','group_booking_id' => 'SS-1099241704251,SY-1105851704211,SY-623211611191'),
        array('assigned_vendor_id' => '119','group_booking_id' => 'SR-1103801704201,SS-1029241705033'),
        array('assigned_vendor_id' => '125','group_booking_id' => 'SS-1105931704212236,SS-1105931704211577'),
        array('assigned_vendor_id' => '128','group_booking_id' => 'SS-1038851703121,SS-1056501703261,SW-1095861704141,SS-1045691703201,SS-1088581704081412,SW-1088931704091,SS-1045691703201,SS-1088581704081412,SS-962401704053'),
        array('assigned_vendor_id' => '130','group_booking_id' => 'SS-1086541704171,SC-1091181704121,SR-1096101704151,SS-1088571704081326,SS-1097381704161575,SS-1086541704171'),
        array('assigned_vendor_id' => '131','group_booking_id' => 'SS-1098761704241,SS-1098761704241'),
        array('assigned_vendor_id' => '132','group_booking_id' => 'SC-1105611704221,ST-1075101704182,SS-1093191704121970'),
        array('assigned_vendor_id' => '134','group_booking_id' => 'SY-990991704212'),
        array('assigned_vendor_id' => '137','group_booking_id' => 'SS-1075361704061'),
        array('assigned_vendor_id' => '138','group_booking_id' => 'SS-1101711704291,SW-1106011704211,SW-1106011704211'),
        array('assigned_vendor_id' => '139','group_booking_id' => 'SS-643841611271,SS-647891611281,SS-537081611112,SS-267081610011,SS-643841611271,SS-647891611281'),
        array('assigned_vendor_id' => '140','group_booking_id' => 'SS-1068111704032304,SR-559601704013'),
        array('assigned_vendor_id' => '145','group_booking_id' => 'SS-582031611161,SV-1105671704221,SW-1109341704241'),
        array('assigned_vendor_id' => '150','group_booking_id' => 'SS-1084931704182,SS-1084931704182,ST-1095551704141'),
        array('assigned_vendor_id' => '153','group_booking_id' => 'SX-1105231704211'),
        array('assigned_vendor_id' => '155','group_booking_id' => 'SS-1102941704261,SS-1098451704232769,SS-1101401704241,SW-1109041704231,SS-1101311704251,SP-1090811704111,SP-1090811704111,SS-1098451704232769'),
        array('assigned_vendor_id' => '156','group_booking_id' => 'SS-1104061704231,SS-1106831704251,SF-1106061704221,SS-1102091704181340,SS-1105961704211332,SS-1089031704091139,SV-1106151704221,SS-1102091704181340,SS-1107251704241,SS-1105811704211478,SC-1106201704221'),
        array('assigned_vendor_id' => '157','group_booking_id' => 'SV-1106341704221,SV-1109251704241'),
        array('assigned_vendor_id' => '158','group_booking_id' => 'SS-1104981704211541,SS-1092031704172539'),
        array('assigned_vendor_id' => '163','group_booking_id' => 'SS-1109051704231982,SW-1108951704231,SC-1109361704241'),
        array('assigned_vendor_id' => '164','group_booking_id' => 'SS-717121704232414'),
        array('assigned_vendor_id' => '165','group_booking_id' => 'SS-1092311704161'),
        array('assigned_vendor_id' => '167','group_booking_id' => 'SW-1109001704231,SX-1087261704081,SP-1105741704221,SK-1105621704211,SY-1030291704192,SP-1105741704221'),
        array('assigned_vendor_id' => '158','group_booking_id' => 'SY-1061131704103,SX-1102301704191,SS-1077551704071,SY-1102401704191,SS-1077551704071'),
        array('assigned_vendor_id' => '170','group_booking_id' => 'SS-627021611211'),
        array('assigned_vendor_id' => '172','group_booking_id' => 'SS-1099591704232825,SS-1109171704231199'),
        array('assigned_vendor_id' => '173','group_booking_id' => 'SS-1067621704071,SX-1095681704141,ST-1102311704191'),
        array('assigned_vendor_id' => '174','group_booking_id' => 'SS-1105981704211337,ST-1097161704171'),
        array('assigned_vendor_id' => '175','group_booking_id' => 'ST-1100831704181,SS-1098801704211'),
        array('assigned_vendor_id' => '179','group_booking_id' => 'SS-1086551704191,SS-1051011703271,SP-1065121703251,SS-1004621704022,SS-1080941704141,SS-1099081704231,SS-651461612031,SS-1074881703311538,SS-1085861704142685,SS-1092581704181,SS-1075721704121,SS-1090601704111478,SS-1051011703271,SP-1065121703251,SS-1080201704141,SS-1080481704151,SS-639261612081,SS-1097831704171,SS-651461612031,SS-1089791704181,SS-1077741704081,SS-1056451703281,SS-1080201704141,SS-1080481704151,SS-1086551704191,SS-639261612081,SS-1065951704031,SS-1078251704122614,SS-1080941704141,SS-1073811704082311,SS-1075721704121,SS-1097111704151548'),
        array('assigned_vendor_id' => '181','group_booking_id' => 'SS-1099861704251,SS-1099861704251'),
        array('assigned_vendor_id' => '182','group_booking_id' => 'SS-1100231704251,ST-1109301704241'),
        array('assigned_vendor_id' => '184','group_booking_id' => 'SC-1106221704221'),
        array('assigned_vendor_id' => '185','group_booking_id' => 'SS-1068081704051,SS-1078721704071,SS-1080731704151,SS-1078721704071'),
        array('assigned_vendor_id' => '189','group_booking_id' => 'SV-1101961704181,SS-1089821704131,SS-1105561704211227,SS-1109161704231165,SM-1104691704201,SV-1105601704221,SS-651051611301,SV-1101121704191,SS-95931704212258,SS-1107701704222745,SS-1108231704232498,ST-1106131704221,SS-1098971704212989,SS-651051611301,SS-1099521704172349'),
        array('assigned_vendor_id' => '195','group_booking_id' => 'SS-1108991704231646'),
        array('assigned_vendor_id' => '196','group_booking_id' => 'SA-1103211704201,SS-1067271704192509,SS-1067271704192509'),
        array('assigned_vendor_id' => '200','group_booking_id' => 'SS-1014581704143865,SS-1093631704201,SS-921741701251,SC-1090721704242,SS-1093631704201,SS-921741701251'),
        array('assigned_vendor_id' => '202','group_booking_id' => 'SS-1103561704201591'),
        array('assigned_vendor_id' => '203','group_booking_id' => 'SY-526541703202'),
        array('assigned_vendor_id' => '205','group_booking_id' => 'SP-1092981704121,SS-1104781704201311,SS-1092561704182977,SS-1108711704221939,SX-1103481704191'),
        array('assigned_vendor_id' => '206','group_booking_id' => 'SS-1099411704221'),
        array('assigned_vendor_id' => '207','group_booking_id' => 'SP-1096181704151,SS-1078561704111,SK-1104801704211'),
        array('assigned_vendor_id' => '210','group_booking_id' => 'SS-1006711702131'),
        array('assigned_vendor_id' => '211','group_booking_id' => 'SS-1094451704131101,SS-1098681704281,SM-1052821704144,SM-1103381704191,SK-1094651704131,SY-1108621704221,SK-1072971703301,SK-1094651704131'),
        array('assigned_vendor_id' => '212','group_booking_id' => 'SS-1101381704261,SX-1106241704221,SS-1101381704261'),
        array('assigned_vendor_id' => '213','group_booking_id' => 'ST-1105281704211,SS-1068161704011'),
        array('assigned_vendor_id' => '216','group_booking_id' => 'SM-1105861704211'),
        array('assigned_vendor_id' => '217','group_booking_id' => 'SS-1069551704051,SS-1095221704131876,SS-1069551704051,SS-1100461704171356'),
        array('assigned_vendor_id' => '218','group_booking_id' => 'SS-993291704213724'),
        array('assigned_vendor_id' => '219','group_booking_id' => 'SS-1044691704012,SS-1078331704132541'),
        array('assigned_vendor_id' => '223','group_booking_id' => 'SX-1096921704151,SS-1094851704172754,SX-1096921704151,SS-1094851704172754'),
        array('assigned_vendor_id' => '227','group_booking_id' => 'SM-1103411704191,SN-1093311704131,SM-1097031704151'),
        array('assigned_vendor_id' => '228','group_booking_id' => 'SM-1030361702271'),
        array('assigned_vendor_id' => '230','group_booking_id' => 'SS-1102541704191198,SS-1095641704141591,SS-1094721704251,SS-1095641704141591'),
        array('assigned_vendor_id' => '236','group_booking_id' => 'SS-1087751704141,SS-1087751704141,SS-1099351704251'),
        array('assigned_vendor_id' => '237','group_booking_id' => 'SS-1093581704201'),
        array('assigned_vendor_id' => '239','group_booking_id' => 'SR-1096011704151'),
        array('assigned_vendor_id' => '242','group_booking_id' => 'SS-1104561704251'),
        array('assigned_vendor_id' => '244','group_booking_id' => 'SY-1102491704191,SX-1100771704181,SS-1096581704211,SY-340201704242,SY-1104851704201'),
        array('assigned_vendor_id' => '245','group_booking_id' => 'SS-1087871704171'),
        array('assigned_vendor_id' => '249','group_booking_id' => 'SS-1086771704131,SS-1092201704161,SS-1059061703231,SD-1081771704051,SX-1097711704161'),
        array('assigned_vendor_id' => '251','group_booking_id' => 'SS-588701704072'),
        array('assigned_vendor_id' => '252','group_booking_id' => 'SS-1101231704271,SV-1109291704241,SW-1102211704191,SW-1102211704191,SS-1104901704201247'),
        array('assigned_vendor_id' => '254','group_booking_id' => 'SS-1103061704222880,SS-1103061704222880,SS-1099051704231,SS-1099051704231'),
        array('assigned_vendor_id' => '255','group_booking_id' => 'SX-1103271704191'),
        array('assigned_vendor_id' => '256','group_booking_id' => 'SY-1103841704201'),
        array('assigned_vendor_id' => '258','group_booking_id' => 'SS-1099511704241'),
        array('assigned_vendor_id' => '265','group_booking_id' => 'SS-1099101704182282,SS-1099101704182282,SW-1104671704201'),
        array('assigned_vendor_id' => '266','group_booking_id' => 'SK-1105481704211'),
        array('assigned_vendor_id' => '269','group_booking_id' => 'SP-1097871704171,SP-1097871704171,SY-1106211704221'),
        array('assigned_vendor_id' => '273','group_booking_id' => 'SP-147361704212'),
        array('assigned_vendor_id' => '274','group_booking_id' => 'ST-1079811704243,SX-1093081704121,SX-1097861704171,SC-1104951704211,ST-1100921704181,SX-1105801704211'),
        array('assigned_vendor_id' => '276','group_booking_id' => 'SS-1090091704151,SX-1104771704201'),
        array('assigned_vendor_id' => '277','group_booking_id' => 'SS-1099021704222256'),
        array('assigned_vendor_id' => '279','group_booking_id' => 'SS-1093121704121'),
        array('assigned_vendor_id' => '283','group_booking_id' => 'SY-1093361704121')
      );
        
      return $service_center_booking_action;


    }
    
    /**
     * @desc: This method checks any update exist in the booking state change table 
     * for requesting booking and service center
     * @param String $booking_id
     * @param String $service_center_id
     * @return Array
     */
    function check_any_update_in_state_change($booking_id, $service_center_id){
        $sql = "SELECT DISTINCT(bsc.booking_id), service_center_id FROM booking_state_change as bsc "
                . " where booking_id = '$booking_id' "
                . " AND service_center_id = '$service_center_id' "
                . " AND new_state != '".ENGG_ASSIGNED."'"
                . " AND create_date >= date('Y-m-d') ";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    /**
     * @Desc: This function is used to get Penalty on Booking details by Booking ID
     * @params: Booking ID
     * @return: Array
     * 
     * 
     */
    function get_penalty_on_booking_by_booking_id($booking_id){
        $this->db->select('*');
        $this->db->where('booking_id',$booking_id);
        //$this->db->join('employee','penalty_on_booking.penalty_remove_agent_id = employee.id');
        $query = $this->db->get('penalty_on_booking');
        return $query->result_array();
        
    }
    
    /**
     * @Desc: This function is used to Updated Penalty on Bookings Table for particular Booking ID
     *         Only those bookings are updated whose current state is Cancelled or Completed
     *         Bookings which are Escalted are not Updated
     * @params: ID ,data Array
     * @return: Boolean
     */
    function update_penalty_on_booking($id,$data){
        $this->db->where('id',$id);
        $this->db->update('penalty_on_booking',$data);
        if($this->db->affected_rows() > 0){
            return TRUE;
        }else{
            return FALSE;
        }
    }
    /**
     * @desc: This is used to return penalty amount and booking id
     * @param String $vendor_id
     * @param String $from_date
     * @param String $to_date
     * @return boolean
     */
    function add_penalty_in_invoice($vendor_id, $from_date, $to_date,$distinct, $is_regenerate){
        $where = "";
        if (PENALTY_ON_COMPLETED_BOOKING == TRUE && PENALTY_ON_CANCELLED_BOOKING == TRUE) {
            $where = " AND booking_details.current_status IN ('Completed', 'Cancelled') ";
        } else if (PENALTY_ON_COMPLETED_BOOKING == TRUE && PENALTY_ON_CANCELLED_BOOKING == FALSE) {
            $where = " AND booking_details.current_status IN ('Completed') ";
        } else if (PENALTY_ON_COMPLETED_BOOKING == FALSE && PENALTY_ON_CANCELLED_BOOKING == TRUE) {
            $where = " AND booking_details.current_status IN ('Cancelled') ";
        }
        $invoice_check = "";
        if($is_regenerate == 0){
            $invoice_check =" AND foc_invoice_id IS NULL ";
        }
        if (PENALTY_ON_COMPLETED_BOOKING != FALSE && PENALTY_ON_CANCELLED_BOOKING != FALSE) {
            
            $sql = " SELECT COUNT( $distinct p.booking_id) as penalty_times, p.booking_id,criteria_id,

                CASE WHEN ((count(p.booking_id) *  p.penalty_amount) > cap_amount) THEN (cap_amount)

                ELSE (COUNT(p.booking_id) * p.penalty_amount) END  AS p_amount, p.penalty_amount

                FROM `penalty_on_booking` AS p, penalty_details, booking_details 
                WHERE criteria_id IN (11,10,9,8,2) 
                AND criteria_id = penalty_details.id 
                AND  p.active = 1  
                
                AND  closed_date >= '".$from_date."'
                AND closed_date < '".$to_date."'
                AND service_center_id = '".$vendor_id."'
                $invoice_check
                AND booking_details.booking_id = p.booking_id $where
                GROUP BY p.booking_id, criteria_id  ";
            
            
            $query = $this->db->query($sql);
            return $query->result_array();
            
        } else {
            return FALSE;
        }
    }
    
    function get_removed_penalty($vendor_id, $to_date, $distinct) {
        $from_date = date('Y-m-d', strtotime('-1 months', strtotime($to_date)));
        $where = "";
        if (PENALTY_ON_COMPLETED_BOOKING == TRUE && PENALTY_ON_CANCELLED_BOOKING == TRUE) {
            $where = " AND booking_details.current_status IN ('Completed', 'Cancelled') ";
        } else if (PENALTY_ON_COMPLETED_BOOKING == TRUE && PENALTY_ON_CANCELLED_BOOKING == FALSE) {
            $where = " AND booking_details.current_status IN ('Completed') ";
        } else if (PENALTY_ON_COMPLETED_BOOKING == FALSE && PENALTY_ON_CANCELLED_BOOKING == TRUE) {
            $where = " AND booking_details.current_status IN ('Cancelled') ";
        }

        if (PENALTY_ON_COMPLETED_BOOKING != FALSE && PENALTY_ON_CANCELLED_BOOKING != FALSE) {
            
            $sql = " SELECT COUNT( $distinct p.booking_id) as penalty_times, p.booking_id,criteria_id,

                CASE WHEN ((count(p.booking_id) *  p.penalty_amount) > cap_amount) THEN (cap_amount)

                ELSE (COUNT(p.booking_id) * p.penalty_amount) END  AS p_amount, p.penalty_amount

                FROM `penalty_on_booking` AS p, penalty_details, booking_details 
                WHERE criteria_id IN (11,10,9,8,2) 
                AND criteria_id = penalty_details.id 
                AND  p.active = 0  
                AND foc_invoice_id IS NOT NULL
                AND  closed_date >= '".$from_date."'
                AND closed_date < '".$to_date."'
                AND service_center_id = '".$vendor_id."'
                
                AND booking_details.booking_id = p.booking_id $where
                GROUP BY p.booking_id, criteria_id";           
            
            $query = $this->db->query($sql);
            return $query->result_array();
        } else {
            return FALSE;
        }
    }
    
    /**
     * @desc This is used to update penalty table
     * @param Array $where
     * @param Array $data
     */
    function update_penalty_any($where, $data){
        $this->db->where($where);
        $this->db->update("penalty_on_booking", $data);
        
    }
    
    /**
     * @desc This is used to get penalty on booking table for booking id on selected condition
     * @param Array $where
     * @return Array $data
     */
    function get_penalty_on_booking_any($where){
        $this->db->select('*');
        $this->db->where($where);
        $this->db->from('penalty_on_booking');
        $query = $this->db->get();
        return $query->result_array();
        
    }


}
