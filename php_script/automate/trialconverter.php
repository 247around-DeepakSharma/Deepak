
<?php
require_once 'C:xampp/htdocs/247around/spout-2.4.3/src/Spout/Autoloader/autoload.php';

require_once 'C:\xampp\htdocs\247around\php_script\automate\db_config.php';

$db=mysqli_connect(DB_SERVER,DB_USER,DB_PASSWORD,DB_DATABASE);

use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;

$filePath='C:\xampp\htdocs\247around\all_india_pin_code.xlsx';

$reader = ReaderFactory::create(Type::XLSX);  
$reader->open($filePath);
echo "Inserting data from all_india_pin_code.xlsx to all_india_pin_code_table in db\n\n";
	
	//Initialisation of values
	$area="";
	$pincode=0;
	$division="";
	$region="";
	$taluk="";
	$district="";
	$state="";
	$count=0;
	$total_rows_inserted=0;

	//Read each row and as and when the no. of records read exceed 10,000 insert them into db
	$res="INSERT INTO `all_india_pin_code_table` (`area`, `pincode`, `division`, `region`, `taluk`, `district`, `state`) VALUES ";	
   
   foreach ($reader->getSheetIterator() as $sheet) {
   foreach ($sheet->getRowIterator() as $row) 
      {
      	if($count>0)
      	{
      		if($count%10000==0)
			{
				$res[strlen($res)-1]=";";
				$data2=mysqli_query($db,$res)or die(mysqli_error($db));
				$updated_rows=mysqli_affected_rows($db);
				$total_rows_inserted+=$updated_rows;
				
				$res="INSERT IGNORE INTO `all_india_pin_code_table` (`area`, `pincode`, `division`, `region`, `taluk`, `district`, `state`) VALUES ";	
					if($data2!=false)
						echo "Added ".$updated_rows." records\n\n";

			}
			
			else
			{
			
			$area=str_replace("'", "/'",$row[0]);
			$pincode=$row[1];
			$division=$row[2];
			$region=$row[3];
			$taluk=$row[4];
			$district=$row[5];
			$state=$row[6];
			$sql="('$area','$pincode','$division','$region','$taluk','$district','$state'),";
			$res.=$sql;
			}
		}
		$count++;
	}
}
	
	echo "Total rows inserted =".$total_rows_inserted;
	$reader->close();
?>