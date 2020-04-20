<?php
include("delivery_challan_without_gst_variables.php");
?>
<!DOCTYPE html>
<html>
<head>
	<title>Delivery Challan</title>
	<style type="text/css">
		#table1{
			border:solid 2px;
			border-collapse: collapse;
			width: 960px;
			font-family: sans-serif;
			font-size: 100%;
			margin: auto;
			
			/*total cols=14*/
		}
		#table1 td{
			border: solid 1px;
			
			padding: 1%
		}
		#table1 .blank_row td{
			border-right: hidden;
		}
		/**{
			border:solid;
			margin: auto;
		}*/

		#table2{
			border:hidden;
			width: 960px;
			font-family: sans-serif;
			font-size: 100%;
			margin: auto;
		}
		#table2 td{
			padding:1%;
		}
	</style>
</head>
<body>
	<table id="table1">
		<tr>
			<td colspan="2" style="border-right: hidden;"><img style="padding: 5px;" src="https://aroundhomzapp.com/images/logo.jpg"></td>
			<td colspan="11" align="left"><h1>Delivery Challan</h1></td>
		</tr>
		<tr>
			<td colspan="13" style="border-bottom: hidden; text-align: center;"><b><?php echo "$sf_name";?></td>
		</tr>
		<tr>
			<td colspan="7" align="left" style="border-right: hidden;"><b><?php echo "$sf_address";?></td>
				<td style="border-right: hidden;"></td>
			<td colspan="5" align="right">GST: <?php echo "$sf_gst";?></td>
		</tr>
		<tr>
			<td colspan="7" align="left" style="border-bottom: hidden;"><b><?php echo "$partner_name";?></td>
				<td style="border-bottom: hidden;border-right: hidden;"></td>
			<td  colspan="5" align="left" style="border-bottom: hidden;"><b>Challan No </b><?php echo "$sf_challan_no";?></td>
		</tr>
		<tr>
			<td  colspan="7" rowspan="2" align="left" style="border-bottom: hidden;">Address: <?php echo "$partner_address";?></td>
			<td style="border-bottom: hidden;border-right: hidden;"></td>
			<td colspan="5" align="left" style="border-bottom: hidden;"><b>Ref No: </b><?php echo "$partner_challan_no";?></td>
		</tr>
		<tr>
			<td style="border-bottom: hidden;border-right: hidden;"></td>
			<td colspan="5" align="left" style="border-bottom: hidden;"><b>Date </b><?php echo "$date";?></td>
		</tr>
		<tr>
			<td  colspan="7" align="left"><b>GST: </b><?php echo "$partner_gst";?></td>
			<td style="border-right: hidden;"></td>
			<td colspan="5"></td>
		</tr>
		<tr class="blank_row"><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
			<td></td><td></td><td></td><td></td><td style="border-right: solid 1px;"></td>
		</tr>
		<tr  style="text-align: center;">
			<td><b>S No</b></td>
			<td colspan="6"><b>Description</b></td>
			<td><b>Qty</b></td>
			<td colspan="3"><b>Booking ID</b></td>
			<td colspan="2"><b>Value (Rs.)</b></td>
		</tr>
		<?php
			foreach ($record as $info) {
				echo "<tr>	<td align="."\"center\"".">".$i++."
							<td colspan="."6"." align="."\"center\"".">$info[spare_desc]
							<td colspan="."1"." align="."\"center\"".">$info[qty]
							<td colspan="."3"." align="."\"center\"".">$info[booking_id]
							<td colspan="."2"." align="."\"center\"".">$info[value]
					</tr>";
					$total_qty+=$info["qty"];
					$total_value+=$info["value"];
			}
		?>
		<tr  style="font-weight: bold;">
			<td colspan="6"></td>
			<td>Total Qty</td>
			<td colspan="3"><?php echo "$total_qty";?></td>
			<td>Total Amt</td>
			<td colspan="2"><?php echo "$total_value";?></td>
		</tr>
		<tr>
			<td style="text-align: right;padding-top: 3%; padding-bottom: 10%;padding-right: 2%" colspan="13">For <?php echo "sf_name";?></td>
		</tr>
		<tr><td colspan="13" style="border-bottom: hidden;border-right: hidden;border-left: hidden; text-align: center;"><small>This is a computer generated challan and does not need signature.</tr>
	</table>

	<hr style="border-top: dashed 1px;">

	<table id="table2">
		<tr><td align="center"><h1>Declaration of GST Non-Enrollment</td></tr>
		<tr><td>To Whom It May Concern:</td></tr>
		<tr><td>Dear Sir / Madam,<br><br>
		We, <?php echo "$sf_owner_name";?>, <?php echo "$sf_name";?>, <?php echo "$sf_address";?> do hereby state that we are not required to get ourselves registered under the Goods and Services Tax Act, 2017 as we have the turnover below the taxable limit as specified under the Goods and Services Tax Act, 2017.</td>
		<tr><td>We hereby also confirm that if during any financial year, we decide or require to register under the GST in that case, we undertake to provide all the requisite information and documents.
		</td></tr>
		<tr><td>We request you to treat this communication as a declaration regarding non-requirement to be registered under the Goods and Service Tax Act, 2017. 
			<br><br>
		Signature of Authorised Signatory:
			<br><br>
			<div style="border:solid  1px; width: 500px; height: 100px"></div>
			<br>
			Name of the Authorised Signatory: <?php echo "$sf_owner_name";?><br>
			Name of Business: <?php echo "$sf_name";?><br>
			Date:  <?php echo "$date";?>
		</td></tr>
</td></tr>
	</table>
</body>
</html>