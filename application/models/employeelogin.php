<?php

class Employeelogin extends CI_Model {

    /**
     * @desc load both db
     */
    function __construct() {
	parent::__Construct();

	$this->db_location = $this->load->database('default1', TRUE, TRUE);
	$this->db = $this->load->database('default', TRUE, TRUE);
    }

    /** @description* Post request to get authentication admin
     *  @param : employee id and password
     *  @retun: array(result)
     */
    function login($employee_id, $employee_password) {
	$sql = "SELECT * FROM employee WHERE employee_id = '$employee_id' AND employee_password = '$employee_password'";
	$data = $this->db->query($sql);
	return $data->result_array();
    }

// end of model
}
