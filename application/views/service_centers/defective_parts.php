<?php if($this->uri->segment(3)){ $sn_no =  $this->uri->segment(3) +1; } else{ $sn_no = 1;} ?>
<div class="container-fluid">

   <div class="row" style="margin-top: 40px;">
      <div class="col-md-12">
      
         <div class="panel panel-default">
            <div class="panel-heading">
                <h1 class="panel-title" ><i class="fa fa-money fa-fw"></i> Defective Parts Need To Be Shipped</h1>
            </div>
            <div class="panel-body">
               <div class="table-responsive">
                 
                   <table class="table table-bordered table-hover table-striped">
                       <thead>
                           <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Customer Name</th>
                            <th class="text-center">Booking Id</th>
                            <th class="text-center">Parts Received</th>
                            <th class="text-center">Remarks By Partner</th>
                            <th class="text-center">Update</th>
                           </tr>
                       </thead>
                       <tbody>
                           <tbody>
                                <?php  foreach($spare_parts as $key =>$row){?>
                               <tr style="text-align: center;<?php if(!is_null($row['remarks_defective_part_by_partner'])){ echo "color:red"; }?>">
                                    <td>
                                        <?php echo $sn_no; ?>
                                    </td>
                                     <td>
                                        <?php echo $row['name']; ?>
                                    </td>
                                    <td>
                                         <a  href="<?php echo base_url();?>service_center/booking_details/<?php echo urlencode(base64_encode($row['booking_id']));?>"  title='View'><?php echo $row['booking_id'];?></a>
                                    </td>
<!--                                    <td>
                                        <?php //echo $row['age_of_booking']; ?>
                                    </td>-->
                                    <td>
                                        <?php echo $row['parts_shipped']; ?>
                                    </td>

                                    <td>
                                        <?php if(!is_null($row['remarks_defective_part_by_partner'])){  echo $row['remarks_defective_part_by_partner']; } else { echo $row['remarks_by_partner'];} ?>
                                    </td>
                                    <td>
                                         <a href="<?php echo base_url() ?>service_center/update_defective_parts/<?php echo $row['booking_id']; ?>" class="btn btn-sm btn-primary" style="background-color:#2C9D9C; border-color: #2C9D9C;" ><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>
                                    </td>

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
<div class="custom_pagination" style="margin-left: 16px;" > <?php if(isset($links)) echo $links; ?></div>
</div>
