<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false&libraries=places&key=AIzaSyB4pxS4j-_NBuxwcSwSFJ2ZFU-7uep1hKc"></script>
<script src="<?php echo base_url();?>js/googleScript.js"></script> 
<style type="text/css">
    th,td{
    border: 1px #f2f2f2 solid;
    vertical-align: center;
    padding: 1px;
    }
    tr:nth-child(even) {background-color: #f2f2f2}
</style>
<a href="https://s3.amazonaws.com/bookings-collateral/jobcards-pdf/<?php echo $booking_history[0]['booking_jobcard_filename']; ?> " class='btn btn-md btn-warning  pull-right' download style="margin-right: 40px;margin-top:15px;margin-bottom: 10px;"><i class="fa fa-download" aria-hidden="true"></i></a>
<div class="btn-pref btn-group btn-group-justified btn-group-lg" role="group" aria-label="...">
    <div class="btn-group" role="group">
        <button type="button" id="stars" class="btn btn-primary" href="#tab1" data-toggle="tab">
            <div class="hidden-xs">Booking Details</div>
        </button>
    </div>
    <div class="btn-group" role="group">
        <button type="button" id="favorites" class="btn btn-default" href="#tab2" data-toggle="tab">
            <div class="hidden-xs">Appliance Details</div>
        </button>
    </div>
    <div class="btn-group" role="group">
        <button type="button" id="following" class="btn btn-default" href="#tab3" data-toggle="tab">
            <div class="hidden-xs">Spare Parts</div>
        </button>
    </div>
    <div class="btn-group" role="group">
        <button type="button" id="following" class="btn btn-default" href="#tab4" data-toggle="tab">
            <div class="hidden-xs">Booking History</div>
        </button>
    </div>
    <div class="btn-group" role="group">
        <button type="button" id="following" class="btn btn-default" href="#tab5" data-toggle="tab">
            <div class="hidden-xs">SMS History</div>
        </button>
    </div>
    <div class="btn-group" role="group">
        <button type="button" id="following" class="btn btn-default" href="#tab7" data-toggle="tab">
            <div class="hidden-xs">Penalty</div>
        </button>
    </div>
    <?php if($this->session->userdata('is_engineer_app') == 1){ ?>
    <div class="btn-group" role="group">
        <button type="button" id="following" class="btn btn-default" href="#tab6" data-toggle="tab">
            <div class="hidden-xs">Engineer Action</div>
        </button>
    </div>
    <?php } ?>
</div>
<div class="well">
    <div class="tab-content">
        <div class="tab-pane fade in active" id="tab1">
            <div class="row">
                <div class="col-md-12">
                   
                        <table class="table  table-striped table-bordered" >
                            <tr>
                                <th >Name </th>
                                <td><?php echo $booking_history[0]['name']; ?></td>
                                  <th>Mobile </th>
                                <td><?php echo $booking_history[0]['booking_primary_contact_no'];
                                    if (!empty($booking_history[0]['booking_alternate_contact_no'])) {
                                        echo " / " . $booking_history[0]['booking_alternate_contact_no'];
                                    } ?></td>
                            </tr>
                            
                            <tr>
                                <th >Booking ID </th>
                                <td><?php echo $booking_history[0]['booking_id']; ?></td>
                               <th>Amount Due </th>
                                <td><?php echo $booking_history[0]['amount_due']; ?></td>
                            </tr>
                          
                            <tr>
                                 <th>Booking date </th>
                                <td><?php echo $booking_history[0]['booking_date']; ?></td>
                                <th>Timeslot </th>
                                <td><?php echo $booking_history[0]['booking_timeslot']; ?></td>
                                
                            </tr>
                           
                            <tr>
                                <th>Customer Paid Through Paytm</th>
                                <td><?php if(isset($booking_history[0]['onlinePaymentAmount'])){ echo $booking_history[0]["onlinePaymentAmount"];}?></td>
                                <th>Amount Paid </th>
                                <td><?php echo $booking_history[0]['amount_paid']; ?></td>
                                
                            </tr>
                            
                            <tr>
                                 <th>Address </th>
                                <td><?php echo $booking_history[0]['booking_address']; ?></td>
                                <th>City </th>
                                <td><?php echo $booking_history[0]['city']; ?></td>
                                 
                            </tr>
                       
                            <tr>
                                <th>Pincode </th>
                                <td><?php echo $booking_history[0]['booking_pincode']; ?></td>
                                <th>State </th>
                                <td><?php echo $booking_history[0]['state']; ?></td>
                                
                            </tr>
                            
                            <tr>
                                <th>Status </th>
                                <td><?php echo $booking_history[0]['current_status']." / ". $booking_history[0]['internal_status']; ?></td>
                                <th>Rescheduled Reason </th>
                                <td><?php echo $booking_history[0]['reschedule_reason']; ?></td>
                            </tr>
                           
                            <tr>
                                <th>Cancellation Reason </th>
                                <td><?php echo $booking_history[0]['cancellation_reason']; ?></td>
                                  <th>Closed date </th>
                                <td><?php echo $booking_history[0]['closed_date']; ?></td>
                            </tr>
                           
                            <tr>
                                <th>Closing Remarks </th>
                                <td><?php echo $booking_history[0]['closing_remarks']; ?></td>
                                 <th>Rating </th>
                                <td><?php if (!empty($booking_history[0]['rating_stars'])) {
                                    echo $booking_history[0]['rating_stars'] . "/5";
                                    } ?></td>
                            </tr>
                            
                            <tr>
                                <th>Rating Comment </th>
                                <td><?php if (!empty($booking_history[0]['rating_comments'])) {
                                    echo $booking_history[0]['rating_comments'];
                                    } ?></td>
                                <th>Remarks </th>
                                <td><?php echo $booking_history[0]['booking_remarks']; ?></td>
                            </tr>
                            <?php if($booking_history[0]['is_upcountry'] ==  1){ ?>
                            <tr>
                                <th colspan="1">Upcountry</th>
                                <td colspan="3">
                                    <p>Municipal Limit: <?php echo $this->session->userdata('municipal_limit')." KM"; ?></p>
                                    <div class="col-md-12">
                                        <div class="col-md-4"> <input type="hidden" class="form-control" id="txtSource" value="<?php echo 
                                            $booking_history[0]['booking_pincode'].", india"; ?>"></div>
                                        <div class="col-md-4">   <input type="hidden" class="form-control" id="txtDestination" value="<?php if(isset($dhq[0]['pincode'])){
                                        echo $dhq[0]['pincode'].", India";}?>"></div>
<!--                                        <div class="col-md-4"> <button class="btn btn-success" onclick="GetRoute()">Get Route</button></div>-->
                                             
                                    </div>
                                    <div class="col-md-12"> 
                                        <div id="dvDistance" style="display:none"></div>
                                         <br/>
                                        <div id="dvMap" style=" height: 200px">
                                    </div>
                                   
                                </td>
                            </tr>
                            <?php } ?>
                        </table>
                    </div>
                </div>
            </div>
      
        <div class="tab-pane fade in" id="tab2">
            <?php if (!empty($unit_details)) { ?>
            <table class="table  table-striped table-bordered">
                <tr>
                    <th>Brand</th>
                    <th>Category</th>
                    <th>Capacity</th>
                    <th>Model Number</th>
                    <th>SF Model Number</th>
                    <th>Serial Number</th>
                    <th>Description</th>
                    <th>Call Type</th>
                    <?php if ($booking_history[0]['current_status'] != "Completed") { ?>
                    <?php if ($booking_history[0]['is_upcountry'] == 1) { ?>
                    <th>Upcountry Charges</th>
                    <?php } ?>
                    <th>Total Charges</th>
                    <?php } else { ?>
                    <th>Paid Service Charges</th>
                    <th>Paid Additional Charges</th>
                    <th>Paid Parts Cost</th>
                    <?php if ($booking_history[0]['is_upcountry'] == 1) { ?>
                    <th>Paid Upcountry Charges</th>
                    <?php } ?>
                    <th>Total Amount Paid</th>
                    <?php } ?>
                    <th>Booking Status</th>
                    <?php if ($booking_history[0]['current_status'] === "Completed") { ?>
                    <th>Cash Invoice ID</th>
                    <th>Foc Invoice ID</th>
                    <?php } ?>
                    <th>SF Earning</th>
                </tr>
                <tbody>
                    <?php foreach ($unit_details as $key => $unit_detail) { ?>
                    <tr>
                        <td><?php echo $unit_detail['appliance_brand'] ?></td>
                        <td><?php echo $unit_detail['appliance_category'] ?></td>
                        <td><?php echo $unit_detail['appliance_capacity'] ?></td>
                        <td><?php echo $unit_detail['model_number'] ?></td>
                        <td><?php echo $unit_detail['sf_model_number'] ?></td>
                        <td><?php if(!empty($unit_detail['serial_number_pic'])){?>
                             <a target="_blank" href="<?php echo S3_WEBSITE_URL;?>engineer-uploads/<?php echo $unit_detail['serial_number_pic'];?>"><?php echo $unit_detail['serial_number'];?></a>
                             <?php } else { echo $unit_detail['serial_number'];} ?> / <?php echo $unit_detail['partner_serial_number']?></td>
                        <td><?php echo $unit_detail['appliance_description'] ?></td>
                        <td><?php print_r($unit_detail['price_tags']); ?></td>

                        <?php if ($booking_history[0]['current_status'] != "Completed") { ?>
                        
                        <?php if ($booking_history[0]['is_upcountry'] == 1) { ?>
                        <td><?php if($key == 0) { if ($booking_history[0]['upcountry_paid_by_customer'] == 0) {
                            echo "0";
                            } else {
                            echo $booking_history[0]['upcountry_distance'] * $booking_history[0]['partner_upcountry_rate'];
                        } }
                            ?>
                        </td>
                        <?php } ?>
                        <td><?php if ($booking_history[0]['upcountry_paid_by_customer'] == 0) {
                            echo $unit_detail['customer_net_payable'];
                            } else {
                            echo ($booking_history[0]['upcountry_distance'] * $booking_history[0]['partner_upcountry_rate']) + $unit_detail['customer_net_payable'];
                            }
                                    ?></td>
                        <?php } else { ?>
                       
                        <td><?php print_r($unit_detail['customer_paid_basic_charges']); ?></td>
                        <td><?php print_r($unit_detail['customer_paid_extra_charges']); ?></td>
                        <td><?php print_r($unit_detail['customer_paid_parts']); ?></td>
                        <?php if ($booking_history[0]['is_upcountry'] == 1) { ?>
                        <td><?php echo $booking_history[0]['customer_paid_upcountry_charges']; ?></td>
                        <?php } ?>
                        <td><?php
                            if ($booking_history[0]['upcountry_paid_by_customer'] == 0) {
                                echo ($unit_detail['customer_paid_basic_charges'] + $unit_detail['customer_paid_extra_charges'] + $unit_detail['customer_paid_parts']);
                            } else {
                                echo ($unit_detail['customer_paid_basic_charges'] + $unit_detail['customer_paid_extra_charges'] + $unit_detail['customer_paid_parts'] + $booking_history[0]['customer_paid_upcountry_charges']);
                            }
                            ?>
                        </td>
                        <?php } ?>
                        <td><?php print_r($unit_detail['booking_status']); ?></td>
                        <?php if ($booking_history[0]['current_status'] === "Completed") { ?>
                        <td><?php print_r($unit_detail['vendor_cash_invoice_id']); ?></td>
                        <td><?php print_r($unit_detail['vendor_foc_invoice_id']); ?></td>
                        
                        <?php } ?>
                        <td>
                            <?php echo round($unit_detail['vendor_basic_charges'] + $unit_detail['vendor_st_or_vat_basic_charges'] + 
                                                $unit_detail['vendor_extra_charges']  +  $unit_detail['vendor_st_extra_charges']  + 
                                                 $unit_detail['vendor_parts']  + $unit_detail['vendor_st_parts'], 2);?>
                            
                        </td>
                       
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <div class="row">
                <center><img id="misc_charge_loader" style="width: 50px;" src="<?php echo base_url(); ?>images/loader.gif"/></center>
                 <div class="col-md-12" id="misc_charge_div" >
                    <h1 style='font-size:24px;margin-top: 40px;'>Miscellaneous Charge</h1>

                    <div id="misc_charge">

                    </div>
                </div>
            </div>
            <div style="margin-top:20px;" id="sf_payout"></div>
            <?php }else{?> 
            <div class="text-danger">No Data Found</div>
            <?php }?>
            
        </div>
        <div class="tab-pane fade in" id="tab3">
            <?php if (isset($booking_history['spare_parts'])) { $estimate_given = false; $parts_shipped = false; $defective_parts_shipped = FALSE; ?>
          
                <h1 style='font-size:24px;'>Spare Parts Requested By SF</h1>
                
                    <table class="table  table-striped table-bordered" >
                        <thead>
                            <tr>
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
                                <th>Current Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($booking_history['spare_parts'] as $sp) { ?>
                            <tr>
                                <td><?php echo $sp['model_number']; ?></td>
                                <td><?php echo $sp['parts_requested']; ?></td>
                                <td><?php echo $sp['create_date']; ?></td>
                                <td><?php if (!is_null($sp['invoice_pic'])) {
                                    if ($sp['invoice_pic'] != '0') { ?> <a href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY; ?>/misc-images/<?php echo $sp['invoice_pic']; ?> " target="_blank">Click Here</a><?php }
                                    } ?>
                                </td>
                                <td><?php if (!is_null($sp['serial_number_pic'])) {
                                    if ($sp['serial_number_pic'] !== '0') { ?> <a href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY; ?>/misc-images/<?php echo $sp['serial_number_pic']; ?> " target="_blank">Click Here</a><?php }
                                    } ?>
                                </td>
                                <td><?php if (!is_null($sp['defective_parts_pic'])) {
                                    if ($sp['defective_parts_pic'] !== '0') { ?> <a href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY; ?>/misc-images/<?php echo $sp['defective_parts_pic']; ?> " target="_blank">Click Here</a><?php }
                                    } ?>
                                </td>
                                <td><?php if (!is_null($sp['defective_back_parts_pic'])) {
                                    if ($sp['defective_back_parts_pic'] !== '0') { ?> <a href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY; ?>/misc-images/<?php echo $sp['defective_back_parts_pic']; ?> " target="_blank">Click Here</a><?php }
                                    } ?>
                                </td>
                                <td><?php echo $sp['serial_number']; ?></td>
                                <td><?php echo $sp['acknowledge_date']; ?></td>
                                <td><?php echo $sp['remarks_by_sc']; ?></td>
                                <td><?php echo $sp['status'];?></td>
                            </tr>
                            <?php if(!is_null($sp['parts_shipped'])){ $parts_shipped = true;} if(!empty($sp['defective_part_shipped'])){
                                $defective_parts_shipped = TRUE;
                                } if($sp['purchase_price'] > 0){ $estimate_given = TRUE; }  } ?>
                        </tbody>
                    </table>
                
            <?php 
                if($estimate_given){ ?>
                        

                        <h1 style='font-size:24px;'>Estimate Given</h1>
                        <div class="col-md-12" style="padding-left:1px;">
                            <table class="table  table-striped table-bordered" >
                                <thead>
                                    <tr>
                                    <th >Estimate Given</th>
                                    <th >Estimate Given Date </th>
                                    <th >Status </th>
                                </tr>
                                </thead>

                                <tbody>
                                     <?php foreach ($booking_history['spare_parts'] as $sp){ if($sp['purchase_price'] > 0) { ?>
                                <tr>
                                   
                                    <td><?php echo $sp['sell_price']; ?></td>
                                    <td><?php if(!empty($sp['estimate_cost_given_date'])){ echo date("d-m-Y", strtotime($sp['estimate_cost_given_date'])); } ?></td>
                                    <td><?php echo $sp['status']; ?></td>
                                </tr>
                                     <?php  } } ?>
                                </tbody>
                            </table>
                        </div>
                   
                   <?php  }
                    
                    ?>
            <?php if ($parts_shipped) { ?>
          
                <h1 style='font-size:24px;'>Spare Parts Shipped</h1>
               
                    <table class="table  table-striped table-bordered" >
                        <thead>
                            <tr>
                                <th >Shipped Parts </th>
                                <th >Courier Name</th>
                                <th >AWB </th>
                                <th >Shipped date </th>
                                <th >EDD </th>
                                <th >Remarks By Partner</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($booking_history['spare_parts'] as $sp) { if(!empty($sp['parts_shipped'])) { ?>
                            <tr>
                                <td><?php echo $sp['parts_shipped']; ?></td>
                                <td><?php echo $sp['courier_name_by_partner']; ?></td>
                                <td><?php echo $sp['awb_by_partner']; ?></td>
                                <td><?php echo $sp['shipped_date']; ?></td>
                                <td><?php echo $sp['edd']; ?></td>
                                <td><?php echo $sp['remarks_by_partner']; ?></td>
                            </tr>
                            <?php }  } ?>
                        </tbody>
                    </table>
                
          
            <?php } if ($defective_parts_shipped) { ?>
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
                                <th >Shipped date </th>
                                <th >Remarks By SF </th>
                                <th >Remarks By Partner </th>
                                <th>Courier Invoice</th>
                                <th>Challan file</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($booking_history['spare_parts'] as $sp) { if(!empty($sp['defective_part_shipped'])){ ?>
                            <tr>
                                <td><?php echo $sp['defective_part_shipped']; ?></td>
                                <td><?php echo $sp['courier_name_by_sf']; ?></td>
                                <td><?php echo $sp['awb_by_sf']; ?></td>
                                <td><?php echo $sp['courier_charges_by_sf']; ?></td>
                                <td><?php echo date('Y-m-d', strtotime($sp['defective_part_shipped_date'])); ?></td>
                                <td><?php echo $sp['remarks_defective_part_by_sf']; ?></td>
                                <td><?php echo $sp['remarks_defective_part_by_partner']; ?></td>
                                <td><a href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY; ?>/misc-images/<?php echo $sp['defective_courier_receipt']; ?> " target="_blank">Click Here to view</a></td>
                                <td>
                                    <?php if(!empty($sp['sf_challan_file'])) { ?>
                                        <a href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY?>/vendor-partner-docs/<?php echo $sp['sf_challan_file']; ?>" target="_blank">Click Here to view</a>
                            <?php } ?>
                                </td>
                            </tr>
                            <?php } }?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php } ?>
            <?php } else{ ?> 
            <div class="text-danger">Spare Part Not Requested</div>
            <?php } ?>
        </div>
        <div class="tab-pane fade in" id="tab4">
            <?php if (isset($booking_state_change_data)) { ?>
            <table class="table  table-striped table-bordered" >
                <thead>
                    <tr>
                        <th class="jumbotron" style="text-align: center">S.N</th>
                        <th class="jumbotron" style="text-align: center">Old State</th>
                        <th class="jumbotron" style="text-align: center">New State</th>
                        <th class="jumbotron" style="text-align: center">Remarks</th>
                        <th class="jumbotron" style="text-align: center">Agent</th>
                        <th class="jumbotron" style="text-align: center">Partner</th>
                        <th class="jumbotron" style="text-align: center">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $key = 1;
                        foreach ($booking_state_change_data as $value) { ?>
                    <tr>
                        <td><?php echo ($key++) . '.'; ?></td>
                        <td><?php echo $value['old_state']; ?></td>
                        <td><?php echo $value['new_state']; ?></td>
                        <td><?php echo $value['remarks']; ?></td>
                        <td><?php echo $value['full_name']; ?></td>
                        <td><?php
                            if ($value['source'] == "Website") {
                                echo '247 Around';
                            } else {
                                echo $value['source'];
                            }
                                    ?></td>
                        <td><?php
                            $old_date = $value['create_date'];
                            $old_date_timestamp = strtotime($old_date);
                            $new_date = date('j F, Y g:i A', $old_date_timestamp);
                            echo $new_date;
                            ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php } else { ?> 
            <div class="text-danger">No Data Found</div>
            <?php } ?>
        </div>
        <div class="tab-pane fade in" id="tab5">
            <?php if (isset($sms_sent_details) && !empty($sms_sent_details)) { ?>
            <table class="table  table-striped table-bordered" >
                <thead>
                    <tr>
                        <th class="jumbotron" style="text-align: center;width: 1%">S.N</th>
                        <th class="jumbotron" style="text-align: center">Phone</th>
                        <th class="jumbotron" style="text-align: center;width:45%;">Content</th>
                        <th class="jumbotron" style="text-align: center">Sent on Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $key = 1;
                        foreach ($sms_sent_details as $value) { ?>
                    <tr>
                        <td><?php echo ($key++) . '.'; ?></td>
                        <td><?php echo $value['phone']; ?></td>
                        <td style="font-size: 90%;"><?php echo $value['content']; ?></td>
                        <td><?php
                            $old_date = $value['created_on'];
                            $old_date_timestamp = strtotime($old_date);
                            $new_date = date('j F, Y g:i A', $old_date_timestamp);
                            echo $new_date;
                            ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php } else { ?> 
            <div class="text-danger">No Data Found</div>
            <?php } ?>
        </div>

            <?php if($this->session->userdata('is_engineer_app') == 1){ ?>
            <div class="tab-pane fade in" id="tab6">
                <?php if($engineer_action_not_exit) { ?>

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
                <?php if(isset($signature_details)){ ?>
                <table class="table  table-striped table-bordered">
                    <tr>
                        <th>Amount Paid</th>
                        <th>Customer Signature</th>
                        <th>Closed Date</th>
                        <th>Closing Address</th>
                        <th>Remarks</th>
                    </tr>
                    <tbody>

                        <?php if(!empty($signature_details)){ ?>
                        <tr>
                            

                            <td><?php echo $signature_details[0]['amount_paid']; ?></td>
                            <td><a href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY;?>/engineer-uploads/<?php echo $signature_details[0]['signature'];?>" target="_blank">Click Here</a></td>
                            <td><?php echo $signature_details[0]['closed_date']; ?></td>
                            <td><?php echo $signature_details[0]['address']; ?></td>
                            <td><?php echo $signature_details[0]['remarks']; ?></td>
                            
                        </tr>

                        <?php } ?>

                    
                    </tbody>
                </table>
                <?php }?>
                <?php } else {
                    echo "Engineer Action Not Found";
                } ?>
            </div>
            <?php } ?>
            
            <div class="tab-pane fade in" id="tab7">
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
    </div>
</div>
<style type="text/css">
    .spare_image {
    width: 350px;;
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
<script>
    <?php if($booking_history[0]['is_upcountry'] == 1){  ?>  
             setTimeout(function(){ GetRoute(); }, 1000);
    <?php } ?>
    $(document).ready(function () {
        $(".btn-pref .btn").click(function () {
            $(".btn-pref .btn").removeClass("btn-primary").addClass("btn-default");
            // $(".tab").addClass("active"); // instead of this do the below 
            $(this).removeClass("btn-default").addClass("btn-primary");
        });
        

        $.ajax({
          type: 'POST',
          url: '<?php echo base_url(); ?>employee/service_centers/get_sf_payout/<?php echo $booking_history[0]['booking_id']; ?>/<?php echo $booking_history[0]['assigned_vendor_id'];?>/<?php echo $booking_history[0]['amount_due'];?>',
          success: function (data) {
             console.log(data);
             $("#sf_payout").html(data);
          }
        });
        
        $.ajax({
            method:'GET',
            url:'<?php echo base_url(); ?>employee/vendor/get_miscellaneous_charges/<?php echo $booking_history[0]['booking_id']?>/1/0',
            success:function(response){
                
                if(response === "Failed"){
                   $("#misc_charge_loader").css('display','none');
                } else{
                   $("#misc_charge_loader").css('display','none');
                   $("#misc_charge_div").css('display', 'block');
                   $("#misc_charge").html(response);

                }

            }
        });
    
    });
</script>
