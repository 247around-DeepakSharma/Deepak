<?php $required_sp_id = array(); ?>
<?php
    $flag = 0;
    if (isset($booking_history['spare_parts'])) {
    
        foreach ($booking_history['spare_parts'] as $value) {
            if ($value['status'] == "Completed" || $value['status'] == "Cancelled") {
                
            } else {
                if ($value['defective_part_required'] == 1) {
                    if (!empty($value['parts_shipped'])) {
                        $flag = 1;
                        array_push($required_sp_id, $value['id']);
                    }
                }
            }
        }
    }
    ?>
<form name="myForm" class="form-horizontal" id ="booking_form" action="<?php echo base_url() ?>employee/service_centers/process_complete_booking/<?php echo $booking_id; ?>" onSubmit="document.getElementById('submitform').disabled=true;" method="POST" enctype="multipart/form-data">
<input type="hidden" id="spare_parts_required" name="spare_parts_required" value="<?php echo $flag; ?>" />
<input type="hidden" name="sp_required_id" value='<?php echo json_encode($required_sp_id, TRUE); ?>' />
<input type="hidden" name="partner_id" value='<?php echo $booking_history[0]['partner_id']; ?>' />
<input type="hidden" name="approval" value='1' />
<input type="hidden" name="amount_paid" id="amount_paid" value="<?php echo $booking_history[0]["amount_paid"];?>" />
<input type="hidden" name="mismatch_pincode" id="mismatch_pincode" value="<?php echo $mismatch_pincode;?>" />
<div class="" >
    <div class="panel panel-info">
        <div class="panel-heading">Approve Booking (Amount Paid By Customer <strong><i class="fa fa-inr" aria-hidden="true"></i><?php echo $amount_paid;?></strong>)
                    <button type="button" class="close" data-dismiss="modal">&times;</button>

        </div>
        <div class="panel-body">
            <div class="col-md-12">
                  <?php $count = 0; foreach ($bookng_unit_details as $key => $unit_details) { ?>
                    <div class="clonedInput panel panel-info " id="clonedInput1">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-8">
                                       
                                        <div class="form-group col-md-4" style="width:28%;">
                                        <div class="col-md-12">
                                           <label > Product Found Broken</label>
                                            <select type="text" class="form-control appliance_broken"   id="broken" name="broken[]" required>
                                                <option selected disabled>Product Found Broken</option>
                                                <option <?php if($unit_details['is_broken'] == 1){ echo "selected";}?> >Yes</option>
                                                <option <?php if($unit_details['is_broken'] == 0){ echo "selected";}?>>No</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4" style="width:29%;">
                                        <div class="col-md-12 ">
                                            <label > Brand</label>
                                            <select type="text" disabled="" class="form-control appliance_brand"    name="appliance_brand[]" id="appliance_brand_1" >
                                                <option selected disabled><?php echo $unit_details['brand']; ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4" style="width:29%;">
                                        <label > Category</label>
                                        <div class="col-md-12 ">
                                            <select type="text" disabled="" class="form-control appliance_category"   id="appliance_category_1" name="appliance_category[]"  >
                                                <option selected disabled><?php echo $unit_details['category']; ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group col-md-4" style=" padding-right: 0px;width:26%;">
                                        <div class="col-md-12">
                                            <?php if (!empty($unit_details['capacity'])) { ?>
                                            <label > Capacity</label>
                                            <select type="text" disabled="" class="form-control appliance_capacity"   id="appliance_capacity_1" name="appliance_capacity[]" >
                                                <option selected disabled><?php echo $unit_details['capacity']; ?></option>
                                            </select>
                                             <?php } ?>
                                        </div>
                                    </div>
                                    
                                   
                               
                                        
                                   <div class="col-md-12">
                                    <table class="table priceList table-striped table-bordered" name="priceList" >
                                        <tr>
                                            <th style="width:375px;">Enter Serial Number</th>
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
                                                foreach ($unit_details['quantity'] as $key => $price) {
                                                 
                                                    ?>
                                            <tr>
                                                <td>
                                                    <?php if ($price['pod'] == "1" || !empty($price['en_serial_number'])) { ?>
                                                    <div class="form-group">
                                                        <div class="col-md-12">
                                                            <input type="text" id="<?php echo "serial_number" . $count ?>" class="form-control" name="<?php echo "serial_number[" . $price['unit_id'] . "]" ?>" value="<?php echo $price['en_serial_number']; ?>" placeholder="Enter Serial No" required  />
                                                            <input type="hidden" id="<?php echo "serial_number_pic" . $count ?>" class="form-control" name="<?php echo "serial_number_pic[" . $price['unit_id'] . "]" ?>" value="<?php echo $price['en_serial_number_pic']; ?>" placeholder=""   />
                                                            <input type="hidden" id="<?php echo "pod" . $count ?>" class="form-control" name="<?php echo "pod[" . $price['unit_id'] . "]" ?>" value="<?php echo $price['pod']; ?>"   />
                                                        </div>
                                                    </div>
                                                    <?php } ?>
                                                </td>
                                                <td id="<?php echo "price_tags".$count; ?>"><?php echo $price['price_tags'] ?></td>
                                                <td id="<?php echo "amount_due".$count; ?>"><?php echo $price['customer_net_payable']; ?></td>
                                                <td>  
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
                                                       class="<?php echo "is_broken_".$count;?>" value="<?php echo $price['en_is_broken'];?>"/>
                                                <td>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group ">
                                                                <div class="col-md-10">
                                                                    <div class="radio">
                                                                        <label><input class="radio_button" type="radio"  name="<?php echo "booking_status[" . $price['unit_id'] . "]" ?>"  value="Completed" <?php
                                                                            if ($price['en_internal_status'] == "Completed") {
                                                                            echo "checked";
                                                                            }
                                                                            ?> id="<?php echo "completed_" . $price['pod'] . "_" . $count; ?>" required ><?php
                                                                            if ($price['product_or_services'] == "Product") {
                                                                            echo " Delivered";
                                                                            } else {
                                                                            echo " Completed";
                                                                            }
                                                                            ?><br/>
                                                                        <input class="radio_button" type="radio" id="<?php echo "cancelled_" . $price['pod'] . "_" . $count; ?>" name="<?php echo "booking_status[" . $price['unit_id'] . "]" ?>"  value="Cancelled" <?php
                                                                            if ($price['en_internal_status'] == "Cancelled") {
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
                                                }
                                                ?>
                                        </tbody>
                                    </table>
                                    <span class="error_msg" style="color: red"></span>
                                </div>
                                    </div>
                                    
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
                                            <a target="_blank" href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY;?>/<?php echo SERIAL_NUMBER_PIC_DIR;?>/<?php echo $serial_number_pic;?>">   
                                            <img style="height:150px; width:150px;" src="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY;?>/<?php echo SERIAL_NUMBER_PIC_DIR;?>/<?php echo $serial_number_pic;?>" />
                                            </a>
                                            <?php } ?>
                                        </div>
                                        <div class="col-md-6" style="padding-left:0px; margin-top: 10px; padding-left: 15px; ">
                                            <?php if(!empty($signature)){ ?><a  target="_blank" href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY;?>/engineer-uploads/<?php echo $signature;?>">   
                                            <img style="height:150px; width: 150px; <?php if(!empty($signature)){ ?>border: 1px solid;<?php } ?>" src="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY;?>/engineer-uploads/<?php echo $signature;?>" /></a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                

                            </div>
                        </div>
                    </div>
                    <?php } ?>
                <span class="error_msg" style="color: red"></span>
            </div>
            <div class="row">
                <div class ="col-md-12">
                    <?php if($booking_history[0]['is_upcountry'] == '1' 
                        && $booking_history[0]['upcountry_paid_by_customer']== '1' ){ ?>
                    <div class="form-group col-md-5" >
                        <label for="type" class="col-md-12">Upcountry Charges Paid By Customer</label>
                        <div class="col-md-12">
                            <div class="input-group">
                                <div class="input-group-addon">Rs.</div>
                                <input  type="text" class="form-control cost" name="upcountry_charges" id="upcountry_charges" value="<?php echo "0";?>" placeholder="Enter Upcountry Charges Paid By Customer">
                            </div>
                        </div>
                    </div>
                    <?php } else { ?>
                    <input  type="hidden" class="form-control cost" name="upcountry_charges" id="upcountry_charges" value="<?php echo "0";?>" placeholder="Enter Upcountry Charges Paid By Customer">
                    <?php } ?>
                    <div class="form-group col-md-5">
                        <label for="type" class="col-md-12">Total Customer Paid</label>
                        <div class="col-md-12">
                            <div class="input-group">
                                <div class="input-group-addon">Rs.</div>
                                <input  type="text" class="form-control"  name="grand_total_price" id="grand_total_price" value="<?php echo $paid_basic_charges + $paid_additional_charges + $paid_parts_cost; ?>" placeholder="Total Price" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-top:25px;">
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group col-md-6" >
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
                <center style="margin-top:60px;">
                <input type="submit" id="submitform"  onclick="return onsubmit_form('<?php echo $booking_history[0]['upcountry_paid_by_customer']; ?>', '<?php echo $count; ?>')" class="btn btn-md" style="background-color: #2C9D9A;
                    border-color: #2C9D9A; color:#fff;" value="Complete Booking">
            </div>
        </div>
    </div>
</div>
</form>


<script>
$('.appliance_broken').css('pointer-events','none'); 
$('.radio_button').css('pointer-events','none'); 
</script>