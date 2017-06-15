<?php if($this->uri->segment(4)){ $count =  $this->uri->segment(4) +1; } else{ $count = 1;} ?>
<div class="container-fluid">
    <div class="row" style="margin-top: 40px;">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h1 class="panel-title" style="font-size:24px;"><i class="fa fa-money fa-fw"></i> <?php echo $status." Bookings" ?></h1>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>S No.</th>
                                    <th>Order ID</th>
                                    <th>247around Booking ID</th>
                                    <th>Call Type</th>
                                    <th>User Name</th>
                                    <th>Mobile</th>
                                    <th>City</th>
                                    <th>Closed Booking</th>
                                    <?php if($status == "Cancelled"){ ?>
                                    <th>Cancellation Reason</th>
                                    <th>Open</th>
                                    <?php } ?>
                                   
                                    
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($bookings as $key =>$row){?>
                                <tr>
                                    <td><?php if($row['is_upcountry']== 1 && $row['upcountry_paid_by_customer']== 0){ ?>
                                         <i style="color:red; font-size:20px;" onclick="open_upcountry_model('<?php echo $row['booking_id'];?>', '<?php echo $row['amount_due'];?>')"
                                       class="fa fa-road" aria-hidden="true"></i>
                                   <?php } ?>
                                        <?php echo $count; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['order_id']; ?>
                                    </td>
                                    <td>
                                       <a  href="<?php echo base_url();?>partner/booking_details/<?php echo $row['booking_id']?>" target='_blank' title='View'> 
                                           <?php echo $row['booking_id']; ?></a>
                                    </td>
                                    <td>
                                        <?php switch ($row['request_type']){
                                            case "Installation & Demo":
                                                echo "Installation";
                                                 break;
                                            case "Repair - In Warranty":
                                            case "Repair - Out Of Warranty":
                                                echo "Repair";
                                                 break;
                                            default:
                                                    echo $row['request_type'];
                                                    break;
                                            
                                            
                                            }  ?>
                                    </td>
                                    <td>
                                        <?php echo $row['customername'];?>
                                    </td>
                                    <td>
                                        <?php echo $row['booking_primary_contact_no']; ?>
                                    </td>
                                    <td>
                                        <?php echo  $row['city']; ?>
                                    </td>
                                    <td>
                                        <?php echo date('d-m-y', strtotime($row['booking_date'])); ?> 
                                    </td>
                                        <?php if($status == "Cancelled"){ ?>        
                                    <td>
                                        <?php echo $row['cancellation_reason']; ?>
                                    </td>
                                    <td>
                                        <a class='btn btn-sm btn-info' href="<?php echo base_url();?>partner/update_booking/<?php echo $row['booking_id']?>" target='_blank' title='View'><i class='fa fa-envelope-o' aria-hidden='true'></i></a>
                                    </td>
                                    <?php } ?>
                                    
                                </tr>
                                <?php $count++; } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- end  col-md-12-->
        </div>
    </div>
</div>
<div id="myModal1" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content" id="modal-content1">
            
        </div>
    </div>
</div>
<div class="custom_pagination" style="margin-left: 16px;" > <?php if(isset($links)) echo $links; ?></div>
<script>
function open_upcountry_model(booking_id, amount_due){
      
       $.ajax({
      type: 'POST',
      url: '<?php echo base_url(); ?>employee/partner/booking_upcountry_details/'+ booking_id+"/"+amount_due,
      success: function (data) {
         // console.log(data);
       $("#modal-content1").html(data);   
       $('#myModal1').modal('toggle');
    
      }
    });
    }
</script>