<?php
include("247around_tax_invoice_intrastate_variables.php");
?>
<html>
<head>
	<title>247 Around Tax Invoice Intrastate</title>
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
		
		td{
			border:solid 2px;
			padding: 1%;
		}
		
		
		
	</style>
</head>
<body>
<table class="table">
	<tr>
		<td style="text-align: center;border-bottom: hidden; " colspan="10" ><h1>Blackmelon Advance Technology Co. Pvt. Ltd.</h1></td></tr>
	<tr style="border-bottom:hidden;"><td rowspan="2" align="left" style="border-right: hidden;"><img style="padding: 5px;" src="https://aroundhomzapp.com/images/logo.jpg"></td>
		<td colspan="7"  align="center" style="border:hidden;padding-right: 10%">A-1/7a, A BLOCK, KRISHNA NAGAR,<br> 
DELHI 110051</td>
		<td rowspan="3" colspan="2" style="text-align: right; border-left: hidden;"><b>(<?php echo "$recipient_type";?>)</td>
	</tr>
	<tr style="border: hidden;"><td colspan="6" align="center" style=" border-right: hidden;padding-right: 5%">Email: billing@247around.com</td></tr>
<!-- 	<tr><td style="border-bottom: hidden;border-top: hidden;border-right: hidden;padding: 1%">shmskjdhlskdjls</td>	
 -->	
 	<tr style="text-align: center;" ><td colspan="10"><b>GSTIN: 07AAFCB1281J1ZQ</td></tr>
	

	<tr style="text-align: center; height: 50px; font-size: 40px;background-color: rgb(211,211,211);">	
		<td colspan="10"><b><?php echo "$invoice_type";?></b></tr>


	<tr><td colspan="5">Invoice No:<?php echo "$invoice_id";?></td>		
		<td colspan="6">Period: <?php echo "$sd";?> - <?php echo "$ed";?></td></tr>
	<tr><td colspan="5">Date: <?php echo "$invoice_date";?></td>	
		<td colspan="6">Reverse Charge: No</td>
		
	</tr>



	<tr style="text-align: center;background-color: rgb(211,211,211);"><td colspan="10"><b>Bill to Party</b></tr>


	<tr style="height: 20px">
		<td colspan="10"><b>Name : <?php echo $company_name?></td></tr>
	<tr style="height: 5%"><td colspan="10"><b>Address :
		<?php echo "$company_address";?></td></tr>
	<tr><td colspan="3"><b>GSTIN :  <?php echo "$gst_number";?></td>
		<td colspan="6"><b>Place of Supply: <?php echo "$state";?></td>	
		<td><b>Code:<?php echo "$state_code";?></td></tr>


	<tr style="text-align: center;background-color: rgb(211,211,211);">
		<td rowspan="2" width="40%"><b>Product Description</td>
		<td rowspan="2" width="6%" style="padding: 1px"><b>HSN/SAC<br>Code</td>
		<td rowspan="2" width="10%"><b>Qty</td>
		<td rowspan="2" width="10%"><b>Rate</td>
		<td rowspan="2" width="10%"><b>Taxable value</td>
		<td colspan="2" width="10%"><b>CGST</td>
		<td colspan="2" width="10%"><b>SGST</td>
		<td rowspan="2" width="5%"><b>Total</td>
	</tr>
	<tr align="center" style="background-color: rgb(211,211,211)"><td width="5%" style="padding: 1px"><b>Rate</td>
		<td width="5%"><b>Amount</td>
	<td width="5%" style="padding: 1px"><b>Rate</td>
		<td width="5%"><b>Amount</td>
	</tr>


	<?php
		foreach ($record as $info) {
			echo "<tr style='text-align: center;'>
				<td>$info[description]</td>
				<td>$info[hsn_code]</td>
				<td>$info[qty]</td>
				<td>$info[rate]</td>
				<td>$info[taxable_value]</td>
				<td>$info[cgst_rate]</td>
				<td>$info[cgst_tax_amount]</td>
				<td>$info[sgst_rate]</td>
				<td>$info[sgst_tax_amount]</td>
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

		<tr >
			<td colspan="4" align="center"><b>Total Invoice amount in words</b></td>
			<td colspan="5"><b>Total Amt before Tax</td>
			<td colspan="1"><b><?php echo "$total_taxable_value";?></td>
		</tr>
		<tr>
			<td colspan="4" rowspan="3" style="padding: 2%; text-align: center;"><?php echo $price_inword;?></td>
			<td colspan="5"><b>Add: CGST</td>
			<td colspan="1"><b><?php echo "$cgst_total_tax_amount";?></td>
		<tr style="font-weight: bold;">
			<td colspan="5"><b>Add: SGST</td>
			<td colspan="1"><b><?php echo "$sgst_total_tax_amount";?></td>
		</tr>
		<tr style="font-weight: bold;"><td colspan="5">Total Amt after Tax</td>
			<td colspan="1"><?php echo "$sub_total_amount";?></td>
		</tr>

		<tr>
			<td align="center" style="background-color: rgb(211,211,211);"><b>Bank Details</td>
			<td rowspan="6" colspan="3">stamp</td>
			<td colspan="5"><b>GST on Reverse Charge</td>
			<td colspan="1">0</td>
		</tr>
		<tr><td>Bank Name: ICICI Bank</td>
			<td rowspan="2" colspan="7" style="padding: 2%;background-color: rgb(211,211,211);"><b>For Blackmelon Advance Technology Co. Pvt. Ltd.</td>
		</tr>
		<tr><td>Acc No: 102405500277</td></tr>
		<tr><td>IFSC: ICIC0001024</td>
			<td rowspan="2" colspan="7">sign</td>
		</tr>
		<tr>
			<td style="border-bottom: hidden;">	</td>
		</tr>
		<tr><td>	</td>
			<td colspan="7" align="center"><b>Authorised Signature</b></td>
		</tr>

</table>

</body>
</html>

