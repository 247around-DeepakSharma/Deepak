<?php
include("sf_foc_tax_invoice_interstate_variables.php");
?>
<html>
<head>
	<title>SF FOC Tax Invoice Interstate</title>
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
		<td style="text-align: center;border-bottom: hidden; " colspan="8" ><h1><?php echo "$company_name";?></h1></td></tr>
	<tr style="border-bottom:hidden;">
		<td colspan="7" width="75%" align="center0" style="border-right:hidden; padding-left: 5%"><?php echo "$company_address";?></td>
		<td colspan="1" style="text-align: right; width: 20%; border-left: hidden;"><b>(<?php echo "$recipient_type";?>)</td>
	</tr>
	<tr ><td colspan="8" align="center" style=" border-top: hidden;border-bottom: hidden;">Phone: <?php echo "$owner_phone_1";?></td></tr>
<!-- 	<tr><td style="border-bottom: hidden;border-top: hidden;border-right: hidden;padding: 1%">shmskjdhlskdjls</td>	
 -->	
 	<tr style="text-align: center; font-weight: bold;"><td colspan="8">GSTIN :  <?php echo "$gst_number"; ?></td></tr>
	

	<tr style="text-align: center; height: 50px; font-size: 40px;background-color: rgb(211,211,211);">	
		<td colspan="8"><b>Tax Invoice</b></tr>


	<tr><td colspan="3">Invoice No:<?php echo "$invoice_id";?></td>		
		<td colspan="5">Period: <?php echo "$sd";?> - <?php echo "$ed";?>
	<tr><td colspan="3">Invoice Date: <?php echo "$invoice_date";?></td>	
		<td colspan="3">Reverse Charge (Y/N):</td>
		<td colspan="2">N</td>
	</tr>


	<tr style="text-align: center;background-color: rgb(211,211,211);"><td colspan="8"><b>Bill to Party</b></tr>


	<tr style="height: 20px"><td colspan="8">Name: Blackmelon Advance Technology Co. Pvt. Ltd.</td></tr>
	<tr style="height: 5%"><td colspan="8">Address: A-1/7, F/F A BLOCK, KRISHNA NAGAR, DELHI, 110051</td></tr>
	<tr><td colspan="3">GSTIN: 07AAFCB1281J1ZQ</td>
		<td colspan="4">Place of Supply: Delhi</td>	
		<td>Code: 07</td></tr>


	<tr style="text-align: center;background-color: rgb(211,211,211);">
		<td rowspan="2" width="30%"><b>Product Description</td>
		<td rowspan="2" width="6%" style="padding: 1px"><b>HSN/SAC<br>Code</td>
		<td rowspan="2" width="10%"><b>Qty</td>
		<td rowspan="2" width="10%"><b>Rate</td>
		<td rowspan="2"><b>Taxable value</td>
		<td colspan="2" width="30%"><b>IGST</td>
		<td rowspan="2"><b>Total</td>
	</tr>
	<tr align="center" style="background-color: rgb(211,211,211)"><td width="12%" style="padding: 1px"><b>Rate</td>
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
				<td width='12%'>$info[igst_rate]</td>
				<td width='15%'>$info[igst_tax_amount]</td>
				<td>$info[total_amount]</td>
			</tr>";
			$total_qty+=$info['qty'];
			$total_taxable_value+=$info['taxable_value'];
			$igst_total_tax_amount+=$info['igst_tax_amount'];
			
			$sub_total_amount += $info['total_amount'];
		}
	?>		


	<tr align="center"><td style="background-color: rgb(211,211,211);"><b>Total</td>	
		<td style="border-right: hidden;"></td>	
		<td><b><?php echo "$total_qty";?></td>	
		<td style="border-left: hidden;"></td>	
		<td><?php echo "$total_taxable_value";?></td>
		<td></td>
		<td><?php echo "$igst_total_tax_amount";?></td>
	
		<td><b><?php echo "$sub_total_amount";?></td>
	</tr>

		<tr align="left" >
			<td width="60%" colspan="4" align="center"><b>Total Invoice amount in words</b></td>
			<td width="30%" colspan="3"><b>Total Amt before Tax</td>
			<td width="10%" colspan="1"><b><?php echo "$total_taxable_value";?></td>
		</tr>
		<tr align="left">
			<td colspan="4" rowspan="2" style="padding: 2%; text-align: center;"><?php echo $price_inword;?></td>
			<td colspan="3"><b>Add: IGST</td>
			<td colspan="1"><b><?php echo "$igst_total_tax_amount";?></td>
		<tr style="font-weight: bold;text-align: left;"><td colspan="3">Total Amt after Tax</td>
			<td colspan="1"><?php echo "$sub_total_amount";?></td>
		</tr>

		<tr>
			<td rowspan="4" align="left">Declaration: We declare that this invoice shows the actual price of the goods and services described and that all particulars are true and correct.</td>
			<td rowspan="4" colspan="3"></td>
			<td colspan="3"><b>GST on Reverse Charge</td>
			<td colspan="1">0</td>
		</tr>
		<tr>
			<td colspan="4" align="center" style="padding: 2%;background-color: rgb(211,211,211);"><b>For <?php echo "$company_name";?></td>
		</tr>
		
		<tr>
			<td colspan="4" height="30%" style="padding: 5%">&nbsp</td>
		</tr>
		
		<tr>
			<td colspan="4" align="center" height="40%"><b>Authorised signatory</td>
		</tr>
</table>
</td>
</tr>
<p style="text-align: center; margin-top: 0px"><small>This is a computer generated invoice, no signature is required.</small>

</body>
</html>

