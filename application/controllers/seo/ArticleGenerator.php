<?php

/*
 * READ THIS FIRST:
 *
 * How this script works:
 *
 * This script relies on the workbook2 table which has prefix, keyword, suffix,
 * title and service name as main columns. Keyword is used to find the template
 * in blogs table, it should  match with blogs.keyword column. Column active in
 * workbook2 table decides whether articles for this row needs to be generated
 * or not.
 *
 * Once the script is fired, it looks for all the active templates. For each
 * active template, it finds out the service name and the brands associated with
 * the service. It then finds the template blog from the blogs table. Once it is
 * found, the script generates all possible url combinations.
 *
 * Assumption:
 *
 * 1. All keywords in workbook table should have " " in beginning and end depending on
 * prefix and suffix. If prefix is there, there should be " " in beginning. If
 * suffix is there, there should be " " in end. If both are there, " " should
 * be there at both the ends. THIS NEEDS TO BE FIXED.
 *
 * 2.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

//For infinite memory
ini_set('memory_limit', '-1');

//For timeout
ini_set('max_execution_time', 3600);

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

class ArticleGenerator extends CI_Controller {

    //Count of the last sitemap file
    private $last_sitemap;
    //Master sitemap file pointer
    private $master_sitemap;

    function __Construct() {
	parent::__Construct();

	$this->load->model('booking_model');
	$this->load->model('blogs_model');
	$this->load->model('url_model');
    }

    //To run, type: php index.php seo/ArticleGenerator check hi
    public function check($arg = "") {
	echo __METHOD__ . ': Check with argument (' . $arg . ') Passed' . PHP_EOL;
    }

    /**
     * This command generates all the articles with brands and places replaced
     * with the actual names.
     */
    public function index() {
	log_message('info', 'Entering: ' . __METHOD__);

	//prepare all arrays that will be required later
	$regions = $this->booking_model->get_distinct_place('Region');
	log_message('info', 'Regions: ' . count($regions));
	$pincodes = $this->booking_model->get_distinct_place('Pincode');
	log_message('info', 'Pincodes: ' . count($pincodes));
	$areas = $this->booking_model->get_distinct_place('Area');
	log_message('info', 'Areas: ' . count($areas));
	$cities = $this->booking_model->get_distinct_place('City');
	log_message('info', 'Cities: ' . count($cities));

	//Get all the ACTIVE templates from workbook table for which articles need
	//to be generated.
	//TODO: there should be a way to select few templates in the workbook
	$templates_data = $this->url_model->get_url_template_with_prefix_suffix();
	$urls = $templates_data[0];
	$services = $templates_data[1];
	log_message('info', 'URL Templates: ' . count($urls));

	//$keywords = $this->url_model->get_keyword();
	$res = "";
	$total_rows_inserted = 0;

	//This loop runs for all entries in workbook table
	for ($k = 0; $k < count($urls); $k++) {
	    //initialising local variables of this loop
	    $col = "";
	    $num_rows_inserted = 0;
	    $suffix_size = 1;

	    $appliance_id = $this->booking_model->getServiceId($services[$k]);
	    $brands = $this->booking_model->getBrandForService($appliance_id);
	    log_message('info', 'Brands: ' . count($brands));

	    //check if the $urls[$k] have is_tempate flag ON in the blogs table or not
	    //by joining through keyword of blogs table and generated url from workbook table
	    echo "<-- WORKING on url number " . $k . " " . $urls[$k] . "  -->\n";

	    $url = $urls[$k];
	    //echo 'URL: ' . $url . PHP_EOL;
	    //$sql = "SELECT * FROM blogs WHERE keyword='$url'";
	    //$result = mysqli_query($db, $sql);
	    //$first_row = mysqli_fetch_assoc($result);
	    //Find blog by keyword
	    $first_row = $this->blogs_model->get_blog_by_keyword($url);
	    $is_template = intval($first_row[0]['is_template']);
	    $id = $first_row[0]['id'];
	    //replace " " by "-"
	    $urls[$k] = str_replace(" ", "-", $urls[$k]);
	    $sql = "('$urls[$k]','<brand>','<suffix>','$id',CURRENT_TIMESTAMP),";

	    //if the url has a blog of template type, then copies of url is generated otherwise not
	    if ($is_template) {

		echo "Template exists for this URL.\n ";
		//this is to check whether url has prefix(<brand>) or not
		if (strpos($urls[$k], "<brand>") !== false) {
		    echo "Prefix exists for this URL\n " . sizeof($brands) . "  number of prefixes exists.\n ";

		    if ($this->is_suffix_exists($urls[$k])) {
			if (strpos($urls[$k], "<place>") !== false) {
			    $col = "<place>";
			    $suffix_size = sizeof($places);
			} else if (strpos(strtolower($urls[$k]), "<region>") !== false) {
			    $col = "<region>";
			    $suffix_size = sizeof($regions);
			} else if (strpos(strtolower($urls[$k]), "<area>") !== false) {
			    $col = "<area>";
			    $suffix_size = sizeof($areas);
			} else if (strpos(strtolower($urls[$k]), "<pincode>") !== false) {
			    $col = "<pincode>";
			    $suffix_size = sizeof($pincodes);
			} else if (strpos(strtolower($urls[$k]), "<city>") !== false) {
			    $col = "<city>";
			    $suffix_size = sizeof($cities);
			}

			echo "  " . $col . " suffix also exists in this URL\n    " . $suffix_size .
			" number of suffixes exists for this suffix\n";
		    } else {
			echo "  But Suffix doesn't exist in the URL\n ";
		    }

		    //echo sizeof($brands)*$suffix_size." number of URLs must be inserted\n";
		    //if prefix exists then for every specific prefix urls must be generated
		    for ($i = 0; $i < sizeof($brands); $i++) {
			$res = "INSERT IGNORE INTO `url` (`url`, `brand`, `place`, `blog_id`, `create_date`) VALUES ";
			if ($this->is_suffix_exists($urls[$k])) {
			    //echo "prefix and suffix both exists";
			    //this part will executed if url contains both prefix and suffix
			    //ex= <brand>-tv-repair-in-pincode
			    if (strpos($urls[$k], "<place>") !== false)
				$res = $this->make_copies_of_url($brands[$i]['brand_name'], $places, "<place>", $res, $sql, $urls[$k]);
			    else if (strpos(strtolower($urls[$k]), "<region>") !== false)
				$res = $this->make_copies_of_url($brands[$i]['brand_name'], $regions, "<region>", $res, $sql, $urls[$k]);
			    else if (strpos(strtolower($urls[$k]), "<area>") !== false)
				$res = $this->make_copies_of_url($brands[$i]['brand_name'], $areas, "<area>", $res, $sql, $urls[$k]);
			    else if (strpos(strtolower($urls[$k]), "<pincode>") !== false)
				$res = $this->make_copies_of_url($brands[$i]['brand_name'], $pincodes, "<pincode>", $res, $sql, $urls[$k]);
			    else if (strpos(strtolower($urls[$k]), "<city>") !== false)
				$res = $this->make_copies_of_url($brands[$i]['brand_name'], $cities, "<city>", $res, $sql, $urls[$k]);
			}
			else {
			    //this part will executed if url contains prefix only and there are no suffix exists in the url
			    //ex= <brand>-customer-care
			    $sql_url = $this->get_final_url($brands[$i]['brand_name'], "", $sql, "");
			    $res = $res . $sql_url;

			    $final_url = $this->get_final_url($brands[$i]['brand_name'], "", $urls[$k], "");
			    $this->update_sitemap($final_url);
			}

			//for optimisation all the insert queries are concatenated in the $res variable which is
			//executed at once now
			$res[strlen($res) - 1] = ";";
			$this->url_model->execute_sql($res);
			//$stat = mysqli_query($db, $res)or die(mysqli_error($db));
			//
			//count no of entries inserted by counting "(" in the $res string
			//subtract 1 for additional "(" in the insert query
			$num_rows_inserted += (substr_count($res, "(") - 1);
			//$num_rows_inserted+=mysqli_affected_rows($db);
		    }
		} else if ($this->is_suffix_exists($urls[$k])) {
		    //echo "Prefix doesnot exist, only suffix exists for this URL\n";
		    //when there is no brand but have places
		    //ex= tv-repair-in-<place>
		    $res = "INSERT IGNORE INTO `url` (`url`, `brand`, `place`, `blog_id`, `create_date`) VALUES ";
		    if (strpos($urls[$k], "<place>") !== false)
			$res = $this->make_copies_of_url("", $places, "<place>", $res, $sql, $urls[$k]);
		    else if (strpos(strtolower($urls[$k]), "<region>") !== false)
			$res = $this->make_copies_of_url("", $regions, "<region>", $res, $sql, $urls[$k]);
		    else if (strpos(strtolower($urls[$k]), "<area>") !== false)
			$res = $this->make_copies_of_url("", $areas, "<area>", $res, $sql, $urls[$k]);
		    else if (strpos(strtolower($urls[$k]), "<pincode>") !== false)
			$res = $this->make_copies_of_url("", $pincodes, "<pincode>", $res, $sql, $urls[$k]);
		    else if (strpos(strtolower($urls[$k]), "<city>") !== false)
			$res = $this->make_copies_of_url("", $cities, "<city>", $res, $sql, $urls[$k]);

		    //for optimisation all the insert queries are concatenated in the $res variable
		    //which is executed at once now
		    $res[strlen($res) - 1] = ";";
		    //Execute this SQL query directly
		    $this->url_model->execute_sql($res);

		    //echo $res;
		    //count no of entries inserted by counting "(" in the $res string
		    //subtract 1 for additional "(" in the insert query
		    $num_rows_inserted += (substr_count($res, "(") - 1);
		    //$num_rows_inserted = mysqli_affected_rows($db);
		}

		if ($num_rows_inserted > 0) {
		    echo $num_rows_inserted . " rows inserted for this URL.\n";
		} else
		    echo "ERROR ocurred while inserting URLs for this template.\n";

		if ($num_rows_inserted == sizeof($brands) * $suffix_size) {
		    echo "URLs successfully generated\n";
		}

		$total_rows_inserted += $num_rows_inserted;
	    } else {
		echo "Template doesn't exist for this URL.\n No rows added.\n";
	    }

	    echo "\n";
	}

	//Inserting urls of blogs table which are not in template form in the url table
	//TODO: It doesnot create any sitemap as of now.
	echo "\nInserting urls of blogs table which are not in template form in the url table\n\n";
	$non_template_blogs = $this->url_model->add_non_template_blogs();

	if ($non_template_blogs > 0)
	    echo "Added $non_template_blogs blogs which are not templates\n\n";
	else
	    echo "Added 0 blogs which are not templates\n\n";

	$total_rows_inserted += $non_template_blogs;

	//Make sure the sitemaps are closed properly
	echo "Last sitemap: " . $this->last_sitemap . "\n";
	//write closing tag in the last sitemap file
	$last_sitemap_name = __DIR__ . "/sitemap-" . $this->last_sitemap . ".xml";
	$f_last_sitemap = fopen($last_sitemap_name, "a");

	if ($f_last_sitemap !== FALSE) {
	    fwrite($f_last_sitemap, '</urlset>');
	} else {
	    echo "\n\nUnable to write closing tag in the last sitemap file\n\n";
	    exit(-1);
	}

	//write closing tag in the sitemap index file
	$this->master_sitemap = fopen(__DIR__ . "/sitemap.xml", "a");
	if ($this->master_sitemap !== FALSE) {
	    fwrite($this->master_sitemap, '</sitemapindex>');
	} else {
	    echo "\n\nUnable to write closing tag in the sitemap index file\n\n";
	    exit(-2);
	}

	//Everything done, now switch tables
	$this->url_model->switch_url_tables();

	echo "\nSwitching of Tables Complete........\n";

	echo "\n\n******TOTAL " . $total_rows_inserted . " rows are inserted in the URL table.******\n\n";
    }

    /**
     * @desc: Returns string after replacing prefix placeholder (<brand> in our case) and
     * suffix placeholder (<city>/<region> etc) in the input string.
     *
     * $param String	$prefix	    Prefix string. It is Brand name currently.
     * $param String	$suffix	    Suffix string. It is a place name.
     * $param String	$string	    String in which these need to be replaced.
     *				    For e.g.
     * "('<brand>-refrigerator-repair-in-<city>','<brand>','<suffix>','1212',CURRENT_TIMESTAMP),"
     *
     * $param String	$to_replace Contains place-type to be replaced like <city> in case of generating
     * urls from cities.
     *
     * @return String String after the replacements
     *
     */
    function get_final_url($prefix, $suffix, $string, $to_replace) {
	log_message('info', 'Entering: ' . __METHOD__);

	//these chars should not come in URL and sitemap.xml
	$chars_to_be_ignored = array('(', ')', ',', '/', '?', '&', '"', '\'');

	$suffix = str_replace($chars_to_be_ignored, "", $suffix);
	$to_replace = str_replace($chars_to_be_ignored, "", $to_replace);

	$suffix = str_replace("&", "and", $suffix);
	$to_replace = str_replace("&", "and", $to_replace);

	$res_string = str_replace("<suffix>", str_replace(array(" ", "."), "-", $suffix), strtolower($string));
	$res_string = str_replace($to_replace, str_replace(array(" ", "."), "-", $suffix), $res_string);
	$res_string = str_replace("<-in-'$to_replace'>", str_replace(" ", "-", "-in-" . $suffix), $res_string);

	$res_string = str_replace("<brand->", str_replace(" ", "-", $prefix . "-"), $res_string);
	$res_string = str_replace("<brand>", str_replace(" ", "-", $prefix), $res_string);

//	log_message('info', 'Final URL: ' . $res_string);

	return $res_string;
    }

    /**
     * *this function is used to check if suffix exists or not
     * *$url is of the form $prefix.$keyword.$suffix
     * *so if a valid suffix exists like pincode/region/city, it returns true
     * */
    function is_suffix_exists($url) {
	return ((strpos($url, "<place>") !== false) ||
	    (strpos($url, "<region>") !== false) ||
	    (strpos($url, "<area>") !== false) ||
	    (strpos($url, "<pincode>") !== false) ||
	    (strpos($url, "<city>") !== false));
    }

    /**
     * This function takes a brand and an array of places as input and makes
     * all possible combinations of that brand and all places. These combos
     * would then be inserted in the URL table and in the sitemap files.
     * It returns the sql query string which would be used to insert these new
     * URLs in the table. It also inserts all these new URLs in sitemap files
     * as well.
     *
     * @param String	$brand	    Brand name
     * @param Array	$suffixes   Array having all places names
     * @param String	$to_replace Place type to replaced. Values could be:
     *				    <city>, <area> etc.
     * @param String	$res	    SQL initial INSERT query string. This would
     *				    be populated with all the values and returned back
     *				    so that these values can be added in the URL table.
     *				    For e.g.:
     * "INSERT IGNORE INTO `url` (`url`, `brand`, `place`, `blog_id`, `create_date`) VALUES "
     *
     * @param String	$sql	    SQL INSERT query string with the values. Values have
     *				    placeholders in them which would be replaced with actual places.
     * String replaced after the replacement would be appended in the $res string.
     * For e.g.:
     * "('<brand>-refrigerator-repair-in-<city>','<brand>','<suffix>','1212',CURRENT_TIMESTAMP),"
     *
     * @param String	$url	    URL with placeholders which would be replaced with
     *				    actual values and then would be added in the sitemap
     * files. For e.g.:
     * "<brand>-refrigerator-repair-in-<city>"
     *
     * @return String SQL query to be used to insert new URLs in URL table.
     *
     */
    function make_copies_of_url($brand, $suffixes, $to_replace, $res, $sql, $url) {
	//log_message('info', 'Entering: ' . __METHOD__);

	$column = str_replace(array("<", ">"), "", $to_replace);

	//TODO:
	switch ($column) {
	    case 'city':
		$column = "City";
		break;

	    case 'region':
		$column = "Region";
		break;

	    case 'pincode':
		$column = "Pincode";
		break;

	    case 'area':
		$column = "Area";
		break;

	    default:
		break;
	}

	for ($j = 0; $j < sizeof($suffixes); $j++) {

	    $sql_url = $this->get_final_url($brand, $suffixes[$j][$column], $sql, $to_replace);
	    $res = $res . $sql_url;

	    $final_url = $this->get_final_url($brand, $suffixes[$j][$column], $url, $to_replace);
	    $this->update_sitemap($final_url);
	}

	return $res;
    }

    /**
     * *function to update sitemap
     * *$sitemap is the the file pointer
     * *$url is the final url for which sitemap has to built
     * */
    function update_sitemap($url) {
	static $count = 0;
	static $prevfile = 0;
	static $curfile = 0;
	static $sitemap = '';
	//static $this->master_sitemap = '';
	//global $this->last_sitemap;

	$max = 50000;  //limit imposed by the w3c standard, max URLs in sitemap is 50000
	$prevfile = $curfile;
	$this->last_sitemap = $curfile;

	if ($count === 0) {
	    $this->master_sitemap = fopen(__DIR__ . "/sitemap.xml", "w") or die("Unable to open file!");
	    $sitemap = fopen(__DIR__ . "/sitemap-0.xml", "w") or die("Unable to open file!");

	    $section = <<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	<sitemap>
		<loc>http://www.247around.com/sitemap-fixed.xml</loc>
	</sitemap>
	<sitemap>
		<loc>http://www.247around.com/sitemap-blogs.xml</loc>
	</sitemap>
	<sitemap>
		<loc>http://www.247around.com/sitemap-0.xml</loc>
	</sitemap>
EOD;
	    fwrite($this->master_sitemap, $section . PHP_EOL);

	    $section = <<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<urlset
      xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
EOD;
	    fwrite($sitemap, $section . PHP_EOL);
	}

	$count++;

	$curfile = intval($count / $max);

	if ($curfile != $prevfile) {
	    //update sitemap index file
	    $section = <<<EOD
	<sitemap>
		<loc>http://www.247around.com/sitemap-$curfile.xml</loc>
	</sitemap>
EOD;
	    fwrite($this->master_sitemap, $section . PHP_EOL);

	    //close <urlset> tag in the previous sitemap file first
	    $urlset_close = '</urlset>';
	    fwrite($sitemap, $urlset_close);

	    //open new sitemap file
	    $sitemap = fopen(__DIR__ . "/sitemap-$curfile.xml", "w") or die("Unable to open file!");
	    $this->last_sitemap = __DIR__ . "/sitemap-$curfile.xml";

	    $section = <<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<urlset
      xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
EOD;
	    fwrite($sitemap, $section . PHP_EOL);
	}
	/*
	 * SITEMAP TEMPLATE
	  <url>
	  <loc>http://247around.com/washing-machine-repair</loc>
	  <changefreq>weekly</changefreq>
	  <priority>0.64</priority>
	  </url>
	 *
	 */
	$section1 = <<<EOD1
<url>
  <loc>http://247around.com/
EOD1;

	$section2 = <<<EOD2
</loc>
  <changefreq>weekly</changefreq>
  <priority>0.64</priority>
</url>
EOD2;
	fwrite($sitemap, $section1 . trim($url) . $section2 . PHP_EOL);
    }

}
