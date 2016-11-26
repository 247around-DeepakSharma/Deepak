<?php if($this->uri->segment(4)){ $sn_no =  $this->uri->segment(4) +1; } else{ $sn_no = 1;} ?>
<div class="container-fluid">
      <a href="<?php echo base_url(); ?>partner/download_spare_parts" class='btn btn-md btn-warning  pull-right' style="margin-right: 40px;margin-top:15px; margin-bottom: 15px;"><i class="fa fa-download" aria-hidden="true"></i></a>
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
<!--                    <form id="spare_form1" action="<?php echo base_url(); ?>partner/shipped_spare_parts" name="fileinfo1"  method="POST" enctype="multipart/form-data">-->
                   <table class="table table-bordered table-hover table-striped">
                       <thead>
                           <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Booking Id</th>
<!--                            <th class="text-center">Shipment Address</th>-->
                            <th class="text-center">Parts</th>
                            <th class="text-center">Model Number</th>
                            <th class="text-center">Serial Number</th>
                            <th class="text-center">Description</th>
                            <th class="text-center">Update</th>
                            <th class="text-center">Address</th>
                            <th class="text-center">Courier Manifest</th>
                            
                           </tr>
                       </thead>
                       <tbody>
                           <tbody>
                                <?php $sn_no1 = 1; foreach($spare_parts as $key =>$row){?>
                                <tr style="text-align: center;">
                                    <td>
                                        <?php echo $sn_no1; ?>
                                    </td>
                                    <td>
                                         <a  href="<?php echo base_url();?>partner/booking_details/<?php echo $row['booking_id'];?>"  title='View'><?php echo $row['booking_id'];?></a>
                                    </td>
<!--                                    <td data-popover="true" style="position: absolute; border:0px; white-space:nowrap; overflow:hidden;text-overflow:ellipsis;max-width: 206px;" data-html=true data-content="<?php echo $row['address']; ?> ">
                                              
                                        <?php echo $row['address']; ?>
                                    </td>-->
                                    <td>
                                        <?php echo $row['parts_requested']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['model_number']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['serial_number']; ?>
                                    </td>
                                    
                                    <td>
                                        <?php echo $row['remarks_by_sc']; ?>
                                    </td>
                                    
                                    <td>
                                        <a href="<?php echo base_url() ?>partner/update_spare_parts_form/<?php echo $row['booking_id']; ?>" class="btn btn-sm btn-primary" style="background-color:#2C9D9C; border-color: #2C9D9C;" ><i class='fa fa-eye' aria-hidden='true'></i></a>
                                    </td>
                                    <td>
                                        <a href="<?php echo base_url(); ?>partner/download_sc_address/<?php echo $row['booking_id']; ?>" class='btn btn-md btn-success' ><i class="fa fa-download" aria-hidden="true"></i></a>
                                    </td>
                                    <td <?php if(empty($row['parts_shipped'])){  ?>data-popover="true" data-html=true data-content="Please Update Shipped Parts" style=" border:0px; white-space:nowrap; overflow:hidden;text-overflow:ellipsis;" <?php } ?>>
                                        <a href="<?php echo base_url(); ?>partner/download_courier_manifest/<?php echo $row['booking_id']; ?>" class='btn btn-md btn-primary <?php if(empty($row['parts_shipped'])){ echo "disabled";} ?>' ><i class="fa fa-download" aria-hidden="true"></i></a>
                                    </td>
                                   
                                </tr>
                                <?php $sn_no1++; } ?>
                            </tbody>
                        </table>
<!--                        <div id="loading1" style="text-align: center;">
                            <input type= "submit" id="submit_button"  class="btn btn-danger btn-md" style="background-color:#2C9D9C; border-color: #2C9D9C;" value ="Update Booking" >
                        </div>
                    </form>-->
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<script type="text/javascript">
     $(".shipped_date").datepicker({dateFormat: 'yy-mm-dd'});
//     $(document).ready(function() {
//          $('body').popover({
//           selector: '[data-popover]',
//           trigger: 'click hover',
//           placement: 'auto',
//           delay: {
//               show: 50,
//               hide: 100
//           }
//        });
//     });
 $(document).ready(function() {
$('body').popover({
           selector: '[data-popover]',
           trigger: 'click hover',
           placement: 'auto',
           delay: {
               show: 50,
               hide: 100
           }
        });
         } );
</script>

 <?php $this->session->unset_userdata('success'); ?>