<?php if(!empty($booking_history)) { ?> 
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false&libraries=places&key=AIzaSyB4pxS4j-_NBuxwcSwSFJ2ZFU-7uep1hKc"></script>
<script src="<?php echo base_url();?>js/googleScript.js"></script> 
<style type="text/css">
    th,td{
    border: 1px #f2f2f2 solid;
    vertical-align: center;
    padding: 1px;
    }
    tr:nth-child(even) {background-color: #f2f2f2}
    .spare_image {
    width: 350px;
    height: 300px;
    background: url('<?php echo base_url() ?>images/loader.gif') 50% no-repeat;
    border: 1px solid black;
    border-radius: 5px;
    }
    .btn-pref .btn {
    -webkit-border-radius:0 !important;
    }
    .btn-primary{
    color: #fff;
    background-color: #2C9D9C;
    border-color: #2C9D9C;
    }
    .btn-primary.active, .btn-primary.focus, 
    .btn-primary:active, .btn-primary:focus, 
    .btn-primary:hover, .open>.dropdown-toggle.btn-primary {
    color: #fff;
    background-color: #2C9D9C;
    border-color: #2C9D9C;
    }
    .btn-default.active, .btn-default.focus, 
    .btn-default:active, .btn-default:focus, 
    .btn-default:hover, .open>.dropdown-toggle.btn-default {
    color: #fff;
    background-color: #2C9D9C;
    border-color: #2C9D9C;
    }
</style>
<div class="page-wrapper" style="margin-top:35px;">
<div class="btn-pref btn-group btn-group-justified btn-group-lg" role="group" aria-label="...">
    <div class="btn-group" role="group">
        <button type="button" class="btn btn-primary" href="#tab1" data-toggle="tab">
            <div class="hidden-xs">Booking Details</div>
        </button>
    </div>
    <div class="btn-group" role="group">
        <button type="button" class="btn btn-default" href="#upcountry" data-toggle="tab">
            <div class="hidden-xs">SF / Upcountry</div>
        </button>
    </div>
    <div class="btn-group" role="group">
        <button type="button" class="btn btn-default" href="#tab2" data-toggle="tab">
            <div class="hidden-xs">Appliance Details</div>
        </button>
    </div>
    <div class="btn-group" role="group">
        <button type="button" class="btn btn-default" href="#tab3" data-toggle="tab">
            <div class="hidden-xs">Spare Parts</div>
        </button>
    </div>
    <div class="btn-group" role="group">
        <button type="button" class="btn btn-default" href="#tab4" data-toggle="tab">
            <div class="hidden-xs">History / Sms</div>
        </button>
    </div>
    <div class="btn-group" role="group">
        <button type="button" class="btn btn-default" href="#tab5" data-toggle="tab">
            <div class="hidden-xs">Penalty</div>
        </button>
    </div>
    <?php if($engineer_action_not_exit) { ?>
    <div class="btn-group" role="group">
        <button type="button" class="btn btn-default" href="#tab6" data-toggle="tab">
            <div class="hidden-xs">Engineer Action</div>
        </button>
    </div>
    <?php }  if($booking_history[0]['current_status'] != 'Cancelled'){?>
<div class="btn-group" role="group">
    <button type="button" class="btn btn-default" href="#tab7" data-toggle="tab">
        <div class="hidden-xs">Transactions</div>
    </button>
</div>
    <?php }?>
</div>
<div class="well">
    <div class="tab-content">
        <div class="tab-pane fade in active" id="tab1">
            <div class="row">
                <div class="col-md-12">
                    <table class="table  table-striped table-bordered" >
                        <tr>
                            <th>Name </th>
                            <td><?php echo $booking_history[0]['name']; ?></td>
                            <th>Mobile </th>
                            <td>
                                <?php echo $booking_history[0]['booking_primary_contact_no'] . ' / ' . $booking_history[0]['booking_alternate_contact_no']; ?>
                                <button type="button" onclick="outbound_call(<?php echo $booking_history[0]['booking_primary_contact_no'] ?>)" class="btn btn-sm btn-info pull-right"><i class="fa fa-phone fa-lg" aria-hidden="true"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <th >Booking ID </th>
                            <td><?php echo "<a href='"."https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/jobcards-pdf/".$booking_history[0]['booking_jobcard_filename']."'>".$booking_history[0]['booking_id']."</a>"; ?></td>
                            <th >Order ID </th>
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
                                <?php } ?> 
                            </td>
                        </tr>
                        <tr>
                            <th>Appliance </th>
                            <td><?php echo $booking_history[0]['services']." ( ".$booking_history[0]['quantity']." Units)"; ?></td>
                            <th>Source </th>
                            <td><?php echo $booking_history[0]['public_name'] . ' / ' . $booking_history[0]['partner_source']; ?></td>
                        </tr>
                        <tr>
                            <th>Booking Date/ Timeslot </th>
                            <td><?php echo $booking_history[0]['booking_date']." / ".$booking_history[0]['booking_timeslot']; ?></td>
                            <th>Amount Due / Paid  </th>
                            <td><i class="fa fa-rupee"></i> <?php echo $booking_history[0]['amount_due']." / ".$booking_history[0]['amount_paid']; ?>
                            <button style="background-color: #2C9D9C;color:#fff;border-color: #2C9D9C;" type="button" class="btn btn-default" data-toggle="modal" data-target="#paytm_transaction" onclick="get_transaction_status(<?php echo "'".$booking_history[0]['booking_id']."'"?>)">Get Paytm Transaction Status</button>
                            </td>
                        </tr>
                        <?php if(isset($booking_history[0]['onlinePaymentAmount'])) { ?>
                        <tr>
                            <th>Payment Channel </th>
                            <td><?php echo $booking_history[0]['channels']; ?></td>
                            <th>Customer Paid Through Paytm </th>
                            <td><i class="fa fa-rupee"></i> <?php echo $booking_history[0]['onlinePaymentAmount']; ?></td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <th>Booking Address </th>
                            <td style="max-width:200px;"><?php echo $booking_history[0]['booking_address'];?></td>
                            <th>Booking City/District: </th>
                            <td><?php echo ($booking_history[0]['city']."/".$booking_history[0]['district']); ?></td>
                        </tr>
                        <tr>
                            <th>Booking State </th>
                            <td><?php echo $booking_history[0]['state']; ?></td>
                            <th>Booking Pincode </th>
                            <td><?php echo $booking_history[0]['booking_pincode']; ?></td>
                        </tr>
                        <tr>
                            <th>247Around Status </th>
                            <td><?php echo $booking_history[0]['current_status']." / ". $booking_history[0]['internal_status']; ?></td>
                            <th>Partner Status </th>
                            <td><?php echo $booking_history[0]['partner_internal_status']; ?></td>
                        </tr>
                        <tr>
                            <th>Booking Create / Closed Dated </th>
                            <td><?php if(!empty($booking_history[0]['closed_date'])){ echo date("jS M, Y", strtotime($booking_history[0]['create_date'])).
                                " / ".date("jS M, Y", strtotime($booking_history[0]['closed_date'])); } 
                                else  { echo date("jS M, Y", strtotime($booking_history[0]['create_date'])); } ?></td>
                            <th>EDD / Delivery Date</th>
                            <td><?php echo $booking_history[0]['estimated_delivery_date']." / ".$booking_history[0]['delivery_date']; ?></td>
                        </tr>
                        <?php if(!empty($booking_history[0]['dealer_id'])) { ?> 
                        <tr id="dealer_details">
                            <th>Dealer Name </th>
                            <td id="dealer_name"></td>
                            <th>Dealer Phone Number </th>
                            <td id="dealer_phone_number"></td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <th>Rating Stars </th>
                            <td><?php echo $booking_history[0]['rating_stars']; ?></td>
                            <th >Rating Comments </th>
                            <td style="max-width:200px;"><?php echo $booking_history[0]['rating_comments']; ?></td>
                        </tr>
                        <tr >
                            <th>Query Remarks </th>
                            <td style="max-width:200px;"><?php echo $booking_history[0]['query_remarks']; ?></td>
                            <th>Booking Remarks </th>
                            <td style="max-width:200px;"><?php echo $booking_history[0]['booking_remarks']; ?></td>
                        </tr>
                        <tr >
                            <th >Reschedule Reason </th>
                            <td style="max-width:200px;"><?php echo $booking_history[0]['reschedule_reason']; ?></td>
                            <th >Cancellation Reason </th>
                            <td style="max-width:200px;"><?php echo $booking_history[0]['cancellation_reason']; ?></td>
                        </tr>
                        <tr>
                            <th>Service Promise Date</th>
                            <td ><?php echo $booking_history[0]['service_promise_date'];?></td>
                            <th >Paid By Customer(STS)</th>
                            <td ><?php if(!is_null($booking_history[0]['paid_by_customer'])) { if($booking_history[0]['paid_by_customer'] == 1){ echo "Paid By Customer"; } 
                                else {echo "Free For Customer";}} ?></td>
                        </tr>
                        <tr>
                            <th>Closing Remarks</th>
                            <td colspan="3"><?php echo $booking_history[0]['closing_remarks'];?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="tab-pane fade in" id="upcountry">
            <div class="row">
                <div class="col-md-12">
                    <?php if(!empty($booking_history[0]['vendor_name'])){?>
                    <table class="table  table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>SF Name </th>
                                <th>Poc Name </th>
                                <th>Poc Number </th>
                                <th>Municipal Limit </th>
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
                                <td><?php if($booking_history[0]['is_upcountry'] == 1){ echo $booking_history[0]["municipal_limit"]." KM";}  ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table  table-striped table-bordered">
                        <thead>
                            <th>One Way Distance </th>
                            <th>Upcountry Distance </th>
                            <th>Upcountry District </th>
                            <th>Upcountry Pincode</th>
                            <th>Upcountry Remarks </th>
                        <thead>
                        <tbody>
                            <tr>
                                <td><?php echo round(($booking_history[0]["upcountry_distance"] + ($booking_history[0]["municipal_limit"] * 2))/2,2) . " KM"; ?></td>
                                <td><?php if($booking_history[0]['is_upcountry'] == 1){ echo $booking_history[0]["upcountry_distance"]." KM";} ?></td>
                                <td> <?php if(isset($dhq[0]['district'])){echo $dhq[0]['district'];}?></td>
                                <td><?php if(isset($dhq[0]['pincode'])){ echo $dhq[0]['pincode'];} ?></td>
                                <td><?php echo $booking_history[0]["upcountry_remarks"];  ?></td>
                            </tr>
                            <tr>
                                <?php if($booking_history[0]['is_upcountry'] == 1){  ?>  
                            <tr>
                                <td colspan="8">
                                    <div class="col-md-12">
                                        <div class="col-md-4"> <input type="text" class="form-control" id="txtSource" value="<?php echo $booking_history[0]['city'].", ".
                                            $booking_history[0]['booking_pincode'].", india"; ?>"></div>
                                        <div class="col-md-4">   <input type="text" class="form-control" id="txtDestination" value="<?php if(isset($dhq[0]['district'])){
                                            echo $dhq[0]['district'].",".$dhq[0]['pincode'].", India";}?>"></div>
                                        <div class="col-md-4"> <button class="btn btn-success" onclick="GetRoute()">Get Route</button></div>
                                    </div>
                                    <div class="col-md-12">
                                        <div id="dvDistance" style="display:none;"></div>
                                        <br/>
                                        <div id="dvMap" style=" height: 200px">
                                        </div>
                                        </div
                                </td>
                            </tr>
                            <?php } ?>
                            </tr>
                        </tbody>
                    </table>
                    <?php } else {
                        echo "Booking is not Assign";
                        } ?>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade in" id="tab2">
                <div class="row">
                    <div class="col-md-12">
                        <?php if(!empty($unit_details)) { ?>
                        <table class="table  table-striped table-bordered">
                            <tr>
                                <th>Brand</th>
                                <th>Category/<br/>Capacity</th>
                                <th>Model Number</th>
                                <th>Serial Number</th>
                                <th>Purchase Date</th>
                                <th>Description</th>
                                <th>Service Category</th>
                                <th>Pay to SF</th>
                                <th>Broken</th>
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
                                <th>SF Earning</th>
                            </tr>
                            <tbody>
                                <?php  foreach ( $unit_details as $key =>  $unit_detail) { ?>
                                <tr>
                                    <td><?php echo $unit_detail['appliance_brand']?></td>
                                    <td><?php echo $unit_detail['appliance_category']."/<br/>".$unit_detail['appliance_capacity']?></td>
                                    <td><?php echo $unit_detail['model_number']?></td>
                                    <td><?php echo $unit_detail['serial_number']?></td>
                                    <td><?php if(!empty($unit_detail['purchase_month'])) {echo $unit_detail['purchase_month']."-". $unit_detail['purchase_year'];} else { echo $unit_detail['purchase_year'];}?></td>
                                    <td><?php echo $unit_detail['appliance_description']?></td>
                                    <?php if($booking_history[0]['current_status'] != "Completed"){ ?>
                                    <td><?php  print_r($unit_detail['price_tags']); ?></td>
                                    <td><?php if($unit_detail['pay_to_sf'] ==1){ echo "Yes"; } else { echo "No";} ?></td>
                                    <td><?php if($unit_detail['is_broken'] ==1){ echo "Yes"; } else { echo "No";} ?></td>
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
                                    <td><?php if($unit_detail['pay_to_sf'] ==1){ echo "YES"; } else { echo "NO";} ?></td>
                                    <td><?php if($unit_detail['is_broken'] ==1){ echo "Yes"; } else { echo "No";} ?></td>
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
                                    <?php $sf_upcountry_charges = 0; if($booking_history[0]['is_upcountry'] == 1){ 
                                        if($key == 0){
                                            if($booking_history[0]['upcountry_paid_by_customer'] == 0){
                                                $sf_upcountry_charges =  $booking_history[0]['upcountry_distance'] * $booking_history[0]['sf_upcountry_rate'];
                                            } else {
                                                $sf_upcountry_charges = -($booking_history[0]['customer_paid_upcountry_charges'] * basic_percentage);
                                        
                                            }
                                        }
                                        }?>
                                    <td><?php print_r($unit_detail['booking_status']); ?></td>
                                    <?php if($booking_history[0]['current_status'] === 'Completed'){ ?>
                                    <td><a  href="javascript:void(0)" onclick="get_invoice_data('<?php echo $unit_detail['vendor_cash_invoice_id']; ?>')" ><?php echo $unit_detail['vendor_cash_invoice_id']; ?></a></td>
                                    <td><a  href="javascript:void(0)" onclick="get_invoice_data('<?php echo $unit_detail['vendor_foc_invoice_id']; ?>')" ><?php echo $unit_detail['vendor_foc_invoice_id']; ?></a></td>
                                    <td><a  href="javascript:void(0)" onclick="get_invoice_data('<?php echo $unit_detail['partner_invoice_id']; ?>')"><?php echo $unit_detail['partner_invoice_id'];?></a></td>
                                    <?php }  ?>
                                    <td>
                                        <?php echo round($unit_detail['vendor_basic_charges'] + $unit_detail['vendor_st_or_vat_basic_charges'] + 
                                            $unit_detail['vendor_extra_charges']  +  $unit_detail['vendor_st_extra_charges']  + 
                                             $unit_detail['vendor_parts']  + $unit_detail['vendor_st_parts'] +
                                            $sf_upcountry_charges, 2);?>
                                    </td>
                                    <?php } ?>
                            </tbody>
                        </table>
                        <?php  } ?>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade in" id="tab3">
                <?php if (isset($booking_history['spare_parts'])) { $estimate_given = false; $parts_shipped = false; $defective_parts_shipped = FALSE; ?>
                <div class="row">
                    <div class="col-md-12" >
                        <h1 style='font-size:24px;margin-top: 40px;'>Spare Parts Requested By SF</h1>
                        <div class="col-md-12" style="padding-left:1px;">
                            <table class="table  table-striped table-bordered" >
                                <thead>
                                    <tr>
                                        <th >Model Number </th>
                                        <th >Requested Parts </th>
                                        <th >Requested Date</th>
                                        <th >Invoice Image </th>
                                        <th >Serial Number Image </th>
                                        <th >Defective Part Image </th>
                                        <th >Serial Number </th>
                                        <th >Acknowledge Date BY SF </th>
                                        <th >Remarks By SC </th>
                                        <th >Current Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($booking_history['spare_parts'] as $sp) { ?>
                                    <tr>
                                        <td><?php echo $sp['model_number']; ?></td>
                                        <td><?php echo $sp['parts_requested']; ?></td>
                                        <td><?php echo $sp['create_date']; ?></td>
                                        <td><?php if (!is_null($sp['invoice_pic'])) {
                                            if ($sp['invoice_pic'] != '0') {
                                            ?> <a href="https://s3.amazonaws.com/bookings-collateral/misc-images/<?php echo $sp['invoice_pic']; ?> " target="_blank">Click Here to view Invoice Image</a><?php }
                                            }
                                            ?>
                                        </td>
                                        <td><?php if (!is_null($sp['serial_number_pic'])) {
                                            if ($sp['serial_number_pic'] !== '0') {
                                                ?> <a href="https://s3.amazonaws.com/bookings-collateral/misc-images/<?php echo $sp['serial_number_pic']; ?> " target="_blank">Click Here to view Serial Number Image</a><?php }
                                            }
                                            ?>
                                        </td>
                                        <td><?php if (!is_null($sp['defective_parts_pic'])) {
                                            if ($sp['defective_parts_pic'] !== '0') {
                                                ?> <a href="https://s3.amazonaws.com/bookings-collateral/misc-images/<?php echo $sp['defective_parts_pic']; ?> " target="_blank">Click Here to view Defective Part Image</a><?php }
                                            }
                                            ?>
                                        </td>
                                        <td><?php echo $sp['serial_number']; ?></td>
                                        <td><?php echo $sp['acknowledge_date']; ?></td>
                                        <td><?php echo $sp['remarks_by_sc']; ?></td>
                                        <td><?php echo $sp['status']; ?></td>
                                    </tr>
                                    <?php if(!is_null($sp['parts_shipped'])){ $parts_shipped = true;} if(!empty($sp['defective_part_shipped'])){
                                        $defective_parts_shipped = TRUE;
                                        } if($sp['purchase_price'] > 0){ $estimate_given = TRUE; } } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php 
                    if($estimate_given){ ?>
                <div class="row">
                    <div class="col-md-12">
                        <h1 style='font-size:24px;'>Estimate Given</h1>
                        <div class="col-md-12" style="padding-left:1px;">
                            <table class="table  table-striped table-bordered" >
                                <thead>
                                    <tr>
                                        <th >Estimate Given</th>
                                        <th >Estimate Given Date </th>
                                        <th >Purchase Invoice</th>
                                        <th >Sale Invoice ID</th>
                                        <th >Status </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($booking_history['spare_parts'] as $sp){ if($sp['purchase_price'] > 0) { ?>
                                    <tr>
                                        <td><?php echo $sp['purchase_price']; ?></td>
                                        <td><?php if(!empty($sp['estimate_cost_given_date'])) { echo date("d-m-Y", strtotime($sp['estimate_cost_given_date'])); } ?></td>
                                        <td><?php if(!is_null($sp['incoming_invoice_pdf'])) { if( $sp['incoming_invoice_pdf'] !== '0'){ ?> <a href="https://s3.amazonaws.com/bookings-collateral/invoices-excel/<?php echo $sp['incoming_invoice_pdf'];  ?> " target="_blank">Click Here</a><?php } } ?></td>
                                        <td><?php echo $sp['sell_invoice_id'];?></td>
                                        <td><?php echo $sp['status']; ?></td>
                                    </tr>
                                    <?php  } } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php  }
                    ?>
                <?php if ($parts_shipped) { ?>
                <div class="row">
                    <div class="col-md-12">
                        <h1 style='font-size:24px;'>Spare Parts Shipped</h1>
                        <div class="col-md-12" style="padding-left:1px;">
                            <table class="table  table-striped table-bordered" >
                                <thead>
                                    <tr>
                                        <th >Shipped Parts </th>
                                        <th >Courier Name</th>
                                        <th >AWB </th>
                                        <th >Shipped date </th>
                                        <th >EDD </th>
                                        <th >Remarks By Partner</th>
                                        <th >Challan Number </th>
                                        <th >Challan approx Value </th>
                                        <th>Challan File</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($booking_history['spare_parts'] as $sp) { if(!empty($sp['parts_shipped'])){ ?>
                                    <tr>
                                        <td><?php echo $sp['parts_shipped']; ?></td>
                                        <td><?php echo $sp['courier_name_by_partner']; ?></td>
                                        <td><?php echo $sp['awb_by_partner']; ?></td>
                                        <td><?php echo $sp['shipped_date']; ?></td>
                                        <td><?php echo $sp['edd']; ?></td>
                                        <td><?php echo $sp['remarks_by_partner']; ?></td>
                                        <td><?php echo $sp['partner_challan_number']; ?></td>
                                        <td><?php echo $sp['challan_approx_value']; ?></td>
                                        <td><a href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY?>/vendor-partner-docs/<?php echo $sp['partner_challan_file']; ?>" target="_blank">Click Here to view</a></td>
                                    </tr>
                                    <?php } } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php } if ($defective_parts_shipped) { ?>
                <div class="row">
                    <div class="col-md-12">
                        <h1 style='font-size:24px;'>Defective Spare Parts Shipped By SF</h1>
                        <div class="col-md-12" style="padding-left:1px;">
                            <table class="table  table-striped table-bordered" >
                                <thead>
                                    <tr>
                                        <th >Shipped Parts </th>
                                        <th >Courier Name </th>
                                        <th >AWB </th>
                                        <th >Courier Charge </th>
                                        <th> Courier Invoice</th>
                                        <th >Shipped date </th>
                                        <th >Remarks By SF </th>
                                        <th >Remarks By Partner </th>
                                        <th>SF Challan Number</th>
                                        <th>SF Challan File</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($booking_history['spare_parts'] as $sp) { ?>
                                    <tr>
                                        <td><?php echo $sp['defective_part_shipped']; ?></td>
                                        <td><?php echo $sp['courier_name_by_sf']; ?></td>
                                        <td><?php echo $sp['awb_by_sf']; ?></td>
                                        <td><?php echo $sp['courier_charges_by_sf']; ?></td>
                                        <td><a href="https://s3.amazonaws.com/bookings-collateral/misc-images/<?php echo $sp['defective_courier_receipt']; ?> " target="_blank">Click Here to view</a></td>
                                        <td><?php echo date('Y-m-d', strtotime($sp['defective_part_shipped_date'])); ?></td>
                                        <td><?php echo $sp['remarks_defective_part_by_sf']; ?></td>
                                        <td><?php echo $sp['remarks_defective_part_by_partner']; ?></td>
                                        <td><?php echo $sp['sf_challan_number']; ?></td>
                                        <td><a href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY?>/vendor-partner-docs/<?php echo $sp['sf_challan_file']; ?>" target="_blank">Click Here to view</a></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php } ?>
                <?php } else { ?> 
                <div class="text-danger">Spare Part Not Requested</div>
                <?php } ?>
            </div>
            <div class="tab-pane fade in" id="tab4">
            </div>
            <div class="tab-pane fade in" id="tab5">
                <div class="row">
                    <div class="col-md-12">
                        <?php if (!empty($penalty)) { ?>
                        <table  class="table table-striped table-bordered">
                            <tr>
                                <th >Date</th>
                                <th >Status</th>
                                <th >Penalty Amount</th>
                                <th >Penalty On SF</th>
                                <th >Agent Name</th>
                                <th >Remarks</th>
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
                                <td><?php echo $penalty[$key]['sf_name']; ?></td>
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
                                <td><?php echo $penalty[$key]['sf_name']; ?></td>
                                <td><?php echo $penalty[$key]['agent_name']; ?></td>
                                <td><?php echo $penalty[$key]['penalty_remove_reason']; ?></td>
                            </tr>
                            <?php }?>
                            <?php }?>
                        </table>
                        <?php } else { echo "Penalty Not Found";?>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?php if($engineer_action_not_exit) { ?>
            <div class="tab-pane fade in" id="tab6">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table  table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Service Category</th>
                                    <th>Broken</th>
                                    <th>Serial Number</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($unit_details as $unit){?>
                                <tr>
                                    <td><?php echo $unit["price_tags"];?></td>
                                    <td><?php if($unit['en_is_broken'] ==1){ echo "Yes"; } else { echo "No";} ?></td>
                                    <td><a href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY;?>/engineer-uploads/<?php echo $unit['en_serial_number_pic'];?>" target="_blank"><?php  echo $unit['en_serial_number']; ?></a></td>
                                    <td><?php  echo $unit['en_current_status']." / ".$unit['en_internal_status']; ?></td>
                                </tr>
                                <?php }?>
                            </tbody>
                        </table>
                        <?php if(isset($signature_details) && !empty($signature_details)){ ?>
                        <table class="table  table-striped table-bordered">
                            <tr>
                                <th>Amount Paid</th>
                                <th>Customer Signature</th>
                                <th>Closed Date</th>
                                <th>Closing Address</th>
                                <th>Remarks</th>
                            </tr>
                            <tbody>
                                <tr>
                                    <td><?php echo $signature_details[0]['amount_paid']; ?></td>
                                    <td><a href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY;?>/engineer-uploads/<?php echo $signature_details[0]['signature'];?>" target="_blank">Click Here</a></td>
                                    <td><?php echo $signature_details[0]['closed_date']; ?></td>
                                    <td><?php echo $signature_details[0]['address']; ?></td>
                                    <td><?php echo $signature_details[0]['remarks']; ?></td>
                                </tr>
                            </tbody>
                        </table>
                        <?php }?>
                    </div>
                </div>
            </div>
        <?php
                }
        ?>   
        <div class="tab-pane fade in" id="tab7">
                <div class="row">
                    <div class="col-md-12">
                        <div style="">
                            <table class="table  table-striped table-bordered">
                                <tr>
                                    <?php 
                                    $temp = 0;
                                     if(!empty($booking_history[0]['amount_paid'])){$temp++?>
                                        <th colspan="1">Total Paid Amount</th>
                                        <td colspan="3"><?php echo $booking_history[0]['amount_paid'];?></td>
                                     <?php } 
                                     if(isset($booking_history[0]['onlinePaymentAmount'])){$temp++
                                     ?>
                                        <th colspan="1">Paid through Paytm</th>
                                        <td colspan="3"><?php echo $booking_history[0]['onlinePaymentAmount'];?></td>
                                     <?php }
                                     if(!empty($unit_details[0]['user_invoice_id'])){$temp++?>
                                        <th colspan="1">Customer Invoice</th>
                                        <td colspan="3"><?php echo $unit_details[0]['user_invoice_id']?></td>
                                     <?php }?>
                                </tr>
                            </table>
                              <?php if($temp !=0){ ?>
                             <hr style="border: 1px solid #5bc0de;">
                              <?php } ?>
                        </div>
                        <div style="background: #5bc0de;margin-bottom: 20px;">
                            <?php if($booking_history[0]['current_status'] != 'Cancelled' && $booking_history[0]['current_status'] != 'Completed'){ ?>
                        <a target="_blank" href="<?php echo base_url(); ?>payment/resend_QR_code/<?php echo $booking_history[0]['booking_id']?>/1" class="btn btn-success action_buton" 
                           >Regenerate and send QR Code</a>
                               <a target="_blank" href="<?php echo base_url(); ?>payment/resend_QR_code/<?php echo $booking_history[0]['booking_id']?>/0" class="btn btn-success action_buton">
                                   Resend Same QR Code</a>
                            <?php } ?>
                               <button type="button" class="btn btn-success action_buton">Resend Customer Invoice</button>
                               </div>
                         <?php if($paytm_transaction) { ?>   
                        <hr style="border: 1px solid #5bc0de;">
                        <h3>Paytm Transaction and Cashback Details</h3>
                <table class="table  table-striped table-bordered">
                <thead>
                    <tr>
                        <th>S.N</th>
                        <th>Paid Amount</th>
                        <th>Txn ID</th>
                        <th>Transaction Date</th>
                        <th>Channel</th>
                        <th>Vendor<br> Invoice</th>
                        <?php
                        if($this->session->userdata('user_group') == 'admin'){
                        ?>
                        <th>Initiate<br> Cashback</th>
                        <?php
                            }
                        ?>
                       <th>Cashback</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $index =1;
                    foreach($paytm_transaction as $paytm){
                        $tempPaidArray[] = $paytm['paid_amount'];
                        ?>
                    <tr>
                 <td ><?php echo $index?></td>
                <td ><?php echo $paytm['paid_amount']?></td>
                <td ><?php echo $paytm['txn_id']?></td>
                <td ><?php echo $paytm['create_date']?></td>
                <td ><?php echo explode("_",$paytm['order_id'])[1]?></td>
                <td>
                    <?php if($paytm['vendor_invoice_id']){?>
                    <a target="_blank" style="background-color: #5bc0de;color:#fff;border-color: #5bc0de;" class="btn btn-sm" href="<?php echo S3_WEBSITE_URL."invoices-excel/".$paytm['vendor_invoice_id'].".pdf"?>"
                    title="Partner Invoice"> <i class="fa fa-file-pdf-o" aria-hidden="true"></i></a><?php } ?>
                </td>
                       <?php 
                        if($this->session->userdata('user_group') == 'admin'){?>
                <td>
                <button style="background-color: #5bc0de;color:#fff;border-color: #5bc0de;padding: 5px 8px;" type="button" class="btn btn-default" data-toggle="modal" data-target="#processCashback" 
                        onclick="create_cashback_form(<?php echo "'".$paytm['paid_amount']."'"?>,<?php echo "'".$paytm['txn_id']."'"?>,<?php echo "'".$paytm['order_id']."'"?>)">
                        <i class="fa fa-money" aria-hidden="true"></i></button></td>
                            <?php
                        }?>
                        <td ><?php
                $tempCashbackHolder = array();
                if($paytm['cashback_amount']){
                    $cashbackAmountArray = explode(",",$paytm['cashback_amount']);
                    $cashbackReasonArray = explode(",",$paytm['cashback_reason']);
                    $cashbackFromArray = explode(",",$paytm['cashback_from']);
                    $cashbackDateArray = explode(",",$paytm['cashback_date']);
                    $tempCashbackHolder[] = array_sum($cashbackAmountArray);
                    ?>
                    <table class="table  table-striped table-bordered">
                        <tr>
                                    <th colspan="1">S.N</th>    
                                     <th colspan="1">Cashback Amount</th>
                                      <th colspan="2">Cashback BY</th>
                                      <th colspan="2">Reason</th>
                                      <th colspan="3">Date</th>
                                      </tr>
                    <?php
                    $cashbackIndex = 1;
                    foreach($cashbackAmountArray as $index=>$value){
                        ?>
                        <tr>
                            <td colspan="1"><?php echo $cashbackIndex?></td>
                            <td colspan="1"><?php echo $cashbackAmountArray[$index]?></td>
                            <td colspan="2"><?php echo $cashbackFromArray[$index]?></td>
                            <td colspan="2"><?php echo $cashbackReasonArray[$index]?></td>
                            <td colspan="3"><?php echo $cashbackDateArray[$index]?></td>
                            </tr>
                        <?php
                        $cashbackIndex++;
                    }
                    ?></table>
                            <?php
                }
                ?></td>
                <?php $index++;?>
                </tr>
                    <?php
                    }
                    ?>
                </tbody>
                </table>
                        </div>
                    
                            <?php } ?>
                      
                    </div>
            <hr style="border: 1px solid #5bc0de;">
               </div>     
        </div>
        
    </div>
</div>
<div id="paytm_transaction" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">New Transactions</h4>
      </div>
        <div class="modal-body" id="transaction_response_container" align="center">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
                    <div id="processCashback" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Initiate Cashback</h4>
      </div>
<div class="modal-body" id="response_container" style="display:none;">
            
        </div>
        <div class="modal-body" id="form_container">
        <form>
            <div class="form-group" style="display:none;">
      <input type="text" class="form-control" id="form_transaction_id" value="">
  </div>
  <div class="form-group" style="display:none;">
      <input type="text" class="form-control" id="form_order_id" value="">
  </div>
             <div class="form-group">
                 <input type="text" class="form-control" id="form_paid_amount" value="" readonly="">
  </div>
             <div class="form-group">
                 <input type="number" class="form-control" id="form_cashback_amount" value="" placeholder="Cashback Amount" required="">
  </div>
             <div class="form-group">
                 <input type="text" class="form-control" id="form_cashback_reason" value="" placeholder="Cashback Reason" required="">
  </div>
            <button type="button" class="btn btn-primary" onclick="process_cashback_form()">Process Cashback</button>
</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
<script>
 function process_cashback_form(){
            var cashback_amount = document.getElementById("form_cashback_amount").value;
            var cashback_reason = document.getElementById("form_cashback_reason").value;
            var order_id =  document.getElementById("form_order_id").value;
            var transaction_id = document.getElementById("form_transaction_id").value;
           var confirm_value = confirm("Are you Sure You want to process a refund for amount "+cashback_amount);
        if(confirm_value == true){
            if(cashback_amount && cashback_reason){
                var url = '<?php echo base_url(); ?>payment/process_cashback_by_form';
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: {order_id: order_id, transaction_id: transaction_id, cashback_amount: cashback_amount, cashback_reason: cashback_reason},
                    success: function (response) {
                        $('#form_container').hide();
                        $('#response_container').show();
                        document.getElementById("response_container").innerHTML = response;
                      }
                });
         }
         else{
             alert("Please enter cashback amount and reason");
         }
     }
     else{
     location.reload();
     }
   }
    function create_cashback_form(paid_amount,transaction_id,order_id){
    $('#form_container').show();
        $('#response_container').hide();
        document.getElementById("form_cashback_amount").value="";
        document.getElementById("form_cashback_reason").value="";
        document.getElementById("form_paid_amount").value=paid_amount;
        document.getElementById("form_order_id").value=order_id;
        document.getElementById("form_transaction_id").value=transaction_id;
}
    $('document').ready(function () {
        var booking_id = '<?php echo base_url() ?>employee/booking/get_booking_life_cycle/<?php echo $booking_history[0]['booking_id'] ?>';
                $.ajax({
                    type: 'POST',
                    url: booking_id,
                    success: function (response) {
                        $('#tab4').html(response);
                    }
                });
            });
    
            $(document).ready(function () {
                $(".btn-pref .btn").click(function () {
                    $(".btn-pref .btn").removeClass("btn-primary").addClass("btn-default");
                    // $(".tab").addClass("active"); // instead of this do the below 
                    $(this).removeClass("btn-default").addClass("btn-primary");
                });
            });
</script>
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
    function get_invoice_data(invoice_id){
        if (invoice_id){
                $.ajax({
                    method: 'POST',
                    data: {invoice_id: invoice_id},
                    url: '<?php echo base_url(); ?>employee/accounting/search_invoice_id',
                    success: function (response) {
                        //console.log(response);
                        $("#open_model").html(response);   
                        $('#invoiceDetailsModal').modal('toggle');
    
                    }
                });
            }else{
                console.log("Contact Developers For This Issue");
            }
    }
    function get_transaction_status(booking_id){
        $.ajax({
                    method: 'POST',
                    data: {},
                    url: '<?php echo base_url(); ?>payment/get_booking_transaction_status_by_check_status_api/'+booking_id,
                    success: function (response) {
                        document.getElementById("transaction_response_container").innerHTML = response;
                    }
                });
    }
    
    <?php if(!empty($booking_history[0]['dealer_id'])) { ?>
         $.ajax({
             method:'GET',
             url:'<?php echo base_url(); ?>employee/dealers/get_dealer_data/'+<?php echo $booking_history[0]['dealer_id']?>,
             success:function(response){
                 obj = JSON.parse(response);
                 if(obj.msg){
                    $('#dealer_name').html(obj.data[0].dealer_name);
                    $('#dealer_phone_number').html(obj.data[0].dealer_phone_number_1);
                 }else{
                     $('#dealer_details').hide();
                 }
                 
             }
         });
    <?php } ?>
</script>
<style>
    .action_buton{
        margin: 10px;
background-color: #f5f5f5;
    border-color: #ffffff;
    color: black;
    }
    </style>
<?php }else { ?>
    <div class="container">
        <div class="row">
            <div class="alert alert-danger text-center"> Booking Id not found</div>
        </div>
    </div>
 <?php } ?>