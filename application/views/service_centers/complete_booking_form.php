<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>css/jquery.loading.css">
 <script src="<?php echo base_url();?>js/jquery.loading.js"></script>
 <?php $dop_mendatory = 0; ?>
<div id="page-wrapper" >
    <div class="" >
        <?php if(validation_errors()){?>
        <div class="panel panel-danger" style="margin-top:10px;margin-bottom:-10px;">
            <div class="panel-heading" style="padding:7px 0px 0px 13px">
                <?php echo validation_errors(); ?>
            </div>
        </div>
        <?php }?>
        <?php $required_sp_id = array(); $can_sp_id = array(); ?>
        <?php  $flag = 0; $requestedParts = false; if(isset($booking_history['spare_parts'])){ 
            foreach ($booking_history['spare_parts'] as  $value) {
                if($value['status'] == "Completed" || $value['status'] == "Cancelled"){} else {
                    if($value['defective_part_required'] == 1 && $value['status'] != SPARE_PARTS_REQUESTED){
                        if(!empty($value['parts_shipped'])){
                            $flag = 1; 
                            array_push($required_sp_id, $value['id']);   
                        }
                    }
                }

                if($value['status'] == SPARE_PARTS_REQUESTED){
                    $date1=date_create($value['date_of_request']);
                    $date2=date_create(date('Y-m-d'));
                    $diff=date_diff($date1,$date2);
                    $d = $diff->format("%R%a days");
                    
                    if($diff->format("%R%a days") < 15){
                       $requestedParts = true;
                    } else{
                        array_push($can_sp_id, array('part_name' => $value['parts_requested'], "part_id" => $value['id'], 
                            'requested_inventory_id' => $value['requested_inventory_id'], 'entity_type' => $value['entity_type'], 'partner_id' => $value['partner_id']));
                    }
                }
            }

        }?>
        <center><?php if($requestedParts) { ?><span style="color:red; font-weight: bold;" ><?php echo UNABLE_COMPLETE_BOOKING_SPARE_MSG;?></span><?php } ?></center>
        <div class="panel panel-info" style="margin-top:20px;">
            <div class="panel-heading">Complete Booking </div>
            <div class="panel-body">
                <form name="myForm" onSubmit="document.getElementById('submitform').disabled=true;" class="form-horizontal" id ="booking_form" action="<?php echo base_url() ?>employee/service_centers/process_complete_booking/<?php echo $booking_id; ?>"  method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="col-md-4">Booking ID</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="booking_id" name="booking_id" value = "<?php
                                            if (isset($booking_history[0]['booking_id'])) {
                                                echo $booking_history[0]['booking_id'];
                                            }
                                            ?>" readonly="readonly">
                                       
                                    </div>
                                    <div class="col-md-6">
                                        <input type="hidden" class="form-control" id="appliance_id" name="appliance_id" value = "<?php
                                            if (isset($booking_history[0]['service_id'])) {
                                                echo $booking_history[0]['service_id'];
                                            }
                                            ?>" readonly="readonly">
                                       
                                </div>
                                </div>
                                <div class="form-group">
                                    <label for="name" class="col-md-4">Name</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="name" name="user_name" value = "<?php
                                            if (isset($booking_history[0]['name'])) {
                                                echo $booking_history[0]['name'];
                                            }
                                            ?>" readonly="readonly">
                                    </div>
                                </div>
                                <?php if(isset($booking_history[0]['onlinePaymentAmount'])) { ?>
                                <div class="form-group">
                                    <label for="Customer paid Through Paytm" class="col-md-4">Customer Paid Through Paytm</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="customer_paid_through_paytm" name="customer_paid_through_paytm" value = "<?php
                                            if (isset($booking_history[0]['onlinePaymentAmount'])) {
                                                echo $booking_history[0]['onlinePaymentAmount'];
                                            }
                                            ?>" readonly="readonly">
                                    </div>
                                </div>
                                <?php } else { ?>
                                    <input type="hidden" class="form-control" id="customer_paid_through_paytm" name="customer_paid_through_paytm" value = "0" readonly="readonly">
                                <?php } ?>
                                
                                <!--  end col-md-6  -->
                            </div>
                            <!--  start col-md-6  -->
                            <div class="col-md-6">
                                
                                <div class="form-group ">
                                    <label for="booking_primary_contact_no" class="col-md-4">Mobile *</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control"  id="booking_primary_contact_no" name="booking_primary_contact_no" value = "<?php
                                            if (isset($booking_history[0]['booking_primary_contact_no'])) {
                                            echo $booking_history[0]['booking_primary_contact_no'];
                                            }
                                            ?>" readonly="readonly">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="service_name" class="col-md-4">Appliance *</label>
                                    <div class="col-md-6">
                                        <input type="hidden" name="service" id="services"/>
                                        <select type="text" disabled class="form-control"  id="service_id" name="service_id" >
                                            <option value="<?php
                                                if (isset($booking_history[0]['service_id'])) {
                                                echo $booking_history[0]['service_id'];
                                                }
                                                ?>" selected="selected" disabled="disabled"><?php
                                                if (isset($booking_history[0]['services'])) {
                                                    echo $booking_history[0]['services'];
                                                }
                                                ?></option>
                                        </select>
                                    </div>
                                </div>
                                <!-- end col-md-6 -->
                            </div>
                        </div>
                    </div>
                    <!-- row End  -->
                    
                    <input type="hidden" id="spare_parts_required" name="spare_parts_required" value="<?php echo $flag;?>" />
                    <input type="hidden" name="sp_required_id" value='<?php echo json_encode($required_sp_id,TRUE); ?>' />
                    <input type="hidden" name="can_sp_required_id" value='<?php echo json_encode($can_sp_id,TRUE); ?>' />
                    <input type="hidden" name="partner_id" value='<?php echo $booking_history[0]['partner_id']; ?>' />
                    <input type="hidden" name="user_id" value='<?php echo $booking_history[0]['user_id']; ?>' />
                    <input type="hidden" name="approval" value='0' />
                    <input type="hidden" name="count_unit" id ="count_unit" value="<?php echo count($bookng_unit_details);?>" />
                    <input type="hidden" name="mismatch_pincode" id="mismatch_pincode" value="<?php if(isset($mismatch_pincode)) { echo $mismatch_pincode; }?>" />
                    <?php $count = 0; foreach ($bookng_unit_details as $key1 => $unit_details) { ?>
                    <div class="clonedInput panel panel-info " id="clonedInput1">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div <?php if($this->session->userdata('is_engineer_app') == 1){?> class="col-md-12" <?php } else { ?> class="col-md-12" <?php } ?> >
                                        <div class="form-group col-md-3" style="<?php if($this->session->userdata('is_engineer_app') == 1){?>width:26.32%;
                                            <?php } else {?> width:20.32%;<?php }?>">
                                            <div class="col-md-12" style="padding-left:0px;">
                                                <label> Product Found Broken</label>
                                                <select type="text" class="form-control appliance_broken" id="<?php echo "broken_".$key1?>" name="broken[]" onchange="check_broken('<?php echo $key1;?>')" >
                                                    <option selected disabled>Product Found Broken</option>
                                                    <option  value="1">Yes</option>
                                                    <option value="0">No</option>
                                                </select>
                                            </div>
                                        </div>
                                        <input type="hidden" id="<?php echo "count_line_item_".$key1;?>" value="<?php echo count($unit_details['quantity']);?>"/>
                                        <div class="form-group col-md-3" style="<?php if($this->session->userdata('is_engineer_app') == 1){?>width:29.32%;
                                            <?php } else {?> width:22.6%;<?php }?>">
                                            <div class="col-md-12 ">
                                                 <label> Brand</label>
                                                <select type="text" disabled="" class="form-control appliance_brand"    name="appliance_brand[]" id="appliance_brand_1" >
                                                    <option selected disabled><?php echo $unit_details['brand']; ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-3" style="width:22.6%">
                                            <div class="col-md-12 ">
                                                <label> Category</label>
                                                <select type="text" disabled="" class="form-control appliance_category"   id="appliance_category_1" name="appliance_category[]"  >
                                                    <option selected disabled><?php echo $unit_details['category']; ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-3"style="width:22.6%" style=" padding-right: 0px;">
                                            <div class="col-md-12">
                                                <label> Capacity</label>
                                                <select type="text" disabled="" class="form-control appliance_capacity"   id="appliance_capacity_1" name="appliance_capacity[]" >
                                                    <?php if (!empty($unit_details['capacity'])) { ?>
                                                    <option selected disabled><?php echo $unit_details['capacity']; ?></option>
                                                    <?php } ?>
                                                </select>
                                                
                                            </div>
                                        </div>
                                        <div class="form-group col-md-3"style="width:21.6%" style=" padding-right: 0px;">
                                            <label> Purchase Date</label>
                                            <div class="input-group input-append date">
                                                <input onchange="update_dop_for_unit('<?php echo $key1?>')"  id="<?php echo "dop_".$key1?>" class="form-control dop" placeholder="Purchase Date" name="dop[]" type="text" value="<?php if(isset($booking_history['spare_parts'])){  echo $booking_history['spare_parts'][0]['date_of_purchase']; } ?>">
                                                        <span class="input-group-addon add-on" onclick="dop_calendar('<?php echo "dop_".$key1?>')"><span class="glyphicon glyphicon-calendar"></span></span>
                                             </div>
                                        </div>
                                        <div class="col-md-12" style="padding-left:0px;">
                                            <table class="table priceList table-striped table-bordered" name="priceList" >
                                                <tr>
                                                    <th style="width:300px;">Model Number</th>
                                                    <th style="width:320px;">Serial Number</th>
                                                    <th>Service Category</th>
                                                    <th>Amount Due</th>
                                                    <th>Customer Basic Charge</th>
                                                    <th>Additional Charge</th>
                                                    <th style="width: 112px;">Parts Cost</th>
                                                    <th style="width:265px;">Status</th>
                                                </tr>
                                                <tbody>
                                                    <?php
                                                        $paid_basic_charges = 0;
                                                        $paid_additional_charges = 0;
                                                        $paid_parts_cost = 0;
                                                        $serial_number = "";
                                                        $serial_number_pic = "";
                                                        foreach ($unit_details['quantity'] as $key => $price) {
                                                            if($price['booking_status'] != _247AROUND_CANCELLED){ 
                                                            ?>
                                                    <tr>
                                                        <td>
                                                            <?php if(isset($price['model_data']) && !empty($price['model_data'])){ ?>
                                                            <select class="form-control model_number" id="<?php echo "model_number_" . $count ?>" name="<?php echo "model_number[" . $price['unit_id'] . "]" ?>"  required="">
                                                                <option value="" selected desa>Please Select Model Number</option>
                                                                <?php foreach ($price['model_data'] as $m) { ?>
                                                                <option value="<?php echo $m['model_number'];?>"><?php echo $m['model_number'];?></option>
                                                                                
                                                                <?php }?>
                                                            </select>
                                                           <?php } else { ?>
                                                            <input type="text" name="<?php echo "model_number[" . $price['unit_id'] . "]" ?>" value="" class="form-control" id="<?php echo "model_number_text_" . $count ?>">
                                                          <?php } ?>
                                                            <input type="hidden" name="<?php echo "appliance_dop[" . $price['unit_id'] . "]" ?>" 
                                                            class="<?php echo "unit_dop_".$key1."_".$key;?>" value="<?php if(isset($booking_history['spare_parts'])){  echo $booking_history['spare_parts'][0]['date_of_purchase']; } ?>" />
                                                        </td>
                                                               
                                                        <td>
                                                            <?php $sr =FALSE; if(isset($price['en_serial_number'])){ if(!empty($price['en_serial_number'])){ $sr = TRUE; }} 
                                                            if ((strpos($price['price_tags'],REPAIR_STRING) !== false) && (strpos($price['price_tags'],IN_WARRANTY_STRING) !== false)) {
                                                                   $dop_mendatory = 1; 
                                                            }
                                                            ?>
                                                            <?php if ($price['pod'] == "1" || !empty($sr)) { ?>
                                                            <div class="form-group">
                                                                <div class="col-md-12">
                                                                    <input type="hidden" id="<?php echo "serial_number_pic" . $count ?>" class="form-control" name="<?php echo "serial_number_pic[" . $price['unit_id'] . "]" ?>" 
                                                                        value="<?php if(isset($price['en_serial_number_pic'])){ echo $price['en_serial_number_pic'];} else {$price["serial_number_pic"];}  ?>" placeholder=""   />
<!--                                                                    onblur="validateSerialNo('<?php //echo $count;?>')" -->
                                                                    <input type="text" style="text-transform: uppercase;" id="<?php echo "serial_number" . $count ?>" onblur="validateSerialNo('<?php echo $count;?>')" class="form-control" name="<?php echo "serial_number[" . $price['unit_id'] . "]" ?>"  
                                                                        value="<?php if(isset($price['en_serial_number'])){ echo $price['en_serial_number'];} else {$price["serial_number"];}  ?>" placeholder="Enter Serial No" onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || (event.charCode > 47 && event.charCode < 58) || event.charCode == 8"   />
                                                                    <input type="hidden" id="<?php echo "pod" . $count ?>" class="form-control" name="<?php echo "pod[" . $price['unit_id'] . "]" ?>" value="<?php echo $price['pod']; ?>"   />
                                                                    <input type="hidden" id="<?php echo "sno_required" . $count ?>" class="form-control" name="<?php echo "is_sn_file[" . $price['unit_id'] . "]" ?>" value="0"   />
                                                                    <input type="hidden" id="<?php echo "duplicate_sno_required" . $count ?>" class="form-control" name="<?php echo "is_dupliacte[" . $price['unit_id'] . "]" ?>" value="0"   />
                                                                    <input type="hidden" id="<?php echo "is_sn_correct" . $count ?>" class="form-control" name="<?php echo "is_sn_correct[" . $price['unit_id'] . "]" ?>"/>
                                                                    <br/>
                                                                    <span style="color:red;" id="<?php echo 'error_serial_no'.$count;?>"></span>
                                                                    <input style="margin-top: 10px;" type="file" id="<?php echo "upload_serial_number_pic" . $count ?>"   class="form-control" name="<?php echo "upload_serial_number_pic[" . $price['unit_id'] . "]" ?>"   />
                                                                </div>
                                                                
                                                            </div>
                                                            <?php } ?>
                                                        </td>
                                                        <td id="<?php echo "price_tags".$count; ?>"><?php echo $price['price_tags']; ?></td>
                                                        <td id="<?php echo "amount_due".$count; ?>"><?php echo $price['customer_net_payable']; ?></td>
                                                        <td>  
                                                            <input type="hidden" name="<?php echo "price_tags[" . $price['unit_id'] . "]" ?>" value="<?php echo $price['price_tags'];?>">
                                                            <?php if($price['product_or_services'] != "Product"){  ?>
                                                            <input  id="<?php echo "basic_charge".$count; ?>" type="<?php  if (($price['product_or_services'] == "Service" 
                                                                && $price['customer_net_payable'] == 0) ){ echo "hidden";} ?>" 
                                                                class="form-control cost"  name="<?php echo "customer_basic_charge[" . $price['unit_id'] . "]" ?>"  value = "0">
                                                            <?php } ?>
                                                        </td>
                                                        <td>  <input id="<?php echo "extra_charge".$count; ?>"  type="<?php  if ($price['product_or_services'] == "Product") { 
                                                            echo "hidden";} else { echo "text";} ?>" class="form-control cost"  
                                                            name="<?php echo "additional_charge[" . $price['unit_id'] . "]" ?>"  
                                                            value = "0">
                                                        </td>
                                                        <td>  
                                                            <?php if($price['product_or_services'] != "Service"){  ?>
                                                            <input  id="<?php echo "basic_charge".$count; ?>" type="<?php if ($price['product_or_services'] == "Product"
                                                                && $price['customer_net_payable'] > 0){ echo "text"; } 
                                                                else { echo "hidden";}?>" class="form-control cost" 
                                                                name="<?php echo "customer_basic_charge[" . $price['unit_id'] . "]" ?>"  value = "0">
                                                            <?php } ?>
                                                            <input id="<?php echo "parts_cost".$count; ?>"  type="<?php if($price['product_or_services'] != "Service"){ 
                                                                if ($price['product_or_services'] == "Product" && $price['customer_net_payable'] == 0) { 
                                                                    echo "text";} else { echo "hidden";} } else { echo "text";}?>" 
                                                                class="form-control cost" 
                                                                name="<?php echo "parts_cost[" . $price['unit_id'] . "]" ?>"  value = "0" >
                                                        </td>
                                                        <input type="hidden" name="<?php echo "appliance_broken[" . $price['unit_id'] . "]" ?>" 
                                                            class="<?php echo "is_broken_".$count;?>" value="" />
                                                        <td>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="form-group ">
                                                                        <div class="col-md-12">
                                                                            <div class="radio">
                                                                                <label><input onclick="check_broken('<?php echo $key1;?>')" class="<?php echo "completed_".$key."_".$key1;?>" type="radio"  name="<?php echo "booking_status[" . $price['unit_id'] . "]" ?>"  value="Completed" <?php
                                                                                    if ($price['booking_status'] == "Completed") {
                                                                                    echo "checked";
                                                                                    }
                                                                                    ?> id="<?php echo "completed_" . $price['pod'] . "_" . $count; ?>" required ><?php
                                                                                    if ($price['product_or_services'] == "Product") {
                                                                                    echo " Delivered";
                                                                                    } else {
                                                                                    echo " Completed";
                                                                                    }
                                                                                    ?><br/>
                                                                                <input onclick="check_broken('<?php echo $key1;?>')" class="<?php echo "cancelled_".$key."_".$key1;?>" type="radio" id="<?php echo "cancelled_" . $price['pod'] . "_" . $count; ?>" name="<?php echo "booking_status[" . $price['unit_id'] . "]" ?>"  value="Cancelled" <?php
                                                                                    if ($price['booking_status'] == "Cancelled") {
                                                                                    echo "checked";
                                                                                    }
                                                                                    ?>  required><?php
                                                                                    if ($price['product_or_services'] == "Product") {
                                                                                       echo " Not Delivered";
                                                                                    } else {
                                                                                       echo " Not Completed";
                                                                                    }
                                                                                    ?>
                                                                                </label>
                                                                                
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                        if(isset($price['en_serial_number'])){ if(!empty($price['en_serial_number'])){ 
                                                            $serial_number = $price['en_serial_number'];
                                                            $serial_number_pic = $price['en_serial_number_pic'];
                                                        }}
                                                         $count++;
                                                          
                    } }
                                                          ?>
                                                </tbody>
                                            </table>
                                            <span class="error_msg" style="color: red"></span>
                                        </div>
                                    </div>
                                    <?php  if($this->session->userdata('is_engineer_app') == 1){?>
                                    <div class="col-md-4">
                                        <?php if(!empty($serial_number)){ ?>
                                        <div class="col-md-12">
                                            <div class="col-md-12 page-header" style="margin: 0px 0 0px;">
                                                Serial Number:  <b><?php echo $serial_number;?></b>
                                            </div>
                                           
                                        </div>
                                        <?php } ?>
                                        <div class="col-md-6" style="padding-left:0px; margin-top: 10px; padding-left: 15px;">
                                            <?php if(!empty($serial_number_pic)){ ?>
                                            <a target="_blank" href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY;?>/engineer-uploads/<?php echo $serial_number_pic;?>">   
                                            <img style="height:150px; width:150px; " src="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY;?>/engineer-uploads/<?php echo $serial_number_pic;?>" />
                                            </a>
                                            <?php } ?>
                                        </div>
                                        <div class="col-md-6" style="padding-left:0px; margin-top: 10px; padding-left: 15px;">
                                            <?php if(!empty($signature)){ ?><a target="_blank" href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY;?>/engineer-uploads/<?php echo $signature;?>">   
                                            <img style="height:150px;width:150px;  <?php if(!empty($signature)){ ?>border: 1px solid;<?php } ?>" src="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY;?>/engineer-uploads/<?php echo $signature;?>" /></a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <div class="row">
                        <div class ="col-md-12">
                            <div class="form-group col-md-6" style=" margin-left:-29px;">
                                <label for="type" class="col-md-12">Total Customer Paid</label>
                                <div class="col-md-12">
                                    <div class="input-group">
                                        <div class="input-group-addon">Rs.</div>
                                        <input  type="text" class="form-control"  name="grand_total_price" id="grand_total_price" value="<?php echo $paid_basic_charges + $paid_additional_charges + $paid_parts_cost; ?>" placeholder="Total Price" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="remark" class="col-md-12">Symptom *</label>
                                <div class="col-md-12" >
                                    <select  class="form-control" name="closing_symptom" id="technical_problem" onchange="update_defect()" <?php if(!empty($technical_problem)){ echo "required";} ?>>
                                        <option value="" selected="" disabled="">Please Select Symptom</option>
                                        <?php foreach ($technical_problem as $value) { 
                                            $selected=(($value['id'] == 1) ? 'selected' :''); //$booking_symptom[0]['symptom_id_booking_creation_time'] ?>
                                        <option value="<?php echo $value['id']?>" <?=$selected?> ><?php echo $value['symptom']; ?></option>
                                         
                                    <?php }?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group col-md-6" style=" margin-left:-29px;">
                                <label for="remark" class="col-md-12">Defect *</label>
                                <div class="col-md-12" >
                                    <select  class="form-control" name="closing_defect" id="technical_defect" onchange="update_solution()" required >
                                        <option value="" selected="" disabled="">Please Select Defect</option>
                                        <?php foreach ($technical_defect as $value) { 
                                            $selected=(($value['defect_id'] == 1) ? 'selected' :''); ?>
                                        <option value="<?php echo $value['defect_id']?>" <?=$selected?> ><?php echo $value['defect']; ?></option>
                                         
                                    <?php }?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="remark" class="col-md-12">Solution *</label>
                                <div class="col-md-12" >
                                    <select class="form-control" name="technical_solution" id = "technical_solution" disabled required >
                                        <option value="" selected="" disabled="">Please Select Solution</option>
                                        <?php if($technical_problem[0]['id'] == 1) { ?>
                                        <option value="1" selected>Default</option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <?php 
                        if($booking_history[0]['is_upcountry'] == '1' 
                                && $booking_history[0]['upcountry_paid_by_customer']== '1' ){ ?>
                        <div class="col-md-12">
                            <div class="form-group col-md-6" style=" margin-left:-29px;">
                                <label for="type" class="col-md-12">Upcountry Charges Paid By Customer</label>
                                <div class="col-md-12">
                                    <div class="input-group">
                                        <div class="input-group-addon">Rs.</div>
                                        <input  type="text" class="form-control cost" name="upcountry_charges" id="upcountry_charges" value="<?php echo "0";?>" placeholder="Enter Upcountry Charges Paid By Customer">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } else { ?>
                        <input  type="hidden" class="form-control cost" name="upcountry_charges" id="upcountry_charges" value="<?php echo "0";?>" placeholder="Enter Upcountry Charges Paid By Customer">
                        <?php } ?>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group col-md-6" style=" margin-left:-29px;">
                                <label for="remark" class="col-md-12">Booking Remarks</label>
                                <div class="col-md-12" >
                                    <textarea class="form-control"  rows="2" name="booking_remarks" readonly><?php
                                        if (isset($booking_history[0]['booking_remarks'])) {
                                            echo str_replace("<br/>", "&#13;&#10;", $booking_history[0]['booking_remarks']);
                                        }
                                        ?></textarea>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="remark" class="col-md-12">Closing Remarks</label>
                                <div class="col-md-12" >
                                    <textarea class="form-control"  rows="2" name="closing_remarks" id="closing_remarks" required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group  col-md-12" >
                        <?php if($requestedParts) { ?>
                         <center style="margin-top:60px; font-weight: bold;">
                            <?php echo UNABLE_COMPLETE_BOOKING_SPARE_MSG;?>
                         </center>
                        <?php } else { ?>
                            <center style="margin-top:60px;">
                                <input type="submit" id="submitform"  onclick="return onsubmit_form('<?php echo $booking_history[0]['upcountry_paid_by_customer']; ?>', '<?php echo $count; ?>', '<?php echo count($bookng_unit_details)?>')" class="btn btn-lg" style="background-color: #2C9D9A;
                                border-color: #2C9D9A; color:#fff;" value="Complete Booking">
                            </center>
                        <?php }?>
                    </div>
                    
            </div>
            </form>
            <!-- end Panel Body  -->
        </div>
    </div>
</div>
</div>
<script>
    $("#service_id").select2();
    $("#booking_city").select2();
    
    
    $(document).ready(function() {
        //called when key is pressed in textbox
        $(".cost").keypress(function(e) {
            //if the letter is not digit then display error and don't type anything
            if (e.which !== 8 && e.which !== 0 && (e.which < 48 || e.which > 57)) {
                //display error message
                $(".error_msg").html("Digits Only").show().fadeOut("slow");
                return false;
            }
        });
        if($('#technical_solution').val() == 1)
            $('#technical_solution').removeAttr('disabled');
    });
    
    
    $(document).on('keyup', '.cost', function(e) {
    
        var price = 0;
        $("input.cost").each(function() {
            price += Number($(this).val());
    
        });
    
        $("#grand_total_price").val(price);
    });
    
    function update_defect(){
        var technical_problem = $("#technical_problem").val();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() ?>employee/service_centers/get_defect_on_symptom',
            data:{technical_problem:technical_problem},
            success: function (response) {
                $('#technical_solution').attr('disabled',true);
                $('#technical_defect').empty();
                $('#technical_solution').empty();
                response=JSON.parse(response);
                var str="<option value='' selected='' disabled=''>Please Select Defect</option>";
                if(response.length>0)
                {
                    for(var i=0;i<response.length;i++)
                    {
                        str+="<option value="+response[i]['defect_id']+" >"+response[i]['defect']+"</option>";
                    }
                }
                $('#technical_defect').append(str);
            }
        });
    }
    
    function update_solution(){
        var technical_symptom = $("#technical_problem").val();
        var technical_defect = $("#technical_defect").val();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() ?>employee/service_centers/get_solution_on_symptom_defect',
            data:{technical_symptom:technical_symptom,technical_defect:technical_defect},
            success: function (response) {
                $('#technical_solution').removeAttr('disabled');
                $('#technical_solution').empty();
                response=JSON.parse(response);
                var str="<option value='' selected='' disabled=''>Please Select Solution</option>";
                if(response.length>0)
                {
                    for(var i=0;i<response.length;i++)
                    {
                        str+="<option value="+response[i]['solution_id']+" >"+response[i]['technical_solution']+"</option>";
                    }
                }
                $('#technical_solution').append(str);
            }
        });
    }
    
    function onsubmit_form(upcountry_flag, number_of_div, appliance_count) {
    
        var flag = 0;
        var div_count = 0;
        var is_completed_checkbox = [];
        var serial_number_tmp = [];
       
        for(m= 0; m< Number(appliance_count); m++){
            var isbroken = $("#broken_"+ m).val();
          
            if(isbroken === null){
                alert("Please Select Is Broken DropDown");
                document.getElementById("broken_"+ m).style.borderColor = "red";
                flag = 1;
                return false;
            }
           <?php if($dop_mendatory ==1){ ?>
            var dop = $("#dop_0").val();
            if(dop === ""){
                    alert("Purchase Date Should Not Blank For Repair Call");
                    return false; 
              }  
        <?php } ?>
        }
        var prediv = -1;
        $(':radio:checked').each(function(i) {
            div_count = div_count + 1;
        
            //console.log($(this).val());
            var div_no = this.id.split('_');
            is_completed_checkbox[i] = div_no[0];
            if (div_no[0] === "completed") {
                
                //if POD is also 1, only then check for serial number.
                if (div_no[1] === "1") {
                   
                    var completedRadioButton = document.getElementById(this.id);
                    
                    var className = completedRadioButton.className;
                    var appdiv = Number(className.split('_')[2]);
                    
                    var serial_number = $("#serial_number" + div_no[2]).val();
                    if($("#model_number_" + div_no[2]).length !== 0) {
                        var model_number = $("#model_number_" + div_no[2]).val();
                        if(model_number === ""){
                            alert("Please Select Model Number");
                            document.getElementById('model_number_' + div_no[2]).style.borderColor = "red";
                            flag = 1;
                            return false;
                        }
                    }
                    else{
                        model_text_value = $("#model_number_text_" + div_no[2]).val();
                        if(model_text_value ===""){
                              alert("Model Number is blank");
                              document.getElementById('model_number_text_' + div_no[2]).style.borderColor = "red";
                              flag = 1;
                              return false;
                        }
                    }
                  
                    if(prediv !== appdiv){
                      
                        prediv = appdiv;
                        serial_number_tmp.push(serial_number);
                    }
                    
                    if (serial_number === "") {
    
                        document.getElementById('serial_number' + div_no[2]).style.borderColor = "red";
                        flag = 1;
                        
                    }
    
                    if (serial_number === "0") {
                        document.getElementById('serial_number' + div_no[2]).style.borderColor = "red";
                        flag = 1;
                        
                    }
    
                    var numberRegex = /^[+-]?\d+(\.\d+)?([eE][+-]?\d+)?$/;
                    if (numberRegex.test(serial_number)) {
                        if (serial_number > 0) {
                            flag = 0;
                        } else {
                            
                            document.getElementById('serial_number' + div_no[2]).style.borderColor = "red";
                            flag = 1;
                            
                        }
                    }
                    //var requiredPic = $('#sno_required'+ div_no[2]).val();
                   // if(requiredPic === '1'){
                        if( document.getElementById("upload_serial_number_pic"+div_no[2]).files.length === 0 ){
                            alert('Please Attach Serial Number image');
                            document.getElementById('upload_serial_number_pic' + div_no[2]).style.borderColor = "red";
                            flag = 1;
                            return false;
                        }
                  //  }
                    var duplicateSerialNo = $('#duplicate_sno_required'+ div_no[2]).val();
                    if(duplicateSerialNo === '1'){
                        alert('<?php echo DUPLICATE_SERIAL_NUMBER_USED;?>');
                        document.getElementById('serial_number' + div_no[2]).style.borderColor = "red";
                        $("#error_serial_no" +div_no[2]).html('<?php echo DUPLICATE_SERIAL_NUMBER_USED;?>');
                        flag = 1;
                    }
                }
               
                var amount_due = $("#amount_due" + div_no[2]).text();
                var price_tags = $("#price_tags" + div_no[2]).text();
                var basic_charge = $("#basic_charge" + div_no[2]).val();
                var additional_charge = $("#extra_charge" + div_no[2]).val();
                var parts_cost = $("#parts_cost" + div_no[2]).val();
                if (Number(amount_due) > 0) {
                   
                    var total_sf = Number(basic_charge) + Number(additional_charge) + Number(parts_cost);
                    if (Number(total_sf) === 0) {
                       
                        alert("Please fill amount collected from customer, Amount Due: Rs." + amount_due);
                        flag = 1;
                        
                    }
                    
                    if(price_tags === '<?php echo REPAIR_OOW_PARTS_PRICE_TAGS;?>'){
                       
                        if(Number(basic_charge) < Number(amount_due)){
                           alert("Please fill amount collected from customer, Amount Due: Rs." + amount_due);
                           //flag = 1;
                           }
                             
                    }
                     
                }
                  
            } else if(div_no[0] === "cancelled"){
                var price_tags = $("#price_tags" + div_no[2]).text();
                var amount_due = $("#amount_due" + div_no[2]).text();
                if(price_tags === '<?php echo REPAIR_OOW_PARTS_PRICE_TAGS;?>'){
                    alert("You can not mark as a not delivered of Spare Parts. fill amount collected from customer, Amount Due: Rs." + amount_due);
                    flag = 1;
                    return false;
                }
            }
        });
 
        if (Number(number_of_div) !== Number(div_count)) {
            alert('Please Select All Services Delivered Or Not Delivered.');
            flag = 1;
            return false;
        }
        if ($.inArray('completed', is_completed_checkbox) !== -1) {
    
        } else {
         
            alert('Please Select atleast one Completed or Delivered checkbox.');
            flag = 1;
            return false;
    
        }
        temp = [];
        $.each(serial_number_tmp, function(key, value) {
            if ($.inArray(value, temp) === -1) {
                temp.push(value);
            } else {
                alert(value + " is a Duplicate Serial Number");
                flag = 1;
                return false;
            }
        });
    
        var is_sp_required = $("#spare_parts_required").val();
        
        if (Number(is_sp_required) === 1) {
           
            alert("Ship Defective Spare Parts");
        }
    
        if (Number(upcountry_flag) === 1) {
            var upcountry_charges = $("#upcountry_charges").val();
            if (Number(upcountry_charges) === 0) {
                flag = 1;
                document.getElementById('upcountry_charges').style.borderColor = "red";
                alert("Please Enter Upcountry Charges which Paid by Customer");
                return false;
            } else if (Number(upcountry_charges) > 0) {
                flag = 0;
                document.getElementById('upcountry_charges').style.borderColor = "green";
            }
        }
        <?php if(!empty($technical_problem)){ ?>
            var technical_problem = $("#technical_problem").val();
            if(technical_problem === null){
                alert('Please Select Symptom');
                document.getElementById('technical_problem').style.borderColor = "red";
                flag = 1;
                return false;
            }
        <?php } ?>
            
        <?php if(!empty($technical_defect)){ ?>
            var technical_defect = $("#technical_defect").val();
            
            if(technical_defect === null){
                alert('Please Select Defect');
                document.getElementById('technical_defect').style.borderColor = "red";
                flag = 1;
                return false;
            }
        <?php } ?>
        if($("#technical_solution").val() != '') {
            var technical_solution = $("#technical_solution").val();
            
            if(technical_solution === null){
                alert('Please Select Solution');
                document.getElementById('technical_solution').style.borderColor = "red";
                flag = 1;
                return false;
            }
        }
        var closing_remarks = $("#closing_remarks").val();
        if (closing_remarks === "") {
            alert("Please Enter Remarks");
            document.getElementById('closing_remarks').style.borderColor = "red";
            flag = 1;
            return false;
        }
        
        var customer_paid_through_paytm = Number($("#customer_paid_through_paytm").val());
        if(customer_paid_through_paytm > 0){
            var grand_total_price = $("#grand_total_price").val();
            
            if(grand_total_price < customer_paid_through_paytm){
                alert("Please fill correct amount collected from customer");
                flag = 1;
                return false;
            }
        }
        
        if (flag === 0) {
            $('#submitform').val("Please wait.....");
            return true;
    
        } else if (flag === 1) {
    
            return false;
        }
    }
</script>
<style type="text/css">
    .panel-info>.panel-heading {
    color: #fff;
    background-color: #2C9D9A;
    border-color: #2C9D9A;
    }
    .panel-info {
    border-color: #bce8f1;
    }
    #booking_form .form-group label.error {
    color: #FB3A3A;
    display: inline-block;
    margin: 42px 0 0px 0px;
    padding: 0;
    text-align: left;
    width: 240px;
    position: absolute;
    }
</style>
<script type="text/javascript">
    (function ($, W, D)
    {
    var JQUERY4U = {};
    
    JQUERY4U.UTIL =
    {
      setupFormValidation: function ()
      {
      
    //form validation rules
    $("#booking_form").validate({
       rules: {
    booking_status: "required",
    serial_number: "required",
    closing_remarks: "required"
       },
       messages: {
    booking_status: "Please select on of these button ",
    serial_number: " Please Enter Serial Number",
    closing_remarks: "Please Enter Remarks"
    
       },
       submitHandler: function (form) {
    form.submit();
       }
    });
      }
    };
    
    //when the dom has loaded setup form validation rules
    $(D).ready(function ($) {
     JQUERY4U.UTIL.setupFormValidation();
    });
    
    })(jQuery, window, document);
    
    function update_dop_for_unit(div){
          var div_item_count = $("#count_line_item_"+div).val();
          var dopValue = $("#dop_"+div).val();
            for(i = 0; i < Number(div_item_count); i++ ){
                $(".unit_dop_"+div+"_"+i).val(dopValue);
         }
    }
    function check_broken(div){
        
        var broken = Number($("#broken_"+ div).val());
      
        var div_item_count = $("#count_line_item_"+div).val();
        var count_unit = $("#count_unit").val();
        var no  = 0;
        
           for(k =0; k< Number(count_unit); k++){
            for(i = 0; i < Number(div_item_count); i++ ){

                if(broken === 1){
                    $(".is_broken_"+no).val("1");
                } else {

                     $(".is_broken_"+no).val("0");
                }

               var amount_due = Number($("#amount_due" + no).text());
               var price_tags = $("#price_tags" +no ).text();
               if(amount_due === 0 && price_tags.indexOf("Wall Mount Stand") > -1 && broken === 1){

                    $(".cancelled_"+i+"_"+div).prop('checked', true);
               }
                   
               no++;
            }
        }
    }
    
    function validateSerialNo(index){
       var model_number = '';
       var temp = $("#serial_number" +index).val();
       var serialNo =  temp.toUpperCase();
       $("#serial_number" +index).val(serialNo);
       var price_tags = $("#price_tags"+index).text();
       if(<?php echo $booking_history[0]['partner_id'] ?> == <?php echo LEMON_ID ?>){
            var model_number = $("#model_number_"+index).val();
            if(!model_number){
                alert("Please Select Model Number");
                $("#serial_number" +index).val("");
                return false;
            }
       }
       if(serialNo !== ''){
            $.ajax({
                type: 'POST',
                beforeSend: function(){

                    $('body').loadingModal({
                    position: 'auto',
                    text: 'Loading Please Wait...',
                    color: '#fff',
                    opacity: '0.7',
                    backgroundColor: 'rgb(0,0,0)',
                    animation: 'wave'
                });

                    },
                url: '<?php echo base_url() ?>employee/service_centers/validate_booking_serial_number',
                data:{serial_number:serialNo,model_number:model_number,partner_id:'<?php echo $booking_history[0]['partner_id'];?>',appliance_id:'<?php echo $booking_history[0]['service_id'];?>', price_tags:price_tags,
                user_id: '<?php echo $booking_history[0]['user_id'];?>', 'booking_id': '<?php echo $booking_history[0]['booking_id'];?>'},
                success: function (response) {
                    var is_block = false;
                    var data = jQuery.parseJSON(response);
                    if(data.code === 247){
                        $('body').loadingModal('destroy');
                        //$("#upload_serial_number_pic"+index).css('display', "none");
                        $("#error_serial_no" +index).text("");
                        $("#sno_required"+index).val('0');
                        $("#duplicate_sno_required"+index).val('0');
                        if(data.notdefine===1)
                        {
                             $("#is_sn_correct"+index).val('2');
                        }
                        else
                        {
                        $("#is_sn_correct"+index).val('1');
                        }
                    } else if(data.code === Number(<?php echo DUPLICATE_SERIAL_NO_CODE; ?>)){
                        $("#duplicate_sno_required"+index).val('1');
                        $("#error_serial_no" +index).html(data.message);
                        $('body').loadingModal('destroy');
                        
                    } else {
                        if(data.message == '<?php echo REPEAT_BOOKING_FAILURE_MSG?>'){
                             is_block = true;
                        }
                        $("#sno_required"+index).val('1');
                        $("#error_serial_no" +index).html(data.message);
                       // $("#upload_serial_number_pic"+index).css('display', "block");
                        $("#duplicate_sno_required"+index).val('0');
                        $('body').loadingModal('destroy');
                    }
                    if(is_block){
                        $("#submitform").hide();
                    }
                    else{
                        $("#submitform").show();
                    }
                }
            });
       } else {
       
           // $("#upload_serial_number_pic"+index).css('display', "none");
            $("#error_serial_no" +index).text("");
            $("#sno_required"+index).val('0');
       }
    }
    function dop_calendar(id){
         $("#"+id).datepicker({
             dateFormat: 'yy-mm-dd', 
             changeMonth: true,
             changeYear: true,
             maxDate:0
         }).datepicker('show');
    }
</script>
