<?php

class url_table_model extends CI_Model {

    /**
     * @desc Load database
     */
    function __construct() {
        parent::__Construct();

        $this->db = $this->load->database('default', TRUE, TRUE);
    }


    function get_blog_id($url) {
        $query = $this->db->get_where('url_table', array('url' => $url));

	return $query->result_array();
    }

}
