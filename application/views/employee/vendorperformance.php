<?php if(isset($vendor)) {?>
<script type="text/javascript">
		$(function() {

		getVendorPerformance();

	  $('input[name="datefilter"]').daterangepicker({
	      autoUpdateInput: false,
	      locale: {
	          cancelLabel: 'Clear'
	      }
	  });

	  $('input[name="datefilter"]').on('apply.daterangepicker', function(ev, picker) {
	      $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
	      getVendorPerformance();
	  });

	  $('input[name="datefilter"]').on('cancel.daterangepicker', function(ev, picker) {
	      $(this).val('');
	      getVendorPerformance();
	  });

	});
</script>
<div id="page-wrapper" >
<script src="<?php echo base_url()?>js/report.js"></script>
<script src="<?php echo base_url()?>js/moment.min.js"></script>
<script src="<?php echo base_url()?>js/daterangepicker.js"></script>
<link href="<?php echo base_url()?>css/daterangepicker.css" rel="stylesheet" />

	<div class="container-fluid" >
		<div class="panel panel-info" style="margin-top:20px;">
			<div class="panel-heading">
				<h2>Vendor Performance</h2>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-md-12">
						<ul class="vendor_performance ">
							<li class="col-md-2">
								<select onchange="getVendorPerformance()" class="form-control"  id="vendor" >
									<option>All</option>
									<?php 
									foreach ($vendor as $key => $value) { ?>

									<option value="<?php echo $value['Vendor_ID'] ?>"> <?php echo $value['Vendor_Name']; ?></option>

									<?php }
									?>
								</select>
							</li>
							<li class="col-md-2">
								<select onchange="getVendorPerformance()" class="form-control"  id="city" name="city" >
									<option selected disabled>Select city</option>
									<option value="">All</option>
									<?php 
									foreach ($city as $key => $City) { ?>

									<option value="<?php echo $City['City'] ?>"> <?php echo $City['City']; ?></option>

									<?php }
									?>
								</select>
							</li>
							<li class="col-md-2" style="border: 1px solid #bbb;">
								<select  onchange="getVendorPerformance()" class="form-control"  id="service" name="service" >
									<option selected disabled>Select Appliances</option>
									<option>All</option>
									<?php 
									foreach ($services as $key => $values) { ?>

									<option value="<?php echo $values['id'] ?>"> <?php echo $values['services']; ?></option>

									<?php }
									?>
								</select>
							</li>
							
							<li class="col-md-2" style="border: 1px solid #bbb;" >
								<select  onchange="getVendorPerformance()" class="form-control"  id="period" name="period" >
									<option  disabled>Select Period</option>
									<option  value = "" >All</option>
									<option value="All Year" selected>Year</option>
									<option value="All Month" >Month</option>
									<option >Quater</option>
									<option value="Week" >Week</option>
									
								</select> 
 

							</li>
							
						</ul>
					</div>
					<div class="col-md-12 col-md-offset-3"><img src="" id="loader_gif"></div>
					<div class="col-md-12" style="margin-top:20px;">
						<table class="table paginated  table-striped table-bordered" id="performance">
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$('#vendor').select2();
	$('#city').select2();
	$('#service').select2();
	$('#period').select2();
</script>

<style type="text/css">
	div.pager {
		text-align: center;
		margin: 1em 0;
	}

	div.pager span {
		display: inline-block;
		width: 1.8em;
		height: 1.8em;
		line-height: 1.8;
		text-align: center;
		cursor: pointer;
		background: #bce8f1;
		color: #fff;
		margin-right: 0.5em;
	}

	div.pager span.active {
		background: #c00;
	}
</style>
<?php } ?>

<?php  if(isset($data)){ ?>

<table class="table  table-striped table-bordered">

	<tr>
		<th>No.</th>
		<th>Vendor</th>
		<th>City</th>
		<th>Appliances</th>
		<th>Completed Booking</th>
		<th>Cancelled Booking</th>
		<th>Closed Date</th>
	</tr>
	<tbody>
		<?php $i=1;foreach ($data as $key => $variable) {
			$completed = 0 ; $cancelled =0 ; foreach ($variable as $keys => $value) { ?>
			<tr>
				<td><?php  echo $i; ?></td>
				<td><?php echo $variable[0]['Vendor_Name']; ?></td>
				<td><?php echo $variable[0]['City']; ?></td>
				<td><?php echo $variable[0]['Appliance']; ?></td>
				<td><?php echo $value['completed_booking']; $completed +=  $value['completed_booking'];?></td>
				<td><?php echo $value['cancelled_booking']; $cancelled +=  $value['cancelled_booking'];?></td>
				<td><?php if(isset($value['month'])) { if(isset($value['year'])){ echo $value['month']."  ".$value['year'];} else {echo $value['month'];} }  ?></td>
			</tr>


			<?php $i++; } ?>

			<tr style="height: 60px;">
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>

			<?php }  ?>
		</tbody>
	</table>

	<?php } ?>