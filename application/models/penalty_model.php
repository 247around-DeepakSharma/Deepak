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
            $this->insert_penalty_on_booking($data);
            if ($data['criteria_id'] == '2') {
                $this->booking_model->update_booking($data['booking_id'], array('is_penalty' => '1'));
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

    /**
     * Applies penalty on SF for bookings which have not been updated today
     * This is triggered from CRON.
     * 
     * @return boolean
     */
    function penalty_on_service_center_for_update_booking() {
        log_message('info', __FUNCTION__);
        $sql = "SELECT distinct(SC.booking_id), SC.service_center_id as assigned_vendor_id "
                . " FROM service_center_booking_action as SC, booking_details as BD, service_centres as SCS "
                . " WHERE (SC.current_status='Pending') "
                . " AND SC.booking_id = BD.booking_id "
                . " AND (DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(BD.booking_date, '%d-%m-%Y')) >= 0)"
                . " AND SCS.id = SC.service_center_id "
                . " AND SCS.is_update = 1 ";
        $query = $this->db->query($sql);

        if ($query->num_rows > 0) {
            $result = $query->result_array();
            foreach ($result as $value) {
                $data = $this->check_any_update_in_state_change($value['booking_id'], $value['assigned_vendor_id']);
                if (empty($data)) {
                    $value['agent_id'] = _247AROUND_DEFAULT_AGENT;
                    $value['remarks'] = 'Booking Not Updated On Time';
                    $where = array('criteria' => BOOKING_NOT_UPDATED_BY_SERVICE_CENTER, 'active' => '1');
                    $this->get_data_penalty_on_booking($value, $where);
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
    function get_penalty_on_booking_by_booking_id($booking_id) {
        $this->db->select('*');
        $this->db->where('booking_id', $booking_id);
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
    function add_penalty_in_invoice($vendor_id, $from_date, $to_date, $distinct, $is_regenerate) {
        $where = "";
        if (PENALTY_ON_COMPLETED_BOOKING == TRUE && PENALTY_ON_CANCELLED_BOOKING == TRUE) {
            $where = " AND booking_details.current_status IN ('Completed', 'Cancelled') ";
        } else if (PENALTY_ON_COMPLETED_BOOKING == TRUE && PENALTY_ON_CANCELLED_BOOKING == FALSE) {
            $where = " AND booking_details.current_status IN ('Completed') ";
        } else if (PENALTY_ON_COMPLETED_BOOKING == FALSE && PENALTY_ON_CANCELLED_BOOKING == TRUE) {
            $where = " AND booking_details.current_status IN ('Cancelled') ";
        }
        $invoice_check = "";
        if ($is_regenerate == 0) {
            $invoice_check = " AND foc_invoice_id IS NULL ";
        }
        if (PENALTY_ON_COMPLETED_BOOKING != FALSE && PENALTY_ON_CANCELLED_BOOKING != FALSE) {
            $sql = "SELECT COUNT( $distinct p.booking_id ) as penalty_times,CASE WHEN (COUNT( p.booking_id ) * penalty_amount) < '" . CAP_ON_PENALTY_AMOUNT . "' 
            THEN (COUNT( p.booking_id ) * penalty_amount) ELSE ( " . CAP_ON_PENALTY_AMOUNT . " ) END AS p_amount, 
            p.booking_id, penalty_amount FROM  
           `penalty_on_booking` AS p, booking_details 
            WHERE  `criteria_id` IN (2,9,10,11,8) AND  `closed_date` >=  '" . $from_date . "' 
            AND closed_date <  '" . $to_date . "'
            AND service_center_id = '" . $vendor_id . "'
            AND p.active = 1
            $invoice_check
            AND booking_details.booking_id = p.booking_id $where
            GROUP BY p.booking_id";

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
            $sql = "SELECT COUNT( $distinct p.booking_id ) as penalty_times,CASE WHEN (COUNT( p.booking_id ) * penalty_amount) < '" . CAP_ON_PENALTY_AMOUNT . "' 
            THEN (COUNT( p.booking_id ) * penalty_amount) ELSE ( " . CAP_ON_PENALTY_AMOUNT . " ) END AS p_amount, 
            p.booking_id, penalty_amount FROM  
           `penalty_on_booking` AS p, booking_details 
            WHERE  `criteria_id` IN (2,9,10,11,8) AND  `closed_date` >=  '" . $from_date . "' 
            AND closed_date <  '" . $to_date . "'
            AND service_center_id = '" . $vendor_id . "'
            AND p.active = 0
            AND foc_invoice_id IS NOT NULL
            AND booking_details.booking_id = p.booking_id $where
            GROUP BY p.booking_id";

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
    function get_penalty_on_booking_any($where) {
        $this->db->select('*');
        $this->db->where($where);
        $this->db->from('penalty_on_booking');
        $query = $this->db->get();
        return $query->result_array();
    }

}
