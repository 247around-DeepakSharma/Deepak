<?php

//error_reporting(E_ERROR);
//ini_set('display_errors', '0');

class ad_campaigns extends CI_Model {

    /**
     * @desc load both db
     */
    function __construct() {
	parent::__Construct();

	$this->db = $this->load->database('default', TRUE, TRUE);
    }

    function insert($data) {
	$this->db->insert('booking_campaign_tracking', $data);
    }

}
