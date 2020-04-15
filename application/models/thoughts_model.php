<?php

class Thoughts_model extends CI_Model {

    /**
     * @desc Load database
     */
    function __construct() {
        parent::__Construct();

        $this->db = $this->load->database('default', TRUE, TRUE);
    }

    function get_thoughts($category) {
	//If category is all, then return all master articles
	if (strcasecmp($category, "all") == 0) {
	    $data = array('published' => '1', "is_master" => '1');
	} else {
	    $data = array('published' => '1', 'category' => $category, "is_master" => '1');
	}

	$query = $this->db->get_where('blogs', $data);

	return $query->result_array();
    }

    function get_thought($id) {
	$data = array('id' => $id, 'published' => '1');
	$query = $this->db->get_where('blogs', $data);

	return $query->result_array();
    }

    function get_appliance_page_details($appliance) {
	$data = array('service' => $appliance);
	$query = $this->db->get_where('appliance_web_pages', $data);

	return $query->result_array()[0];
    }

}
