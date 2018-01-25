<style type="text/css">
    /* example styles for validation form demo */
    .err {
    color: red;
    }
    #booking_form .form-group label.error {
    color: #FB3A3A;
    display: inline-block;
    margin: 4px 0 5px 125px;
    padding: 0;
    text-align: left;
    width: 220px;
    }
    .col-md-3{
        width: 25%;
    }
    
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
    
    
</style>
<div id="page-wrapper" >
    <div class="container-fluid" >
        <form name="myForm" class="form-horizontal" id ="booking_form" action="<?php echo base_url()?>partner/process_update_booking/<?php echo $booking_history[0]['booking_id']; ?>"  method="POST" enctype="multipart/form-data">
            <div class="panel panel-info" style="margin-top:20px;">
                <div class="panel-heading">Step 1</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <?php if($this->session->userdata('success')) {
                                echo '<div class="alert alert-danger alert-dismissible" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <strong>' . $this->session->userdata('success') . '</strong>
                                </div>';
                                }
                                ?>
                           
                            <div class="col-md-4 ">
                                <div class="form-group col-md-12   <?php if( form_error('booking_primary_contact_no') ) { echo 'has-error';} ?>">
                                    <label for="booking_primary_contact_no">Mobile *</label>
                                    <input type="text" class="form-control"  id="booking_primary_contact_no" name="booking_primary_contact_no" value = "<?php echo $booking_history[0]['booking_primary_contact_no']; ?>" required readonly>
                                    <span id="error_mobile_number" style="color:red"></span>
                                </div>
                            </div>
                            
                            <div class="col-md-4" >
                                <div class="form-group col-md-12 <?php if( form_error('user_name') ) { echo 'has-error';} ?>">
                                    <label for="booking_primary_contact_no">Name *</label>
                                    <input type="hidden" name="user_id" id="user_id" value="<?php if(isset($booking_history[0]['user_id'])){ echo $booking_history[0]['user_id']; } ?>" />
                                    <input type="hidden" name="assigned_vendor_id" id="assigned_vendor_id" value="<?php if(isset($booking_history[0]['assigned_vendor_id'])){ echo $booking_history[0]['assigned_vendor_id']; }  ?>" />
                                    <input type="hidden" name="upcountry_data" id="upcountry_data" value="" />
                                    <input type="hidden" name="partner_code" id="partner_code" value="<?php echo $partner_code;?>" />
                                    <input type="hidden" name="partner_type" id="partner_type" value="<?php echo $partner_type;?>" />
                                    <input type="hidden" name="appliance_id" id='appliance_id' value="<?php echo $unit_details[0]['appliance_id']; ?>" />
                                    
                                    <input type="text" class="form-control" id="name" name="user_name" value = "<?php if(isset($booking_history[0]['name'])){ echo $booking_history[0]['name']; } else { echo set_value('user_name'); }  ?>" <?php //if(isset($booking_history[0]['name'])){ echo "readonly"; }  ?> placeholder="Please Enter User Name">
                                    <?php echo form_error('user_name'); ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group col-md-12 <?php if( form_error('service_id') ) { echo 'has-error';} ?>">
                                    <label for="Appliance">Appliance * <span id="error_appliance" style="color: red;"></span></label>
                                    <select type="text" class="form-control"  id="service_name" name="service_id"   required onchange="return get_brands(), get_category(), get_capacity()" readonly >
                                        <option selected disabled>Select Appliance</option>
                                        <?php foreach ($appliances as $values) { ?>
                                        <option <?php if(count($appliances) ==1){echo "selected";} ?>  data-id="<?php echo $values->services;?>" value=<?= $values->id; ?> <?php if($booking_history[0]['service_id'] == $values->id){ echo "selected";} ?>>
                                            <?php echo $values->services; }    ?>
                                        </option>
                                    </select>
                                    <?php echo form_error('service_id'); ?>
                                    <span id="error_pincode" style="color: red;"></span>
                                </div>
                            </div>
                        </div>
                         <div class="col-md-12">
                            <div class="col-md-4">
                                <div class="form-group col-md-12  <?php if( form_error('booking_pincode') ) { echo 'has-error';} ?> ">
                                    <label for="booking_pincode">Pincode *   <span id="error_pincode" style="color: red;"></span></label>
                                    <input type="text" class="form-control" id="booking_pincode" name="booking_pincode" value = "<?php if(isset($booking_history[0]['booking_pincode'])){ echo $booking_history[0]['booking_pincode']; } else { echo set_value('booking_pincode'); }  ?>" placeholder="Enter Area Pin" required readonly>
                                    <?php echo form_error('booking_pincode'); ?>
                                </div>
                            </div>
                            <div class="col-md-4 ">
                                <div class="form-group col-md-12  <?php if( form_error('city') ) { echo 'has-error';} ?>">
                                    <label for="city ">City * <span id="error_city" style="color: red;"></span><span style="color:grey;display:none" id="city_loading">Loading ...</span></label>
                                    
                                    <select type="text" class="form-control"  id="booking_city" name="city" required >

                                        <option value="<?php echo $booking_history[0]['city']; ?>" selected><?php echo $booking_history[0]['city']; ?></option>
                                            
                                    </select>
                                    <?php echo form_error('city'); ?>
                                </div>
                            </div>
                            
                            <input type="hidden" name="appliance_name" id="appliance_name" value=""/>
                            <div class="col-md-4">
                                <div class="form-group col-md-12 <?php if( form_error('appliance_brand') ) { echo 'has-error';} ?>">
                                    <label for="appliance_brand">Brand *  <span id="error_brand" style="color: red;"><span style="color:grey;display:none" id="brand_loading">Loading ...</span></label>
                                    
                                    <select type="text" class="form-control appliance_brand"    name="appliance_brand" id="appliance_brand_1" required onchange="return get_category()">
                                        <option selected disabled value="option1">Select Brand</option>
                                    </select>
                                    <?php echo form_error('appliance_brand'); ?>
                                </div>
                            </div>
                         </div>
                         <div class="col-md-12">
                            <div class="col-md-4 col-md-12">
                                <div class="form-group col-md-12 <?php if( form_error('appliance_category') ) { echo 'has-error';} ?>">
                                    <label for="appliance_category">Category * <span id="error_category" style="color: red;"> <span style="color:grey;display:none" id="category_loading">Loading ...</span></label>
                                   
                                    <select type="text" class="form-control appliance_category"   id="appliance_category_1" name="appliance_category"   required onchange="return get_capacity()">
                                        <option selected disabled value="option1">Select Appliance Category</option>
                                    </select>
                                    <?php echo form_error('appliance_category'); ?>
                                </div>
                            </div>
                            <div class="col-md-4 col-md-12">
                                <div class="form-group col-md-12 <?php if( form_error('appliance_capacity') ) { echo 'has-error';} ?>">
                                    <label for="appliance_capacity">Capacity  <span id="error_capacity" style="color: red;"> <span style="color:grey;display:none" id="capacity_loading">Loading ...</span></label>
                                   
                                    <select type="text" class="form-control appliance_capacity"   id="appliance_capacity_1" name="appliance_capacity" onchange="return get_models(), getPrice()">
                                        <option selected disabled value="option1">Select Appliance Capacity</option>
                                    </select>
                                    <?php echo form_error('appliance_capacity'); ?>
                                </div>
                            </div>
                            <div class="col-md-4 col-md-12">
                                <div class="form-group col-md-12 <?php if( form_error('model_number') ) { echo 'has-error';} ?>">
                                    <label for="Model Number">Model Number  <span id="error_model" style="color: red;"></label>
                                    <span id="model_number_2">
                                    <select class="form-control"  name="model_number" id="model_number_1" >
                                        <option selected disabled>Select Model</option>
                                    </select>
                                    </span>
                                    <?php echo form_error('model_number'); ?>
                                </div>
                            </div>
                            <div class="col-md-4 col-md-12">
                               
                                <div class="form-group col-md-12  <?php if( form_error('booking_date') ) { echo 'has-error';} ?>">
                                    <label for="Booking Date ">Booking Date *</label>
                                    <input type="date" class="form-control"  id="booking_date" name="booking_date"  value = "<?php echo date('Y-m-d', strtotime($booking_history[0]['booking_date'])); ?>"  >
                                    <!--   -->
                                    <?php echo form_error('booking_date'); ?>
                                </div>
                            
                            </div>
                             
                            <div class="col-md-4 ">
                                <div class="form-group col-md-12  <?php if( form_error('partner_source') ) { echo 'has-error';} ?>">
                                    <label for="Partner source ">Seller Channel*  <span id="error_seller" style="color: red;"></label>
                                     
                                    <select type="text" class="form-control"  id="partner_source" name="partner_source" >
                                        <option value="">Please select seller channel</option>
                                        <option <?php if(set_value('partner_source') == "Amazon"){ echo "selected";} else if($booking_history[0]['partner_source'] == 'Amazon') { echo "selected";} ?>>Amazon</option>
<!--                                        <option <?php //if(set_value('partner_source') == "CallCenter"){ echo "selected";} else if($booking_history[0]['partner_source'] == 'CallCenter') { echo "selected";} ?>>CallCenter</option>-->
                                        <option <?php if(set_value('partner_source') == "Ebay"){ echo "selected";} else if($booking_history[0]['partner_source'] == 'Ebay') { echo "selected";} ?>>Ebay</option>
                                        <option <?php if(set_value('partner_source') == "Flipkart"){ echo "selected";} else if($booking_history[0]['partner_source'] == 'Flipkart') { echo "selected";} ?>>Flipkart</option>
                                        <option <?php if(set_value('partner_source') == "Offline"){ echo "selected";} else if($booking_history[0]['partner_source'] == 'Offline') { echo "selected";} ?>>Offline</option>
                                        <option <?php if(set_value('partner_source') == "Paytm"){ echo "selected";} else if($booking_history[0]['partner_source'] == 'Paytm') { echo "selected";} ?>>Paytm</option>
                                        <option <?php if(set_value('partner_source') == "Pepperfry"){ echo "selected";} else if($booking_history[0]['partner_source'] == 'Pepperfry') { echo "selected";} ?>>Pepperfry</option>
                                        <option <?php if(set_value('partner_source') == "Shopclues"){ echo "selected";} else if($booking_history[0]['partner_source'] == 'Shopclues') { echo "selected";} ?>>Shopclues</option>
                                        <option <?php if(set_value('partner_source') == "TataCliq"){ echo "selected";} else if($booking_history[0]['partner_source'] == 'TataCliq') { echo "selected";} ?>>TataCliq</option>
                                        <option <?php if(set_value('partner_source') == "Snapdeal"){ echo "selected";} else if($booking_history[0]['partner_source'] == 'Snapdeal') { echo "selected";} ?>>Snapdeal</option>
                                        
                                    </select>
                                    <!--   -->
                                    <?php echo form_error('booking_date'); ?>
                                </div>
                            </div>
                            <?php  $unique_appliance = array_unique(array_map(function ($k) {
                                        return $k['appliance_id'];
                                    }, $unit_details));
                                    
                                    ?>
                            <div class="col-md-4 col-md-12">
                                <div class="form-group col-md-5 ">
                                    <label for="Appliance unit ">Unit* <span id="error_seller" style="color: red;"></label>
                                     
                                    <select type="text" style="width:55%" class="form-control" onchange="final_price()"  id="appliance_unit" name="appliance_unit" >
                                      
                                        <?php for($i =1; $i <26; $i++) { ?>
                                        <option value="<?php echo $i;?>" <?php if(count($unique_appliance) == $i){ echo "selected";} ?>><?php echo $i; ?></option>
                                        <?php }?>
                                       
                                       
                                        
                                    </select>
                                    <!--   -->
                                    
                                </div>

<!--                                <div class="form-group col-md-7  <?php //if( form_error('product_type') ) { echo 'has-error';} ?>">
                                     <label for="Product Type">Product Type *</label>
                                   <?php //if(empty($booking_history[0]['assigned_vendor_id'])){ ?>
                                     <label class="radio-inline">
                                        <input type="radio" name="product_type" value="Delivered" checked>Delivered
                                      </label>
                                      <label class="radio-inline">
                                          <input type="radio" name="product_type" value="Shipped">Shipped
                                      </label>
                                   <?php //} else  {?>
                                    <label class="radio-inline">
                                        <input type="radio" name="product_type" value="Delivered" checked>Delivered
                                      </label>
                                   <?php //} ?>
                                    <?php //echo form_error('product_type'); ?>
                                </div>-->
                            </div>
                            
                            <!-- end col-md-6 -->
                        </div>
                    </div>
                </div>
            </div>
                        <!-- end -->
            <div class="panel panel-info" style="margin-top:20px;">
                <div class="panel-heading">Step 2</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">

                            <div class="form-group">
                                <div  class="col-md-12">
                                    <table class="table priceList table-striped table-bordered" name="priceList" id="priceList">
                                        <tr class="text-center">
                                            <th class="text-center">Service Category</th>
                                            <th class="text-center">Final Charges</th>
                                            <th class="text-center" id='selected_service'>Selected Services</th>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                           
                                <div class="col-md-4 ">
                                <div class="form-group col-md-12  ">
                                    <label for="Grand total">Grand Total *</label>
                                    <input  type="text" class="form-control"  name="grand_total" id="grand_total" value = "<?php echo set_value('grand_total'); ?>" placeholder="0.00" readonly >
                                    
                                </div>
                            </div> 
                           
                        </div>
                    </div>
                </div>
            </div>
            <!-- row End  -->
            <div class="clonedInput panel panel-info " id="clonedInput1">
                <div class="panel-heading">
                    Step 3
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-3 ">
                                <div class="form-group col-md-12    <?php if( form_error('order_id') ) { echo 'has-error';} ?>">
                                    <label for="order id">Order ID * <span id="error_order_id" style="color:red"></span></label>
                                    <input class="form-control" name= "order_id" value="<?php if(!empty(set_value('order_id'))){ echo set_value('order_id'); } else { echo $booking_history[0]['order_id'];} ?>" placeholder ="Please Enter Order ID" id="order_id"  />
                                    
                                </div>
                            </div>
                            <div class="col-md-3 ">
                                <div class="form-group col-md-12  <?php if( form_error('serial_number') ) { echo 'has-error';} ?>">
                                    <label for="serial NUmber">Serial Number * <span id="error_serial_number" style="color:red"></span></label>
                                    <input  type="text" class="form-control"  name="serial_number" id="serial_number" value = "<?php if(!empty(set_value('serial_number'))){ echo set_value('serial_number'); } else { echo $unit_details[0]['serial_number'];} ?>" placeholder="Enter Serial Number" >
                                    
                                </div>
                            </div> 
                            <div class="col-md-3 ">
                                <div class="form-group col-md-12  <?php if( form_error('dealer_phone_number') ) { echo 'has-error';} ?>">
                                    <label for="dealer_phone_number">Dealer Phone Number  <span id="error_dealer_phone_number" style="color:red"></span></label>
                                    <input  type="text" class="form-control"  name="dealer_phone_number" id="dealer_phone_number" value = "<?php if(isset($dealer_data)){echo $dealer_data['dealer_phone_number_1'] ;}?>" placeholder="Enter Dealer Phone Number" autocomplete="off">
                                    <div id="dealer_phone_suggesstion_box"></div>
                                </div>
                            </div>
                            <div class="col-md-3 ">
                                <div class="form-group col-md-12  <?php if( form_error('dealer_name') ) { echo 'has-error';} ?>">
                                    <label for="dealer_name">Dealer Name *  <span id="error_dealer_name" style="color:red"></span></label>
                                    <input  type="text" class="form-control"  name="dealer_name" id="dealer_name" value = "<?php if(isset($dealer_data)){echo $dealer_data['dealer_name'] ;}?>" placeholder="Enter Dealer Name" autocomplete="off">
                                    <input type="hidden" name="dealer_id" id="dealer_id" value="<?php if(isset($dealer_data)){echo $dealer_data['dealer_id'] ;}?>">
                                     <div id="dealer_name_suggesstion_box"></div>
                                </div>
                            </div> 
                            
                            <div class="col-md-6 ">
                                <div class="form-group col-md-12  <?php if( form_error('purchase_month') ) { echo 'has-error';} ?>">
                                    <label for="Date of Purchase">Date of Purchase</label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <select  type="text" class=" form-control "   name="purchase_month" id="purchase_month_1" >
                                                
                                                <?php //$current_month = date("M"); for($i = 0; $i< 12; $i++){ ?>
<!--                                                <option  <?php //if(set_value('purchase_month') == date('M', strtotime("+ $i month"))){ echo "selected";} 
                                                  //else if(date('M', strtotime("+ $i month")) == $current_month){ echo "selected";}
                                                  //else if(date('M', strtotime("+ $i month")) == $unit_details[0]['purchase_month']){ echo "selected";}?> >
                                                    <?php// echo date('M', strtotime("+ $i month")); ?>
                                                </option>-->
                                                <?php //}  ?>
                                                <option selected="selected" value="" disabled=""> Select Month</option>
                                                <option <?php if(set_value('purchase_month') === "Jan"){ echo "selected";} else if($unit_details[0]['purchase_month'] === "Jan"){ echo "selected";} ?> >Jan</option>
                                                <option <?php if(set_value('purchase_month') === "Feb"){ echo "selected";} else if($unit_details[0]['purchase_month'] === "Feb"){ echo "selected";} ?>>Feb</option>
                                                <option <?php if(set_value('purchase_month') === "Mar"){ echo "selected";} else if($unit_details[0]['purchase_month'] === "Mar"){ echo "selected";} ?>>Mar</option>
                                                <option <?php if(set_value('purchase_month') === "Apr"){ echo "selected";} else if($unit_details[0]['purchase_month'] === "Apr"){ echo "selected";} ?>>Apr</option>
                                                <option <?php if(set_value('purchase_month') === "May"){ echo "selected";} else if($unit_details[0]['purchase_month'] === "May"){ echo "selected";} ?>>May</option>
                                                <option <?php if(set_value('purchase_month') === "Jun"){ echo "selected";} else if($unit_details[0]['purchase_month'] === "Jun"){ echo "selected";} ?>>Jun</option>
                                                <option <?php if(set_value('purchase_month') === "July"){ echo "selected";} else if($unit_details[0]['purchase_month'] === "July"){ echo "selected";} ?> >July</option>
                                                <option <?php if(set_value('purchase_month') === "Aug"){ echo "selected";} else if($unit_details[0]['purchase_month'] === "Aug"){ echo "selected";} ?>>Aug</option>
                                                <option <?php if(set_value('purchase_month') === "Sept"){ echo "selected";} else if($unit_details[0]['purchase_month'] === "Sept"){ echo "selected";} ?>>Sept</option>
                                                <option <?php if(set_value('purchase_month') === "Oct"){ echo "selected";} else if($unit_details[0]['purchase_month'] === "Oct"){ echo "selected";} ?>>Oct</option>
                                                <option <?php if(set_value('purchase_month') === "Nov"){ echo "selected";} else if($unit_details[0]['purchase_month'] === "Nov"){ echo "selected";} ?>>Nov</option>
                                                <option <?php if(set_value('purchase_month') === "Dec"){ echo "selected";} else if($unit_details[0]['purchase_month'] === "Dec"){ echo "selected";}?>>Dec</option>
                                            </select>
                                            <p><?php echo form_error('purchase_month'); ?></p>
                                        </div> 
                                        <div class="col-md-6">
                                            <select  type="text" class="form-control "   name="purchase_year" id="purchase_year_1" >
                                                 <?php $current_year = date('Y'); for($i = 0; $i> -26; $i--){ ?>
                                                <option  <?php if(set_value('purchase_year') == date("Y",strtotime($i." year"))){ echo "selected";} 
                                                  else if(date("Y",strtotime($i." year")) == $current_year){ echo "selected";}
                                                  else if(date("Y",strtotime($i." year")) == $unit_details[0]['purchase_year']){ echo "selected";}?> >
                                                    <?php echo date("Y",strtotime($i." year")); ?>
                                                </option>
                                                <?php }  ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                 <div class="form-group col-md-12  <?php if( form_error('query_remarks') ) { echo 'has-error';} ?>">
                                     <label for="landmark ">Remarks  <span id="error_remarks" style="color: red;"></label>
                                    <textarea class="form-control" rows="2" id="remarks" name="query_remarks"  placeholder="Enter Problem Description" ><?php if(set_value('query_remarks')) { echo set_value('query_remarks'); } else { echo $booking_history[0]['booking_remarks'];} ?></textarea>
                                    <?php echo form_error('query_remarks'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-info" style="margin-top:20px;">
                <div class="panel-heading">Step 4</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                           
                            <div class="col-md-4 ">
                                <div class="form-group col-md-12  <?php if( form_error('alternate_phone_number') ) { echo 'has-error';} ?>">
                                    <label for="alternate_phone_number ">Alternate Mobile</label>
                                    <input type="text" class="form-control booking_alternate_contact_no"  id="booking_alternate_contact_no" name="alternate_phone_number" value = "<?php if(set_value('alternate_phone_number')){ echo set_value('alternate_phone_number'); } else { echo $booking_history[0]['booking_alternate_contact_no'];} ?>" placeholder ="Please Enter Alternate Contact No" >
                                    <?php echo form_error('alternate_phone_number'); ?>
                                </div>
                            </div>
                            <div class="col-md-4 ">
                                <div class="form-group col-md-12  <?php if( form_error('user_email') ) { echo 'has-error';} ?>">
                                    <label for="user_email ">Email </label>
                                    <input type="email" class="form-control"  id="booking_user_email" name="user_email" value = "<?php if(set_value('user_email')){ echo set_value('user_email'); } else { echo $booking_history[0]['user_email'];} ?>" placeholder="Please Enter User Email">
                                    <?php echo form_error('user_email'); ?>
                                </div>
                            </div>
                            
                            <div class="col-md-4 ">
                                <div class="form-group col-md-12  <?php if( form_error('landmark') ) { echo 'has-error';} ?>">
                                    <label for="landmark ">Landmark </label>
                                    <input type="text" class="form-control" id="landmark" name="landmark" value = "<?php if(set_value('landmark')){ echo set_value('landmark'); } else { echo $booking_history[0]['booking_landmark'];} ?>" placeholder="Enter Any Landmark">
                                    <?php echo form_error('landmark'); ?>
                                </div>
                            </div>
                            <div class="col-md-12 ">
                                <div class="form-group col-md-12  <?php if( form_error('booking_address') ) { echo 'has-error';} ?>">
                                    <label for="landmark ">Booking Address *  <span id="error_address" style="color: red;"></label>
                                    <textarea class="form-control" rows="2" id="booking_address" name="booking_address" placeholder="Please Enter Address"  required ><?php if(set_value('booking_address')){ echo set_value('booking_address'); } else { echo $booking_history[0]['booking_address'];} ?></textarea>
                                    <?php echo form_error('booking_address'); ?>
                                </div>
                            </div>
                           
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group  col-md-12" >
                    <center>
                        <input type="hidden" name="product_type" value="Delivered"/>
                        <input type="submit" id="submitform" class="btn btn-primary "<?php if(count($unique_appliance) > 1){ echo "disabled";}?> onclick="return check_validation()" value="Submit Booking">
                    </center>
                </div>
            </div>
        </form>
        <!-- end Panel Body  -->
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
    
            document.getElementById(input_id).style.borderColor = color;
            document.getElementById(error_id).innerHTML = message;
    }
</script>
<?php if($this->session->userdata('success')){$this->session->unset_userdata('success');} ?>
<script type="text/javascript">
    $("#booking_city").select2({
         tags: true
    });
    $("#price_tag").select2();
   // $("#service_name").select2();
    $("#appliance_brand_1").select2();
    $("#appliance_capacity_1").select2();
    $("#appliance_category_1").select2();
    $("#partner_source").select2();
    var today = new Date();
    $("#booking_date").datepicker({
        dateFormat: 'yy-mm-dd', 
        minDate: today.getHours() >= 12 ? 1 : 0,
        beforeShowDay: function(date) {
            var day = date.getDay();
            return [(day !== 0), ''];
        }
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
                    brand:'<?php echo $unit_details[0]['appliance_brand']; ?>',
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
                    brand: brand, category:'<?php echo $unit_details[0]['appliance_category']; ?>', 
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
        brand: brand,category:category, capacity:'<?php echo $unit_details[0]['appliance_capacity']; ?>',
                        partner_type:partner_type},
            
            success: function (data) {

                    //First Resetting Options values present if any
                    //$("#appliance_capacity_1 option[value !='option1']").remove();
                    //$('#appliance_capacity_1').append(data).change();
                    $('#appliance_capacity_1').html(data).change();
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
        }
       
        $.ajax({
                        type: 'POST',
                        url: '<?php echo base_url(); ?>employee/partner/get_model_for_partner',
                        data: {service_id: service_id,partner_id:<?php echo $this->session->userdata('partner_id')?>, 
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
                                    getPrice();
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
            
        } else {
            postData['capacity'] = capacity;
        }
        postData['booking_id'] = '<?php echo $booking_history[0]['booking_id']; ?>';
        postData['service_category'] = '<?php  echo $price_tags; ?>';
        postData['pincode'] = $("#booking_pincode").val();
        postData['city'] = $("#booking_city").val();
        postData['assigned_vendor_id'] = $("#assigned_vendor_id").val();
        
        postData['partner_type'] = '<?php echo $partner_type;?>';
        
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
    $("input[type=checkbox]:checked").each(function (i) {
        count = count + 1;

        var id = this.id.split('checkbox_');

        var up_val = $("#is_up_val_" + id[1]).val();

        if (Number(up_val) === 1) {
            is_upcountry = 1;
        } else  if (Number(up_val) === -1) {
            non_upcountry = -1;
        }
    });
    if (count > 0) {
        if(non_upcountry === -1){
            $("#upcountry_charges").text("0.00");
            $("#checkbox_upcountry").val("upcountry_0_0");
           document.getElementById("checkbox_upcountry").checked = false;
           final_price();
           $('#submitform').attr('disabled', false);
             
        } else if (is_upcountry === 1) {
            
            var data1 = jQuery.parseJSON(upcountry_data);
            console.log(data1);
            var partner_approval = Number(data1.partner_upcountry_approval);

            if (data1.message === "UPCOUNTRY BOOKING") {
                $("#upcountry_charges").text("0.00");
                $("#checkbox_upcountry").val("upcountry_0_0");
                document.getElementById("checkbox_upcountry").checked = false;
                final_price();
                $('#submitform').attr('disabled', false); 

            } else if (data1.message === "UPCOUNTRY LIMIT EXCEED" && partner_approval === 0) {
                $('#submitform').attr('disabled', true);
                 document.getElementById("checkbox_upcountry").checked = false;
                 $("#upcountry_charges").text("0.00");
                 $("#checkbox_upcountry").val("upcountry_0"); 
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
            var data1 = jQuery.parseJSON(upcountry_data);
            if (data1.message === "UPCOUNTRY BOOKING" || data1.message === "UPCOUNTRY LIMIT EXCEED") {


                var upcountry_charges = (Number(3) * Number(data1.upcountry_distance)).toFixed(2);
                
                $("#upcountry_charges").text(upcountry_charges);
                $("#checkbox_upcountry").val("upcountry_"+upcountry_charges +"_0");
                document.getElementById("checkbox_upcountry").checked = true;
                
                final_price();

            } else {
                document.getElementById("checkbox_upcountry").checked = false;
                $("#upcountry_charges").text("0.00");
                $("#checkbox_upcountry").val("upcountry_0_0");
                
            }
            $('#submitform').attr('disabled', false);
            
        }
    } else {
        
        $("#upcountry_charges").text("0.00");
        $("#checkbox_upcountry").val("upcountry_0_0");
        final_price();
        $('#submitform').attr('disabled', true);
    }
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
    
</script>
<script>
    $(document).ready(function(){
        $("#dealer_phone_number").keyup(function(){
            var partner_id = '<?php echo $this->session->userdata('partner_id')?>';
            if(partner_id !== undefined){
                 var search_term = $(this).val();
                 dealer_setup(partner_id, search_term, "dealer_phone_number_1");
            } else{
                alert("Please Select Partner");
            }
        });
	$("#dealer_name").keyup(function(){
            var partner_id = '<?php echo $this->session->userdata('partner_id')?>';
            if(partner_id !== undefined){
                var search_term = $(this).val();
                dealer_setup(partner_id, search_term, "dealer_name");
                 
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

</script>