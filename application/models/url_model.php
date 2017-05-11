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

    /** @description Insert new blogs in url table which are not templates
     *
     * Ignore if URL is already there
     *
     *  @param
     *  @return Int Number of URLs inserted
     */
    function add_non_template_blogs() {
	$sql = "INSERT IGNORE INTO `url` (`url`, `brand`, `place`, `blog_id`) VALUES ";

	//Find non-template blogs
	$this->db->where('is_template', 0);
	$query = $this->db->get('blogs');

	$non_templates = $query->result_array();

	foreach ($non_templates as $nt) {
	    $title = $nt['url'];
	    $id = $nt['id'];

	    $s = "('$title','','','$id'),";
	    $sql .= $s;
	}

	//Replace last "," with ";"
	$sql[strlen($sql) - 1] = ";";

	$this->db->query($sql);

	return count($non_templates);
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

	//log_message('info', __METHOD__ . '=> Last query: ' . $this->db->last_query());
    }

    /** @description Deletes all URLs from url table
     *  @param
     *  @return
     */
    function delete_all() {
	$this->db->empty_table('url');

	log_message('info', __METHOD__ . '=> Last query: ' . $this->db->last_query());
    }

    /** @description Renames the newly generated table and create a new table
     * for next iteration.
     *
     * This is called after all operations succeed and URLs have been
     * generated properly.
     *
     *  @param
     *  @return
     */
    function switch_url_tables() {
	$this->load->dbforge();

	//Drop old table first
	$this->dbforge->drop_table('url_table');

	//Rename 'url' table
	$this->dbforge->rename_table('url', 'url_table');

	//Make a fresh copy of URL table as well for next time
	$this->db->query("CREATE TABLE url LIKE url_table");

	log_message('info', __METHOD__ . '=> Last query: ' . $this->db->last_query());
    }
    
    function getworkbook_details($select, $where){
        $this->db->select($select);
        $this->db->where($where);
        $data  = $this->db->get('workbook2');
        return $data->result_array();
        
    }
    
    function truncate_url_ref_table(){
       
        $this->db->truncate('url_table_ref');
    }
    
    function insert_url_ref_data_batch($data){
        //$this->db->ignore();
        $this->db->insert_Ignore_Duplicate_batch("url_table_ref", $data);
    }

//end model
}
