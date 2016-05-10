<?php 
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
include_once('S3.php');

$root = dirname(__DIR__);
echo $root;
$conn = new mysqli("localhost", "root", "Around1234!@#$","boloaaka");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}  

$sql ="SELECT id,profile_photo from `handyman` where `current_time` >= DATE_SUB(CURDATE(), INTERVAL 1 DAY) and `image_processing`='0' ";
$results = $conn->query($sql);


if ($results->num_rows > 0) {

    while($rows = $results->fetch_assoc()) {

     print_r($rows);
      $path   = "https://s3.amazonaws.com/vendor-original/".$rows['profile_photo'];
      $path1  = $root.'/public_html/uploads/fulllength/';
      $path2  = $root.'/public_html/uploads/resize/';
      
 
         
      if(copy($path, $path1.$rows['profile_photo'])){
	     $commond = 'convert '.$root.'/public_html/uploads/fulllength/'.$rows['profile_photo'].' -resize 310x252 '.$root.'/public_html/uploads/resize/'.$rows['profile_photo'];
	       

	      $output = shell_exec($commond);
          
	        $bucket = "boloaaka-images";
          $file = $root.'/public_html/uploads/resize/'.$rows['profile_photo'];
	  $random = uniqid();
	  $ext = pathinfo($file, PATHINFO_EXTENSION);
	  $image_name = $random.".".$ext;
          $directory = "vendor-320x252/".$image_name;
	  
          $input = S3::inputFile($file);
         
          S3::putObject($input, $bucket, $directory,S3::ACL_PUBLIC_READ); 
         
          $id = $rows['id'];
          $sqls = "UPDATE handyman
          SET image_processing = '1', profile_photo = '$image_name'
          where id ='$id' ";
          $result = $conn->query($sqls); 
         
				     
    }


    }
}


?>
