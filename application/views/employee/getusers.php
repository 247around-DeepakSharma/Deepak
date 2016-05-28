
<?php if(isset($city)) {?>
<div id="page-wrapper" >
<script src="<?php echo base_url()?>js/report.js"></script>

	<div class="container-fluid" >
		<div class="panel panel-info" style="margin-top:20px;">
			<div class="panel-heading">
				<h2>Booking</h2>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-md-12">
						<ul class="vendor_performance ">
							
							<li class="col-md-2">
								<select onchange="getusercount()" class="form-control"  id="city" name="city" >
									<option  disabled>Select city</option>
									<option value="" selected>All City</option>
									<?php 
									foreach ($city as $key => $City) { ?>

									<option value="<?php echo $City['City'] ?>"> <?php echo $City['City']; ?></option>

									<?php }
									?>
								</select>
							</li>

							<li class="col-md-2">
								<select onchange="getusercount()" class="form-control"  id="mon_user" >
									<option  disabled>Select Any One</option>
									<option value="" selected >All</option>
									<option value="All Month" >Month</option>
									<option value="All Year" >Year</option>
									<option value="Quater" >Quarter</option>
									<option value="Week" >Week</option>
									
								</select>
							</li>

							<li class="col-md-2" style="border: 1px solid #bbb;">
								<select onchange="getusercount()" class="form-control"  id="source" name="source" >
									<option  disabled>Select Source</option>
									<option value="" selected>All Source</option>
									<?php 
									foreach ($source as $key => $partner) { ?>

									<option value="<?php echo $partner['code'] ?>"> <?php echo $partner['source']; ?></option>

									<?php }
									?>
								</select>
							</li>
							
							
							<!--<li class="col-md-2"  >
							 <input type="text" class="form-control" style="height:29px;" placeholder="Custom Date Range" name="datefilter" value="" />
								
							</li>-->
							<li class="col-md-2" style="border: 1px solid #bbb;" >
							<p id="total_user"></p>
							</li>
							<li class="col-md-2" style="border: 1px solid #bbb;" >
							 <p id="completed_booking_user"></p>
							</li>
							<li class="col-md-2" style="border: 1px solid #bbb;" >
							 <p id="cancelled_booking_user"></p>
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


<script type="text/javascript">
	
	$('#city').select2();
	$('#mon_user').select2();
	$('#source').select2();
    $(function() {

	  getusercount();

	});
	
</script>

<?php  } if (isset($user)){ ?>


    <table class="table table-striped table-bordered" >
        <tr>
            <th>Source</th>
            <th>Booking</th>
            <th>Pending</th>
            <th>Completed Booking </th>
            <th>Cancelled Booking </th>
             <th>% Completed Booking</th>
            <th>Month/Year</th>
        </tr>

        <tbody>
        <?php $total_booking = 0; $scheduled = 0 ;$completed = 0; $cancelled =0; foreach ($user as $value) {  ?>
        	<tr>
        	    <td><?php echo $value['source'];  ?></td>
        	    <td><?php echo $value['total_booking'];  $total_booking += $value['total_booking']; ?></td>
        	    <td><?php echo $value['scheduled']; $scheduled += $value['scheduled'];  ?></td>
        	    <td><?php echo $value['completed_booking_user']; $completed += $value['completed_booking_user']; ?></td>
        	    <td><?php echo $value['cancelled_booking_user']; $cancelled += $value['cancelled_booking_user']; ?></td>
        	    <?php $total = $value['completed_booking_user'] + $value['cancelled_booking_user'];?>
        	    <td><?php if($total>0){ $percantage = ($value['completed_booking_user'] *100)/($total); echo round($percantage,2); } else { echo "0"; } ?></td>
        	    <td><?php if(isset($value['month'])) { if(isset($value['year'])){ echo $value['month']."  ".$value['year'];} else {echo $value['month'];} }  ?></td>

        	</tr>

        <?php  } ?>
        <tr>
        	<td><b>Total</b></td>
        	<td><b><?php echo $total_booking; ?></b></td>
        	<td><b><?php echo $scheduled; ?></b></td>
        	<td><b><?php echo $completed; ?></b></td>
        	<td><b><?php echo $cancelled; ?></b></td>
        	<td><b><?php if($total_booking >0){ $total_percantage = ($completed *100)/($total_booking); echo round($total_percantage,2);} else { echo "0"; } ?></b></td>
        	<td></td>
        </tr>
      
        </input>
        </tbody>

    </table>
      <input type="hidden" value="<?php  echo $total_booking; ?>" id ="total_booking_user"></input>
        <input type="hidden" value="<?php echo $completed; ?>" id ="total_booking_completed_booking_user">
        <input type="hidden" value="<?php echo $cancelled; ?>" id ="total_booking_cancelled"></input>

<?php } ?>


