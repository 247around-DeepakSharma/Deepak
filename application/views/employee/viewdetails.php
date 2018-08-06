<?php if(!empty($booking_history)) { ?> 
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false&libraries=places&key=AIzaSyB4pxS4j-_NBuxwcSwSFJ2ZFU-7uep1hKc"></script>
<script src="<?php echo base_url();?>js/googleScript.js"></script> 
<style type="text/css">
    
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
    <button type="button" class="btn btn-default" onclick="sf_tab_active()" href="#upcountry" data-toggle="tab">
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
                                <a href="javascript:void(0);" onclick="outbound_call(<?php echo $booking_history[0]['booking_primary_contact_no'] ?>)"><?php echo $booking_history[0]['booking_primary_contact_no']; ?></a>
                                <?php if(!empty($booking_history[0]['booking_alternate_contact_no'])) { ?> 
                                / <a href="javascript:void(0);" onclick="outbound_call(<?php echo $booking_history[0]['booking_alternate_contact_no'] ?>)"><?php echo $booking_history[0]['booking_alternate_contact_no']; ?></a>   
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <th >Booking ID </th>
                            <td><?php echo "<a href='"."https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/jobcards-pdf/".$booking_history[0]['booking_jobcard_filename']."'>".$booking_history[0]['booking_id']."</a>"; ?></td>
                            <th >Order ID </th>
                            <td>
                                <input type="file" id="supportfileLoader" name="files" onchange="uploadsupportingfile()" style="display:none" />
                                <div class="progress-bar progress-bar-success myprogress" id="<?php echo "myprogress_supproting_file";?>"  role="progressbar" style="width:0%">0%</div>
                                <?php if(!empty($booking_history)){  echo $booking_history[0]['order_id'];
                                $src = base_url() . 'images/no_image.png';
                                $image_src = $src;
                                if (isset($booking_history[0]['support_file']) && !empty($booking_history[0]['support_file'])) {
                                    //Path to be changed
                                    $src = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/misc-images/".$booking_history[0]['support_file'];
                                    $image_src = base_url().'images/view_image.png';
                                }
                                ?>
                                <a id="a_order_support_file" href="<?php  echo $src?>" target="_blank"><img id="m_order_support_file" src="<?php  echo $image_src ?>" width="35px" height="35px" style="border:1px solid black;margin-left:10px;" /></a>
                                <?php } ?> 
                                &nbsp;&nbsp;<i id="supporting_file" class="fa fa-pencil fa-lg" onclick="upload_supporting_file();"></i>
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
                            <td style="max-width:200px;"><span class="text"><?php echo $booking_history[0]['booking_address'];?></span> <span class="edit"><i class="fa fa-pencil fa-lg"></i></span></td>
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
                        <?php if($booking_history[0]['internal_status'] != 'Completed') {?>
                         <tr>
                            <th> Actor / Action </th>
                            <td><?php echo $booking_history[0]['actor']." / ".$booking_history[0]['next_action']; ?></td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <th>Booking Create / Closed Dated </th>
                            <td><?php if(!empty($booking_history[0]['closed_date'])){ echo date("jS M, Y", strtotime($booking_history[0]['create_date'])).
                                " / ".date("jS M, Y", strtotime($booking_history[0]['service_center_closed_date'])); } 
                                else  { echo date("jS M, Y", strtotime($booking_history[0]['create_date'])); } ?></td>
                            <th>EDD / Delivery Date</th>
                            <td><?php echo $booking_history[0]['estimated_delivery_date']." / ".$booking_history[0]['delivery_date']; ?></td>
                        </tr>
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
                            <th >Jeeves CD/BD</th>
                            <td ><?php echo $booking_history[0]['api_call_status_updated_on_completed']; ?></td>
                        </tr>
                        <tr>
                            <th>Closing Remarks</th>
                            <td style="max-width: 330px;"><?php echo $booking_history[0]['closing_remarks'];?></td>
                            <th >Paid By Customer(STS)</th>
                            <td ><?php if(!is_null($booking_history[0]['paid_by_customer'])) { if($booking_history[0]['paid_by_customer'] == 1){ echo "Paid By Customer"; } 
                            else {echo "Free For Customer";}} ?></td>
                        </tr>
                    </table>
                    <table class="table  table-striped table-bordered" >
                        <tr>
                            <th colspan="4" style="font-size: 16px; color: #2c9d9c;">Dealer Detail</th>
                        </tr> 
                        <tr>
                            <th style="width: 25%;">Dealer Name </th>
                            <td style="width: 23%;"><?php if(isset($booking_history[0]['dealer_id'])) echo $booking_history[0]['dealer_name']; ?></td>
                            <th style="width: 21%;">Dealer Phone Number</th>
                            <td><?php if(isset($booking_history[0]['dealer_id'])) echo $booking_history[0]['dealer_phone_number_1']; ?></td>
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
                                <td><?php if(isset($booking_history[0]["municipal_limit"])) { echo $booking_history[0]["municipal_limit"]." KM"; }  ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <?php }  ?>
                    </div>
                    
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
                                <td> <?php if($booking_history[0]['is_upcountry'] == 1){ ?>
                                    <?php echo round(($booking_history[0]["upcountry_distance"] + ($booking_history[0]["municipal_limit"] * 2))/2,2) . " KM"; ?>
                                <?php } ?>
                                </td>
                                <td><?php if($booking_history[0]['is_upcountry'] == 1){ echo $booking_history[0]["upcountry_distance"]." KM";} ?></td>
                                <td> <?php if(isset($dhq[0]['district'])){echo $dhq[0]['district'];}?></td>
                                <td><?php if(isset($dhq[0]['pincode'])){ echo $dhq[0]['pincode'];} ?></td>
                                <td><?php echo $booking_history[0]["upcountry_remarks"];  ?></td>
                            </tr>
                           
                               
                            <tr>
                                <td colspan="8">
                                    <div class="col-md-12">
                                        <div class="col-md-4"> <input type="text" class="form-control" id="txtSource" value="<?php echo $booking_history[0]['booking_pincode'].", india"; ?>"></div>
                                        <div class="col-md-4">   <input type="text" class="form-control" id="txtDestination" value="<?php if(isset($dhq[0]['district'])){
                                            echo $dhq[0]['pincode'].", India";}?>"></div>
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
                           
                            </tr>
                        </tbody>
                    </table>
                
                </div>
            </div>
            <div class="tab-pane fade in" id="tab2">
                <div class="row">
                    <div class="col-md-12">
                        <?php if(!empty($unit_details)) { ?>
                        <table class="table  table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>Brand</th>
                                <th>Category/<br/>Capacity</th>
                                <th>Model Number</th>
                                <th>SF Model Number</th>
                                <th>SF / Partner Serial Number</th>
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
                            </thead>
                            <tbody>
                                <?php $user_invoice_id  = ""; foreach ( $unit_details as $key =>  $unit_detail) { 
                                   if(!empty($unit_detail['user_invoice_id'])){
                                       $user_invoice_id = $unit_detail['user_invoice_id'];
                                   }?>
                                <tr>
                                    <td><?php echo $unit_detail['appliance_brand']?></td>
                                    <td><?php echo $unit_detail['appliance_category']."/<br/>".$unit_detail['appliance_capacity']?></td>
                                    <td><?php echo $unit_detail['model_number']?></td>
                                    <td><?php echo $unit_detail['sf_model_number']?></td>
                                    <td><?php if(!empty($unit_detail['serial_number_pic'])){?>
                                        <a target="_blank" href="<?php echo S3_WEBSITE_URL;?>engineer-uploads/<?php echo $unit_detail['serial_number_pic'];?>"><?php echo $unit_detail['serial_number'];?></a>
                                             <?php } else { echo $unit_detail['serial_number'];} ?> / <?php echo $unit_detail['partner_serial_number']?>
                                    </td>
                                    <td><?php if(!empty($unit_detail['purchase_date'])) {echo $unit_detail['purchase_date'];}?></td>
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
                                        <?php echo sprintf("%0.2f",$unit_detail['vendor_basic_charges'] + $unit_detail['vendor_st_or_vat_basic_charges'] + 
                                            $unit_detail['vendor_extra_charges']  +  $unit_detail['vendor_st_extra_charges']  + 
                                             $unit_detail['vendor_parts']  + $unit_detail['vendor_st_parts'] +
                                            $sf_upcountry_charges);?>
                                    </td>
                                </tr>
                                    <?php } ?>
                            </tbody>
                        </table>
                        <?php  } ?>
                    </div>
                </div>
                <div class="row">
                    <center><img id="misc_charge_loader" style="width: 50px;" src="<?php echo base_url(); ?>images/loader.gif"/></center>
                     <div class="col-md-12" id="misc_charge_div" >
                        <h1 style='font-size:24px;margin-top: 40px;'>Miscellaneous Charge</h1>
                       
                        <div id="misc_charge">
                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade in" id="tab3">
                <?php if (isset($booking_history['spare_parts'])) { $estimate_given = false; $parts_shipped = false; $defective_parts_shipped = FALSE; ?>
                <input type="file" id="fileLoader" name="files" onchange="uploadfile()" style="display:none" />
                <div class="row">
                    <div class="col-md-12" >
                        <h1 style='font-size:24px;margin-top: 40px;'>Spare Parts Requested By SF</h1>
                        <div class="col-md-12" style="padding-left:1px;">
                            <table class="table  table-striped table-bordered" >
                                <thead>
                                    <tr>
                                        <th >Partner/Warehouse </th>
                                        <th >Model Number </th>
                                        <th >Requested Parts </th>
                                        <th >Requested Date</th>
                                        <th >Invoice Image </th>
                                        <th >Serial Number Image </th>
                                        <th >Defective Front Part Image </th>
                                        <th >Defective Back Part Image </th>
                                        <th >Serial Number </th>
                                        <th >Acknowledge Date BY SF </th>
                                        <th >Remarks By SC </th>
                                        <th >Current Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($booking_history['spare_parts'] as $sp) { ?>
                                    <tr>
                                        <td><?php if($sp['entity_type'] == _247AROUND_PARTNER_STRING){ echo "Partner";} else { echo "Warehouse";} ?></td>
                                        <td><?php echo $sp['model_number']; ?></td>
                                        <td><?php echo $sp['parts_requested']; ?></td>
                                        <td><?php echo $sp['create_date']; ?></td>
                                        <td><div class="progress-bar progress-bar-success myprogress" id="<?php echo "myprogressinvoice_pic".$sp['id'] ?>" role="progressbar" style="width:0%">0%</div><?php if (!is_null($sp['invoice_pic'])) {
                                            if ($sp['invoice_pic'] != '0') {
                                                ?> <a href="<?php echo S3_WEBSITE_URL; ?>misc-images/<?php echo $sp['invoice_pic']; ?> " target="_blank" id="<?php echo "a_invoice_pic_".$sp['id']; ?>">Click Here</a> &nbsp;&nbsp;<i id="<?php echo "invoice_pic_".$sp['id']; ?>" class="fa fa-pencil fa-lg" onclick="openfileDialog('<?php echo $sp["id"];?>','invoice_pic');"></i><?php }
                                            }
                                            ?>
                                        </td>
                                        <td><div class="progress-bar progress-bar-success myprogress" id="<?php echo "myprogressserial_number_pic".$sp['id'] ?>"  role="progressbar" style="width:0%">0%</div><?php if (!is_null($sp['serial_number_pic'])) {
                                            if ($sp['serial_number_pic'] !== '0') {
                                                ?> <a href="<?php echo S3_WEBSITE_URL; ?>misc-images/<?php echo $sp['serial_number_pic']; ?> " target="_blank" id="<?php echo "a_serial_number_pic_".$sp['id']; ?>">Click Here</a> &nbsp;&nbsp;<i id="<?php echo "serial_number_pic_".$sp['id']; ?>" class="fa fa-pencil fa-lg" onclick="openfileDialog('<?php echo $sp["id"];?>','serial_number_pic');"></i><?php }
                                            }
                                            ?>
                                        </td>
                                        <td><div class="progress-bar progress-bar-success myprogress" id="<?php echo "myprogressdefective_parts_pic".$sp['id'] ?>"  role="progressbar" style="width:0%">0%</div><?php if (!is_null($sp['defective_parts_pic'])) {
                                            if ($sp['defective_parts_pic'] !== '0') {
                                                ?> <a href="<?php echo S3_WEBSITE_URL; ?>misc-images/<?php echo $sp['defective_parts_pic']; ?> " target="_blank" id="<?php echo "a_defective_parts_pic_".$sp['id']; ?>">Click Here</a>&nbsp;&nbsp;<i id="<?php echo "defective_parts_pic_".$sp['id']; ?>" class="fa fa-pencil fa-lg" onclick="openfileDialog('<?php echo $sp["id"];?>','defective_parts_pic');"></i><?php }
                                            }
                                            ?>
                                        </td>
                                        <td><div class="progress-bar progress-bar-success myprogress" id="<?php echo "myprogressdefective_back_parts_pic".$sp['id'] ?>" role="progressbar" style="width:0%">0%</div><?php if (!is_null($sp['defective_back_parts_pic'])) {
                                            if ($sp['defective_back_parts_pic'] !== '0') {
                                                ?> <a href="<?php echo S3_WEBSITE_URL; ?>misc-images/<?php echo $sp['defective_back_parts_pic']; ?> " target="_blank" id="<?php echo "a_defective_back_parts_pic_".$sp['id']; ?>">Click Here</a>&nbsp;&nbsp;<i id="<?php echo "defective_back_parts_pic_".$sp['id']; ?>" class="fa fa-pencil fa-lg" onclick="openfileDialog('<?php echo $sp["id"];?>','defective_back_parts_pic');"></i><?php }
                                            }
                                            ?>
                                        </td>
                                        <td><span class="serial_no_text" id="<?php echo $sp['id']."|serial_number";?>"><?php echo $sp['serial_number']; ?></span> <span class="serial_no_edit"><i class="fa fa-pencil fa-lg"></i></td>
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
                                        <th >Estimate Given By Partner/Warehouse</th>
                                        <th >Estimate Cost</th>
                                        <th >Estimate Given Date </th>
                                        <th >Purchase Invoice</th>
                                        <th >Sale Invoice ID</th>
                                        <th >Status </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($booking_history['spare_parts'] as $sp){ if($sp['purchase_price'] > 0) { ?>
                                    <tr>
                                        <td><?php if($sp['entity_type'] == _247AROUND_PARTNER_STRING){ echo "Partner";} else { echo "Warehouse";} ?></td>
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
                                        <th >Part Shipped By Partner/Warehouse</th>
                                        <th >Shipped Parts </th>
                                        <th >Courier Name</th>
                                        <th >AWB </th>
                                        <th >Shipped date </th>
                                        <th >EDD </th>
                                        <th >Remarks By Partner</th>
                                        <th >Challan Number </th>
                                        <th >Challan approx Value </th>
                                        <th>Challan File</th>
                                        <th>Courier File</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($booking_history['spare_parts'] as $sp) { if(!empty($sp['parts_shipped'])){ ?>
                                    <tr>
                                        <td><?php if($sp['entity_type'] == _247AROUND_PARTNER_STRING) { echo "Partner";} else { echo "Warehouse";} ?></td>
                                        <td><?php echo $sp['parts_shipped']; ?></td>
                                        <td><?php echo ucwords(str_replace(array('-','_'), ' ', $sp['courier_name_by_partner'])); ?></td>
                                        <td><a href="#" onclick="get_awb_details('<?php echo $sp['courier_name_by_partner']; ?>','<?php echo $sp['awb_by_partner']; ?>','<?php echo $sp['status']; ?>')"><?php echo $sp['awb_by_partner']; ?></a> <span id="awb_loader" style="display:none;"><i class="fa fa-spinner fa-spin"></i></span></td>
                                        <td><?php echo $sp['shipped_date']; ?></td>
                                        <td><?php echo $sp['edd']; ?></td>
                                        <td><?php echo $sp['remarks_by_partner']; ?></td>
                                        <td><?php echo $sp['partner_challan_number']; ?></td>
                                        <td><?php echo $sp['challan_approx_value']; ?></td>
                                        <td>
                                            <?php if(!empty($sp['partner_challan_file'])){ ?> 
                                            <a href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY?>/vendor-partner-docs/<?php echo $sp['partner_challan_file']; ?>" target="_blank">Click Here to view</a>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php if(!empty($sp['courier_pic_by_partner'])){ ?> 
                                            <a href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY?>/vendor-partner-docs/<?php echo $sp['courier_pic_by_partner']; ?>" target="_blank">Click Here to view</a>
                                            <?php } ?>
                                        </td>
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
                                        <th>Update Spare Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($booking_history['spare_parts'] as $sp) { if(!empty($sp['defective_part_shipped'])){ ?>
                                    <tr>
                                        <td><?php echo $sp['defective_part_shipped']; ?></td>
                                        <td><?php echo ucwords(str_replace(array('-','_'), ' ', $sp['courier_name_by_sf'])); ?></td>
                                        <td><?php echo $sp['awb_by_sf']; ?></td>
                                        <td><?php echo $sp['courier_charges_by_sf']; ?></td>
                                        <td><a href="https://s3.amazonaws.com/bookings-collateral/misc-images/<?php echo $sp['defective_courier_receipt']; ?> " target="_blank">Click Here to view</a></td>
                                        <td><?php echo date('Y-m-d', strtotime($sp['defective_part_shipped_date'])); ?></td>
                                        <td><?php echo $sp['remarks_defective_part_by_sf']; ?></td>
                                        <td><?php echo $sp['remarks_defective_part_by_partner']; ?></td>
                                        <td><?php echo $sp['sf_challan_number']; ?></td>
                                        <td>
                                            <?php if(!empty($sp['sf_challan_file'])){ ?> 
                                            <a href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY?>/vendor-partner-docs/<?php echo $sp['sf_challan_file']; ?>" target="_blank">Click Here to view</a>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <a class="btn btn-primary" href="<?php echo base_url();?>employee/service_centers/update_spare_courier_details/<?php echo $sp['id'];?>" target="_blank">Update</a>
                                        </td>
                                    </tr>
                                    <?php } } ?>
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
                <div style="padding: 0 15px;">
    <div class="row">
                <div id="historyDetails"></div>
                <div id="commentbox"> </div>
    </div>
                </div>
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
                                     if(!empty($user_invoice_id)){$temp++?>
                                        <th colspan="1">Customer Invoice</th>
                                        <td colspan="3"><?php if(!empty($user_invoice_id)){ ?> <a href="<?php echo S3_WEBSITE_URL;?>invoices-excel/<?php echo $user_invoice_id.".pdf"; ?>"><?php echo $user_invoice_id;?></a><?php }?></td>
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
                            <?php if(!empty($user_invoice_id)) { ?>
                                  <a href="javascript:void(0)" onclick="resendCustomerInvoice('<?php echo $booking_history[0]['booking_id'];?>', '<?php echo $user_invoice_id; ?>')"  class="btn btn-success action_buton">Resend Customer Invoice</a> 
                                <?php } ?>

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
                    if($paytm['paid_amount']>$cashback_rules[0]['amount_criteria_less_than']){
                               $expectedCashback = ($paytm['paid_amount']*$cashback_rules[0]['cashback_amount_percentage'])/100;
                               if($expectedCashback>$cashback_rules[0]['paytm_cashback_limit']){
                                   $cashbackByPaytm = $cashback_rules[0]['paytm_cashback_limit'];
                               }
                               else{
                                   $cashbackByPaytm = $expectedCashback;
                               }
                        ?>
                             <tr>
                            <td colspan="1"><?php echo $cashbackIndex?></td>
                            <td colspan="1"><?php echo $cashbackByPaytm;?></td>
                            <td colspan="2">Paytm</td>
                            <td colspan="2">Discount</td>
                            <td colspan="3"></td>
                            </tr>
                     <?php
                     $cashbackIndex++;
                    }
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



    <!-- model -->
    <div id="gen_model" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" id="gen_model_title"></h4>
                </div>
                <div class="modal-body" id="gen_model_body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>
<script>
function sf_tab_active(){
  <?php if($booking_history[0]['is_upcountry'] == 1){  ?>  
   setTimeout(function(){ GetRoute(); }, 1000);
  <?php } ?>
}
 function resendCustomerInvoice(booking_id, invoice_id){
        alert("Please Wait! we will send invoice to customer via sms or email");
         var url ="<?php echo base_url();?>employee/user_invoice/resend_customer_invoice/"+ booking_id+"/"+invoice_id;
         $.ajax({
             method:'POST',
             url: url, 
             success:function(response){
                 if(response === 'success'){
                     alert("Success! Invoice Sent Successfully");
                 } else {
                     alert("Error! There is some problem to send invoice to customer");
                 }
                 
             }
         });
    }
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
                        $('#historyDetails').html(response);
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
    function load_comment_area(){
    
        document.getElementById("comment_section").style.display='block';
       // document.getElementById("comment").innerHTML=data;
        $('#commnet_btn').hide();
    }
    
     function load_update_area(data="", key){
    
    document.getElementById("update_section").style.display='block';
    document.getElementById("comment2").innerHTML=data;
    $('#comment_id').attr("value",key);
    $('#commnet_btn').hide();
    }
    
    function cancel(){
        getcommentbox();
    }   
    
    function addComment() {
        var prethis = $(this);
        var comment = $("#comment").val();
        var booking_id = '<?php echo $booking_history[0]['booking_id']?>';
  
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/booking/addComment',
             beforeSend: function(){
                
                 prethis.html('<i class="fa fa-circle-o-notch fa-lg" aria-hidden="true"></i>');
             },
            data: {comment: comment, booking_id: booking_id},
            success: function (response) { 
                if(response === "error"){
                    alert('There is some issue. Please refresh and try again');
                } else {
                    document.getElementById("commentbox").innerHTML = response;
                }   
            }
            
        });
    }
    
    function editComment(key){
       document.getElementById("comment_section").style.display='none';
       // document.getElementById("comment").innerHTML=data;
        $('#commnet_btn').hide();
        var comment = $("#comment_text_"+key).text();
        
        load_update_area(comment, key);
    }
    
    function updateComment() {
        var prethis = $(this);
        var comment = $("#comment2").val();
        var comment_id= $("#comment_id").val();
        var booking_id= '<?php echo $booking_history[0]['booking_id']?>';
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/booking/update_Comment',
             beforeSend: function(){
                
                 prethis.html('<i class="fa fa-circle-o-notch fa-lg" aria-hidden="true"></i>');
             },
            data: {comment: comment, comment_id: comment_id, booking_id: booking_id},
            success: function (response) {
                if(response === "error"){
                    alert('There is some issue. Please refresh and try again');
                } else {
                    document.getElementById("commentbox").innerHTML = response;
                } 
            }
            
        });
    }
    
    
     function deleteComment(comment_id) {
                
                
            var check = confirm("Do you want to delete this comment?");
            if(check == true){
                var comment_id = comment_id;
                var booking_id= '<?php echo $booking_history[0]['booking_id'] ?>';
                $.ajax({
                    type: 'POST',
                    url: '<?php echo base_url(); ?>employee/booking/deleteComment',
                    data: {comment_id: comment_id, booking_id:booking_id},
                    success: function (response) {
                        if(response === "error"){
                            alert('There is some issue. Please refresh and try again');
                        } else {
                            document.getElementById("commentbox").innerHTML = response;
                        } 
                    }
                    
                });
            }
        }
    
    
    
    
    function getcommentbox(){
    $.ajax({
        method: 'POST',
        data: {},
        url: '<?php echo base_url(); ?>employee/booking/get_comment_section/<?php echo $booking_history[0]['booking_id']?>',
        success: function (response) {
            document.getElementById("commentbox").innerHTML = response;
        }
    });
    }
    getcommentbox();
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
//         $.ajax({
//             method:'GET',
//             url:'<?php echo base_url(); ?>employee/dealers/get_dealer_data/<?php echo $booking_history[0]['dealer_id']?>',
//             success:function(response){
//                 obj = JSON.parse(response);
//                 console.log(obj);
//                 if(obj.msg){
//                    $('#dealer_name').html(obj.data[0].dealer_name);
//                    $('#dealer_phone_number').html(obj.data[0].dealer_phone_number_1);
//                 }else{
//                     $('#dealer_details').hide();
//                 }
//                 
//             }
//         });
    <?php } ?>
    $.ajax({
        method:'GET',
        url:'<?php echo base_url(); ?>employee/vendor/get_miscellaneous_charges/<?php echo $booking_history[0]['booking_id']?>/1/1',
        success:function(response){
            console.log(response);
            if(response === "Failed"){
               $("#misc_charge_loader").css('display','none');
            } else{
                $("#misc_charge_loader").css('display','none');
               $("#misc_charge_div").css('display', 'block');
               $("#misc_charge").html(response);
              
            }

        }
    });
</script>
<script>
    var spareID = 0;
    var spareFileColumn = "";
    $("table td").hover(function() {
    $(this).children(".edit").show();
    $(this).children(".serial_no_edit").show();
}, function() {
   // $(this).children(".edit").hide();
});

$(".edit").click(function() {
    if ($(this).siblings(".text").is(":hidden")) {
        var prethis = $(this);
        var address = $(this).siblings("input").val();
        $(this).siblings(".text").text($(this).siblings("input").val());
        
        $.ajax({
            url: "<?php echo base_url() ?>employee/booking/update_booking_address",
            type: "POST",
            beforeSend: function(){
                
                 prethis.html('<i class="fa fa-circle-o-notch fa-lg" aria-hidden="true"></i>');
             },
            data: { address: address, booking_id: '<?php echo $booking_history[0]['booking_id'];?>'},
            success: function (data) {
                if(data === "Success"){
                    
                    prethis.siblings("input").remove();
                    prethis.siblings(".text").show();
                    prethis.html('<i class="fa fa-pencil fa-lg" aria-hidden="true"></i>');
                } else {
                    alert("There is a problem to update");
                    alert(data);
                }
                
            }
        });
    }
    else {
        var text = $(this).siblings(".text").text();
        $(this).before("<input type=\"text\" class=\"form-control\" value=\"" + text + "\">");
        $(this).html('<i class="fa fa-check fa-lg" aria-hidden="true"></i>');
        $(this).siblings(".text").hide();
    }
});

function openfileDialog(spare_id, column_name) {
    spareID = spare_id;
    spareFileColumn = column_name;
    $("#fileLoader").click();
}

function upload_supporting_file(){
    $("#supportfileLoader").click();
}

function uploadsupportingfile(){
     var file = $('#supportfileLoader').val();
     if (file === '') {
        alert('Please select file');
        return;
    } else {
        var formData = new FormData();
        formData.append('support_file', $('#supportfileLoader')[0].files[0]);
        formData.append('booking_id', '<?php echo $booking_history[0]['booking_id'];?>');
        
        $.ajax({
                url: '<?php echo base_url();?>employee/booking/upload_order_supporting_file',
                data: formData,
                processData: false,
                contentType: false,
                type: 'POST',
                // this part is progress bar
                xhr: function () {
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function (evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = evt.loaded / evt.total;
                            percentComplete = parseInt(percentComplete * 100);
                            
                            $('#myprogress_supproting_file').text(percentComplete + '%');
                            $('#myprogress_supproting_file').css('width', percentComplete + '%');
                        }
                    }, false);
                    return xhr;
                },
                success: function (response) {
                    $('#myprogress_supproting_file').css('width', '0%');
                    obj = JSON.parse(response);
                    
                    if(obj.code === "success"){
                        $("#a_order_support_file").attr("href", "<?php echo S3_WEBSITE_URL;?>misc-images/" + obj.name);
                        $("#m_order_support_file").attr("src", "<?php echo S3_WEBSITE_URL;?>misc-images/" + obj.name);
                    } else {
                        alert(obj.message);
                    }
                }
            });
    }
}

function uploadfile(){
    var flag = true;
    var file = $('#fileLoader').val();
    
    if (file === '') {
        alert('Please select file');
        flag = false;
        return;
    }
    if(spareID === 0){
        alert('Please refresh page and try again');
        flag = false;
         return;
    }
    if(spareFileColumn === ""){
        alert('Please refresh page and try again');
        flag = false;
        return;
    }
    
    
        if(flag === true){
            var formData = new FormData();
            formData.append('file', $('#fileLoader')[0].files[0]);
            formData.append('spareID', spareID);
            formData.append('spareColumn', spareFileColumn);
            formData.append('booking_id', '<?php echo $booking_history[0]['booking_id'];?>');
            
            $.ajax({
                url: '<?php echo base_url();?>employee/inventory/processUploadSpareItem',
                data: formData,
                processData: false,
                contentType: false,
                type: 'POST',
                // this part is progress bar
                xhr: function () {
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function (evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = evt.loaded / evt.total;
                            percentComplete = parseInt(percentComplete * 100);
                            console.log('#myprogress' + spareFileColumn + spareID);
                            $('#myprogress' + spareFileColumn + spareID).text(percentComplete + '%');
                            $('#myprogress' + spareFileColumn + spareID).css('width', percentComplete + '%');
                        }
                    }, false);
                    return xhr;
                },
                success: function (response) {
                    $('#myprogress' + spareFileColumn + spareID).css('width', '0%');
                    obj = JSON.parse(response);
                    
                    if(obj.code === "success"){
                        $("#a_"+ spareFileColumn +"_" + spareID).attr("href", "<?php echo S3_WEBSITE_URL;?>misc-images/" + obj.name);
                        spareID = 0;
                        spareFileColumn = "";
                    } else {
                        alert(obj.message);
                    }
                }
            });
        }
}

$(".serial_no_edit").click(function() {
    if ($(this).siblings(".serial_no_text").is(":hidden")) {
        var prethis = $(this);
        var text_id = $(this).siblings(".serial_no_text").attr('id');
        var split = text_id.split('|');
        var line_item_id = split[0];
        var column = split[1];
        var data_value = $(this).siblings("input").val();
        $(this).siblings(".serial_no_text").text($(this).siblings("input").val());
        
        $.ajax({
            url: "<?php echo base_url() ?>employee/inventory/update_spare_parts_column",
            type: "POST",
            beforeSend: function(){
                
                 prethis.html('<i class="fa fa-circle-o-notch fa-lg" aria-hidden="true"></i>');
             },
            data: { data: data_value, id: line_item_id, column:column},
            success: function (data) {
                if(data === "Success"){
                    
                    prethis.siblings("input").remove();
                    prethis.siblings(".serial_no_text").show();
                    prethis.html('<i class="fa fa-pencil fa-lg" aria-hidden="true"></i>');
                } else {
                    alert("There is a problem to update");
                    alert(data);
                }
                
            }
        });
    }
    else {
        var text = $(this).siblings(".serial_no_text").text();
        $(this).before("<input type=\"text\" class=\"form-control\" value=\"" + text + "\">");
        $(this).html('<i class="fa fa-check fa-lg" aria-hidden="true"></i>');
        $(this).siblings(".serial_no_text").hide();
    }
});

</script>
<style>
    .edit
{
    display: none;
    margin-left: 10px;
}
.serial_no_edit{
    display: none;
    margin-left: 10px;
}
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

    <script>
    function removeMiscitem(id, booking_id){
        $.ajax({
           type: 'POST',
           url: '<?php echo base_url(); ?>/employee/service_centre_charges/cancel_misc_charges/'+id +"/"+booking_id,
           data: {},
           success: function (data) {
              if(data === "success"){
                  alert("Charges Removed");
                  location.reload(true);
              } else {
                  alert("Please refresh and tyy again");
              }

           }
    });
}


    function get_awb_details(courier_code,awb_number,status){
        if(courier_code && awb_number && status){
            $('#awb_loader').show();
            $.ajax({
                method:"POST",
                url:'<?php echo base_url(); ?>courier_tracking/get_awb_real_time_tracking_details/' + courier_code + '/' + awb_number + '/' + status,
                success: function(res){
                    $('#awb_loader').hide();
                    $('#gen_model_title').html('<h3> AWB Number : ' + awb_number + '</h3>');
                    $('#gen_model_body').html(res);
                    $('#gen_model').modal('toggle');
                }
            });
        }else{
            alert('Something Wrong. Please Refresh Page...');
        }
    }
    </script>
