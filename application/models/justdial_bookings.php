<?php

class Justdial_bookings extends CI_Model {

    /**
     * @desc Load database
     */
    function __construct() {
        parent::__Construct();

        $this->db = $this->load->database('default', TRUE, TRUE);
    }

    function insert_booking($jd_booking) {
        //log_message('info', __METHOD__ . "-> " . print_r($jd_booking, TRUE));

        $this->db->insert("justdial_booking", $jd_booking);

        //$result = (bool) ($this->db->affected_rows() > 0);
        //log_message('info', __METHOD__ . " => SQL: " . $this->db->last_query() . ", Result: " . $result);
    }

}
