<div class="container-fluid">
    <div class="row" style="margin-top: 40px;">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2 class="panel-title"><i class="fa fa-money fa-fw"></i> <?php echo $status." Bookings"; if($status == 'Completed') { echo '<a class="btn btn-primary btn-sm" href="'. base_url().'employee/service_centers/download_service_center_completed_bookings" id="download" style="float:right;font-size:12px;color:white;margin-top:-6px;">Download List</a>';} ?></h2>
                </div>

                <div class="panel-body">
                   
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Booking Id</th>
                                    <th>User Name</th>
                                    <th>Mobile</th>
                                    <th>Service&nbsp;Name</th>
                                    <th>Request&nbsp;Type</th>
                                    <th>Closing&nbsp;Date</th>
                                    <th>Closing&nbsp;Remarks</th>
                                    <?php if ($status == "Completed") { ?> 
                                    <th>SF&nbsp;Earned</th> 
                                    <th>Rating</th>
                                    <th>Engineer</th>
                                    <?php } ?>
                                    <th>View</th>
                                   
                                </tr>
                            </thead>
                            <tbody>
                                <?php $count = $serial_number +1; ?>
                                    <?php foreach($bookings as $key =>$row){?>
                                        <tr>
                                            <td>
                                                <?php echo $count; ?>
                                                <?php if($row['is_upcountry'] == 1) { ?>.
                                                <i style="color:red; font-size:20px;" 
                                                   onclick="open_upcountry_model('<?php echo $row['booking_id'];?>', '<?php echo $row['amount_due'];?>', '<?php echo $row['flat_upcountry'];?>')" 
                                                   class="fa fa-road" aria-hidden="true"></i><?php } ?>
                                            </td>
                                            <td>
                                               <?php if(!empty($row['penalty'])) {  ?> <image src="<?php echo base_url();?>images/icon_image.png" /> <?php } ?><?php echo $row['booking_id']; ?>
                                            </td>
                                            <td>
                                                <?php echo $row['customername'];?>
                                            </td>
                                            <td>
                                                <?php echo $row['booking_primary_contact_no']; ?>
                                            </td>
                                            <td>
                                                <?php echo  $row['services']; ?>
                                            </td>
                                            <td>
                                                <?php echo  $row['request_type']; ?>
                                            </td>

                                            <td><?php echo date('d-m-Y', strtotime($row['closed_date'])); ?></td>
                                             <td data-popover="true" style="position: absolute; border:0px; white-space:nowrap; overflow:hidden;text-overflow:ellipsis;max-width: 110px;" data-html=true data-content=" <?php if ($status == "Completed")
                                                            echo $row['closing_remarks'];
                                                          else 
                                                            echo $row['cancellation_reason'];  
                                                    ?>">
                                                 <?php if ($status == "Completed")
                                                            echo $row['closing_remarks'];
                                                          else 
                                                            echo $row['cancellation_reason'];  
                                                    ?>
                                            </td>
                                        
<!--                                            <td data-popover="true" style="position: absolute; border:0px; width: 12%; max-width: 100px; word-wrap:break-word;" data-html=true  data-content="
                                                    <?php if ($status == "Completed")
                                                            echo $row['closing_remarks'];
                                                          else 
                                                            echo $row['cancellation_reason'];  
                                                    ?>">
                                             
                                                   
                                                
                                            </td>-->
                                            <?php if ($status === "Completed") { ?> 
                                                     <td>
                                                <?php echo  "<i class='fa fa-inr'></i> ".round($row['sf_earned'],2); ?>
                                                    </td>
                                                    <td><?php if(!empty($row['rating_stars'])) 
                                                        { echo $row['rating_stars']."/<b>5</b>"; } 
                                                        else { echo "Ratings Not Taken Yet";}?></td>
                                                    <td><?php echo $row['Engineer'];?></td>
                                            <?php } ?>
                                            
                                            <td><a class='btn btn-sm btn-primary' href="<?php echo base_url();?>service_center/booking_details/<?php echo urlencode(base64_encode($row['booking_id']));?>" target='_blank' title='View'><i class='fa fa-eye' aria-hidden='true'></i></a></td>
                

                                        </tr>
                                        <?php $count++; } ?>
                            </tbody>
                        </table>
                   
                     <?php if(!empty($links)){ ?><div class="custom_pagination" style="float:left;margin-top: 20px;margin-bottom: 20px;"> <?php if(isset($links)){echo $links;} ?></div> <?php } ?>
                   
                </div>
            </div>

            <!-- end  col-md-12-->
        </div>
    </div>
</div>
<div id="myModal1" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" id="open_model">
        <!-- Modal content-->
        <div class="modal-content" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Upcountry Call</h4>
            </div>
            <div class="modal-body" >
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style type="text/css">
    .marquee {
        height: 100%;
        width: 100%;
        color: red;
        overflow: hidden;
        position: relative;
    }
    
    .marquee div {
        display: block;
        width: 100%;
        height: 22px;
    
        position: relative;
        overflow: hidden;
        animation: marquee 5s linear infinite;
    }
    
    .marquee span {
        
        width: 50%;
    }
    
    @keyframes marquee {
        0% {
            left: 0;
        }
        100% {
            left: -100%;
        }
    }

</style>
<script type="text/javascript">
    $('body').popover({
        selector: '[data-popover]',
        trigger: 'click hover',
        placement: 'auto',
        delay: {
            show: 50,
            hide: 100
        }
    });
    function open_upcountry_model(booking_id, is_customer_paid, flat_upcountry){
      
       $.ajax({
      type: 'POST',
      url: '<?php echo base_url(); ?>service_center/pending_booking_upcountry_price/' + booking_id+"/"+is_customer_paid+"/"+ flat_upcountry,
      success: function (data) {
       $("#open_model").html(data);   
       $('#myModal1').modal('toggle');
    
      }
    });
    }
</script>
