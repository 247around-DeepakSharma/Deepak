<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false&libraries=places&key=<?php echo GOOGLE_MAPS_API_KEY;?>"></script>
<script src="<?php echo base_url();?>js/googleScript.js"></script> 
<style type="text/css">
    th,td{
    border: 1px #f2f2f2 solid;
    vertical-align: center;
    padding: 1px;
    }
    tr:nth-child(even) {background-color: #f2f2f2}
</style>
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
    <?php if(isset($saas_module) && !$saas_module) { ?>
    <div class="btn-group" role="group">
        <button type="button" id="following" class="btn btn-default" href="#tab8" data-toggle="tab">
            <div class="hidden-xs">Transactions</div>
        </button>
    </div>
    <?php } if($this->session->userdata('is_engineer_app') == 1){ ?>
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
                                <th >Name</th>
                                <td><?php echo $booking_history[0]['name']; ?></td>
                                  <th>Mobile </th>
                                <td><?php echo $booking_history[0]['booking_primary_contact_no'];
                                    if (!empty($booking_history[0]['booking_alternate_contact_no'])) {
                                        echo " / " . $booking_history[0]['booking_alternate_contact_no'];
                                    } ?></td>
                            </tr>
                            
                            <tr>
                                <th >Booking ID </th>
                                <td><?php echo $booking_history[0]['booking_id']; ?> <a href='<?php echo "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/jobcards-pdf/".$booking_history[0]['booking_jobcard_filename']; ?>' class='btn btn-md btn-warning  pull-right' download ><i class="fa fa-download" aria-hidden="true"></i></a></td>
                               <th>Amount Due </th>
                                <td><?php echo $booking_history[0]['amount_due']; ?></td>
                            </tr>
                          
                            <tr>
                                 <th>Booking date/Timeslot </th>
                                <td><?php echo $booking_history[0]['booking_date']."/".$booking_history[0]['booking_timeslot']; ?></td>
                                <th> Closed Date </th>
                                <td><?php echo $booking_history[0]['service_center_closed_date']; ?></td>
                                
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
                                <td><?php echo $booking_history[0]['service_center_closed_date']; ?></td>
                            </tr>
                            <tr>
                                <th>Symptom (Booking Creation Time)</th>
                                <td><?php if(!empty($symptom)){ echo $symptom[0]['symptom'];};?>
                                </td>
                                <th >Symptom (Booking Completion Time)</th>
                                <td><?php if(!empty($completion_symptom)) { echo $completion_symptom[0]['symptom']; } ;?>
                                </td>
                            </tr>
                            <tr>
                                <th>Defect</th>
                                <td ><?php if(!empty($technical_defect)) { echo $technical_defect[0]['defect']; }?></td>
                                <th>Solution</th>
                                <td ><?php if(!empty($technical_solution)) { echo $technical_solution[0]['technical_solution']; }?></td>
                            </tr>
                            <tr>
                                <th>Remarks</th>
                                <td><?php echo $booking_history[0]['booking_remarks']; ?></td>
                                <th>Closing Remarks </th>
                                <td><?php echo $booking_history[0]['closing_remarks']; ?></td>
                            </tr>
                            <tr>
                                <th>Repeat Reason </th>
                                <td ><?php if (!empty($booking_history[0]['repeat_reason'])) {
                                    echo $booking_history[0]['repeat_reason'];
                                    } ?></td>
                                <th>Rating Comment </th>
                                
                                <td><?php if (!empty($booking_history[0]['rating_comments'])) {
                                    echo $booking_history[0]['rating_comments'];
                                    } ?>
                                </td>
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
                            <tr>
                                <th>Rating </th>
                                <td><?php if (!empty($booking_history[0]['rating_stars'])) {
                                    echo $booking_history[0]['rating_stars'] . "/5";
                                    } ?></td>
                                <th>Engineer Name </th>
                                <td><?php if (isset($booking_history[0]['assigned_engineer_name'])) {
                                    echo $booking_history[0]['assigned_engineer_name'] . "/5";
                                    } ?>
                                </td>
                            </tr> 
                        </table>
                    <table class="table  table-striped table-bordered cloned" >
                        <tr>
                            <th colspan="2" style="font-size: 16px; color: #2c9d9c;">
                                Support Files
                                <?php if(isset($booking_files) && !empty($booking_files)) { ?>
                                <button class="btn btn-sm btn-primary" id="btn_addSupportFile" style="float:right;margin-right:5px;">Add Support File</button>
                                <?php } ?>
                            </th>
                        </tr>
                        <tr>
                            <th style="width: 50%;">File Type </th>
                            <th style="width: 50%;">File</th>
                        </tr>
                        <?php $count=0;
                        if(isset($booking_files) && !empty($booking_files)) {
                            $count = count($booking_files);
                        foreach($booking_files as $key => $files) { ?>
                        <tr class="uploaded_support_file">
                            <td style="width: 50%;"><?php if(isset($files['file_description'])) echo $files['file_description']; ?></td>
                            <td style="width: 50%;">
                                <input type="file" id="supportfileLoader_<?=$key?>" name="files" onchange="uploadsupportingfile(this.id,'<?=$files['id']?>')" style="display:none" />
                                <div class="progress-bar progress-bar-success myprogress" id="<?php echo "myprogress_supproting_file_".$key;?>"  role="progressbar" style="width:0%">0%</div>
                                <?php $src = base_url() . 'images/no_image.png';
                                $image_src = $src;
                                if (isset($files['file_name']) && !empty($files['file_name'])) {
                                    //Path to be changed
                                    $src = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/misc-images/".$files['file_name'];
                                    $image_src = base_url().'images/view_image.png';
                                }
                                ?>
                                <a id="a_order_support_file_<?=$key?>" href="<?php  echo $src?>" target="_blank"><img id="m_order_support_file_<?=$key?>" src="<?php  echo $image_src ?>" width="35px" height="35px" style="border:1px solid black;margin-left:10px;" /></a>
                                
                            </td>
                        </tr>
                        <?php } } ?>
                        
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
                    <table class="table  table-striped table-bordered" id="relative_holder">
                        <tr>
                            <th colspan="3" style="font-size: 16px; color: #2c9d9c;">Booking Relatives</th>
                        </tr> 
                        <tr>
                            <th style="width: 25%;">Parent </th>
                            <th style="width: 21%;">Child</th>
                            <th style="width: 21%;">Siblings</th>
                        </tr>
                        <tr>
                            <td style="width: 23%;" id="parent_holder"><center><img  src="<?php echo base_url(); ?>images/loadring.gif" ></center></td>
                            <td style="width: 23%;" id="child_holder"><center><img  src="<?php echo base_url(); ?>images/loadring.gif" ></center></td>
                            <td style="width: 23%;" id="sibling_holder"><center><img  src="<?php echo base_url(); ?>images/loadring.gif" ></center></td>
                        </tr>
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
                             <a target="_blank" href="<?php echo S3_WEBSITE_URL;?><?php echo SERIAL_NUMBER_PIC_DIR;?>/<?php echo $unit_detail['serial_number_pic'];?>"><?php echo $unit_detail['serial_number'];?></a>
                             <?php } else { echo $unit_detail['serial_number'];} ?> / <?php echo $unit_detail['partner_serial_number']?></td>
                        <td><?php echo $unit_detail['appliance_description'] ?></td>
                        <td><?php print_r($unit_detail['price_tags']); ?></td>

                        <?php if ($booking_history[0]['current_status'] != "Completed") { ?>
                        
                        <?php if ($booking_history[0]['is_upcountry'] == 1) { ?>
                        <td><?php $up_charges = 0; if($key == 0) { if ($booking_history[0]['upcountry_paid_by_customer'] == 0) {
                            echo "0";
                            } else if($booking_history[0]['flat_upcountry'] == 1){
                                  $up_charges =  $booking_history[0]['upcountry_to_be_paid_by_customer'];
                                        echo $up_charges;
                        } else {
                            $up_charges =  $booking_history[0]['upcountry_distance'] * $booking_history[0]['partner_upcountry_rate'];
                             echo $up_charges;
                        } }
                            ?>
                        </td>
                        <?php } ?>
                        <td><?php if ($booking_history[0]['upcountry_paid_by_customer'] == 0) {
                            echo $unit_detail['customer_net_payable'];
                            } else {
                            echo ($up_charges) + $unit_detail['customer_net_payable'];
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
                                <th >Original Requested Parts </th>
                                <th >Final Requested Parts </th>
<!--                                <th > Requested Part Number </th>-->
                                <th >Requested Parts Type</th>
                                <th >Requested Quantity</th>
                                <th >Requested Date</th>
                                <th >Date Of Purchase</th>
                                <th >Invoice Image </th>
                                <th >Serial Number Image </th>
                                <th >Defective Front Part Image </th>
                                <th >Defective Back Part Image </th>
                                <th >Serial Number </th>
                                <th >Acknowledge Date BY SF </th>
                                <th >Remarks By SC </th>
                                <th>Current Status</th>
                                <th>Spare Cancellation Reason</th>
                                <th>Consumption</th>
                                <th>Consumption Reason</th>
                                <?php if($this->session->userdata("is_micro_wh") == 1){ ?>
                                <th>Remove MSL Consumption</th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($booking_history['spare_parts'] as $sp) { ?>
                            <tr>
                                <td><?php echo $sp['model_number']; ?></td>
                                <td style=" word-break: break-all;"><?php if(isset($sp['original_parts'])){ echo $sp['original_parts']."<br><br><b>".$sp['original_parts_number']."</b>"; } else { echo $sp['parts_requested'].(isset($sp['part_number']) ? ("<br><br><b>".$sp['part_number']."</b>") : ''); } ?></td>
                                <td style=" word-break: break-all;"><?php if(isset($sp['final_spare_parts'])){ echo $sp['final_spare_parts']."<br><br><b>".$sp['part_number']."</b>"; }  ?></td>
<!--                                <td style=" word-break: break-all;"><?php if(isset($sp['part_number'])){ echo $sp['part_number']; }  ?></td>-->
                                <td><?php echo $sp['parts_requested_type']; ?></td>
                                <td><?php echo $sp['quantity']; ?></td>
                                <td><?php echo date_format(date_create($sp['create_date']),'d-m-Y h:i:A'); ?></td>
                                <td><?php echo date_format(date_create($sp['date_of_purchase']),'d-m-Y'); ?></td>
                                <td><?php if (!is_null($sp['invoice_pic'])) {
                                    if ($sp['invoice_pic'] != '0') { ?> <a href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY; ?>/misc-images/<?php echo $sp['invoice_pic']; ?> " target="_blank">Click Here</a><?php }
                                    } ?>
                                </td>
                                <td><?php if (!is_null($sp['serial_number_pic'])) {
                                    if ($sp['serial_number_pic'] !== '0') { ?> <a href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY; ?>/<?php echo SERIAL_NUMBER_PIC_DIR;?>/<?php echo $sp['serial_number_pic']; ?> " target="_blank">Click Here</a><?php }
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
                                <td><?php echo $sp['part_cancel_reason'];?></td>
                                <td><?php if($sp['is_consumed'] == 1) { echo 'Yes';} else { echo 'No';} ?></td>
                                <td><?php echo $sp['consumed_status']; ?></td>
                                <?php if($this->session->userdata("is_micro_wh") == 1){ 
                                    if($sp['status'] == SPARE_DELIVERED_TO_SF && $sp['entity_type'] == _247AROUND_SF_STRING && $sp['partner_id'] == $this->session->userdata("service_center_id") && $sp['service_center_id'] == $this->session->userdata("service_center_id")){ ?>
                                <td><button class="btn btn-primary" onclick="open_model_for_remove_msl(<?php echo $sp['id']; ?>, '<?php echo $sp['booking_id'];  ?>', <?php echo $sp['shipped_inventory_id']; ?>)">Remove</button></td>
                                <?php }else{ ?>
                                    <td><button class="btn btn-primary" disabled>Remove</button></td>
                                <?php } ?>
                                <?php } ?>
                            </tr>
                            <?php if(!is_null($sp['parts_shipped'])){ $parts_shipped = true;} if(!empty($sp['defective_part_shipped'])){
                                $defective_parts_shipped = TRUE;
                                } if($sp['purchase_price'] > 0){ $estimate_given = TRUE; }  } ?>
                        </tbody>
                    </table>
<?php if(!empty($booking_history['spare_parts']) && !empty($booking_history['spare_parts'][0]['wrong_part_name'])) { ?>
                <div class="row">
                    <div class="col-md-12" >
                        <h1 style='font-size:24px;margin-top: 40px;'>Wrong Part Details</h1>
                        <div class="col-md-12" style="padding-left:1px;">
                            <table class="table  table-striped table-bordered" >
                                <thead>
                                    <th>S. No.</th>
                                    <th>Requested Part Name</th>
                                    <th>Wrong Part Name</th>
                                    <th>Wrong Part Remarks</th>
                                </thead>
                                <tbody>
                                    <?php foreach($booking_history['spare_parts'] as $kk => $spare_record) { ?>
                                    <tr>
                                        <td><?php echo $kk; ?></td>
                                        <td><?php echo $spare_record['parts_requested']; ?></td>
                                        <td><?php echo $spare_record['wrong_part_name']; ?></td>
                                        <td><?php echo $spare_record['wrong_part_remarks']; ?></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php } ?>                
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
                                <th>Shipped Parts </th>
                                <th>Shipped Parts Number </th>
                                <th>Shipped Quantity </th>
                                <th>Pickup Request </th>
                                <th>Pickup Schedule</th>
                                <th>Courier Name</th>
                                <th>AWB </th>
                                <th>Shipped date </th>
                                <th>EDD </th>
                                <th>Remarks By Partner</th>
                                <?php if($this->session->userdata('is_wh')) { ?> 
                                <th>Courier File</th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($booking_history['spare_parts'] as $sp) { if(!empty($sp['parts_shipped'])) { ?>
                            <tr>
                                <td><?php echo $sp['parts_shipped']; ?></td>
                                <td><?php if(!empty($sp['shipped_part_number'])){echo $sp['shipped_part_number'];}else{echo 'Not Available';}  ?></td>
                                <td><?php echo $sp['shipped_quantity']; ?></td>
                                <td style="word-break: break-all;"><?php if($sp['around_pickup_from_service_center'] == COURIER_PICKUP_REQUEST){    echo 'Pickup Requested';} ?></td>
                                <td style="word-break: break-all;"><?php if($sp['around_pickup_from_service_center'] == COURIER_PICKUP_SCHEDULE){    echo 'Pickup Schedule';} ?></td>
                                <td><?php echo ucwords(str_replace(array('-','_'), ' ', $sp['courier_name_by_partner'])); ?></td>
                                <td><a href="javascript:void(0)" onclick="get_awb_details('<?php echo $sp['courier_name_by_partner']; ?>','<?php echo $sp['awb_by_partner']; ?>','<?php echo $sp['status']; ?>','<?php echo "awb_loader_".$sp['awb_by_partner']; ?>')"><?php echo $sp['awb_by_partner']; ?></a> 
                                            <span id=<?php echo "awb_loader_".$sp['awb_by_partner'];?> style="display:none;"><i class="fa fa-spinner fa-spin"></i></span></td>
                                <td><?php echo $sp['shipped_date']; ?></td>
                                <td><?php echo $sp['edd']; ?></td>
                                <td><?php echo $sp['remarks_by_partner']; ?></td>
                                <?php if($this->session->userdata('is_wh')) { ?> 
                                    <td>
                                    <?php if(!empty($sp['courier_pic_by_partner'])){ ?> 
                                        <a href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY?>/vendor-partner-docs/<?php echo $sp['courier_pic_by_partner']; ?>" target="_blank">Click Here to view</a>
                                    <?php } ?>
                                    </td>
                                <?php } ?>
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
                                <th >Shipped Parts Number </th>
                                <th >Courier Name </th>
                                <th>AWB </th>
                                <th> No. Of Boxes </th>
                                <th> Weight</th>
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
                                <td><?php if(!empty($sp['shipped_part_number'])){echo $sp['shipped_part_number'];}else{ echo 'Not Available';}  ?></td>
                                <td><?php echo ucwords(str_replace(array('-','_'), ' ', $sp['courier_name_by_sf'])); ?></td>
                                 <?php
                                        $spareStatus = DELIVERED_SPARE_STATUS;
                                        if(!$sp['defactive_part_received_date_by_courier_api']){
                                            $spareStatus = $sp['status'];
                                        }
                                        ?>
                                <td><a href="javascript:void(0)" onclick="get_awb_details('<?php echo $sp['courier_name_by_sf']; ?>','<?php echo $sp['awb_by_sf']; ?>','<?php echo $spareStatus; ?>','<?php echo "awb_loader_".$sp['awb_by_sf']; ?>')"><?php echo $sp['awb_by_sf']; ?></a> 
                                            <span id=<?php echo "awb_loader_".$sp['awb_by_sf'];?> style="display:none;"><i class="fa fa-spinner fa-spin"></i></span></td>
                               
                                <td><?php if (!empty($sp['awb_by_sf']) && !empty($courier_boxes_weight_details['box_count'])) {
                                    echo $courier_boxes_weight_details['box_count'];
                                } ?></td>
                                <td><?php
                                        if (!empty($sp['awb_by_sf'])) {
                                            if (!empty($courier_boxes_weight_details['billable_weight'])) {
                                                $expl_data = explode('.', $courier_boxes_weight_details['billable_weight']);
                                                if (!empty($expl_data[0])) {
                                                    echo $expl_data[0] . ' KG ';
                                                }
                                                if (!empty($expl_data[1])) {
                                                    echo $expl_data[1] . ' Gram';
                                                }
                                            }
                                        }
                                   ?></td>
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
                        <td><?php echo ((isset($value['full_name'])) ? $value['full_name']:""); ?></td>
                        <td><?php
                            if ((isset($value['source'])) && ($value['source'] == _247AROUND_WEBSITE)) {
                                echo '247 Around';
                            } else {
                                echo ((isset($value['source'])) ? $value['source']:"");
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
                        <td><a href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY;?>/<?php echo SERIAL_NUMBER_PIC_DIR;?>/<?php echo $unit['en_serial_number_pic'];?>" target="_blank"><?php  echo $unit['en_serial_number']; ?></a></td>
                        
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
                                <th >Penalty Debit on Invoice</th>
                                <th >Penalty Credit On Invoice</th>
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
                                <td><?php echo $penalty[$key]['foc_invoice_id']; ?></td>
                                <td><?php echo $penalty[$key]['removed_penalty_invoice_id']; ?></td>
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
                                <td><?php echo $penalty[$key]['foc_invoice_id']; ?></td>
                                <td><?php echo $penalty[$key]['removed_penalty_invoice_id']; ?></td>
                            </tr>
                            <?php }?>
                            <?php }?>
                        </table>
                        <?php } else { echo "Penalty Not Found";?>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade in" id="tab8">
                <div class="row">
                    <div class="col-md-12">
                         <?php if($paytm_transaction) { ?>   
                        <h3>Paytm Transaction</h3>
                <table class="table  table-striped table-bordered">
                <thead>
                    <tr>
                        <th>S.N</th>
                        <th>Paid Amount</th>
                        <th>Txn ID</th>
                        <th>Transaction Date</th>
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
                            <?php
                        }?>
                <?php $index++;?>
                </tr>
                    <?php
                    }
                    ?>
                </tbody>
                </table>
                        </div>
                    </div>
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
    
    <div id="remove_msl_model" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" id="modal-title">Remove MSL Consumption</h4>
                </div>
                <div class="modal-body">
<!--                    <h4 style="padding: 3px;font-size: 1em;" id="status_label" class="modal-title">Cancellation Reason *</h4>
                    <select id="msl_remove_reason" class="form-control"></select>-->
                    <h4 style="padding: 3px;font-size: 1em; margin-top: 10px;" id="remarks_label" class="modal-title">Remarks *</h4>
                    <textarea rows="3" class="form-control" id="msl_remove_remark" placeholder="Enter Remarks"></textarea>
                    <input type="hidden" id="model_spare_id">
                    <input type="hidden" id="model_booking_id">
                    <input type="hidden" id="model_inventory_id">
                </div>
                <input type="hidden" id="url"></input>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="cancelAutoDeliveredPart()" id="reject_btn">Send</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
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
        get_booking_relatives();
        $(".btn-pref .btn").click(function () {
            $(".btn-pref .btn").removeClass("btn-primary").addClass("btn-default");
            // $(".tab").addClass("active"); // instead of this do the below 
            $(this).removeClass("btn-default").addClass("btn-primary");
        });
        

        $.ajax({
          type: 'POST',
          url: '<?php echo base_url(); ?>employee/service_centers/get_sf_payout/<?php echo $booking_history[0]['booking_id']; ?>/<?php echo $booking_history[0]['assigned_vendor_id'];?>/<?php echo $booking_history[0]['amount_due'];?>/<?php echo $booking_history[0]['flat_upcountry'];?>',
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
    
    function get_awb_details(courier_code,awb_number,status,id){
        if(courier_code && awb_number && status){
            $('#'+id).show();
            $.ajax({
                method:"POST",
                data : {courier_code: courier_code, awb_number: awb_number, status: status},
                url:'<?php echo base_url(); ?>courier_tracking/get_awb_real_time_tracking_details',
                success: function(res){
                    $('#'+id).hide();
                    $('#gen_model_title').html('<h3> AWB Number : ' + awb_number + '</h3>');
                    $('#gen_model_body').html(res);
                    $('#gen_model').modal('toggle');
                }
            });
        }else{
            alert('Something Wrong. Please Refresh Page...');
        }
    }
         function get_booking_relatives(){
            $.ajax({
                method:"POST",
                data : {},
                url:'<?php echo base_url(); ?>employee/partner/get_booking_relatives/<?php echo $booking_history[0]['booking_id']; ?>',
                success: function(res){
                    if(res){
                    var obj = JSON.parse(res);
                    parent_string = child_string = sibling_string = "NULL";
                    if(obj.parent){
                        parent_string = "<a href = '<?php echo base_url(); ?>service_center/booking_details/"+encodeURIComponent(window.btoa(obj.parent))+"' target = '_blank'>"+obj.parent+"</a>";
                    }
                    if(obj.siblings){
                        sibling_string ="";
                        sibling_array = obj.siblings.split(",");
                        for(var i = 0;i<sibling_array.length;i++){
                            sibling_string = sibling_string+(i+1)+") <a href = '<?php echo base_url(); ?>service_center/booking_details/"+encodeURIComponent(window.btoa(sibling_array[i]))+"' target = '_blank'>"+sibling_array[i]+"</a><br>";
                        }
                    }
                    if(obj.child){
                        child_string ="";
                        child_array = obj.child.split(",");
                        for(var i = 0;i<child_array.length;i++){
                            child_string = child_string+(i+1)+") <a href = '<?php echo base_url(); ?>booking_details/booking_details"+encodeURIComponent(window.btoa(child_array[i]))+"' target = '_blank'>"+child_array[i]+"</a><br>";
                        }
                    }
                    $('#parent_holder').html(parent_string);
                    $('#sibling_holder').html(sibling_string);
                    $('#child_holder').html(child_string);
                }
             }
            });
        }
        
    function open_model_for_remove_msl(spare_id, booking_id, inventory_id){
        /*
        $.ajax({
            method:"POST",
            data : {},
            url:'<?php //echo base_url(); ?>employee/spare_parts/get_spare_parts_cancellation_reasons',
            success: function(response){
                $("#model_spare_id").val(spare_id);
                $("#model_booking_id").val(booking_id);
                $("#model_inventory_id").val(inventory_id);
                $("#msl_remove_reason").html(response);
                $("#msl_remove_reason").select2();
            }
        });
        */
           
        $("#model_spare_id").val(spare_id);
        $("#model_booking_id").val(booking_id);
        $("#model_inventory_id").val(inventory_id);
        $("#msl_remove_reason").select2();
        $("#remove_msl_model").modal('show');
    }    
        
    function cancelAutoDeliveredPart(){ 
        var spare_id = $("#model_spare_id").val();
        var booking_id = $("#model_booking_id").val();
        /*
        var spare_cancel_id = $("#msl_remove_reason").val();
        if(!spare_cancel_id){
            alert("Please select cancellation reason");
        }
        */
        if(!$("#msl_remove_remark").val()){
            alert("Please fill remarks");
        }
        else{
            $.ajax({
                method:"POST",
                url:'<?php echo base_url(); ?>employee/inventory/remove_msl_consumption',
                data:{'spare_parts_id':spare_id, 'booking_id':booking_id, 'spare_cancel_reason': '<?php echo SPARE_RECIEVED_NOT_USED; ?>', 'remarks':$("#msl_remove_remark").val(), 'inventory_id':$("#model_inventory_id").val()},
                success: function(response){
                    if(response){
                        alert("MSL removed successfully");
                        location.reload();
                    }
                    else{
                        alert("Error in removing MSL");
                    }
                }
            });
        }
    }
</script>