<?php
$name="Demo";
$phone=8888888888;
$invoice_no=873;
$booking_id="id99";
$reverse_charge=7888;
$place_of_supply="Delhi";
$code=3459;
$amount_in_words="Thirty thousand";
$invoice_date="27/05/2018";
$total_qty=4;
$total_value_of_supply=0;
$record= array(array('description' => 'tv','hsn_sac_code'=>456,'qty'=>1,'rate'=>45,'value'=>45 ),
			
			array('description' => 'fridge','hsn_sac_code'=>226,'qty'=>1,'rate'=>2675,'value'=>2670 ),
			array('description' => 'fridge','hsn_sac_code'=>226,'qty'=>1,'rate'=>2675,'value'=>2670 ),
			array('description' => 'fridge','hsn_sac_code'=>226,'qty'=>1,'rate'=>2675,'value'=>2670 ),
			array('description' => 'fridge','hsn_sac_code'=>226,'qty'=>1,'rate'=>2675,'value'=>2670 ),
			array('description' => 'fridge','hsn_sac_code'=>226,'qty'=>1,'rate'=>2675,'value'=>2670 ),
			array('description' => 'fridge','hsn_sac_code'=>226,'qty'=>1,'rate'=>2675,'value'=>2670 ),
			array('description' => 'fridge','hsn_sac_code'=>226,'qty'=>1,'rate'=>2675,'value'=>2670 ),
			array('description' => 'fridge','hsn_sac_code'=>226,'qty'=>1,'rate'=>2675,'value'=>2670 ),
			array('description' => 'fridge','hsn_sac_code'=>226,'qty'=>1,'rate'=>2675,'value'=>2670 ),
			array('description' => 'fridge','hsn_sac_code'=>226,'qty'=>1,'rate'=>2675,'value'=>2670 ),
			array('description' => 'fridge','hsn_sac_code'=>226,'qty'=>1,'rate'=>2675,'value'=>2670 ),
			array('description' => 'fridge','hsn_sac_code'=>226,'qty'=>1,'rate'=>2675,'value'=>2670 ),
			array('description' => 'fridge','hsn_sac_code'=>226,'qty'=>1,'rate'=>2675,'value'=>2670 ),
			array('description' => 'fridge','hsn_sac_code'=>226,'qty'=>1,'rate'=>2675,'value'=>2670 ),
			array('description' => 'fridge','hsn_sac_code'=>226,'qty'=>1,'rate'=>2675,'value'=>2670 ));
?>
<html>
<head>
	<title>Payment Voucher</title>
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
			margin: auto;
			border-top: 0px;
			text-align: center;
		}
		td{
			border:solid 2px;
			padding: 1%;
		}
		.no_padding{
			padding: 0px;
		}
		
		
	</style>
</head>
<body>
<table class="table">
	<tr>
		<td style="text-align: center;"  colspan="5"><h1> Name <?php echo "$name"?></h1></td></tr>
	<tr><td style="border-bottom: hidden;border-top: hidden;border-right: hidden;padding: 1%">shmskjdhlskdjls</td>	
		<td style="text-align: right;border-bottom: hidden;border-top: hidden;"  colspan="4"><b>(Duplicate Copy)</b></td></tr>
	<tr style="text-align: center;" ><td colspan="5">Phone: <?php echo "$phone"; ?></td></tr>
	<tr style="text-align: center; height: 50px; font-size: 40px;background-color: rgb(211,211,211);">	
		<td colspan="5"><b>Payment Voucher</b></tr>
	<tr class="no_padding"><td>Invoice No:<?php echo "$invoice_no";?></td>		
		<td colspan="4">Booking Id:<?php echo "$booking_id";?></td></tr>
	<tr class="no_padding"><td>Invoice Date: <?php echo "$invoice_date";?></td>	
		<td colspan="4">Reverse Charge (Y/N):<?php echo $reverse_charge;?></td></tr>
	<tr style="text-align: center;background-color: rgb(211,211,211);"><td colspan="5"><b>Bill to Party</b></tr>
	<tr style="height: 50px"><td colspan="5"><b>Name: Blackmelon Advance Technology Co. Pvt. Ltd.</b></td></tr>
	<tr style="height: 20%"><td colspan="5">sdfjkhfkjdksdnkdjkjgjfck</td></tr>
	<tr><td>dgjhdfjkghjkfg</td>
		<td colspan="3">Place of Supply: <?php echo "$place_of_supply";?></td>	
		<td colspan="3">Code:<?php echo "$code";?></td></tr>
	<tr style="text-align: center;background-color: rgb(211,211,211);"><td><b>Product Description</td>
		<td><b>HSN/SAC<br>Code</td>
		<td><b>Qty</td>
		<td><b>Rate</td>
		<td><b>Value of<br>Supply</td></tr>
	<?php
		foreach ($record as $info) {
			echo "<tr align=center><td>$info[description]</td>
				<td>$info[hsn_sac_code]
				<td>$info[qty]
				<td>$info[rate]
				<td>$info[value]</td></tr>";
			$total_value_of_supply += $info['value'];
		}
	?>		
	<tr align="center"><td style="background-color: rgb(211,211,211);"><b>Total</td>	
		<td></td>	
		<td><b><?php echo "$total_qty";?></td>	
		<td></td>	
		<td><b><?php echo "$total_value_of_supply";?></td></tr>
	<tr align="center" style="background-color: rgb(211,211,211);"><td colspan="5"><b>Total Invoice amount in words</b></td></tr>
	<tr align="center"><td colspan="5" style="padding: 2%"><?php echo $amount_in_words;?></td></tr>
	<table class="subtable">
		<tr>
			<td rowspan="4" style="padding: 1%; border-top: 0px; height: 10%;">Declaration: We declare that this invoice shows the actual price of the goods and services described and that all particulars are true and correct.</td></tr>
			<td rowspan="4" colspan="2" style="border-top: 0px; width: 25%;height: 10%"></td>
			<td style="background-color: rgb(211,211,211);width: 50%;height : 20%;padding-left: 1%;border-top: 0px">dfidjkdfgjdkdkfjdskfjdkfjd</td>
		</tr>
		<tr style="height: 70%"><td></td></tr>
		<tr><td style="padding: 0%; height: 10%"><b>Authorised Signature</b></td></tr>
	</table>
	<p style="text-align: center; margin-top: 0px"><small>This is a computer generated invoice, no signature is required.</small>
</table>
</body>
</html>

