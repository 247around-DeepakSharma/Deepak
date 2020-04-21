<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

class Wp_blog extends CI_Controller {

    /**
     * load list modal and helpers
     */
    function __Construct() {
        parent::__Construct();
        $this->load->model('blogs_model');
    }

    function test() {
        echo "Controller " . __CLASS__ . " working fine..." . PHP_EOL;
    }

    function update_sitemap() {
        // Your SiteMap URL
        $sm_urls = array(
            "http://247around.com/sitemap.xml",
            "http://www.247around.com/sitemap.xml",
        );

        // Search Engine URLs
        $se_list = array(
            "http://www.google.com/webmasters/sitemaps/ping?sitemap=",
            //"http://www.bing.com/webmaster/ping.aspx?siteMap=",
            //"http://search.yahooapis.com/SiteExplorerService/V1/updateNotification?appid=YahooDemo&url="
        );

        // Ping Them!
        foreach ($se_list as $i) {
            foreach ($sm_urls as $url) {
                $url_to_ping = $i . $url;
                $data = file_get_contents($url_to_ping);
                if ($data) {
                    echo $data;
                } else {
                    echo "Failed loading from " . $url_to_ping;
                }

                echo " \n <br> \r\n ";
            }
        }
    }

    function nl2para($string) {
        $string = str_replace(array('<p>', '</p>', '<br>', '<br />'), '', $string);

        // It is conceivable that people might still want single line-breaks
        // without breaking into a new paragraph.
	return '<p>' . preg_replace(
		array("/([\n]{2,})/i", "/([\r\n]{3,})/i", "/([^>])\n([^<])/i"), array("</p>\n<p>", "</p>\n<p>", '$1<br>$2'), trim($string)) . '</p>';
    }

    function blog_update_required($new, $old) {
        $result = 0;

        $new_post_author = $this->blogs_model->get_author($new['post_author']);

        if (($new['post_title'] != $old['title']) ||
            ($new['post_name'] != $old['url']) ||
            ($new_post_author != $old['author']) ||
            ($this->nl2para($new['post_content']) != $old['content'])) {
            echo 'Mismatch 1 found' . PHP_EOL;
            $result = 1;
        }

        $new_kw = $this->blogs_model->get_focus_keyword($new['ID']);
        $old_kw = $old['keyword'];

        $new_meta = $this->blogs_model->get_meta_desc($new['ID']);
        $old_meta = $old['description'];

        if (($new_kw != $old_kw) || ($new_meta != $old_meta)) {
            echo 'Mismatch 2 found' . PHP_EOL;
            $result = 1;
        }

        $new_file_path = $this->blogs_model->get_file_name($new['ID']);
        $new_file = basename($new_file_path);
        $old_file = $old['file_input'];

        if ($new_file != $old_file) {
            echo 'Mismatch 3 found' . PHP_EOL;
            $result = 1;
        }

        return $result;
    }

    function validate_blog($blog) {
        $result = 1;

        if ($blog['post_title'] == '') {
            echo 'Post title empty' . PHP_EOL;
            $result = 0;
        }

        if ($blog['post_name'] == '') {
            echo 'Post URL empty' . PHP_EOL;
            $result = 0;
        }

        if ($blog['post_author'] == '') {
            echo 'Post author empty' . PHP_EOL;
            $result = 0;
        }

        if ($blog['post_content'] == '') {
            echo 'Post content empty' . PHP_EOL;
            $result = 0;
        }

        if ($this->blogs_model->get_focus_keyword($blog['ID']) == '') {
            echo 'Post keyword empty' . PHP_EOL;
            $result = 0;
        }

        if ($this->blogs_model->get_meta_desc($blog['ID']) == '') {
            echo 'Post description empty' . PHP_EOL;
            $result = 0;
        }

        return $result;
    }

    function update_workbook_table($keyword) {

	$prefix = "";
	$suffix = "";
	$key = "";

	$count1 = strpos($keyword, '>');
	if ($count1 > 0) {
	    $prefix = substr($keyword, 0, $count1 + 1);
	    echo "prefix is " . $prefix . PHP_EOL;
	} else
	    echo "No prefix exists..";

	$count2 = strpos($keyword, '<', $count1);
	$len_of_suffix = 0;
	if ($count2 > 0) {
	    $len_of_suffix = strlen($keyword) - $count2;
	    $suffix = substr($keyword, $count2, $len_of_suffix);
	    echo "suffix is " . $suffix . PHP_EOL;
	} else
	    echo "No suffix exists";

	$len_of_keyword = strlen($keyword) - $count1 - $len_of_suffix;
	$key = substr($keyword, $count1 + 1, $len_of_keyword - 1);

	echo "key is " . $key . PHP_EOL;

	//function must be called to insert the entry in the workbook table
	if ($prefix != "" || $suffix != "") {
	    echo "updating workbook table\n";
	    $this->blogs_model->add_entry_in_workbook($prefix, $key, $suffix);
	} else
	    echo "No update required in workbook table\n";
    }

    //Check whether a URL is valid or not
    function check_url2($url) {
	$headers = @get_headers($url);
	$headers = (is_array($headers)) ? implode("\n ", $headers) : $headers;

	return (bool) preg_match('#^HTTP/.*\s+[(200|301|302)]+\s#i', $headers);
    }

    function index() {
	$updates_done = 0;
        $new_blogs = $this->blogs_model->find_blogs_to_update();

        foreach ($new_blogs as $new_blog) {
            $blogs = $this->blogs_model->get_blog_by_wpid($new_blog['ID']);

            if (count($blogs) > 0) {
                //blog exists, update it
                echo 'Blog "' . $new_blog['post_title'] . '" exists, updating...' . PHP_EOL;

                if ($this->blog_update_required($new_blog, $blogs[0])) {
                    echo 'Blog update required...' . PHP_EOL;

                    if ($this->validate_blog($new_blog)) {
                        echo 'Blog validated, update can be done' . PHP_EOL;

                        //update blog
                        $blog['title'] = $new_blog['post_title'];
                        $blog['url'] = $new_blog['post_name'];
                        $blog['author'] = $this->blogs_model->get_author($new_blog['post_author']);
                        $blog['content'] = $this->nl2para($new_blog['post_content']);
                        $blog['keyword'] = $this->blogs_model->get_focus_keyword($new_blog['ID']);
                        $blog['description'] = $this->blogs_model->get_meta_desc($new_blog['ID']);
                        $blog['category'] = $this->blogs_model->get_category($new_blog['ID']);

			echo "category is: " . $blog['category'] . PHP_EOL;

			$file_path = $this->blogs_model->get_file_name($new_blog['ID']);
                        $file_name = basename($file_path);
                        $blog['file_input'] = $file_name;
                        $blog['published'] = '1';

                        //update workbook table
                        //$this->update_workbook_table($blog['keyword']);
			//download file as well
                        $src_path = 'http://i2.wp.com/blog.247around.com/wp-content/uploads/' . $file_path;

                        //Remote path
                        $dest_path = '/var/www/247around.com/public_html/images/' . $file_name;

			//Local path
			//$dest_path = '/Applications/MAMP/htdocs/247Around/images/' . $file_name;

			$cmd = "curl $src_path -o $dest_path";
			//exec($cmd);

			$blog['alternate_text'] = $this->blogs_model->get_focus_keyword($new_blog['ID']);

                        $this->blogs_model->update_blog($new_blog['ID'], $blog);

                        echo 'Updated successfully...' . PHP_EOL . PHP_EOL;

                        $updates_done++;
                        unset($blog);
                    } else {
                        echo 'Blog validation failed, update skipped...' . PHP_EOL;
                    }
                } else {
                    echo 'No update required' . PHP_EOL;
                }
            } else {
                //blog doesn't exist, insert it
                echo 'Blog "' . $new_blog['post_title'] . '" does not exist, inserting...' . PHP_EOL;

                if (!$this->validate_blog($new_blog)) {
                    echo 'Some issue in blog, skipped' . PHP_EOL . PHP_EOL;
                } else {
                    $blog['wp_id'] = $new_blog['ID'];
                    $blog['title'] = $new_blog['post_title'];
                    $blog['url'] = $new_blog['post_name'];
                    $blog['author'] = $this->blogs_model->get_author($new_blog['post_author']);

                    $blog['content'] = $this->nl2para($new_blog['post_content']);

                    $blog['keyword'] = $this->blogs_model->get_focus_keyword($new_blog['ID']);
                    $blog['description'] = $this->blogs_model->get_meta_desc($new_blog['ID']);
		    $blog['category'] = $this->blogs_model->get_category($new_blog['ID']);

		    echo "category is: " . $blog['category'] . PHP_EOL;

		    $file_path = $this->blogs_model->get_file_name($new_blog['ID']);
                    $file_name = basename($file_path);
                    $blog['file_input'] = $file_name;

                    $blog['published'] = '1';

		    //update workbook table
		    //$this->update_workbook_table($blog['keyword']);
		    //download file as well
		    $src_path = 'http://i2.wp.com/blog.247around.com/wp-content/uploads/' . $file_path;

                    //Remote path
                    $dest_path = '/var/www/247around.com/public_html/images/' . $file_name;

		    //Local path
                    //$dest_path = '/Applications/MAMP/htdocs/247Around/images/' . $file_name;

		    $cmd = "curl $src_path -o $dest_path";
		    //exec($cmd);

		    $blog['alternate_text'] = $this->blogs_model->get_focus_keyword($new_blog['ID']);

                    $this->blogs_model->add_blog($blog);

                    echo 'Inserted successfully...' . PHP_EOL . PHP_EOL;

                    $updates_done++;
                    unset($blog);
                }
            }
        }

        if ($updates_done) {
            //1+ update/insert happened, update sitemap.xml and inform Search Engines
        }
    }

    function add_new_blog($blog_id) {
	$new_blog = $this->blogs_model->get_blog_from_wp_table($blog_id);
	//print_r($new_blog);
	//blog doesn't exist, insert it
	echo 'Inserting New Blog: "' . $new_blog['post_title'] . PHP_EOL;

	if (!$this->validate_blog($new_blog)) {
	    echo 'Some issue in blog, skipped' . PHP_EOL . PHP_EOL;
	} else {
	    $blog['wp_id'] = $new_blog['ID'];
	    $blog['title'] = $new_blog['post_title'];
	    $blog['url'] = $new_blog['post_name'];
	    $blog['author'] = $this->blogs_model->get_author($new_blog['post_author']);

	    $blog['content'] = $this->nl2para($new_blog['post_content']);

	    $blog['keyword'] = $this->blogs_model->get_focus_keyword($new_blog['ID']);
	    $blog['description'] = $this->blogs_model->get_meta_desc($new_blog['ID']);
	    $blog['category'] = $this->blogs_model->get_category($new_blog['ID']);

	    echo "category is: " . $blog['category'] . PHP_EOL;

	    $file_path = $this->blogs_model->get_file_name($new_blog['ID']);
	    $file_name = basename($file_path);
	    $blog['file_input'] = $file_name;

	    $blog['published'] = '1';
	    $blog['is_master'] = '0';
	    $blog['is_template'] = '0';

	    //download file as well
	    $src_path = 'http://i2.wp.com/blog.247around.com/wp-content/uploads/' . $file_path;

	    //Remote path
	    $dest_path = '/var/www/247around.com/public_html/images/' . $file_name;
	    //Local path
	    //$dest_path = '/Applications/MAMP/htdocs/247Around/images/' . $file_name;

	    $cmd = "curl $src_path -o $dest_path";
	    exec($cmd);

	    $blog['alternate_text'] = $this->blogs_model->get_focus_keyword($new_blog['ID']);

	    $id = $this->blogs_model->add_blog($blog);

	    echo 'Inserted successfully...' . PHP_EOL . PHP_EOL;

	    //Add this new blog entry in URL table as well
	    $new_url = array('url' => $blog['url'], 'brand' => '', 'place' => '', 'blog_id' => $id);
	    $this->blogs_model->add_url($new_url);

	    exit(0);
	}
    }

    function check_all_links_from_blogs_content() {
	$data = $this->blogs_model->view_blogs();

	//$links = array();

	foreach ($data as $row) {
	    //find all links in content & check their validity
	    //preg_match_all('/\bhttps?:\/\/\S+(?:jpg|jpeg|png|gif|tif|exf|svg|wfm)\b/i', $row['content'], $matches);
	    preg_match_all('#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#', $row['content'], $matches);
	    //var_dump($matches[0]);
	    if (!empty($matches[0])) {
		foreach ($matches[0] as $link) {
		    //array_push($links, $link);
		    //check for broken link
		    if ($this->check_url2($link)) {
			//echo "Link Works" . "\n";
		    } else {
			//echo "Broken Link: " . $link . "\n";
			echo $row['id'] . "," . $row['url'] . "," . $link . "\n";
		    }
		}
	    }

	    echo ".\n";
	}

	//return $links;
	exit(0);
    }

}
