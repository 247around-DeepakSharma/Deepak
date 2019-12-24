<script src="<?php echo base_url();?>js/validation_js.js"></script>
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
<div class="right_col" role="main">
<?php
    if ($this->session->userdata('success')) {
        echo '<div class="alert alert-danger alert-dismissible" role="alert" style="margin-top: 55px;">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <strong>' . $this->session->userdata('success') . '</strong>
                                </div>';
    }
    ?>
<?php
    if ($this->session->userdata('error')) {
        echo '<div class="alert alert-danger alert-dismissible" role="alert" style="margin-top: 55px;">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <strong>' . $this->session->userdata('error') . '</strong>
                                </div>';
    }
    ?>
<form name="myForm" class="form-horizontal" onSubmit="document.getElementById('submitform').disabled=true;" id ="booking_form" action="<?php echo base_url()?>employee/partner/process_addbooking"  method="POST" enctype="multipart/form-data">
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
                            <input type="text" class="form-control"  id="booking_primary_contact_no" name="booking_primary_contact_no" value = "<?php if(isset($user[0]['phone_number'])){ echo $user[0]['phone_number']; } else if($phone_number !="process_addbooking"){ echo  $phone_number; }  ?>" required>
                            <span id="error_mobile_number" style="color:red"></span>
                        </div>
                    </div>
                    <div class="col-md-4" >
                        <div class="form-group col-md-12 <?php if( form_error('user_name') ) { echo 'has-error';} ?>">
                            <label for="name">Name * <span id="error_username" style="color: red;"></span></label>
                            <input type="hidden" name="assigned_vendor_id" id="assigned_vendor_id" value="" />
                            <input type="hidden" id="partner_channel" value=""/>
                            <input type="hidden" name="upcountry_data" id="upcountry_data" value="" />
                            <input type="hidden" name="partner_type" id="partner_type" value="<?php echo $partner_type;?>" />
                            <input type="hidden" name="partner_code" id="partner_code" value="<?php echo $partner_code;?>" />
                            <input type="hidden" name="is_active" id="is_active" value="<?php echo $this->session->userdata('status');?>" />
                            <input type="text" class="form-control" id="name" name="user_name" value = "<?php if(isset($user[0]['name'])){ echo $user[0]['name']; } else { echo set_value('user_name'); }  ?>" <?php //if(isset($user[0]['name'])){ echo "readonly"; }  ?> placeholder="Please Enter User Name">
                            <?php echo form_error('user_name'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group col-md-12 <?php if( form_error('service_id') ) { echo 'has-error';} ?>">
                            <label for="service_name">Appliance * <span id="error_appliance" style="color: red;"></span></label>
                            <select class="form-control"  id="service_name" name="service_id"   required onchange="return enablePincode(),get_city(), get_brands(), get_category(), get_capacity()">
                                <option selected disabled>Select Appliance</option>
                                <?php foreach ($appliances as $values) { ?>
                                <option <?php if(count($appliances) ==1){echo "selected";} ?> data-id="<?php echo $values->services;?>" value=<?= $values->id; ?>>
                                    <?php echo $values->services; }    ?>
                                </option>
                            </select>
                            <?php echo form_error('service_id'); ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-4">
                        <div class="form-group col-md-12  <?php if( form_error('booking_pincode') ) { echo 'has-error';} ?> ">
                            <label for="booking_pincode">Pincode *   <span id="error_pincode" style="color: red;"></span></label>
                            <input type="text" class="form-control" id="booking_pincode" name="booking_pincode" value = "<?php if(isset($user[0]['pincode'])){echo $user[0]['pincode'];} else { echo set_value('booking_pincode');} ?>" placeholder="Enter Area Pin" required>
                            <?php echo form_error('booking_pincode'); ?>
                        </div>
                    </div>
                    <div class="col-md-4 ">
                        <div class="form-group col-md-12  <?php if( form_error('city') ) { echo 'has-error';} ?>">
                            <label for="booking_city">City * <span id="error_city" style="color: red;"></span><span style="color:grey;display:none" id="city_loading">Loading ...</span></label>
                            <select class="form-control"  id="booking_city" name="city" required>
                                <option selected="selected" disabled="disabled">Select City</option>
                                <?php if(isset($user[0]['city'])){ ?>
                                <option selected><?php echo $user[0]['city']; ?></option>
                                <?php  }
                                    ?>
                            </select>
                            <?php echo form_error('city'); ?>
                        </div>
                    </div>
                    <input type="hidden" name="appliance_name" id="appliance_name" value=""/>
                    <div class="col-md-4">
                        <div class="form-group col-md-12 <?php if( form_error('appliance_brand') ) { echo 'has-error';} ?>">
                            <label for="appliance_brand_1">Brand *   <span style="color:grey;display:none" id="brand_loading">Loading ...</span> <span id="error_brand" style="color: red;"></label>
                            <select class="form-control appliance_brand"    name="appliance_brand" id="appliance_brand_1" required onchange="return get_category()">
                                <option selected disabled value="option1">Select Brand</option>
                            </select>
                            <?php echo form_error('appliance_brand'); ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-4">
                        <div class="form-group col-md-12 <?php if( form_error('appliance_category') ) { echo 'has-error';} ?>">
                            <label for="appliance_category_1">Category *<span style="color:grey;display:none" id="category_loading">Loading ...</span> <span id="error_category" style="color: red;"></label>
                            <select class="form-control appliance_category"   id="appliance_category_1" name="appliance_category"   required onchange="return get_capacity()">
                                <option selected disabled value="option1">Select Appliance Category</option>
                            </select>
                            <?php echo form_error('appliance_category'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group col-md-12 <?php if( form_error('appliance_capacity') ) { echo 'has-error';} ?>">
                            <label for="appliance_capacity_1">Capacity *  <span style="color:grey;display:none" id="capacity_loading">Loading ...</span> <span id="error_capacity" style="color: red;"></label>
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
                                <select class="form-control select-model"  name="model_number" id="model_number_1" >
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
                            <input type="text" class="form-control" readonly="" id="booking_date" name="booking_date"  value = "<?php echo date('H') >= 12 ? date("d-m-Y", strtotime("+1 day")):date("d-m-Y", strtotime("+0 day")); ?>" style="background-color:#FFF;" >
                            <?php echo form_error('booking_date'); ?>
                        </div>
                    </div>
                    <div class="col-md-4 ">
                        <div class="form-group col-md-12  <?php if( form_error('partner_source') ) { echo 'has-error';} ?>">
                            <label for="partner_source">Seller Channel* <span id="error_seller" style="color: red;"></label>
                            <select class="form-control"  id="partner_source" name="partner_source" required>
                                <option value="" selected disabled>Please select seller channel</option>
                                <?php if(isset($channel)) {
                                    foreach ($channel as $key => $value) { ?>
                                <option><?php echo $value['channel_name'];  ?></option>
                                <?php } } ?>
                            </select>
                            <?php echo form_error('partner_source'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group col-md-5 ">
                            <label for="appliance_unit">Unit* <span id="error_unit" style="color: red;"></label>
                            <select disabled="" style="width:55%" class="form-control" onchange="final_price()"   id="appliance_unit" name="appliance_unit" >
                                <?php for($i =1; $i <11; $i++) { ?>
                                <option value="<?php echo $i;?>"><?php echo $i; ?></option>
                                <?php }?>
                            </select>
                        </div>
                        <!--                                <div class="form-group col-md-7  <?php //if( form_error('product_type') ) { echo 'has-error';} ?>">
                            <label for="Product Type">Product Type *</label>
                              <label class="radio-inline">
                                 <input type="radio" name="product_type" value="Delivered" checked>Delivered
                               </label>
                               <label class="radio-inline">
                                   <input type="radio" name="product_type" value="Shipped">Shipped
                               </label>
                             <?php //echo form_error('product_type'); ?>
                            </div>-->
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
                    <div class="col-md-12" style="margin-bottom:10px;padding:10px;">
                        <span style="color:red;text-align: center;font-size: 16px;font-weight:bold;" class="errorMsg"></span>
                    </div>
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
                <div class="form-group col-md-12    <?php if (form_error('order_id')) {
                    echo 'has-error';
                    } ?>">
                    <label for="order_id">Reference / Invoice / Order Number <span id="error_order_id" style="color:red"></span></label>
                    <input class="form-control" name= "order_id" value="<?php echo set_value('order_id'); ?>" placeholder ="Please Enter Reference / Invoice / Order Number" id="order_id" />
                </div>
            </div>
            <div class="col-md-3 ">
                <div class="form-group col-md-12  <?php if (form_error('serial_number')) {
                    echo 'has-error';
                    } ?>">
                    <label for="serial_number">Serial Number <span id="error_serial_number" style="color:red"></span></label>
                    <input  type="text" class="form-control"  name="serial_number" id="serial_number" value = "<?php echo set_value('serial_number'); ?>" placeholder="Enter Serial Number" onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || (event.charCode > 47 && event.charCode < 58) || event.charCode == 8" >
                </div>
            </div>
            <div class="col-md-3 ">
                <div class="form-group col-md-12  <?php if (form_error('dealer_phone_number')) {
                    echo 'has-error';
                    } ?>">
                    <label for="dealer_phone_number">Dealer Phone Number  <span id="error_dealer_phone_number" style="color:red"></span></label>
                    <input  type="text" class="form-control"  name="dealer_phone_number" id="dealer_phone_number" value = "<?php echo set_value('dealer_phone_number'); ?>" placeholder="Enter Dealer Phone Number" autocomplete="off">
                    <div id="dealer_phone_suggesstion_box"></div>
                </div>
            </div>
            <div class="col-md-3 ">
                <div class="form-group col-md-12  <?php if (form_error('dealer_name')) {
                    echo 'has-error';
                    } ?>">
                    <label for="dealer_name">Dealer Name   <span id="error_dealer_name" style="color:red"></span></label>
                    <input  type="text" class="form-control"  name="dealer_name" id="dealer_name" value = "<?php echo set_value('dealer_name'); ?>" placeholder="Enter Dealer Name" autocomplete="off">
                    <input type="hidden" name="dealer_id" id="dealer_id" value="">
                    <div id="dealer_name_suggesstion_box"></div>
                </div>
            </div>
            <div class="col-md-3 ">
                <div class="form-group col-md-12" style="margin: 0px;padding: 0px;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group col-md-12  <?php if( form_error('purchase_date') ) { echo 'has-error';} ?>">
                                <label for="purchase_date">Purchase Date * <span id="error_purchase_date" style="color: red;"></span></label>
                                <input style="background-color:#FFF;"  readonly="" placeholder="Please Choose Purchase Date" type="text" class="form-control"  id="purchase_date" name="purchase_date"  value = "" max="<?=date('d-m-Y');?>" autocomplete='off' onkeydown="return false" >
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
                                </select>
                                <?php echo form_error('booking_request_symptom'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group col-md-12  <?php if (form_error('query_remarks')) { echo 'has-error';} ?>">
                    <label for="remarks">Remarks*  <span id="error_remarks" style="color: red;"></label>
                    <textarea class="form-control" rows="2" id="remarks" name="query_remarks"  placeholder="Enter Problem Description" ><?php echo set_value('query_remarks'); ?></textarea>
                    <?php echo form_error('query_remarks'); ?>
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
                            <div class="form-group col-md-12  <?php if (form_error('alternate_phone_number')) { echo 'has-error';} ?>">
                                <label for="booking_alternate_contact_no">Alternate Mobile<span id="error_alternate_contact_no" style="color: red;"></span></label>
                                <input class="form-control booking_alternate_contact_no"  id="booking_alternate_contact_no" name="alternate_phone_number" value = "<?php if (isset($user[0]['alternate_phone_number'])) {
                                    echo $user[0]['alternate_phone_number'];
                                    } else {
                                    echo set_value('alternate_phone_number');
                                    } ?>" placeholder ="Please Enter Alternate Contact No" >
                                <?php echo form_error('alternate_phone_number'); ?>
                            </div>
                        </div>
                        <div class="col-md-4 ">
                            <div class="form-group col-md-12  <?php if (form_error('user_email')) { echo 'has-error';} ?>">
                                <label for="booking_user_email">Email </label>
                                <input type="email" class="form-control"  id="booking_user_email" name="user_email" value = "<?php if (isset($user[0]['user_email'])) {
                                    echo $user[0]['user_email'];
                                    } ?>" placeholder="Please Enter User Email">
                                <?php echo form_error('user_email'); ?>
                            </div>
                        </div>
                        <div class="col-md-4 ">
                            <div class="form-group col-md-12  <?php if (form_error('landmark')) { echo 'has-error'; } ?>">
                                <label for="landmark">Landmark </label>
                                <input type="text" class="form-control" id="landmark" name="landmark" value = "<?php if (isset($user[0]['landmark'])) {
                                    echo $user[0]['landmark'];
                                    } else {
                                    echo set_value('landmark');
                                    } ?>" placeholder="Enter Any Landmark">
                                <?php echo form_error('landmark'); ?>
                            </div>
                        </div>
                        <div class="col-md-12 ">
                            <div class="form-group col-md-12  <?php if (form_error('booking_address')) { echo 'has-error';} ?>">
                                <label for="booking_address">Booking Address *  <span id="error_address" style="color: red;"></label>
                                <textarea class="form-control" rows="2" id="booking_address" name="booking_address" placeholder="Please Enter Address"  required ><?php if (isset($user[0]['home_address'])) { echo $user[0]['home_address']; } else { echo set_value('booking_address');} ?></textarea>
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
                        <input type="hidden" id="not_visible" name="not_visible" value="0"/>
                        <input type="hidden" name="product_type" value="Delivered"/>
                        <?php //if($this->session->userdata('partner_id') == VIDEOCON_ID) { ?>
<!--                        <input type="hidden" name="product_type" value="Shipped"/>-->
                        <?php// } else { ?>
<!--                        <input type="hidden" name="product_type" value="Delivered"/>-->
                        <?php // }//if(!empty($this->session->userdata('status'))) {?>
                        <div class="row">
                            <div class="form-group  col-md-12" >
                                <center>
                                    <input type="button" id="submitform" class="btn btn-primary " onclick="return check_validation()" value="Submit">
                                    <p id="error_not_visible" style="color: red"></p>
                                </center>
                            </div>
                        </div>
                        <?php //} ?>
</form>
<?php if(empty($this->session->userdata('status'))) { ?>
<div class="row">
<div class="form-group  col-md-12" >
<center>
<!--                                        <input type="submit" class="btn btn-primary " disabled value="Submit Booking"><br/><br/><br/>-->
<p id="error_not_visible" style="color: red; margin-top: 10px;"></p>
</center>
</div>
</div>
<?php }?>
</div>
</div>
</div>
</div>
</form>
</div>
<!--Modal -->
<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="text-align: center;margin: 0px;">
                <button type="button" id="close_modal" class="close btn-primary well" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Verify Booking Details</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group col-md-12  <?php if( form_error('booking_appliance') ) { echo 'has-error';} ?> ">
                            <label for="service_name">Appliance * <span id="error_booking_appliance" style="color: red;"></span></label>
                            <input type="text" class="form-control" id="booking_appliance" name="booking_appliance" placeholder="Enter Appliance" required readonly>
                            <input type="hidden" id="service_id" name="service_id">
                            <?php echo form_error('booking_appliance'); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group col-md-12  <?php if( form_error('pincode') ) { echo 'has-error';} ?> ">
                            <label for="pincode">Pincode * <span id="error_pincode1" style="color: red;"></span></label>
                            <input type="text" class="form-control" id="pincode" name="booking_pincode" placeholder="Enter Area Pin" required>
                            <?php echo form_error('pincode'); ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 ">
                        <div class="form-group col-md-12  <?php if( form_error('city') ) { echo 'has-error';} ?>">
                            <label for="city">City * <span id="error_city1" style="color: red;"></span><span style="color:grey;display:none" id="city_loading">Loading ...</span></label>
                            <select class="form-control"  id="city" name="city" required style="width: 404px;">
                                <option selected="selected" disabled="disabled">Select City</option>
                                <?php if(isset($user[0]['city'])){ ?>
                                <option selected><?php echo $user[0]['city']; ?></option>
                                <?php  }
                                    ?>
                            </select>
                            <?php echo form_error('city'); ?>
                        </div>
                    </div>
                    <div class="col-md-6 ">
                        <div class="form-group col-md-12  <?php if (form_error('address')) { echo 'has-error';} ?>">
                            <label for="address">Booking Address *  <span id="error_address1" style="color: red;"></label>
                            <textarea class="form-control" rows="2" id="address" name="booking_address" placeholder="Please Enter Address"  required ><?php if (isset($user[0]['home_address'])) {
                                echo $user[0]['home_address'];
                                } else {
                                    echo set_value('booking_address');
                                } ?>
                            </textarea>
                            <?php echo form_error('address'); ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btn_submit" onclick="return check_address_validation()" class=" btn btn-success">Submit Booking</button>
                    <button type="button" id="btn_cancel" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Modal ends-->
<?php if($this->session->userdata('success')){$this->session->unset_userdata('success');} ?>
<?php if($this->session->userdata('error')){$this->session->unset_userdata('error');} ?>
<script type="text/javascript">
    
    var blDisableAcCategoryOptions = "<?= ($this->session->userdata('user_group') == PARTNER_CALL_CENTER_USER_GROUP ? '1' : '0'); ?>";
    
    function check_validation(){ 
        var exp1 = /^[6-9]{1}[0-9]{9}$/;
        var user_name  = $("#name").val();
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
        var not_visible = $("#not_visible").val();
        var purchase_date = $("#purchase_date").val();
        var symptom = $('#booking_request_symptom option:selected').text();
        var alternate_contact_no = $("#booking_alternate_contact_no").val();
        //var model_value = $("#model_number_1").val();
        var user_regex = /^([a-zA-Z\s]*)$/;
        if(!mobile_number.match(exp1)){
            alert('Please Enter Valid User Phone Number');   
            display_message("booking_primary_contact_no","error_mobile_number","red","Please Enter Valid User Phone Number");
            return false;
        }
         if(mobile_number === ""){
            display_message("booking_primary_contact_no","error_mobile_number","red","Please Enter Mobile");
             return false;
        } else {
            display_message("booking_primary_contact_no","error_mobile_number","green","");
             
        }
        if(user_name === "" || user_name.trim().length == '0'){
            display_message("name","error_username","red","Please Enter User Name");
            return false;
        }else {
           display_message("name","error_username","green","");
            
        }
        if(!user_name.match(user_regex)){
            display_message("name","error_username","red","Please Enter Valid User Name");
            return false;
        }else {
           display_message("name","error_username","green","");
            
        }
        if(appliance === null){
            display_message("service_name","error_appliance","red","Please Select Appliance");
             return false;
        } else {
            display_message("service_name","error_appliance","green","");
        }
        if(pincode === ""){
              display_message("booking_pincode","error_pincode","red","Please Enter Pincode");
             return false;
        } else {
           display_message("booking_pincode","error_pincode","green","");
            
        }
        if((city === null) || ($.trim(city) === '')){
            
             display_message("booking_city","error_city","red","Please Enter City");
             return false;
        } else {
             display_message("booking_city","error_city","green","");
            
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
        
    //        if(model_value === ""){
    //            display_message("model_number_1","error_model","red","Please Select Model");
    //             return false;
    //        } else {
    //              display_message("model_number_1","error_model","green","");
    //        }
        if(partner_source === "" || partner_source === null){
           
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
        if(dealer_phone_number !== "" && dealer_name === ""){
             alert("Please Enter Dealer Name");
             return false;
        }
         if(dealer_phone_number !=="" && !dealer_phone_number.match(exp1)){
            alert('Please Enter Valid Dealer Phone Number');   
            return false;
        }
        if(purchase_date === ""){
              display_message("purchase_date","error_purchase_date","red","Please Enter Purchase Date");
             return false;
        } else {
           display_message("purchase_date","error_purchase_date","green",""); 
        }
        if((symptom == 0) || (symptom == '0')) {

        }
        else if(symptom === "" || symptom === "Please Select Any Symptom"){
            alert("Please Enter Symptom");
            return false;
        }
        
        if(not_visible === 0){
             display_message("not_visible","error_not_visible","red","Service Temporarily Un-available In This Pincode, Please Contact backoffice Team.");
             return false;
        }
        
    //        if(order_id === '' && serial_number === '' && dealer_phone_number === ''){
    //            alert("Please Fill Any one of these Order Id/Serial Number/Dealer Phone Number");
    //            return false;
    //        } 
        
       
        
        
    //        if (order_id === "" && serial_number === ""  ) {
    //             document.getElementById('order_id').style.borderColor = "red";
    //             document.getElementById('serial_number').style.borderColor = "red";
    //            document.getElementById('error_order_id').innerHTML = "Please enter Order ID";
    //            document.getElementById('error_serial_number').innerHTML = "Please enter Serial Number";
    //               
    //            return false;
    //        } else {
    //    
    //            document.getElementById('order_id').style.borderColor = "green";
    //            document.getElementById('serial_number').style.borderColor = "green";
    //            document.getElementById('error_order_id').innerHTML = "";
    //            document.getElementById('error_serial_number').innerHTML = "";
    //        }
    
        if(booking_address.trim().length < 1){
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
        
        if(alternate_contact_no !== "" && !alternate_contact_no.match(exp1)){
            alert('Please Enter Valid Alternate Phone Number');   
            display_message("booking_alternate_contact_no","error_alternate_contact_no","red","Please Enter Valid Alternate Mobile");
            return false;
        }
        
        var delivered_price_tags = [];
        $(".price_checkbox:checked").each(function (i) {
            var price_tags = $("#"+ $(this).attr('id')).attr('data-price_tag');
            delivered_price_tags.push(price_tags);

        });
        
        var pr = checkPriceTagValidation(delivered_price_tags);
        if(pr === false){
            alert('Not Allow to select multiple different type of service category');
            $("#selected_service").css("color","red");
            return false;
        } else {
            $("#selected_service").css("color","black");
        }
        
       
        <?php if(empty($this->session->userdata('status'))){   ?>
                var grand_total = Number($("#grand_total").val());
                if(grand_total < 2){
                    alert("<?php echo $this->session->userdata("message");?>");
                    document.getElementById('error_not_visible').innerHTML = "<?php echo $this->session->userdata("message");?>"; 
                    return false;
                }
               
       <?php } ?>
        
        $("#booking_appliance").val($.trim($("#service_name option:selected").text()));
        $("#service_id").val(appliance);
        $("#pincode").val(pincode);
        $("#pincode").keyup();
        $('#city option[value="'+city+'"]').prop("selected",true);
        $('#select2-city-container').text(city);
        $("#address").val(booking_address);
        $("#myModal").modal("show");
        EnableDisableFields('booking_form',true);
        
        return true;
    }
    
    function EnableDisableFields(id,status)
    {
        var form = document.getElementById(id);
        var elements = form.elements;
        for (var i = 0, len = elements.length; i < len; ++i) {
            if (elements[i].id !== 'close_modal') {
                elements[i].readonly  = status;
            }
        }
    }
    
    function check_address_validation() {
        var booking_address = $('#address').val();
        var city = $('#city').val();
        var pincode = $('#pincode').val();
        var appliance = $("#booking_appliance").val();
        
        if((appliance === null) || ($.trim(appliance) === '')){
            display_message("booking_appliance","error_booking_appliance","red","Please Enter Appliance");
             return false;
        } else {
            display_message("booking_appliance","error_booking_appliance","green","");
        }
        if($.trim(pincode) === ""){
              display_message("pincode","error_pincode1","red","Please Enter Pincode");
             return false;
        } else {
           display_message("pincode","error_pincode1","green","");
            
        }
        if((city === null) || ($.trim(city) === '')){
            
             display_message("city","error_city1","red","Please Select City");
             return false;
        } else {
             display_message("city","error_city1","green","");
            
        }
        if(booking_address.trim().length < 1){
             display_message("address","error_address1","red","Please Enter Booking Address");
             return false;
        } else {
            display_message("address","error_address1","green","");
        }
        
        $('#btn_submit').val("Please wait.....");
        document.getElementById('btn_submit').disabled=true;
        document.getElementById('booking_form').submit();
        
        return true;
    }
    
    function display_message(input_id, error_id, color,message){
    
            document.getElementById(input_id).style.borderColor = color;
            document.getElementById(error_id).innerHTML = message;
    }
    
    var service_name = document.getElementById("service_name").value;
    if(service_name === 'Select Appliance'){
        document.getElementById("booking_pincode").disabled=true;
    }
    function enablePincode(){
        document.getElementById("booking_pincode").disabled=false;
    }
    $("#booking_city").select2({
         tags: true
    });
    $("#city").select2({
         tags: true,
         width:"404px"
    });
    $("#booking_request_symptom").select2();
    $("#model_number_1").select2();
    $("#price_tag").select2();
    $("#service_name").select2();
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
                minDate: '<?php echo date('H') >= 12 ? date("d-m-Y", strtotime("+1 day")):date("d-m-Y", strtotime("+0 day")); ?>',
                maxDate: '<?php echo date("d-m-Y", strtotime("+15 day")); ?>',
                locale:{
                    format: 'DD-MM-YYYY'
                }
            });
            
    $('#booking_date').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD-MM-YYYY'));
    });
    
    $('#booking_date').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
    
    $('#purchase_date').daterangepicker({
                autoUpdateInput: false,
                singleDatePicker: true,
                showDropdowns: true,
                minDate:"01-01-1998",
                maxDate:'<?php echo date("d-m-Y"); ?>',
                locale:{
                    format: 'DD-MM-YYYY'
                }
            });
            
    $('#purchase_date').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD-MM-YYYY'));
        check_booking_request();
    });
    
    $('#purchase_date').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
    
    $("#pincode").keyup(function(event) {
        enablePincode();
        get_city($("#pincode").val(),$("#service_id").val());
    });
    
    $('.close,#btn_cancel').click(function() {
        EnableDisableFields('booking_form',false);
    });
    get_city();
    get_brands();
    
    
    //This funciton is used to get Distinct Brands for selected service for Logged Partner
    function get_brands(){
        service_id =  $("#service_name").val();
        partner_type = $("#partner_type").val();
        
        if(service_id){
         $.ajax({
            type: 'POST',
            beforeSend: function(){
                $('#brand_loading').css("display", "inherit");
            },
            url: '<?php echo base_url(); ?>employee/partner/get_brands_from_service',
            data: {service_id: service_id,partner_id:<?php echo $this->session->userdata('partner_id')?>, 
                    brand:'<?php echo set_value('appliance_brand');?>', 
                    partner_type:partner_type},
            success: function (data) {
                               
                    //First Resetting Options values present if any
                    $('#appliance_brand_1').find('option').remove();
                    $('#appliance_brand_1').append(data).change();
            },
            complete: function(){
                $('#brand_loading').css("display", "none");
            }
        });
        }
    }
    
    //This function is used to get Category for partner id , service , brands specified
    
    function get_category(){ 
        service_id =  $("#service_name").val();
        brand =  $("#appliance_brand_1").val();
        
        partner_type = $("#partner_type").val();
        if(service_id && brand){ 
            $.ajax({
                type: 'POST',
                beforeSend: function(){
                    $('#category_loading').css("display", "inherit");
                },
                url: '<?php echo base_url(); ?>employee/partner/get_category_from_service',
                data: {service_id: service_id,partner_id:<?php echo $this->session->userdata('partner_id')?>, 
                        brand: brand,
                        partner_type:partner_type},
                success: function (data) {

                    //First Resetting Options values present if any
                    $("#appliance_category_1 option[value !='option1']").remove();
                    $('#appliance_category_1').append(data).change();
                    //get_capacity();
                },
                complete: function(){
                    $('#category_loading').css("display", "none");
                }            
            });
        }
        
    }
    
    //This function is used to get Capacity and Model
    function get_capacity(){ 
        service_id =  $("#service_name").val();
        brand = $("#appliance_brand_1").find(':selected').val();
        category = $("#appliance_category_1").find(':selected').val();
        
        partner_type = $("#partner_type").val();
        
        if(service_id && brand && category){ 
            $.ajax({
                type: 'POST',
                beforeSend: function(){
                    $('#capacity_loading').css("display", "inherit");
                },
                url: '<?php echo base_url(); ?>employee/partner/get_capacity_for_partner',
                data: {service_id: service_id,partner_id:<?php echo $this->session->userdata('partner_id')?>, 
                        brand: brand,category:category, 
                        partner_type:partner_type},

                success: function (data) {
                        //First Resetting Options values present if any
        //                    $("#appliance_capacity_1 option[value !='option1']").remove();
        //                    $('#appliance_capacity_1').append(data).change();

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
        
    }
    
    //This function is used to get Model for corresponding previous data's
    function get_models(){
        service_id =  $("#service_name").val();
        brand = $("#appliance_brand_1").find(':selected').val();
        category = $("#appliance_category_1").find(':selected').val();
        capacity = $("#appliance_capacity_1").val();
        
        partner_type = $("#partner_type").val();
        if(capacity === null && capacity === ""){
            capacity = '';
            $("#appliance_capacity_1").removeAttr("required");
        }
        if(service_id && brand && category){
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/partner/get_model_for_partner',
            data: {service_id: service_id,partner_id:<?php echo $this->session->userdata('partner_id')?>, 
                    brand: brand,category:category,capacity:capacity,
                    partner_type:partner_type},           
            success: function (data) {
                if($.trim(data) === "Data Not Found"){  
                    var input = '<input type="text" name="model_number" id="model_number_1" class="form-control" placeholder="Please Enter Model" onkeypress="return checkQuote(event);" oninput="return checkInputQuote(this);">';
                    $("#model_number_2").html(input).change();
                    $('.select-model').next(".select2-container").hide();
                } else {
                    //First Resetting Options values present if any
                    var input_text = '<span id="model_number_2"><select class="form-control select-model"  name="model_number" id="model_number_1" ><option selected disabled>Select Model</option></select></span>';
                    $("#model_number_2").html(input_text).change();
                    $("#model_number_1").append(data).change();
                    $("#model_number_1").select2();
                    $('.select-model').next(".select2-container").show();
                    
                }
            }
        });
        }
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
        postData['service_category'] = "";
        postData['booking_id'] = "";
        postData['pincode'] = $("#booking_pincode").val();
        postData['city'] = $("#booking_city").val();
        
        postData['partner_type'] = $('#partner_type').val();
        postData['assigned_vendor_id'] = "";
        postData['add_booking'] = "add_booking";
        
        if(postData['brand'] !== null 
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
                        
                         // alert("Outstation Bookings Are Not Allowed, Please Contact backoffice Team.");
    
                     } else { 
                          var data1 = jQuery.parseJSON(data);
                         
                          $("#priceList").html(data1.table);
                          $("#upcountry_data").val(data1.upcountry_data);
                          $('#submitform').attr('disabled',false);
                          
                        if(blDisableAcCategoryOptions == '1') {
                            $('.price_checkbox[data-price_tag="Gas Recharge (R410) - In Warranty"]').prop('disabled', true);
                            $('.price_checkbox[data-price_tag="Gas Recharge (R410) - Out of warranty"]').prop('disabled', true);
                            $('.price_checkbox[data-price_tag="Gas Recharge - In Warranty"]').prop('disabled', true);
                            $('.price_checkbox[data-price_tag="Gas Recharge - Out of Warranty"]').prop('disabled', true);
                            $('.price_checkbox[data-price_tag="Small Stand"]').css('pointer-events', 'none');
                            $('.price_checkbox[data-price_tag="Drain Pipe Per Meter"]').css('pointer-events', 'none');
                            $('.price_checkbox[data-price_tag="22 Gauge Refrigerant Pipe, Insulation, Wire Set / ft"]').css('pointer-events', 'none');
                            $('.price_checkbox[data-price_tag="Gas Recharge with Dryer (In Warranty)"]').prop('disabled', true);
                            $('.price_checkbox[data-price_tag="Gas Recharge with Dryer (Out Warranty)"]').prop('disabled', true);
                        }
                     }
                }
            });
        } else {
          //console.log("error");
        }
        
        $(this).dequeue();
    });
    
    
    }
    
    function getVendorData() {
    
        var postData = {};
        appliance_name = $("#service_name").find(':selected').attr('data-id');
        $("#appliance_name").val(appliance_name);
        
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
        postData['service_category'] = "";
        postData['booking_id'] = "";
        postData['pincode'] = $("#pincode").val();
        postData['city'] = $("#city").val();
        
        postData['partner_type'] = $('#partner_type').val();
        postData['assigned_vendor_id'] = "";
        postData['add_booking'] = "add_booking";
        
        if(postData['brand'] !== null 
                && postData['category'] !== null && postData['pincode'].length === 6 && postData['city'] !== null){
          
            $.ajax({
                type: 'POST',
                beforeSend: function(){
                  
                  $('#btn_submit').attr('disabled',true);
                  
                },
                url: '<?php echo base_url(); ?>employee/partner/get_price_for_partner',
                data: postData,
                success: function (data) {
                    //console.log(data);
                     if(data === "ERROR"){
                        
                         // alert("Outstation Bookings Are Not Allowed, Please Contact backoffice Team.");
    
                     } else { 
                          var data1 = jQuery.parseJSON(data);
                         
                          $("#upcountry_data").val(data1.upcountry_data);
                          $('#btn_submit').attr('disabled',false);
                     }
                }
            });
        } else {
          //console.log("error");
        }
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
        
        $('#priceList').children('tbody').children('tr').each(function(index) {
            if($('#checkbox_'+index).prop("checked") == true && $('#checkbox_'+index).attr('data-price_tag').indexOf('Installation') != -1 && $("#service_name").val() == '50') {
                $('.price_checkbox[data-price_tag="Small Stand"]').prop('checked', true).css('pointer-events', 'none');
                $('.price_checkbox[data-price_tag="Drain Pipe Per Meter"]').prop('checked', true).css('pointer-events', 'none');
                $('.price_checkbox[data-price_tag="22 Gauge Refrigerant Pipe, Insulation, Wire Set / ft"]').prop('checked', true).css('pointer-events', 'none');
            }
        });
    }
    
    
    
    
    
    $("#booking_pincode").keyup(function(event) {
        get_city();
        
    });
    
    function get_city(pincode1 = '', service_id1 = ''){
        var pincode = pincode1;
        var service_id = service_id1;
        var btn_submit = 'btn_submit';
        if(pincode1 == '') {
            pincode = $("#booking_pincode").val();
            btn_submit = 'submitform';
        }
        if(service_id1 == '') {
            service_id =  $("#service_name").val();
            btn_submit = 'submitform';
        }
        if(pincode.length === 6 && service_id != null){
            $.ajax({
                type: 'POST',
                beforeSend: function(){
                  
                    $('#city_loading').css("display", "-webkit-inline-box");
                    $('#'+btn_submit).prop('disabled', true);
                },
                url: '<?php echo base_url(); ?>employee/partner/get_district_by_pincode/'+ pincode+"/"+service_id,
                async: false,
                success: function (data) {
                    if(data.includes("ERROR")){
                        alert("Service Temporarily Un-available In This Pincode, Please Contact backoffice Team.");
                        $('#'+btn_submit).prop('disabled', true);
                        $("#not_visible").val('0');
                    }
                    else if(data.includes("Not_Serve")){
                        alert("This PINCODE is not in your Serviceable Area associated with us!");
                         $('#'+btn_submit).prop('disabled', true);
                         $("#not_visible").val('0');
                    }
                    else {
                        //                        $("#booking_city").select2({
                        //                           tags: true
                        //                        });
                         if(pincode1 == '') {
                            $('#booking_city').select2().html(data).change();
                            getPrice();
                         }
                         else {
                             $('#city').select2().html(data).change();
                             getVendorData();
                         }
                         $('#'+btn_submit).prop('disabled', false);
                         $("#not_visible").val('1');
                    }
                },
                complete: function(){
                    $('#city_loading').css("display", "none");
                }  
            }); 
        }
    }
    
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
                        
                } else if(Number(is_upcountry) == 1 && Number(data1.partner_provide_upcountry) == 1 ){
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
                            var upcountry_charges = customer_price;
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
                            var upcountry_charges = customer_price;
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
    
    var not_visible = $("#not_visible").val();
    
    if(Number(not_visible) === 0){
      
     alert('Service Temporarily Un-available In This Pincode, Please Contact backoffice Team');
     display_message("not_visible","error_not_visible","red","Service Temporarily Un-available In This Pincode, Please Contact backoffice Team.");
      $('#submitform').attr('disabled', true);
         return false;
    } else {
      display_message("not_visible","error_not_visible","","");
      $('#submitform').attr('disabled', false);
         
    }        
    
    
    
    }        
    function final_price(){
        var price = 0;
        var price_array ;
        ch =0;
        var delivered_price_tags = [];
        var appliance_unit =$("#appliance_unit").val();
    
         $("input[type=checkbox]:checked").each(function(i) {
            price_array = $(this).val().split('_');
            //console.log(price_array);
            price += (Number(price_array[1]) -Number(price_array[2]) );
            if(price_array[0] !== "upcountry"){
                ch = 1;
            }
            
            var price_tags = $("#"+ $(this).attr('id')).attr('data-price_tag');
            delivered_price_tags.push(price_tags);
           
        });

        if(ch === 0){
            document.getElementById("checkbox_upcountry").checked = false;
            $("#grand_total").val("0.00");
            
        } else {
            var final_price = Number(price) * Number(appliance_unit);;
            $("#grand_total").val(final_price.toFixed(2));
        }
        
        
        var pr = checkPriceTagValidation(delivered_price_tags);
        if(pr === false){
             alert('Not Allow to select multiple different type of service category');
             $("#selected_service").css("color","red");
             $(".price_checkbox:checked").prop("checked", false);
             document.getElementById("checkbox_upcountry").checked = false;
             $("#grand_total").val("0.00");
             final_price();

        } else {
            $("#selected_service").css("color","black");
        }
        
    }
    
    function check_active_paid(no){
       var is_active = Number($("#is_active").val());
       if(is_active === 0){
           if ($("#checkbox_" + no).is(':checked')) {
                var price_array = $("#checkbox_"+no).val();
                var price_array1 = price_array.split('_');
                var customer_total = Number(price_array1[1]);
    
                if(customer_total > 0){
                    var partner_net_payable = Number(price_array1[2]);
                    var customer_net_payable = customer_total - partner_net_payable;
                    if(customer_net_payable < 1){
    
                        alert('<?php echo $this->session->userdata("message");?>');
    
                        document.getElementById("checkbox_" + no).checked = false;
                         
                    }
                }
           }
       }
       
    }
    
    
    $(document).ready(function(){
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
    getPartnerChannel();
    function getPartnerChannel(){
        var partnerChannelServiceUrl = '<?php echo base_url(); ?>employee/partner/get_partner_channel/';
        var postData = {};
        postData['partner_id'] = '<?php echo $this->session->userdata('partner_id')?>';
        postData['channel'] = $("#partner_channel").val();
        if( postData['partner_id'] !== null){
            sendAjaxRequest(postData, partnerChannelServiceUrl).done(function (data) {
               $("#partner_source").html("");
               $("#partner_source").html(data).change();
            });
        }
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
    
 
 <?php  
 if($this->session->userdata('partner_id')==VIDEOCON_ID){ ?>
    $("#booking_city, #city").change(function(){
      var cities = ["Mumbai","Thane"];
      var city = $(this).val();
      if(jQuery.inArray(city, cities)!='-1'){
          alert("This PINCODE is not in your Serviceable Area associated with us!");
          $('#submitform').prop("disabled", true);
          $('#submitform').attr("type", "button");
          $('#submitform').removeClass("btn-primary");
          $("#submitform").removeAttr("onclick");
     }else{
          $('#submitform').attr("type", "button");  
          $('#submitform').addClass("btn-primary");
          $('#submitform').attr('onclick',"return check_validation()");
     }
   }); 
     
 <?php }
 ?>   
 
 function checkPriceTagValidation(delivered_price_tags){
        var partner_id = '<?php echo $this->session->userdata('partner_id')?>';
        var repair_flag = false;
        var repair_out_flag = false;
        var installation_flag = false;
        var amc_flag = false;
        var pdi = false;
        var extended_warranty = false;
        var pre_sales = false;
        var others_flag = false;
        var array =[];
        var videocon_id = "247130";

        if((findInArray(delivered_price_tags, 'Repair - In Warranty (Home Visit)') > -1 
                || findInArray(delivered_price_tags, 'Repair - In Warranty (Service Center Visit)') > -1 
                || findInArray(delivered_price_tags, 'Repair - In Warranty (Customer Location)') > -1 
                )){
            
            repair_flag = true;
            array.push(repair_flag);
         } 
         
         if((findInArray(delivered_price_tags, 'Repair - Out Of Warranty (Home Visit)') > -1 
                || findInArray(delivered_price_tags, 'Repair - Out Of Warranty (Home Visit)') > -1
                || findInArray(delivered_price_tags, 'Repair - Out Of Warranty (Customer Location)') > -1
                || findInArray(delivered_price_tags, 'Repair - Out Of Warranty (Service Center Visit)') > -1)){
            
            repair_out_flag = true;
            array.push(repair_out_flag);
         }
         
         if(findInArray(delivered_price_tags, 'Extended Warranty') > -1 ){
             extended_warranty = true;
             array.push(extended_warranty);
         }
         
         if(findInArray(delivered_price_tags, 'Presale Repair') > -1 ){
             pre_sales = true;
             array.push(pre_sales);
         }
         
         if(findInArray(delivered_price_tags, 'Installation & Demo (Free)') > -1 
                || findInArray(delivered_price_tags, 'Installation & Demo (Paid)') > -1){
                   installation_flag = true;
                   array.push(installation_flag);
         }
         
         if(findInArray(delivered_price_tags, 'Pre-Dispatch Inspection PDI - With Packing') > -1
                || findInArray(delivered_price_tags, 'Pre-Dispatch Inspection PDI - With Packing') > -1
                || findInArray(delivered_price_tags, 'Pre-Dispatch Inspection PDI - Without Packing') > -1
                || findInArray(delivered_price_tags, 'Pre-Dispatch Inspection PDI - Without Packing') > -1){
                    pdi = true;
                    array.push(pdi);
                }
                
         if(findInArray(delivered_price_tags, 'AMC (Annual Maintenance Contract)') > -1 ){
             amc_flag = true;
             array.push(amc_flag);
         }
         
         // ----------------------------------------------------------------------------------------
         // DO NOT ALLOW SAME REQUEST TYPE COMBINATIONS IN BOOKING         
         if((findInArray(delivered_price_tags, 'Repair - In Warranty (Home Visit)') > -1 && findInArray(delivered_price_tags, 'Repair - In Warranty (Service Center Visit)') > -1) ||
            (findInArray(delivered_price_tags, 'Repair - In Warranty (Home Visit)') > -1 && findInArray(delivered_price_tags, 'Repair - In Warranty (Customer Location)') > -1)|| 
            (findInArray(delivered_price_tags, 'Repair - In Warranty (Service Center Visit)') > -1 && findInArray(delivered_price_tags, 'Repair - In Warranty (Customer Location)') > -1)
            ){            
            repair_flag = true;
            array.push(repair_flag);
         } 
         
         if((findInArray(delivered_price_tags, 'Repair - Out Of Warranty (Home Visit)') > -1 && findInArray(delivered_price_tags, 'Repair - Out Of Warranty (Customer Location)') > -1) ||
            (findInArray(delivered_price_tags, 'Repair - Out Of Warranty (Home Visit)') > -1 && findInArray(delivered_price_tags, 'Repair - Out Of Warranty (Service Center Visit)') > -1) ||
            (findInArray(delivered_price_tags, 'Repair - Out Of Warranty (Service Center Visit)') > -1 && findInArray(delivered_price_tags, 'Repair - Out Of Warranty (Customer Location)') > -1)            
           ){            
            repair_out_flag = true;
            array.push(repair_out_flag);
         }
         
         if(findInArray(delivered_price_tags, 'Installation & Demo (Free)') > -1 && findInArray(delivered_price_tags, 'Installation & Demo (Paid)') > -1){
                   installation_flag = true;
                   array.push(installation_flag);
         }
         
         if(findInArray(delivered_price_tags, 'Pre-Dispatch Inspection PDI - With Packing') > -1 && findInArray(delivered_price_tags, 'Pre-Dispatch Inspection PDI - Without Packing') > -1){
                    pdi = true;
                    array.push(pdi);
         }
         
         if(findInArray(delivered_price_tags, 'Gas Recharge - In Warranty') > -1 && findInArray(delivered_price_tags, 'Gas Recharge - Out of Warranty') > -1){
                    others_flag = true;
                    array.push(others_flag);
         }
         
         if(findInArray(delivered_price_tags, 'Gas Recharge (R410) - In Warranty') > -1 && findInArray(delivered_price_tags, 'Gas Recharge (R410) - Out of warranty') > -1){
                    others_flag = true;
                    array.push(others_flag);
         }
         
         if(findInArray(delivered_price_tags, 'Wet Service - In Warranty') > -1 && findInArray(delivered_price_tags, 'Wet Service - Out of Warranty') > -1){
                    others_flag = true;
                    array.push(others_flag);
         }
         
         if(partner_id === videocon_id){
              if((findInArray(delivered_price_tags, 'Repair - In Warranty (Home Visit)') > -1 
                || findInArray(delivered_price_tags, 'Repair - In Warranty (Service Center Visit)') > -1 
                || findInArray(delivered_price_tags, 'Repair - In Warranty (Customer Location)') > -1
                || findInArray(delivered_price_tags, 'Repair - Out Of Warranty (Home Visit)') > -1
                || findInArray(delivered_price_tags, 'Repair - Out Of Warranty (Customer Location)') > -1
                || findInArray(delivered_price_tags, 'Repair - Out Of Warranty (Service Center Visit)') > -1
                || findInArray(delivered_price_tags, 'Repair - Out Of Warranty (Home Visit)') > -1
                || findInArray(delivered_price_tags, 'Extended Warranty') > -1
                || findInArray(delivered_price_tags, 'Presale Repair') > -1 
                || findInArray(delivered_price_tags, 'AMC (Annual Maintenance Contract)') > -1
                )
                &&(
                  findInArray(delivered_price_tags, 'Gas Recharge - In Warranty') > -1
                ||findInArray(delivered_price_tags, 'Gas Recharge - Out of Warranty') > -1
                ||findInArray(delivered_price_tags, 'Gas Recharge (R410) - Out of warranty') > -1
                )){
                    others_flag = true;
                    array.push(others_flag);
                }
         }
         
         // ---------------------------------------------------------------------------------------------------------
                
         if(array.length > 1){
             return false;
         } else {
             return true;
         }
    }
    function findInArray(ar, val) {
        for (var i = 0,len = ar.length; i < len; i++) {
            if ( ar[i] === val ) { // strict equality test
                return i;
            }
        }
        return -1;
    }
    
    // function to cross check request type of booking with warranty status of booking 
    function check_booking_request()
    {
        $(".price_checkbox").attr("disabled", false);
        var model_number = $(".select-model").val();
        var dop = $("#purchase_date").val();
        var partner_id = '<?php echo $this->session->userdata('partner_id')?>';
        var service_id = $("#service_name").val();
        var booking_id = 1;
        var booking_create_date = "<?= date('Y-m-d')?>";
        var booking_request_types = []; 
        $(".price_checkbox:checked").each(function(){
            var price_tag = $(this).attr('data-price_tag');
            booking_request_types.push(price_tag);
        });
        $("#submitform").attr("disabled", false);
        $('.errorMsg').html("");
        if(dop !== "" && booking_request_types.length > 0){                               
            $.ajax({
                method:'POST',
                url:"<?php echo base_url(); ?>employee/service_centers/get_warranty_data/2",
                data:{
                    'bookings_data[0]' : {
                        'partner_id' : partner_id,
                        'booking_id' : booking_id,
                        'booking_create_date' : booking_create_date,
                        'service_id' : service_id,
                        'model_number' : model_number,
                        'purchase_date' : dop, 
                        'booking_request_types' : booking_request_types
                    }
                },
                success:function(response){
                    var returnData = JSON.parse(response);
                    $('.errorMsg').html(returnData['message']);
                    if(returnData['status'] == 1)
                    {
                        $("#submitform").attr("disabled", true);                        
                    }
                }                           
            });
        }
    }
// function ends here ---------------------------------------------------------------- 
    
</script>
 
