<div class="container-fluid">
    <div class="row" style="margin-top: 60px;">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2 class="panel-title"><i class="fa fa-money fa-fw"></i>  Customer Details</h2>
                </div>
                <div class="panel-body">
                    <table class="table table-bordered table-hover table-striped">
                        <tr>
                            <td >Name: </td>
                            <td><?php echo $query1[0]['name']; ?></td>
                        </tr>
                        <tr>
                            <td>Mobile Number: </td>
                            <td><?php echo $query1[0]['phone_number']; ?></td>
                        </tr>
                        <tr>
                            <td>Alternate Number: </td>
                            <td><?php echo $query1[0]['alternate_phone_number']; ?></td>
                        </tr>
                        <tr>
                            <td>Email: </td>
                            <td><?php echo $query1[0]['user_email']; ?></td>
                        </tr>
                        <tr>
                            <td>Address: </td>
                            <td><?php echo $query1[0]['home_address'];?></td>
                        </tr>
                        <tr>
                            <td>City: </td>
                            <td><?php echo $query1[0]['city'];?></td>
                        </tr>
                        <tr>
                            <td>State: </td>
                            <td><?php echo $query1[0]['state'];?></td>
                        </tr>
                        <tr>
                            <td>Pincode: </td>
                            <td><?php echo $query1[0]['pincode'];?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <!-- end md-6-->
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2 class="panel-title"><i class="fa fa-money fa-fw"></i>  Applicance Details</h2>
                </div>
                <div class="panel-body">
                    <?php for($i=0; $i<$query1[0]['quantity']; $i++) {?>
                    <table class="table table-bordered table-hover table-striped">
                        <tr>
                            <td >Brand: </td>
                            <td><?php echo $query2[$i]['appliance_brand'];?></td>
                        </tr>
                        <tr>
                            <td>Model: </td>
                            <td><?php echo $query2[$i]['model_number'];?></td>
                        </tr>
                        <tr>
                            <td>Serial No: </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Category: </td>
                            <td><?php echo $query2[$i]['appliance_category'];?></td>
                        </tr>
                        <tr>
                            <td>Capacity: </td>
                            <td><?php echo $query2[$i]['appliance_capacity'];?></td>
                        </tr>
                        <tr>
                            <td>Description: </td>
                            <td><?php echo $query1[0]['description'];?></td>
                        </tr>
                        <?php for($j = 0; $j < count($query2); $j++) {?>
                        <tr>
                            <td>Call Type: </td>
                            <td><?php echo $query2[$j]['price_tags'];?></td>
                        </tr>
                        <?php } ?>
                    </table>
                    <?php } ?>
                </div>
            </div>
        </div>
        <!-- end md-6 -->
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2 class="panel-title"><i class="fa fa-money fa-fw"></i>  Booking Details</h2>
                </div>
                <div class="panel-body">
                    <div class="col-md-6">
                        <table class="table table-bordered table-hover table-striped">
                            <tr>
                                <td>Booking ID: </td>
                                <td><?php echo $query1[0]['booking_id']; ?></td>
                            </tr>
                            <tr>
                                <td>Platform / Order ID: </td>
                                <td><?php echo $query1[0]['partner_source']." / ";  if(isset($query1[0]['order_id'])){ echo $query1[0]['order_id'];} else if(!empty($query4)){ echo $query4[0]['order_id']; } ?></td>
                            </tr>
                            <tr>
                                <td>Booking Date: </td>
                                <td><?php echo $query1[0]['booking_date']; ?></td>
                            </tr>
                            <tr>
                                <td>Booking Timeslot: </td>
                                <td><?php echo $query1[0]['booking_timeslot']; ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-bordered table-hover table-striped">
                            <tr>
                                <td>Booking Remarks: </td>
                                <td><?php echo $query1[0]['booking_remarks']; ?></td>
                            </tr>
                            <tr>
                                <td>Booking Current Status: </td>
                                <td><?php echo $query1[0]['current_status']; ?></td>
                            </tr>
                            <tr>
                                <td>Booking Internal Status: </td>
                                <td><?php echo $query1[0]['internal_status']; ?></td>
                            </tr>
                            <tr>
                                <td>Booking Closed Date: </td>
                                <td><?php echo $query1[0]['closed_date']; ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- end md-6 -->
    </div>
</div>