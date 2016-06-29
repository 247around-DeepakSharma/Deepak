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

    /*
     * @desc: This function shows all the blogs present
     * @param: void     
     * @return: array of all the blogs
     */
    function view_blogs() {
	$sql = "SELECT * FROM blogs";

	$query = $this->db->query($sql);

	return $query->result_array();
    }

    /*
     * @desc: This function shows blog details with their wordpress id
     * @param: $wp_id         
     * @return: details of particular blog
     */
    function get_blog_by_wpid($wp_id) {
	$sql = "SELECT * FROM blogs WHERE wp_id = '$wp_id'";

	$query = $this->db->query($sql);
	$results = $query->result_array();

	return $results;
    }

    /*
     * @desc: This function searches/finds blog with the matching keyword
     * @param: $kw - keyword used to search blog   
     * @return: array of blogs with matching keywords
     */
    function get_blog_by_keyword($kw) {
	$this->db->where('keyword', $kw);
	$query = $this->db->get('blogs');

	return $query->result_array();
    }

    /*
     * @desc: This function is to add a new blog
     * @param: $blog- details of blog to be added.
     * @return: the id of particular blog after insertion     
     */
    function add_blog($blog) {
	$this->db->insert('blogs', $blog);

	return $this->db->insert_id();
    }

     /*
     * @desc: This function is to add url of the blog along with blog id
     * @param: $url- url to be inserted.
     * @return: void
     */
    function add_url($url) {
	$this->db->insert('url_table', $url);
    }

    /*
     * @desc: This function is to update an existing blog
     * @param: $wp_id- wordpress id of the blog to be updated
     * @param: $blog- blog contents/ data to be updated.
     * @return: void
     */
    function update_blog($wp_id, $blog) {
	$this->db->where(array('wp_id' => $wp_id));
	$this->db->update('blogs', $blog);
    }

    /*
     * @desc: This function is to copy the blogs from wordpress to our blogs table
     * @param: $blog- blog contents/ data to be inserted.
     * @return: void
     */
    function copy_blog_from_wordpress($blog) {
	$this->db->insert('blogs', $blog);
    }

    /*
     * @desc: This function is to get details of the blog to be edited
     * @param: $id- id of the blog to be edited
     * @return: blog details
     */
    function editblog($id) {
	$sql = "SELECT * FROM blogs WHERE id='$id'";

	$query = $this->db->query($sql);

	return $query->result_array();
    }

    /*
     * @desc: This function edits the selected blogs with edited details
     * @param: $blog- details of blog to finally edit
     * @return: void
     */
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

    /*
     * @desc: This function is to publish(make it live/active) a particular blog
     * 
     * Its done by updating published field to 1.
     * 
     * @param: $id- id of the blog to be published
     * @return: void
     */
    function publish($id) {
	$sql = "UPDATE blogs SET published = 1 WHERE id='$id'";
//	$query = $this->db->query($sql);    // $query is unused
        $this->db->query($sql);
    }

    /*
     * @desc: This function is to unpublish(make it inactive) a particular blog
     * 
     * Its done by updating published field to 0.
     * 
     * @param: $id- id of the blog to be unpublished
     * @return: void
     */
    function unpublish($id) {
	$sql = "UPDATE blogs SET published = 0 WHERE id='$id'";
//	$query = $this->db->query($sql);    // $query is unused
        $this->db->query($sql);
    }

    /*
     * @desc: This function is to delete a particular blog
     * @param: $id- id of the blog to be deleted
     * @return: void
     */
    function delete($id) {
	$sql = "Delete FROM blogs WHERE id='$id'";
//	$query = $this->db->query($sql);    // $query is unused
        $this->db->query($sql);
    }

    /*
     * @desc: This function is to finds the blogs to be updated
     * 
     *  This search is on the basis of posting date of blog and where post status of blog is pending
     * 
     * @param: void
     * @return: blogs to be updated
     */
    function find_blogs_to_update() {
	$sql = "SELECT * FROM  `wp_posts` WHERE `post_date` > '2016-01-13 00:00:00'
                AND `post_status` LIKE 'pending'";
	$query = $this->db_wp->query($sql);

	return $query->result_array();
    }

    /*
     * @desc: This function is to find the blog from wordpress table     
     * @param: $blog_id- the id of the blog to be selected
     * @return: atrray of blog details
     */
    function get_blog_from_wp_table($blog_id) {
	$sql = "SELECT * FROM  `wp_posts` WHERE ID=$blog_id";
	$query = $this->db_wp->query($sql);

	return $query->result_array()[0];
    }

    /*
     * @desc: This function is to get details of a particular author
     * @param: $auth_id - id of the author of which we want details
     * @return: name of the author
     */
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
    
    /*
     * @desc: This function is to get file name of particulat wordpress id
     * 
     * This file is the image(most of times) which is a part of the particular blog.
     * 
     * @param: $wp_id - its the id of the wordpress of which we want file name.
     * @return: name of the file
     */
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

    /*
     * @desc: This function is to add entry in our workbook
     *      
     * @param: $prefix - the prefix of the data
     * @param: $keyword - the keyword of the blog
     * @param: $suffix - the suffix of the data
     * @return: void
     */
    function add_entry_in_workbook($prefix, $keyword, $suffix) {
	$data = array('Prefix' => $prefix, 'Keyword' => $keyword, 'suffix' => $suffix, 'title' => $prefix . $keyword . $suffix, 'Status' => "not done", 'Sitemap' => "not done");
	$this->db->insert('workbook2', $data);
    }

    /*
     * @desc: This function is to get the category of a post.
     * @param: $post_id - id of the post
     * @return: category
     */
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
