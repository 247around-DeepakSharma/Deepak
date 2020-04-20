<?php
$sf_owner_name="sd";
$sf_name="scc";
$date="12/2/2018";
$sf_address="ss";;
?>
<html>
<head>
	<title>SF Without GST Declaration</title>
	<style type="text/css">
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
	<table id="table2">
			<tr><td align="center"><h1>Declaration of GST Non-Enrollment</td></tr>
			<tr><td><b>To Whom It May Concern:</td></tr>
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
