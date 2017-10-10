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
                                <th>Status</th>
                                <th>View</th>
                                <th>Go To Booking </th>
                                <th>Un-Cancel</th>
                            </tr>
                        </thead>
                        <?php $count = 1;  foreach($Bookings as $key =>$row){ ?>
                        <tr>
                            <td><?php echo $count; $count++;?>.</td>
                            <td><?php echo $row->booking_id;?></td>
                            <td><?php echo $row->customername;?></td>
                            <td><?php echo $row->services;?></td>
                            <td><?php echo $row->booking_date;?></td>
                            <td><?php echo $row->booking_timeslot;?></td>
                            <td><?php echo $row->current_status;?></td>
                            <td>
                                <a class='btn btn-sm btn-primary' href="<?php echo base_url();?>employee/booking/viewdetails/<?php echo $row->booking_id;?>"
                                    target='_blank'title='view'><i class='fa fa-eye' aria-hidden='true'></i></a>
                            </td>
                            <td>
                                <?php 
                                    if ($row->type == "Query") {
                                        //It is a query, check its status and assign appropriate view
                                      switch ($row->current_status) {
                                          case 'FollowUp':
                                          $view = 'employee/booking/view_queries/FollowUp/p_all/'.$row->booking_id;
                                          break;
                                    
                                          case 'Cancelled':
                                            $view = 'employee/booking/view_queries/Cancelled/p_all/'.$row->booking_id;
                                            break;
                                    
                                            default:
                                            $view = 'employee/booking/view_queries/FollowUp/p_all/'.$row->booking_id;
                                            break;
                                      }
                                    
                                    } else {
                                      //It is a booking, find its status first.
                                      switch ($row->current_status) {
                                        case 'Pending':
                                        case 'Rescheduled':
                                        $view = 'employee/booking/view_bookings_by_status/Pending/'.$row->booking_id;
                                        break;
                                    
                                        case 'Cancelled':
                                        $view = 'employee/booking/view_bookings_by_status/Cancelled/'.$row->booking_id;
                                        break;
                                    
                                        case 'Completed':
                                        $view = 'employee/booking/view_bookings_by_status/Completed/'.$row->booking_id;
                                        break;
                                    
                                        default:
                                        $view = 'employee/booking/view_bookings_by_status/Pending/'.$row->booking_id;
                                        break;
                                      }
                                    }
                                    
                                    ?>
                                <a href="<?php echo  base_url().$view;?>" class="btn btn-small btn-success btn-sm" title="More Action"><i class="fa fa-bars" aria-hidden="true"></i></a>
                            </td>
                            <td>
                                <?php if ($row->current_status =='Cancelled' && strpos($row->booking_id, "Q") !== FALSE) {?>
                                <a class="btn btn-small btn-danger btn-sm" href="<?php echo base_url(); ?>employee/booking/open_cancelled_query/<?php echo $row->booking_id;?>" title="uncancel"><i class="fa fa-folder-open-o" aria-hidden="true"></i></a>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php }?>
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
            <?php } ?>
        </div>
        <div id="ap_sms"></div>
    </div>
    </div>              
</html>