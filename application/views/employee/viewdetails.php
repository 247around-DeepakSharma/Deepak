<?php if(!empty($booking_history)) { ?> 
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false&libraries=places&key=<?php echo GOOGLE_MAPS_API_KEY;?>"></script>
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
    .modal-title{
        color:#5bc0de;
    }
        .loader {
    position: fixed;
    left: 0px;
    top: 0px;
    width: 100%;
    height: 100%;
    z-index: 999999;
    background: url('<?php echo base_url();  ?>images/loading_new.gif') 50% 50% no-repeat rgba(249,249,249,0.80);
  }
</style>
 <div class="loader hide"></div>
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
    <button type="button" class="btn btn-default view_spare_details" data-type="2" href="#tab3" data-toggle="tab">
        <div class="hidden-xs">Spare Parts</div>
    </button>
</div>
<div class="btn-group" role="group">
    <button type="button" class="btn btn-default view_spare_details" data-type="1" href="#tab4" data-toggle="tab">
        <div class="hidden-xs">History</div>
    </button>
</div>
<div class="btn-group" role="group">
    <button type="button" class="btn btn-default" href="#tab8" data-toggle="tab">
        <div class="hidden-xs">Sms / Email</div>
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
    <?php }  if($booking_history[0]['current_status'] != 'Cancelled' && isset($saas_module) && !$saas_module){?>
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
                                <?php if($c2c){   ?>
                                     <a href="javascript:void(0);" onclick="outbound_call(<?php echo $booking_history[0]['booking_primary_contact_no'] ?>)"><?php echo $booking_history[0]['booking_primary_contact_no']; ?></a>
                                    <?php if(!empty($booking_history[0]['booking_alternate_contact_no'])) { ?> 
                                    / <a href="javascript:void(0);" onclick="outbound_call(<?php echo $booking_history[0]['booking_alternate_contact_no'] ?>)"><?php echo $booking_history[0]['booking_alternate_contact_no']; ?></a>   
                                <?php } } else { ?>
                                    <?php echo $booking_history[0]['booking_primary_contact_no']; if(!empty($booking_history[0]['booking_alternate_contact_no'])){ echo ' / '.$booking_history[0]['booking_alternate_contact_no']; }?>
                               <?php } ?>
                               
                            </td>
                        </tr>
                        <tr>
                            <th >Booking ID </th>
                            <td><?php echo "<a href='"."https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/jobcards-pdf/".$booking_history[0]['booking_jobcard_filename']."'>".$booking_history[0]['booking_id']."</a>"; ?></td>
                            <th >Order ID </th>
                            <td>
                                <?php if(!empty($booking_history)){  echo $booking_history[0]['order_id']; } ?>
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
                            <td><?php  echo $booking_history[0]['rating_stars']; ?></td>
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
                            <th >Symptom (Booking Creation Time)</th>
                            <td style="max-width: 330px;"><?php if(!empty($symptom)){ echo $symptom[0]['symptom'];};?>
                            </td>
                            <th >Symptom (Booking Completion Time)</th>
                            <td><?php if(!empty($completion_symptom)) { echo $completion_symptom[0]['symptom']; } ;?>
                            </td>
                        </tr>
                        
                        <tr>
                            <th>Defect</th>
                            <td ><?php if(!empty($technical_defect)) { echo $technical_defect[0]['defect']; }?></td>
                            <th >Solution</th>
                            <td ><?php if(!empty($technical_solution)) { echo $technical_solution[0]['technical_solution']; }?></td>
                        </tr>
                        <tr>
                            <th>Closing Remarks</th>
                            <td style="max-width: 330px;"><?php echo $booking_history[0]['closing_remarks'];?></td>
                            <th>Service Promise Date</th>
                            <td ><?php echo $booking_history[0]['service_promise_date'];?></td>
                        </tr>
                        <tr>
                            <th >Jeeves CD/BD</th>
                            <td ><?php echo $booking_history[0]['api_call_status_updated_on_completed']; ?></td>
                            <th>Repeat Reason</th>
                            <td style="max-width: 330px;"><?php echo $booking_history[0]['repeat_reason'];?></td>
                        </tr>
                        <tr>
                            <th >Paid By Customer(STS)</th>
                            <td ><?php if(!is_null($booking_history[0]['paid_by_customer'])) { if($booking_history[0]['paid_by_customer'] == 1){ echo "Paid By Customer"; } 
                            else {echo "Free For Customer";}} ?></td>
                            <td colspan="2">&nbsp;</td>
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
                                &nbsp;&nbsp;<button type="button" class="btn btn-sm btn-primary fa fa-pencil fa-lg" title="Update File" id="supporting_file_<?=$key?>" onclick="upload_supporting_file(this.id);" style="width:35px;height:35px;"></button>
                            </td>
                        </tr>
                        <?php } } ?>
                        <tr class="clonedInput" id="cat<?=$count?>">
                            <td style="width: 50%;">
                                <select class="form-control" id="file_description_<?=$count?>"  name="file_description" style="width:40%" >
                                    <option selected disabled>Select File Type</option>
                                    <?php if(!empty($file_type)) {
                                        foreach($file_type as $val) { ?>
                                    <option value="<?=$val['id']?>" ><?=$val['file_type']?></option>
                                    <?php  }
                                    } ?>
                                </select>
                            </td>
                            <td style="width: 50%;">
                                <input type="file" id="supportfileLoader_<?=$count?>" name="files[]" onchange="uploadsupportingfile(this.id)" style="display:none" />
                                <div class="progress-bar progress-bar-success myprogress" id="myprogress_supproting_file_<?=$count?>"  role="progressbar" style="width:0%">0%</div>
                                <?php $src = base_url() . 'images/no_image.png';
                                $image_src = $src;    ?>
                                <a id="a_order_support_file_<?=$count?>" href="<?php  echo $src?>" target="_blank"><img id="m_order_support_file_<?=$count?>" src="<?php  echo $image_src ?>" width="35px" height="35px" style="border:1px solid black;margin-left:10px;" /></a>
                                &nbsp;&nbsp;<button type="button" class="btn btn-sm btn-primary fa fa-plus fa-lg" title="Add File" id="supporting_file_<?=$count?>" onclick="upload_supporting_file(this.id);" style="width:35px;height:35px;"></button>
                                &nbsp;&nbsp;<button type="button" class="btn btn-sm btn-primary" title="Remove Row" id="remove_row_<?=$count?>" onclick="remove(this.id)" style="float:right;margin-right:7px;">Remove</button>
                                &nbsp;&nbsp;<button type="button" class="clone btn btn-sm btn-primary" title="Add Row" id="add_row_<?=$count?>" style="float:right;margin-right:5px;">Add</button>
                            </td>
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
        <div class="tab-pane fade in" id="upcountry">
            
            <div class="row">
                <div class="col-md-12">
                    <?php if(!empty($booking_history[0]['vendor_name'])){?>
                    <table class="table  table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>SF Name </th>
                                <th>Account Manager </th>
                                <th>Engineer Name </th>
                                <th>Poc Name </th>
                                <th>Poc Number </th>
                                <th>Municipal Limit </th>
                            </tr>
                            </thead>
                        <tbody>
                            <tr>
                                <td><?php if(isset($booking_history[0]['vendor_name'])){ ?><a href="<?php echo base_url();?>employee/vendor/viewvendor/<?php echo $booking_history[0]['assigned_vendor_id']?>" target="_blank"><?php echo $booking_history[0]['vendor_name']?></a> <?php }?></td>
                                <td><?php if(isset($booking_history[0]['account_manager_name'])){echo $booking_history[0]['account_manager_name'];}?></td>
                                <td><?php if(isset($booking_history[0]['assigned_engineer_name'])){echo $booking_history[0]['assigned_engineer_name'];}?></td>
                                <td><?php if(isset($booking_history[0]['primary_contact_name'])){echo $booking_history[0]['primary_contact_name'];}?></td>
                                <td><?php if(isset($booking_history[0]['primary_contact_phone_1'])){echo $booking_history[0]['primary_contact_phone_1'];?>
                                    <?php if($c2c) { ?>
                                    <button type="button" onclick="outbound_call(<?php echo $booking_history[0]['primary_contact_phone_1'] ?>)" class="btn btn-sm btn-info pull-right"><i class="fa fa-phone fa-lg" aria-hidden="true"></i></button>
                                <?php } }?>
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
                                <td> <?php if($booking_history[0]['is_upcountry'] == 1 && isset($booking_history[0]["municipal_limit"])){ ?>
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
                                        </div>
                                </td>
                            </tr>
                           
                            </tr>
                        </tbody>
                    </table>
                
                </div>
            </div>
            <div class="tab-pane fade in" id="tab2">
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <?php if(!empty($unit_details)) { ?>
                        <table class="table  table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>Brand</th>
                                <th>Category/<br/>Capacity</th>
                                <th>Model Number</th>
                                <th>SF Model Number</th>
                                <th>SF / Partner Serial Number</th>
                                <th>Purchase Date / SF Purchase Date</th>
                                <th>Description</th>
                                <th>Service Category</th>
                                <th>Pay to SF</th>
                                <th>Broken</th>
                                <?php if($booking_history[0]['current_status'] != "Completed"){ ?>
                                <th>Charges</th>
                                <th>Partner Offer</th>
                                <?php if(isset($saas_module) && !$saas_module) { ?>
                                <th>247Around Offer</th>
                                <?php } ?>
                                <th>Upcountry Charges</th>
                                <th>Partner Offer Upcountry Charges</th>
                                <th>Total Charges</th>
                                <?php } else { ?>
                                <th>Charges</th>
                                <th>Partner Offer</th>
                                <?php if(isset($saas_module) && !$saas_module) { ?>
                                <th>247Around Offer</th>
                                <?php } ?>
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
                                   }
                                   $c_up = 0; $p_up = 0; $s_up = 0; 
                                   if($booking_history[0]['is_upcountry'] == 1 && $key == 0){
                                       $s_up = $booking_history[0]['upcountry_distance'] * $booking_history[0]['sf_upcountry_rate'];
                                        if($booking_history[0]['flat_upcountry'] ==1 ){
                                            $s_up = $booking_history[0]['upcountry_sf_payout'];
                                            $c_up =  $booking_history[0]['upcountry_to_be_paid_by_customer'];
                                            $p_up =  $booking_history[0]['partner_upcountry_charges'];
                                        } else if($booking_history[0]['upcountry_paid_by_customer'] == 1) {
                                            
                                            $c_up = round($booking_history[0]['upcountry_distance'] * $booking_history[0]['partner_upcountry_rate'],0);
                                        }  else if($booking_history[0]['upcountry_paid_by_customer'] == 0) {
                                            $p_up = round($booking_history[0]['upcountry_distance'] * $booking_history[0]['partner_upcountry_rate'],0);
                                        }
                                    } 
                                   
                                   
                                   ?>
                                <tr>
                                    <td><?php echo $unit_detail['appliance_brand']?></td>
                                    <td><?php echo $unit_detail['appliance_category']."/<br/>".$unit_detail['appliance_capacity']?></td>
                                    <td><?php echo $unit_detail['model_number']?></td>
                                    <td><?php echo $unit_detail['sf_model_number']?></td>
                                    <td><?php if(!empty($unit_detail['serial_number_pic'])){?>
                                        <a target="_blank" href="<?php echo S3_WEBSITE_URL;?>engineer-uploads/<?php echo $unit_detail['serial_number_pic'];?>"><?php echo $unit_detail['serial_number'];?></a>
                                             <?php } else { echo $unit_detail['serial_number'];} ?> / <?php echo $unit_detail['partner_serial_number']?>
                                    </td>
                                    <td><?php if(!empty($unit_detail['purchase_date'])) {echo $unit_detail['purchase_date'];}?> / <?php if(!empty($unit_detail['sf_purchase_date'])) {echo $unit_detail['sf_purchase_date'];}?></td>
                                    <td><?php echo $unit_detail['appliance_description']?></td>
                                    <?php if($booking_history[0]['current_status'] != "Completed"){ ?>
                                    <td><?php  print_r($unit_detail['price_tags']); ?></td>
                                    <td><?php if($unit_detail['pay_to_sf'] ==1){ echo "Yes"; } else { echo "No";} ?></td>
                                    <td><?php if($unit_detail['is_broken'] ==1){ echo "Yes"; } else { echo "No";} ?></td>
                                    <td><?php  print_r($unit_detail['customer_total']); ?></td>
                                    <td><?php print_r($unit_detail['partner_net_payable']);  ?></td>
                                    <?php if(isset($saas_module) && !$saas_module) { ?>
                                    <td><?php print_r($unit_detail['around_net_payable']);  ?></td>
                                    <?php } ?>
                                    <!--Upcountry Charges-->
                                    <td><?php echo round($c_up + $p_up, 0); ?></td>
                                    <!--Partner Offer Upcountry Charges-->
                                    <td><?php  echo round($p_up, 0); ?></td>
                                    <!--Total Charges-->
                                    <td><?php  print_r($unit_detail['customer_net_payable'] +$c_up); ?></td>
                                    <?php } else {   ?>
                                    <td><?php  print_r($unit_detail['price_tags']); ?></td>
                                    <td><?php if($unit_detail['pay_to_sf'] ==1){ echo "YES"; } else { echo "NO";} ?></td>
                                    <td><?php if($unit_detail['is_broken'] ==1){ echo "Yes"; } else { echo "No";} ?></td>
                                    <td><?php  print_r($unit_detail['customer_total']); ?></td>
                                    <td><?php print_r($unit_detail['partner_net_payable']);  ?></td>
                                    <?php if(isset($saas_module) && !$saas_module) { ?>
                                    <td><?php print_r($unit_detail['around_net_payable']);  ?></td>
                                    <?php } ?>
                                    <td><?php if($key == 0){ if($booking_history[0]['is_upcountry'] == 1){ echo round($booking_history[0]['upcountry_distance'] * $booking_history[0]['partner_upcountry_rate'],0); } } ?></td>
                                    <!--Partner Offer Upcountry Charges-->
                                    <td><?php echo round($p_up, 0); ?></td>
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
                                                $sf_upcountry_charges =  $s_up;
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
                                    <?php   } ?>
                            </tbody>
                        </table>
                        <?php } ?>
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
                <?php if (isset($booking_history['spare_parts'])) { $estimate_given = false; $parts_shipped = false; $defective_parts_shipped = FALSE;   ?>
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
                                        <th> Original Requested Parts </th>
                                        <th> Final Requested Parts </th>
                                        <th> Requested Part Number </th>
                                        <th> Parts Type </th>  
                                        <th> Parts Warranty Status </th>    
                                        <th>Requested Quantity </th>                                
                                        <th >Requested Date</th>
                                        <th >Invoice Image </th>
                                        <th >Serial Number Image </th>
                                        <th >Defective Front Part Image </th>
                                        <th >Defective Back Part Image </th>
                                        <th >Serial Number </th>
                                        <th >Acknowledge Date BY SF </th>
                                        <th >Remarks By SC </th>
                                        <th >Current Status</th>
                                        <th>Move To Vendor</th>
                                        <th>Move To Partner</th>
                                        <?php if(($booking_history[0]['request_type']==HOME_THEATER_REPAIR_SERVICE_TAG_OUT_OF_WARRANTY) || ($booking_history[0]['request_type']==REPAIR_OOW_TAG)){ } else{ ?>
                                        <th>Copy Booking Id</th>
                                        <?php  } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($booking_history['spare_parts'] as $sp) { ?>
                                    <tr>
                                        <td class="  <?php if($sp['entity_type']==_247AROUND_SF_STRING) echo 'warehouse_name';  ?> "  data-warehouse="<?php echo $sp['partner_id'];  ?>" ><span id="entity_type_id"><?php if($sp['entity_type'] == _247AROUND_PARTNER_STRING){ echo "Partner";} else {
                                              echo "Warehouse";
                                          } 
                                         ?></span></td>
                                        <td><?php echo $sp['model_number']; ?></td>
                                        <td style=" word-break: break-all;"><?php if(isset($sp['original_part_number'])){ echo $sp['original_part_number']; } else { echo $sp['parts_requested']; } ?></td>
                                        <td style=" word-break: break-all;"><?php if(isset($sp['final_spare_parts'])){ echo $sp['final_spare_parts']; } ?></td>
                                        <td style=" word-break: break-all;"><?php if(isset($sp['part_number'])){ echo $sp['part_number']; } ?></td>
                                        <td style=" word-break: break-all;"><?php echo $sp['parts_requested_type']; ?></td>  
                                        <td><?php if($sp['part_warranty_status']==2){echo 'Out Of Warranty';}else{echo 'In - Warranty';} ?></td> 
                                        <td><?php echo $sp['quantity']; ?></td> 
                                        
                                        <td><?php echo $sp['create_date']; ?></td>
                                        <td><div class="progress-bar progress-bar-success myprogress" id="<?php echo "myprogressinvoice_pic".$sp['id'] ?>" role="progressbar" style="width:0%">0%</div><?php if (!is_null($sp['invoice_pic'])) {
                                            if ($sp['invoice_pic'] != '0') {
                                        ?> <a href="<?php echo S3_WEBSITE_URL; ?>misc-images/<?php echo $sp['invoice_pic']; ?> " target="_blank" id="<?php echo "a_invoice_pic_".$sp['id']; ?>">Click Here</a> <?php } } ?> &nbsp;&nbsp;<i id="<?php echo "invoice_pic_".$sp['id']; ?>" class="fa fa-pencil fa-lg" onclick="openfileDialog('<?php echo $sp["id"];?>','invoice_pic');"></i>
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
                                        <td style=" word-break: break-all;"><span class="serial_no_text" id="<?php echo $sp['id']."|serial_number";?>"><?php echo $sp['serial_number']; ?></span> <span class="serial_no_edit"><i class="fa fa-pencil fa-lg"></i></span></td>
                                        <td><?php echo $sp['acknowledge_date']; ?></td>
                                        <td><?php echo $sp['remarks_by_sc']; ?></td>
                                        <td><?php echo $sp['status']; ?></td>


                                     <?php if(($booking_history[0]['request_type']==HOME_THEATER_REPAIR_SERVICE_TAG_OUT_OF_WARRANTY) || ($booking_history[0]['request_type']==REPAIR_OOW_TAG)){ } else{ ?>
                                        <?php  if($sp['entity_type']==_247AROUND_PARTNER_STRING && $sp['status'] == SPARE_PARTS_REQUESTED){?>
                                            <td>
                                                <form id="move_to_update_spare_parts">
                                                    <input type="hidden" name="spare_parts_id" id="spare_parts_id" value="<?php echo $sp['id']; ?>">
                                                    <input type="hidden" name="booking_partner_id" id="booking_partner_id" value="<?php echo $booking_history[0]['partner_id']; ?>">
                                                    <input type="hidden" name="entity_type" id="entity_type" value="<?php echo _247AROUND_SF_STRING; ?>">
                                                    <input type="hidden" name="bulk_input" id="booking_id" value="<?php echo $sp['booking_id']; ?>">   
                                                    <input type="hidden" name="requested_spare_id" id="rew_in_id" value="<?php echo $sp['requested_inventory_id']; ?>">  
                                                    <input type="hidden" name="state" id="booking_state" value="<?php echo $booking_history[0]['state']; ?>"> 
                                                    <input type="hidden" name="parts_requested" id="booking_state" value="<?php echo $sp['parts_requested']; ?>"> 
                                                    <input type="hidden" name="service_center_id" id="booking_state" value="<?php echo $sp['service_center_id']; ?>">   
                                         <a class="move_to_update btn btn-md btn-primary" id="move_to_vendor" href="javascript:void(0);">Move To Vendor</a>
                                                 </form>
                                            </td>
                                        <?php } else {?> 
                                           <td></td>   
                                         <?php } } ?>

                                        <?php if(($booking_history[0]['request_type']==HOME_THEATER_REPAIR_SERVICE_TAG_OUT_OF_WARRANTY) || ($booking_history[0]['request_type']==REPAIR_OOW_TAG)){ } else{ ?>
                                        <?php  if($sp['entity_type']==_247AROUND_SF_STRING && $sp['status'] == SPARE_PARTS_REQUESTED){?>
                                            <td>
                                                <form id="move_to_update_spare_parts_partner">
                                                    <input type="hidden" name="spare_parts_id" id="spare_parts_id" value="<?php echo $sp['id']; ?>">
                                                    <input type="hidden" name="booking_partner_id" id="booking_partner_id" value="<?php echo $booking_history[0]['partner_id']; ?>">
                                                    <input type="hidden" name="entity_type" id="entity_type" value="<?php echo _247AROUND_PARTNER_STRING; ?>">
                                                    <input type="hidden" name="bulk_input" id="booking_id" value="<?php echo $sp['booking_id']; ?>">     
                                                    <input type="hidden" name="requested_spare_id" id="rew_in_id" value="<?php echo $sp['requested_inventory_id']; ?>"> 

                                                    <input type="hidden" name="parts_requested" id="booking_state" value="<?php echo $sp['parts_requested']; ?>"> 
                                                    <input type="hidden" name="warehouse_id" id="booking_state" value="<?php echo $sp['partner_id']; ?>"> 
                                                    <a class="move_to_update_partner btn btn-md btn-primary" id="move_to_vendor" href="javascript:void(0);">Move To Partner</a>

                                                 </form>
                                            </td>
                                        <?php } else {?> 
                                           <td></td>   
                                         <?php } } ?>
                                       
                                       <?php if(($booking_history[0]['request_type']==HOME_THEATER_REPAIR_SERVICE_TAG_OUT_OF_WARRANTY) || ( $sp['part_warranty_status'] == 2 )){ } else{ ?>
                                        <td><button type="button" class="copy_booking_id  btn btn-info" data-toggle="modal" id="<?php echo $sp['booking_id']."_".$sp['id']; ?>" data-target="#copy_booking_id">Copy</button>
                                       </td>                                
                                     <?php } ?>
                                   
                                    </tr>
                                    <?php if(!is_null($sp['parts_shipped'])){ $parts_shipped = true;} if(!empty($sp['defective_part_shipped'])){
                                        $defective_parts_shipped = TRUE;
                                        } if($sp['purchase_price'] > 0){ $estimate_given = TRUE; }                                         
                                        } ?>
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
                                        <th>Part Shipped By Partner/Warehouse</th>
                                        <th>Shipped Parts </th>
                                        <th>Shipped  Parts Number</th>
                                        <th>Pickup Request </th>
                                        <th>Pickup Schedule</th>
                                        <th>Courier Name</th>
                                        <th>AWB </th>
                                        <th>Shipped date </th>
                                        <th>EDD </th>
                                        <th>Remarks By Partner</th>
                                        <th>Challan Number </th>
                                        <th>Challan approx Value </th>
                                        <th>Challan File</th>
                                        <th>Courier File</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($booking_history['spare_parts'] as $sp) { if(!empty($sp['parts_shipped'])){ ?>
                                    <tr>
                                        <td><?php if($sp['entity_type'] == _247AROUND_PARTNER_STRING) { echo "Partner";} else { echo "Warehouse";} ?></td>
                                        <td style="word-break: break-all;"><?php echo $sp['parts_shipped']; ?></td> 
                                        <td style="word-break: break-all;"><?php if(!empty($sp['shipped_part_number'])){echo $sp['shipped_part_number'];}else{echo 'Not Available';}  ?></td>   
                                        <td style="word-break: break-all;"><?php if($sp['around_pickup_from_service_center'] == COURIER_PICKUP_REQUEST){    echo 'Pickup Requested';} ?></td>
                                        <td style="word-break: break-all;"><?php if($sp['around_pickup_from_service_center'] == COURIER_PICKUP_SCHEDULE){    echo 'Pickup Schedule';} ?></td>
                                        <td>                                            
                                            <span class="serial_no_text" id="<?php echo $sp['id']."|courier_name_by_partner";?>"><?php echo str_replace(array('-','_'), ' ', $sp['courier_name_by_partner']); ?></span> <span class="serial_no_edit"><i class="fa fa-pencil fa-lg"></i></span>
                                            <input type="hidden" value="<?php echo $sp['courier_name_by_partner'];  ?>" id="<?php echo $sp['id']."_courier_name_by_partner";?>" />
                                        </td>                                        
                                        <td>
                                            <span class="serial_no_text" id="<?php echo $sp['id']."|awb_by_partner";?>" style="color:blue; pointer:cursor" onclick="get_awb_details('<?php echo $sp['courier_name_by_partner']; ?>','<?php echo $sp['awb_by_partner']; ?>','<?php echo $sp['status']; ?>','<?php echo "awb_loader_".$sp['awb_by_partner']; ?>')"><?php echo $sp['awb_by_partner']; ?></span> 
                                            <span class="serial_no_edit"><i class="fa fa-pencil fa-lg"></i></span>
                                            <span id=<?php echo "awb_loader_".$sp['awb_by_partner'];?> style="display:none;"><i class="fa fa-spinner fa-spin"></i></span>
                                        </td>                                       
                                        <td> <input type="hidden" value="<?php echo $sp['status'];  ?>" id="<?php echo $sp['id']."_status";?>" /><?php echo $sp['shipped_date']; ?></td>
                                        <td><?php echo $sp['edd']; ?></td>
                                        <td><?php echo $sp['remarks_by_partner']; ?></td>
                                        <td>                                         
                                            <span class="serial_no_text" id="<?php echo $sp['id']."|partner_challan_number";?>"><?php echo str_replace(array('-','_'), ' ', $sp['partner_challan_number']); ?></span> <span class="serial_no_edit"><i class="fa fa-pencil fa-lg"></i></span>
                                            <input type="hidden" value="<?php echo $sp['partner_challan_number'];  ?>" id="<?php echo $sp['id']."_partner_challan_number";?>" />
                                        </td>
                                        <td>                                                                                    
                                            <span class="serial_no_text" id="<?php echo $sp['id']."|challan_approx_value";?>"><?php echo str_replace(array('-','_'), ' ', $sp['challan_approx_value']); ?></span> <span class="serial_no_edit"><i class="fa fa-pencil fa-lg"></i></span>
                                            <input type="hidden" value="<?php echo $sp['challan_approx_value'];  ?>" id="<?php echo $sp['id']."_challan_approx_value";?>" />
                                            
                                        </td>
                                        <td>
                                            <?php if(!empty($sp['partner_challan_file'])){ ?> 
                                            
                                            <?php } ?>
                                            
                                        <div class="progress-bar progress-bar-success myprogress" id="<?php echo "myprogresspartner_challan_file".$sp['id'] ?>" role="progressbar" style="width:0%">0%</div><?php if (!is_null($sp['partner_challan_file'])) {
                                            if ($sp['partner_challan_file'] != '0') {
                                        ?> <a href="<?php echo S3_WEBSITE_URL;?>vendor-partner-docs/<?php echo $sp['partner_challan_file']; ?>" target="_blank" id="<?php echo "a_partner_challan_file_".$sp['id']; ?>">Click Here to view</a> <?php } } ?> &nbsp;&nbsp;<i id="<?php echo "partner_challan_file_".$sp['id']; ?>" class="fa fa-pencil fa-lg" onclick="openfileDialog('<?php echo $sp["id"];?>','partner_challan_file');"></i>
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
                                        <th >Shipped Parts Number</th>
                                        <th >Courier Name </th>
                                        <th>AWB </th>
                                        <th> No. Of Boxes </th>
                                        <th> Weight</th>
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
                                        <td><?php if(!empty($sp['part_number'])){ echo $sp['part_number'];}else{echo 'Not Available';} ?></td>
                                        <td><?php echo ucwords(str_replace(array('-','_'), ' ', $sp['courier_name_by_sf'])); ?></td>
                                        <?php
                                        $spareStatus = DELIVERED_SPARE_STATUS;
                                        if(!$sp['defactive_part_received_date_by_courier_api']){
                                            $spareStatus = $sp['status'];
                                        }
                                        ?>
                                        <td><a href="javascript:void(0)" onclick="get_awb_details('<?php echo $sp['courier_name_by_sf']; ?>','<?php echo $sp['awb_by_sf']; ?>','<?php echo $spareStatus; ?>','<?php echo "awb_loader_".$sp['awb_by_sf']; ?>')"><?php echo $sp['awb_by_sf']; ?></a> 
                                            <span id="<?php echo "awb_loader_".$sp['awb_by_sf'];?>" style="display:none;"><i class="fa fa-spinner fa-spin"></i></span></td>
                                        <td><?php if(!empty($sp['awb_by_sf']) && !empty($courier_boxes_weight_details['box_count'])){ echo $courier_boxes_weight_details['box_count']; } ?></td>
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
                <div class="row">
                    <div class="col-md-12">
                        <h1 style='font-size:24px;'>Invoice Id Details</h1>
                        <div class="col-md-12" style="padding-left:1px;">
                            <table class="table  table-striped table-bordered" >
                                <thead>
                                    <tr>
                                        <th> Model Number </th>
                                        <th> Requested Parts </th>
                                        <th> Requested Parts Number</th>
                                        <th>Parts Type</th>
                                        <th> Purchase Invoice Id </th>
                                        <th>Sale Invoice Id</th>
                                        <th>Reverse Purchase Invoice Id</th>
                                        <th>Reverse Sale Invoice Id </th>
                                        <th>Warehouse Courier Invoice Id</th>
                                        <th>Partner Courier Invoice Id</th>
                                        <th>Vendor Courier Invoice Id</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    
                                    if(!empty($booking_history['spare_parts'])){
                                    foreach ($booking_history['spare_parts'] as $sp) {
                                     ?>
                                    <tr>
                                        <td><?php echo $sp['model_number']; ?></td>
                                        <td style=" word-break: break-all;"><?php echo $sp['parts_requested']; ?></td>
                                        <td style=" word-break: break-all;"><?php if(!empty($sp['part_number'])){ echo $sp['part_number'];}else{echo 'Not Available';} ?></td>
                                        <td style=" word-break: break-all;"><?php echo $sp['parts_requested_type']; ?></td> 
                                        <td><?php echo $sp['purchase_invoice_id']; ?></td>
                                        <td><?php echo $sp['sell_invoice_id']; ?></td>  
                                        <td><?php echo $sp['reverse_sale_invoice_id']; ?></td>
                                        <td><?php echo $sp['reverse_purchase_invoice_id']; ?></td>  
                                        <td><?php echo $sp['warehouse_courier_invoice_id']; ?></td> 
                                        <td><?php echo $sp['partner_courier_invoice_id']; ?></td> 
                                        <td><?php echo $sp['vendor_courier_invoice_id']; ?></td> 
                                    </tr>
                                    <?php } }?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">  
                        <span id="spare_parts_template">
                            <div id="spare_parts_commentbox"> </div>  
                        </span>
                    </div>
            </div>
           <!-- Common uses of hidden field  --->
           <input type="hidden" id="comment_type" name="comment_type" value="">
           
            <div class="tab-pane fade in" id="tab4">
                <div style="padding: 0 15px;">
                    <div class="row">
                        <div id="historyDetails"></div>
                        <span id="booking_hostory_template">
                            <div id="commentbox"> </div>
                        </span>
                        
                    </div>
                </div>
            </div>
            <div class="tab-pane fade in" id="tab8">
                <div style="padding: 0 15px;">
                    <div class="row">
                        <div id="email_and_sms_box"></div>
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
    
    <!-- copy Booking Id  Modal  start -->
        <div class="modal fade" id="copy_booking_id" role="dialog">
            <div class="modal-dialog">    
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title" style="display: inline-block">Old Booking Id :</h4>
                        <span id="old_booking_html" style="display: inline;font-weight: bold; padding-left: 10px;"></span> 
                    </div>
                    <div class="modal-body">
                        <p id="response_err"></p>                       
                        <p>
                        <div style="display: inline-block; font-weight: bold; padding-bottom:5px;">New Booking Id  </div>
                        <div  style="display: inline;">
                            <input type="hidden" name="spare_parts_id" id="spare_parts_id" value="">
                            <input type="text" class="form-control" name="new_booking_id" id="new_booking_id" value="">
                        </div>                        
                        <div style="display: inline-block; font-weight: bold; padding: 5px 0px 5px 0px;">Status</div>
                        <div  style="display: inline;">                            
                            <input type="text" class="form-control" name="status" id="status" value="<?php echo SPARE_PARTS_REQUESTED; ?>" disabled="true">
                        </div>   
                        
                        </p>
                        <p>
                            <a href="javascript:void(0);" class="btn btn-primary" id="generate_new_booking">Generate</a>                 
                        </p>              
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>

            </div>
        </div>

    </div>
<!-- copy Booking Id  Modal  End -->

<!--Invoice Payment History Modal-->
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
<!-- end Invoice Payment History Modal -->

<script>
    var regex = /^(.+?)(\d+)$/i;
    var cloneIndex = "<?=((isset($booking_files) && !empty($booking_files))?(count($booking_files)+1):1);?>";//$(".clonedInput").length;
    
    function clone(){
        var len = $(".clonedInput").length;
        var row_id = $(".clonedInput")[len-1].id.substr(3);
        $("#cat"+row_id).clone()
           .appendTo(".cloned")
           .attr("id", "cat" +  cloneIndex)
           .find("*")
           .each(function() {
               var id = this.id || "";
               var match = id.match(regex) || [];

               if (match.length === 3) {
                   this.id = match[1] + (cloneIndex);
               }
           })
           .on('click', 'button.clone', clone)
           
        cloneIndex++;
        return false;
    }
    function remove(id){
        if($('.clonedInput').length > 1) {
            $("#cat"+id.split("_")[2]).remove();
        }
        return false;
    }
    $("button.clone").on("click", clone);
    
    $("#btn_addSupportFile").click(function() {
        $('tr.clonedInput').toggle();
    });
    
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
        var emailsms_url = '<?php echo base_url() ?>employee/booking/get_booking_email_sms/<?php echo $booking_history[0]['booking_id'] ?>';
                $.ajax({
                    type: 'POST',
                    url: booking_id,
                    success: function (response) {
                        $('#historyDetails').html(response);
                    }
                });
                
                $.ajax({
                    type: 'POST',
                    url: emailsms_url,
                    success: function (response) {
                        $('#email_and_sms_box').html(response);
                        $('#email_and_sms_box').find('.booking_history_div').css("display", "none");
                    }
                });
    });
    
            $(document).ready(function () {
                $(".btn-pref .btn").click(function () {
                    $(".btn-pref .btn").removeClass("btn-primary").addClass("btn-default");
                    // $(".tab").addClass("active"); // instead of this do the below 
                    $(this).removeClass("btn-default").addClass("btn-primary");
                });
                if($('tr.uploaded_support_file').length >= 1) {
                    $("#btn_addSupportFile").click();
                }
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
            get_booking_relatives();
    });
        function get_booking_relatives(){
            $.ajax({
                method:"POST",
                data : {},
                url:'<?php echo base_url(); ?>employee/booking/get_booking_relatives/<?php echo $booking_history[0]['booking_id']; ?>',
                success: function(res){
                    if(res){
                    $("#relative_holder").show();
                    var obj = JSON.parse(res);
                    parent_string = child_string = sibling_string = "NULL";
                    if(obj.parent){
                        parent_string = "<a href = '<?php echo base_url(); ?>employee/booking/viewdetails/"+obj.parent+"' target = '_blank'>"+obj.parent+"</a>";
                    }
                    if(obj.siblings){
                        sibling_string ="";
                        sibling_array = obj.siblings.split(",");
                        for(var i = 0;i<sibling_array.length;i++){
                            sibling_string = sibling_string+(i+1)+") <a href = '<?php echo base_url(); ?>employee/booking/viewdetails/"+sibling_array[i]+"' target = '_blank'>"+sibling_array[i]+"</a><br>";
                        }
                    }
                    if(obj.child){
                        child_string ="";
                        child_array = obj.child.split(",");
                        for(var i = 0;i<child_array.length;i++){
                            child_string = child_string+(i+1)+") <a href = '<?php echo base_url(); ?>employee/booking/viewdetails/"+child_array[i]+"' target = '_blank'>"+child_array[i]+"</a><br>";
                        }
                    }
                    console.log(parent_string);
                    if(parent_string !== null){
                        $('#parent_holder').html(parent_string);
                    }else{
                        $('#parent_holder').html("<span>NA</span>");
                    }
                    if(sibling_string !== null){
                        $('#sibling_holder').html(sibling_string);
                    }else{
                        $('#sibling_holder').html("<span>NA</span>");
                    }
                    if(child_string !== null){
                        $('#child_holder').html(child_string);
                    }else{
                        $('#child_holder').html("<span>NA</span>");
                    }
                }
             }
            });
        }
    
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
    
    function addComment() {
        var prethis = $(this);
        var comment_type = $("#comment_type").val();
        var comment = $("#comment").val();
        var booking_id = '<?php echo $booking_history[0]['booking_id']?>';
  
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/booking/addComment',
             beforeSend: function(){
                
                 prethis.html('<i class="fa fa-circle-o-notch fa-lg" aria-hidden="true"></i>');
             },
            data: {comment_type : comment_type, comment: comment, booking_id: booking_id},
            success: function (response) { 
                if(response === "error"){
                    alert('There is some issue. Please refresh and try again');
                } else {
                    document.getElementById("commentbox").innerHTML = response;
                    document.getElementById("spare_parts_commentbox").innerHTML = response;
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
        var comment_type = $("#comment_type").val();
        var comment = $("#comment2").val();
        var comment_id= $("#comment_id").val();
        var booking_id= '<?php echo $booking_history[0]['booking_id']?>';
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/booking/update_Comment',
             beforeSend: function(){
                
                 prethis.html('<i class="fa fa-circle-o-notch fa-lg" aria-hidden="true"></i>');
             },
            data: {comment: comment, comment_id: comment_id, booking_id: booking_id, comment_type: comment_type},
            success: function (response) {
                if(response === "error"){
                    alert('There is some issue. Please refresh and try again');
                } else {
                    document.getElementById("commentbox").innerHTML = response;
                    document.getElementById("spare_parts_commentbox").innerHTML = response;
                } 
            }
            
        });
    }
    
    
     function deleteComment(comment_id) {
                
            var comment_type = $("#comment_type").val(); 
            var check = confirm("Do you want to delete this comment?");
            if(check == true){
                var comment_id = comment_id;
                var booking_id= '<?php echo $booking_history[0]['booking_id'] ?>';
                $.ajax({
                    type: 'POST',
                    url: '<?php echo base_url(); ?>employee/booking/deleteComment',
                    data: {comment_id: comment_id, booking_id:booking_id ,comment_type : comment_type},
                    success: function (response) {
                        if(response === "error"){
                            alert('There is some issue. Please refresh and try again');
                        } else {
                            document.getElementById("commentbox").innerHTML = response;
                            document.getElementById("spare_parts_commentbox").innerHTML = response;  
                        } 
                    }
                    
                });
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
//         $.ajax({
//             method:'GET',
//             url:'<?php //echo base_url(); ?>employee/dealers/get_dealer_data/<?php //echo $booking_history[0]['dealer_id']?>',
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

function upload_supporting_file(id){
    var key = id.split("_")[2];
    $("#supportfileLoader_"+key).click();
}

function uploadsupportingfile(id, file_id=''){
    var key = id.split("_")[1];
     var file = $("#supportfileLoader_"+key).val();
     if (file === '') {
        alert('Please select file');
        return;
    } else {
        var formData = new FormData();
        formData.append('support_file[]', $("#supportfileLoader_"+key)[0].files[0]);
        if(file_id !== '') {
            formData.append('id', file_id);
        }
        else {
            formData.append('file_description_id', $("#file_description_"+key).val());
        }
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
                            
                            $('#myprogress_supproting_file_'+key).text(percentComplete + '%');
                            $('#myprogress_supproting_file_'+key).css('width', percentComplete + '%');
                        }
                    }, false);
                    return xhr;
                },
                success: function (response) {
                    $('#myprogress_supproting_file_'+key).css('width', '0%');
                    obj = JSON.parse(response);
                    
                    if(obj.code === "success"){
                        $("#a_order_support_file_"+key).attr("href", "<?php echo S3_WEBSITE_URL;?>misc-images/" + obj.name);
                        $("#m_order_support_file_"+key).attr("src", "<?php echo S3_WEBSITE_URL;?>misc-images/" + obj.name);
                        if(file_id === '') {
                            location.reload();
                        }
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
    
    if(spareFileColumn=='partner_challan_file'){
        directory_name = 'vendor-partner-docs';
    }else{
        directory_name = '';
    }
    
    
        if(flag === true){
            var formData = new FormData();
            formData.append('file', $('#fileLoader')[0].files[0]);
            formData.append('spareID', spareID);
            formData.append('spareColumn', spareFileColumn);
            formData.append('directory_name', directory_name);
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
                        if(directory_name!=''){
                        $("#a_"+ spareFileColumn +"_" + spareID).attr("href", "<?php echo S3_WEBSITE_URL;?>vendor-partner-docs/" + obj.name);                        
                        }else{
                         $("#a_"+ spareFileColumn +"_" + spareID).attr("href", "<?php echo S3_WEBSITE_URL;?>misc-images/" + obj.name);   
                        }
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
                    
                    if(column === "awb_by_partner"){
                        var c_name = $('#'+line_item_id+"_courier_name_by_partner").val();
                        var status = $("#"+line_item_id+"_status").val();
                       prethis.siblings(".serial_no_text").attr('onclick', 'get_awb_details("'+c_name+'", "'+data_value+'", "'+status+'", "awb_loader_'+data_value+'")');
                    } else if(column === "courier_name_by_partner"){
                        $('#'+line_item_id+"_courier_name_by_partner").val(data_value);
                        var status = $("#"+line_item_id+"_status").val();
                        var awb = $("#"+line_item_id+"|awb_by_partner").text();
                        $("#"+line_item_id+"|awb_by_partner").attr('onclick', 'get_awb_details("'+c_name+'", "'+awb+'", "'+status+'", "awb_loader_'+awb+'")');
                    }
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
    </script>
    
      
    <script type="text/javascript">
    $(document).ready(function(){
        $(".copy_booking_id").click(function(){
            var ids_string = $(this).attr('id');
            var ids_array = ids_string.split('_');
            $("#old_booking_html").html(ids_array[0]);
            $("#spare_parts_id").val(ids_array[1]);
        });
        
        $("#new_booking_id").on('keypress',function(){
            $("#response_err").html('');
        });
        
        $("#generate_new_booking").click(function(){           
           var spare_parts_id = $("#spare_parts_id").val();
           var new_booking_id = $("#new_booking_id").val(); 
           var status = $("#status").val();            
           if(spare_parts_id!='' && new_booking_id!='' && status!=''){          
           
            $.ajax({
                method:"POST",
                data : {spare_parts_id: spare_parts_id, new_booking_id: new_booking_id,status:status},
                url:'<?php echo base_url(); ?>employee/spare_parts/copy_booking_details_by_spare_parts_id',
                success: function(response){                  
                    if(response=='success'){
                        $("#response_err").html("Process is successful").css({"color": "green"});
                        $("#new_booking_id").val("");
                    }else{
                        $("#response_err").html("Process is failed").css({"color": "red"});
                    }                    
                }
            });
            
         }else{
          $("#response_err").html("Please Enter Valid Information.").css({"color": "red"});
          }
            
        });
       
       $(".move_to_update").on('click', function () { 
                
                swal({
                title: "Are you sure?",
                text: "You are going to transfer the spare part!",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, Transfer it!",
                cancelButtonText: "No, cancel !",
                closeOnConfirm: false,
                closeOnCancel: false
               },
                 function(isConfirm) {
                    if (isConfirm) {
                        $(".loader").removeClass('hide');
                        $.ajax({
                        type: "POST",
                        url: "<?php echo base_url(); ?>employee/spare_parts/bulkConversion_process",
                        data: $("#move_to_update_spare_parts").serialize(),
                        success: function (data) {
                        console.log(data);
                       if (data != '') {
                        
                        $("#entity_type_id").html("<?php echo _247AROUND_PARTNER_STRING; ?>");
                        if(data=='success'){
                          $(".loader").addClass('hide');
                          swal("Transferred!", "Your spare has been transferred !.", "success");
                          $("#move_to_vendor").hide();
                        //  location.reload();
                        }else{
                           $(".loader").addClass('hide');
                           swal("Failed", "Spare  transferred has been failed due to stock not available", "error");  
                        }
                    }else{
                       $(".loader").addClass('hide');
                       swal("Failed", "Spare  transferred has been failed. Requested inventory not found/mapped", "error");   
                    }
                    },
                    error: function () {
                     $(".loader").addClass('hide');
                     swal("Error Occured", "Some error occured data not found", "error");
                    }
                  });
                    } else {
                       $(".loader").addClass('hide');
                       swal("Cancelled", "Your Transferred has been cancelled !", "error");
                   }
                });

        });
        
               $(".move_to_update_partner").on('click', function () { 
                swal({
                title: "Are you sure?",
                text: "You are going to transfer the spare part!",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, Transfer it!",
                cancelButtonText: "No, cancel !",
                closeOnConfirm: false,
                closeOnCancel: false
               },
                 function(isConfirm) {
                    if (isConfirm) {
                        $(".loader").removeClass('hide');
                        $.ajax({
                        type: "POST",
                        url: "<?php echo base_url(); ?>employee/spare_parts/move_to_update_spare_parts_details",
                        data: $("#move_to_update_spare_parts_partner").serialize(),
                        success: function (data) {
                        console.log(data);
                       if (data != '') {               
                        $("#entity_type_id").html("<?php echo _247AROUND_PARTNER_STRING; ?>");
                        if(data=='success'){
                          $(".loader").addClass('hide');
                          swal("Transferred!", "Your spare has been transferred to partner!.", "success");
                          $("#move_to_vendor").hide();
                        //  location.reload();
                        }else if(data='fail_mail'){
                          $(".loader").addClass('hide');
                          swal("Failed", "Your Transferred has been failed. Check your mail for details!", "error"); 
                        }else{
                           $(".loader").addClass('hide');
                           swal("Failed", "Your Transferred has been failed. Either  network error occured !", "error");  
                        }
                    }
                    },
                    error: function () {
                     $(".loader").addClass('hide');
                     swal("Error Occured", "Some error occured data not found", "error");
                    }
                  });
                    } else {
                        $(".loader").addClass('hide');
                       swal("Cancelled", "Your Transferred has been cancelled !", "error");
                   }
                });

        });
        
        
        
       
    });
    
     $(".view_spare_details").on('click',function(){ 
         var type_val = $(this).data('type');
         $("#comment_type").val(type_val);   
           getcommentbox(type_val);    
                      
     });
     
    function cancel(){
     var type_val = $("#comment_type").val();   
      getcommentbox(type_val);        
    }   
    
    
    function getcommentbox(type_val){
        $.ajax({
                    method: 'POST',
                    data: {},
                    url: '<?php echo base_url(); ?>employee/booking/get_comment_section/<?php if(!empty($booking_history)){ echo $booking_history[0]['booking_id']; }?>/'+type_val,
                    success: function (response) {
                        if(type_val == 2){
                            document.getElementById("commentbox").remove();
                            document.getElementById("booking_hostory_template").innerHTML = '<div id="commentbox"></div>';                         
                            document.getElementById("spare_parts_commentbox").innerHTML = response;
                        }else{
                            document.getElementById("commentbox").innerHTML = response;                        
                            document.getElementById("spare_parts_commentbox").remove();
                            document.getElementById("spare_parts_template").innerHTML = '<div id="spare_parts_commentbox"> </div>';
                        }

                    }
                  });
    }
    
   $(document).ready(function(){
    $(".warehouse_name").each(function(){
        var warehouse_id = $(this).attr("data-warehouse");
        if (warehouse_id>0) {
              $.ajax({url: "<?php echo base_url(); ?>employee/vendor/get_warehouse_data/"+warehouse_id, success: function(result){
              var obj =  JSON.parse(result);
              console.log(obj[0].district);
              $(".warehouse_name").html('<span id="entity_type_id">'+obj[0].district + ' Warehouse</span>');
           }});
        }
    });
   });
    </script>
    
   
