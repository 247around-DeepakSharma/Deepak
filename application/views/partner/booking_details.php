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
                <div class="col-md-12">
                    <h1 style="font-size:24px;" >Booking Details</h1>
<!--                    <a href="<?php echo base_url()?>partner/get_booking_life_cycle/<?php echo $booking_history[0]['booking_id']?>" class="btn btn-info" style="margin-left:87%;margin-top:-5%;" target="_blank">View Booking History</a>-->
                </div>
                <div class="col-md-6">
                    <table class="table  table-striped table-bordered" >
                        <tr>
                            <th >Name: </th>
                            <td><?php echo $booking_history[0]['name']; ?></td>
                        </tr>
                        <tr>
                            <th>Mobile: </th>
                            <td><?php echo $booking_history[0]['booking_primary_contact_no']; 
                                if(!empty( $booking_history[0]['booking_alternate_contact_no'])){ echo "/". $booking_history[0]['booking_alternate_contact_no'];} ?>
                            </td>
                        </tr>
                        <tr>
                            <th >Booking ID: </th>
                            <td><?php echo $booking_history[0]['booking_id']; ?></td>
                        </tr>
                        <tr>
                            <th>Platform / Order ID: </th>
                            <td><?php  echo $booking_history[0]['partner_source']." / "; if(!empty($booking_history[0]['order_id'])) { echo $booking_history[0]['order_id']; }  ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Serial Number: </th>
                            <td><?php if(isset($unit_details[0]['partner_serial_number'])) { echo $unit_details[0]['partner_serial_number'];} ?></td>
                        </tr>
                        <tr>
                            <th>Call Type: </th>
                            <td><?php echo $booking_history[0]['request_type']; ?></td>
                        </tr>
                        <tr>
                            <th>Booking date: </th>
                            <td><?php echo $booking_history[0]['booking_date']; ?></td>
                        </tr>
                        <tr>
                            <th>Address: </th>
                            <td><?php echo $booking_history[0]['booking_address'];?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table  table-striped table-bordered">
                        <tr>
                            <th>City: </th>
                            <td><?php echo $booking_history[0]['city']; ?></td>
                        </tr>
                        <tr>
                            <th>State: </th>
                            <td><?php echo $booking_history[0]['state']; ?></td>
                        </tr>
                        <tr>
                            <th>Pincode: </th>
                            <td><?php echo $booking_history[0]['booking_pincode']; ?></td>
                        </tr>
                        <tr>
                            <th>Remarks: </th>
                            <td><?php echo $booking_history[0]['booking_remarks']; ?></td>
                        </tr>
                        <tr>
                            <th>Status: </th>
                            <td><?php echo $booking_history[0]['current_status']; ?></td>
                        </tr>
                        <tr>
                            <th>Cancellation Reason: </th>
                            <td><?php echo $booking_history[0]['cancellation_reason']; ?></td>
                        </tr>
                        <tr>
                            <th>Booking closed date: </th>
                            <td><?php echo $booking_history[0]['closed_date']; ?></td>
                        </tr>
                    </table>
                </div>
                <?php if(!empty($unit_details)) { ?>
                <div class="col-md-12" style="margin-top:20px;" >
                    <h1 style='font-size:24px;'>Appliance Details</h1>
                    <table class="table  table-striped table-bordered">
                        <tr>
                            <th>Brand</th>
                            <th>Category</th>
                            <th>Capacity</th>
                            <th>Model Number</th>
                            <th>SF Serial Number</th>
                            <th>Purchase Date</th>
                            <th>Description</th>
                            <th>Call Type</th>
                            <?php if($booking_history[0]['current_status'] != "Completed"){ ?>
                            <th>Charges</th>
                            <th>Partner Offer</th>
                            <th>247Around Offer</th>
                            <th>Total Charges</th>
                            <?php } else { ?>
                            <th>Partner Offer</th>
                            <th>247Around Offer</th>
                            <th>Paid Service Charges</th>
                            <th>Paid Additional Charges</th>
                            <th>Paid Parts Cost</th>
                            <th>Total Amount Paid</th>
                            <?php } ?>
                            <th>Booking Status</th>
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
                                <?php }?>
                                <td><?php print_r($unit_detail['booking_status']);  ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <?php } ?>
                <?php if(isset($booking_history['spare_parts'])){ ?>
                <div class="col-md-12">
                    <h1 style='font-size:24px;'>Spare Parts Requested By SF</h1>
                    <div class="col-md-6" style="padding-left:1px;">
                        <table class="table  table-striped table-bordered" >
                            <tr>
                                <th >Model Number: </th>
                                <td><?php echo $booking_history['spare_parts']['model_number']; ?></td>
                            </tr>
                            <tr>
                                <th >Requested Parts: </th>
                                <td><?php echo $booking_history['spare_parts']['parts_requested']; ?></td>
                            </tr>
                            <tr>
                                <th >Requested Date: </th>
                                <td><?php echo $booking_history['spare_parts']['create_date']; ?></td>
                            </tr>
                            <tr>
                                <th >Invoice Image: </th>
                                <td><?php if(!is_null($booking_history['spare_parts']['invoice_pic'])) { if($booking_history['spare_parts']['invoice_pic'] != '0'){  ?> <a href="https://s3.amazonaws.com/bookings-collateral/misc-images/<?php echo $booking_history['spare_parts']['invoice_pic']; ?> " target="_blank">Click Here to view Invoice Image</a><?php } } ?></td>
                            </tr>
                            <tr>
                                <th >Serial Number Image: </th>
                                <td><?php if(!is_null($booking_history['spare_parts']['serial_number_pic'])) { if($booking_history['spare_parts']['serial_number_pic'] != '0'){  ?> <a href="https://s3.amazonaws.com/bookings-collateral/misc-images/<?php echo $booking_history['spare_parts']['serial_number_pic'];  ?> " target="_blank">Click Here to view Serial Number Image</a><?php } } ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table  table-striped table-bordered" >
                            <tr>
                                <th >Defective Part Image: </th>
                                <td><?php if(!is_null($booking_history['spare_parts']['defective_parts_pic'])) {  if($booking_history['spare_parts']['defective_parts_pic'] != '0'){   ?> <a href="https://s3.amazonaws.com/bookings-collateral/misc-images/<?php echo $booking_history['spare_parts']['defective_parts_pic'];  ?> " target="_blank">Click Here to view Defective Part Image</a><?php } } ?></td>
                            </tr>
                            <tr>
                                <th >Shipment Address: </th>
                                <td><?php echo $booking_history[0]['address']; ?></td>
                            </tr>
                            <tr>
                                <th >Serial Number: </th>
                                <td><?php echo $booking_history['spare_parts']['serial_number']; ?></td>
                            </tr>
                             <tr>
                                <th >Acknowledge Date BY SF: </th>
                                <td><?php echo $booking_history['spare_parts']['acknowledge_date']; ?></td>
                            </tr>
                             <tr>
                                <th >Remarks By SC: </th>
                                <td><?php echo $booking_history['spare_parts']['remarks_by_sc']; ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <?php if(!is_null($booking_history['spare_parts']['parts_shipped'])){ ?>
                <div class="col-md-12">
                    <h1 style='font-size:24px;'>Spare Parts Shipped By <?php echo $this->session->userdata('partner_name');?></h1>
                    <div class="col-md-6" style="padding-left:1px;">
                        <table class="table  table-striped table-bordered" >
                            <tr>
                                <th >Shipped Parts: </th>
                                <td><?php echo $booking_history['spare_parts']['parts_shipped']; ?></td>
                            </tr>
                            <tr>
                                <th >Courier Name: </th>
                                <td><?php echo $booking_history['spare_parts']['courier_name_by_partner']; ?></td>
                            </tr>
                            <tr>
                                <th >AWB: </th>
                                <td><?php echo $booking_history['spare_parts']['awb_by_partner']; ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table  table-striped table-bordered" >
                            <tr>
                                <th >Shipped date: </th>
                                <td><?php echo $booking_history['spare_parts']['shipped_date']; ?></td>
                            </tr>
                            <tr>
                                <th >EDD: </th>
                                <td><?php echo $booking_history['spare_parts']['edd']; ?></td>
                            </tr>
                            <tr>
                                <th >Remarks By Partner: </th>
                                <td><?php echo $booking_history['spare_parts']['remarks_by_partner']; ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <?php } if($booking_history['spare_parts']['approved_defective_parts_by_partner'] == "1"){ ?>
                <div class="col-md-12">
                    <h1 style='font-size:24px;'>Defective Spare Parts Shipped By SF</h1>
                    <div class="col-md-6" style="padding-left:1px;">
                        <table class="table  table-striped table-bordered" >
                            <tr>
                                <th >Shipped Parts: </th>
                                <td><?php echo $booking_history['spare_parts']['defective_part_shipped']; ?></td>
                            </tr>
                            <tr>
                                <th >Courier Name: </th>
                                <td><?php echo $booking_history['spare_parts']['courier_name_by_sf']; ?></td>
                            </tr>
                            <tr>
                                <th >AWB: </th>
                                <td><?php echo $booking_history['spare_parts']['awb_by_sf']; ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table  table-striped table-bordered" >
                            <tr>
                                <th >Shipped date: </th>
                                <td><?php echo date('Y-m-d', strtotime($booking_history['spare_parts']['defective_part_shipped_date'])); ?></td>
                            </tr>
                           
                            <tr>
                                <th >Remarks By SF: </th>
                                <td><?php echo $booking_history['spare_parts']['remarks_defective_part_by_sf']; ?></td>
                            </tr>
                             <tr>
                                <th >Rejected Remarks By Partner: </th>
                                <td><?php echo $booking_history['spare_parts']['remarks_defective_part_by_partner']; ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <?php } } ?>
            </div>
            <div class="col-md-12"id="booking_history"></div>
        </div>
    </div>
    <div style="margin-top: 40px;"></div>
</div>
<style type="text/css">
    .spare_image {
    width: 350px;;
    height: 300px;
    background: url('<?php echo base_url()?>images/loader.gif') 50% no-repeat;
    border: 1px solid black;
    border-radius: 5px;
    }
    
</style>
<script>
        $('document').ready(function(){
        var booking_id = '<?php echo base_url()?>partner/get_booking_life_cycle/<?php echo $booking_history[0]['booking_id']?>';
            $.ajax({
                type: 'POST',
                url: booking_id,
                success: function(response) {
                    $('#booking_history').html(response);
                }
            });
    });
</script>