<?php

class converter_model extends CI_Model {

    /**
     * @desc load both db
     */
    function __construct() {
	parent::__Construct();

	$this->db_location = $this->load->database('default1', TRUE, TRUE);

	$this->db = $this->load->database('default', TRUE, TRUE);
    }

    function insert_rows_in_batch($rows) {
	$query = $this->db->insert_batch('india_pincode', $rows);
    }

}
