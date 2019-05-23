<div id="page-wrapper" style="width:100%;">
   <div class="">
      <div class="row">
         <div id="for_user" style="width:90%;margin:50px;">
            <div class="panel panel-info" style="width:90%;margin:50px 0px 10px  50px;">
               <div class="panel-heading">
                   <center><span style="font-size: 120%;">Booking History
<!--                           <b><?php if(isset($data[0]['name'])){echo ucfirst($data[0]['name']);} else { echo "Booking Not Found";} ?></b>-->
                       </span></center>
               </div>
            </div>
            <table class="table table-striped table-bordered table-hover">
               <thead>
                  <tr>
                     <th>No.</th>
                     <th>Booking ID</th>
                     <th>Name</th>
                     <th>Appliance</th>
                     <th>Booking Date</th>
                     <th>Status</th>
                     <th>View</th>
                     <th>More Action</th>
                  </tr>
               </thead>
               <?php
                  if (isset($data[0]['booking_id'])) {
                      $count = 1;
                      ?>
               <?php foreach ($data as $key => $row) { ?>
               <tr>
                  <td><?php
                     echo $count;
                     $count++;
                     ?>.</td>
                  <td><?= $row['booking_id']; ?></td>
                  <td><?= $row['name']; ?></td>
                  <td><?= $row['services']; ?></td>
                  <td><?= $row['booking_date']." / ". $row['booking_timeslot']; ?></td>
                  <td><?php echo $row['internal_status']; ?></td>
                  <td>
                    <a href="<?php echo base_url(); ?>service_center/booking_details/<?php echo urlencode(base64_encode($row['booking_id'])) ?>"
                        target='_blank'title='view' class='btn btn-sm btn-primary'><i class='fa fa-eye' aria-hidden='true'></i></a>
                  </td> 

                  <td>
                      <?php  switch ($row['current_status']) {
                            case 'Pending':
                            case 'Rescheduled':
                            $view = 'service_center/pending_booking/'.$row['booking_id'];
                            break;

                            case 'Cancelled':
                            $view = 'service_center/cancelled_booking/0/0/'.$row['booking_id'];
                            break;

                            case 'Completed':
                            $view = 'service_center/completed_booking/0/0/'.$row['booking_id'];
                            break;

                            default:
                            $view = 'service_center/pending_booking/'.$row['booking_id'];
                            break;
                          }?>
                    
                      
                        <a href="<?php echo  base_url().$view;?>" class="btn btn-small btn-success btn-sm" title="More Action"><i class="fa fa-bars" aria-hidden="true"></i></a>

                      
                  </td>

               </tr>
               <?php
                  }
                  }
                  ?>
            </table>
         </div>
      </div>

   </div>
</div>
