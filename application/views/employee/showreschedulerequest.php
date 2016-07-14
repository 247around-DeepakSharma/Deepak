<script type="text/javascript">
  $(document).ready(function(){ 
    $("#selecctall").change(function(){
      $(".checkbox1").prop('checked', $(this).prop("checked"));
      });
});

</script>
<div id="page-wrapper">
        <div class="row">
         <div style="width:100%;margin-left:10px;margin-right:5px;">
                <h2 align="left">
                    Request For Reschedule Bookings
                </h2>


                <div class="col-md-12">
                 <form action="<?php echo base_url();?>employee/new_booking/process_reschedule_booking" method="post">
                
                <table class="table table-bordered table-hover table-striped">
                <thead>
                	<tr>
                		<th>SNo.</th>
                		<th>Booking Id</th>
                		<th>Service Center </th>
                        <th>User Name</th>
                        <th>User Phone Number</th>
                        <th>Quantity</th>
                        <th>Booking Date</th>
                        <th>Reschedule Booking Date</th>
                        <th>Reschedule Reason</th>
                         <th  ><input type="checkbox" id="selecctall" />  Reschedule</th>
                        <th></th>
                	</tr>
                </thead>
                <tbody>
                	<?php $sno = 1; foreach ($data as $key => $value) { ?>
                		<tr>
                		<td><?php echo $sno; ?></td>
                		<td><?php echo $value['booking_id'];  ?></td>
                		<td><?php echo $value['service_center_name'];  ?></td>
                		<td><?php echo $value['customername'];  ?></td>
                		<td><?php echo $value['booking_primary_contact_no'];  ?></td>
                		<td><?php echo $value['quantity'];  ?></td>
                		<td><?php echo $value['booking_date']." / ".$value['booking_timeslot'] ;  ?></td>
                		<td><?php echo $value['reschedule_date_request']." / ".$value['reschedule_timeslot_request'] ;  ?>
                        <input type="hidden" name="reschedule_booking_date[<?php echo $value['booking_id']; ?>]" value="<?php echo $value['reschedule_date_request'] ?>" ></input>
                        <input type="hidden" name="reschedule_booking_timeslot[<?php echo $value['booking_id']; ?>]" value="<?php echo $value['reschedule_timeslot_request'] ?>" ></input>
                		</td>
                		<td><?php echo $value['service_center_remarks'];  ?></td>
                		<td><input type="checkbox"  class="checkbox1" name="reschedule[]" value="<?php echo $value['booking_id']; ?>"></input>
                         </td>
                		</tr>
                	<?php $sno++;  } ?>

                </tbody>
                </table>
                <?php if(!empty($data)){?>
                <div class"col-md-12">
                <center><input type="submit" value="Save Bookings" class="btn btn-md btn-success"></input></center>
                
                </div>
                 <?php } ?>
                 </form>
                </div>
           </div>
        </div>
</div>