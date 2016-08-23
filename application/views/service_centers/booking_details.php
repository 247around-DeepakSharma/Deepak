<style type="text/css">
    th,td{
    border: 1px #f2f2f2 solid;
    vertical-align: center;
    padding: 1px;
    }
    tr:nth-child(even) {background-color: #f2f2f2}
</style>
<div id="page-wrapper">
    <div class="">
        <div class="row">
            <div>
                <h2 >Details:</h2>
                <div class="col-md-12">
                    <b >Customer Details:-</b><br>
                    <table class="table  table-striped table-bordered">
                        <tr>
                            <th >Name: </th>
                            <td><?php echo $booking_history[0]['name']; ?></td>
                            <th>Mobile: </th>
                            <td><?php echo $booking_history[0]['phone_number']; ?></td>
                            <th>Alternate No: </th>
                            <td><?php echo $booking_history[0]['alternate_phone_number']; ?></td>
                        </tr>
                        <tr>
                            <th>Email ID: </th>
                            <td><?php echo $booking_history[0]['user_email']; ?></td>
                            <th>Address: </th>
                            <td><?php echo $booking_history[0]['home_address'];?></td>
                            <th>City:</th>
                            <td><?php echo $booking_history[0]['city'];  ?></td>
                        </tr>
                        <tr>
                            <th>Landmark: </th>
                            <td><?php echo $booking_history[0]['booking_landmark']; ?></td>
                            <th>State: </th>
                            <td><?php echo $booking_history[0]['state'];?></td>
                            <th>Pincode:</th>
                            <td><?php echo $booking_history[0]['pincode']  ?></td>
                        </tr>
                    </table>
                    <br>
                </div>
                <div class="col-md-12"><b >Booking Details:-</b></div>
                <div class="col-md-6">
                    <table class="table  table-striped table-bordered" >
                        <tr>
                            <th >Booking ID: </th>
                            <td><?php echo $booking_history[0]['booking_id']; ?></td>
                        </tr>
                        <tr>
                            <th >Source / Order ID: </th>
                            <td><?php  echo $booking_history[0]['partner_source']." / "; if(!empty($booking_history[0]['order_id'])) { echo $booking_history[0]['order_id']; }  ?>

                          </td>
                        </tr>
                        <tr>
                            <th>Booking Type: </th>
                            <td><?php echo $booking_history[0]['type']; ?></td>
                        </tr>
                       
                      
                        <tr>
                            <th>Number of appliances: </th>
                            <td><?php echo $booking_history[0]['quantity']; ?></td>
                        </tr>
                        <tr>
                            <th>Booking date: </th>
                            <td><?php echo $booking_history[0]['booking_date']; ?></td>
                        </tr>
                        <tr>
                            <th>Booking timeslot: </th>
                            <td><?php echo $booking_history[0]['booking_timeslot']; ?></td>
                        </tr>
                        <tr>
                            <th>Booking address: </th>
                            <td><?php echo $booking_history[0]['booking_address'];?></td>
                        </tr>
                         <tr>
                            <th>Booking City: </th>
                            <td><?php echo $booking_history[0]['city']; ?></td>
                        </tr>
                       
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table  table-striped table-bordered">

                        <tr>
                            <th>Booking State: </th>
                            <td><?php echo $booking_history[0]['state']; ?></td>
                        </tr>
                        <tr>
                            <th>Booking Pincode: </th>
                            <td><?php echo $booking_history[0]['booking_pincode']; ?></td>
                        </tr>
                        <tr>
                            <th>Booking Primary Contact No.: </th>
                            <td><?php echo $booking_history[0]['booking_primary_contact_no']; ?></td>
                        </tr>
                        <tr>
                            <th>Booking Alternate Contact No.: </th>
                            <td><?php echo $booking_history[0]['booking_alternate_contact_no']; ?></td>
                        </tr>
                        <tr>
                            <th>Booking Remarks: </th>
                            <td><?php echo $booking_history[0]['booking_remarks']; ?></td>
                        </tr>
                        
                        <tr>
                            <th>Booking current status: </th>
                            <td><?php echo $booking_history[0]['current_status']; ?></td>
                        </tr>
                        <tr>
                            <th>Booking internal status: </th>
                            <td><?php echo $booking_history[0]['internal_status']; ?></td>
                        </tr>
                        <tr>
                            <th>Booking closed date: </th>
                            <td><?php echo $booking_history[0]['closed_date']; ?></td>
                        </tr>
                       
                       
                        
                       
                    </table>
                </div>
                <?php if(!empty($unit_details)) { ?>
                <div class="col-md-12" style="margin-top:20px;" >
                    <b >Appliance Details:-</b><br>
                    <table class="table  table-striped table-bordered">
                        <tr>
                            <th>Brand</th>
                            <th>Category</th>
                            <th>Capacity</th>
                            <th>Model Number</th>
                            <th>Purchase Date</th>
                            <th>Description</th>
                            <th>Call Type</th>
                            <?php if($booking_history[0]['current_status'] != "Completed"){ ?>
                            <th>Charges</th>
                            <th>Partner Offer</th>
                            <th>Discount</th>
                            <th>Total Charges</th>
                            <?php } else { ?>
                            <th>Partner Offer</th>
                            <th>Discount</th>
                            <th>Paid Service Charges</th>
                            <th>Paid Additional Charges</th>
                            <th>Paid Parts Cost</th>
                            <th>Total Amount Paid</th>
                            <th>Booking Status</th>
                            <?php } ?>
                        </tr>
                        <tbody>
                           <?php  foreach ( $unit_details as  $unit_detail) { ?>
                            
                            <tr>
                                <td><?php echo $unit_detail['appliance_brand']?></td>
                                <td><?php echo $unit_detail['appliance_category']?></td>
                                <td><?php echo $unit_detail['appliance_capacity']?></td>
                                <td><?php echo $unit_detail['model_number']?></td>
                                <td><?php if(!empty($unit_detail['purchase_month'])) {echo $unit_detail['purchase_month']."-". $unit_detail['purchase_year'];} else { echo $unit_detail['purchase_year'];}?></td>
                                <td><?php echo $unit_detail['appliance_description']?></td>
                                <?php if($booking_history[0]['current_status'] != "Completed"){ ?>
                                <td><?php  print_r($unit_detail['price_tags']); ?></td>
                                <td><?php  print_r($unit_detail['customer_total']); ?></td>
                                <td><?php print_r($unit_detail['partner_net_payable']);  ?></td>
                                <td><?php print_r($unit_detail['around_net_payable']);  ?></td>
                                <td><?php print_r($unit_detail['customer_net_payable']);  ?></td>
                                <?php } else {   ?>
                                <td><?php  print_r($unit_detail['price_tags']); ?></td>
                                <td><?php print_r($unit_detail['partner_net_payable']);  ?></td>
                                <td><?php print_r($unit_detail['around_net_payable']);  ?></td>
                                <td><?php  print_r($unit_detail['customer_paid_basic_charges']); ?></td>
                                <td><?php print_r($unit_detail['customer_paid_extra_charges']);  ?></td>
                                <td><?php print_r($unit_detail['customer_paid_parts']);  ?></td>
                                <td><?php print_r($unit_detail['customer_paid_basic_charges'] + $unit_detail['customer_paid_extra_charges'] + $unit_detail['customer_paid_parts'] );  ?></td>
                                <td><?php print_r($unit_detail['booking_status']);  ?></td>
                                <?php }?>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div style="margin-top: 40px;"></div>
</div>