<?php if(isset($services)) {?>

<div id="page-wrapper" >
	<div class="container-fluid" >
		<div class="panel panel-info" style="margin-top:20px;">
			<div class="panel-heading"><h3>Check Availability For Vendor </h3> </div>
			<div class="panel-body">
				<table class="table  table-striped table-bordered">
					<tr>
						<th style="width: 22%"> 
							<select class="form-control"  id="service_id" name="services" >
								<option selected disabled>Select Services</option>
								<?php 
                                   foreach ($services as $key => $value) { ?>

                                   <option value="<?php echo $value['id'] ?>"> <?php echo $value['services']; ?></option>
                                   	
                                   <?php }
								?>
							</select>
						</th>
						<th>
							<select class="form-control"  onchange="checkVendor()" id="city" name="city" >
								<option value="Select City" >Select City</option>
								<?php 
                                   foreach ($city as $key => $values) { ?>

                                   <option value="<?php echo $values['City']; ?>"> <?php echo $values['City']; ?></option>
                                   	
                                   <?php }
								?>
							</select>
						</th>

						<th>
							<select class="form-control" onchange="checkVendor()" id="pincode" name="pincode" >
								<option value="Select Pincode">Select Pincode</option>
								<?php 
                                   foreach ($pincode as $key => $Pincode) { ?>

                                   <option value="<?php echo $Pincode['Pincode']; ?>"> <?php echo $Pincode['Pincode']; ?></option>
                                   	
                                   <?php }
								?>
							</select>
						</th>

					</tr>
				</table>
				<table class="table table-striped table-bordered" id="vendor"></table>
			</div>
		</div>
	</div>
</div>


<script type="text/javascript">
	$("#service_id").select2();

    $("#city").select2();

    $("#pincode").select2({
        tags: true
    });


    function checkVendor(){
    	var postdata ={};
    	postdata['service_id'] =  $("#service_id").val();
    	postdata['city'] =  $("#city").val();
    	postdata['pincode'] =  $("#pincode").val();

	    	$.ajax({
	        type: 'POST',
	        url: '<?php echo base_url() ?>employee/vendor/check_availability_for_vendor',
	        data: postdata,
	        success: function (data) {
	          console.log(data);
	          $("#vendor").html(data);   
	        }
        });

    }

</script>


<?php } ?>

<?php  if(isset($vendor)){ ?>

<table class="table table-striped table-bordered" >
    <th>No.</th>
	<th>Vendor Name</th>
	<th>Pincode</th>
	<th>Area</th>
	<tbody>
	<?php $i=1; foreach ($vendor as $key => $value) { ?>
	    <tr>
	    <td><?php echo $i; ?></td>
		<td><?php echo $value['Vendor_Name'] ?></td>
		<td><?php echo $value['Pincode'] ?></td>
		<td><?php echo $value['Area']; ?></td>
		</tr>
	<?php $i++ ; } ?>
		
	</tbody>
</table>


<?php } ?>

