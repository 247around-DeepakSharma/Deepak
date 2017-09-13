<style type="text/css">
    #invoiceDetailsModal .modal-lg {
        width: 100%!important;
    }
    th,td{
    border: 1px #f2f2f2 solid;
    vertical-align: center;
    padding: 1px;
    }
    tr:nth-child(even) {background-color: #f2f2f2}
</style>
<!--Invoice Details Modal-->
<div id="invoiceDetailsModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-body">
              <div id="open_model"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          </div>
        </div>
  </div>
</div>
<!-- end Invoice Details Modal -->
<div id="page-wrapper">
    <div class="">
        <?php if(!empty($booking_history)) { ?>
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
                                <td><?php echo $booking_history[0]['alternate_phone_number']; ?>
                                   <?php if(!empty($booking_history[0]['alternate_phone_number']))
                                   {?>
                                <button type="button" onclick="outbound_call(<?php echo $booking_history[0]['alternate_phone_number']; ?>)" class="btn btn-sm btn-info pull-right"><i class = 'fa fa-phone fa-lg' aria-hidden = 'true' class="pull-right"></i></button>
                                   <?php } ?>
                                
                                </td>
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
                                <td><?php if(!empty($booking_history)){  echo $booking_history[0]['order_id'];
                                                    $src = base_url() . 'images/no_image.png';
                                                    $image_src = $src;
                                                    if (isset($booking_history[0]['support_file']) && !empty($booking_history[0]['support_file'])) {
                                                        //Path to be changed
                                                        $src = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/misc-images/".$booking_history[0]['support_file'];
                                                        $image_src = base_url().'images/view_image.png';
                                                    }
                                                    ?>
                                <a href="<?php  echo $src?>" target="_blank"><img src="<?php  echo $image_src ?>" width="35px" height="35px" style="border:1px solid black;margin-left:10px;" /></a>
                                <?php } ?> </td>
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
                                <td><?php echo $booking_history[0]['public_name'] . ' / ' . $booking_history[0]['partner_source']; ?></td>
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

                                <th>Partner Offer Upcountry Charges</th>
                                <th>Total Charges</th>
                                <?php } else { ?>
                                <th>Charges</th>
                                <th>Partner Offer</th>
                                <th>247Around Offer</th>
                                <th>Upcountry Charges</th>
                                <th>Partner Offer Upcountry Charges</th>
                                <th>Paid Service Charges</th>
                                <th>Paid Additional Charges</th>
                                <th>Paid Parts Cost</th>
                                <th>Paid Upcountry Charges</th>

                                <th>Total Amount Paid</th>

                                <?php } ?>

                                 <th>Booking Status</th>
                                 <?php if($booking_history[0]['current_status'] === 'Completed'){ ?>
                                 <th>Vendor Cash Invoice ID</th>
                                 <th>Vendor Foc Invoice ID</th>
                                 <th>Partner Invoice ID</th>
                                 <?php } ?>

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

                                    <!--Upcountry Charges-->
                                    <td><?php if($key == 0){ if($booking_history[0]['is_upcountry'] == 1){ echo round($booking_history[0]['upcountry_distance'] * $booking_history[0]['partner_upcountry_rate'],0); } } ?></td>
                                    <!--Partner Offer Upcountry Charges-->
                                    <td><?php if($key == 0){ if($booking_history[0]['upcountry_paid_by_customer'] == 0){ echo round($booking_history[0]['upcountry_distance'] * $booking_history[0]['partner_upcountry_rate'],0); } else { echo "0.00";} } ?></td>
                                    <!--Total Charges-->
                                    <td><?php if($booking_history[0]['upcountry_paid_by_customer'] == 0){ print_r($unit_detail['customer_net_payable']);
                                    } else if($key == 0) { print_r($unit_detail['customer_net_payable'] + ($booking_history[0]['upcountry_distance'] * DEFAULT_UPCOUNTRY_RATE));  } else { print_r($unit_detail['customer_net_payable']); } ?></td>
                                    <?php } else {   ?>

                                    <td><?php  print_r($unit_detail['price_tags']); ?></td>
                                     <td><?php  print_r($unit_detail['customer_total']); ?></td>
                                    <td><?php print_r($unit_detail['partner_net_payable']);  ?></td>
                                    <td><?php print_r($unit_detail['around_net_payable']);  ?></td>
                                    <td><?php if($key == 0){ if($booking_history[0]['is_upcountry'] == 1){ echo round($booking_history[0]['upcountry_distance'] * $booking_history[0]['partner_upcountry_rate'],0); } } ?></td>
                                    <!--Partner Offer Upcountry Charges-->
                                    <td><?php if($key == 0){ if($booking_history[0]['upcountry_paid_by_customer'] == 0){ echo round($booking_history[0]['upcountry_distance'] * $booking_history[0]['partner_upcountry_rate'],0); } else { echo "0.00";} } ?></td>
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
                                    <?php if($booking_history[0]['current_status'] === 'Completed'){ ?>
                                    <td><a class="get_cash_invoice_id_data" href="javascript:void(0)" data-id="<?php echo $unit_detail['vendor_cash_invoice_id']; ?>"><?php print_r($unit_detail['vendor_cash_invoice_id']); ?></a></td>
                                    <td><a class="get_foc_invoice_id_data" href="javascript:void(0)" data-id="<?php echo $unit_detail['vendor_foc_invoice_id']; ?>"><?php print_r($unit_detail['vendor_foc_invoice_id']); ?></a></td>
                                    <td><a class="get_partner_invoice_id_data" href="javascript:void(0)" data-id="<?php echo $unit_detail['partner_invoice_id']; ?>"><?php print_r($unit_detail['partner_invoice_id']); ?></a></td>
                                    <?php } ?>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <?php } ?>


                    <?php if(isset($booking_history['spare_parts'])){ $parts_shipped = false; $defective_parts_shipped = FALSE; ?>

                    <div class="col-md-12">

                        <h1 style='font-size:24px;'>Spare Parts Requested By SF</h1>
                        <div class="col-md-12" style="padding-left:1px;">
                            <table class="table  table-striped table-bordered" >
                                <thead>
                                    <tr>
                                    <th >Model Number </th>
                                    <th >Requested Parts </th>
                                    <th >Requested Date </th>
                                    <th >Invoice Image </th>
                                    <th >Serial Number Image </th>
                                    <th >Defective Part Image </th>
                                    <th >Serial Number</th>
                                    <th >Acknowledge Date BY SF </th>
                                    <th >Remarks By SC </th>
                                    <th >Current Status </th>
                                </tr>
                                </thead>

                                <tbody>
                                     <?php foreach ($booking_history['spare_parts'] as $sp){  ?>
                                <tr>
                                    <td><?php echo $sp['model_number']; ?></td>
                                    <td><?php echo $sp['parts_requested']; ?></td>
                                    <td><?php echo $sp['create_date']; ?></td>
                                    <td><?php if(!is_null($sp['invoice_pic']) ) { if($sp['invoice_pic'] != '0'){ ?> <a href="https://s3.amazonaws.com/bookings-collateral/misc-images/<?php echo $sp['invoice_pic']; ?> " target="_blank">Click Here to view Invoice Image</a><?php } } ?></td>
                                    <td><?php if(!is_null($sp['serial_number_pic'])) { if( $sp['serial_number_pic'] !== '0'){ ?> <a href="https://s3.amazonaws.com/bookings-collateral/misc-images/<?php echo $sp['serial_number_pic'];  ?> " target="_blank">Click Here to view Serial Number Image</a><?php } } ?></td>
                                    <td><?php if(!is_null($sp['defective_parts_pic']) ) { if($sp['defective_parts_pic'] !== '0'){ ?> <a href="https://s3.amazonaws.com/bookings-collateral/misc-images/<?php echo $sp['defective_parts_pic'];  ?> " target="_blank">Click Here to view Defective Part Image</a><?php } } ?></td>
                                    <td><?php echo $sp['serial_number']; ?></td>
                                    <td><?php echo $sp['acknowledge_date']; ?></td>
                                    <td><?php echo $sp['remarks_by_sc']; ?></td>
                                    <td><?php echo $sp['status']; ?></td>
                                </tr>
                                     <?php  if(!is_null($sp['parts_shipped'])){ $parts_shipped = true;} if(!empty($sp['defective_part_shipped'])){
                                         $defective_parts_shipped = TRUE;
                                     } } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>


                     <?php if($parts_shipped){ ?>
                    <div class="col-md-12">
                        <h1 style='font-size:24px;'>Spare Parts Shipped By Partner</h1>
                        <div class="col-md-12" style="padding-left:1px;">
                            <table class="table  table-striped table-bordered" >
                                <thead>
                                    <tr>
                                        <th >Shipped Parts </th>
                                        <th >Courier Name </th>
                                        <th >AWB </th>
                                        <th >Shipped date </th>
                                        <th >EDD </th>
                                        <th >Remarks By Partner </th>
                                        
                                    </tr>

                                </thead>
                                <tbody>
                                    <?php foreach ($booking_history['spare_parts'] as $sp){ if(!is_null($sp['parts_shipped'])) { ?>
                                    <tr>
                                        <td><?php echo $sp['parts_shipped']; ?></td>
                                        <td><?php echo $sp['courier_name_by_partner']; ?></td>
                                        <td><?php echo $sp['awb_by_partner']; ?></td>
                                        <td><?php echo $sp['shipped_date']; ?></td>
                                        <td><?php echo $sp['edd']; ?></td>
                                        <td><?php echo $sp['remarks_by_partner']; ?></td>
                                        

                                    </tr>
                     <?php } }  ?>
                                </tbody>

                            </table>
                        </div>

                    </div>
                    <?php } ?> 

                    <?php if($defective_parts_shipped){ ?>
                    <div class="col-md-12">
                        <h1 style='font-size:24px;'>Defective Spare Parts Shipped By SF</h1>
                        <div class="col-md-12" style="padding-left:1px;">
                            <table class="table  table-striped table-bordered" >
                                <thead>
                                    <tr>
                                     <th >Shipped Parts </th>
                                     <th >Courier Name:</th>
                                     <th >AWB </th>
                                     <th >Courier Charge </th>
                                     <th >Shipped date </th>
                                     <th >Remarks By SF </th>
                                     <th >Remarks By Partner</th>
                                     <th>Courier Invoice</th>
                                    </tr>
                                </thead>
                                <tbody>
                                     <?php foreach ($booking_history['spare_parts'] as $sp) { ?>
                                    <tr>
                                        <td><?php echo $sp['defective_part_shipped']; ?></td>
                                        <td><?php echo $sp['courier_name_by_sf']; ?></td>
                                        <td><?php echo $sp['awb_by_sf']; ?></td>
                                        <td><?php echo $sp['courier_charges_by_sf']; ?></td>
                                        <td><?php echo date('Y-m-d', strtotime($sp['defective_part_shipped_date'])); ?></td>
                                        <td><?php echo $sp['remarks_defective_part_by_sf']; ?></td>
                                        <td><?php echo $sp['remarks_defective_part_by_partner']; ?></td>
                                         <td><a href="https://s3.amazonaws.com/bookings-collateral/misc-images/<?php echo $sp['defective_courier_receipt']; ?> " target="_blank">Click Here to view</a></td>
                                    </tr>
                    <?php  } ?>
                                </tbody>

                            </table>
                        </div>

                    </div>
                    <?php } }   ?>



                    <?php if(!empty($booking_history[0]['vendor_name'])){?>
                    <div class="col-md-12">
                        <h3>Service Center Details:</h3>
                        <table class="table  table-striped table-bordered">
                            <thead>
                            <tr>
                                 <th>Service Center Name </th>
                                 <th>PoC Name </th>
                                 <th>PoC Number </th>
                                 <th>Upcountry District </th>
                                 <th>Upcountry Pincode </th>
                            </tr>
                            </thead
                            <tbody>
                            <tr>
                                <td><?php if(isset($booking_history[0]['vendor_name'])){ ?><a href="<?php echo base_url();?>employee/vendor/viewvendor/<?php echo $booking_history[0]['assigned_vendor_id']?>" target="_blank"><?php echo $booking_history[0]['vendor_name']?></a> <?php }?></td>
                                <td><?php if(isset($booking_history[0]['primary_contact_name'])){echo $booking_history[0]['primary_contact_name'];}?></td>
                                <td><?php if(isset($booking_history[0]['primary_contact_phone_1'])){echo $booking_history[0]['primary_contact_phone_1'];?>
                                    <button type="button" onclick="outbound_call(<?php echo $booking_history[0]['primary_contact_phone_1'] ?>)" class="btn btn-sm btn-info pull-right"><i class="fa fa-phone fa-lg" aria-hidden="true"></i></button>
                                           <?php }?>
                                </td>
                                <td> <?php if(!is_null($booking_history[0]['sub_vendor_id'])){ ?><?php if(isset($dhq[0]['district'])){echo $dhq[0]['district'];}?><?php } ?></td>
                                <td><?php if(isset($dhq[0]['pincode'])){ echo $dhq[0]['pincode'];} ?></td>
                            </tr>
                            </tbody>

                    <?php } ?>


                        </table>
                    </div>
                </div>


                <div class="col-md-12" id="booking_history" style="margin-top:20px;"></div>

                <div class="col-md-12" id="penalty_on_booking">
                    <?php if (!empty($penalty)) { ?>
                        <h1 style='font-size:24px;'>Penalty History</h1>

                        <table  class="table table-striped table-bordered">
                            <tr>
                                <th class="jumbotron" style="text-align: center">Date</th>
                                <th class="jumbotron" style="text-align: center">Status</th>
                                <th class="jumbotron" style="text-align: center">Penalty Amount</th>
                                <th class="jumbotron" style="text-align: center">Agent Name</th>
                                <th class="jumbotron" style="text-align: center">Remarks</th>
                            </tr>
                                <?php foreach ($penalty as $key => $value){?>
                                <?php if($penalty[$key]['active'] == 1){?>
                                <tr>
                                    <td><?php
                                        $old_date = $penalty[$key]['create_date'];
                                        $old_date_timestamp = strtotime($old_date);
                                        $new_date = date('j F, Y g:i A', $old_date_timestamp);
                                        echo $new_date;
                                        ?>
                                    </td>
                                    <td><?php echo 'Penalty Added' ?></td>
                                    <td><?php echo $penalty[$key]['penalty_amount']; ?></td>
                                    <td><?php echo $penalty[$key]['agent_name']; ?></td>
                                    <td><?php echo $penalty[$key]['remarks']; ?></td>

                                </tr>
                                <?php }else if($penalty[$key]['active'] == 0){?>
                                <tr>
                                    <td><?php
                                        $old_date = $penalty[$key]['penalty_remove_date'];
                                        $old_date_timestamp = strtotime($old_date);
                                        $new_date = date('j F, Y g:i A', $old_date_timestamp);
                                        echo $new_date;
                                        ?>
                                    </td>
                                    <td><?php echo 'Penalty Removed' ?></td>
                                    <td><?php echo $penalty[$key]['penalty_amount']; ?></td>
                                    <td><?php echo $penalty[$key]['agent_name']; ?></td>
                                    <td><?php echo $penalty[$key]['penalty_remove_reason']; ?></td>

                                </tr>
                                <?php }?>
                                <?php }?>
                        </table>


                    <?php } ?>
                </div>
            </div>
        <?php } else { ?>
        
        <div class="alert alert-danger text-center" style="margin: 10px;">No Data Found</div>
        <?php } ?>
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

<script>
        $('.get_cash_invoice_id_data').click(function(){
            var invoice_id = $.trim($(".get_cash_invoice_id_data").attr("data-id"));
            get_invoice_data(invoice_id)
        });
        $('.get_foc_invoice_id_data').click(function(){
            var invoice_id = $.trim($(".get_foc_invoice_id_data").attr("data-id"));
            get_invoice_data(invoice_id)
        });
        $('.get_partner_invoice_id_data').click(function(){
            var invoice_id = $.trim($(".get_partner_invoice_id_data").attr("data-id"));
            get_invoice_data(invoice_id)
        });
    
    function get_invoice_data(invoice_id){
        if (invoice_id){
                $.ajax({
                    method: 'POST',
                    data: {invoice_id: invoice_id},
                    url: '<?php echo base_url(); ?>employee/accounting/search_invoice_id',
                    success: function (response) {
                        console.log(response);
                        $("#open_model").html(response);   
                        $('#invoiceDetailsModal').modal('toggle');

                    }
                });
            }else{
                console.log("Contact Developers For This Issue");
            }
    }
</script>