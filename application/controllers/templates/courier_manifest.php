<?php
include("courier_manifest_variables.php");
?>
<!DOCTYPE html>
<html>
<head>
	<title>Courier Manifest</title>
	<style type="text/css">
		table{
			border-collapse: collapse;
			width: 960px;
			font-family: sans-serif;
			font-size: 100%;
			margin: auto;
		}
		td,th{
			border: solid 1px;
			padding: 1%;
			text-align: left;
		}
	</style>
</head>
<body>
	<table>
		<tr>
			<th width="40%">BRAND</th>
			<th width="60%"><?php echo "$source";?></th>
		</tr>
		<tr>
			<td>Job No.
			<td><?php echo "$booking_id";?>
		</tr>
		<tr>
			<td>Customer Name
			<td><?php echo "$name";?>
		</tr>
		<tr>
			<td>Address
			<td><?php echo "$booking_address";?>
		</tr>
		<tr>
			<td>Booking Age
			<td><?php echo "$booking_age";?>
		</tr>
		<tr>
			<td>Parts Requested
			<td><?php echo "$parts_requested";?>
		</tr>
		<tr>
			<td>Parts Shipped
			<td><?php echo "$parts_shipped";?>
		</tr>
		<tr>
			<td>Serial Number
			<td><?php echo "$serial_number";?>
		</tr>
		<tr>
			<td>Model Number
			<td><?php echo "$serial_number";?>
		</tr>
	</table>
</body>
</html>