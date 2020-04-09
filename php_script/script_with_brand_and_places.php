<?php

//saving content of brand and total number of brands
$brandfilename = "tv-brands.txt";
$brandfile = fopen($brandfilename, "r");
$brands = fread($brandfile, filesize($brandfilename));
fclose($brandfile);

$brand_arr = explode("\n", $brands);
$n = sizeof($brand_arr);

//saving content of places file and total number of places in th file
$placefilename = "places-to-be-added.txt";
$placefile = fopen($placefilename, "r");
$places = fread($placefile, filesize($placefilename));
fclose($placefile);

$place_arr = explode("\n", $places);
$m = sizeof($place_arr);

for ($i = 0; $i < $n; $i++) {
    //print_r($brand_arr[$i]);
    # code...
}

$file = "tv-repair.sql";
$fileptr = fopen($file, 'r');
$originaldata = fread($fileptr, filesize($file));
$myfile = fopen("newfile.sql", "w") or die("Unable to open file!");

for ($i = 0; $i < $n; $i++) {
    for ($j = 0; $j < $m; $j++) {
	$filedata = str_replace("<place>", str_replace(" ", "-", $place_arr[$j]), $originaldata);
	$filedata = str_replace("<brand>", str_replace(" ", "-", $brand_arr[$i]), $filedata);
	fwrite($myfile, $filedata . "\n");
    }
}

fclose($myfile);

