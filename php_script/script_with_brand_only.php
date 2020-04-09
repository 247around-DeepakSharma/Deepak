<?php

//saving content of brand and total number of brands
$brandfilename = "brands.txt";
$brandfile = fopen($brandfilename, "r");
$brands = fread($brandfile, filesize($brandfilename));
fclose($brandfile);

$brand_arr = explode("\n", $brands);
$n = sizeof($brand_arr);

$file = "customer-care.sql";
$fileptr = fopen($file, 'r');
$originaldata = fread($fileptr, filesize($file));
$myfile = fopen("newfile-customer-care.sql", "w") or die("Unable to open file!");

for ($i = 0; $i < $n; $i++) {
    $filedata = str_replace("<brand>", $brand_arr[$i], $originaldata);
    fwrite($myfile, $filedata . "\n");
}

fclose($myfile);