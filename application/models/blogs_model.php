<?php

class Blogs_model extends CI_Model {

    /**
     * @desc load both db
     */
    function __construct() {
	parent::__Construct();

	$this->db_location = $this->load->database('default1', TRUE, TRUE);
	$this->db = $this->load->database('default', TRUE, TRUE);

	$this->db_wp = $this->load->database('wordpress', TRUE, TRUE);
    }

    function view_blogs() {
	$sql = "SELECT * FROM blogs";

	$query = $this->db->query($sql);

	return $query->result_array();
    }

    function get_blog_by_wpid($wp_id) {
	$sql = "SELECT * FROM blogs WHERE wp_id = '$wp_id'";

	$query = $this->db->query($sql);
	$results = $query->result_array();

	return $results;
    }

    function get_blog_by_keyword($kw) {
	$this->db->where('keyword', $kw);
	$query = $this->db->get('blogs');

	return $query->result_array();
    }

    function add_blog($blog) {
	$this->db->insert('blogs', $blog);

	return $this->db->insert_id();
    }

    function add_url($url) {
	$this->db->insert('url_table', $url);
    }

    function update_blog($wp_id, $blog) {
	$this->db->where(array('wp_id' => $wp_id));
	$this->db->update('blogs', $blog);
    }

    function copy_blog_from_wordpress($blog) {
	$this->db->insert('blogs', $blog);
    }

    function editblog($id) {
	$sql = "SELECT * FROM blogs WHERE id='$id'";

	$query = $this->db->query($sql);

	return $query->result_array();
    }

    function edit_blog($blog, $image_file) {
	$sql = "UPDATE blogs SET title=?, url=?,"
	    . "description=?, keyword=?, "
	    . "content=?, author=?, file_input=?, "
	    . "alternate_text=? WHERE id=?";

	$query = $this->db->query($sql, array(
	    $blog[title], $blog[url], $blog[description], $blog[keyword],
	    $blog[content], $blog[author], $blog[file_input],
	    $blog[alternate_text], $blog[id]));
    }

    function publish($id) {
	$sql = "UPDATE blogs SET published = 1 WHERE id='$id'";
	$query = $this->db->query($sql);
    }

    function unpublish($id) {
	$sql = "UPDATE blogs SET published = 0 WHERE id='$id'";
	$query = $this->db->query($sql);
    }

    function delete($id) {
	$sql = "Delete FROM blogs WHERE id='$id'";
	$query = $this->db->query($sql);
    }

    function find_blogs_to_update() {
	$sql = "SELECT * FROM  `wp_posts` WHERE `post_date` > '2016-01-13 00:00:00'
                AND `post_status` LIKE 'pending'";
	$query = $this->db_wp->query($sql);

	return $query->result_array();
    }

    function get_blog_from_wp_table($blog_id) {
	$sql = "SELECT * FROM  `wp_posts` WHERE ID=$blog_id";
	$query = $this->db_wp->query($sql);

	return $query->result_array()[0];
    }

    function get_author($auth_id) {
	$sql = "SELECT * FROM wp_users WHERE id = '$auth_id'";

	$query = $this->db_wp->query($sql);
	$results = $query->result_array();

	return $results[0]['display_name'];
    }

    function get_focus_keyword($wp_id) {
	$sql = "SELECT * FROM wp_postmeta WHERE post_id = '$wp_id' AND meta_key = '_yoast_wpseo_focuskw_text_input'";

	$query = $this->db_wp->query($sql);
	$results = $query->result_array();

	return $results[0]['meta_value'];
    }

    function get_meta_desc($wp_id) {
	$sql = "SELECT * FROM wp_postmeta WHERE post_id = '$wp_id' AND meta_key = '_yoast_wpseo_metadesc'";

	$query = $this->db_wp->query($sql);
	$results = $query->result_array();

	return $results[0]['meta_value'];
    }

    function get_file_name($wp_id) {
	$sql1 = "SELECT * FROM wp_postmeta WHERE post_id = '$wp_id' AND meta_key = '_thumbnail_id'";
	$query1 = $this->db_wp->query($sql1);
	$results1 = $query1->result_array();

	$file_id = $results1[0]['meta_value'];

	$sql2 = "SELECT * FROM wp_postmeta WHERE post_id = '$file_id' AND meta_key = '_wp_attached_file'";
	$query2 = $this->db_wp->query($sql2);
	$results2 = $query2->result_array();

	return $results2[0]['meta_value'];
    }

    function add_entry_in_workbook($prefix, $keyword, $suffix) {
	$data = array('Prefix' => $prefix, 'Keyword' => $keyword, 'suffix' => $suffix, 'title' => $prefix . $keyword . $suffix, 'Status' => "not done", 'Sitemap' => "not done");
	$this->db->insert('workbook2', $data);
    }

    function get_category($post_id) {
	$query1 = $this->db_wp->get_where('wp_term_relationships', array('object_id' => $post_id));
	$results1 = $query1->result_array();

	$number_of_categories = sizeof($results1);

	for ($i = 0; $i < $number_of_categories; $i++) {
	    $term_taxonomy_id = $results1[$i]['term_taxonomy_id'];
	    $query2 = $this->db_wp->get_where('wp_term_taxonomy', array('term_taxonomy_id' => $term_taxonomy_id));
	    $results2 = $query2->result_array();

	    $term_id = $results2[0]['term_id'];
	    $is_parent = 1 - $results2[0]['parent'];

	    if ($is_parent) {
		$query3 = $this->db_wp->get_where('wp_terms', array('term_id' => $term_id));
		$results3 = $query3->result_array();

		$category = $results3[0]['name'];
		return $category;
	    }
	}
    }

}
