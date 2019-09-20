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
//  log_message('info', __FUNCTION__);
//  $sql = "SELECT distinct(BD.booking_id), assigned_vendor_id, BD.partner_id, assigned_engineer_id, "
//      . " SC.create_date, BD.booking_date FROM booking_details as BD,  "
//      . " service_center_booking_action as SC, service_centres as SCS "
//      . " WHERE BD.assigned_vendor_id IS NOT NUll "
//      . " AND (BD.current_status='Pending' OR BD.current_status='Rescheduled') "
//      . " AND SC.booking_id = BD.booking_id "
//      . " AND SC.service_center_id = BD. assigned_vendor_id "
//      . " AND (SC.current_status='Pending' OR SC.current_status='InProcess') "
//            . " AND SCS.id = SC.service_center_id "
//            . " AND SCS.is_update = 1 ";
//
//  $query = $this->db->query($sql);
//  $assigned_engineer = $query->result_array();
//
//  foreach ($assigned_engineer as $value) {
//      $engineer = $this->check_engineer_assigned($value['booking_id'], $value['assigned_vendor_id']);
//      $date_1 = date_create(date('Y-m-d ', strtotime($value['booking_date'])));
//      $date_2 = date_create(date('Y-m-d', strtotime($value['create_date'])));
//
//      $date_diff = date_diff($date_1, $date_2);
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
//      log_message('info', __FUNCTION__ . " Days > 1");
//      if (empty($engineer)) {
//          log_message('info', __FUNCTION__ . " Engineer is not assign");
//          // If engineer is not assign till 2 PM then service center will pay penalty
//          // Current Time is greater than 12 PM
//          if (date('H') > Max_TIME_TO_BE_ASSIGNED_ENGINEER) {
//          log_message('info', __FUNCTION__ . " Current Time is greater than 12 PM");
//          $where = array('partner_id' => $value['partner_id'], 'criteria' => ENGG_NOT_ASSIGN, 'active' => '1');
//          $this->get_data_penalty_on_booking($value, $where);
//          }
//      } else {
//          log_message('info', __FUNCTION__ . " Engineer assigned");
//          log_message('info', __FUNCTION__ . " Assigned Engineer Time " . date('H', strtotime($engineer['create_date'])));
//          if (date('H', strtotime($engineer['create_date'])) > Max_TIME_TO_BE_ASSIGNED_ENGINEER) {
//          $where = array('partner_id' => $value['partner_id'], 'criteria' => ENGG_LATE_ASSIGN, 'active' => '1');
//          $this->get_data_penalty_on_booking($value, $where);
//          }
//      }
//      } else if ($date_diff->days == 0) {
//      // Assigned Engineer for same day booking
//      log_message('info', __FUNCTION__ . " Days == 0");
//      $date3 = date('H', strtotime($value['create_date']));
//
//      if (10 >= $date3) {
//          // Assgined Engineer befor 10AM.
//          // Service center will not assigned till 2PM, then they will pay penalty
//          log_message('info', __FUNCTION__ . " Assgined Engineer till 10 AM");
//          if (empty($engineer)) {
//          log_message('info', __FUNCTION__ . " Engineer is not assign");
//          if (date('H') > Max_TIME_TO_BE_ASSIGNED_ENGINEER) {
//              $where = array('partner_id' => $value['partner_id'], 'criteria' => ENGG_NOT_ASSIGN, 'active' => '1');
//              $this->get_data_penalty_on_booking($value, $where);
//          }
//          } else {
//          log_message('info', __FUNCTION__ . " Engineer assigned");
//          $date4 = date('H', strtotime($engineer['create_date']));
//          if ($date4 > Max_TIME_TO_BE_ASSIGNED_ENGINEER) {
//              $where = array('partner_id' => $value['partner_id'], 'criteria' => ENGG_LATE_ASSIGN, 'active' => '1');
//              $this->get_data_penalty_on_booking($value, $where);
//          }
//          }
//      } else {
//          log_message('info', __FUNCTION__ . " Assgined Engineer after 10 AM");
//          // Assgined Engineer after 10AM.
//          // Service centers need to be assign engineer in the next 4 hours from assigned time
//          $date5 = date('H', strtotime($value['create_date'] . " +".Max_TIME_WITH_IN_ASSIGNED_ENGINEER." hours"));
//          if (empty($engineer)) {
//          log_message('info', __FUNCTION__ . " Engineer is not assign");
//          if (date('H') > $date5) {
//              $where = array('partner_id' => $value['partner_id'], 'criteria' => ENGG_NOT_ASSIGN, 'active' => '1');
//              $this->get_data_penalty_on_booking($value, $where);
//          }
//          } else {
//          log_message('info', __FUNCTION__ . " Engineer assigned");
//          $date4 = date('H', strtotime($engineer['create_date']));
//          if ($date4 > $date5) {
//              $where = array('partner_id' => $value['partner_id'], 'criteria' => ENGG_LATE_ASSIGN, 'active' => '1');
//              $this->get_data_penalty_on_booking($value, $where);
//          }
//          }
//      }
//      }
//  }
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
            $data['agent_id'] = isset($value['agent_id']) && !empty($value['agent_id']) ? $value['agent_id'] : NULL;
            $data['remarks'] = isset($value['remarks']) && !empty($value['remarks']) ? $value['remarks'] : NULL;
            $data['criteria_id'] = $penalty_details['id'];
            $data['penalty_amount'] = $penalty_details['penalty_amount'];
            $data['active'] = 1;
            $data['create_date'] = date('Y-m-d H:i:s');
            $data['agent_type'] = $value['agent_type'];
            $this->insert_penalty_on_booking($data);
            if ($data['criteria_id'] == BOOKING_NOT_UPDATED_PENALTY_CRITERIA) {
                $this->booking_model->update_booking($data['booking_id'], array('is_penalty' => '1', 'dependency_on' => '1'));
            }

            return $data;
        } else {
            log_message('info', __FUNCTION__ . 'Unable to get Penalty Details for provided values of where ' . print_r($where, TRUE));
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

    function get_update_booking_penalty_data(){
        $sql = "SELECT SC.service_center_id AS assigned_vendor_id, CONCAT(  '', GROUP_CONCAT( DISTINCT (

                BD.booking_id
                ) ) ,  '' ) AS booking_group,SCS.non_working_days
                FROM service_center_booking_action AS SC, booking_details AS BD, service_centres AS SCS
                WHERE (
                SC.current_status =  'Pending' AND BD.current_status IN ('Pending', 'Rescheduled')
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
        return $query;
    }
    /**
     * Applies penalty on SF for bookings which have not been updated today
     * This is triggered from CRON.
     * 
     * @return boolean
     */
    function penalty_on_service_center_for_update_booking() {
        log_message('info', __FUNCTION__);
        $query = $this->get_update_booking_penalty_data();
        if ($query->num_rows > 0) {
            $result = $query->result_array();
            foreach ($result as $value) {
                $non_wroking_days = explode(',', $value['non_working_days']);
                if (!in_array(date('l'), $non_wroking_days)) {
                    $booking_id_array = explode(",", $value['booking_group']);
                    
                    $booking_not_update = 0;
                    foreach ($booking_id_array as $booking_id) {
                        $data = $this->check_any_update_in_state_change($booking_id, $value['assigned_vendor_id']);
                        if (empty($data)) {
                            $data1['agent_id'] = _247AROUND_DEFAULT_AGENT;
                            $data1['remarks'] = 'Booking Not Updated On Time';
                            $where = array('criteria' => BOOKING_NOT_UPDATED_BY_SERVICE_CENTER, 'active' => '1');
                            $data1['booking_id'] = $booking_id;
                            $data1['assigned_vendor_id'] = $value['assigned_vendor_id'];
                            $data1['agent_type'] = 'admin';
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
            }
        } else {
            return FALSE;
        }
    }

    /**
     * @desc: This method checks any update exist in the booking state change table 
     * for requesting booking and service center
     * @param String $booking_id
     * @param String $service_center_id
     * @return Array
     */
    function check_any_update_in_state_change($booking_id, $service_center_id) {
        $sql = "SELECT DISTINCT(bsc.booking_id), service_center_id FROM booking_state_change as bsc "
                . " where booking_id = '$booking_id' "
                . " AND service_center_id = '$service_center_id' "
                . " AND new_state != '" . ENGG_ASSIGNED . "'"
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
    function get_penalty_on_booking_by_booking_id($booking_id, $vendor_id = "") {
        $this->db->select('penalty_on_booking.*, name as sf_name');
        $this->db->where('booking_id', $booking_id);
        if(!empty($vendor_id)){
            $this->db->where('penalty_on_booking.service_center_id', $vendor_id);
        }
        $this->db->join('service_centres', "service_centres.id = penalty_on_booking.service_center_id");
        $query = $this->db->get('penalty_on_booking');
        $result=  $query->result_array();
        if(!empty($result)){
            foreach ($result as $key => $value){
                if($value['active'] == 0){
                    $where = array('id' => $value['penalty_remove_agent_id']);
                    $data1 = $this->employee_model->get_employee_by_group($where);
                    $result[$key]['agent_name'] = isset($data1[0]['full_name']) ? $data1[0]['full_name'] : '';
                }else if($value['active'] == 1){
                    if($value['agent_type'] == 'admin'){
                        $where = array('id' => $value['agent_id']);
                        $data1 = $this->employee_model->get_employee_by_group($where);
                        $result[$key]['agent_name'] = isset($data1[0]['full_name']) ? $data1[0]['full_name'] : '';
                    }else if($value['agent_type'] == 'partner'){
                        $where = array('partners.id' => $value['agent_id']);
                        $data1 = $this->partner_model->getpartner_details('public_name',$where);
                        $result[$key]['agent_name'] = isset($data1[0]['public_name']) ? $data1[0]['public_name'] : '';
                    }
                }
            }
        }
        
        return $result;
    }

    /**
     * @Desc: This function is used to Updated Penalty on Bookings Table for particular Booking ID
     *         Only those bookings are updated whose current state is Cancelled or Completed
     *         Bookings which are Escalted are not Updated
     * @params: ID ,data Array
     * @return: Boolean
     */
    function update_penalty_on_booking($id, $data) {
        $this->db->where('id', $id);
        $this->db->update('penalty_on_booking', $data);
        if ($this->db->affected_rows() > 0) {
            return TRUE;
        } else {
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
            
            $sql = " SELECT COUNT( $distinct p.booking_id) as penalty_times, GROUP_CONCAT(p.id) as p_id, p.booking_id,criteria_id,criteria,

                CASE WHEN ((count(p.booking_id) *  p.penalty_amount) > cap_amount) THEN (cap_amount)

                ELSE (COUNT(p.booking_id) * p.penalty_amount) END  AS p_amount, p.penalty_amount

                FROM `penalty_on_booking` AS p, penalty_details, booking_details 
                WHERE criteria_id = penalty_details.id 
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
    
    function get_removed_penalty($vendor_id, $from_date, $to_date, $distinct) {
        
        $where = "";
        if (PENALTY_ON_COMPLETED_BOOKING == TRUE && PENALTY_ON_CANCELLED_BOOKING == TRUE) {
            $where = " AND booking_details.current_status IN ('Completed', 'Cancelled') ";
        } else if (PENALTY_ON_COMPLETED_BOOKING == TRUE && PENALTY_ON_CANCELLED_BOOKING == FALSE) {
            $where = " AND booking_details.current_status IN ('Completed') ";
        } else if (PENALTY_ON_COMPLETED_BOOKING == FALSE && PENALTY_ON_CANCELLED_BOOKING == TRUE) {
            $where = " AND booking_details.current_status IN ('Cancelled') ";
        }

        if (PENALTY_ON_COMPLETED_BOOKING != FALSE && PENALTY_ON_CANCELLED_BOOKING != FALSE) {
            
            $sql = " SELECT COUNT( $distinct p.booking_id) as penalty_times, GROUP_CONCAT(p.id) as c_id, p.booking_id,criteria_id,criteria,

                CASE WHEN ((count(p.booking_id) *  p.penalty_amount) > cap_amount) THEN (cap_amount)

                ELSE (COUNT(p.booking_id) * p.penalty_amount) END  AS p_amount, p.penalty_amount

                FROM `penalty_on_booking` AS p, penalty_details, booking_details
                WHERE criteria_id = penalty_details.id 
                AND  p.active = 0  
                AND foc_invoice_id IS NOT NULL
                AND penalty_remove_date >= '".$from_date."'
                AND penalty_remove_date < '".$to_date."'
                AND service_center_id = '".$vendor_id."'
                AND removed_penalty_invoice_id IS NULL
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
    function update_penalty_any($where, $data) {
        $this->db->where($where);
        $this->db->update("penalty_on_booking", $data);
    }

    /**
     * @desc This is used to get penalty on booking table for booking id on selected condition
     * @param Array $where
     * @return Array $data
     */

    function get_penalty_on_booking_any($where, $select, $reserved = array()){
        $this->db->_reserved_identifiers = $reserved;
        $this->db->select($select);

        $this->db->where($where);
        $this->db->from('penalty_on_booking');
        $this->db->join("penalty_details", "penalty_on_booking.criteria_id = penalty_details.id");
        $this->db->join("service_centres", "penalty_on_booking.service_center_id = service_centres.id");
        $query = $this->db->get();
        return $query->result_array();
    }
    
    function get_panelty_details_data($id = NULL) {
        $this->db->select('vendor_escalation_policy.id, vendor_escalation_policy.entity, vendor_escalation_policy.escalation_reason, vendor_escalation_policy.active as escalation_policy_active, penalty_details.escalation_id, penalty_details.cap_amount, penalty_details.penalty_amount, penalty_details.active as penalty_active');
        $this->db->join("penalty_details", "vendor_escalation_policy.id = penalty_details.escalation_id", "left");   
        if(!empty($id)) {
            $this->db->where('vendor_escalation_policy.id', $id);
        }
        $this->db->order_by('vendor_escalation_policy.escalation_reason');
        $query = $this->db->get('vendor_escalation_policy');
        return $query->result_array();        
    }
    
}
