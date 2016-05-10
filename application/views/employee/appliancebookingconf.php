<div id="page-wrapper">
  <div class="">
    <div class="row">
      <div style="width:1000px;margin:50px;">
        <h2 style="color:blue;">Please verify booking details</h2>

        <form class="form-horizontal" method="POST" action=<?php echo (isset($booking['appliance_id']))? base_url()."employee/booking/process_appliance_booking_form" : base_url()."employee/bookings_excel/post_confirm_sd_lead_form"?>>
        <table class="table table-striped table-bordered">

            <tr>
                <td>User Name</td>
                <td><?= $booking['user_name']; ?></td>
            </tr>

            <tr>
                <td>Primary Contact Number</td>
                <td><?= $booking['booking_primary_contact_no']; ?></td>
            </tr>

            <tr>
                <td>Alternate Contact Number</td>
                <td><?= $booking['booking_alternate_contact_no']; ?></td>
            </tr>

            <tr>
                <td>Service Name</td>
                <td><?=$result[0]['services'];?></td>
            </tr>

            <tr>
                <td>No of Appliance</td>
                <td><?=$booking['quantity'];?></td>
            </tr>
            
            <tr>
            	<td>Booking For Date</td>
                <td><?=$booking['booking_date'];?></td>
            </tr>

            <tr>
            	<td>Booking Timeslot</td>
                <td><?=$booking['booking_timeslot'];?></td>
            </tr>

            <tr>
            	<td>Booking Address</td>
                <td><?=$booking['booking_address'];?></td>
            </tr>

            <tr>
            	<td>Booking Pincode</td>
                <td><?=$booking['booking_pincode'];?></td>
            </tr>

            <tr>
            	<td>Booking Remarks</td>
                <td><?=$booking['booking_remarks'];?></td>
            </tr>

            <tr>
                <td>Amount Due</td>
                <td><?=$booking['amount_due'];?></td>
            </tr>
        
             <div>
              <input type="hidden" name="appliance_id" value="<?php if(isset($booking['appliance_id'])){echo $booking['appliance_id'];}?>">
              <input type="hidden" name="lead_id" value="<?php if(isset($lead_id)){echo $lead_id;}?>">
              <input type="hidden" name="service_id" value="<?php echo $booking['service_id'];?>">
              <input type="hidden" name="user_id" value="<?php echo $booking['user_id'];?>">
              
                <input type="hidden" name="booking_primary_contact_no" value="<?php echo $booking['booking_primary_contact_no']; ?>">
                <input type="hidden" name="booking_alternate_contact_no" value="<?php echo $booking['booking_alternate_contact_no']; ?>">
                <input type="hidden" name="source" value="<?php echo $booking['source'];?>">

              <input type="hidden" name="booking_date" value="<?php echo $booking['booking_date'];?>">
              <input type="hidden" name="booking_timeslot" value="<?php echo $booking['booking_timeslot'];?>">
                <input type="hidden" name="booking_remarks" value="<?php echo $booking['booking_remarks'];?>">
                <input type="hidden" name="query_remarks" value="<?php echo $booking['query_remarks'];?>">
                <input type="hidden" name="booking_address" value="<?php echo $booking['booking_address'];?>">
                <input type="hidden" name="booking_pincode" value="<?php echo $booking['booking_pincode'];?>">
              
              <input type="hidden" name="appliance_brand" value="<?php echo $booking['appliance_brand'];?>">
              <input type="hidden" name="appliance_category" value="<?php echo $booking['appliance_category'];?>">
              <input type="hidden" name="appliance_capacity" value="<?php echo $booking['appliance_capacity'];?>">
              <input type="hidden" name="model_number" value="<?php echo $booking['model_number'];?>">
              <!--<input type="hidden" name="description" value=""> -->
              <input type="hidden" name="appliance_tags" value="<?php echo $booking['appliance_tags'];?>">
              <input type="hidden" name="items_selected" value="<?php echo $booking['items_selected'];?>">
              <input type="hidden" name="total_price" value="<?php echo $booking['total_price'];?>">
                <input type="hidden" name="amount_due" value="<?php echo $booking['amount_due']; ?>">

                <input type="hidden" name="type" value="<?php echo $booking['type'];?>">
                <input type="hidden" name="create_date" value="<?php echo $booking['create_date'];?>">
                <input type="hidden" name="quantity" value="<?php echo $booking['quantity'];?>">
                <input type="hidden" name="current_status" value="<?php echo $booking['current_status'];?>">
                <input type="hidden" name="services" value="<?php echo $result[0]['services'];?>">
                <input type="hidden" name="user_name" value="<?php echo $booking['user_name'];?>">
            </div>

        </table>
        
        <div style="align:center;">
        <table align="center" class="table table-striped table-bordered" name="appliance" style="height:300px;width:400px;">
            <tr>
                <td>Appliance Brand</td>
                <td><?=$booking['appliance_brand'];?></td>
            </tr>
            <tr>
                <td>Appliance category</td>
                <td><?=$booking['appliance_category'];?></td>
            </tr>
            <tr>
                <td>Appliance Capacity</td>
                <td><?=$booking['appliance_capacity'];?></td>
            </tr>
            <tr>
                <td>Services Selected</td>
                <td><?=$booking['items_selected'];?></td>
            </tr>
            <tr>
                <td>Total Price</td>
                <td><?=$booking['total_price'];?></td>
            </tr>
        </table>
        

        </div>

            <div><center><input type="Submit" value="Save Booking" class="btn btn-primary"></center></div>

        </form>
      </div>
    </div>
  </div>
</div>

