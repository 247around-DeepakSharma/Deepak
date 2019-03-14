<script src="<?php echo base_url();?>js/base_url.js"></script>
<script src="<?php echo base_url();?>js/custom_js.js"></script>
<style>
    #dealer_list{
        float:left;
        width:88%;
        max-height: 300px;
        list-style:none;
        margin-top:0px;
        padding:0;
        position: absolute;
        z-index: 99999;
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 3px;
        overflow-y: auto;
    }
    #dealer_list li{padding: 10px; border-bottom: #bbb9b9 1px solid;}
    #dealer_list li:hover{background:#e9ebee;cursor: pointer;}
    
    <?php if(!empty($model[0])) { ?> 
    .select-model{
        display:block;
    }
    .input-model{
        display:none;
    }    
    <?php }else{ ?>
    .select-model{
        display:none;
    }
    .input-model{
        display:block;
    }  
    <?php } ?>
    
    
</style>
<div id="page-wrapper" >
    <div class="container" >
        <?php if(validation_errors()){?>
        <div class="panel panel-danger" style="margin-top:10px;margin-bottom:-10px;">
            <div class="panel-heading" style="padding:7px 0px 0px 13px">
            <?php echo validation_errors(); ?>
            
            </div>
        </div>
        <?php }?>
        <?php
                if ($this->session->userdata('error')) {
                    echo '<div class="alert alert-danger alert-dismissible" role="alert" style="margin-top:10px;">
                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
                   <strong>' . $this->session->userdata('error') . '</strong>
               </div>';
                }
                ?>
        <div class="panel panel-info" style="margin-top:20px;">
            <?php
            if($is_repeat){ ?>
            <div class="panel-heading">Create Repeat Booking</div>
            <?php } else{      ?>
            <div class="panel-heading">Update Booking</div>
            <?php } ?>
            <div class="panel-body">
                <?php
                $is_repeat_value = "";
                $parentBkng = "";
                if($is_repeat){
                    $bkng_id = INSERT_NEW_BOOKING;
                    $parentBkng = $booking_history[0]['booking_id'];
                    $is_repeat_value = 1;
                }
                else{
                    $bkng_id = $booking_history[0]['booking_id'];
                }
                if($booking_history[0]['parent_booking'] && !$is_repeat){
                     $is_repeat_value = 1;
                     $parentBkng = $booking_history[0]['parent_booking'];
                }
                ?>
                <form name="myForm" class="form-horizontal" id ="booking_form" action="<?php if(isset($booking_history[0]['booking_id'])){ echo base_url()?>employee/booking/update_booking/<?php echo $booking_history[0]['user_id'];?>/<?php echo $bkng_id; }  ?> "  method="POST" enctype="multipart/form-data">
                    <input type="hidden" value="<?php echo $is_repeat_value ?>" name="is_repeat" id="is_repeat">
                    <p id="parent_id_temp" style="display:none;"><?php echo $parentBkng; ?></p>
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
                                        <input type="hidden" name="is_active" value="<?php echo $active;?>" id="is_active" />
                                        <input type="hidden" id="partner_channel" value="<?php echo $booking_history[0]['partner_source']; ?>"/>
                                        <input type="hidden" name="booking_type" id="booking_type" value="<?php echo $booking_history[0]["type"];?>" />
                                        <input type="hidden" name="partner_id" value="<?php echo $booking_history[0]['partner_id'];?>" id="partner_id" />
                                        <input type="hidden" name="assigned_vendor_id" value="<?php if(!empty($booking_history[0]['assigned_vendor_id'])){ echo $booking_history[0]['assigned_vendor_id']; } else { echo '';} ?>" id="assigned_vendor_id" />
                                        <input type="text" class="form-control"  id="booking_primary_contact_no" name="booking_primary_contact_no" value = "<?php echo $booking_history[0]['booking_primary_contact_no']?>" required  <?php if($is_repeat){ echo 'readonly="readonly"'; } ?>/>
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
                                        <input type="text" class="form-control" id="booking_pincode" name="booking_pincode" value = "<?php if(isset($booking_history[0]['booking_pincode'])){echo $booking_history[0]['booking_pincode'];} ?>" placeholder="Enter Area Pin" <?php if($is_repeat){ echo 'readonly="readonly"'; } ?>>
                                           <span id="error_pincode" style="color:red"></span>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="booking_city" class="col-md-4">City *</label>
                                    <div class="col-md-6">
                                        <select class="form-control"  id="booking_city" name="city" required <?php if($is_repeat){ echo 'readonly="readonly"'; } ?> <?php if($is_repeat){ echo 'readonly="readonly"'; } ?>>
                                            <option selected="selected" disabled="disabled">Select City</option>
                                            <?php
                                                $flag = 0;
                                                foreach ($city as $key => $cites) {
                                                    ?>
                                            <option <?php if(strtolower($cites['district']) == strtolower($booking_history[0]['city'])){ echo "Selected"; $flag = 1; } else {if($is_repeat){echo 'disabled';}}?>><?php echo $cites['district']; ?></option>
                                            <?php }
                                                ?>
                                           <?php if($flag == 0){ ?>
                                            <option selected="selected" ><?php echo $booking_history[0]['city']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group <?php if (form_error('service_id')) { echo 'has-error';} ?>">
                                    <label for="service_name" class="col-md-4">Appliance *</label>
                                    <div class="col-md-6">
                                        <input type="hidden" name="service" id="services"/>
                                        <select type="text" class="form-control"  id="service_id" name="service_id" value = "<?php echo set_value('service_id'); ?>" onChange="getBrandForService();" readonly required>
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
                                        <input class="form-control" name= "order_id" value="<?php if(isset($booking_history[0]['order_id'])){ echo $booking_history[0]['order_id']; } ?>" <?php if(!empty($booking_history[0]['order_id'])){?> readonly <?php }?> placeholder="Enter Order ID" id="order_id"></input>
                                    </div>
                                </div>
                                <div class="form-group ">
                                     <label for="dealer_phone_number" class="col-md-4">Dealer Mobile Number </label>
                                      <div class="col-md-6">
                                          <input class="form-control" name= "dealer_phone_number" value="<?php if(isset($booking_history[0]['dealer_phone_number'])){ echo $booking_history[0]['dealer_phone_number']; } ?>" placeholder="Enter Dealer Mobile No" id="dealer_phone_number" <?php if($is_repeat){ echo 'readonly="readonly"'; } ?>/>
                                           <input type="hidden" name="dealer_id" id="dealer_id" value="<?php if(isset($booking_history[0]['dealer_id'])){ echo $booking_history[0]['dealer_id']; } ?>">
                                            <div id="dealer_phone_suggesstion_box"></div>
                                      </div>
                                 </div>
                                <div class="form-group ">
                                     <label for="dealer_phone_number" class="col-md-4">Parent Booking </label>
                                      <div class="col-md-6">
                                          <input  class="form-control" type="text" value="<?php echo $parentBkng; ?>" name="parent_id" id="parent_id" readonly="readonly">
                                      </div>
                                 </div>
                                <!--  end col-md-6  -->
                            </div>
                            <!--  start col-md-6  -->
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label  class="col-md-4">Email</label>
                                    <div class="col-md-6">
                                        <input type="email" class="form-control"  id="booking_user_email" name="user_email" value = "<?php echo $booking_history[0]['user_email']; ?>" <?php if($is_repeat){ echo 'readonly="readonly"'; } ?>>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="booking_alternate_contact_no" class="col-md-4">Alternate No</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control booking_alternate_contact_no"  id="booking_alternate_contact_no" name="booking_alternate_contact_no" value = "<?php echo $booking_history[0]['alternate_phone_number']?>" <?php if($is_repeat){ echo 'readonly="readonly"'; } ?>>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="source_name" class="col-md-4">Booking Source *</label>
                                    <div class="col-md-6">
                                        <select onchange= "getAppliance('<?php echo $booking_history[0]['service_id'];?>')" class="booking_source form-control"  id="source_code" name="source_code" required>
                                            <option selected="selected" disabled="disabled">Select Booking Source</option>
                                            <?php foreach ($sources as $key => $values) { ?>
                                            <option data-id="<?php echo $values['partner_id']; ?>" <?php if($values['partner_id'] == $booking_history[0]['partner_id']){ echo "selected"; } else {if($is_repeat){echo 'disabled';}}?> value=<?php echo $values['code']; ?>>
                                                <?php echo $values['source']; }    ?>
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="partner_source" class="col-md-4">Seller Platform</label>
                                    <div class="col-md-6">
                                        <select class="form-control"  id="partner_source" name="partner_source" <?php if(!empty($booking_history[0]['partner_source'])){ echo "readonly";} ?>>
                                            <option value="">Please Select Seller Platform</option>
                                           
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="type" class="col-md-4">Type *</label>
                                    <p id="booking_old_type_holder" style="display:none;"><?php echo $booking_history[0]['type'] ?></p>
                                    <div class="col-md-8">
                                     <!-- <input style="width:65px;height:20px;display:inline;" id="query" type="radio" class="form-control booking_type" name="type" value="Query"  <?php //if(isset($booking_history[0]['type'])){ if($booking_history[0]['type'] == "Query" ){ echo "checked"; } } ?>  required>Query
                                        <input style="width:65px;height:20px;display:inline;" id="booking" type="radio" class="form-control booking_type" name="type" value="Booking" <?php //if(isset($booking_history[0]['type'])){   if($booking_history[0]['type'] == "Booking" ){ echo "checked"; } } ?> required>Booking-->

                                        <input style="width:65px;height:20px;display:inline;" id="query" type="radio" class="form-control booking_type" onclick="check_prepaid_balance('Query')" name="type" value="Query" required <?php if($is_repeat){ echo 'disabled'; } ?> >Query
                                        <input style="width:65px;height:20px;display:inline;" id="booking" type="radio" class="form-control booking_type" onclick="check_prepaid_balance('Booking')" name="type" value="Booking" <?php if($is_repeat){ echo "checked"; } ?> required <?php if($is_repeat){ echo 'readonly="readonly"'; } ?>>Booking
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label  class="col-md-4">Support File</label>
                                    <div class="col-md-6">
                                        <div class="col-md-10">
                                        <input type="file" class="form-control"  id="support_file" name="support_file" value = "<?php echo $booking_history[0]['support_file']; ?>">
                                    
                                        </div>
                                        <div class="col-md-2">
                                            <?php
                                                $src = base_url() . 'images/no_image.png';
                                                $image_src = $src;
                                                if (isset($booking_history[0]['support_file']) && !empty($booking_history[0]['support_file'])) {
                                                    //Path to be changed
                                                    $src = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/misc-images/".$booking_history[0]['support_file'];
                                                    $image_src = base_url().'images/view_image.png';
                                                }
                                                ?>
                                            <a href="<?php  echo $src?>" target="_blank"><img src="<?php  echo $image_src ?>" width="35px" height="35px" style="border:1px solid black;margin-left:-5px;" /></a>
                                      
                                        </div>
                                        </div>
                                </div>
                               
                                <div class="form-group ">
                                     <label for="dealer name" class="col-md-4">Dealer Name </label>
                                      <div class="col-md-6">
                                          <input class="form-control" name= "dealer_name" value="<?php if(isset($booking_history[0]['dealer_name'])){ echo $booking_history[0]['dealer_name']; } ?>" placeholder="Enter Dealer Name" id="dealer_name" <?php if($is_repeat){ echo 'readonly="readonly"'; } ?>/>
                                          <div id="dealer_name_suggesstion_box"></div>
                                      </div>
                                 </div>
                                
<!--                                <div class="form-group">
                                    <label for="support_file" class="col-md-4">Upload Support file</label>
                                    <div class="col-md-6">
                                        <input type="file" class="form-control" id="support_file" name="support_file" value="<?php 
//                                            if (isset($booking_history[0]['support_file'])) {
//                                                    echo $booking_history[0]['support_file'];
//                                                }
                                        ?>">
                                        
                                        <div class="col-md-4 col-sm-4" style="margin-top:23px;">
                                            <?php
//                                                $src = base_url() . 'images/no_image.png';
//                                                $image_src = $src;
//                                                if (isset($booking_history[0]['support_file']) && !empty($booking_history[0]['support_file'])) {
//                                                    //Path to be changed
//                                                    $src = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/".$booking_history[0]['support_file'];
//                                                    $image_src = base_url().'images/view_image.png';
//                                                }
                                                ?>
                                            <a href="<?php // echo $src?>" target="_blank"><img src="<?php // echo $image_src ?>" width="35px" height="35px" style="border:1px solid black;margin-left:-5px;" /></a>
                                        </div>
                                    </div>
                                </div>-->
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
                            <?php if(!$is_repeat){?>
                            <button class="clone btn btn-sm btn-info">Add</button>
                            <button class="remove btn btn-sm btn-info">Remove</button>
                            <?php } ?>
                            <p class="pull-right"><?php if(!is_null($booking_history[0]['paid_by_customer'])){ 
                                if($booking_history[0]['paid_by_customer'] == 1) { 
                                    echo "<b style='margin-right:100px'>Paid By Customer</b>";} 
                                    else { 
                                        echo "<b style='margin-right:100px'>Free For Customer</b>"; 
                                        
                                    }
                                } ?></p>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-6">
                                        <div class="form-group ">
                                            <label for="service_name" class="col-md-4">Brand *</label>
                                            <div class="col-md-6">
                                                <select type="text" class="form-control appliance_brand"  <?php if(!empty($appliance_id)) { echo "disabled"; } ?>
                                                    name="appliance_brand[]" id="appliance_brand_1" onChange="getCategoryForService(this.id)"  required <?php if($is_repeat){ echo 'readonly="readonly"'; } ?>>
                                                    <option selected disabled>Select Brand</option>
                                                    <?php foreach ($brand[0] as  $appliance_brand) { ?>
                                                    <option <?php if(isset($unit_details[0]['brand'])) {  if (strcasecmp($appliance_brand['brand_name'], $unit_details[0]['brand']) == 0){ echo "selected";} else{  if($is_repeat){ echo "disabled"; }} }  ?>
                                                        ><?php echo $appliance_brand['brand_name']; ?></option >
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="service_name" class="col-md-4">Category *</label>
                                            <div class="col-md-6">
                                                <select type="text" class="form-control appliance_category"  <?php if(!empty($appliance_id)) { echo "disabled"; } ?>  id="appliance_category_1" name="appliance_category[]"  onChange="getCapacityForCategory(this.value, this.id);" required <?php if($is_repeat){ echo 'readonly="readonly"'; } ?>>
                                                    <option selected disabled>Select Appliance Category</option>
                                                    <?php foreach ($category[0] as $key => $appliance_category) { ?>
                                                    <option <?php if(isset($unit_details[0]['category'])) { if( $appliance_category['category'] == $unit_details[0]['category']) { echo "selected"; } else{  if($is_repeat){ echo "disabled"; }} } ?>
                                                        ><?php echo $appliance_category['category']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group <?php if (form_error('appliance_capacity')) { echo 'has-error';} ?>">
                                            <label for="service_name" class="col-md-4">Capacity *</label>
                                            <div class="col-md-6">
                                                <select type="text" class="form-control appliance_capacity"  <?php if(!empty($appliance_id)) { echo "disabled"; } ?>  id="appliance_capacity_1" name="appliance_capacity[]"  onChange="getPricesForCategoryCapacity(this.id);getModelForServiceCategoryCapacity(this.id);" <?php if($is_repeat){ echo 'readonly="readonly"'; } ?>>
                                                    <option  selected disabled>Select Appliance Capacity</option>
                                                    <?php foreach ($capacity[0] as $appliance_capacity) { ?>
                                                    <option <?php if(isset($unit_details[0]['capacity'])) {if($appliance_capacity['capacity'] == $unit_details[0]['capacity']) { echo "selected"; } else{  if($is_repeat){ echo "disabled"; }} } ?>
                                                        ><?php echo $appliance_capacity['capacity']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="type" class="col-md-4">Appliance Model </label>
                                            <div class="col-md-6">
                                                <input  type="text" class="form-control input-model"  name="model_number[]" id="model_number_1" value = "<?php if(isset($unit_details[0]['model_number'])) { echo $unit_details[0]['model_number']; } ?>" placeholder="Enter Model"  <?php if(!empty($appliance_id)) { echo "readonly"; } ?> disabled="" <?php if($is_repeat){ echo 'readonly="readonly"'; } ?>>
                                                <select class="form-control select-model"  <?php if(!empty($appliance_id)) { echo "disabled"; } ?>  id="model_number_1" name="model_number[]">
                                                    <?php foreach ($model[0] as $value) { ?>
                                                    <option <?php if(isset($unit_details[0]['model_number'])) {if($value['model'] == $unit_details[0]['model_number']) { echo "selected"; } else{  if($is_repeat){ echo "disabled"; }} } ?>
                                                        ><?php echo $value['model']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
<!--                                        <div class="form-group ">
                                            <label for="type" class="col-md-4">Appliance Serial No </label>
                                            <div class="col-md-6">
                                                <input  type="text" class="form-control"  name="serial_number[]" id="serial_number_1" value = "<?php //if(isset($unit_details[0]['serial_number'])) { echo $unit_details[0]['serial_number']; } ?>" placeholder="Enter Appliance Serial Number"  <?php// if(!empty($appliance_id)) { echo "readonly"; } ?> >
                                            </div>
                                        </div>-->
                                        <div class="form-group ">
                                            <label for="type" class="col-md-4">Appliance Description </label>
                                            <div class="col-md-6">
                                                <textarea <?php if($is_repeat){ echo 'readonly="readonly"'; } ?> class="form-control"  name="appliance_description[]" id="description_1" placeholder="Enter Description"  <?php if(!empty($appliance_id)) { echo "readonly"; } ?> ><?php if(isset($unit_details[0]['description'])) { echo $unit_details[0]['description']; } ?></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="order_item_id" class="col-md-4">Order Item Id </label>
                                             <div class="col-md-6">
                                                 <input <?php if($is_repeat){ echo 'readonly="readonly"'; } ?> class="form-control" name= "order_item_id[]" value="<?php if(isset($unit_details[0]['sub_order_id'])){ echo $unit_details[0]['sub_order_id']; } ?>" placeholder="Enter Order Item Id" id="order_item_id_1"/>
                                             </div>
                                        </div>
<!--                                         <div class="form-group ">
                                            <label for="type" class="col-md-4">Appliance Tag</label>
                                            <div class="col-md-6">
                                                <input  type="text" class="form-control"  name="appliance_tags[]" id="appliance_tags_1" value = "<?php //if(isset($unit_details[0]['appliance_tag'])) {  echo $unit_details[0]['appliance_tag']; } ?>" placeholder="Enter Tag"  <?php// if(!empty($appliance_id)) { echo "readonly"; } ?> >
                                            </div>
                                        </div> -->
                                        <div class="form-group ">
                                <label for="purchase_date" class="col-md-4">Purchase Date *</label>
                                <div class="col-md-6">
                                <div class="input-group input-append date">
                                    <input <?php if($is_repeat){ echo 'readonly="readonly"'; } ?> id="purchase_date_1" class="form-control purchase_date"  name="purchase_date[]" type="date" value = "<?php if(isset($unit_details[0]['purchase_date'])){ echo $unit_details[0]['purchase_date']; }?>">
                                    <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
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
                                                        <?php $clone_number = 1; $i=0; $div = 1; $k=0; foreach ( $prices[0] as  $price) { ?>
                                                        <tr>
                                                            <td><?php echo $price['service_category']; ?></td>
                                                            <td><?php 
                                                            $ct = $price['customer_total'];
                                                              if(isset($unit_details[0]['quantity'])){
                                                                   
                                                                       foreach ($unit_details[0]['quantity'] as  $tags) {
                                                                           if($tags['price_tags'] == $price['service_category'] ){
                                                                              $ct = $tags['customer_total'];
                                                                           }
                                                                        }
                                                                    }
                                                                    echo $ct;
                                                            
                                                            ?></td>
                                                            <td>
                                                                <input type="text" class="form-control partner_discount" name="<?php echo "partner_paid_basic_charges[".$unit_details[0]['brand_id']."][".$clone_number."][". $price['id']."][]"; ?>" id="<?php echo "partner_paid_basic_charges_". $div . "_1"; ?>" value = "<?php  if(isset($unit_details[0]['quantity'])){
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
                                                                    $customer_net_payable = 0;
                                                                       foreach ($unit_details[0]['quantity'] as  $tags) {
                                                                           if($tags['price_tags'] == $price['service_category'] ){
                                                                              $customer_net_payable = $tags['customer_net_payable'];
                                                                           }
                                                                        }
                                                                    }
                                                                    
                                                                    echo $customer_net_payable;
                                                                    ?>
                                                            </td>
                                                            <td><input type="text" class="form-control discount" name="<?php echo "discount[".$unit_details[0]['brand_id']."][".$clone_number."][". $price['id']."][]"; ?>" id="<?php echo "discount_".$div . "_1"; ?>"
                                                                value = "<?php  if(isset($unit_details[0]['quantity'])){
                                                                    $around_net_payable = NUll;
                                                                       foreach ($unit_details[0]['quantity'] as  $tags) {
                                                                           if($tags['price_tags'] == $price['service_category'] ){
                                                                              $around_net_payable = $tags['around_net_payable'];
                                                                           }
                                                                        }
                                                                    }
                                                                    
                                                                    if(is_null($around_net_payable)){
                                                                        echo intval($price['around_net_payable']);
                                                                    } else {
                                                                        echo $around_net_payable;
                                                                    }?>"
                                                                placeholder='Enter discount' readonly />
                                                            </td>
                                                            <td>
                                                                <?php
                                                                 $onclick = "onclick='final_price(), enable_discount(this.id), set_upcountry()'";
                                                                ?>
                                                                <input type='hidden'name ='is_up_val' id="<?php echo "is_up_val_".$div."_1" ?>" value="<?php echo $price['is_upcountry']; ?>" />
                                                                <input <?php if ($price['service_category'] == REPEAT_BOOKING_TAG){ echo "class='price_checkbox repeat_".$price['product_or_services']."'"; } else { ?>
                                                                class='price_checkbox <?php echo $price['product_or_services']; } ?>' <?php if(isset($unit_details[0]['quantity'])){
                                                                    foreach ($unit_details[0]['quantity'] as  $tags) {
                                                                        if($is_repeat){
                                                                            if($price['service_category'] ==  REPEAT_BOOKING_TAG){
                                                                                echo " checked ";
                                                                            }
                                                                            echo "style= 'pointer-events: none;'";
                                                                        }
                                                                        else{
                                                                            if(($tags['price_tags'] == $price['service_category'])){
                                                                                echo " checked ";
                                                                                if($price['service_category'] ==  REPEAT_BOOKING_TAG){
                                                                                    $tempString = "'".$booking_history[0]['booking_primary_contact_no']."','".$booking_history[0]['service_id']."','".$booking_history[0]['partner_id']."',this.checked,true";
                                                                                    //$onclick = 'onclick="get_parent_booking('.$tempString.')"';
                                                                                    $onclick = 'onclick="final_price(), enable_discount(this.id), set_upcountry(),get_parent_booking('.$tempString.')"';
                                                                                }
                                                                            }
                                                                            else{ 
                                                                                if($price['service_category'] ==  REPEAT_BOOKING_TAG){
                                                                                   $tempString = "'".$booking_history[0]['booking_primary_contact_no']."','".$booking_history[0]['service_id']."','".$booking_history[0]['partner_id']."',this.checked,false";
                                                                                   //$onclick = 'onclick="get_parent_booking('.$tempString.')"';
                                                                                    $onclick = 'onclick="final_price(), enable_discount(this.id), set_upcountry(),get_parent_booking('.$tempString.')"';
                                                                                }
                                                                            }
                                                                        }
                                                                     }
                                                                    }
                                                                    
                                                                    ?>
                                                                    type='checkbox' id="<?php echo "checkbox_" . $div . "_1" ; ?>" name='prices[<?php echo $unit_details[0]['brand_id']; ?>][<?php echo $clone_number; ?>][]' <?php if( $price['service_category'] ==REPAIR_OOW_PARTS_PRICE_TAGS){ if($customer_net_payable == 0){ echo "onclick='return false;' ";}}?>  <?php echo $onclick; ?> value = "<?php echo $price['id']. "_" .intval($ct)."_".$div."_1" ?>">
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
                        <?php if(!$is_repeat) { ?>
                        <?php if(count($unit_details) > 1) { ?>
                        <?php $number = 1; foreach ($unit_details as $key => $booking_unit_details) { ?>
                        <?php if($number > 1) { $clone_number++; ?>
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
<!--                                            <div class="form-group ">
                                                <label for="type" class="col-md-4">Appliance Serial No </label>
                                                <div class="col-md-6">
                                                    <input  type="text" class="form-control"  name="serial_number[]" id="<?php //echo "serial_number".$number;?>" value = "<?php //if(isset($booking_unit_details['serial_number'])) { echo $booking_unit_details['serial_number']; } ?>" placeholder="Enter Appliance Serial Number"  <?php //if(!empty($appliance_id)) { echo "readonly"; } ?> >
                                                </div>
                                            </div>-->
                                            <div class="form-group ">
                                                <label for="type" class="col-md-4">Appliance Description </label>
                                                <div class="col-md-6">
                                                    <input  type="text" class="form-control"  name="appliance_description[]" id="<?php echo "description".$number;?>"  value = "<?php if(isset($booking_unit_details['description'])) { echo $booking_unit_details['description']; } ?>"  placeholder="Enter Description"  <?php if(!empty($appliance_id)) { echo "readonly"; } ?> >
                                                </div>
                                            </div>
                                            <div class="form-group ">
                                                <label for="order_item_id" class="col-md-4">Order Item Id </label>
                                                 <div class="col-md-6">
                                                     <input class="form-control" name= "order_item_id[]" placeholder="Enter Order Item Id" value = "<?php if(isset($booking_unit_details['sub_order_id'])) { echo $booking_unit_details['sub_order_id']; } ?>" id="<?php echo "order_item_id_".$number ;?>"/>
                                                 </div>
                                            </div>
                                            <!--<div class="form-group <?php //if( form_error('appliance_tags') ) { echo 'has-error';} ?>">
                                                <label for="type" class="col-md-4">Appliance Tag</label>
                                                <div class="col-md-6">
                                                    <input  type="text" class="form-control"  name="appliance_tags[]" id="<?php //echo "appliance_tags_".$number;?>" value = "<?php //echo $booking_unit_details['appliance_tag']; ?>" placeholder="Enter Tag" >
                                                </div>
                                            </div>-->
                                             <div class="form-group ">
                                <label for="purchase_date" class="col-md-4">Purchase Date *</label>
                                <div class="col-md-6">
                                <div class="input-group input-append date">
                                    <input class="form-control purchase_date" name= "purchase_date[]" type="date" value = "<?php if(isset($booking_unit_details['purchase_date'])) { echo $booking_unit_details['purchase_date']; } ?>" id="<?php echo "purchase_date_".$number ;?>"/>
                                    <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
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
                                                                <td><?php 
                                                                
                                                                $ct = $price['customer_total'];
                                                              if(isset($unit_details[0]['quantity'])){
                                                                   
                                                                       foreach ($booking_unit_details['quantity'] as  $tags) {
                                                                           if($tags['price_tags'] == $price['service_category'] ){
                                                                              $ct = $tags['customer_total'];
                                                                           }
                                                                        }
                                                                    }
                                                                    echo $ct;
                                                                ?>
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control partner_discount" name="<?php echo "partner_paid_basic_charges[".$booking_unit_details['brand_id']."][".$clone_number."][". $price['id']."][]"; ?>" id="<?php echo "partner_paid_basic_charges_". $div . "_".$number; ?>" value = "<?php  if(isset($booking_unit_details['quantity'])){
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
                                                                <td><input type="text" class="form-control discount" name="<?php echo "discount[".$booking_unit_details['brand_id']."][".$clone_number."][". $price['id']."][]"; ?>" id="<?php echo "discount_". $div . "_".$number; ?>"
                                                                    value = "<?php  if(isset($booking_unit_details['quantity'])){
                                                                        $around_net_payable = NUll;
                                                                           foreach ($booking_unit_details['quantity'] as  $tags) {
                                                                               if($tags['price_tags'] == $price['service_category'] ){
                                                                                  $around_net_payable = $tags['around_net_payable'];
                                                                               }
                                                                            }
                                                                        }
                                                                        
                                                                        if(is_null($around_net_payable)){
                                                                             echo intval($price['around_net_payable']);
                                                                        } else {
                                                                            echo $around_net_payable;
                                                                        }?>"
                                                                    placeholder='Enter discount' readonly />
                                                                </td>
                                                                <td>
                                                                   
                                                                    <input class='price_checkbox' <?php if(isset($booking_unit_details['quantity'])){
                                                                        foreach ($unit_details[$key]['quantity'] as  $tags) {
                                                                            if($tags['price_tags'] == $price['service_category'] ){
                                                                               echo " checked ";
                                                                            }
                                                                         }
                                                                        }
                                                                        
                                                                        ?>
                                                                        type='checkbox' id="<?php echo "checkbox_" . $div . "_".$number ; ?>" name='prices[<?php echo $booking_unit_details['brand_id']; ?>][<?php echo $clone_number;?>][]'  onclick='final_price(), enable_discount(this.id), set_upcountry()' value = "<?php echo $price['id']. "_" .intval($ct)."_".$div."_".$number ?>">
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
                        <?php  } $number++; } } }
                            ?>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group ">
                                <label for="booking_date" class="col-md-4">Booking Date *</label>
                                <div class="col-md-6">
                                <div class="input-group input-append date">
                                    <input id="booking_date" class="form-control"  name="booking_date" type="date" 
                                           value = "<?php if(!empty($booking_history[0]['booking_date']) && !$is_repeat){ echo  date("Y-m-d", strtotime($booking_history[0]['booking_date'])); } 
                                           else { if(date('H') < '13'){echo  date("Y-m-d");}else{ echo date("Y-m-d", strtotime("+1 day"));} } ?>" required echo readonly='true'>
                                    <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label  for="booking_address" class="col-md-4">Booking Address *</label>
                                <div class="col-md-6">
                                    <textarea <?php if($is_repeat) { echo "readonly='true'" ;} ?> class="form-control" rows="4" id="booking_address" name="home_address"   ><?php echo $booking_history[0]['booking_address']; ?></textarea>
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
                                <label for="type" class="col-md-4">Symptom</label>
                                <div class="col-md-6">
                                    <select class="form-control" name="booking_request_symptom" id="booking_request_symptom">
                                        <option disabled selected>Please Select Any Symptom</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group ">
                                <label for="type" class="col-md-4">Remarks</label>
                                <div class="col-md-6">
                                    <textarea class="form-control" rows="4" name="query_remarks"  id="query_remarks" placeholder="Enter Query Remarks" ><?php if (isset($booking_history[0]['type'])) {
                                        if ($booking_history[0]['type'] == "Booking") {
                                        echo$booking_history[0]['booking_remarks'];
                                        } else {
                                        echo $booking_history[0]['query_remarks'];
                                        }
                                        } ?></textarea>
                                </div>
                            </div>
                          
                            <div class="form-group " style="display:none;" id="repeat_reason_holder">
                                <label for="type" class="col-md-4">Repeat Reason</label>
                                <div class="col-md-6">
                                    <textarea class="form-control" rows="4" name="repeat_reason"  id="repeat_reason" placeholder="Enter Reason to Repeat Booking" ><?php if (isset($booking_history[0]['repeat_reason'])) {
                                        echo$booking_history[0]['repeat_reason'];
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
                                
                                <input type="submit" id="submitform" onclick="return addBookingDialog('admin_update')" class="btn btn-primary" value="Submit Booking">
                        </div>
                        </center>
                    </div>
                </form>
                <!-- end Panel Body  -->
            </div>
        </div>
    </div>
</div>
<!-- Repeat Booking Model  -->
<div class="modal fade" id="repeat_booking_model" tabindex="-1" role="dialog" aria-labelledby="repeat_booking_model" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Select Parent Booking</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
        <div class="modal-body" id="repeat_booking_body" style="padding: 3px;   font-size: 13px;">
      </div>
<!--      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>-->
    </div>
  </div>
</div>

<script>

   
</script>
<script>
    check_pincode();
    $(".booking_source").select2();
    //$("#service_id").select2();
    $('#service_id').css('pointer-events','none'); 
    $("#booking_city").select2({
         tags: true
    });
    $("#partner_source").select2();

     $("#booking_date").datepicker({dateFormat: 'yy-mm-dd', minDate: 0, maxDate: '<?php echo date("Y-m-d", strtotime("+15 day")); ?>'});
       getPartnerChannel();
     

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
           $('#order_item_id_'+cloneIndex).val("");
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
     <?php
     if($is_repeat){ ?>
        $('#repeat_reason_holder').show();
   <?php  }
     ?>
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
     
    postData['partner_type'] =  $("#partner_type").val();
    postData['booking_city'] =  $("#booking_city").val();
    postData['booking_pincode'] =  $("#booking_pincode").val();
    postData['clone_number'] = 1;
    postData['assigned_vendor_id'] = $("#assigned_vendor_id").val();
    postData['capacity'] = $("#appliance_capacity_1").val();
    postData['partner_id'] = $("#source_code").find(':selected').attr('data-id');
    $('#submitform').attr('disabled',true);

    sendAjaxRequest(postData, pricesForCategoryCapacityUrl).done(function(data) {
        console.log(data);
        var data1 = jQuery.parseJSON(data);
        
        $("#upcountry_data").val(data1.upcountry_data);
        final_price();
        $('#submitform').attr('disabled',false);
        set_upcountry();
       
    });
   
});
  $("#purchase_date").datepicker({dateFormat: 'yy-mm-dd'});
  
  function readonly_select(objs, action) {
    if (action===true)
        objs.prepend('<div class="disabled-select"></div>');
    else
        $(".disabled-select", objs).remove();
}
function get_parent_booking(contactNumber,serviceID,partnerID,isChecked,is_already_repeat){
        if(isChecked){
            if(!is_already_repeat){
              $.ajax({
                      type: 'POST',
                      url: '<?php echo base_url(); ?>employee/booking/get_posible_parent_id',
                      data: {contact: contactNumber, service_id: serviceID,partnerID:partnerID,day_diff:<?php echo _247AROUND_REPEAT_BOOKING_ALLOWED_DAYS; ?>},
                      success: function(response) {
                          obj = JSON.parse(response);
                          if(obj.status  == <?Php echo _NO_REPEAT_BOOKING_FLAG; ?>){
                              alert("There is not any Posible Parent booking for this booking, It can not be a repeat booking");
                              $('.repeat_Service:checked').prop('checked', false);
                              $("#repeat_reason_holder").hide();
                          }
                         else if(obj.status  == <?Php echo _ONE_REPEAT_BOOKING_FLAG; ?>){
                             $('.Service:checked').prop('checked', false);
                             $('.Service').each(function() {
                                $(this).prop('disabled', true);
                             });
                             $("#parent_id").val(obj.html);
                             $("#is_repeat").val("1");
                             $("#repeat_reason_holder").show();
                             $(".cloned :input").attr("disabled", true);
                          }
                          else if(obj.status  == <?Php echo _MULTIPLE_REPEAT_BOOKING_FLAG; ?>){
                              $('.Service:checked').prop('checked', false);
                                $('.Service').each(function() {
                                    $(this).prop('disabled', true);
                                });
                              $('#repeat_booking_model').modal('show');
                              $("#repeat_booking_body").html(obj.html);
                              $("#repeat_reason_holder").show();
                              $(".cloned :input").attr("disabled", true);
                          }
                      }
                  });
              }
              else{
                $('.Service:checked').prop('checked', false);
                $('.Service').each(function() {
                    $(this).prop('disabled', true);
                });
                $("#parent_id").val($("#parent_id_temp").text());
                $("#is_repeat").val("1");
                $("#repeat_reason_holder").show();
              }
           }
           else{
           $('.Service').each(function() {
               $(this).prop('disabled', false);
           });
            $("#parent_id").val("");
            $("#is_repeat").val("");
            $("#repeat_reason_holder").hide();
            $(".cloned :input").attr("disabled", false);
           }
    }
    function parentBooking(id){
        $("#parent_id").val(id);
        $("#is_repeat").val("1");
        $('#repeat_booking_model').modal('hide');
    }
    
    get_symptom('<?php echo $booking_history[0]['booking_request_symptom'];?>');
    $("#purchase_date_1").datepicker({dateFormat: 'YYYY-MM-DD', maxDate: 0});
    

</script>
<style type="text/css">
    #errmsg1
    {
    color: red;
    }
</style>
<?php if($this->session->userdata('error')){$this->session->unset_userdata('error');} ?>