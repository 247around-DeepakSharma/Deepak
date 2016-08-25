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
                            <th>Alternate Number: </th>
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
                            <th >Booking Id: </th>
                            <td><?php echo $booking_history[0]['booking_id']; ?></td>
                        </tr>
                        <tr>
                            <th >Order Id: </th>
                            <td><?php if(!empty($booking_history)){ echo $booking_history[0]['order_id']; } ?></td>
                        </tr>
                        <tr>
                            <th>Booking Type: </th>
                            <td><?php echo $booking_history[0]['type']; ?></td>
                        </tr>
                        <tr>
                            <th>Service name: </th>
                            <td><?php echo $booking_history[0]['services']; ?></td>
                        </tr>
                        <tr>
                            <th>Source: </th>
                            <td><?php echo $booking_history[0]['source']; ?></td>
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
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table  table-striped table-bordered">
                        <tr>
                            <th>Booking Alternate Contact No.: </th>
                            <td><?php echo $booking_history[0]['booking_alternate_contact_no']; ?></td>
                        </tr>
                        <tr>
                            <th>Booking Remarks: </th>
                            <td><?php echo $booking_history[0]['booking_remarks']; ?></td>
                        </tr>
                        <tr>
                            <th>Query Remarks: </th>
                            <td><?php echo $booking_history[0]['query_remarks']; ?></td>
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
                        <tr>
                            <th>Potential Value: </th>
                            <td><?php echo $booking_history[0]['potential_value']; ?></td>
                        </tr>
                        <!-- <tr><td>Amount Due: </td><td><?php echo $booking_history[0]['amount_due']; ?></td></tr>
                            <tr><td>Amount Paid: </td><td><?php echo $booking_history[0]['amount_paid']; ?></td></tr>-->
                        <tr>
                            <th>Rating star: </th>
                            <td><?php echo $booking_history[0]['rating_stars']; ?></td>
                        </tr>
                        <tr>
                            <th>Rating comments: </th>
                            <td><?php echo $booking_history[0]['rating_comments']; ?></td>
                        </tr>
                        <tr>
                            <th>Vendor Rating star: </th>
                            <td><?php echo $booking_history[0]['vendor_rating_stars']; ?></td>
                        </tr>
                        <tr>
                            <th>Vendor Rating comments: </th>
                            <td><?php echo $booking_history[0]['vendor_rating_comments']; ?></td>
                        </tr>
                        <tr>
                            <th>Closing Remark: </th>
                            <td><?php echo $booking_history[0]['closing_remarks']; ?></td>
                        </tr>
                        <tr>
                            <th>Cancellation Reason: </th>
                            <td><?php echo $booking_history[0]['cancellation_reason']; ?></td>
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
                            <th>Serial Number</th>
                            <th>Purchase Date</th>
                            <th>Description</th>
                            <th>Service Category</th>
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
                                <td><?php echo $unit_detail['serial_number']?></td>
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
                                <td><?php print_r($unit_detail['booking_status']); ?></td>
                                <?php }?>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <?php } if(!empty($service_center)){?>
                <div class="col-md-6">
                    <b >Service Centre Details:</b><br>
                    <table class="table  table-striped table-bordered">
                        <tr>
                            <th>Service Centre Name: </th>
                            <td><?php if(isset($service_center[0]['service_centre_name'])){echo $service_center[0]['service_centre_name'];}?>
                            </td>
                        </tr>
                        <tr>
                            <th>PoC Name: </th>
                            <td><?php if(isset($service_center[0]['primary_contact_name'])){echo $service_center[0]['primary_contact_name'];}?>
                            </td>
                        </tr>
                        <tr>
                            <th>PoC Number: </th>
                            <td><?php if(isset($service_center[0]['primary_contact_phone_1'])){echo $service_center[0]['primary_contact_phone_1'];}?>
                            </td>
                        </tr>
                    </table>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>