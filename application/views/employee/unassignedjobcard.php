<?php
//echo "<pre>";
//print_r($query1);
//print_r($query2);
//exit();
?>
<div id="page-wrapper">
  <div class="">
    <div class="row">
      <div style="width:1000px;margin:50px;">

      	<p>
      		<b>Booking Details:-</b>
      		<br>Customer name: <?php echo $query1[0]['name']; ?>
      		<br>Customer phone number: <?php echo $query1[0]['phone_number']; ?>
      		<br>Customer email address: <?php echo $query1[0]['user_email']; ?>
      		<br>Booking Id: <?php echo $query1[0]['booking_id']; ?>
      		<br>Service name: <?php echo $query1[0]['services']; ?>
                <br>Units: <?php echo $query1[0]['quantity']; ?>
                <br>Booking date: <?php echo $query1[0]['booking_date']; ?>
                <br>Booking time slot: <?php echo $query1[0]['booking_timeslot']; ?>
                <br>Amount due: <?php echo $query1[0]['amount_due']; ?>
      		<br>Booking address: <?php echo $query1[0]['booking_address'];?>
                <br>Booking Pincode: <?php echo $query1[0]['booking_pincode']; ?>

                <br><br>

                <b>Appliance Details:-</b><br>
        	<?php for($i=0; $i<$query1[0]['quantity']; $i++) {?>

        	<br>Brand : <?php echo $query2[$i]['appliance_brand'];?>
        	<br>Category : <?php echo $query2[$i]['appliance_category'];?>
        	<br>Capacity : <?php echo $query2[$i]['appliance_capacity'];?>
        	<br>Selected service/s is/are : <?php echo $query2[$i]['price_tags'];?>
        	<br>Total price is : <?php echo $query2[$i]['total_price'];?>
        	<br>
        	<?php } ?>
        </p>

        <div>

        	<center><a href="<?php echo base_url();?>employee/booking/viewbooking"><input class="btn btn-primary btn-lg" type="button" value="Back" ></a></center>
        </div>

      </div>
    </div>
  </div>
</div>