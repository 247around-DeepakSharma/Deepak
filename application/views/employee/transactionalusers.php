<div id="page-wrapper">
<script src="<?php echo base_url()?>js/report.js"></script>
<script src="<?php echo base_url()?>js/moment.min.js"></script>
<script src="<?php echo base_url()?>js/daterangepicker.js"></script>
<link href="<?php echo base_url()?>css/daterangepicker.css" rel="stylesheet" />
<div class="container-fluid" >
  <div class="panel panel-info" style="margin-top:20px;">
    <div class="panel-heading">
        <h2>Transactional User</h2>
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
		<select onchange="getusercount()" class="form-control"  id="mon_user" name="mon_user">
		  <option  disabled>Select Any One</option>
                  <option value="Unique User" selected >Unique User</option>
                  <option value="All Month" >All Month</option>
		  <option value="All Year" >All Year</option>
		  <option value="Quater" >Quarter</option>									
		</select>
	    </li>
							
            <li class="col-md-2">
                <select onchange="getusercount()" class="form-control"  id="user_source" name="user_source" style="color:black;">
                  <option disabled>Select Any One</option>
		  <option value="Android App" selected >AndroidApp</option>
		  <option value="Snapdeal" >Snapdeal</option>
		  <option value="Website" >Website</option>
		  <option value="Call Center" >Call Center</option>
                  <option value="Paytm" >Paytm</option>
                  <option value="Just Dial" >Justdial</option>
                </select>
            </li>
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
        $('#user_source').select2();
        //user_source
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




    <table class="table table-striped table-bordered" >
        <tr>

            <th>Total User</th>
            <th>Total Booking Completed</th>
            <th>Total Booking Cancelled</th>
            <th>Month/Year</th>
        </tr>

        <tbody>
        <?php $total_user = 0; $completed = 0; $cancelled =0; foreach ($user as $value) { ?>
        	<tr>
                    <td><?php echo $user_count;  $total_user += $value['total_user']; ?></td>
        	    <td><?php echo $value['total_user'];  $total_user += $value['total_user']; ?></td>
        	    <td><?php echo $value['completed_booking_user']; $completed += $value['completed_booking_user']; ?></td>
        	    <td><?php echo $value['cancelled_booking_user']; $cancelled += $value['cancelled_booking_user']; ?></td>
        	    <td><?php if(isset($value['month'])) { if(isset($value['year'])){ echo $value['month']."  ".$value['year'];} else {echo $value['month'];} }  ?></td>

        	</tr>

        <?php  } ?>
      
        </input>
        </tbody>

    </table>
      <input type="hidden" value="<?php  echo $total_user; ?>" id ="total_booking_user"></input>
        <input type="hidden" value="<?php echo $completed; ?>" id ="total_booking_completed_booking_user">
        <input type="hidden" value="<?php echo $cancelled; ?>" id ="total_booking_cancelled"></input>




        