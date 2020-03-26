<?php
include("paytm_payment_voucher_variables.php");
?>
<html>
<head>
	<title>Paytm_payment_Voucher</title>
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
		
		.bold{
			font-weight: bold;
		}
	</style>
</head>
<body>
<table class="table">
	<tr>
		
		<td style="text-align: center;border-bottom: hidden; " colspan="6" ><h1><?php echo "$company_name";?></h1></td></tr>
	<tr>
		<td colspan="5" width="75%" align="center" style="border-right:hidden; padding-left: 5%"><?php echo "$company_address";?></td>
		<td class="bold" rowspan="2" colspan="1" style="text-align: right; width: 20%; border-left: hidden;"><b>(<?php echo "$recipient_type";?>)</td>
	</tr>
	<tr><td colspan="4" style="padding-right: 15%; border-right: hidden;border-top: hidden; text-align: center;">Phone :  <?php echo "$owner_phone_1"; ?></td></tr>
<!-- 	<tr><td style="border-bottom: hidden;border-top: hidden;border-right: hidden;padding: 1%">shmskjdhlskdjls</td>	
 -->	
 	

	<tr style="text-align: center; height: 50px; font-size: 40px;background-color: rgb(211,211,211);">	
		<td colspan="6"><b>Payment Voucher</b></tr>


	<tr><td colspan="3">Invoice No:<?php echo "$invoice_id";?></td>		
		<td colspan="3">Booking ID:
						<?php echo "$booking_id";?></td></tr>
	<tr>	
		<td colspan="3">Invoice Date: <?php echo "$invoice_date";?></td>
		<td colspan="3">Reverse Charge (Y/N): N</td>
	</tr>


	<tr style="text-align: center;background-color: rgb(211,211,211);"><td colspan="6"><b>Bill to Party</b></tr>


	<tr style="height: 20px" class="bold"><td colspan="6">Name: Blackmelon Advance Technology Co. Pvt. Ltd.</td></tr>
	<tr style="height: 5%"><td colspan="6">Address: A-34, FIRST FLOOR, SECTOR-63, NOIDA, UTTAR PRADESH, 201301</td></tr>
	<tr><td colspan="2">GSTIN: 09AAFCB1281J1ZM</td>
		<td colspan="3" width="30%">Place of Supply: Uttar Pradesh</td>	
		<td>Code: 09</td></tr>


	<tr style="text-align: center;background-color: rgb(211,211,211);">
		<td colspan="2"><b>Product Description</td>
		<td style="padding: 1px"><b>HSN/SAC<br>Code</td>
		<td><b>Qty</td>
		<td><b>Rate</td>
		<td><b>Value of Supply</td>

	</tr>


	<?php
		foreach ($record as $info) {
			$total_amount=$info['qty']*$info['rate'];
			echo "<tr style='text-align: center;'>
				<td colspan='2'>$info[description]</td>
				<td >$info[hsn_code]</td>
				<td >$info[qty]</td>
				<td >$info[rate]</td>
				<td>$total_amount</td>
			</tr>";
			$total_qty+=$info['qty'];			
			$sub_total_amount += $total_amount;
		}
	?>		

	<tr align="center"><td style="background-color: rgb(211,211,211);"><b>Total</td>	
		<td style="border-right: hidden;width: 20%"></td>	
		<td style="border-left: hidden;"></td>
		<td><b><?php echo "$total_qty";?></td>	
		<td></td>
		<td><b><?php echo "$sub_total_amount";?></td>
	</tr>
	
		<tr>
			<td colspan="6" align="center" style="background-color: rgb(211,211,211); font-weight: bold;"><b>Total Invoice amount in words</b></td>
		<tr>
			<td colspan="6 rowspan="1" style="padding: 2%; text-align: center;"><?php echo $price_inword;?></td>

		<tr style="text-align: center;">
			<td rowspan="3" align="center">Declaration: We declare that this invoice shows the actual price of the goods and services described and that all particulars are true and correct.</td>
			<td rowspan="3" colspan="2"></td>
			<td colspan="4" class="bold" style="background-color: rgb(211,211,211);">For <?php echo "$company_name";?></td>
		</tr>
		
		<tr>
			<td colspan="3" height="30%"><h1>&nbsp</td>
		</tr>
		
		<tr class="bold" align="center">
			<td colspan="3">Authorised signatory</td>
		</tr>
</table>
</td>
</tr>
<p style="text-align: center; margin-top: 0px"><small>This is a computer generated invoice, no signature is required.</small>

</body>
</html>
