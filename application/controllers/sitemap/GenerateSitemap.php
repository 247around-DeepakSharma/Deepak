<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('memory_limit', '-1');

ini_set('max_execution_time', 360000000); //3600 seconds = 600 minutes

class GenerateSitemap extends CI_Controller {

    Private $Brand = array();
    Private $Region = array();
    Private $Area = array();
    Private $city = array();
    Private $Pincode = array();
    Private $workbook = array();
    Private $workbookurl = array();
    Private $UrlTableData = array();
    Private $Appliance = array();
    Private $SitemapName = array();
    Private $SitemapDirectory = FCPATH."sitemap/";
    Private $TargetSitemap = 50000;
    Private $MultiSitemapName = array();
    Private $SitemapNumber = 0;

    function __Construct() {
        parent::__Construct();
        $this->load->model('booking_model');
        $this->load->model('url_model');
        $this->load->model('vendor_model');
        $this->load->model('blogs_model');
    }
    /**
     * @desc This is used to generate sitemap. 
     */
    function index() {
        
        echo "index" . PHP_EOL;
        echo "Table Truncate " . PHP_EOL;
        $this->url_model->truncate_url_ref_table();
        //Select Appliance
        $this->Appliance = $this->booking_model->selectservice();

        foreach ($this->Appliance as $value) {
            $this->workbook = array();
            $this->setUsefulArrayDetails($value->id);
            echo "Service Id" . $value->id . PHP_EOL;

            $this->setUrlKeyword();
            echo "EXIT URL Keyword";
            $this->setUrlSuffixPreffix($value->id);
            if (!empty($this->UrlTableData)) {
                echo "Count Not Unique URL " . count($this->UrlTableData) . PHP_EOL;
                //$this->UrlTableData = $this->unique_multidim_array($this->UrlTableData, "url");

                echo "Count Unique URL " . count($this->UrlTableData) . PHP_EOL;

                $this->createSitemap();

                echo "Insert Batch count" . count($this->UrlTableData) . PHP_EOL;
                $this->url_model->insert_url_ref_data_batch($this->UrlTableData);
            }

            $this->UrlTableData = array();

            echo PHP_EOL . " EXIT Appliance ID" . $value->id . PHP_EOL;
        }
        echo "Empty Array to release memory";
        $this->Pincode = array();
        $this->Region = array();
        $this->Area = array();
        $this->city = array();
        $this->Brand = array();
        $this->workbook = array();
        echo "Exit Foreach" . PHP_EOL;


        echo "Exit createSitemap";
        $this->createMainSitemap();
    }

    function setUsefulArrayDetails($service_id) {
        echo "getUsefulArrayDetails" . PHP_EOL;


        $this->Brand = $this->booking_model->get_brand(array('service_id' => $service_id, 'seo' => 1));
        echo "Get Brand" . PHP_EOL;
        $this->workbook = $this->url_model->getworkbook_details('*', array('service_id' => $service_id, 'active' => 1));
        echo "Get Workbook" . PHP_EOL;
        $this->Pincode[$service_id] = $this->vendor_model->get_pincode_mapping_form_col("vendor_pincode_mapping.Pincode", array("Appliance_ID" => $service_id));
        echo "Get Pincode" . PHP_EOL;
        $this->Region[$service_id] = $this->vendor_model->get_pincode_mapping_form_col("vendor_pincode_mapping.Region", array("Appliance_ID" => $service_id));
        echo "Get Region" . PHP_EOL;
        $this->Area[$service_id] = $this->vendor_model->get_pincode_mapping_form_col("vendor_pincode_mapping.Area", array("Appliance_ID" => $service_id));
        echo "Get Area" . PHP_EOL;
        $this->city[$service_id] = $this->vendor_model->get_pincode_mapping_form_col("vendor_pincode_mapping.City", array("Appliance_ID" => $service_id));
        echo "Get City" . PHP_EOL;

        echo "EXIT setUsefulArrayDetails" . PHP_EOL;
    }

    function setUrlKeyword() {
        echo "setUrl" . PHP_EOL;
        $urls = array();
        $this->workbookurl = array();
        foreach ($this->workbook as $value) {
            $url = $value['keyword'];
            if (!empty($value['prefix'])) {
                $url = $value['prefix'] . $url;
            }
            if (!empty($value['suffix'])) {
                $url = $url . $value['suffix'];
            }
            array_push($urls, $url);
            echo ".";
        }

        $this->workbookurl = $urls;
        return true;
    }

    function setUrlSuffixPreffix($service_id) {
        echo "setUrlSuffixPreffix" . PHP_EOL;
        foreach ($this->workbookurl as $urlkeyword) {
            echo ".";
            $is_blog = $this->blogs_model->get_blogs_details(array('keyword' => $urlkeyword, 'is_template' => 1));
            if ($is_blog) {
                // echo "Keyword ". $urlkeyword. " Count ".count($is_blog).PHP_EOL;
                //echo "Template exists for this URL.\n ";
                //this is to check whether url has prefix(<brand>) or not
                $blog_id = $is_blog[0]['id'];
                $urlkeyword = str_replace(" ", "-", $urlkeyword);
                if (strpos($urlkeyword, "<brand>") !== false) {
                    $this->setBrandPrefix($urlkeyword, $blog_id, $service_id);
                } else {
                    $this->replaceSuffix($urlkeyword, $blog_id, "", $service_id);
                }
            }
        }
        unset($this->Pincode[$service_id]);
        unset($this->Region[$service_id]);
        unset($this->Area[$service_id]);
        unset($this->city[$service_id]);
        echo "EXIT setUrlSuffixPreffix" . PHP_EOL;
        return true;
    }

    function setBrandPrefix($urlkeyword, $blog_id, $service_id) {
        echo "setBrandPrefix" . PHP_EOL;

        foreach ($this->Brand as $brand) {
            echo ".";
            $urlkeyword1 = str_replace(" ", "-", trim($brand['brand_name']));
            $urlkeyword2 = str_replace("<brand>", $urlkeyword1, $urlkeyword);
            $blogsKeyword = str_replace(' ', '-', $urlkeyword2);
            $this->replaceSuffix($blogsKeyword, $blog_id, trim($brand['brand_name']), $service_id);
        }
        echo "EXIT setBrandPrefix" . PHP_EOL;
        return true;
    }

    function replaceSuffix($blogsKeyword, $blog_id, $brand, $service_id) {
        echo "replaceSuffix" . PHP_EOL;
        echo "URL KEyword " . $blogsKeyword . PHP_EOL;

        if ($this->is_suffix_exists($blogsKeyword)) {

            if (strpos($blogsKeyword, "<city>") !== false) {

                $this->setCitySuffix($blogsKeyword, $blog_id, $brand, $service_id);
            } else if (strpos($blogsKeyword, "<region>") !== false) {

                $this->setRegionSuffix($blogsKeyword, $blog_id, $brand, $service_id);
            } else if (strpos($blogsKeyword, "<area>") !== false) {

                $this->setAreaSuffix($blogsKeyword, $blog_id, $brand, $service_id);
            } else if (strpos($blogsKeyword, "<pincode>") !== false) {

                $this->setPincodeSuffix($blogsKeyword, $blog_id, $brand, $service_id);
            }
        } else {
            $array['url'] = $blogsKeyword;
            $array['brand'] = $brand;
            $array['place'] = "";
            $array['blog_id'] = $blog_id;
            $array['create_date'] = date('Y-m-d H:i:s');
            if (!array_search($blogsKeyword, array_column($this->UrlTableData, 'url'))) {
                array_push($this->UrlTableData, $array);
                echo "Replace Suffix" . $blogsKeyword . PHP_EOL;
            } else {
                echo " Replacesuffix: URL EXIST " . $blogsKeyword.PHP_EOL;
            }
        }
        echo "EXIT replaceSuffix" . PHP_EOL;
        return true;
    }

    function setCitySuffix($blogKeyword, $blog_id, $brand, $service_id) {
        echo "setCitySuffix" . PHP_EOL;
        foreach ($this->city[$service_id] as $value) {

            $url = $this->getFinalUrl(trim($value['City']), $blogKeyword, '<city>');
            $array['url'] = $url;
            $array['brand'] = $brand;
            $array['place'] = trim($value['City']);
            $array['blog_id'] = $blog_id;
            $array['create_date'] = date('Y-m-d H:i:s');
            if (!array_search($url, array_column($this->UrlTableData, 'url'))) {
                array_push($this->UrlTableData, $array);
                echo "City suffix " . $url . PHP_EOL;
            } else {
                echo "setCitySuffix URL EXIST " . $url.PHP_EOL;
            }
        }

        echo "EXIT setCitySuffix" . PHP_EOL;
        return true;
    }

    function setRegionSuffix($blogKeyword, $blog_id, $brand, $service_id) {
        echo "setRegionSuffix" . PHP_EOL;
        foreach ($this->Region[$service_id] as $value) {
            $url = $this->getFinalUrl(trim($value['Region']), $blogKeyword, '<region>');
            $array['url'] = $url;
            $array['brand'] = $brand;
            $array['place'] = trim($value['Region']);
            $array['blog_id'] = $blog_id;
            $array['create_date'] = date('Y-m-d H:i:s');
            if (!array_search($url, array_column($this->UrlTableData, 'url'))) {
                array_push($this->UrlTableData, $array);
                echo "setRegionSuffix  " . $url . PHP_EOL;
            } else {
                echo "setRegionSuffix URL EXIST " . $url.PHP_EOL;
            }
        }

        return true;
    }

    function setAreaSuffix($blogKeyword, $blog_id, $brand, $service_id) {
        echo "setAreaSuffix" . PHP_EOL;
        foreach ($this->Area[$service_id] as $value) {
            $url = $this->getFinalUrl(trim($value['Area']), $blogKeyword, '<area>');
            $array['url'] = $url;
            $array['brand'] = $brand;
            $array['place'] = trim($value['Area']);
            $array['blog_id'] = $blog_id;
            $array['create_date'] = date('Y-m-d H:i:s');
            if (!array_search($url, array_column($this->UrlTableData, 'url'))) {
                array_push($this->UrlTableData, $array);
                echo "setAreaSuffix  " . $url . PHP_EOL;
            } else {
                echo "setAreaSuffix URL EXIST " . $url.PHP_EOL;
            }
        }

        echo "EXIT setAreaSuffix" . PHP_EOL;
        return true;
    }

    function setPincodeSuffix($blogKeyword, $blog_id, $brand, $service_id) {
        echo "setPincodeSuffix" . PHP_EOL;
        foreach ($this->Pincode[$service_id] as $value) {
            $url = $this->getFinalUrl(trim($value['Pincode']), $blogKeyword, '<pincode>');
            $array['url'] = $url;
            $array['brand'] = trim($brand);
            $array['place'] = trim($value['Pincode']);
            $array['blog_id'] = $blog_id;
            $array['create_date'] = date('Y-m-d H:i:s');
            if (!array_search($url, array_column($this->UrlTableData, 'url'))) {
                array_push($this->UrlTableData, $array);
                echo "setPincodeSuffix  " . $url . PHP_EOL;
            } else {
                echo "setPincodeSuffix URL EXIST " . $url.PHP_EOL;
            }
        }

        echo "EXIT setPincodeSuffix" . PHP_EOL;
        return true;
    }

    function getFinalUrl($suffix, $blogKeyword, $to_replace) {
        echo "Entry getFinalUrl" . PHP_EOL;

        //these chars should not come in URL and sitemap.xml
        $chars_to_be_ignored = array('(', ')', ',', '/', '?', '&', '"');

        $suffix1 = str_replace($chars_to_be_ignored, "", trim(strtolower($suffix)));
        $to_replace1 = str_replace($chars_to_be_ignored, "", $to_replace);

        $suffix2 = str_replace("&", "and", $suffix1);
        $to_replace2 = str_replace("&", "and", $to_replace1);

        $res_string = str_replace($to_replace2, str_replace(array(" ", "."), "-", $suffix2), strtolower($blogKeyword));
        $res_string1 = str_replace("<-in-'$to_replace'>", str_replace(" ", "-", "-in-" . $suffix), $res_string);
        echo "EXIT getFinalUrl" . PHP_EOL;
        return ($res_string1);
    }

    function is_suffix_exists($url) {
        echo "is_suffix exist" . PHP_EOL;
        return ( (strpos($url, "<region>") !== false) || (strpos($url, "<area>") !== false) || (strpos($url, "<pincode>") !== false) || (strpos($url, "<city>") !== false));
    }

    function createSitemap() {
        echo "update_sitemap " . PHP_EOL;
        if (count($this->UrlTableData) < $this->TargetSitemap) {
            $target_sitemap = count($this->UrlTableData);
        } else {
            $target_sitemap = $this->TargetSitemap;
        }

        echo "Target Sitemap " . $target_sitemap . PHP_EOL;

        $per_sitemap = count($this->UrlTableData) / $target_sitemap;
        $per_sitemap1 = round($per_sitemap);
        if ($per_sitemap1 < $per_sitemap) {
            $count_sitemap = $per_sitemap1 + 1;
        } else {
            $count_sitemap = $per_sitemap1;
        }
        $output_dir = $this->SitemapDirectory;
        $j = 0;
        for ($i = 0; $i < $count_sitemap; $i++) {
            $res = 0;
            if (file_exists($output_dir . "sitemap-" . ($i + $this->SitemapNumber) . ".xml")) {
                system(" chmod 777 " . $output_dir . "sitemap-" . ($i + $this->SitemapNumber) . ".xml", $res);
                unlink($output_dir . "sitemap-" . ($i + $this->SitemapNumber) . ".xml");
            }
            echo $output_dir . "sitemap-" . ($i + $this->SitemapNumber) . ".xml" . PHP_EOL;
            $sitemap_name = fopen($output_dir . "sitemap-" . ($i + $this->SitemapNumber) . ".xml", "a+") or die("Unable to open file!");
            array_push($this->SitemapName, "sitemap-" . ($i + $this->SitemapNumber) . ".xml");
            $section1 = $this->sitemapSection();
            fwrite($sitemap_name, $section1 . PHP_EOL);

            for ($k = $j; $k < $target_sitemap; $k++) {
                echo ".";
                $section2 = <<<EOD1
<url>
  <loc>http://247around.com/
EOD1;

                $section3 = <<<EOD2
</loc>
  <changefreq>weekly</changefreq>
  <priority>0.64</priority>
</url>
EOD2;
                fwrite($sitemap_name, $section2 . trim($this->UrlTableData[$k]['url']) . $section3 . PHP_EOL);
            }
            $section2 = <<<EOD
</urlset>
EOD;
            fwrite($sitemap_name, $section2 . PHP_EOL);
            echo "count K " . $k . PHP_EOL;
            $j = $target_sitemap;

            $target_sitemap += $target_sitemap;
            if (count($this->UrlTableData) < $target_sitemap) {
                $target_sitemap = count($this->UrlTableData);
            }
            echo " Below Target SiteMap " . $target_sitemap . PHP_EOL;

            fclose($sitemap_name);
        }

        $this->SitemapNumber = $i + 1;
        echo " Next Sitemap No. " . $i + 1 . PHP_EOL;
        echo "EXIT createSitemap" . PHP_EOL;

        return true;
    }

    function sitemapSection() {
        $section = <<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<urlset
      xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
EOD;
        return $section;
    }

    function createMainSitemap() {
        echo "createMainSitemap" . PHP_EOL;
        if (count($this->SitemapName) < $this->TargetSitemap) {
            $this->CreateSitemapIndex(0);
        } else {
            $this->MultiSiteMap();
            $this->CreateSitemapIndex(1);
        }

        echo "EXIT createMainSitemap" . PHP_EOL;
        return true;
    }

    function MultiSiteMap() {
        echo "MultiSiteMap" . PHP_EOL;
        if (count($this->SitemapName) < $this->TargetSitemap) {
            $target_sitemap = count($this->SitemapName);
        } else {
            $target_sitemap = $this->TargetSitemap;
        }

        $per_sitemap = count($this->SitemapName) / $target_sitemap;
        $per_sitemap1 = round($per_sitemap);
        if ($per_sitemap1 < $per_sitemap) {
            $count_sitemap = $per_sitemap1 + 1;
        } else {
            $count_sitemap = $per_sitemap1;
        }
        $output_dir = $this->SitemapDirectory;
        $j = 0;
        for ($i = 0; $i < $count_sitemap; $i++) {
            $res = 0;
            if (file_exists($output_dir . "multi-sitemap-" . $i . ".xml")) {
                system(" chmod 777 " . $output_dir . "multi-sitemap-" . $i . ".xml", $res);
                unlink($output_dir . "multi-sitemap-" . $i . ".xml");
            }

            echo $output_dir . "multi-sitemap-" . $i . ".xml" . PHP_EOL;
            $sitemap_name = fopen($output_dir . "multi-sitemap-" . $i . ".xml", "a+") or die("Unable to open file!");
            array_push($this->MultiSitemapName, "multi-sitemap-" . $i . ".xml");
            $section = <<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	
EOD;
            fwrite($sitemap_name, $section . PHP_EOL);

            for ($k = $j; $k < $target_sitemap; $k++) {
                echo ".";
                $value = $this->SitemapName[$i];
                $section2 = <<<EOD1
<sitemap>
    <loc>http://www.247around.com/$value</loc>
</sitemap>
EOD1;

                fwrite($sitemap_name, $section2 . PHP_EOL);
            }
            $section3 = <<<EOD
</sitemapindex>
EOD;
            fwrite($sitemap_name, $section3 . PHP_EOL);
            echo "count I " . $k . PHP_EOL;
            $j = $target_sitemap;

            $target_sitemap += $target_sitemap;
            if (count($this->SitemapName) < $target_sitemap) {
                $target_sitemap = count($this->SitemapName);
            }
        }
        echo "EXIT MultiSiteMap" . PHP_EOL;
        return true;
    }

    function CreateSitemapIndex($m_s) {
        echo "CreateSitemapIndex" . PHP_EOL;
        if ($m_s == 1) {
            $data = $this->MultiSitemapName;
        } else {
            $data = $this->SitemapName;
        }
        $res = 0;
        $output_dir = $this->SitemapDirectory;
        if (file_exists($output_dir . "sitemap.xml")) {
            system(" chmod 777 " . $output_dir . "sitemap.xml", $res);
            unlink($output_dir . "sitemap.xml");
        }

        $section = $this->getMainSiteMapSection();
        $sitemap_name = fopen($output_dir . "sitemap.xml", "a+") or die("Unable to open file!");
        fwrite($sitemap_name, $section . PHP_EOL);
        foreach ($data as $value) {
            echo ".";
            $section = <<<EOD
	<sitemap>
		<loc>http://www.247around.com/$value</loc>
	</sitemap>
EOD;
            fwrite($sitemap_name, $section . PHP_EOL);
        }
        $section1 = <<<EOD
</sitemapindex>
EOD;
        fwrite($sitemap_name, $section1 . PHP_EOL);
        fclose($sitemap_name);
        echo "EXIT MultiSiteMap" . PHP_EOL;
        $this->SitemapName = array();
        $this->MultiSitemapName = array();
        return true;
    }

    function getMainSiteMapSection() {
        $section = <<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	<sitemap>
		<loc>http://www.247around.com/fixed-sitemap.xml</loc>
	</sitemap>
	
EOD;

        return $section;
    }

//    function unique_multidim_array($array, $key) {
//        $temp_array = array();
//        $i = 0;
//        $key_array = array();
//
//        foreach ($array as $val) {
//            if (!in_array($val[$key], $key_array)) {
//                $key_array[$i] = $val[$key];
//                $temp_array[$i] = $val;
//                $i++;
//            }
//        }
//        return $temp_array;
//    }

}
