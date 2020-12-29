<html>
    <script type="text/javascript" src="<?php echo base_url();?>js/jquery-1.3.2.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>js/jquery-ui-1.7.1.custom.min.js"></script>
    <script>
        $(document).ready(function()
          {
            $('#for_appliance').hide();
            $('#for_user').show();
            $('#for_user_page').show();
            $("#appliance_toogle_button").click(function()
              {
                $("#for_appliance").toggle();
                $("#for_user").toggle();
                $('#for_user_page').toggle();
                
            });
            
          });
    </script>
    <div id="page-wrapper" style="width:100%;">
        <div class="">
            <?php if(!empty($Bookings)){ ?>
            <div class="row">
                <div id="for_user" style="width:90%;margin:50px;">
                    <center>
                        <h2>Booking History: <?php echo $Bookings[0]->customername;?></h2>
                    </center>
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Booking ID</th>
                                <th>Name</th>
                                <th>Appliance</th>
                                <th>Booking Date</th>
                                <th>Booking Timeslot</th>
                                <th>Booking Type</th>
                                <th>Purchase Date</th>
                                <th>Booking Warranty Status <span data-toggle="tooltip" title="No Data found means waranty plan not found."><i class="fa fa-info-circle" aria-hidden="true"></i></span></th>
                                <th>Current Warranty Status <span data-toggle="tooltip" title="No Data found means waranty plan not found."><i class="fa fa-info-circle" aria-hidden="true"></i></span></th>
                                <th>Status</th>
                                <th>View</th>
                                <th>Go To Booking </th>
                                <th>Un-Cancel</th>
                                <th>Repeat</th>
                            </tr>
                        </thead>
                        <?php $count = 1; if(!empty($Bookings[0]->booking_id)){  foreach($Bookings as $key =>$row){ ?>
                        <tr>
                            <td><?php echo $count; $count++;?>.</td>
                            <td><?php echo $row->booking_id;?></td>
                            <td><?php echo $row->customername;?></td>
                            <td><?php echo $row->services;?></td>
                             <td><?php if(!empty($row->booking_date) && $row->booking_date != '0000-00-00') { echo date("d-M-Y", strtotime($row->booking_date)); }?></td>
                            <td><?php echo $row->booking_timeslot;?></td>
                            <td><?php echo $row->request_type;?></td>
                            <td id='purchase_date_<?php echo $row->booking_id;?>'></td>
                            <td id='booking_type_<?php echo $row->booking_id;?>'></td>
                            <td id='current_warranty_<?php echo $row->booking_id;?>'><script>$(document).ready(function(){check_warranty_booking('<?php echo $row->booking_id;?>');});</script></td>
                            <td><?php echo $row->current_status;?></td>
                            <td>
                                <a class='btn btn-sm btn-primary' href="<?php echo base_url();?>employee/booking/viewdetails/<?php echo $row->booking_id;?>"
                                    target='_blank'title='view'><i class='fa fa-eye' aria-hidden='true'></i></a>
                            </td>
                            <td>
                                
                                <a href="<?php echo  base_url();?>employee/user/finduser?booking_id=<?php echo $row->booking_id;?>" class="btn btn-small btn-success btn-sm" title="More Action"><i class="fa fa-bars" aria-hidden="true"></i></a>
                            </td>
                            <td>
                                <?php if ($row->current_status =='Cancelled' && strpos($row->booking_id, "Q") !== FALSE) {?>
                                <a class="btn btn-small btn-danger btn-sm" href="<?php echo base_url(); ?>employee/booking/open_cancelled_query/<?php echo $row->booking_id;?>" title="uncancel"><i class="fa fa-folder-open-o" aria-hidden="true"></i></a>
                                <?php } ?>
                            </td>
                           <td>
                             <?php if (!empty($row->service_center_closed_date) && $row->service_center_closed_date != '0000-00-00 00:00:00' && $row->current_status != 'Cancelled' && $row->internal_status != 'InProcess_Cancelled' && empty($row->cancellation_reason)) {
                                            $today = strtotime(date("Y-m-d"));
                                            $closed_date = strtotime($row->service_center_closed_date);
                                            $completedDays = round(($today - $closed_date) / (60 * 60 * 24));
                                            if($completedDays < _247AROUND_REPEAT_BOOKING_ALLOWED_DAYS){
                                    ?>
                                <a target="_blank" href="<?php echo base_url(); ?>employee/booking/get_repeat_booking_form/<?php echo $row->booking_id;?>" class="btn btn-small btn-success btn-sm" title="Create Repeat Booking"><i class="glyphicon glyphicon-plus" aria-hidden="true"></i></a>
                            <?php
                                            }
                                 }
                                 ?>
                          </td>
                        </tr>
                        <?php } }?>
                    </table>
                </div>
                <div class="row" style="margin-top: 20px;">
                    <div class="col-md-3 col-md-offset-2">
                        <a class="btn btn-primary" href="<?php echo base_url(); ?>employee/booking/addbooking/<?php echo $Bookings[0]->phone_number;?>">New Booking</a>
                    </div>
                    <div class="col-md-3">
                        <a href="<?php echo base_url();?>employee/user/get_edit_user_form/<?php echo $Bookings[0]->phone_number; ?>" class='btn btn-primary'>User Details</a>
                    </div>
                    <div class="col-md-3">
                        <input id="appliance_toogle_button" type="Button" value="Appliance Details" class='btn btn-primary'>
                    </div>
                </div>
            </div>
            <?php
                if(!empty($Bookings[0]->phone_number)){
            ?>
                <script>
                    $.ajax({
                           type: 'POST',
                           url: '<?php echo base_url(); ?>employee/user/get_sms_Send_detail_and_user_applinace/<?php echo $Bookings[0]->phone_number;?>',
                           success: function (data) {
                             console.log(data);
                             $("#ap_sms").html(data);

                           }
                         });
                </script>
            <?php
                }
            } ?>
        </div>
        <div id="ap_sms"></div>
    </div>
    </div>
     <script>
    function check_warranty_booking(booking_id){
        var datastring ="booking_id="+booking_id;
        var request = $.ajax({
        type: 'POST',
        data: datastring,
        url: "<?php echo base_url(); ?>employee/user/check_warranty_booking_search",
        beforeSend: function(){
            $("#purchase_date_"+booking_id).html("<img src='<?php echo base_url(); ?>images/loader.gif' style='width:30px'>");
            $("#booking_type_"+booking_id).html("<img src='<?php echo base_url(); ?>images/loader.gif' style='width:30px'>");
            $("#current_warranty_"+booking_id).html("<img src='<?php echo base_url(); ?>images/loader.gif' style='width:30px'>");
        },
        success: function(data){
            var obj = JSON.parse(data);
            $("#purchase_date_"+booking_id).html(obj.purchase_date);
            $("#booking_type_"+booking_id).html(obj.booking_warranty_status);
            $("#current_warranty_"+booking_id).html(obj.current_warranty_status);
        }
        });
    }
    </script>
</html>