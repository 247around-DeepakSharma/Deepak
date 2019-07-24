<?php
$image=" ";
$email="billing@247around.com";
$gstin=4578346547856	;
$invoice_no=55765879;
$date="17th May, 2018";
$reference_no=235342;
$period="17th May, 2018 - 17th May, 2018";
$reverse_charge="N";
$name="Demo";
$address="fkddfkjgkghgkh-201305, UTTARPRADESH";
$gstin2= 79639183;
$place_of_supply= "Noida";
$code= 1;
$total_qty=0;
$total_taxable_value=0;
$total_amount=0;
$total=0;
$amount_in_words="xxxx";
$bank_name="ICICI";
$acc_no=1111111111111;
$ifsc="hgkajhdkad";
$stamp=" ";
$signature_image=" ";
$gst_on_reverse_charge=0;
$record= array(array('description'=>'tv','hsn_sac_code'=>456,'qty'=>1,'rate1'=>" ",'taxable_value'=>45,'rate2'=>23,'amount'=>21,'total'=>22),
			array('description' => 'fridge','hsn_sac_code'=>226,'qty'=>1,'rate1'=>" ",'taxable_value'=>45,'rate2'=>23,'amount'=>21,'total'=>22 ));
?>
<html>
<head>
	<title>Credit Note</title>
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
		.subtable{
			border:solid 2px;
			border-collapse: collapse;
			width: 960px;
			height: 20%;
			font-family: sans-serif;
			font-size: 100%;
			margin: 0px;
			border-top: 0px;
			text-align: center;
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
		<td style="text-align: center;border-bottom: hidden; " colspan="8" ><h1>Blackmelon Advance Technology Co. Pvt. Ltd.</h1></td></tr>
	<tr style="border-bottom:hidden;"><td rowspan="2" width="20%" align="left" style="border-right: hidden;"><?php echo $image?></td>
		<td colspan="6" width="75%" align="center0" style="border:hidden; padding-left: 5%">A-1/7a, A BLOCK, KRISHNA NAGAR, DELHI-110051</td>
		<td rowspan="2" colspan="3" style="text-align: right; width: 20%; border-left: hidden;"><b>(Original Copy)</td>
	</tr>
	<tr style="border: hidden;"><td colspan="6" align="center" style="padding-right: 15%">Email: <?php echo "$email";?></td></tr>
<!-- 	<tr><td style="border-bottom: hidden;border-top: hidden;border-right: hidden;padding: 1%">shmskjdhlskdjls</td>	
 -->	
 	<tr style="text-align: center;" ><td colspan="9">GSTIN :  <?php echo "$gstin"; ?></td></tr>
	

	<tr style="text-align: center; height: 50px; font-size: 40px;background-color: rgb(211,211,211);">	
		<td colspan="9"><b>Credit Note</b></tr>


	<tr><td colspan="3">Invoice No:<?php echo "$invoice_no";?></td>		
		<td colspan="5">Reference No:<?php echo "$reference_no";?></td></tr>
	<tr><td colspan="3">Date: <?php echo "$date";?></td>	
		<td colspan="5">Period:<?php echo $period;?></td></tr>
	<tr><td colspan="2">Reverse Charge (Y/N):</td>
		<td><?php echo $reverse_charge?></td>
		<td colspan="5"></td>
	</tr>


	<tr style="text-align: center;background-color: rgb(211,211,211);"><td colspan="8"><b>Bill to Party</b></tr>


	<tr style="height: 20px"><td colspan="9"><b>Name: <?php echo $name?></b></td></tr>
	<tr style="height: 5%"><td colspan="9"><?php echo "$address";?></td></tr>
	<tr><td colspan="2">GSTIN: <?php echo "$gstin2";?></td>
		<td colspan="5" style="border-left: hidden;">Place of Supply: <?php echo "$place_of_supply";?></td>	
		<td>Code:<?php echo "$code";?></td></tr>


	<tr style="text-align: center;background-color: rgb(211,211,211);">
		<td rowspan="2" width="30%"><b>Product Description</td>
		<td rowspan="2" width="6%" style="padding: 1px"><b>HSN/SAC<br>Code</td>
		<td rowspan="2" width="12%"><b>Qty</td>
		<td rowspan="2" width="12%"><b>Rate</td>
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
				<td width='17%'>$info[hsn_sac_code]</td>
				<td width='10%'>$info[qty]</td>
				<td width='8%'>$info[rate1]</td>
				<td>$info[taxable_value]</td>
				<td width='12%'>$info[rate2]</td>
				<td width='15%'>$info[amount]</td>
				<td>$info[total]</td>
			</tr>";
			$total_qty+=$info['qty'];
			$total_taxable_value+=$info['taxable_value'];
			$total_amount+=$info['amount'];
			$total += $info['total'];
		}
	?>		


	<tr align="center"><td style="background-color: rgb(211,211,211);"><b>Total</td>	
		<td></td>	
		<td><b><?php echo "$total_qty";?></td>	
		<td></td>	
		<td><?php echo "$total_taxable_value";?></td>
		<td></td>
		<td><?php echo "$total_amount";?></td>
		<td><b><?php echo "$total";?></td>
	</tr>

		<tr align="center" >
			<td width="60%" colspan="4"><b>Total Invoice amount in words</b></td>
			<td width="30%" colspan="3"><b>Total Amt before Tax</td>
			<td width="10%"><b><?php echo "$total_taxable_value";?></td>
		</tr>
		<tr align="center">
			<td colspan="4" rowspan="2" style="padding: 2%"><?php echo $amount_in_words;?></td>
			<td colspan="3"><b>Add: IGST</td>
			<td><b><?php echo "$total_amount";?></td>
		</tr>
		<tr><td colspan="3">Total Amt after Tax</td>
			<td><?php echo "$total";?></td>
		</tr>

		<tr>
			<td align="center" style="background-color: rgb(211,211,211);">Bank Details</td>
			<td rowspan="6" colspan="3"><?php echo $stamp?></td>
			<td colspan="3"><b>GST on Reverse Charge</td>
			<td><?php echo $gst_on_reverse_charge?></td>
		</tr>
		<tr><td>Bank Name: <?php echo "$bank_name";?></td>
			<td rowspan="2" colspan="4" style="background-color: rgb(211,211,211);">For Blackmelon Advance Technology Co. Pvt. Ltd.</td>
		</tr>
		<tr><td>AccNo: <?php echo "$acc_no";?></td></tr>
		<tr><td>IFSC: <?php echo "$ifsc";?></td>
			<td rowspan="2" colspan="4"><?php echo "$signature_image";?></td>
		</tr>
		<tr>
			<td style="border-bottom: hidden;">	</td>
		</tr>
		<tr><td>	</td>
			<td colspan="4"><b>Authorised Signature</b></td>
		</tr>
</table>
</td>
</tr>
<p style="text-align: center; margin-top: 0px"><small>This is a computer generated invoice, no signature is required.</small>

</body>
</html>

