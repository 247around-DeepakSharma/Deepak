<?php

$fixed_sitemap = "fixed-sitemap.xml";
$sm_file = fopen($fixed_sitemap, "r");
$sm = fread($sm_file, filesize($fixed_sitemap));
fclose($sm_file);

$urlfilename = "url.txt";
$urlfile = fopen($urlfilename, "r");
$urls = fread($urlfile, filesize($urlfilename));
fclose($urlfile);

$url_arr = explode("\n", $urls);
$n = sizeof($url_arr);

$myfile = fopen("sitemap.xml", "w") or die("Unable to open file!");

//Write fixed sitemap in this file first
fwrite($myfile, $sm);

/*
 * SITEMAP TEMPLATE
  <url>
  <loc>http://247around.com/washing-machine-repair</loc>
  <changefreq>weekly</changefreq>
  <priority>0.64</priority>
  </url>
 *
 */

foreach ($url_arr as $url) {
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

    fwrite($myfile, $section1 . trim($url) . $section2 . PHP_EOL);
}

//Add this in the end of sitemap: </urlset>
fwrite($myfile, "</urlset>");

fclose($myfile);

