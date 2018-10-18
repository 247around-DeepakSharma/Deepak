<?php
include("customer_tax_invoice_variables_intrastate.php");
?>
<html>
<head>
	<title>Tax Invoice Intrastate</title>
	<style type="text/css">
		.table{
			border:solid 2px;
			border-collapse: collapse;
			width: 960px;
			height: 75%;
			font-family: sans-serif;
			font-size: 100%;
			margin: auto;
		}
		/*.subtable{
			border:solid 2px;
			border-collapse: collapse;
			width: 960px;
			height: 20%;
			font-family: sans-serif;
			font-size: 100%;
			margin: 0px;
			border-top: 0px;
			text-align: center;
		}*/
		td{
			border:solid 2px;
			padding: 1%;
		}
		
		
		
	</style>
</head>
<body>
<table class="table">
	<tr>
		<td style="text-align: center;border-bottom: hidden; " colspan="10" ><h1><?php echo "$company_name";?></h1></td></tr>
	<tr style="border-bottom:hidden;"><td rowspan="2" width="20%" align="left" style="border-right: hidden;"><img style="padding: 5px;" src="https://aroundhomzapp.com/images/logo.jpg"></td>
		<td colspan="7" width="75%" align="center" style="border:hidden;"><?php echo "$company_address";?></td>
		<td rowspan="3" colspan="2" style="text-align: right; width: 20%; border-left: hidden;"><b>(<?php echo "$recipient_type";?>)</td>
	</tr>
	<tr style="border: hidden;"><td colspan="6" align="center" style=" border-right: hidden; ">Phone: <?php echo "$owner_phone_1";?></td></tr>
<!-- 	<tr><td style="border-bottom: hidden;border-top: hidden;border-right: hidden;padding: 1%">shmskjdhlskdjls</td>	
 -->	
 	<tr style="text-align: center;" ><td colspan="10">GSTIN :  <?php echo "$gst_number"; ?></td></tr>
	

	<tr style="text-align: center; height: 50px; font-size: 40px;background-color: rgb(211,211,211);">	
		<td colspan="10"><b>Tax Invoice</b></tr>


	<tr><td colspan="4">Invoice No:<?php echo "$invoice_id";?></td>		
		<td colspan="3">Booking ID:
		<td colspan="3"><?php echo "$booking_id";?></td></tr>
	<tr><td colspan="4">Invoice Date: <?php echo "$invoice_date";?></td>	
		<td colspan="3">Reverse Charge (Y/N):</td>
		<td colspan="3">N</td>
	</tr>


	<tr style="text-align: center;background-color: rgb(211,211,211);"><td colspan="10"><b>Bill to Party</b></tr>


	<tr style="height: 20px"><td colspan="10">Name: <?php echo $customer_name?></td></tr>
	<tr style="height: 5%"><td colspan="10"><?php echo "$customer_address";?></td></tr>
	<tr><td colspan="3">Phone:9555000247</td>
		<td colspan="6" style="border-left: hidden;">Place of Supply: <?php echo "$state";?></td>	
		<td>Code:<?php echo "$state_code";?></td></tr>


	<tr style="text-align: center;background-color: rgb(211,211,211);">
		<td rowspan="2" width="30%"><b>Product Description</td>
		<td rowspan="2" width="6%" style="padding: 1px"><b>HSN/SAC<br>Code</td>
		<td rowspan="2" width="10%"><b>Qty</td>
		<td rowspan="2" width="10%"><b>Rate</td>
		<td rowspan="2"><b>Taxable value</td>
		<td colspan="2" width="30%"><b>CGST</td>
		<td colspan="2" width="30%"><b>SGST</td>
		<td rowspan="2"><b>Total</td>
	</tr>
	<tr align="center" style="background-color: rgb(211,211,211)"><td width="12%" style="padding: 1px"><b>Rate</td>
		<td width="15%"><b>Amount</td>
	<td width="12%" style="padding: 1px"><b>Rate</td>
		<td width="15%"><b>Amount</td>
	</tr>


	<?php
		foreach ($record as $info) {
			echo "<tr style='text-align: center;'>
				<td width='30%'>$info[description]</td>
				<td width='17%'>$info[hsn_code]</td>
				<td width='10%'>$info[qty]</td>
				<td width='8%'>$info[rate]</td>
				<td>$info[taxable_value]</td>
				<td width='12%'>$info[cgst_rate]</td>
				<td width='15%'>$info[cgst_tax_amount]</td>
				<td width='12%'>$info[sgst_rate]</td>
				<td width='15%'>$info[sgst_tax_amount]</td>
				<td>$info[total_amount]</td>
			</tr>";
			$total_qty+=$info['qty'];
			$total_taxable_value+=$info['taxable_value'];
			$cgst_total_tax_amount+=$info['cgst_tax_amount'];
			$sgst_total_tax_amount+=$info['sgst_tax_amount'];
			$sub_total_amount += $info['total_amount'];
		}
	?>		


	<tr align="center"><td style="background-color: rgb(211,211,211);"><b>Total</td>	
		<td style="border-right: hidden;"></td>	
		<td><b><?php echo "$total_qty";?></td>	
		<td style="border-left: hidden;"></td>	
		<td><?php echo "$total_taxable_value";?></td>
		<td></td>
		<td><?php echo "$cgst_total_tax_amount";?></td>
		<td></td>
		<td><?php echo "$sgst_total_tax_amount";?></td>
		<td><b><?php echo "$sub_total_amount";?></td>
	</tr>

		<tr align="center" >
			<td width="60%" colspan="4"><b>Total Invoice amount in words</b></td>
			<td width="30%" colspan="3"><b>Total Amt before Tax</td>
			<td width="10%" colspan="3"><b><?php echo "$total_taxable_value";?></td>
		</tr>
		<tr align="center">
			<td colspan="4" rowspan="3" style="padding: 2%"><?php echo $price_inword;?></td>
			<td colspan="3"><b>Add: CGST</td>
			<td colspan="3"><b><?php echo "$cgst_total_tax_amount";?></td>
		<tr style="font-weight: bold;text-align: center;">
			<td colspan="3"><b>Add: SGST</td>
			<td colspan="3"><b><?php echo "$sgst_total_tax_amount";?></td>
		</tr>
		<tr style="font-weight: bold;text-align: center;"><td colspan="3">Total Amt after Tax</td>
			<td colspan="3"><?php echo "$sub_total_amount";?></td>
		</tr>

		<tr>
			<td rowspan="4" align="left">Declaration: We declare that this invoice shows the actual price of the goods and services described and that all particulars are true and correct.</td>
			<td rowspan="4" colspan="3"></td>
			<td colspan="3"><b>GST on Reverse Charge</td>
			<td colspan="3">0</td>
		</tr>
		<tr>
			<td colspan="6" style="background-color: rgb(211,211,211);">For <?php echo "$company_name";?></td>
		</tr>
		
		<tr>
			<td colspan="6"><h1>&nbsp</td>
		</tr>
		
		<tr>
			<td colspan="6"></td>
		</tr>
</table>
</td>
</tr>
<p style="text-align: center; margin: auto;width: 960px" >Book Appliance Service from Qualified Engineers on "247AROUND" App / On Phone - 9555000247 / On Website - www.247around.com
<p style="text-align: center; margin-top: 0px"><small>This is a computer generated invoice, no signature is required.</small>

</body>
</html>

