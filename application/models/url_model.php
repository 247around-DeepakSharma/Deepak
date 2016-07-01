<?php

class url_model extends CI_Model {

    /**
     * @desc load both db
     */
    function __construct() {
	parent::__Construct();

	$this->db = $this->load->database('default', TRUE, TRUE);
    }

    /** @description Insert new URL in url table
     *  @param Array $data URL details
     *  @return ID of the URL generated
     */
    function add($data) {
	$this->db->insert('url', $data);

	return $this->db->insert_id();
    }

    /** @description Find all keywords in Workbook table
     *  @param
     *  @return Array Keywords array
     */
    function get_keyword() {
	$this->db->select(array('Keyword'));
	$query = $this->db->get('url');

	log_message('info', __METHOD__ . '=> Keywords count: ' . count($query->result_array()));

	return $query->result_array();
    }

    /** @description Generate possible URL templates with prefix and suffix as
     * per the Workbook table.
     * Template would be of the form:
     *
     * <prefix>keyword<suffix>
     *
     * like
     *
     * <brand> refrigerator repair in <city>
     *
     * As of now, there SHOULD be space before and after the keyword if prefix
     * and suffix are present.
     *
     *  @param
     *
     *  @return Array URL template strings with prefix and suffix
     */
    function get_url_template_with_prefix_suffix() {
	$this->db->where('active', '1');
	$query = $this->db->get('workbook2');
	$templates = $query->result_array();
	$urls = array();
	$services = array();

	foreach ($templates as $t) {
	    $prefix = $t['prefix'];
	    $suffix = $t['suffix'];
	    $url = $t['keyword'];

	    //prefix is added if exists
	    if (!is_null($prefix))
		$url = $prefix . $url;

	    //suffix is added if exists
	    if (!is_null($suffix))
		$url = $url . $suffix;

	    array_push($urls, $url);
	    array_push($services, $t['service_name']);
	}

	log_message('info', __METHOD__ . '=> URLs count: ' . count($urls));

	return array($urls, $services);
    }

    function execute_sql($query) {
	$this->db->query($query);

	log_message('info', __METHOD__ . '=> Last query: ' . $this->db->last_query());
    }

    /** @description Deletes all URLs from url table
     *  @param
     *  @return
     */
    function delete_all() {
	$this->db->empty_table('url');

	log_message('info', __METHOD__ . '=> Last query: ' . $this->db->last_query());
    }

//end model
}
