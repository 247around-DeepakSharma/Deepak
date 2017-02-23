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
                    <h3>Customer Details</h3>
                    <table class="table  table-striped table-bordered">
                        <tr>
                            <th >Name: </th>
                            <td><?php echo $booking_history[0]['name']; ?></td>
                            <th>Mobile: </th>
                            <td><?php echo $booking_history[0]['phone_number']; ?>
                                <button type="button" onclick="outbound_call(<?php echo $booking_history[0]['phone_number']; ?>)" class="btn btn-sm btn-info pull-right"><i class = 'fa fa-phone fa-lg' aria-hidden = 'true' class="pull-right"></i></button>
                            </td>
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
                <div class="col-md-12"><h3>Booking Details</h3>
<!--                    <a href="<?php echo base_url()?>employee/booking/get_booking_life_cycle/<?php echo $booking_history[0]['booking_id']?>" class="btn btn-info" style="margin-left:87%;margin-top:-5%;" target="_blank">View Booking History</a>-->
                </div>
                <div class="col-md-6">
                    <table class="table  table-striped table-bordered" >
                        <tr>
                            <th >Booking ID: </th>
                            <td><?php echo $booking_history[0]['booking_id']; ?></td>
                        </tr>
                        <tr>
                            <th >Order ID: </th>
                            <td><?php if(!empty($booking_history)){ echo $booking_history[0]['order_id']; } ?></td>
                        </tr>
                        <tr>
                            <th>Booking Type: </th>
                            <td><?php echo $booking_history[0]['type']; ?></td>
                        </tr>
                        <tr>
                            <th>Appliance: </th>
                            <td><?php echo $booking_history[0]['services']; ?></td>
                        </tr>
                        <tr>
                            <th>Source: </th>
                            <td><?php echo $booking_history[0]['source'] . ' / ' . $booking_history[0]['partner_source']; ?></td>
                        </tr>
                        <tr>
                            <th>Units: </th>
                            <td><?php echo $booking_history[0]['quantity']; ?></td>
                        </tr>
                        <tr>
                            <th>Booking Date: </th>
                            <td><?php echo $booking_history[0]['booking_date']; ?></td>
                        </tr>
                        <tr>
                            <th>Booking Timeslot: </th>
                            <td><?php echo $booking_history[0]['booking_timeslot']; ?></td>
                        </tr>
                        <tr>
                            <th>Booking Address: </th>
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
                            <th>Contact No: </th>
                            <td><?php echo $booking_history[0]['booking_primary_contact_no'] . ' / ' . $booking_history[0]['booking_alternate_contact_no']; ?></td>
                        </tr>
                        <tr>
                            <th>Booking Remarks: </th>
                            <td><?php echo $booking_history[0]['booking_remarks']; ?></td>
                        </tr>
                        <tr>
                            <th>Query Remarks: </th>
                            <td><?php echo $booking_history[0]['query_remarks']; ?></td>
                        </tr>
                        
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table  table-striped table-bordered">
                        
                        <tr>
                            <th>Booking Current Status: </th>
                            <td><?php echo $booking_history[0]['current_status']; ?></td>
                        </tr>
                        <tr>
                            <th>Booking Internal Status: </th>
                            <td><?php echo $booking_history[0]['internal_status']; ?></td>
                        </tr>
                         <tr>
                            <th>Booking Create Date: </th>
                            <td><?php echo $booking_history[0]['create_date']; ?></td>
                        </tr>
                        <tr>
                            <th>Booking Closed Date: </th>
                            <td><?php echo $booking_history[0]['closed_date']; ?></td>
                        </tr>
                        <tr>
                            <th>EDD: </th>
                            <td><?php echo $booking_history[0]['estimated_delivery_date']; ?></td>
                        </tr>
                        <tr>
                            <th>Delivered Date: </th>
                            <td><?php echo $booking_history[0]['delivery_date']; ?></td>
                        </tr>
                       
                         <tr><th>Amount Due: </th><td><?php echo $booking_history[0]['amount_due']; ?></td></tr>
                        <tr><th>Amount Paid: </th><td><?php echo $booking_history[0]['amount_paid']; ?></td></tr>
                        <tr>
                            <th>Rating Stars: </th>
                            <td><?php echo $booking_history[0]['rating_stars']; ?></td>
                        </tr>
                        <tr>
                            <th>Rating Comments: </th>
                            <td><?php echo $booking_history[0]['rating_comments']; ?></td>
                        </tr>
                        <tr>
                            <th>Closing Remarks: </th>
                            <td><?php echo $booking_history[0]['closing_remarks']; ?></td>
                        </tr>
                        <tr>
                            <th>Reschedule Reason: </th>
                            <td><?php echo $booking_history[0]['reschedule_reason']; ?></td>
                        </tr>
                        <tr>
                            <th>Cancellation Reason: </th>
                            <td><?php echo $booking_history[0]['cancellation_reason']; ?></td>
                        </tr>
                    </table>
                </div>

                <?php if(!empty($unit_details)) { ?>
                <div class="col-md-12" style="margin-top:20px;" >
                    <h3>Appliance Details</h3>
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
                            <th>247Around Offer</th>
                            <th>Upcountry Charges</th>
                            <th>Total Charges</th>
                            <?php } else { ?>
                            <th>Charges</th>
                            <th>Partner Offer</th>
                            <th>247Around Offer</th>
                            <th>Paid Service Charges</th>
                            <th>Paid Additional Charges</th>
                            <th>Paid Parts Cost</th>
                            <th>Upcountry Charges</th>
                            <th>Total Amount Paid</th>
                           
                            <?php } ?>
                            
                             <th>Booking Status</th>
                            
                        </tr>
                        <tbody>
                            <?php  foreach ( $unit_details as $key =>  $unit_detail) { ?>
                            
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
                                <td><?php if($booking_history[0]['upcountry_paid_by_customer'] == 0){ echo "0";} else { echo ($booking_history[0]['upcountry_distance'] * $booking_history[0]['partner_upcountry_rate']);} ?></td>
                                <td><?php if($booking_history[0]['upcountry_paid_by_customer'] == 0){ print_r($unit_detail['customer_net_payable']);
                                } else { print_r($unit_detail['customer_net_payable'] + ($booking_history[0]['upcountry_distance'] * DEFAULT_UPCOUNTRY_RATE));  } ?></td>
                                <?php } else {   ?>
                                <td><?php  print_r($unit_detail['price_tags']); ?></td>
                                 <td><?php  print_r($unit_detail['customer_total']); ?></td>
                                <td><?php print_r($unit_detail['partner_net_payable']);  ?></td>
                                <td><?php print_r($unit_detail['around_net_payable']);  ?></td>
                                <td><?php  print_r($unit_detail['customer_paid_basic_charges']); ?></td>
                                <td><?php print_r($unit_detail['customer_paid_extra_charges']);  ?></td>
                                <td><?php print_r($unit_detail['customer_paid_parts']);  ?></td>
                                <td><?php if($booking_history[0]['upcountry_paid_by_customer'] == 0){ echo "0";} else { echo $booking_history[0]['customer_paid_upcountry_charges'];} ?></td>
                                
                                <td><?php if($booking_history[0]['upcountry_paid_by_customer'] == 0){ 
                                   
                                     print_r($unit_detail['customer_paid_basic_charges'] 
                                            + $unit_detail['customer_paid_extra_charges'] 
                                            + $unit_detail['customer_paid_parts']);}
                                            else { 
                                               if($key == 0){
                                                print_r($unit_detail['customer_paid_basic_charges'] + $unit_detail['customer_paid_extra_charges'] + $unit_detail['customer_paid_parts'] 
                                                        + $booking_history[0]['customer_paid_upcountry_charges']);
                                               } else {
                                                   print_r($unit_detail['customer_paid_basic_charges'] 
                                            + $unit_detail['customer_paid_extra_charges'] 
                                            + $unit_detail['customer_paid_parts']);
                                               }
                                                
                                            }   ?></td>
                                
                                <?php }?>
                                <td><?php print_r($unit_detail['booking_status']); ?></td>
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
                                <td><?php if(!is_null($booking_history['spare_parts']['invoice_pic']) ) { if($booking_history['spare_parts']['invoice_pic'] != '0'){ ?> <a href="https://s3.amazonaws.com/bookings-collateral/misc-images/<?php echo $booking_history['spare_parts']['invoice_pic']; ?> " target="_blank">Click Here to view Invoice Image</a><?php } } ?></td>
                            </tr>
                            <tr>
                                <th >Serial Number Image: </th>
                                <td><?php if(!is_null($booking_history['spare_parts']['serial_number_pic'])) { if( $booking_history['spare_parts']['serial_number_pic'] !== '0'){ ?> <a href="https://s3.amazonaws.com/bookings-collateral/misc-images/<?php echo $booking_history['spare_parts']['serial_number_pic'];  ?> " target="_blank">Click Here to view Serial Number Image</a><?php } } ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table  table-striped table-bordered" >
                            <tr>
                                <th >Defective Part Image: </th>
                                <td><?php if(!is_null($booking_history['spare_parts']['defective_parts_pic']) ) { if($booking_history['spare_parts']['defective_parts_pic'] !== '0'){ ?> <a href="https://s3.amazonaws.com/bookings-collateral/misc-images/<?php echo $booking_history['spare_parts']['defective_parts_pic'];  ?> " target="_blank">Click Here to view Defective Part Image</a><?php } } ?></td>
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
                

                
                <?php if(!empty($service_center)){?>
                <div class="col-md-6">
                    <h3>Service Center Details:</h3>
                    <table class="table  table-striped table-bordered">
                        <tr>
                            <th>Service Center Name: </th>
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

            <div class="col-md-12" id="penalty_on_booking">
                <?php if (!empty($penalty)) { ?>
                    <h1 style='font-size:24px;'>Penalty History</h1>

                    <table  class="table table-striped table-bordered">
                        <tr>
                            <th class="jumbotron" style="text-align: center">Status</th>
                            <th class="jumbotron" style="text-align: center">Booking ID</th>
                            <th class="jumbotron" style="text-align: center">Current State</th>
                            <th class="jumbotron" style="text-align: center">Penalty Amount</th>
                            <th class="jumbotron" style="text-align: center">Remarks</th>
<!--                            <th class="jumbotron" style="text-align: center">Agent</th>-->
                            <th class="jumbotron" style="text-align: center">Date</th>
                        </tr>
                            <?php foreach ($penalty as $key => $value){?>
                            <tr>
                                <td><?php echo 'Penalty Added' ?></td>
                                <td><?php echo $penalty[$key]['booking_id']; ?></td>
                                <td><?php echo $penalty[$key]['current_state']; ?></td>
                                <td><?php echo $penalty[$key]['penalty_amount']; ?></td>
                                <td><?php echo $penalty[$key]['remarks']; ?></td>
                                <td><?php
                                    $old_date = $penalty[$key]['create_date'];
                                    $old_date_timestamp = strtotime($old_date);
                                    $new_date = date('j F, Y g:i A', $old_date_timestamp);
                                    echo $new_date;
                                    ?>
                                </td>
                            </tr>
                            <?php if($penalty[$key]['active'] == 0){?>
                            <tr>
                                <td><?php echo 'Penalty Removed' ?></td>
                                <td><?php echo $penalty[$key]['booking_id']; ?></td>
                                <td><?php echo $penalty[$key]['current_state']; ?></td>
                                <td><?php echo $penalty[$key]['penalty_amount']; ?></td>
                                <td><?php echo $penalty[$key]['penalty_remove_reason']; ?></td>
                                <td><?php
                                    $old_date = $penalty[$key]['penalty_remove_date'];
                                    $old_date_timestamp = strtotime($old_date);
                                    $new_date = date('j F, Y g:i A', $old_date_timestamp);
                                    echo $new_date;
                                    ?>
                                </td>
                            </tr>
                            <?php }?>
                            <?php }?>
                    </table>


                <?php } ?>
            </div>
            <div class="col-md-12"id="booking_history" style="margin-top:20px;"></div>
        </div>
    </div>
</div>

<script type="text/javascript">

    function outbound_call(phone_number){
        var confirm_call = confirm("Call Customer ?");

        if (confirm_call == true) {

             $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/booking/call_customer/' + phone_number,
                success: function(response) {
                    //console.log(response);

                }
            });
        } else {
            return false;
        }

    }
    
    $('document').ready(function(){
        var booking_id = '<?php echo base_url()?>employee/booking/get_booking_life_cycle/<?php echo $booking_history[0]['booking_id']?>';
            $.ajax({
                type: 'POST',
                url: booking_id,
                success: function(response) {
                    $('#booking_history').html(response);
                }
            });
    });

</script>