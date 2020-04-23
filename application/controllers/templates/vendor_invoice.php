<?php
	include('vendor_invoice_variables.php');
?>
<html>
<head>
	<title>Vendor Invoice</title>
	<style type="text/css">
		table{
			border:solid;
			border-collapse: collapse;
			width: 960px;
			height: 100%;
			font-family: sans-serif;
			font-size: 100%;
			margin: auto;padding:2%;
		}
		td{
			border: solid 3px;	
			padding: 1%;
		}
		
	</style>
</head>
<body>
	
	<table>
				<tr style="border-bottom: hidden;">
					<td></td>
					<td colspan="4" style="border-left: hidden;border-right: hidden;"><b>Invoice Number: <?php echo "$invoice_no";?></td>
					<td colspan="4"><b>Date: <?php echo "$date";?></td></tr>
				<tr style="border-bottom: hidden;">
					<td></td>
					<td colspan="4" style="border-left: hidden;border-right: hidden;"></td>
					<td colspan="4"><b>Invoice Period: <?php echo "$invoice_period_from";?> TO <?php echo "$invoice_period_to";?></td></tr>
				<tr style="border-bottom: hidden;text-align: center;"><td><b>From</td>
					<td colspan="4" style="border-left: hidden;border-right: hidden;text-align: left;"><b><?php echo "$from";?></td>
					<td colspan="4" align="left"><b>Bookings: <?php echo "$bookings";?></td>
				</tr>
				<tr style="border-bottom: hidden;"><td></td>
					<td colspan="4" style="border-left: hidden;border-right: hidden;"><?php echo "$from_address";?></td>
					<td colspan="4"></td>
				</tr>
				<tr ><td style="border-bottom: hidden;">&nbsp</td>
					<td colspan="4" style="border-bottom: hidden;border-left: hidden;border-right: hidden;"></td>
					<td rowspan="3" colspan="4" align="right" style="padding: 1%"><img style="padding: 5px;" src="https://aroundhomzapp.com/images/logo.jpg"></td>
				</tr>
				<tr style="border-bottom: hidden; text-align: center;"><td><b>To</td>
					<td colspan="4" style="border-left: hidden;border-right: hidden;text-align: left;"><b>Blackmelon Advance Technology Co. Pvt. Ltd.</td>
				</tr>
				<tr ><td></td>
					<td colspan="4" style="border-left: hidden;border-right: hidden;">92C/1, Lane 7, East Azad Nagar,<br>
						Delhi 110051<br>
						Service Tax Number: AAFCB1281JSD001<br>
						TIN Number: 07627112651
					</td>
				</tr>
		
		<tr style="text-align: center; height: 8%; padding: 2%">
			<td><b>Booking ID</b></td>
			<td><b>Service</b></td>
			<td><b>Booking Date</b></td>
			<td><b>Closed Date</b></td>
			<td><b>Service Charge</b></td>
			<td><b>Additional Service Charge</b></td>
			<td><b>Parts Cost</b></td>
			<td><b>Amount Paid</b></td>
			<td><b>Rating</b></td>
		</tr>
		<?php
			foreach ($record as $info) {
				$i++;
				$amount_paid=$info['service_charge']+$info['additional_service_charge']+$info['parts_cost'];
				echo "<tr align=center>
					<td style='border-right:hidden'>$info[booking_id]
					<td style='border-right:hidden'>$info[service]
					<td style='border-right:hidden'>$info[booking_date]
					<td style='border-right:hidden'>$info[closed_date]
					<td style='border-right:hidden'>$info[service_charge]
					<td style='border-right:hidden'>$info[additional_service_charge]
					<td style='border-right:hidden'>$info[parts_cost]
					<td style='border-right:hidden'>$amount_paid
					<td>$info[rating]
					</tr>";
				$total_amount_collected += $amount_paid;
				$total_service_charge+=$info['service_charge'];
				$total_additional_service_charge+=$info['additional_service_charge'];
				$total_parts_cost+=$info['parts_cost'];
				$total_rating+=$info['rating'];
			}
			$avg_rating = $total_rating/$i;
		?>		
		<tr><td colspan="9"><h2>&nbsp</td></tr>
		<tr><td colspan="9" align="center"><b>Royality Invoice</b></td></tr>
		<tr><td colspan="9" style="padding: 1%;text-align: center;"><b>Thanks 247around partner for your support, we completed 37 bookings with you from 01-02-2016 till 01-03-2016, total transaction value for the bookings was Rs. 23238. Around royalty for this invoice is Rs. 16267. Your rating for completed bookings is 4.6. We look forward to your continued support in future. As a next step, 247around will pay you remaining amount as per our agreement.</b></td></tr>
		<tr height="8%" style="padding: 1%;font-weight: bold; text-align: center;"><td colspan="4"></td>
			<td>Service Charge</td>
			<td>Additional Service Charge</td>
			<td>Parts Cost</td>
			<td>Total Amount Collected</td>
			<td>Rating</td>
		</tr>
		<tr style="font-weight: bold; padding: 2%; text-align: center;"><td colspan="4"></td>
			<td><?php echo"$total_service_charge"?></td>
			<td><?php echo"$total_additional_service_charge"?></td>
			<td><?php echo"$total_parts_cost"?></td>
			<td><?php echo"$total_amount_collected"?></td>
			<td><?php echo"$avg_rating"?></td>
		<tr style="font-weight: bold; padding: 2%; text-align: center;">
			<td colspan="4">247around Royality %tage</td>
			<td><?php echo "$service_charge_royality";?></td>
			<td><?php echo"$additional_service_charge_royality"?></td>
			<td><?php echo"$parts_cost_royality"?></td>
			<td></td>
			<td></td>
		<tr style="font-weight: bold; padding: 2%; text-align: center;">
			<td colspan="4">247around Royality Breakup Rs.</td>
			<td><?php 
					$royality_breakup_service_charge=$total_service_charge*$service_charge_royality/100;
					echo round($royality_breakup_service_charge);?></td>
			<td><?php echo round($total_additional_service_charge*$additional_service_charge_royality/100)?></td>
			<td><?php echo round($total_parts_cost*$parts_cost_royality/100)?></td>
			<td></td>
			<td></td></tr>
		<tr style="font-weight: bold; padding: 2%; text-align: center;">
			<td colspan="4">Amount To Be Paid by 247around Rs.</td>
			<td></td>
			<td><?php echo round($total_service_charge-$royality_breakup_service_charge)?></td>
			<td></td>
			<td></td>
			<td></td></tr>

		<tr><td colspan="9"><h1>&nbsp</td></tr>
		<tr><td colspan="9"><b>Please verify your bank details registered with us and inform us in case of any discrepancy at the earliest.</b></td></tr>
		<tr align="center" style="border-bottom: hidden;"><td colspan="3"><b>Benefitiary Name</b></td>
			<td colspan="6"><?php echo "$beneficiary_name";?></td>
		</tr>
		<tr align="center" style="border-bottom: hidden;"><td colspan="3"><b>Benefitiary Account No.</b></td>
			<td colspan="6"><?php echo "$beneficiary_account_no";?></td>
		</tr>
		<tr align="center" style="border-bottom: hidden;"><td colspan="3"><b>Benefitiary Bank Name</b></td>
			<td colspan="6"><?php echo "$beneficiary_bank_name";?></td>
		</tr>
		<tr align="center"><td colspan="3"><b>IFSC Code</b></td>
			<td colspan="6"><?php echo "$ifsc_code";?></td>
		</tr>

	</table>

</body>
</html>