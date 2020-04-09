<?php

require_once __DIR__ . '/db_config.php';

// Connecting to mysql database
$db=mysqli_connect(DB_SERVER,DB_USER,DB_PASSWORD,DB_DATABASE);

/**
**get distinct brands from appliance brands table
**change this query for different service, right now it is for TV (46)
**/
function get_brands($db)
{
$data=mysqli_query($db,"SELECT DISTINCT(brand_name) FROM appliance_brands WHERE `service_id`=37 AND `seo`=1")or die("Error");

$brands=array();

while($row=mysqli_fetch_assoc($data))
array_push($brands,$row['brand_name']);
if($data!=false)
	echo "Readed ".sizeof($brands)." brands successfully\n\n";
else
	echo "Error ocurred while reading distinct brands from brands table\n\n";

return $brands;
}

/**
**get the sufixes from the vendor_mapping_table
**ex-SELECT DISTINCT region from vendor_mappin_table
**$col specifies which col we need to fetch like area,region,pincodess
**/
function get_suffix($db,$col)
{
	$sql="SELECT DISTINCT ".$col." FROM vendor_pincode_mapping";
	$data=mysqli_query($db,$sql);
	
	$suffixes=array();
	
	while($row=mysqli_fetch_assoc($data))
		array_push($suffixes,$row[$col]);

	if($data!=false)
		echo "Readed ".sizeof($suffixes)." ".$col." successfully\n\n";
	else
		echo "Error ocurred while reading ".$col." from vendor_pincode_mapping table\n\n";

	//print_r($suffixes);

	return $suffixes;
}

/**
**get the keywords from workbook
*/
function get_keywords($db)
{
	$data=mysqli_query($db,"SELECT Keyword FROM workbook2")or die(mysqli_error($db));

	$keywords=array();

	while($row =mysqli_fetch_assoc($data))
		array_push($keywords,$row['Keyword']);

	if($data!=false)
		echo "Readed ".sizeof($keywords)." keywords from workbook successfully\n\n";
	else
		echo "Error ocurred while reading keywords from workbook\n\n";

	return ($keywords);
}

/**
**make complete url template from workbook by adding prefix and suffix and push all urls
**in an array named as $urls
**/
function get_url($db)
{
$data=mysqli_query($db,"SELECT * FROM workbook2")or die(mysqli_error($db));

$urls=array();

while($row =mysqli_fetch_assoc($data))
{
	$prefix=$row['Prefix'];
	$keyword=$row['Keyword'];
	$suffix=$row['Suffix'];

	//keyword is added first because prefix and suffix may not be present 
	$url=$keyword;
	//prefix is added if exists
	if($prefix!=NULL)
		$url=$prefix.$url;
	
	//suffix is added if exists
	if($suffix!=NULL)
		$url=$url.$suffix;
	
	array_push($urls,$url);
}

if($data!=false)
	echo "Generated ".sizeof($urls)." from workbook after adding prefix and suffix in template form successfully\n\n";
else
	echo "Error ocurred while generating urls from workbook table\n\n";

return ($urls);
}

/**
 **make final url after adding proper prefix(which is brand in our case) and suffix(pincode-282001,region-indirapuram..)
 **$to_replace contains the word to be replaced like pincode in case of generating urls from pincode
 **/
function get_final_url($brand,$suffix,$string,$to_replace)
{
	$res_string="";

		//these chars should not come in URL and sitemap.xml
		$chars_to_be_ignored=array('(', ')', ',', '/', '?', '&', '"');
		
		$suffix = str_replace($chars_to_be_ignored, "", $suffix);
		$to_replace = str_replace($chars_to_be_ignored, "", $to_replace);
		
		$suffix = str_replace("&", "and", $suffix);
		$to_replace = str_replace("&", "and", $to_replace);
	

		$res_string = str_replace("<suffix>", str_replace(array(" ", "."), "-", $suffix), strtolower($string));
		$res_string = str_replace($to_replace, str_replace(array(" ", "."), "-", $suffix), $res_string);
		$res_string = str_replace("<-in-'$to_replace'>",str_replace(" ","-","-in-".$suffix), $res_string);	

		$res_string = str_replace("<brand->", str_replace(" ", "-", $brand."-"), $res_string);	
		$res_string = str_replace("<brand>",  str_replace(" ","-",$brand), $res_string);

	return ($res_string);
}

/**
**function to update sitemap
**$sitemap is the the file pointer 
**$url is the final url for which sitemap has to built
**/

function update_sitemap($url)
{
	static $count = 0;
	static $prevfile;
	static $curfile = 0;
	static $sitemap;
	static $master_sitemap;
	global $last_sitemap;

	$max = 50000;		//limit imposed by the w3c standard, max URLs in sitemap is 50000
	$prevfile = $curfile;
	$last_sitemap = $curfile;

	if($count===0)
	{
			$master_sitemap = fopen("sitemap.xml", "w") or die("Unable to open file!");
			$sitemap = fopen("sitemap-0.xml", "w") or die("Unable to open file!");
			
$section = <<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	<sitemap>
		<loc>http://www.247around.com/fixed-sitemap.xml</loc>
	</sitemap>
	<sitemap>
		<loc>http://www.247around.com/sitemap-0.xml</loc>
	</sitemap>
EOD;
			fwrite($master_sitemap, $section . PHP_EOL);
		
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
	
	$curfile = intval($count/$max);
	
	if($curfile != $prevfile)
	{
		//update sitemap index file
		$section = <<<EOD
	<sitemap>
		<loc>http://www.247around.com/sitemap-$curfile.xml</loc>
	</sitemap>
EOD;
		fwrite($master_sitemap, $section . PHP_EOL);
		 
		 //close <urlset> tag in the previous sitemap file first
		 $urlset_close = '</urlset>';
		 fwrite($sitemap, $urlset_close);

		 //open new sitemap file
		 $sitemap = fopen("sitemap-$curfile.xml", "w") or die("Unable to open file!");
		 $last_sitemap = "sitemap-$curfile.xml";

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
  <loc>https://247around.com/
EOD1;

    $section2 = <<<EOD2
</loc>
  <changefreq>weekly</changefreq>
  <priority>0.64</priority>
</url>
EOD2;
    fwrite($sitemap, $section1 . trim($url) . $section2 . PHP_EOL);
}

/**
**this function is used to check if suffix exists or not 
**$url is of the form $prefix.$keyword.$suffix
**so if a valid suffix exists like pincode,<place>,region,city it returns true
**/
function is_suffix_exists($url)
{
	return ((strpos($url,"<place>")!==false)||(strpos($url,"<region>")!==false)||(strpos($url,"<area>")!==false)||(strpos($url,"<pincode>")!==false)||(strpos($url,"<city>")!==false));
}

/**
**it is used to insert the urls of blogs table which 
**have is_template flag =0.
** 
**/
function insert_titles_of_non_template($db,$total_rows_inserted)
{
	echo "\nInserting urls of blogs table which are not in template form in the url table\n\n";
	$data=mysqli_query($db,"SELECT url,id FROM blogs where is_template=0")or die(mysqli_error($db));
 	
 	//intitialising local varibales
 	$title="";
	$id=0;

	echo mysqli_num_rows($data)." must be inserted\n";

	$res="INSERT IGNORE INTO `url_table_ref` (`url`, `brand`, `place`, `blog_id`, `create_date`) VALUES ";	
	while($row =mysqli_fetch_assoc($data))
	{
		$title=$row['url'];
		$id=$row['id'];
		
		$sql="('$title','','','$id',CURRENT_TIMESTAMP),";
		$res.=$sql;
			
		update_sitemap($title);
	}
	//terminate insert query
	$res[strlen($res)-1]=";";
	
	$data2=mysqli_query($db,$res)or die(mysqli_error($db));
	$updated_rows=mysqli_affected_rows($db);
	$total_rows_inserted+=$updated_rows;

	if($data2!=false)
		echo "Added ".$updated_rows." urls of blogs table which are not in template form in the url table\n\n";
	else
		echo "ERROR occured while adding urls of blogs table which are not in template form in the url table\n\n";

	return($total_rows_inserted);
}

/**
** this function makes copy of a url for a specific brand only ,update sitemap for it and return insert query for the same
** if url is <brand>-tv-repair-in-<place> then for a particular brand(through $brand parameter) say samsung,
** it will produce urls like samsung-tv-repair-in-indirapuram,samsung-tv-repair-in-abupur
**/
function make_copies_of_url($brand,$suffixes,$to_replace,$res,$sql,$url)
{
	
	for ($j=0; $j <sizeof($suffixes) ; $j++)
	{
		
		$sql_url=get_final_url($brand,$suffixes[$j],$sql,$to_replace);
		$res=$res.$sql_url;

		$final_url=get_final_url($brand,$suffixes[$j],$url,$to_replace);
		update_sitemap($final_url);

	}

	return $res;
}

//these two statements ensure that all work is undone form url_table_ref and status in workbook is set to "tobedone"
$delete=mysqli_query($db,"DELETE FROM url_table_ref")or die(mysqli_error($db));
if($delete!=false)
	echo mysqli_affected_rows($db)." rows are deleted from url_table_ref\n\n";
else 
	echo "Error ocurred while deleting rows from url_table_ref\n\n";


$update_status=mysqli_query($db,"UPDATE workbook2 SET status='to be done',sitemap='to be done'")or die(mysqli_error($db));
if($update_status!=false)
	echo "Status of ".mysqli_affected_rows($db)." rows is updated to undone in workbook table\n\n";
else
	echo "Error ocurred while updating status to undone in  workbook table\n\n";

//prepare all arrays that will be required by the script
$brands=get_brands($db);
$regions=get_suffix($db,"region");
$pincodes=get_suffix($db,"pincode");
$areas=get_suffix($db,"area");
$cities=get_suffix($db,"city");
$urls=get_url($db);
$keywords=get_keywords($db);
$res="";
$total_rows_inserted=0;
//store the last sitemap file which goes into the sitemap index file
$last_sitemap='';

//MAIN SCRIPT
//this loop runs for all entries in workbook table
for($k=0;$k<sizeof($urls);$k++) 
{
	//initialising local variables of this loop
	$col="";
	$num_rows_inserted=0;
	$suffix_size=1;
	//check if the $urls[$k] have is_tempate flag ON in the blogs table or not 
	//by joining through keyword of blogs table and genrated url from workbook table
	echo "<-- WORKING on url number ".$k." ".$urls[$k]."  -->\n";

	$url=$urls[$k];
	echo 'URL: ' . $url . PHP_EOL;
	$sql="SELECT * FROM blogs WHERE keyword='$url'";
	$result=mysqli_query($db,$sql);
	$first_row=mysqli_fetch_assoc($result);
	$is_template=intval($first_row['is_template']);
	$id=$first_row['id'];
	$urls[$k]=str_replace(" ", "-", $urls[$k]);
	$sql="('$urls[$k]','<brand>','<suffix>','$id',CURRENT_TIMESTAMP),";
	//if the url has a blog of template type then copies of url is generated otherwise not
	if($is_template)
	{

		//echo "Template exists for this URL.\n ";
		//this is to check whether url has prefix(<brand>) or not
		if(strpos($urls[$k],"<brand>")!==false)
		{
			//echo "Prefix exists for this URL\n ".sizeof($brands)."  number of prefixes exists.\n ";
			
			if(is_suffix_exists($urls[$k]))
			{
				if(strpos($urls[$k],"<place>")!==false)
					{$col="<place>";$suffix_size=sizeof($places);}
				else if(strpos(strtolower($urls[$k]),"<region>")!==false)
					{$col="<region>";$suffix_size=sizeof($regions);}
				else if(strpos(strtolower($urls[$k]),"<area>")!==false)
					{$col="<area>";$suffix_size=sizeof($areas);}
				else if(strpos(strtolower($urls[$k]),"<pincode>")!==false)
					{$col="<pincode>";$suffix_size=sizeof($pincodes);}
				else if(strpos(strtolower($urls[$k]),"<city>")!==false)
					{$col="<city>";$suffix_size=sizeof($cities);}

				//echo "  ".$col." suffix also exists in this URL\n    ".$suffix_size." number of suffixes exists for this suffix\n";
			}
			else {
				//echo "  But Suffix doesn't exists in the URL\n ";
			}

			//echo sizeof($brands)*$suffix_size." number of URLs must be inserted\n";
			
			//if prefix exists then for every specific prefix urls must be generated 
			for($i=0;$i<sizeof($brands);$i++)
			{
				$res="INSERT IGNORE INTO `url_table_ref` (`url`, `brand`, `place`, `blog_id`, `create_date`) VALUES ";
				if(is_suffix_exists($urls[$k]))
				{
					//echo "prefix and suffix both exists";
					//this part will executed if url contains both prefix and suffix
					//ex= <brand>-tv-repair-in-pincode
					if(strpos($urls[$k],"<place>")!==false)
						$res=make_copies_of_url($brands[$i],$places,"<place>",$res,$sql,$urls[$k]);
					else if(strpos(strtolower($urls[$k]),"<region>")!==false)
						$res=make_copies_of_url($brands[$i],$regions,"<region>",$res,$sql,$urls[$k]);
					else if(strpos(strtolower($urls[$k]),"<area>")!==false)
						$res=make_copies_of_url($brands[$i],$areas,"<area>",$res,$sql,$urls[$k]);
					else if(strpos(strtolower($urls[$k]),"<pincode>")!==false)
						$res=make_copies_of_url($brands[$i],$pincodes,"<pincode>",$res,$sql,$urls[$k]);	
					else if(strpos(strtolower($urls[$k]),"<city>")!==false)
						$res=make_copies_of_url($brands[$i],$cities,"<city>",$res,$sql,$urls[$k]);	
				}
				else
				{
					//this part will executed if url contains prefix only and there are no suffix exists in the url
					//ex= <brand>-customer-care
					$sql_url=get_final_url($brands[$i],"",$sql,"");
					$res=$res.$sql_url;
					
					$final_url=get_final_url($brands[$i],"",$urls[$k],"");
					update_sitemap($final_url);
				}

			//for optimisation all the insert queries are concatenated in the $res variable which is executed at once now 
			$res[strlen($res)-1] = ";";
			$stat = mysqli_query($db,$res)or die(mysqli_error($db));
			$num_rows_inserted+=mysqli_affected_rows($db);
			}
		}
		else if(is_suffix_exists($urls[$k]))
		{
			//echo "Prefix doesnot exist, only suffix exists for this URL\n";

			//when there is no brand but have places
			//ex= tv-repair-in-<place>
			$res="INSERT IGNORE INTO `url_table_ref` (`url`, `brand`, `place`, `blog_id`, `create_date`) VALUES ";
			if(strpos($urls[$k],"<place>")!==false)
				$res=make_copies_of_url("",$places,"<place>",$res,$sql,$urls[$k]);
			else if(strpos(strtolower($urls[$k]),"<region>")!==false)
				$res=make_copies_of_url("",$regions,"<region>",$res,$sql,$urls[$k]);
			else if(strpos(strtolower($urls[$k]),"<area>")!==false)
				$res=make_copies_of_url("",$areas,"<area>",$res,$sql,$urls[$k]);
			else if(strpos(strtolower($urls[$k]),"<pincode>")!==false)
				$res=make_copies_of_url("",$pincodes,"<pincode>",$res,$sql,$urls[$k]);	
			else if(strpos(strtolower($urls[$k]),"<city>")!==false)
				$res=make_copies_of_url("",$cities,"<city>",$res,$sql,$urls[$k]);	

			//for optimisation all the insert queries are concatenated in the $res variable which is executed at once now 
			$res[strlen($res)-1]=";";
			mysqli_query($db,$res)or die(mysqli_error($db));
			//echo $res;
			$num_rows_inserted = mysqli_affected_rows($db);
		}

		if($num_rows_inserted > 0) {
			//echo $num_rows_inserted." rows inserted for this URL.\n";
		}
		else
			echo "ERROR ocurred while inserting URLs for this template.\n";

		if($num_rows_inserted == sizeof($brands) * $suffix_size) {
			//echo "URLs successfully generated\n";
		}
		
		$total_rows_inserted+=$num_rows_inserted;
	}
	else {
		echo "Template doesn't exists for this URL.\n No rows added.\n";
	}
	

	//processing on one entry of workbook is completed so staus on the workbook table must be updated
	$sql_workdone="UPDATE workbook2 set status='done',sitemap='done' where  Keyword='$keywords[$k]';";
	$status=mysqli_query($db,$sql_workdone)or die(mysqli_error($db));
	if($status!=false) {
		//echo "Status is updated for this url in workbook table.\n";
	}
	else
		echo "ERROR ocurred while updating status for this URL.\n";
	
	echo "\n";
}

//insert those urls of blogs table in which is_template field is 0
$total_rows_inserted=insert_titles_of_non_template($db,$total_rows_inserted);

//Make sure the sitemaps are closed properly

//echo "Last sitemap: " . $last_sitemap . "\n";
//write closing tag in the last sitemap file
$f_last_sitemap = fopen("sitemap-$last_sitemap.xml", "a") or die("Unable to open file!");
fwrite($f_last_sitemap, '</urlset>');

//write closing tag in the sitemap index file
$master_sitemap = fopen("sitemap.xml", "a") or die("Unable to open file!");
fwrite($master_sitemap, '</sitemapindex>');

echo "\n\n******TOTAL ".$total_rows_inserted." rows are inserted in the URL table.******\n\n";

mysqli_close($db);

?>