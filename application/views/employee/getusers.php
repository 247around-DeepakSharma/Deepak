
<div id="page-wrapper" >
<script src="<?php echo base_url()?>js/moment.min.js"></script>
<script src="<?php echo base_url()?>js/daterangepicker.js"></script>
<link href="<?php echo base_url()?>css/daterangepicker.css" rel="stylesheet" />

	<div class="container-fluid" >
		<div class="panel panel-info" style="margin-top:20px;">
			<div class="panel-heading">
				<h2>User</h2>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-md-12">
						<ul class="vendor_performance ">
							
							<li class="col-md-2">
								<select onchange="getusercount()" class="form-control"  id="city" name="city" >
									<option  disabled>Select city</option>
									<option value="" selected>All</option>
									<?php 
									foreach ($city as $key => $City) { ?>

									<option value="<?php echo $City['City'] ?>"> <?php echo $City['City']; ?></option>

									<?php }
									?>
								</select>
							</li>
							
							<li class="col-md-2" style="border: 1px solid #bbb;" >
							 <input type="text" class="form-control" style="height:29px;" placeholder="Custom Date Range" name="datefilter" value="" />
								
							</li>
							<li class="col-md-2" style="border: 1px solid #bbb;" >
							<p id="total_user"></p>
							</li>
							<li class="col-md-2" style="border: 1px solid #bbb;" >
							 <p id="completed_booking_user"></p>
							</li>
						</ul>
					</div>
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
	
	$('#city').select2();

	function getusercount(){

		var postData = {};
		
		postData['city'] = $('#city').val();
		postData['date_range'] = $('input[name="datefilter"]').val();
		$.ajax({
				type: 'POST',
				url: '<?php echo base_url(); ?>employee/user/getusercount',
				data: postData,
				success: function (data) {

					var json = JSON.parse(data);

					$('#total_user').html("Total User:  " +json.total_user);
					$('#completed_booking_user').html("Completed Booking: " +json.completed_booking_user);
				}
		});
    	
    }

    $(function() {

	  $('input[name="datefilter"]').daterangepicker({
	      autoUpdateInput: false,
	      locale: {
	          cancelLabel: 'Clear'
	      }
	  });

	  getusercount();

	  $('input[name="datefilter"]').on('apply.daterangepicker', function(ev, picker) {
	      $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
	      getusercount();
	  });

	  $('input[name="datefilter"]').on('cancel.daterangepicker', function(ev, picker) {
	      $(this).val('');
	      getusercount();
	  });

	});
	
</script>


