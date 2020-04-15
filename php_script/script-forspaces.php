<?php

//saving content of brand and total number of brands
$brandfilename = "tv-brands.txt";
$brandfile = fopen($brandfilename, "r");
$brands = fread($brandfile, filesize($brandfilename));
fclose($brandfile);

$brand_arr = explode("\n", $brands);
$n = sizeof($brand_arr);
$brand_arr[$n]=" ";
$n=$n+1;

//saving content of places file and total number of places in th file
$placefilename = "places.txt";
$placefile = fopen($placefilename, "r");
$places = fread($placefile, filesize($placefilename));
fclose($placefile);

$place_arr = explode("\n", $places);
$m = sizeof($place_arr);
$place_arr[$m]=" ";
$m=$m+1;

for ($i = 0; $i < $n; $i++) {
    print_r($brand_arr[$i]);
    # code...
}

$file = "tv-repair-for2script.sql";
$fileptr = fopen($file, 'r');
$originaldata = fread($fileptr, filesize($file));
$myfile = fopen("resultfile.sql", "w") or die("Unable to open file!");

for ($i = 0; $i < $n; $i++) {
    for ($j = 0; $j < $m; $j++) {
	if($place_arr[$j]==" ")
	{
	$filedata = str_replace("<place>", $place_arr[$j], $originaldata);
	$filedata = str_replace("<-in-place>", $place_arr[$j], $filedata);
	}
	else
	{
	$filedata = str_replace("<place>", str_replace(" ","-",$place_arr[$j]), $originaldata);
	$filedata = str_replace("<-in-place>",str_replace(" ","-","-in-".$place_arr[$j]), $filedata);	
	}

	if($brand_arr[$i]==" ")
	{
		$filedata = str_replace("<brand>",  $brand_arr[$i], $filedata);
		$filedata = str_replace("<brand->", $brand_arr[$i], $filedata);
	}
	else
	{
		$filedata = str_replace("<brand->", str_replace(" ", "-", $brand_arr[$i]."-"), $filedata);	
		$filedata = str_replace("<brand>",  str_replace(" ","-",$brand_arr[$i]), $filedata);
	}
	fwrite($myfile, $filedata . "\n");
    }
}

fclose($myfile);

