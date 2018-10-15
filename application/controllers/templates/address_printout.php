<?php
$vendor_name="vendor_name";
$owner_name="owner_name";
$address="address";
$district="district";
$state="state";
$pincode="pincode";
$primary_contact_phone_1="primary_contact_phone_1";
$primary_contact_phone_2="primary_contact_phone_2";
?>
<!DOCTYPE html>
<html>
<head>
	<title>Address Printout</title>
	<style type="text/css">
		table {
		  font-family: sans-serif;
		  width: 960px;
		  margin: auto;
		  border-collapse: collapse;
		  text-align: center;
		  font-weight: bold;
		}
		td{
			border: solid 1px;
			padding: 2%;
			font-size: 100%;
		}

	</style>
</head>
<body>
	<table>
		<tr>
    		<td colspan="2" id="top_row" style="width: 12%;text-align: left; border-right: hidden;"><img style="padding: 5px;" src="https://aroundhomzapp.com/images/logo.jpg"></td>
    		<td colspan="4" align="left1"><p style="font-family: sans-serif;font-size: 22px;"><b><h1>247AROUND SERVICE CENTER</b></p></td>
    	</tr>
    	<tr>
    		<td colspan="6"><?php echo "$vendor_name"?>
    			<br>
    			C/O - <?php echo "$owner_name"?>
    		</td>
    	</tr>
    	<tr><td colspan="6">Address - <?php echo "$address"?>, <?php echo "$district"?>,<br>
    			<?php echo "$state"?>, <?php echo "$pincode"?>	
    		</td>
    	</tr>
    	<tr><td colspan="
    		6">Phone -  <?php echo "$primary_contact_phone_1"?>, <?php echo "$primary_contact_phone_2"?>
    		</td>
    	</tr>
	</table>

</body>
</html>