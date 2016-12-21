<?php if($this->uri->segment(3)){ $sn_no =  $this->uri->segment(3) +1; } else{ $sn_no = 1;} ?>
<div class="container-fluid">

   <div class="row" style="margin-top: 40px;">
        <?php if($this->session->userdata('success')) {
                    echo '<div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $this->session->userdata('success') . '</strong>
                    </div>';
                    }
                    ?>
      <div class="col-md-12">
      
         <div class="panel panel-default">
            <div class="panel-heading">
                <h1 class="panel-title" style="font-size:24px;"><i class="fa fa-money fa-fw"></i> Defective Parts Shipped By SF</h1>
            </div>
            <div class="panel-body">
               <div class="table-responsive">
                 
                   <table class="table table-bordered table-hover table-striped">
                       <thead>
                           <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Customer Name</th>
                            <th class="text-center">Booking Id</th>
                            <th class="text-center">Parts Shipped</th>
                            <th class="text-center">Courier Name</th>
                            <th class="text-center">AWB</th>
                            <th class="text-center">Shipped Date</th>
                            <th class="text-center">Remarks</th>
                            <th class="text-center">Received</th>
                            <th class="text-center">Reject</th>

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
                                         <a  href="<?php echo base_url();?>partner/booking_details/<?php echo $row['booking_id'];?>"  title='View'><?php echo $row['booking_id'];?></a>
                                    </td>
<!--                                    <td>
                                        <?php //echo $row['age_of_booking']; ?>
                                    </td>-->
                                    <td>
                                        <?php echo $row['defective_part_shipped']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['courier_name_by_sf']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['awb_by_sf']; ?>
                                    </td>
                                     <td>
                                        <?php echo date("d-m-Y", strtotime($row['defective_part_shipped_date'])); ?>
                                    </td>
                                    
                                    <td>
                                        <?php echo $row['remarks_defective_part_by_sf']; ?>
                                    </td>
                                    <td><a href="<?php echo base_url();?>partner/acknowledge_received_defective_parts/<?php echo $row['booking_id'];?>" style="font-size:21px;"><i class="fa fa-check" aria-hidden="true"></i></a></td>
                                    <td>
                                        <div class="dropdown" style="position:absolute;">
                <a href="#" style="font-size:21px;color:red;" class="dropdown-toggle" type="button" data-toggle="dropdown"><i class="fa fa-times" aria-hidden="true"></i>
                <span class="caret"></span></a>
                 <ul class="dropdown-menu" style="right: -41px;left: auto;">
                     <?php foreach($internal_status  as $value){ ?>
                     <li><a href="<?php echo base_url();?>partner/reject_defective_part/<?php echo $row['booking_id']; ?>/<?php echo urlencode(base64_encode($value->status)); ?>"><?php echo $value->status;?></a></li>
                  <li class="divider"></li>
                     <?php } ?>
                  
                </ul>
               
              </div>
                                    
                                    
                                    
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
<div class="custom_pagination" style="margin-left: 16px;" > <?php if(isset($links)) {echo $links;} ?></div>
</div>


 <?php if($this->session->userdata('success')) { $this->session->unset_userdata('success'); } ?>
