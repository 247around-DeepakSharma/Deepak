<style>
    .col-md-3{
    width: 25%;
    }
    #dealer_list{
    float:left;
    width:92%;
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
</style>
<?php
    if(!$is_repeat){
        $url = base_url()."partner/process_update_booking/".$booking_history[0]['booking_id'];
    }
    else{
        $url = base_url()."employee/partner/process_addbooking/";
    }
    ?>
<div class="right_col" role="main">
    <form name="myForm" class="form-horizontal" id ="booking_form" action="<?php echo $url; ?>"  method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Step 1</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="col-md-4 ">
                            <div class="form-group col-md-12   <?php if( form_error('booking_primary_contact_no') ) { echo 'has-error';} ?>">
                                <label for="booking_primary_contact_no">Mobile *</label>
                                <input type="text" class="form-control"  id="booking_primary_contact_no" name="booking_primary_contact_no" value = "<?php echo $booking_history[0]['booking_primary_contact_no']; ?>" required readonly>
                                <span id="error_mobile_number" style="color:red"></span>
                            </div>
                        </div>
                        <div class="col-md-4" >
                            <div class="form-group col-md-12 <?php if( form_error('user_name') ) { echo 'has-error';} ?>">
                                <label for="name">Name *</label>
                                <input type="hidden" name="user_id" id="user_id" value="<?php if(isset($booking_history[0]['user_id'])){ echo $booking_history[0]['user_id']; } ?>" />
                                <input type="hidden" name="assigned_vendor_id" id="assigned_vendor_id" value="<?php if(isset($booking_history[0]['assigned_vendor_id'])){ echo $booking_history[0]['assigned_vendor_id']; }  ?>" />
                                <input type="hidden" name="upcountry_data" id="upcountry_data" value="" />
                                <input type="hidden" name="partner_channel" id="partner_channel" value="<?php echo $booking_history[0]['partner_source']; ?>" />
                                <input type="hidden" name="partner_code" id="partner_code" value="<?php echo $partner_code;?>" />
                                <input type="hidden" name="partner_type" id="partner_type" value="<?php echo $partner_type;?>" />
                                <?php
                                    $parentID = "";
                                    if($is_repeat){ 
                                        $parentID = $booking_history[0]['booking_id'];
                                     }
                                     else{
                                         if($booking_history[0]['parent_booking']){
                                             $parentID = $booking_history[0]['parent_booking'];
                                         }
                                     }
                                    ?>
                                <input type="hidden" name="appliance_id" id='appliance_id' value="<?php echo $unit_details[0]['appliance_id']; ?>" />
                                <input type="text" class="form-control" id="name" name="user_name" value = "<?php if(isset($booking_history[0]['name'])){ echo $booking_history[0]['name']; } else { echo set_value('user_name'); }  ?>" <?php if(isset($booking_history[0]['name'])){ echo "readonly"; }  ?> placeholder="Please Enter User Name" <?php if($is_repeat){ echo "readonly";} ?>>
                                <?php echo form_error('user_name'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group col-md-12 <?php if( form_error('service_id') ) { echo 'has-error';} ?>">
                                <label for="service_name">Appliance * <span id="error_appliance" style="color: red;"></span></label>
                                <select class="form-control"  id="service_name" name="service_id"   required onchange="return get_brands(), get_category(), get_capacity()">
                                    <option selected disabled>Select Appliance</option>
                                    <?php foreach ($appliances as $values) { ?>
                                    <option <?php if(count($appliances) ==1){echo "selected";} ?>  data-id="<?php echo $values->services;?>" value=<?= $values->id; ?> <?php if($booking_history[0]['service_id'] == $values->id){ echo "selected";}else{ if($is_repeat){ echo "disabled";}} ?>>
                                        <?php echo $values->services; }    ?>
                                    </option>
                                </select>
                                <?php echo form_error('service_id'); ?>
                                <span id="error_pincode" style="color: red;"></span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-4">
                            <div class="form-group col-md-12  <?php if( form_error('booking_pincode') ) { echo 'has-error';} ?> ">
                                <label for="booking_pincode">Pincode *   <span id="error_pincode" style="color: red;"></span></label>
                                <input type="text" class="form-control" id="booking_pincode" name="booking_pincode" value = "<?php if(isset($booking_history[0]['booking_pincode'])){ echo $booking_history[0]['booking_pincode']; } else { echo set_value('booking_pincode'); }  ?>" placeholder="Enter Area Pin" required readonly>
                                <?php echo form_error('booking_pincode'); ?>
                            </div>
                        </div>
                        <div class="col-md-4 ">
                            <div class="form-group col-md-12  <?php if( form_error('city') ) { echo 'has-error';} ?>">
                                <label for="booking_city">City * <span id="error_city" style="color: red;"></span><span style="color:grey;display:none" id="city_loading">Loading ...</span></label>
                                <select class="form-control"  id="booking_city" name="city" required >
                                    <option value="<?php echo $booking_history[0]['city']; ?>" selected readonly><?php echo $booking_history[0]['city']; ?></option>
                                </select>
                                <?php echo form_error('city'); ?>
                            </div>
                        </div>
                        <input type="hidden" name="appliance_name" id="appliance_name" value=""/>
                        <div class="col-md-4">
                            <div class="form-group col-md-12 <?php if( form_error('appliance_brand') ) { echo 'has-error';} ?>">
                                <label for="appliance_brand_1">Brand *  <span id="error_brand" style="color: red;"><span style="color:grey;display:none" id="brand_loading">Loading ...</span></label>  
                                <select class="form-control appliance_brand"    name="appliance_brand" id="appliance_brand_1" required onchange="return get_category()">
                                    <option selected disabled value="option1">Select Brand</option>
                                </select>
                                <?php echo form_error('appliance_brand'); ?>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-4">
                            <div class="form-group col-md-12 <?php if( form_error('appliance_category') ) { echo 'has-error';} ?>">
                                <label for="appliance_category_1">Category * <span id="error_category" style="color: red;"> <span style="color:grey;display:none" id="category_loading">Loading ...</span></label>
                                <select class="form-control appliance_category"   id="appliance_category_1" name="appliance_category"   required onchange="return get_capacity()">
                                    <option selected disabled value="option1">Select Appliance Category</option>
                                </select>
                                <?php echo form_error('appliance_category'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group col-md-12 <?php if( form_error('appliance_capacity') ) { echo 'has-error';} ?>">
                                <label for="appliance_capacity_1">Capacity  <span id="error_capacity" style="color: red;"> <span style="color:grey;display:none" id="capacity_loading">Loading ...</span></label>
                                <select class="form-control appliance_capacity"   id="appliance_capacity_1" name="appliance_capacity" onchange="return get_models(), getPrice()">
                                    <option selected disabled value="option1">Select Appliance Capacity</option>
                                </select>
                                <?php echo form_error('appliance_capacity'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group col-md-12 <?php if( form_error('model_number') ) { echo 'has-error';} ?>">
                                <label for="model_number_1">Model Number  <span id="error_model" style="color: red;"></label>
                                <span id="model_number_2">
                                    <select class="form-control"  name="model_number" id="model_number_1" >
                                        <option selected disabled>Select Model</option>
                                    </select>
                                </span>
                                <?php echo form_error('model_number'); ?>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-4">
                            <div class="form-group col-md-12  <?php if( form_error('booking_date') ) { echo 'has-error';} ?>">
                                <label for="booking_date">Booking Date *</label>
                                <input type="text" class="form-control" readonly style="background-color:#FFF;"   id="booking_date" name="booking_date"  value = "<?php if(!empty($booking_history[0]['booking_date']) && !$is_repeat){ echo date('Y-m-d', strtotime($booking_history[0]['booking_date'])); } else { echo date('H') >= 12 ? date("Y-m-d", strtotime("+1 day")):date("Y-m-d", strtotime("+0 day"));}?>">
                                <?php echo form_error('booking_date'); ?>
                            </div>
                        </div>
                        <div class="col-md-4 ">
                            <div class="form-group col-md-12  <?php if( form_error('partner_source') ) { echo 'has-error';} ?>">
                                <label for="partner_source">Seller Channel*  <span id="error_seller" style="color: red;"></label>
                                <select class="form-control"  id="partner_source" name="partner_source" >
                                    <option value="" selected disabled>Please select seller channel</option>
                                </select>
                                <?php echo form_error('partner_source'); ?>
                            </div>
                        </div>
                        <?php  $unique_appliance = array_unique(array_map(function ($k) {
                            return $k['appliance_id'];
                            }, $unit_details));
                            
                            ?>
                        <div class="col-md-4">
                            <div class="form-group col-md-5 ">
                                <label for="appliance_unit">Unit* <span id="error_seller" style="color: red;"></label>
                                <select disabled style="width:55%" class="form-control" onchange="final_price()"  id="appliance_unit" name="appliance_unit" >
                                    <?php if(!$is_repeat){
                                        for($i =1; $i <26; $i++) { ?>
                                    <option value="<?php echo $i;?>" <?php if(count($unique_appliance) == $i){ echo "selected";} ?>><?php echo $i; ?></option>
                                    <?php } }
                                        else{
                                            $unique_appliance = array($unique_appliance[0]);
                                            ?>
                                    <option value="1">1</option>
                                    <?php
                                        }
                                        ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 ">
                            <div class="form-group ">
                                <label for="type" class="col-md-12">Parent Booking</label>
                                <div class="col-md-12">
                                    <input class="form-control" type="text" value="<?php echo $parentID; ?>" name="parent_booking" id="parent_booking" readonly = "readonly" >
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 ">
                            <div class="form-group "  id="repeat_reason_holder" style="display:none;">
                                <label for="type" class="col-md-12">Repeat Reason</label>
                                <div class="col-md-12">
                                    <input class="form-control"  name="repeat_reason"  id="repeat_reason" placeholder=" Repeat Reason" ><?php if (isset($booking_history[0]['repeat_reason'])) {
                                        echo$booking_history[0]['repeat_reason'];
                                        } ?>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Step 2</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="col-md-12">
                            <div  class="form-group col-md-12">
                                <table class="table priceList table-striped table-bordered" id="priceList">
                                    <tr class="text-center">
                                        <th class="text-center">Service Category</th>
                                        <th class="text-center">Final Charges</th>
                                        <th class="text-center" id='selected_service'>Selected Services</th>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group col-md-12  ">
                                <label for="grand_total">Grand Total *</label>
                                <input  type="text" class="form-control"  name="grand_total" id="grand_total" value = "<?php echo set_value('grand_total'); ?>" placeholder="0.00" readonly >
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Step 3</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="col-md-3 ">
                            <div class="form-group col-md-12    <?php if (form_error('order_id')) {echo 'has-error';} ?>">
                                <label for="order_id">Reference / Invoice / Order Number * <span id="error_order_id" style="color:red"></span></label>
                                <input class="form-control" name= "order_id" value="<?php if (!empty(set_value('order_id'))) {
                                    echo set_value('order_id');
                                    } else {
                                    echo $booking_history[0]['order_id'];
                                    } ?>" placeholder ="Please Enter Reference / Invoice / Order Number" id="order_id" <?php if($is_repeat){echo 'readonly';} ?>/>
                            </div>
                        </div>
                        <div class="col-md-3 ">
                            <div class="form-group col-md-12  <?php if (form_error('serial_number')) {echo 'has-error';} ?>">
                                <label for="serial_number">Serial Number * <span id="error_serial_number" style="color:red"></span></label>
                                <input  type="text" class="form-control"  name="serial_number" id="serial_number" value = "<?php if (!empty(set_value('serial_number'))) {
                                    echo set_value('serial_number');
                                    } else {
                                    echo $unit_details[0]['serial_number'];
                                    } ?>" placeholder="Enter Serial Number" onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || (event.charCode > 47 && event.charCode < 58) || event.charCode == 8" <?php if($is_repeat){echo 'readonly';} ?>>
                            </div>
                        </div>
                        <div class="col-md-3 ">
                            <div class="form-group col-md-12  <?php if (form_error('dealer_phone_number')) {echo 'has-error';} ?>">
                                <label for="dealer_phone_number">Dealer Phone Number  <span id="error_dealer_phone_number" style="color:red"></span></label>
                                <input  type="text" class="form-control"  name="dealer_phone_number" id="dealer_phone_number" value = "<?php if (isset($dealer_data)) {
                                    echo $dealer_data['dealer_phone_number_1'];
                                    } ?>" placeholder="Enter Dealer Phone Number" autocomplete="off" <?php if($is_repeat){echo 'readonly';} ?>>
                                <div id="dealer_phone_suggesstion_box"></div>
                            </div>
                        </div>
                        <div class="col-md-3 ">
                            <div class="form-group col-md-12  <?php if (form_error('dealer_name')) {echo 'has-error';} ?>">
                                <label for="dealer_name">Dealer Name *  <span id="error_dealer_name" style="color:red"></span></label>
                                <input  type="text" class="form-control"  name="dealer_name" id="dealer_name" value = "<?php if (isset($dealer_data)) {
                                    echo $dealer_data['dealer_name'];
                                    } ?>" placeholder="Enter Dealer Name" autocomplete="off" <?php if($is_repeat){echo 'readonly';} ?>>
                                <input type="hidden" name="dealer_id" id="dealer_id" value="<?php if (isset($dealer_data)) {
                                    echo $dealer_data['dealer_id'];
                                    } ?>">
                                <div id="dealer_name_suggesstion_box"></div>
                            </div>
                        </div>
                        <div class="col-md-3 ">
                            <div class="form-group col-md-12" style="margin: 0px;padding: 0px;">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group col-md-12  <?php if( form_error('purchase_date') ) { echo 'has-error';} ?>">
                                            <label for="purchase_date">Purchase Date * <span id="error_purchase_date" style="color: red;"></span></label>
                                            <input style="background-color:#FFF;"  type="text" class="form-control" readonly  id="purchase_date" name="purchase_date"  value = "<?php if(isset($unit_details[0]['purchase_date'])){echo $unit_details[0]['purchase_date'];} ?>" <?php if($is_repeat){echo 'readonly';} ?> max="<?=date('Y-m-d');?>" autocomplete='off' onkeydown="return false" >
                                            <?php echo form_error('purchase_date'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 ">
                            <div class="form-group col-md-12" style="margin: 0px;padding: 0px;">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group col-md-12  <?php if( form_error('booking_request_symptom') ) { echo 'has-error';} ?>">
                                            <label for="booking_request_symptom">Symptom * <span id="error_booking_request_symptom" style="color: red;"></span></label>
                                            <select class="form-control" name="booking_request_symptom" id="booking_request_symptom">
                                                <option disabled selected>Please Select Any Symptom</option>
                                                <?php if(isset($symptom)) {
                                                    foreach ($symptom as $value) { 
                                                        $selected=((($value['id'] == 0) || (!empty($booking_symptom) && ($value['id'] == $booking_symptom[0]['symptom_id_booking_creation_time']))) ? 'selected' :'');  ?>
                                                    <option value="<?php echo $value['id']?>" <?=$selected?> ><?php echo $value['symptom']; ?></option>

                                                <?php } } ?>
                                            </select>
                                            <?php echo form_error('booking_request_symptom'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group col-md-12  <?php if (form_error('query_remarks')) {echo 'has-error';} ?>">
                                <label for="remarks">Remarks  <span id="error_remarks" style="color: red;"></label>
                                <textarea <?php if($is_repeat){echo 'readonly';} ?> class="form-control" rows="2" id="remarks" name="query_remarks"  placeholder="Enter Problem Description" ><?php if (set_value('query_remarks')) {  echo set_value('query_remarks'); } else { echo $booking_history[0]['booking_remarks']; } ?></textarea>
                                <?php echo form_error('query_remarks'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Step 4</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="col-md-4 ">
                            <div class="form-group col-md-12  <?php if( form_error('alternate_phone_number') ) { echo 'has-error';} ?>">
                                <label for="booking_alternate_contact_no">Alternate Mobile</label>
                                <input type="text" class="form-control booking_alternate_contact_no"  id="booking_alternate_contact_no" name="alternate_phone_number" value = "<?php if(set_value('alternate_phone_number')){ echo set_value('alternate_phone_number'); } else { echo $booking_history[0]['booking_alternate_contact_no'];} ?>" placeholder ="Please Enter Alternate Contact No" <?php if($is_repeat){echo 'readonly';} ?>>
                                <?php echo form_error('alternate_phone_number'); ?>
                            </div>
                        </div>
                        <div class="col-md-4 ">
                            <div class="form-group col-md-12  <?php if( form_error('user_email') ) { echo 'has-error';} ?>">
                                <label for="booking_user_email">Email </label>
                                <input type="email" class="form-control"  id="booking_user_email" name="user_email" value = "<?php if(set_value('user_email')){ echo set_value('user_email'); } else { echo $booking_history[0]['user_email'];} ?>" placeholder="Please Enter User Email" <?php if($is_repeat){echo 'readonly';} ?>>
                                <?php echo form_error('user_email'); ?>
                            </div>
                        </div>
                        <div class="col-md-4 ">
                            <div class="form-group col-md-12  <?php if( form_error('landmark') ) { echo 'has-error';} ?>">
                                <label for="landmark">Landmark </label>
                                <input type="text" class="form-control" id="landmark" name="landmark" value = "<?php if(set_value('landmark')){ echo set_value('landmark'); } else { echo $booking_history[0]['booking_landmark'];} ?>" placeholder="Enter Any Landmark" <?php if($is_repeat){echo 'readonly';} ?>>
                                <?php echo form_error('landmark'); ?>
                            </div>
                        </div>
                        <div class="col-md-12 ">
                            <div class="form-group col-md-12  <?php if( form_error('booking_address') ) { echo 'has-error';} ?>">
                                <label for="booking_address">Booking Address *  <span id="error_address" style="color: red;"></label>
                                <textarea <?php if($is_repeat){echo 'readonly';} ?> class="form-control" rows="2" id="booking_address" name="booking_address" placeholder="Please Enter Address"  required ><?php if(set_value('booking_address')){ echo set_value('booking_address'); } else { echo $booking_history[0]['booking_address'];} ?></textarea>
                                <?php echo form_error('booking_address'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_content">
                        <center>
                            <input type="hidden" name="product_type" value="Delivered"/>
                            <input type="hidden" id="not_visible" name="not_visible" value="0"/>
                            <?php if($booking_history[0]['partner_id'] == $this->session->userdata('partner_id')) { ?>
                                <input type="submit" id="submitform" class="btn btn-success "<?php if(count($unique_appliance) > 1){ echo "disabled";}?> onclick="return check_validation()" value="Submit Booking">
                            <?php } ?>
                            <p id="error_not_visible" style="color: red"></p>
                        </center>
                    </div>
                </div>
            </div>
        </div>
    </form>
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
            <div class="modal-body" id="repeat_booking_body">
            </div>
            <!--      <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>-->
        </div>
    </div>
</div>
<script type="text/javascript">
    function check_validation(){
        var exp1 = /^[6-9]{1}[0-9]{9}$/;
        var order_id =  $('#order_id').val();
        var booking_address = $('#booking_address').val();
        var mobile_number = $('#booking_primary_contact_no').val();
        var city = $('#booking_city').val();
        var pincode = $('#booking_pincode').val();
        var serial_number = $('#serial_number').val();
        var category = $('#appliance_category_1').val();
        var remarks = $('#remarks').val();
        var partner_source = $("#partner_source").val();
        var appliance = $("#service_name").val();
        var brand = $("#appliance_brand_1").val();
        var dealer_name = $("#dealer_name").val();
        var dealer_phone_number = $("#dealer_phone_number").val();
        var repeat_reason = $("#repeat_reason").val();
        var parant_id = $('#parent_booking').val();
        var isRepeatChecked = $('.repeat_Service:checkbox:checked').length;
        var isServiceChecked = $('.Service:checkbox:checked').length;
        
        if(isRepeatChecked > 0){
            //If anyone select repeat booking then parent ID Shoud not blank
            if(isServiceChecked >0){
                alert("You Can Not Select any other Service in case of Repeat Booking");
                return false;
            }
            if(!parant_id){
                alert("Please Select Parent ID");
                return false;
            }
            if(!repeat_reason){
                alert("Please Enter Repeat Reason");
                return false;
            }
        }
        
        if(!mobile_number.match(exp1)){
            display_message("booking_primary_contact_no","error_mobile_number","red","Please Enter Valid Mobile");
            return false;
        } else {
            display_message("booking_primary_contact_no","error_mobile_number","green","");
             
        }
        if(mobile_number === ""){
            display_message("booking_primary_contact_no","error_mobile_number","red","Please Enter Mobile");
             return false;
        } else {
            display_message("booking_primary_contact_no","error_mobile_number","green","");
             
        }
        if(pincode === ""){
              display_message("booking_pincode","error_pincode","red","Please Enter Pincode");
             return false;
        } else {
           display_message("booking_pincode","error_pincode","green","");
            
        }
        if(city === null){
            
             display_message("booking_city","error_city","red","Please Enter City");
             return false;
        } else {
             display_message("booking_city","error_city","green","");
            
        }
        if(appliance === null){
            display_message("service_name","error_appliance","red","Please Select Appliance");
             return false;
        } else {
            display_message("service_name","error_appliance","green","");
        }
        if(brand === null){
            display_message("appliance_brand_1","error_brand","red","Please Select Brand");
             return false;
        } else {
            display_message("appliance_brand_1","error_brand","green","");
        }
        
        if(category === null){
            display_message("appliance_category_1","error_category","red","Please Select Category");
             return false;
        } else {
              display_message("appliance_category_1","error_category","green","");
        }
        if($('.appliance_capacity').length > 0) {
            var count1=0;
            $(".appliance_capacity").each(function(){
                var capacity_value = document.getElementById(this.id).innerHTML;
                if(($.trim(capacity_value) !== '<option selected="" value=""></option>') && ($("#"+this.id).val() === '')) {
                    display_message("appliance_capacity_1","error_capacity","red","Please Select Capacity");
                    $("#"+this.id).focus();
                    ++count1;
                    return false;
                }
            });
            if(count1 > 0) {
                return false;
            }
            else {
                display_message("appliance_capacity_1","error_capacity","green","");
            }
        }
        
        if(partner_source === ""){
              display_message("partner_source","error_seller","red","Please Seller Channel");
             return false;
        } else {
           display_message("partner_source","error_seller","green","");
            
        }
        
        service_category =0;
         $("input[type=checkbox]:checked").each(function(i) {
            service_category = 1;
            
           
        });
        if(service_category === 0){
             $("#selected_service").css("color","red");
             
            return false;
        } else{
           
             $("#selected_service").css("color","black");
          
        }
        
    //        if(order_id === '' && serial_number === '' && dealer_phone_number === ''){
    //            alert("Please Fill Any one of these Order Id/Serial Number/Dealer Phone Number");
    //            return false;
    //        } 
        
        if(dealer_phone_number !== "" && dealer_name === ""){
             alert("Please Enter Dealer Name");
             return false;
        }
        if(dealer_phone_number !=="" && !dealer_phone_number.match(exp1)){
            alert('Please Enter Valid Dealer Phone Number');   
            return false;
        }
    
        if(booking_address === ""){
             display_message("booking_address","error_address","red","Please Enter Booking Address");
             return false;
        } else {
          display_message("booking_address","error_address","green","");
        }
      
        if(remarks === ""){
             document.getElementById('remarks').style.borderColor = "red";
              document.getElementById('error_remarks').innerHTML = "Please Enter Problem Description";
             return false;
        } else {
            document.getElementById('remarks').style.borderColor = "green";
            document.getElementById('error_remarks').innerHTML = "";  
        }
        <?php if(count($unique_appliance) > 1){ ?>
             alert("Please Contact 247Around Team To Update This Booking");
             return false;
        <?php }?>
        
       
    }
    
    
    function display_message(input_id, error_id, color,message){
    console.log(error_id);
            document.getElementById(input_id).style.borderColor = color;
            document.getElementById(error_id).innerHTML = message;
    }
    
    $("#booking_city").select2({
         tags: true
    });
    $("#price_tag").select2();
    // $("#service_name").select2();
    $("#booking_request_symptom").select2();
    $("#appliance_brand_1").select2();
    $("#appliance_capacity_1").select2();
    $("#appliance_category_1").select2();
    $("#partner_source").select2();
    var today = new Date();
    
    var startDate = today.getHours() >=12 ? today.add(1).day() : today;
    
    $('#booking_date').daterangepicker({
                autoUpdateInput: false,
                singleDatePicker: true,
                showDropdowns: true,
                minDate:startDate,
                maxDate: '<?php echo date("Y-m-d", strtotime("+15 day")); ?>',
                locale:{
                    format: 'YYYY-MM-DD'
                }
            });
            
    $('#booking_date').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD'));
    });
    
    $('#booking_date').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
    get_brands();
    
    //This funciton is used to get Distinct Brands for selected service for Logged Partner
    function get_brands(){
        service_id =  $("#service_name").val();
        
        partner_type = '<?php echo $partner_type;?>';
        
         $.ajax({
                        type: 'POST',
                        beforeSend: function(){
                            $('#brand_loading').css("display", "-webkit-inline-box");
                        },
                        url: '<?php echo base_url(); ?>employee/partner/get_brands_from_service',
                        data: {service_id: service_id,partner_id:<?php echo $this->session->userdata('partner_id')?>, 
                    brand:'<?php echo $unit_details[0]['appliance_brand']; ?>',is_repeat:'<?php echo $is_repeat ?>',
                        partner_type:partner_type},
                        success: function (data) {
                               
                                //First Resetting Options values present if any
                                $("#appliance_brand_1 option[value !='option1']").remove();
                                $('#appliance_brand_1').append(data).change();
                            },
                        complete: function(){
                            $('#brand_loading').css("display", "none");
                        }
                    });
    }
    
    //This function is used to get Category for partner id , service , brands specified
    
    function get_category(brand){
        
        service_id =  $("#service_name").val();
        brand =  $("#appliance_brand_1").val();
        
        partner_type = '<?php echo $partner_type;?>';
        
        $.ajax({
                        type: 'POST',
                        beforeSend: function(){
                            $('#category_loading').css("display", "-webkit-inline-box");
                        },
                        url: '<?php echo base_url(); ?>employee/partner/get_category_from_service',
                        data: {service_id: service_id,partner_id:<?php echo $this->session->userdata('partner_id')?>, 
                    brand: brand, category:'<?php echo $unit_details[0]['appliance_category']; ?>', is_repeat:'<?php echo $is_repeat; ?>', 
                        partner_type:partner_type},
                        success: function (data) {
                               
                                //First Resetting Options values present if any
                                $("#appliance_category_1 option[value !='option1']").remove();
                                $('#appliance_category_1').append(data).change();
                                get_capacity();
                            },
                        complete: function(){
                            $('#category_loading').css("display", "none");
                        }            
                    });
        
    }
    
    //This function is used to get Capacity and Model
    function get_capacity(){
        
        service_id =  $("#service_name").val();
        brand = $("#appliance_brand_1").find(':selected').val();
        category = $("#appliance_category_1").find(':selected').val();
        
        partner_type = '<?php echo $partner_type;?>';
        
        $.ajax({
            type: 'POST',
            beforeSend: function(){
                $('#capacity_loading').css("display", "-webkit-inline-box");
            },
            url: '<?php echo base_url(); ?>employee/partner/get_capacity_for_partner',
            data: {service_id: service_id,partner_id:<?php echo $this->session->userdata('partner_id')?>, 
        brand: brand,category:category, capacity:'<?php echo $unit_details[0]['appliance_capacity']; ?>', is_repeat:'<?php echo $is_repeat; ?>', 
                        partner_type:partner_type},
            
            success: function (data) {
    
                    //First Resetting Options values present if any
                    //$("#appliance_capacity_1 option[value !='option1']").remove();
                    //$('#appliance_capacity_1').append(data).change();
                    $('#appliance_capacity_1').html(data).change();
                    if(($.trim(data) !== "") && ($.trim(data) !== "<option  selected  value=''></option>")) {
                        $("#appliance_capacity_1").attr("required",true);
                    }
                    else{
                        $("#appliance_capacity_1").removeAttr("required");
                    }
                    get_models();
                    getPrice();
                },
            complete: function(){
                $('#capacity_loading').css("display", "none");
            }  
        });
    }
    
    //This function is used to get Model for corresponding previous data's
    function get_models(){
        
        service_id =  $("#service_name").val();
        brand = $("#appliance_brand_1").find(':selected').val();
        category = $("#appliance_category_1").find(':selected').val();
        
        partner_type = '<?php echo $partner_type;?>';
        capacity = $("#appliance_capacity_1").val();
        if(capacity === null && capacity === ""){
            capacity = '';
            $("#appliance_capacity_1").removeAttr("required");
        }
       
        $.ajax({
                        type: 'POST',
                        url: '<?php echo base_url(); ?>employee/partner/get_model_for_partner',
                        data: {service_id: service_id,partner_id:<?php echo $this->session->userdata('partner_id')?>, is_repeat:'<?php echo $is_repeat;?>', 
                    brand: brand,category:category,capacity:capacity, 
                    model:'<?php echo $unit_details[0]['model_number']; ?>',
                        partner_type:partner_type},
                       
                        success: function (data) {
                         
                                if(data === "Data Not Found"){
                                    var input = '<input type="text" name="model_number" id="model_number_1" class="form-control" placeholder="Please Enter Model">';
                                    $("#model_number_2").html(input).change();
                                } else {
                                    //First Resetting Options values present if any
                                     var input_text = '<span id="model_number_2"><select class="form-control"  name="model_number" id="model_number_1" ><option selected disabled>Select Model</option></select></span>';
                                    $("#model_number_2").html(input_text).change();
                                    $("#model_number_1").append(data).change();
                                    
                                }
                            }
                    });
    }
    
    function getPrice() {
    
        var postData = {};
        appliance_name = $("#service_name").find(':selected').attr('data-id');
       
        $("#appliance_name").val(appliance_name);
       $("#priceList").html('<div class="text-center"><img src= "<?php echo base_url(); ?>images/loadring.gif" /></div>').delay(1200).queue(function () {
        postData['service_id'] = $("#service_name").val();
        postData['brand'] = $('#appliance_brand_1').val();
        postData['category'] = $("#appliance_category_1").val();
        capacity = $("#appliance_capacity_1").val();
        if(capacity === null && capacity === ""){
            postData['capacity'] = "";
            $("#appliance_capacity_1").removeAttr("required");
        } else {
            postData['capacity'] = capacity;
        }
        postData['booking_id'] = '<?php echo $booking_history[0]['booking_id']; ?>';
        postData['service_category'] = '<?php  echo $price_tags; ?>';
        postData['pincode'] = $("#booking_pincode").val();
        postData['city'] = $("#booking_city").val();
        postData['assigned_vendor_id'] = $("#assigned_vendor_id").val();
        postData['is_repeat'] = '<?php echo $is_repeat;?>';
        postData['partner_type'] = '<?php echo $partner_type;?>';
        postData['contact'] = '<?php echo $booking_history[0]['booking_primary_contact_no']; ?>';
        
        if( postData['brand'] !== null 
                && postData['category'] !== null && postData['pincode'].length === 6 && postData['city'] !== null){
           
            $.ajax({
                type: 'POST',
                beforeSend: function(){
                  
                  $('#submitform').attr('disabled',true);
                  
                },
                url: '<?php echo base_url(); ?>employee/partner/get_price_for_partner',
                data: postData,
                success: function (data) {
                   //console.log(data);
                     if(data === "ERROR"){
                         // $("#total_price").text("Price is not defined" );
                          alert("Outstation Bookings Are Not Allowed, Please Contact 247around Team.");
    
                     } else {                         
                          var data1 = jQuery.parseJSON(data);
                         
                          $("#priceList").html(data1.table);
                          $("#upcountry_data").val(data1.upcountry_data);
                          $('#submitform').attr('disabled',false);
                          final_price();
                          set_upcountry();
                          //get_symptom('<?php echo (!empty($symptom[0]['symptom'])?$symptom[0]['symptom']:''); ?>');
                     }
                }
            });
            
            
        } else {
       // $("#total_price").html("Please Enter Above Field");
         //  return false;
        }
        
        $(this).dequeue();
    });
    
    
    }
    
    
    // In AC Installation case drain pipe per litter and 22 gauge and small stand should be auto select
    function disableCheckbox(obj) {
        if($(obj).prop("checked") == true) {
            var price_tag = $(obj).attr('data-price_tag');
            if(price_tag.indexOf('Installation') != -1 && $("#service_name").val() == '50') {
                $('.price_checkbox[data-price_tag="Small Stand"]').prop('checked', true).css('pointer-events', 'none');
                $('.price_checkbox[data-price_tag="Drain Pipe Per Meter"]').prop('checked', true).css('pointer-events', 'none');
                $('.price_checkbox[data-price_tag="22 Gauge Refrigerant Pipe, Insulation, Wire Set / ft"]').prop('checked', true).css('pointer-events', 'none');
            } 
        } else {
            $('.price_checkbox[data-price_tag="Drain Pipe Per Meter"]').prop('checked', false).css('pointer-events', 'auto');
            $('.price_checkbox[data-price_tag="Small Stand"]').prop('checked', false).css('pointer-events', 'auto');
            $('.price_checkbox[data-price_tag="22 Gauge Refrigerant Pipe, Insulation, Wire Set / ft"]').prop('checked', false).css('pointer-events', 'auto');
        }
    }
    
    $("#booking_pincode").keyup(function(event) {
        var pincode = $("#booking_pincode").val();
        var service_id =  $("#service_name").val();
        if(pincode.length === 6){
            
            $.ajax({
                type: 'POST',
                beforeSend: function(){
                  
                    $('#city_loading').css("display", "-webkit-inline-box");
                    $('#submitform').prop('disabled', true);
                },
                url: '<?php echo base_url(); ?>employee/partner/get_district_by_pincode/'+ pincode+"/"+service_id,          
                success: function (data) {
                 
                   if(data !== "ERROR"){
                        $('#booking_city').select2().html(data).change();
                        $("#booking_city").select2({
                           tags: true
                        });
                       
                         $('#submitform').prop('disabled', false);
                         getPrice();
                        
                    } else {
                        alert("Service Temporarily Un-available In This Pincode, Please Contact 247around Team.");
                        $('#submitform').prop('disabled', true);
                        
                    }
                    
                },
                complete: function(){
                    $('#city_loading').css("display", "none");
                }  
            }); 
        }
        
    });
    
    function set_upcountry(){
        var upcountry_data = $("#upcountry_data").val();
        console.log(upcountry_data);
        is_upcountry = 0;
        count = 0;
        non_upcountry = 0;
        flat_upcountry = 0;
        var customer_price = 0;
        n = 0;
        $("input[type=checkbox]:checked").each(function (i) {
            count = count + 1;
    
            var id = this.id.split('checkbox_');
    
            var up_val = $("#is_up_val_" + id[1]).val();
            
            var f = Number($("#is_up_val_" + id[1]).attr("data-flat_upcountry"));
            if(f === 1){
                customer_price = Number($("#is_up_val_" + id[1]).attr("data-customer_price"));

                flat_upcountry = f;
            }
    
            if (Number(up_val) === 1) {
                is_upcountry = 1;
            } else  if (Number(up_val) === -1) {
                non_upcountry = -1;
            } else {
                n = 1;
            }
        });
        if (count > 0) {
            var data1 = jQuery.parseJSON(upcountry_data);
            switch(data1.message){
                case 'UPCOUNTRY BOOKING':
                case 'UPCOUNTRY LIMIT EXCEED':
                    if(Number(is_upcountry) == 1 && Number(data1.partner_provide_upcountry) == 0 ){
    
                        if(flat_upcountry == 1){
                            var upcountry_charges =  customer_price;
                            
                        } else {
                            var upcountry_charges = (Number(3) * Number(data1.upcountry_distance)).toFixed(2);
                        }
    
                        $("#upcountry_charges").text(upcountry_charges);
                        $("#checkbox_upcountry").val("upcountry_" + upcountry_charges + "_0");
                        document.getElementById("checkbox_upcountry").checked = true;
                        alert("This is upcountry call. Please inform to customer that booking will be completed in 3 Days");
                        $('#submitform').attr('disabled', false); 
                        final_price();
    
                    } else if(Number(is_upcountry) == 1 && Number(data1.partner_provide_upcountry) == 1){
                        var partner_approval = Number(data1.partner_upcountry_approval);
    
                            if (data1.message === "UPCOUNTRY BOOKING") {
                                $("#upcountry_charges").text("0.00");
                                $("#checkbox_upcountry").val("upcountry_0_0");
                                document.getElementById("checkbox_upcountry").checked = false;
                                final_price();
                                alert("This is upcountry call. Please inform to customer that booking will be completed in 3 Days");
                                $('#submitform').attr('disabled', false); 
    
                            } else if (data1.message === "UPCOUNTRY LIMIT EXCEED" && partner_approval === 0) {
                                $('#submitform').attr('disabled', true);
                                 document.getElementById("checkbox_upcountry").checked = false;
                                 $("#upcountry_charges").text("0.00");
                                 $("#checkbox_upcountry").val("upcountry_0_0"); 
                                 document.getElementById("checkbox_upcountry").checked = false;
                                 final_price();
                                alert("This is out station Booking, not allow to submit Booking/Query. Upcountry Distance "+ data1.upcountry_distance.toFixed(2) + " KM");
                            } else if (data1.message === "UPCOUNTRY LIMIT EXCEED" && partner_approval === 1) {
                                alert("This is out station boking, Waiting for Partner Approval. Upcountry Distance " +data1.upcountry_distance.toFixed(2) + " KM");
    
                                 $("#upcountry_charges").text("0.00");
                                 $("#checkbox_upcountry").val("upcountry_0_0"); 
                                 document.getElementById("checkbox_upcountry").checked = false;
                                 final_price();
                                 $('#submitform').attr('disabled', false);
                            } else {
                                $("#upcountry_charges").text("0.00");
                                $("#checkbox_upcountry").val("upcountry_0_0");
                                 document.getElementById("checkbox_upcountry").checked = false;
                                $('#submitform').attr('disabled', false); 
                            }
                    } else {
                        if(Number(is_upcountry) == 0 && Number(non_upcountry) == 0){
    
                            if(flat_upcountry == 1){
                                var upcountry_charges =  customer_price;
                                
                            } else {
                                var upcountry_charges = (Number(3) * Number(data1.upcountry_distance)).toFixed(2);
                            }
    
                            $("#upcountry_charges").text(upcountry_charges);
                            $("#checkbox_upcountry").val("upcountry_" + upcountry_charges + "_0");
                            document.getElementById("checkbox_upcountry").checked = true;
    
                            final_price();
    
                        } else if(Number(is_upcountry) == 0 && Number(non_upcountry) == -1 && n == 0){
    
                            $("#upcountry_charges").text("0.00");
                            $("#checkbox_upcountry").val("upcountry_0_0");
                            document.getElementById("checkbox_upcountry").checked = false;
                            final_price();
                            $('#submitform').attr('disabled', false);
                        } else if(Number(is_upcountry) == 0 && Number(non_upcountry) == -1 && n == 1){
    
                             if(flat_upcountry == 1){
                                var upcountry_charges =  customer_price;
                                
                            } else {
                                var upcountry_charges = (Number(3) * Number(data1.upcountry_distance)).toFixed(2);
                            }
    
                            $("#upcountry_charges").text(upcountry_charges);
                            $("#checkbox_upcountry").val("upcountry_" + upcountry_charges + "_0");
                            document.getElementById("checkbox_upcountry").checked = true;
    
                            final_price();
                        }
                        $('#submitform').attr('disabled', false);
                    }
                    break;
                default:
                    $("#upcountry_charges").text("0.00");
                    $("#checkbox_upcountry").val("upcountry_0_0");
                    document.getElementById("checkbox_upcountry").checked = false;
                    final_price();
                    $('#submitform').attr('disabled', false);
                    break;
            }
        } else {
    
            $("#upcountry_charges").text("0.00");
            $("#checkbox_upcountry").val("upcountry_0");
            final_price();
            $('#submitform').attr('disabled', true);
        }
    
        //var not_visible = $("#not_visible").val();
    
    //        if(Number(not_visible) === 0){
    //
    //         alert('Service Temporarily Un-available In This Pincode, Please Contact 247around Team');
    //         display_message("not_visible","error_not_visible","red","Service Temporarily Un-available In This Pincode, Please Contact 247around Team.");
    //          $('#submitform').attr('disabled', true);
    //             return false;
    //        } else {
    //          display_message("not_visible","error_not_visible","","");
    //          $('#submitform').attr('disabled', false);
    //
    //       }        
    }           
    function final_price(){
        var price = 0;
        var price_array ;
        ch =0;
        var appliance_unit =$("#appliance_unit").val();
        
         $("input[type=checkbox]:checked").each(function(i) {
            price_array = $(this).val().split('_');
            //console.log(price_array);
           price += (Number(price_array[1]) -Number(price_array[2]) );
            if(price_array[0] !== "upcountry"){
                ch = 1;
            }
           
        });
        if(ch === 0){
            document.getElementById("checkbox_upcountry").checked = false;
            $("#grand_total").val("0.00");
            
        } else {
            var final_price = Number(price) * Number(appliance_unit);
            $("#grand_total").val(final_price.toFixed(2));
        }
        
    
    }
    
    $(document).ready(function(){
       
       <?php
        if($is_repeat){ ?>
           $("#repeat_reason_holder").show();
      <?php  }
        ?>
        $("#dealer_phone_number").keyup(function(){
            var partner_id = '<?php echo $this->session->userdata('partner_id')?>';
            if(partner_id !== undefined){
                 var search_term = $(this).val();
                 var regex = new RegExp("^[0-9 ]+$");
                 if(regex.test(search_term)){
                     dealer_setup(partner_id, search_term, "dealer_phone_number_1");
                 }else{
                     alert("Please enter correct phone number");
                 }
            } else{
                alert("Please Select Partner");
            }
        });
    $("#dealer_name").keyup(function(){
            var partner_id = '<?php echo $this->session->userdata('partner_id')?>';
            if(partner_id !== undefined){
                var search_term = $(this).val();
                var regex = new RegExp("^[a-zA-Z ]+$");
                if(regex.test(search_term)){
                     dealer_setup(partner_id, search_term, "dealer_name");
                }else{
                     alert("Please enter correct name");
                }
                 
            } else{
                alert("Please Select Partner");
            }
        });
        
        
    });
        
     
        function selectDealer(name,ph, id) {
    
            $("#dealer_phone_number").val(ph);
            $("#dealer_name").val(name);
            $("#dealer_id").val(id);
    
            $("#dealer_phone_suggesstion_box").hide();
         }
         
    function selectDealer(name,ph, id) {
    
        $("#dealer_phone_number").val(ph);
        $("#dealer_name").val(name);
        $("#dealer_id").val(id);
    
        $("#dealer_phone_suggesstion_box").hide();
        $("#dealer_name_suggesstion_box").hide();
    }
    
    function dealer_setup(partner_id,search_term,search_filed){
    
        if(search_term === ""){
            $("#dealer_id").val("");
            $("#dealer_name").val("");
            $("#dealer_phone_number").val("");
            $("#dealer_phone_suggesstion_box").hide();
            $("#dealer_name_suggesstion_box").hide();
        }else{
    
            $.ajax({
                type: "POST",
                url: baseUrl + "/employee/partner/get_dealer_details",
                data: {partner_id: partner_id, search_term: search_term,dealer_field: search_filed},
                beforeSend: function () {
                    //$("#search-box").css("background","#FFF url(LoaderIcon.gif) no-repeat 165px");
                },
                success: function (data) {
                    if(search_filed === "dealer_phone_number_1"){
                        $("#dealer_phone_suggesstion_box").show();
                        $("#dealer_phone_suggesstion_box").html(data);
                        $("#dealer_phone_number").css("background", "#FFF");
                   } else {
                        $("#dealer_name_suggesstion_box").show();
                        $("#dealer_name_suggesstion_box").html(data);
                        $("#dealer_name").css("background", "#FFF");
                   }
                }
            });
        }
    }
    $('#purchase_date').daterangepicker({
                autoUpdateInput: false,
                singleDatePicker: true,
                showDropdowns: true,
                minDate:"1998-01-01",
                maxDate:today,
                locale:{
                    format: 'YYYY-MM-DD'
                }
            });
            
    $('#purchase_date').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD'));
    });
    
    $('#purchase_date').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
    
    function check_active_paid(no){
        
    }
    getPartnerChannel();
    function getPartnerChannel(){
        var partnerChannelServiceUrl = '<?php echo base_url(); ?>employee/partner/get_partner_channel/';
        
        var postData = {};
        postData['partner_id'] = '<?php echo $this->session->userdata('partner_id')?>';
        postData['channel'] = $("#partner_channel").val();
        postData['is_repeat'] = '<?php echo $is_repeat; ?>';
        if( postData['partner_id'] !== null){
            sendAjaxRequest(postData, partnerChannelServiceUrl).done(function (data) {
               $("#partner_source").html("");
               $("#partner_source").html(data).change();
            });
        }
    }
    function get_parent_booking(contactNumber,serviceID,partnerID,isChecked,is_already_repeat){
        if(isChecked){
            if(!is_already_repeat){
              $.ajax({
                      type: 'POST',
                      url: '<?php echo base_url(); ?>employee/partner/get_posible_parent_id',
                      data: {contact: contactNumber, service_id: serviceID,partnerID:partnerID,day_diff:<?php echo _PARTNER_REPEAT_BOOKING_ALLOWED_DAYS; ?>},
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
                             $("#parent_booking").val(obj.html);
                             $("#repeat_reason_holder").show();
                          }
                          else if(obj.status  == <?Php echo _MULTIPLE_REPEAT_BOOKING_FLAG; ?>){
                              $('.Service:checked').prop('checked', false);
                                $('.Service').each(function() {
                                    $(this).prop('disabled', true);
                                });
                              $('#repeat_booking_model').modal('show');
                              $("#repeat_booking_body").html(obj.html);
                              $("#repeat_reason_holder").show();
                          }
                      }
                  });
              }
              else{
                $('.Service:checked').prop('checked', false);
                $('.Service').each(function() {
                    $(this).prop('disabled', true);
                });
                $("#parent_booking").val($("#parent_id_temp").text());
              }
           }
           else{
                $('.Service').each(function() {
                    $(this).prop('disabled', false);
                });
                $("#parent_booking").val("");
           }
    }
    function parentBooking(id){
        $("#parent_booking").val(id);
        $('#repeat_booking_model').modal('hide');
    }
    
    function escapeRegExp(string){
       return string.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
    }

    function replaceAll(str, term, replacement) {
        return str.replace(new RegExp(escapeRegExp(term), 'g'), replacement);
    }
      function get_symptom(symptom_id = ""){
        var array = [];
        var postData = {};
        $(".price_checkbox:checked").each(function (i) {
            var price_tags = $("#"+ $(this).attr('id')).attr('data-price_tag');
            var price_tags1 = replaceAll(price_tags, '(Free)', '');
            var price_tags2 = replaceAll(price_tags1, '(Paid)', '');
            if(price_tags2 === '<?php echo REPAIR_IN_WARRANTY_TAG; ?>' || 
                    
                    price_tags === "<?php echo REPAIR_OOW_TAG; ?>" ){
                $("#appliance_unit").val('1');
                final_price();
                $('#appliance_unit').prop("disabled", true);
                
            } else{
                 $('#appliance_unit').prop("disabled", false); 
            }
            array.push(price_tags2);
    
        });
        if(array.length > 0){
            postData['partner_id'] = '<?php echo $this->session->userdata('partner_id')?>';
            postData['request_type'] = array;
            postData['service_id'] = $("#service_name").val();
            postData['booking_request_symptom'] = symptom_id;
            var url = '<?php echo base_url();?>employee/booking_request/get_booking_request_dropdown';
            sendAjaxRequest(postData, url).done(function (data) {
                $('#booking_request_symptom').html("<option disabled selected>Please Select Any Symptom</option>");
                if(data === "Error"){
                    $('#booking_request_symptom').append("").change();
                    $("#booking_request_symptom").removeAttr('required');
                } else {
                    $('#booking_request_symptom').append(data).change();
                    $("#booking_request_symptom").attr('required', 'required');

                }
            });
        }
    
    }
</script>