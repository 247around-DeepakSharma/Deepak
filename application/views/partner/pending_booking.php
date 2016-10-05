<div class="container-fluid">
   <div class="row" style="margin-top: 40px;">
      <div class="col-md-12">
       <?php if($this->session->userdata('success')) {
                    echo '<div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $this->session->userdata('success') . '</strong>
                    </div>';
                    }
                    ?>
         <div class="panel panel-default">
            <div class="panel-heading">
               <h2 class="panel-title"><i class="fa fa-money fa-fw"></i> Pending Bookings </h2>
            </div>
            <div class="panel-body">
               <div class="table-responsive">
                  <table class="table table-bordered table-hover table-striped">
                     <thead>
                        <tr>
                           <th>S No.</th>
                           <th>Order ID</th>
                           <th>247around Booking ID</th>
                           <th>User Name</th>
                           <th>Mobile</th>
                           <th>City</th>
                           <th>Booking Date</th>
                           <th>View</th>
                           <th>Reschedule</th>
                           <th>Cancel</th>
                           <th>Job Card</th>
                          
                        </tr>
                     </thead>
                     <tbody>
                        <?php $sn_no = 1; ?>
                        <?php foreach($bookings as $key =>$row){?>
                        <tr>
                           <td>
                              <?php echo $sn_no; ?>
                           </td>
                           
                           <td>
                              <?=$row->order_id; ?>
                           </td>
                           <td >
                              <?php
                            if (is_null($row->booking_jobcard_filename)) {
                                echo $row->booking_id;
                            } else {
                                echo '<a href="https://s3.amazonaws.com/bookings-collateral/jobcards-pdf/' . $row->booking_jobcard_filename . '">' . $row->booking_id . '</a>';
                            }
                            ?>
                           </td>
                           <td>
                              <?=$row->customername;?>
                           </td>
                           <td>
                              <?= $row->booking_primary_contact_no; ?>
                           </td>
                           <td>
                              <?= $row->city; ?>
                           </td>
                          
                           <td>
                              <?= $row->booking_date; ?> /
                              <?= $row->booking_timeslot; ?>
                           </td>
                          
                           
                         
                           <td><a class='btn btn-sm btn-primary' href="<?php echo base_url();?>partner/booking_details/<?=$row->booking_id?>" target='_blank' title='View'><i class='fa fa-eye' aria-hidden='true'></i></a></td>
                           <td>
                           <a href="<?php echo base_url(); ?>partner/get_reschedule_booking_form/<?php echo $row->booking_id; ?>" id="reschedule" class="btn btn-sm btn-success" title ="Reschedule"><i class='fa fa-calendar' aria-hidden='true' ></i></a>
                            
                           </td>

                            <td><a href="<?php echo base_url(); ?>partner/get_cancel_form/Pending/<?php echo $row->booking_id; ?>" class='btn btn-sm btn-danger' title='Cancel'><i class='fa fa-times' aria-hidden='true'></i></a>
                           </td>
                            <td><a href="https://s3.amazonaws.com/bookings-collateral/jobcards-pdf/<?php echo $row->booking_jobcard_filename?> " class='btn btn-sm btn-warning btn-sm' download><i class="fa fa-download" aria-hidden="true"></i></a></td>
                           
                        </tr>
                        <?php $sn_no++; } ?>
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
         <!-- end  col-md-12-->
      </div>
   </div>
</div>
 <div class="custom_pagination" style="margin-left: 16px;" > <?php if(isset($links)) echo $links; ?></div>

 <?php $this->session->unset_userdata('success'); ?>
      