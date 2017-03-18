<script src="<?php echo base_url();?>js/base_url.js"></script>
<script src="<?php echo base_url();?>js/custom_js.js"></script>
<div id="page-wrapper" >
    <div class="container" >
        <?php if(validation_errors()){?>
        <div class="panel panel-danger" style="margin-top:10px;margin-bottom:-10px;">
            <div class="panel-heading" style="padding:7px 0px 0px 13px">
            <?php echo validation_errors(); ?>
            </div>
        </div>
        <?php }?>
        <div class="panel panel-info" style="margin-top:20px;">
            <div class="panel-heading">Update Booking</div>
            <div class="panel-body">
                <form name="myForm" class="form-horizontal" id ="booking_form" action="<?php if(isset($booking_history[0]['booking_id'])){ echo base_url()?>employee/booking/update_booking/<?php echo $booking_history[0]['user_id'];?>/<?php echo $booking_history[0]['booking_id']; }  ?> "  method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="col-md-4">Name</label>
                                    <div class="col-md-6">
                                        <input type="hidden" name="upcountry_data" value="<?php echo json_decode(""); ?>" id="upcountry_data" /> 
                                        <input type="text" class="form-control" id="name" name="user_name" value = "<?php echo $booking_history[0]['name'] ?>" readonly="readonly"/>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="booking_primary_contact_no" class="col-md-4">Mobile *</label>
                                    <div class="col-md-6">
                                        <input type="hidden" name="partner_type" value="<?php echo $partner_type; ?>" id="partner_type" />
                                        <input type="text" class="form-control"  id="booking_primary_contact_no" name="booking_primary_contact_no" value = "<?php echo $booking_history[0]['booking_primary_contact_no']?>" required/>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" onclick="outbound_call(<?php echo $booking_history[0]['booking_primary_contact_no']; ?>)" class="btn btn-sm btn-info"><i class = 'fa fa-phone fa-lg' aria-hidden = 'true'></i></button>
                                    </div>
                                </div>
                                <div class="form-group <?php
                                    if (form_error('booking_pincode')) {
                                        echo 'has-error';
                                    } ?>">
                                    <label for="booking_pincode" class="col-md-4">Pincode *</label>
                                    <div class="col-md-6">
<!--                                        <input type="text" class="form-control" id="booking_pincode" name="booking_pincode" value = "<?php //if(isset($booking_history[0]['booking_pincode'])){echo $booking_history[0]['booking_pincode'];} ?>" placeholder="Enter Area Pin" >-->
                                            <select   class="form-control"  id="booking_pincode" name="booking_pincode" required>
                                                <option selected="selected" disabled="disabled">Select Pincode</option>
                                                <?php

                                                     $flag = 0;
                                                    foreach ($pincode as $key => $value) {

                                                        ?>
                                                <option <?php if($value['pincode'] == $booking_history[0]['pincode']){ echo "Selected"; $flag = 1; }?>><?php echo $value['pincode']; ?></option>
                                                <?php  }
                                                    ?>
                                                <?php if($flag == 0){ ?>
                                                <option selected="selected" ><?php echo $booking_history[0]['pincode']; ?></option>
                                                <?php } ?>
                                            </select>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="booking_city" class="col-md-4">City *</label>
                                    <div class="col-md-6">
                                        <select type="text" class="form-control"  id="booking_city" name="city" required>
                                            <option selected="selected" disabled="disabled">Select City</option>
                                            <?php
                                                $flag = 0;
                                                foreach ($city as $key => $cites) {
                                                    ?>
                                            <option <?php if($cites['district'] == $booking_history[0]['city']){ echo "Selected"; $flag = 1; }?>><?php echo $cites['district']; ?></option>
                                            <?php }
                                                ?>
                                            <?php if($flag ==0){ ?>
                                            <option selected="selected" ><?php echo $booking_history[0]['city']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group <?php if (form_error('service_id')) { echo 'has-error';} ?>">
                                    <label for="service_name" class="col-md-4">Appliance *</label>
                                    <div class="col-md-6">
                                        <input type="hidden" name="service" id="services"/>
                                        <select type="text" class="form-control"  id="service_id" name="service_id" value = "<?php echo set_value('service_id'); ?>" onChange="getBrandForService();"  required>
                                            <option disabled>Select Service</option>
                                            <?php foreach ($services as $key => $values) { ?>
                                            <option <?php if($booking_history[0]['service_id'] == $values->id ){ echo "selected"; } ?> value=<?= $values->id; ?>>
                                                <?php echo $values->services; }    ?>
                                            </option>
                                            <?php echo form_error('service_id'); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="service_name" class="col-md-4">Order ID </label>
                                    <div class="col-md-6">
                                        <input class="form-control" name= "order_id" value="<?php if(isset($booking_history[0]['order_id'])){ echo $booking_history[0]['order_id']; } ?>" placeholder="Enter Order ID" id="order_id"></input>
                                    </div>
                                </div>
                                <!--  end col-md-6  -->
                            </div>
                            <!--  start col-md-6  -->
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label  class="col-md-4">Email</label>
                                    <div class="col-md-6">
                                        <input type="email" class="form-control"  id="booking_user_email" name="user_email" value = "<?php echo $booking_history[0]['user_email']; ?>">
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="booking_alternate_contact_no" class="col-md-4">Alternate No</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control booking_alternate_contact_no"  id="booking_alternate_contact_no" name="booking_alternate_contact_no" value = "<?php echo $booking_history[0]['booking_alternate_contact_no']?>" >
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="source_name" class="col-md-4">Booking Source *</label>
                                    <div class="col-md-6">
                                        <select type="text" onchange= "getAppliance('<?php echo $booking_history[0]['service_id'];?>')" class="booking_source form-control"  id="source_code" name="source_code" required>
                                            <option selected="selected" disabled="disabled">Select Booking Source</option>
                                            <?php foreach ($sources as $key => $values) { ?>
                                            <option <?php if($values['code'] == $booking_history[0]['source']){ echo "selected"; } ?> value=<?php echo $values['code']; ?>>
                                                <?php echo $values['source']; }    ?>
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="partner_source" class="col-md-4">Seller Platform</label>
                                    <div class="col-md-6">
                                        <select class="form-control"  id="partner_source" name="partner_source" <?php if(!empty($booking_history[0]['partner_source'])){ echo "readonly";} ?> >
                                            <option value="">Please Select Seller Platform</option>
                                            <option <?php if($booking_history[0]['partner_source'] === "Amazon" ){ echo "selected"; } ?> >Amazon</option>
                                            <option <?php if($booking_history[0]['partner_source'] === "CallCenter" ){ echo "selected"; } ?> >CallCenter</option>
                                            <option <?php if($booking_history[0]['partner_source'] === "Flipkart" ){ echo "selected"; } ?>>Flipkart</option>
                                            <option  <?php if($booking_history[0]['partner_source'] === "Ebay" ){ echo "selected"; } ?>>Ebay</option>
                                            <option  <?php if($booking_history[0]['partner_source'] === "Offline" ){ echo "selected"; } ?>>Offline</option>
                                            <option <?php if($booking_history[0]['partner_source'] === "Shopclues" ){ echo "selected"; } ?> >Shopclues</option>
                                            <option <?php if($booking_history[0]['partner_source'] === "STS" ){ echo "selected"; } ?> >STS</option>
                                            <option <?php if($booking_history[0]['partner_source'] === "Snapdeal-delivered-excel" ){ echo "selected"; } ?> >Snapdeal-delivered-excel</option>
                                            <option <?php if($booking_history[0]['partner_source'] === "Snapdeal-shipped-excel" ){ echo "selected"; } ?> >Snapdeal-shipped-excel</option>
                                            <option <?php if($booking_history[0]['partner_source'] === "Paytm-delivered-excel" ){ echo "selected"; } ?> >Paytm</option>
                                            <option <?php if($booking_history[0]['partner_source'] === "Paytm-delivered-excel" ){ echo "selected"; } ?> >Paytm-delivered-excel</option
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="type" class="col-md-4">Type *</label>
                                    <div class="col-md-8">
                                     <!-- <input style="width:65px;height:20px;display:inline;" id="query" type="radio" class="form-control booking_type" name="type" value="Query"  <?php if(isset($booking_history[0]['type'])){ if($booking_history[0]['type'] == "Query" ){ echo "checked"; } } ?>  required>Query
                                        <input style="width:65px;height:20px;display:inline;" id="booking" type="radio" class="form-control booking_type" name="type" value="Booking" <?php if(isset($booking_history[0]['type'])){   if($booking_history[0]['type'] == "Booking" ){ echo "checked"; } } ?> required>Booking-->

                                        <input style="width:65px;height:20px;display:inline;" id="query" type="radio" class="form-control booking_type" name="type" value="Query" required>Query
                                        <input style="width:65px;height:20px;display:inline;" id="booking" type="radio" class="form-control booking_type" name="type" value="Booking" required>Booking
                                    </div>
                                </div>
                                <!-- end col-md-6 -->
                            </div>
                        </div>
                    </div>
                    <!-- row End  -->
                    <input type="hidden" name="appliance_id[]" value="<?php if(isset($unit_details[0]['appliance_id'])){echo $unit_details[0]['appliance_id'];} ?>"/>
                    <div class="clonedInput panel panel-info " id="clonedInput1">
                        <!--  <i class="fa fa-plus addsection pull-right fa-3x" aria-hidden="true" style ="margin-top:15px; margin-bottom: 15px; margin-right:40px; "></i>
                            <i class="fa fa-times pull-right deletesection  fa-3x"  style ="margin-top:15px; margin-bottom: 15px; margin-right:20px; " aria-hidden="true"></i>-->
                        <div class="panel-heading">
                            <button class="clone btn btn-sm btn-info">Add</button>
                            <button class="remove btn btn-sm btn-info">Remove</button>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-6">
                                        <div class="form-group ">
                                            <label for="service_name" class="col-md-4">Brand *</label>
                                            <div class="col-md-6">
                                                <select type="text" class="form-control appliance_brand"  <?php if(!empty($appliance_id)) { echo "disabled"; } ?>
                                                    name="appliance_brand[]" id="appliance_brand_1" onChange="getCategoryForService(this.id)"  required>
                                                    <option selected disabled>Select Brand</option>
                                                    <?php foreach ($brand[0] as  $appliance_brand) { ?>
                                                    <option <?php if(isset($unit_details[0]['brand'])) {  if (strcasecmp($appliance_brand['brand_name'], $unit_details[0]['brand']) == 0){ echo "selected";} } ?>
                                                        ><?php echo $appliance_brand['brand_name']; ?></option >
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="service_name" class="col-md-4">Category *</label>
                                            <div class="col-md-6">
                                                <select type="text" class="form-control appliance_category"  <?php if(!empty($appliance_id)) { echo "disabled"; } ?>  id="appliance_category_1" name="appliance_category[]"  onChange="getCapacityForCategory(this.value, this.id);" required>
                                                    <option selected disabled>Select Appliance Category</option>
                                                    <?php foreach ($category[0] as $key => $appliance_category) { ?>
                                                    <option <?php if(isset($unit_details[0]['category'])) { if( $appliance_category['category'] == $unit_details[0]['category']) { echo "selected"; } } ?>
                                                        ><?php echo $appliance_category['category']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group <?php if (form_error('appliance_capacity')) { echo 'has-error';} ?>">
                                            <label for="service_name" class="col-md-4">Capacity *</label>
                                            <div class="col-md-6">
                                                <select type="text" class="form-control appliance_capacity"  <?php if(!empty($appliance_id)) { echo "disabled"; } ?>  id="appliance_capacity_1" name="appliance_capacity[]"  onChange="getPricesForCategoryCapacity(this.id);">
                                                    <option  selected disabled>Select Appliance Capacity</option>
                                                    <?php foreach ($capacity[0] as $appliance_capacity) { ?>
                                                    <option <?php if(isset($unit_details[0]['capacity'])) {if($appliance_capacity['capacity'] == $unit_details[0]['capacity']) { echo "selected"; } } ?>
                                                        ><?php echo $appliance_capacity['capacity']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="type" class="col-md-4">Appliance Model </label>
                                            <div class="col-md-6">
                                                <input  type="text" class="form-control"  name="model_number[]" id="model_number_1" value = "<?php if(isset($unit_details[0]['model_number'])) { echo $unit_details[0]['model_number']; } ?>" placeholder="Enter Model"  <?php if(!empty($appliance_id)) { echo "readonly"; } ?> >
                                            </div>
                                        </div>
<!--                                        <div class="form-group ">
                                            <label for="type" class="col-md-4">Appliance Serial No </label>
                                            <div class="col-md-6">
                                                <input  type="text" class="form-control"  name="serial_number[]" id="serial_number_1" value = "<?php if(isset($unit_details[0]['serial_number'])) { echo $unit_details[0]['serial_number']; } ?>" placeholder="Enter Appliance Serial Number"  <?php if(!empty($appliance_id)) { echo "readonly"; } ?> >
                                            </div>
                                        </div>-->
                                        <div class="form-group ">
                                            <label for="type" class="col-md-4">Appliance Description </label>
                                            <div class="col-md-6">
                                                <textarea  type="text" class="form-control"  name="appliance_description[]" id="description_1" placeholder="Enter Description"  <?php if(!empty($appliance_id)) { echo "readonly"; } ?> ><?php if(isset($unit_details[0]['description'])) { echo $unit_details[0]['description']; } ?></textarea>
                                            </div>
                                        </div>
<!--                                         <div class="form-group ">
                                            <label for="type" class="col-md-4">Appliance Tag</label>
                                            <div class="col-md-6">
                                                <input  type="text" class="form-control"  name="appliance_tags[]" id="appliance_tags_1" value = "<?php //if(isset($unit_details[0]['appliance_tag'])) {  echo $unit_details[0]['appliance_tag']; } ?>" placeholder="Enter Tag"  <?php// if(!empty($appliance_id)) { echo "readonly"; } ?> >
                                            </div>
                                        </div> -->
                                        <div class="form-group ">
                                            <label for="type" class="col-md-4">Purchase Year</label>
                                            <div class="col-md-4">
                                                <select  type="text" class=" form-control " <?php if(!empty($appliance_id)) { echo "disabled"; } ?>  name="purchase_month[]" id="purchase_month_1" >
                                                    <option value="">Month</option>
                                                    <option <?php if(!empty($unit_details[0]['purchase_month'])) {  if($unit_details[0]['purchase_month'] == "Jan"){ echo "selected";} } else { if(date('m') == '01'){ echo "selected";}} ?>  >Jan</option>
                                                    <option <?php  if(!empty($unit_details[0]['purchase_month'])) { if($unit_details[0]['purchase_month'] == "Feb"){  echo "selected";} }else { if(date('m') == '02'){ echo "selected";}} ?> >Feb</option>
                                                    <option <?php  if(!empty($unit_details[0]['purchase_month'])) { if($unit_details[0]['purchase_month'] == "Mar"){  echo "selected";} }else { if(date('m') == '03'){ echo "selected";}} ?>>Mar</option>
                                                    <option <?php  if(!empty($unit_details[0]['purchase_month'])) { if($unit_details[0]['purchase_month'] == "Apr"){ echo "selected";} }else { if(date('m') == '04'){ echo "selected";}}?> >Apr</option>
                                                    <option <?php  if(!empty($unit_details[0]['purchase_month'])) { if($unit_details[0]['purchase_month'] == "May"){ echo "selected";} }else { if(date('m') == '05'){ echo "selected";}} ?> >May</option>
                                                    <option <?php  if(!empty($unit_details[0]['purchase_month'])) { if($unit_details[0]['purchase_month'] == "Jun"){ echo "selected";} }else { if(date('m') == '06'){ echo "selected";}} ?> >Jun</option>
                                                    <option <?php  if(!empty($unit_details[0]['purchase_month'])) { if($unit_details[0]['purchase_month'] == "July"){ echo "selected";}}else { if(date('m') == '07'){ echo "selected";}} ?>>July</option>
                                                    <option <?php  if(!empty($unit_details[0]['purchase_month'])) {if($unit_details[0]['purchase_month'] == "Aug"){ echo "selected";}}else { if(date('m') == '08'){ echo "selected";}} ?>>Aug</option>
                                                    <option <?php  if(!empty($unit_details[0]['purchase_month'])) {if($unit_details[0]['purchase_month'] == "Sept"){ echo "selected";}}else { if(date('m') == '09'){ echo "selected";}} ?>>Sept</option>
                                                    <option <?php  if(!empty($unit_details[0]['purchase_month'])) { if($unit_details[0]['purchase_month'] == "Oct"){ echo "selected";} }else { if(date('m') == '10'){ echo "selected";}} ?>>Oct</option>
                                                    <option <?php  if(!empty($unit_details[0]['purchase_month'])) { if($unit_details[0]['purchase_month'] == "Nov"){ echo "selected";}}else { if(date('m') == '11'){ echo "selected";}} ?>>Nov</option>
                                                    <option <?php  if(!empty($unit_details[0]['purchase_month'])) { if($unit_details[0]['purchase_month'] == "Dec"){ echo "selected";} }else { if(date('m') == '12'){ echo "selected";}} ?>>Dec</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-md-4">
                                                    <select  type="text" class="col-md-3 form-control "   name="purchase_year[]" id="purchase_year_1" <?php if(!empty($appliance_id)) { echo "disabled"; } ?>  >
                                                        <option selected="selected" value="" >Year</option>
                                                        <?php for($i = 0; $i> -26; $i--){ ?>
                                                        <option <?php  if(!empty($unit_details[0]['purchase_year'])) { if(date("Y",strtotime($i." year")) == $unit_details[0]['purchase_year']){ echo "selected" ;} } ?>>
                                                            <?php echo date("Y",strtotime($i." year")); ?>
                                                        </option>
                                                        <?php }  ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div  class="col-md-12">
                                                <table class="table priceList table-striped table-bordered" name="priceList" id="priceList_1">
                                                    <tr>
                                                        <th>Service Category</th>
                                                        <th>Std. Charges</th>
                                                        <th>Partner Discount</th>
                                                        <th>Final Charges</th>
                                                        <th>247around Discount</th>
                                                        <th>Selected Services</th>
                                                    </tr>
                                                    <tbody>
                                                        <?php if(!empty($prices)) { ?>
                                                        <?php $i=0; $div = 1; $k=0; foreach ( $prices[0] as  $price) { ?>
                                                        
                                                        <tr>
                                                            <td><?php echo $price['service_category']; ?></td>
                                                            <td><?php echo $price['customer_total']; ?></td>
                                                            <td>
                                                                <input type="text" class="form-control partner_discount" name="<?php echo "partner_paid_basic_charges[".$unit_details[0]['brand_id']."][". $price['id']."][]"; ?>" id="<?php echo "partner_paid_basic_charges_". $div . "_1"; ?>" value = "<?php  if(isset($unit_details[0]['quantity'])){
                                                                    $partner_net_payable = NUll;
                                                                       foreach ($unit_details[0]['quantity'] as  $tags) {
                                                                           if($tags['price_tags'] == $price['service_category'] ){
                                                                              $partner_net_payable = $tags['partner_net_payable'];
                                                                           }
                                                                        }
                                                                    }
                                                                    
                                                                    if(is_null($partner_net_payable)){
                                                                        echo $price['partner_net_payable'];
                                                                    } else {
                                                                        echo $partner_net_payable;
                                                                    }?>" readonly  />
                                                            </td>
                                                            <td>
                                                                <?php  if(isset($unit_details[0]['quantity'])){
                                                                    $customer_net_payable = NUll;
                                                                       foreach ($unit_details[0]['quantity'] as  $tags) {
                                                                           if($tags['price_tags'] == $price['service_category'] ){
                                                                              $customer_net_payable = $tags['customer_net_payable'];
                                                                           }
                                                                        }
                                                                    }
                                                                    
                                                                    if(is_null($customer_net_payable)){
                                                                        echo intval($price['customer_net_payable']);
                                                                    } else {
                                                                        echo $customer_net_payable;
                                                                    }?>
                                                            </td>
                                                            <td><input type="text" class="form-control discount" name="<?php echo "discount[".$unit_details[0]['brand_id']."][". $price['id']."][]"; ?>" id="<?php echo "discount_".$div . "_1"; ?>"
                                                                value = "<?php  if(isset($unit_details[0]['quantity'])){
                                                                    $around_net_payable = NUll;
                                                                       foreach ($unit_details[0]['quantity'] as  $tags) {
                                                                           if($tags['price_tags'] == $price['service_category'] ){
                                                                              $around_net_payable = $tags['around_net_payable'];
                                                                           }
                                                                        }
                                                                    }
                                                                    
                                                                    if(is_null($around_net_payable)){
                                                                        echo "0.00";
                                                                    } else {
                                                                        echo $around_net_payable;
                                                                    }?>"
                                                                placeholder='Enter discount' readonly />
                                                            </td>
                                                            <td>
                                                                <input type='hidden'name ='is_up_val' id="<?php echo "is_up_val_".$div."_1" ?>" value="<?php echo $price['is_upcountry']; ?>" />
                                                                <input class='price_checkbox' <?php if(isset($unit_details[0]['quantity'])){
                                                                    foreach ($unit_details[0]['quantity'] as  $tags) {
                                                                        if($tags['price_tags'] == $price['service_category'] ){
                                                                           echo "checked";
                                                                        }
                                                                     }
                                                                    }
                                                                    
                                                                    ?>
                                                                    type='checkbox' id="<?php echo "checkbox_" . $div . "_1" ; ?>" name='prices[<?php echo $unit_details[0]['brand_id']; ?>][]'  onclick='final_price(), enable_discount(this.id), set_upcountry()' value = "<?php echo $price['id']. "_" .intval($price['customer_total'])."_".$div."_1" ?>">
                                                            </td>
                                                        </tr>
                                                        <?php  $i++; $div++; if(count($unit_details[0]['quantity']) > $k){  $k++;} }} ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                   
                    <div class="cloned">
                        <?php if(count($unit_details) > 1) { ?>
                        <?php $number = 1; foreach ($unit_details as $key => $booking_unit_details) { ?>
                        <?php if($number > 1) { ?>
                        <div class="clonedInput panel panel-info " id="<?php echo "cat_".$number;?>">
                            <div class="panel-heading">
                                <button class="clone btn btn-sm btn-info">Add</button>
                                <button class="remove btn btn-sm btn-info">Remove</button>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-6">
                                            <div class="form-group ">
                                                <label for="service_name" class="col-md-4">Brand *</label>
                                                <div class="col-md-6">
                                                    <select type="text" onchange="getCategoryForService(this.id)" class="form-control appliance_brand"    name="appliance_brand[]" id="<?php echo "appliance_brand_".$number;?>" required>
                                                        <option disabled>Select Brand</option>
                                                        <?php foreach ($brand[$key] as  $appliance_brand) { ?>
                                                        <option <?php  if (strcasecmp($appliance_brand['brand_name'], $booking_unit_details['brand']) == 0){ echo "selected";} ?> ><?php echo $appliance_brand['brand_name']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <input type="hidden" name="appliance_id[]" value="<?php echo $booking_unit_details['appliance_id']; ?>"/>
                                            <div class="form-group">
                                                <label for="service_name" class="col-md-4">Category *</label>
                                                <div class="col-md-6">
                                                    <select type="text" class="form-control appliance_category"   id="<?php echo "appliance_category_".$number;?>" name="appliance_category[]"  onChange="getCapacityForCategory(this.value,this.id);" required>
                                                        <option disabled>Select Appliance Category</option>
                                                        <?php if(!empty($category)){ 
                                                        foreach ($category[$key] as  $appliance_category) { ?>
                                                        <option <?php if( $appliance_category['category'] == $booking_unit_details['category']) { echo "selected"; } ?>><?php echo $appliance_category['category']; ?></option>
                                                        <?php } }?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group <?php if (form_error('appliance_capacity')) { echo 'has-error';} ?>">
                                                <label for="service_name" class="col-md-4">Capacity *</label>
                                                <div class="col-md-6">
                                                    <select type="text" class="form-control appliance_capacity"   id="<?php echo "appliance_capacity_".$number;?>" name="appliance_capacity[]"  onChange="getPricesForCategoryCapacity(this.id);">
                                                        <option  disabled>Select Appliance Capacity</option>
                                                        <?php foreach ($capacity[$key] as  $value) {  ?>
                                                        <option <?php if($value['capacity'] == $booking_unit_details['capacity']){ echo "selected";}?> ><?php echo $value['capacity'];?></option>
                                                        <?php   } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group ">
                                                <label for="type" class="col-md-4">Appliance Model </label>
                                                <div class="col-md-6">
                                                    <input  type="text" class="form-control"  name="model_number[]" id="<?php echo "model_number_".$number;?>" value = "<?php echo $booking_unit_details['model_number']; ?>" placeholder="Enter Model" >
                                                </div>
                                            </div>
                                            <div class="form-group ">
                                                <label for="type" class="col-md-4">Appliance Serial No </label>
                                                <div class="col-md-6">
                                                    <input  type="text" class="form-control"  name="serial_number[]" id="<?php echo "serial_number".$number;?>" value = "<?php if(isset($booking_unit_details['serial_number'])) { echo $booking_unit_details['serial_number']; } ?>" placeholder="Enter Appliance Serial Number"  <?php if(!empty($appliance_id)) { echo "readonly"; } ?> >
                                                </div>
                                            </div>
                                            <div class="form-group ">
                                                <label for="type" class="col-md-4">Appliance Description </label>
                                                <div class="col-md-6">
                                                    <input  type="text" class="form-control"  name="appliance_description[]" id="<?php echo "description".$number;?>"  value = "<?php if(isset($booking_unit_details['description'])) { echo $booking_unit_details['description']; } ?>"  placeholder="Enter Description"  <?php if(!empty($appliance_id)) { echo "readonly"; } ?> >
                                                </div>
                                            </div>
                                            <!--<div class="form-group <?php //if( form_error('appliance_tags') ) { echo 'has-error';} ?>">
                                                <label for="type" class="col-md-4">Appliance Tag</label>
                                                <div class="col-md-6">
                                                    <input  type="text" class="form-control"  name="appliance_tags[]" id="<?php //echo "appliance_tags_".$number;?>" value = "<?php //echo $booking_unit_details['appliance_tag']; ?>" placeholder="Enter Tag" >
                                                </div>
                                            </div>-->
                                            <div class="form-group ">
                                                <label for="type" class="col-md-4">Purchase Year</label>
                                                <div class="col-md-4">
                                                    <select  type="text" class=" form-control "   name="purchase_month[]" id="<?php echo "purchase_month_".$number;?>" >
                                                        <option value="">Month</option>
                                                        <option <?php if($booking_unit_details['purchase_month'] == "Jan"){ echo "selected";} ?> >Jan</option>
                                                        <option <?php if($booking_unit_details['purchase_month'] == "Feb"){  echo "selected";} ?> >Feb</option>
                                                        <option <?php if($booking_unit_details['purchase_month'] == "Mar"){  echo "selected";} ?> >Mar</option>
                                                        <option <?php if($booking_unit_details['purchase_month'] == "Apr"){ echo "selected";} ?>>Apr</option>
                                                        <option <?php if($booking_unit_details['purchase_month'] == "May"){ echo "selected";} ?>>May</option>
                                                        <option <?php if($booking_unit_details['purchase_month'] == "Jun"){ echo "selected";} ?>>Jun</option>
                                                        <option <?php if($booking_unit_details['purchase_month'] == "July"){ echo "selected";} ?>>July</option>
                                                        <option <?php if($booking_unit_details['purchase_month'] == "Aug"){ echo "selected";} ?>>Aug</option>
                                                        <option <?php if($booking_unit_details['purchase_month'] == "Sept"){ echo "selected";} ?>>Sept</option>
                                                        <option <?php if($booking_unit_details['purchase_month'] == "Oct"){ echo "selected";} ?>>Oct</option>
                                                        <option <?php if($booking_unit_details['purchase_month'] == "Nov"){ echo "selected";} ?>>Nov</option>
                                                        <option <?php if($booking_unit_details['purchase_month'] == "Dec"){ echo "selected";} ?>>Dec</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <div class="col-md-4">
                                                        <select  type="text" class="col-md-3 form-control "   name="purchase_year[]" id="<?php echo "purchase_year_".$number;?>" required>
                                                            <option selected="selected" value="" >Year</option>
                                                            <?php for($i = 0; $i> -26; $i--){ ?>
                                                            <option <?php if(date("Y",strtotime($i." year")) == $booking_unit_details['purchase_year']){ echo "selected" ;} ?> >
                                                                <?php echo date("Y",strtotime($i." year")); ?>
                                                            </option>
                                                            <?php }  ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div  class="col-md-12">
                                                    <table class="table priceList table-striped table-bordered" name="priceList" id='<?php echo "priceList_".$number ?>'>
                                                        <tr>
                                                            <th>Service Category</th>
                                                            <th>Std. Charges</th>
                                                            <th>Partner Discount</th>
                                                            <th>Final Charges</th>
                                                            <th>247around Discount</th>
                                                            <th>Selected Services</th>
                                                        </tr>
                                                        <tbody>
                                                            <?php if(!empty($prices)) { ?>
                                                            <?php $i=0; $k=0; foreach ( $prices[0] as  $price) { ?>
                                                            <tr>
                                                                <td><?php echo $price['service_category']; ?></td>
                                                                <td><?php echo $price['customer_total']; ?></td>
                                                                <td>
                                                                    <input type="text" class="form-control partner_discount" name="<?php echo "partner_paid_basic_charges[".$booking_unit_details['brand_id']."][". $price['id']."][]"; ?>" id="<?php echo "partner_paid_basic_charges_". $div . "_".$number; ?>" value = "<?php  if(isset($booking_unit_details['quantity'])){
                                                                        $partner_net_payable = NUll;
                                                                           foreach ($booking_unit_details['quantity'] as  $tags) {
                                                                               if($tags['price_tags'] == $price['service_category'] ){
                                                                                  $partner_net_payable = $tags['partner_net_payable'];
                                                                               }
                                                                            }
                                                                        }
                                                                        
                                                                        if(is_null($partner_net_payable)){
                                                                            echo $price['partner_net_payable'];
                                                                        } else {
                                                                            echo $partner_net_payable;
                                                                        }?>" readonly />
                                                                </td>
                                                                <td>
                                                                    <?php  if(isset($booking_unit_details['quantity'])){
                                                                        $customer_net_payable = NUll;
                                                                           foreach ($booking_unit_details['quantity'] as  $tags) {
                                                                               if($tags['price_tags'] == $price['service_category'] ){
                                                                                  $customer_net_payable = $tags['customer_net_payable'];
                                                                               }
                                                                            }
                                                                        }
                                                                        
                                                                        if(is_null($customer_net_payable)){
                                                                            echo intval($price['customer_net_payable']);
                                                                        } else {
                                                                            echo $customer_net_payable;
                                                                        }?>
                                                                </td>
                                                                <td><input type="text" class="form-control discount" name="<?php echo "discount[".$booking_unit_details['brand_id']."][". $price['id']."][]"; ?>" id="<?php echo "discount_". $div . "_".$number; ?>"
                                                                    value = "<?php  if(isset($booking_unit_details['quantity'])){
                                                                        $around_net_payable = NUll;
                                                                           foreach ($booking_unit_details['quantity'] as  $tags) {
                                                                               if($tags['price_tags'] == $price['service_category'] ){
                                                                                  $around_net_payable = $tags['around_net_payable'];
                                                                               }
                                                                            }
                                                                        }
                                                                        
                                                                        if(is_null($around_net_payable)){
                                                                            echo "0.00";
                                                                        } else {
                                                                            echo $around_net_payable;
                                                                        }?>"
                                                                    placeholder='Enter discount' readonly />
                                                                </td>
                                                                <td>
                                                                    
                                                                    <input class='price_checkbox' <?php if(isset($booking_unit_details['quantity'])){
                                                                        foreach ($unit_details[$key]['quantity'] as  $tags) {
                                                                            if($tags['price_tags'] == $price['service_category'] ){
                                                                               echo "checked";
                                                                            }
                                                                         }
                                                                        }
                                                                        
                                                                        ?>
                                                                        type='checkbox' id="<?php echo "checkbox_" . $div . "_".$number ; ?>" name='prices[<?php echo $booking_unit_details['brand_id']; ?>][]'  onclick='final_price(), enable_discount(this.id), set_upcountry()' value = "<?php echo $price['id']. "_" .intval($price['customer_total'])."_".$div."_".$number ?>">
                                                                </td>
                                                            </tr>
                                                            <?php  $i++; $div++; if(count($booking_unit_details['quantity']) > $k){ $k++;} }} ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php  } $number++; } }
                            ?>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group ">
                                <label for="booking_date" class="col-md-4">Booking Date *</label>
                                <div class="col-md-6">
                                <div class="input-group input-append date">
                                    <input id="booking_date" class="form-control"  name="booking_date" type="date" value = "<?php if(!empty($booking_history[0]['booking_date'])){ echo  date("Y-m-d", strtotime($booking_history[0]['booking_date'])); } else { if(date('H') < '13'){echo  date("Y-m-d");}else{ echo date("Y-m-d", strtotime("+1 day"));} } ?>" required readonly='true'>
                                    <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label  for="booking_address" class="col-md-4">Booking Address *</label>
                                <div class="col-md-6">
                                    <textarea class="form-control" rows="4" id="booking_address" name="home_address"   ><?php echo $booking_history[0]['booking_address']; ?></textarea>
                                </div>
                            </div>
                             <div class="form-group ">
                                <label for="type" class="col-sm-4">Upcountry Charges</label>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <div class="input-group-addon">Rs.</div>
                                        <input  type="text" class="form-control"  name="upcountry_charges" id="upcountry_charges" value="0" placeholder="upcountry_charges" readonly>
                                    </div>&nbsp;<span id="errmsg1"></span>
                                </div>
                            </div>
                            <div class="form-group ">
                                <label for="type" class="col-sm-4">Price To be Paid</label>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <div class="input-group-addon">Rs.</div>
                                        <input  type="text" class="form-control"  name="grand_total_price" id="grand_total_price" value="0" placeholder="Total Price" readonly>
                                    </div>
                                    &nbsp;<span id="errmsg1"></span>
                                </div>
                            </div>
                            
                        </div>
                        <div class="col-md-6">
                            <div class="form-group ">
                                <label  for="booking_timeslot" class="col-md-4">Booking Time Slot *</label>
                                <div class="col-md-6">
                                    <select class="form-control" id="booking_timeslot" name="booking_timeslot" value = "<?php echo set_value('booking_timeslot'); ?>"  required>
                                        <option selected disabled>Select time slot</option>
                                        <?php if(isset($booking_history[0]['booking_timeslot']) && !empty($booking_history[0]['booking_timeslot'])) {?>
                                        <option <?php if(isset($booking_history[0]['booking_timeslot'])){ if($booking_history[0]['booking_timeslot'] == "10AM-1PM"){echo "selected"; } } ?>>10AM-1PM</option>
                                        <option <?php if(isset($booking_history[0]['booking_timeslot'])){  if($booking_history[0]['booking_timeslot'] == "1PM-4PM"){echo "selected"; } } ?>>1PM-4PM</option>
                                        <option <?php if(isset($booking_history[0]['booking_timeslot'])){  if($booking_history[0]['booking_timeslot'] == "4PM-7PM"){echo "selected"; } } ?>>4PM-7PM</option>
                                        <?php }else{?>
                                        <option>10AM-1PM</option>
                                        <option>1PM-4PM</option>
                                        <option selected="">4PM-7PM</option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group ">
                                <label for="type" class="col-md-4">Remarks</label>
                                <div class="col-md-6">
                                    <textarea class="form-control" rows="4" name="query_remarks" id="query_remarks" placeholder="Enter Query Remarks" ><?php if (isset($booking_history[0]['type'])) {
                                        if ($booking_history[0]['type'] == "Booking") {
                                        echo$booking_history[0]['booking_remarks'];
                                        } else {
                                        echo $booking_history[0]['query_remarks'];
                                        }
                                        } ?></textarea>
                                </div>
                            </div>
                              <div class="form-group ">
                                <label for="Internal Status" class="col-sm-4">Internal Status</label>
                                <div class="col-md-6">
                                    <?php
                                        foreach($follow_up_internal_status as $status){?>
                                    <div class="radio">
                                        <label>
                                       <!--  <input type="radio" name="internal_status"  class="internal_status"  value="<?php  echo $status->status;?>" <?php if(isset($booking_history[0]['internal_status'])){ if( $status->status == $booking_history[0]['internal_status']){ echo "checked";}} ?>> -->

                                        <input type="radio" name="internal_status"  class="internal_status"  value="<?php  echo $status->status;?>">
                                        <?php echo $status->status;?>
                                        </label>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div>
                            </div>
                        </div>
                        <div class="form-group  col-md-12" >
                            <center>
                                
                                <input type="submit" id="submitform" onclick="return addBookingDialog()" class="btn btn-primary" value="Submit Booking">
                        </div>
                        </center>
                    </div>
                </form>
                <!-- end Panel Body  -->
            </div>
        </div>
    </div>
</div>
<script>

   
</script>
<script>

    $(".booking_source").select2();
    $("#service_id").select2();
    $("#booking_pincode").select2({
         tags: true
    });
    $("#booking_city").select2({
         tags: true
    });
    $("#partner_source").select2();

     $("#booking_date").datepicker({dateFormat: 'yy-mm-dd', minDate: 0});

</script>
</script>

<script type="text/javascript">
    var regex = /^(.+?)(\d+)$/i;
    var cloneIndex= $(".clonedInput").length +1;
    
    function clone(){
       $(this).parents(".clonedInput").clone()
    .appendTo(".cloned")
           .attr("id","cat" +  cloneIndex)
        .find("*")
           .each(function() {
               var id= this.id || "";
               var match = id.match(regex) || [];
               //console.log(match.length);
               if (match.length === 3) {
                   this.id = match[1]+ (cloneIndex);
            }
    })
           .on('click', 'button.clone', clone)
           .on('click', 'button.remove', remove);
    
           $('#priceList_'+cloneIndex).html("");
       cloneIndex++;
       return false;
    }
    function remove(){
       $(this).parents(".clonedInput").remove();
       final_price();
       return false;
    }
    $("button.clone").on("click", clone);
    
    $("button.remove").on("click", remove);
    
    
     $(document).ready(function () {
      final_price();
    });
    
    function outbound_call(phone_number){
        var confirm_call = confirm("Call Customer ?");

        if (confirm_call === true) {

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
    
     $(document).ready(function () {
  
  //called when key is pressed in textbox
  $("#grand_total_price").keypress(function (e) {
     //if the letter is not digit then display error and don't type anything
     if (e.which !== 8 && e.which !== 0 && (e.which < 48 || e.which > 57)) {
        //display error message
        $("#errmsg1").html("Digits Only").show().fadeOut("slow");
               return false;
    }
   });
   var postData = {};
    postData['service_id'] = $("#service_id").val();
    postData['brand'] = $('#appliance_brand_1').val();
    postData['category'] = $("#appliance_category_1").val();
    postData['partner_code'] = $("#source_code option:selected").val();  
    postData['partner_type'] =  $("#partner_type").val();
    postData['booking_city'] =  $("#booking_city").val();
    postData['booking_pincode'] =  $("#booking_pincode").val();
    postData['clone_number'] = 1;
    postData['capacity'] = $("#appliance_capacity_1").val();
    $('#submitform').attr('disabled',true);

    sendAjaxRequest(postData, pricesForCategoryCapacityUrl).done(function(data) {
        var data1 = jQuery.parseJSON(data);
        
        $("#upcountry_data").val(data1.upcountry_data);
        final_price();
    
        set_upcountry();
        $('#submitform').attr('disabled',false);
       
    });
   
});
</script>
<style type="text/css">
    #errmsg1
    {
    color: red;
    }
</style>