<?php if($this->uri->segment(3)){ $sn_no =  $this->uri->segment(3) +1; } else{ $sn_no = 1;} ?>
<div class="container-fluid">

   <div class="row" style="margin-top: 40px;">
      <div class="col-md-12">
      
         <div class="panel panel-default">
            <div class="panel-heading">
                <h1 class="panel-title" ><i class="fa fa-money fa-fw"></i> Defective Parts Received By Partner</h1>
            </div>
            <div class="panel-body">
               <div class="table-responsive">
                  
                   <table class="table table-bordered table-hover table-striped">
                       <thead>
                           <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Customer Name</th>
                            <th class="text-center">Booking Id</th>
                            <th class="text-center">Defective Parts Shipped</th>
                            <th class="text-center">Shipped Date</th>
                            <th class="text-center">AWB</th>
                            <th class="text-center">Courier Name</th>
                            <th class="text-center">Remarks</th>
                           </tr>
                       </thead>
                       <tbody>
                           <tbody>
                                <?php  foreach($spare_parts as $key =>$row){?>
                               <tr style="text-align: center;">
                                    <td>
                                        <?php echo $sn_no; ?>
                                    </td>
                                     <td>
                                        <?php echo $row['name']; ?>
                                    </td>
                                    <td>
                                         <a  href="<?php echo base_url();?>service_center/booking_details/<?php echo urlencode(base64_encode($row['booking_id']));?>"  title='View'><?php echo $row['booking_id'];?></a>
                                    </td>

                                    <td>
                                        <?php echo $row['defective_part_shipped']; ?>
                                    </td>

                                    <td>
                                        <?php if(!is_null($row['defective_part_shipped_date'])){  echo date("d-m-Y",strtotime($row['defective_part_shipped_date'])); }  ?>
                                    </td>
                                   <td>
                                        <?php echo $row['awb_by_sf']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['courier_name_by_sf']; ?>
                                    </td>
                                     <td>
                                        <?php echo $row['remarks_defective_part_by_sf']; ?>
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
