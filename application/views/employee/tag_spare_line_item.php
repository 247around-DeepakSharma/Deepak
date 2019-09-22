<div class="row">
    <div class="col-md-12 col-sm12 col-xs-12">
        <div class="x_title">
            <div class="col-xs-4 col-sm-4 pull-right">
                <button type="button" class="btn btn-default pull-right onspareaddButton" data-count='<?php echo $count;?>' >Add More Spare Item</button>
            </div>
            <div class="clearfix"></div>
        </div>
        <?php foreach ($data as $key => $value) { ?>
        <?php if($key != 0){ ?>
        <script>onBookingIndex++</script>
        <?php } ?>
        <div class="x_panel">
            <div class="x_content">
                <div class="col-md-3">
                   <div class="form-group">
                       
                       <label class="radio-inline col-md-6" style="font-weight:bold">
                           <input type="radio" name="part[<?php echo ($key +$count); ?>][shippingStatus]" required="" class="shippingStatus" id="<?php echo "s_shippingStatus_".($key +$count);?>" value="1">Shipping
                          </label>
                    </div>
                    <div class="form-group">
                       
                        <label class="radio-inline col-md-6" style="font-weight:bold">
                            <input type="radio" name="part[<?php echo ($key +$count); ?>][shippingStatus]" required="" class="shippingStatus" id="<?php echo "n_shippingStatus_".($key +$count);?>" value="0">Not Shipping
                          </label>
                    </div>
                    <div class="form-group">
                       
                         <label class="radio-inline col-md-6" style="font-weight:bold">
                             <input type="radio" name="part[<?php echo ($key +$count); ?>][shippingStatus]" required="" class="shippingStatus" id="<?php echo "l_shippingStatus_".($key +$count);?>" value="-1">To be Shipped
                      </label>
                    </div>
                    <span id="error_<?php echo "shippingStatus_".($key +$count);?>" class="error" style="color: red;"></span>
                   
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="part Name" class="col-md-4">Part Name *</label>
                        <div class="col-md-6">
                            <select onchange="get_part_number_on_booking('<?php echo ($key +$count);?>')" class="form-control part_name" id="<?php echo "onpartName_".($key +$count);?>" 
                                name="part[<?php echo ($key +$count); ?>][part_name]" required="">
                                <option value="" disabled="" selected="">Select Part Name</option>
                                <?php $inventory_id =""; $hsn_code = ""; $gst_rate = ""; $part_name =""; $part_number = ""; $basic_price = ""; 
                                    $total_amount = "";
                                    $type = "";
                                    foreach ($inventory_master_list as $inventory) { ?>
                                <option value="<?php echo $inventory['part_name']; ?>" 
                                    <?php if($value['requested_inventory_id'] == $inventory['inventory_id']){ 
                                        echo "Selected"; $part_name = $inventory['part_name']; 
                                        $part_number = $inventory['part_number'];
                                        $hsn_code = $inventory['hsn_code'];
                                        $gst_rate = $inventory['gst_rate'];
                                        $basic_price=  $inventory['price'];
                                        $inventory_id= $inventory['inventory_id'];
                                        $type = $inventory['type'];
                                        $total_amount = sprintf("%.2f", $basic_price *( 1+ $gst_rate/100));
                                        }?>  ><?php echo $inventory['part_name']; ?></option>
                                <?php } ?>
                            </select>
                            <span id="error_<?php echo "onpartName_".($key +$count);?>" class="error" style="color: red;"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="Basic Price" class="col-md-4">Basic Price</label>
                        <div class="col-md-6">
                            <input type="number" value="<?php echo $basic_price;?>" onkeyup="validateDecimal(this.id, this.value);booking_calculate_total_price('<?php echo $key + $count;?>')" class="form-control allowNumericWithDecimal onpartBasicPrice" id="<?php echo "onpartBasicPrice_".($key + $count);?>" name="part[<?php echo ($key +$count); ?>][part_total_price]"  placeholder=""  required >
                            <label for="<?php echo "onpartBasicPrice_".($key + $count);?>" id="lbl_<?php echo "onpartBasicPrice_".($key + $count);?>" class="error"></label>
                            <span id="error_<?php echo "onpartBasicPrice_".($key + $count);?>" class="error" style="color: red;"></span>
                            <input type="hidden" value="1" class="form-control" id="<?php echo "onquantity_".($key + $count);?>" name="part[<?php echo ($key +$count); ?>][quantity]"  placeholder=""  required readonly="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="GST rate" class="col-md-4">GST Rate</label>
                        <div class="col-md-6">
                            <input type="number" value="<?php echo $gst_rate;?>" onkeyup="booking_calculate_total_price('<?php echo $key + $count;?>')" class="form-control allowNumericWithOutDecimal onpartGstRate" id="<?php echo "onpartGstRate_".($key + $count);?>" 
                                name="part[<?php echo ($key +$count); ?>][gst_rate]"  placeholder="Please Enter GST rate" min="12" max="28"  required >
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="shipped_parts_name" class="col-md-4">Parts Number *</label>
                        <div class="col-md-6">
                            <select required="" onchange="onchange_part_number('<?php echo ($key +$count);?>')" class="form-control spare_parts" 
                                    id="<?php echo "onpartNumber_".($key +$count);?>" 
                                name="part[<?php echo ($key +$count); ?>][part_number]">
                                <option value="" disabled="" selected="">Select Part Number</option>
                                <?php if(!empty($part_number)){ ?>
                                <option data-inventory_id ="<?php echo $inventory_id; ?>" data-gst_rate="<?php echo $gst_rate;?>" 
                                    data-hsn_code="<?php echo $hsn_code;?>" 
                                    data-basic_price ="<?php echo $basic_price;?>" data-total_amount="<?php echo $total_amount;?>" value="<?php echo $part_number;?>" selected><?php echo $part_number;?></option>
                                <?php  } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="<?php echo "onpartHsnCode_".($key +$count);?>" class="col-md-4">HSN Code</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control onpartHsnCode allowNumericWithOutDecimal" id="<?php echo "onpartHsnCode_".($key +$count);?>" 
                                name="part[<?php echo ($key +$count); ?>][hsn_code]" placeholder="Please Enter HSN Code" value="<?php echo $hsn_code;?>"  required >
                        </div>
                    </div>
                    <input type="hidden" value="<?php echo $value['booking_id']; ?>" id="<?php echo "onbookingID_".($key +$count);?>" 
                        name="part[<?php echo ($key +$count); ?>][booking_id]" />
                    <input type="hidden" value="<?php echo $value['service_id']; ?>" id="<?php echo "onserviceId_".($key +$count);?>" 
                        name="part[<?php echo ($key +$count); ?>][service_id]" />
                    <input type="hidden" value="<?php echo $value['partner_id']; ?>" id="<?php echo "onpartnerId_".($key +$count);?>" 
                        name="part[<?php echo ($key +$count); ?>][partner_id]" />

                    <input type="hidden" value="<?php echo $inventory_id; ?>" id="<?php echo "oninventoryId_".($key +$count);?>" 
                        name="part[<?php echo ($key +$count); ?>][inventory_id]" />
                    
                     <input type="hidden" value="<?php echo $inventory_id; ?>" id="<?php echo "onrequestedInventoryId_".($key +$count);?>" 
                        name="part[<?php echo ($key +$count); ?>][requested_inventory_id]" />
                     
                     <input type="hidden" value="<?php echo $type; ?>" id="<?php echo "onspareType_".($key +$count);?>" 
                        name="part[<?php echo ($key +$count); ?>][type]" />
                    
                    <input type="hidden" value="<?php echo $value['id']; ?>" id="<?php echo "onspareID_".($key +$count);?>" 
                        name="part[<?php echo ($key +$count); ?>][spare_id]" />
                    <div class="form-group">
                        <label for="<?php echo "ontotal_amount_".($key +$count);?>" class="col-md-4">Total Amount</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control total_spare_amount" id="<?php echo "ontotal_amount_".($key +$count);?>" 
                                name="part[<?php echo ($key +$count); ?>][total]" value="<?php echo $total_amount; ?>"  required readonly="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
      
        <?php }?>
<!--        <div id="<?php //echo "new_spare_item_".$count;?>"></div>-->
        <div class="dynamic-form-box hide" id="<?php echo "spare_line_template_".$count;?>">
            <div class="spare_clone" id="spare_clone">
                
                <div class="x_panel">
                    <div class="x_content">
                        <div class="col-md-3">
                            <div class="form-group">

                                <label class="radio-inline col-md-6" style="font-weight:bold">
                                     <input type="radio"  id="shipping_status_1" value="1">Shipping
                                   </label>
                             </div>
                             <div class="form-group">

                                 <label class="radio-inline col-md-6" style="font-weight:bold">
                                     <input type="radio"  id="shipping_status_2" value="0">Not Shipping
                                   </label>
                             </div>
                             <div class="form-group">

                                  <label class="radio-inline col-md-6" style="font-weight:bold">
                                      <input type="radio" id="shipping_status_3" value="-1">To be Shipped
                               </label>
                             </div>
                            <span id="error_<?php echo "shippingStatus";?>" class="error" style="color: red;"></span>

                         </div>
                        
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="part Name" class="col-md-4">Part Name *</label>
                                <div class="col-md-6">
                                    <select class="form-control " id="onpartName" 
                                        required="">
                                        <option value="" disabled="" selected="">Select Part Name</option>
                                        <?php 
                                            foreach ($inventory_master_list as $inventory) { ?>
                                        <option value="<?php echo $inventory['part_name']; ?>" ><?php echo $inventory['part_name']; ?></option>
                                        <?php } ?>
                                    </select>
                                    <span id="error_<?php echo "onpartName";?>" class="error" style="color: red;"></span>
                                </div>
                               
                            </div>
                            <div class="form-group">
                                <label for="Basic Price" class="col-md-4">Basic Price</label>
                                <div class="col-md-6">
                                    <input type="text"  class="form-control allowNumericWithDecimal" id="onpartBasicPrice"  placeholder=""  required >
                                    <label for="onpartBasicPrice" id="lbl_onpartBasicPrice" class="error"></label>
                                    <span id="error_onpartBasicPrice" class="error" style="color: red;"></span>
                                    <input type="hidden" value="1" class="form-control" id="onquantity" placeholder=""  required readonly="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="GST rate" class="col-md-4">GST Rate</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control allowNumericWithOutDecimal" id="onpartGstRate" placeholder="Please Enter GST rate" min="12" max="28"  required >
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="shipped_parts_name" class="col-md-4">Parts Number *</label>
                                <div class="col-md-6">
                                    <select required=""  class="form-control spare_parts" id="onpartNumber">
                                        <option value="" disabled="" selected="">Select Part Number</option>
                                    </select>
                                    
                                </div>
                                <div class="col-xs-2 col-sm-2 pull-right">
                                 <button type="button" class="btn btn-default pull-right onspareremoveButton"><i class="fa fa-minus"></i></button>
                               </div>
                            </div>
                            <div class="form-group">
                                <label for="onpartHsnCode" class="col-md-4">HSN Code</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control allowNumericWithOutDecimal" id="onpartHsnCode" placeholder="Please Enter HSN Code"  required >
                                </div>
                            </div>
                            <input type="hidden" value="<?php echo $data[0]['booking_id']; ?>" id="onbookingID" />
                            <input type="hidden" value="<?php echo $data[0]['service_id']; ?>" id="onserviceId"  />
                            <input type="hidden" value="<?php echo $data[0]['partner_id']; ?>" id="onpartnerId" />

                            <input type="hidden" id="onspareType"  />
                            <input type="hidden" value="" id="onrequestedInventoryId" />
                            <input type="hidden" value="" id="oninventoryId" />
                            <input type="hidden" value="new_spare_id" id="onspareID"  />
                            <div class="form-group">
                                <label for="ontotal_amount" class="col-md-4">Total Amount</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control total_spare_amount" id="ontotal_amount" required readonly="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
