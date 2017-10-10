<div id="page-wrapper">
   <div class="container-fluid">
      <div class="row">
         <div class="col-md-6 col-md-offset-1">
            <h1 class="page-header">User List</h1>
         </div>
      </div>
      <div class="row">
         <div class="col-md-8 col-md-offset-1">
            <table class="table table-bordered  table-hover table-striped data"  >
               <thead>
                  <tr >
                     <th>No #</th>
                     <th>User Name</th>
                     <th>Phone Number</th>
                     <th>Email ID</th>
                     <th>Address</th>
                  </tr>
               </thead>
               <tbody>
                  <?php $count = 1; if(!empty($Bookings)){ foreach($Bookings as $key =>$user_details) {?>
                  <tr>
                     <td><?php echo $count;?></td>
                     <td><a href="<?php echo base_url();?>employee/user/finduser?phone_number=<?php echo  $user_details->phone_number; ?>"><?php echo $user_details->customername; ?></a></td>
                     <td><?php if(!empty($user_details->alternate_phone_number)){
                        echo $user_details->phone_number.", ". $user_details->phone_number;
                        } else {
                        echo $user_details->phone_number;
                        } ?></td>
                     <?php  $count = $count+1;  ?>
                     <td><?php echo $user_details->user_email . " / " . $user_details->account_email; ?></td>
                     <td><?php echo $user_details->home_address . ", " . $user_details->pincode; ?></td>
                  </tr>
                  <?php }} ?>
               </tbody>
               </tbody>
            </table>
         </div>
      </div>
      <!-- end container- fluid -->
   </div>
</div>