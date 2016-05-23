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
									<option value="">All Vendor</option>
									<?php 
									foreach ($vendor as $key => $value) { ?>

									<option value="<?php echo $value['id'] ?>"> <?php echo $value['name']; ?></option>

									<?php }
									?>
								</select>
							</li>
							<li class="col-md-2">
								<select onchange="getVendorPerformance()" class="form-control"  id="city" name="city" >
									<option  disabled>Select city</option>
									<option value="" selected>All City</option>
									<?php 
									foreach ($city as $key => $City) { ?>

									<option value="<?php echo $City['City'] ?>"> <?php echo $City['City']; ?></option>

									<?php }
									?>
								</select>
							</li>
							<li class="col-md-2" style="border: 1px solid #bbb;">
								<select  onchange="getVendorPerformance()" class="form-control"  id="service" name="service" >
									<option  disabled>Select Appliances</option>
									<option value="" selected>All Appliances</option>
									<?php 
									foreach ($services as $key => $values) { ?>

									<option value="<?php echo $values['id'] ?>"> <?php echo $values['services']; ?></option>

									<?php }
									?>
								</select>
							</li>

							<li class="col-md-2" style="border: 1px solid #bbb;" >
								<select  onchange="getVendorPerformance()" class="form-control"  id="source" name="source" >
									<option  disabled>Select Source</option>
									<option  value = "" selected>All Source</option>
									<?php 
									foreach ($source as $key => $partner) { ?>

									<option value="<?php echo $partner['code'] ?>"> <?php echo $partner['source']; ?></option>

									<?php }
									?>
									
								</select> 
 

							</li>
							
							<li class="col-md-2" style="border: 1px solid #bbb;" >
								<select  onchange="getVendorPerformance()" class="form-control"  id="period" name="period" >
									<option  disabled>Select Period</option>
									<option  value = "" selected>All group By Date</option>
									<option value="All Year" >Year</option>
									<option value="All Month" >Month</option>
									<option >Quater</option>
									<option value="Week" >Week</option>
									
								</select> 
 

							</li>

							<li class="col-md-2" style="border: 1px solid #bbb;" >
								<select  onchange="getVendorPerformance()" class="form-control"  id="sort" name="sort" >
									<option  value = "DESC" selected>DESC</option>
									<option  value = "ASC" selected>ASC</option>
									
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
	$('#source').select2();
	$('#sort').select2();
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
		<th>Source</th>
		<th>Completed Booking</th>
		<th>Cancelled Booking</th>
		<th>% Completed Booking</th>
		<th>Closed Date</th>
	</tr>
	<tbody>
		<?php $i=1;foreach ($data as $key => $variable) {
			 foreach ($variable as $keys => $value) { $completed = 0 ; $cancelled =0 ; ?>
			<tr>
				<td><?php  echo $i; ?></td>
				<td><?php echo $variable[0]['Vendor_Name']; ?></td>
				<td><?php if(isset($variable[0]['City'])) { echo $variable[0]['City']; }?></td>
				<td><?php if(isset($variable[0]['Appliance'])) { echo $variable[0]['Appliance'];} ?></td>
				<td><?php if(isset($variable[0]['source'])){ echo $variable[0]['source']; }?></td>
				<td><?php echo $value['completed_booking']; $completed +=  $value['completed_booking'];?></td>
				<td><?php echo $value['cancelled_booking']; $cancelled +=  $value['cancelled_booking']; $total_booking = $completed + $cancelled;?></td>
				<td><?php if($total_booking >0){ $percentage = ($completed *100)/ ($completed + $cancelled); echo sprintf ("%.2f", $percentage ); } else { echo "0"; }?></td>
				<td><?php if(isset($value['month'])) { if(isset($value['year'])){ echo $value['month']."  ".$value['year'];} else {echo $value['month'];} }  ?></td>
			</tr>


			<?php $i++; } ?>

	

			<?php }  ?>
		</tbody>
	</table>

	<?php } ?>