<?php if($this->uri->segment(4)){ $sn_no =  $this->uri->segment(4) +1; } else{ $sn_no = 1;} ?>
<div class="container-fluid">
   <div class="row" style="margin-top: 40px;">
       <div class="col-md-12">
           <div class="panel panel-default">
            <div class="panel-heading">
               <h2 class="panel-title"><i class="fa fa-money fa-fw"></i> Pending Bookings </h2>
            </div>
            <div class="panel-body">
               <div class="table-responsive">
                  <table class="table table-bordered table-hover table-striped">
                     <thead>
                         <tr>
                             <th class="text-center">S No.</th>
                           <th class="text-center">Booking ID</th>
                           <th class="text-center">User</th>
                           <th class="text-center">Mobile</th>
                           <th class="text-center">Service Center</th>
                           <th class="text-center">Partner</th>
                           <th class="text-center">Parts Requested</th>
                           <th class="text-center">Parts Shipped</th>
                           <th class="text-center">Status</th>
                           <th class="text-center">Booking Status</th>
                             
                         </tr>
                     </thead>
                     <tbody>
                         <?php foreach ($spare_parts as $value) { ?>
                          <tr>
                              <td class="text-center"><?php echo $sn_no; ?></td>
                              <td class="text-center"><a 
			     href="<?php echo base_url(); ?>employee/booking/viewdetails/<?php echo $value['booking_id']; ?>" target='_blank' title='view'><?php echo $value['booking_id'];?></a>
			    </td>
                              <td class="text-center"><?php echo $value['name'];?></td>
                              <td class="text-center"><?php echo $value['booking_primary_contact_no'];?></td>
                              <td class="text-center"><?php echo $value['sc_name'];?></td>
                              <td class="text-center"><?php echo $value['source'];?></td>
                              <td class="text-center"><?php echo $value['parts_requested'];?></td>
                              <td class="text-center"><?php echo $value['parts_shipped'];?></td>
                              <td class="text-center"><?php echo $value['status'];?></td> 
                              <td class="text-center"><?php echo $value['current_status'];?></td> 
                         </tr>
                             
                        <?php $sn_no++; }?>
                        
                     </tbody>
                  </table>
               </div>
            </div>
           </div>
           
       </div>
   </div>
     <div class="custom_pagination" style="margin-left: 16px;" > <?php if(isset($links)){ echo $links;} ?></div>
</div>