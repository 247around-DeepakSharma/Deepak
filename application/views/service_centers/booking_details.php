<div class="container-fluid">
   <a href="https://s3.amazonaws.com/bookings-collateral/jobcards-pdf/<?php echo $query1[0]['booking_id']; ?> " class='btn btn-md btn-warning  pull-right' download style="margin-right: 40px;margin-top:15px;"><i class="fa fa-download" aria-hidden="true"></i></a>
   <div class="row" style="margin-top: 60px;">

      <div class="col-md-6">
         <div class="panel panel-default">
            <div class="panel-heading">
               <h2 class="panel-title"><i class="fa fa-money fa-fw"></i>  Customer Details</h2>
            </div>
            <div class="panel-body">
               <table class="table table-bordered table-hover table-striped">
                  <tr>
                     <td >Customer name: </td>
                     <td><?php echo $query1[0]['name']; ?></td>
                  </tr>
                  <tr>
                     <td>Customer phone number: </td>
                     <td><?php echo $query1[0]['phone_number']; ?></td>
                  </tr>
                  <tr>
                     <td>Alternate phone number: </td>
                     <td><?php echo $query1[0]['alternate_phone_number']; ?></td>
                  </tr>
                  <tr>
                     <td>Customer email address: </td>
                     <td><?php echo $query1[0]['user_email']; ?></td>
                  </tr>
                  <tr>
                     <td>Home address: </td>
                     <td><?php echo $query1[0]['home_address'];?></td>
                  </tr>
                  <tr>
                     <td>Home City: </td>
                     <td><?php echo $query1[0]['city'];?></td>
                  </tr>
                  <tr>
                     <td>Home State: </td>
                     <td><?php echo $query1[0]['state'];?></td>
                  </tr>
                  <tr>
                     <td>Home Pincode: </td>
                     <td><?php echo $query1[0]['pincode'];?></td>
                  </tr>
               </table>
            </div>
         </div>
      </div>
      <!-- end md-6-->
      <div class="col-md-6">
         <div class="panel panel-default">
            <div class="panel-heading">
               <h2 class="panel-title"><i class="fa fa-money fa-fw"></i>  Applicance Details</h2>
            </div>
            <div class="panel-body">
               <?php for($i=0; $i<$query1[0]['quantity']; $i++) {?>
               <table class="table table-bordered table-hover table-striped">
                  <tr>
                     <td >Brand<?=$i+1;?> : </td>
                     <td><?php echo $query2[$i]['appliance_brand'];?></td>
                  </tr>
                  <tr>
                     <td>Model<?=$i+1;?> : </td>
                     <td><?php echo $query2[$i]['model_number'];?></td>
                  </tr>
                  <tr>
                     <td>Category<?=$i+1;?> : </td>
                     <td><?php echo $query2[$i]['appliance_category'];?></td>
                  </tr>
                  <tr>
                     <td>Capacity<?=$i+1;?> : </td>
                     <td><?php echo $query2[$i]['appliance_capacity'];?></td>
                  </tr>
                  <tr>
                     <td>Description<?=$i+1;?> : </td>
                     <td><?php echo $query1[0]['description'];?></td>
                  </tr>
                  <?php for($j = 0; $j < count($query2); $j++) {?>
                  <tr>
                     <td>Selected services: </td>
                     <td><?php echo $query2[$j]['price_tags'];?></td>
                  </tr>
                  <tr>
                     <td>Total price: </td>
                     <td><?php echo $query2[$j]['total_price'];?></td>
                  </tr>
                  <?php } ?>
               </table>
               <?php } ?>
            </div>
         </div>
      </div>
      <!-- end md-6 -->
       <div class="col-md-12">
         <div class="panel panel-default">
            <div class="panel-heading">
               <h2 class="panel-title"><i class="fa fa-money fa-fw"></i>  Booking Details</h2>
            </div>
            <div class="panel-body">
            <div class="col-md-6">
            	 <table class="table table-bordered table-hover table-striped">
      		<tr><td>Booking Id: </td><td><?php echo $query1[0]['booking_id']; ?></td></tr> 
          <tr><td>Order Id: </td><td><?php if(!empty($query4)){ echo $query4[0]['order_id']; } ?></td></tr>
          <tr><td>Booking Type: </td><td><?php echo $query1[0]['type']; ?></td></tr>
      		<tr><td>Service name: </td><td><?php echo $query1[0]['services']; ?></td></tr>
          <tr><td>Source: </td><td><?php echo $query1[0]['source']; ?></td></tr>
          <tr><td>Number of appliances: </td><td><?php echo $query1[0]['quantity']; ?></td></tr>
          <tr><td>Booking date: </td><td><?php echo $query1[0]['booking_date']; ?></td></tr>
          <tr><td>Booking time slot: </td><td><?php echo $query1[0]['booking_timeslot']; ?></td></tr>
      		<tr><td>Booking address: </td><td><?php echo $query1[0]['booking_address'];?></td></tr>
           <tr><td>Booking City: </td><td><?php echo $query1[0]['city']; ?></td></tr>
            <tr><td>Booking State: </td><td><?php echo $query1[0]['state']; ?></td></tr>
          <tr><td>Booking Pincode: </td><td><?php echo $query1[0]['booking_pincode']; ?></td></tr>
         
          <tr><td>Booking Primary Contact No.: </td><td><?php echo $query1[0]['booking_primary_contact_no']; ?></td></tr>
          <tr><td>Booking Alternate Contact No.: </td><td><?php echo $query1[0]['booking_alternate_contact_no']; ?></td></tr>
          <tr><td>Booking Remarks: </td><td><?php echo $query1[0]['booking_remarks']; ?></td></tr>
          <tr><td>Query Remarks: </td><td><?php echo $query1[0]['query_remarks']; ?></td></tr>
          <tr><td>Service Center Remarks: </td><td><?php echo $query3[0]['service_center_remarks']; ?></td></tr>
          <tr><td>Admin Remarks: </td><td><?php echo $query3[0]['admin_remarks']; ?></td></tr>
         </table>
            </div>
            <div class="col-md-6">
            <table class="table table-bordered table-hover table-striped">
            	 <tr><td>Booking current status: </td><td><?php echo $query1[0]['current_status']; ?></td></tr>
          <tr><td>Booking internal status: </td><td><?php echo $query1[0]['internal_status']; ?></td></tr>
          <tr><td>Booking closed date: </td><td><?php echo $query1[0]['closed_date']; ?></td></tr>
          <tr><td>Potential Value: </td><td><?php echo $query1[0]['potential_value']; ?></td></tr>
          <tr><td>Amount Due: </td><td><?php echo $query1[0]['amount_due']; ?></td></tr>
          <tr><td>Amount Paid: </td><td><?php echo $query1[0]['amount_paid']; ?></td></tr>
          <tr><td>Service charges: </td><td><?php echo $query1[0]['service_charge']; ?></td></tr>
          <tr><td>Additional service charges: </td><td><?php echo $query1[0]['additional_service_charge']; ?></td></tr>
          <tr><td>Parts cost: </td><td><?php echo $query1[0]['parts_cost']; ?></td></tr>
          </table>
            </div>
            </div>
            </div>
            </div>

        <!-- end md-6 -->
   </div>
</div>