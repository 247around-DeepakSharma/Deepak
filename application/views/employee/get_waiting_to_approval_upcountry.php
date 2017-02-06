
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
                                <?php
                if ($this->session->userdata('error')) {
                    echo '<div class="alert alert-danger alert-dismissible partner_error" role="alert">
                       <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                           <span aria-hidden="true">&times;</span>
                       </button>
                       <strong>' . $this->session->userdata('error') . '</strong>
                   </div>';
                }
                ?>
      
         <div class="panel panel-default">
            <div class="panel-heading">
                <h1 class="panel-title" ><i class="fa fa-money fa-fw"></i> Upcountry Booking</h1>
            </div>
            <div class="panel-body">
                 
               <div class="table-responsive">
                  
                   <table class="table table-bordered table-hover table-striped">
                       <thead>
                           <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Booking ID</th>
                            <th class="text-center">Call Type</th>
                            <th class="text-center">Customer Name</th>
                            <th class="text-center">Appliance</th>
                            <th class="text-center">Brand</th>
                            <th class="text-center">Category</th>
                            <th class="text-center">Capacity</th>
                            <th class="text-center">Customer Address</th>
                            <th class="text-center">Upcountry Distance</th>
<!--                            <th class="text-center">Approve</th>
                            <th class="text-center">Reject</th>-->
                           </tr>
                       </thead>
                       <tbody>
                           <tbody>
                                <?php $sn_no = 1; foreach($booking_details as $key =>$row){?>
                               <tr style="text-align: center;">
                                    <td>
                                        <?php echo $sn_no; ?>
                                    </td>
                                     <td>
                                       <a style="color:blue;" href="<?php echo base_url();?>partner/booking_details/<?php echo $row['booking_id'];?>"  title='View'><?php echo $row['booking_id'];?></a>
                                    </td>
                                    <td>
                                         <?php echo $row['request_type']; ?>
                                    </td>

                                    <td>
                                       <?php echo $row['name']; ?>
                                    </td>

                                    <td>
                                         <?php echo $row['services']; ?>
                                    </td>
                                   <td>
                                        <?php echo $row['appliance_brand']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['appliance_category']; ?>
                                    </td>
                                     <td>
                                        <?php echo $row['appliance_capacity']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['booking_address'].", ".$row['city'].", Pincode - ".$row['booking_pincode'].", ". $row['state']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['upcountry_distance']." KM" ; ?>
                                    </td>
<!--                                     <td>
                                         <a href="<?php //echo base_url() ?>partner/upcountry_charges_approval/<?php //echo $row['booking_id'] ?>/1" class="btn btn-md btn-success">Approve</a>
                                    </td>
                                     <td>
                                         <a href="<?php //echo base_url() ?>partner/reject_upcountry_charges/<?php //echo $row['booking_id'] ?>/1" class="btn btn-md btn-danger">Reject</a>
                                    </td>-->
                                </tr>
                                <?php $sn_no++; } ?>
                            </tbody>
                        </table>
                      
                        </div>
                   
               </div>
            </div>
         </div>
      </div>
   </div>

</div>
<?php $this->session->unset_userdata('success'); ?>
<?php $this->session->unset_userdata('error'); ?>