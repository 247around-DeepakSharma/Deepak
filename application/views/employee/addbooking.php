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
            <div class="panel-heading">Add Booking</div>
            <div class="panel-body">
                <form name="myForm" class="form-horizontal" id ="booking_form" action="<?php echo base_url()?>employee/booking/index/<?php echo $user[0]['user_id'];?>"  method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="col-md-4">Name</label>
                                    <div class="col-md-6">
                                        <input type="hidden" name="upcountry_data" value="" id="upcountry_data" /> 
                                         <input type="hidden" name="partner_type" value="" id="partner_type" />
                                          <input type="hidden" name="assigned_vendor_id" value="" id="assigned_vendor_id" />
                                        <input type="text" class="form-control" id="name" name="user_name" value = "<?php echo $user[0]['name'] ?>" readonly="readonly">
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="booking_primary_contact_no" class="col-md-4">Mobile *</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control"  id="booking_primary_contact_no" name="booking_primary_contact_no" value = "<?php echo $user[0]['phone_number']?>" required>
                                    </div>
                                </div>
                                 <div class="form-group <?php
                                if (form_error('booking_pincode')) {
                                    echo 'has-error';
                                } ?>">
                                     <label for="booking_pincode" class="col-md-4">Pincode * </label>
                                <div class="col-md-6">
                                     <input type="text" class="form-control" id="booking_pincode" name="booking_pincode" value = "<?php if(isset($user[0]['pincode'])){echo $user[0]['pincode'];} ?>" placeholder="Enter Area Pin" > 
                                    <span id="error_pincode" style="color:red"></span>
                                        <?php echo form_error('booking_pincode'); ?>
                                </div>
                            </div>
                                <div class="form-group ">
                                    <label for="booking_city" class="col-md-4">City *</label>
                                    <div class="col-md-6">
                                        <select  class="form-control"  id="booking_city" name="city" required>
                                            <option selected="selected" disabled="disabled">Select City</option>
                                            <?php

                                                 $flag = 0;
                                                foreach ($city as $key => $cites) {

                                                    ?>
                                            <option <?php if(strtolower($cites['district']) == strtolower($user[0]['city'])){ echo "Selected"; $flag = 1; }?>><?php echo $cites['district']; ?></option>
                                            <?php  }
                                                ?>
                                           <?php if($flag == 0){ ?>
                                            <option selected="selected" ><?php echo $user[0]['city']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group ">
                                    <label for="service_name" class="col-md-4">Appliance *</label>
                                    <div class="col-md-6">
                                        <input type="hidden" name="service" id="services"/>
                                        <select type="text" class="form-control"  id="service_id" name="service_id" value = "<?php echo set_value('service_id'); ?>" onChange="getBrandForService()"  required>
                                            <option selected disabled>Select Service</option>
                                           

                                        </select>
                                    </div>
                                </div>
                                 <div class="form-group ">
                                     <label for="service_name" class="col-md-4">Order ID </label>
                                      <div class="col-md-6">
                                          <input class="form-control" name= "order_id" id="order_id"></input>
                                      </div>
                                 </div>
                                <!--  end col-md-6  -->
                            </div>
                            <!--  start col-md-6  -->
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label  class="col-md-4">Email</label>
                                    <div class="col-md-6">
                                        <input type="email" class="form-control"  id="booking_user_email" name="user_email" value = "<?php echo $user[0]['user_email']; ?>">

                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="booking_alternate_contact_no" class="col-md-4">Alternate No</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control booking_alternate_contact_no"  id="booking_alternate_contact_no" name="booking_alternate_contact_no" value = "<?php echo $user[0]['alternate_phone_number']?>" >
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="source_name" class="col-md-4">Booking Source *</label>
                                    <div class="col-md-6">
                                        <select type="text" onchange= "getAppliance()" class="booking_source form-control"  id="source_code" name="source_code" required>
                                            <option selected="selected" disabled="disabled">Select Booking Source</option>
                                            <?php foreach ($sources as $key => $values) { ?>
                                            <option  data-id="<?php echo $values['partner_id']; ?>" value=<?php echo $values['code']; ?>>
                                                <?php echo $values['source']; }    ?>
                                            </option>
                                            <?php echo form_error('source_code'); ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group ">
                                    <label for="partner_source" class="col-md-4">Partner Source</label>
                                <div class="col-md-6">
                                    <select class="form-control"  id="partner_source" name="partner_source"  >
                                    <option value="">Please select seller channel</option>
                                    <option>Amazon</option>
                                    <option>AndroidApp</option>
                                    <option>CallCenter</option>
                                    <option>Ebay</option>
                                    <option>Flipkart</option>
                                    <option>Ebay</option>
                                    <option>Offline</option>
                                    <option>Shopclues</option>
                                    <option>TataCliq</option>
                                    <option>Jeeves-delivered-excel</option>
                                    <option>STS</option>
                                    <option>Snapdeal-delivered-excel</option>
                                    <option>Snapdeal-shipped-excel</option>
                                    <option>Snapdeal</option
                                    <option>Paytm-delivered-excel</option>
                                    <option>Paytm</option>
                                    <option>Website</option>
                                    
                                    
                                </select>
                                </div>
                                </div>
                                <div class="form-group <?php if( form_error('type') ) { echo 'has-error';} ?>">
                                    <label for="type" class="col-md-4">Type *</label>
                                    <div class="col-md-8">
                                        <input style="width:65px;height:20px;display:inline;" id="query" type="radio" class="form-control booking_type" name="type" value="Query" required>Query
                                        <input style="width:65px;height:20px;display:inline;" id="booking" type="radio" class="form-control booking_type" name="type" value="Booking" required>Booking
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="support_file" class="col-md-4">Upload Support file</label>
                                    <div class="col-md-8">
                                        <input type="file" class="form-control" id="support_file" name="support_file">
                                    </div>
                                </div>
                                <!-- end col-md-6 -->
                            </div>
                        </div>
                    </div>
                    <!-- row End  -->
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
                                                <select type="text" class="form-control appliance_brand" onchange="getCategoryForService(this.id)"   name="appliance_brand[]" id="appliance_brand_1" required>
                                                    <option selected disabled>Select Brand</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="service_name" class="col-md-4">Category *</label>
                                            <div class="col-md-6">
                                                <select type="text" class="form-control appliance_category"   id="appliance_category_1" name="appliance_category[]"  onChange="getCapacityForCategory(this.value, this.id);" required>
                                                    <option selected disabled>Select Appliance Category</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group <?php if (form_error('appliance_capacity')) { echo 'has-error';} ?>">
                                            <label for="service_name" class="col-md-4">Capacity *</label>
                                            <div class="col-md-6">
                                                <select type="text" class="form-control appliance_capacity"   id="appliance_capacity_1" name="appliance_capacity[]"  onChange="getPricesForCategoryCapacity(this.id);">
                                                    <option selected disabled>Select Appliance Capacity</option>
                                                </select>
                                                <?php echo form_error('appliance_capacity'); ?>
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="type" class="col-md-4">Appliance Model </label>
                                            <div class="col-md-6">
                                                <input  type="text" class="form-control"  name="model_number[]" id="model_number_1" value = "" placeholder="Enter Model" >

                                            </div>
                                        </div>
<!--                                          <div class="form-group ">
                                            <label for="type" class="col-md-4">Appliance Serial No </label>
                                            <div class="col-md-6">
                                                <input  type="text" class="form-control"  name="serial_number[]" id="serial_number_1" value = "<?php if(isset($unit_details[0]['serial_number'])) { echo $unit_details[0]['serial_number']; } ?>" placeholder="Enter Appliance Serial Number"  <?php if(!empty($appliance_id)) { echo "readonly"; } ?> >

                                            </div>
                                        </div>-->
                                         <div class="form-group ">
                                            <label for="type" class="col-md-4">Appliance Description </label>
                                            <div class="col-md-6">
                                                <input  type="text" class="form-control"  name="appliance_description[]" id="description_1" value = "<?php if(isset($unit_details[0]['description'])) { echo $unit_details[0]['description']; } ?>" placeholder="Enter Description"  <?php if(!empty($appliance_id)) { echo "readonly"; } ?> >

                                            </div>
                                        </div>
                                        <!--<div class="form-group <?php if( form_error('appliance_tags') ) { echo 'has-error';} ?>">
                                            <label for="type" class="col-md-4">Appliance Tag</label>
                                            <div class="col-md-6">
                                                <input  type="text" class="form-control"  name="appliance_tags[]" id="appliance_tags_1" value = "<?php echo set_value('appliance_tags'); ?>" placeholder="Enter Tag" >
                                                <?php echo form_error('appliance_tags'); ?>
                                            </div>
                                        </div>-->
                                        <div class="form-group ">
                                            <label for="type" class="col-md-4">Purchase Year</label>
                                            <div class="col-md-4">
                                                <select  type="text" class=" form-control "   name="purchase_month[]" id="purchase_month_1" >
                                                    <option selected="selected" value="">Month</option>

                                                    <option <?php if(date('m') == '01'){ echo "selected";} ?>>Jan</option>
                                                    <option <?php if(date('m') == '02'){ echo "selected";} ?>>Feb</option>
                                                    <option <?php if(date('m') == '03'){ echo "selected";} ?>>Mar</option>
                                                    <option <?php if(date('m') == '04'){ echo "selected";} ?>>Apr</option>
                                                    <option <?php if(date('m') == '05'){ echo "selected";} ?>>May</option>
                                                    <option <?php if(date('m') == '06'){ echo "selected";} ?>>Jun</option>
                                                    <option <?php if(date('m') == '07'){ echo "selected";} ?>>July</option>
                                                    <option <?php if(date('m') == '08'){ echo "selected";} ?>>Aug</option>
                                                    <option <?php if(date('m') == '09'){ echo "selected";} ?>>Sept</option>
                                                    <option <?php if(date('m') == '10'){ echo "selected";} ?>>Oct</option>
                                                    <option <?php if(date('m') == '11'){ echo "selected";} ?>>Nov</option>
                                                    <option <?php if(date('m') == '12'){ echo "selected";} ?>>Dec</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-md-4">
                                                    <select  type="text" class="col-md-3 form-control "   name="purchase_year[]" id="purchase_year_1" required>
                                                        <option value="" >Year</option>
                                                        <?php for($i = 0; $i> -26; $i--){ ?>
                                                        <option  <?php if(date("Y",strtotime($i." year")) === date("Y")){ echo "selected";} ?>>
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
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="cloned"></div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group ">
                                <label for="booking_date" class="col-md-4">Booking Date *</label>
                                <div class="col-md-6">
                                <div class="input-group input-append date">
                                    <input id="booking_date" class="form-control"  name="booking_date" type="date" value = "<?php if(date('H') < '13'){echo  date("Y-m-d");}else{ echo date("Y-m-d", strtotime("+1 day"));} ?>" required readonly='true'>
                                    <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label  for="booking_address" class="col-md-4">Booking Address *</label>
                                <div class="col-md-6">
                                    <textarea class="form-control" rows="4" id="booking_address" name="home_address"   ><?php echo $user[0]['home_address']; ?></textarea>
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
                                    </div>&nbsp;<span id="errmsg1"></span>
                                </div>
                            </div>
                           
                        </div>
                        <div class="col-md-6">
                            <div class="form-group ">
                                <label  for="booking_timeslot" class="col-md-4">Booking Time Slot *</label>
                                <div class="col-md-6">
                                    <select class="form-control" id="booking_timeslot" name="booking_timeslot" value = "<?php echo set_value('booking_timeslot'); ?>"  required>
                                        <option selected disabled>Select time slot</option>
                                        <option>10AM-1PM</option>
                                        <option>1PM-4PM</option>
                                        <option selected="">4PM-7PM</option>
                                    </select>
                                </div>
                            </div>
<!--                            <div class="form-group ">
                                <label for="type" class="col-md-4">Potential Value</label>
                                <div class="col-md-6">

                                    <input  type="text" class="form-control"  name="potential_value" id="potential_value" value = "<?php echo set_value('potential_value'); ?>" placeholder="Enter potential_value" >
                                </div>
                            </div>-->
                            <div class="form-group ">
                                <label for="type" class="col-md-4">Remarks</label>
                                <div class="col-md-6">
                                    <textarea class="form-control" rows="4" name="query_remarks" id="query_remarks" placeholder="Enter Query Remarks"  ></textarea>
                                </div>
                            </div>
                  <div class="form-group ">
                  <label for="Internal Status" class="col-sm-4">Internal Status</label>
                  <div class="col-md-6">
                     <?php
                        
                        foreach($follow_up_internal_status as $status){?>
                     <div class="radio">
                        <label>
                        <input type="radio" name="internal_status"  class="internal_status"  value="<?php  echo $status->status;?>" <?php if(isset($booking_history[0]['internal_status'])){ if( $status->status == $booking_history[0]['internal_status']){ echo "checked";}} ?> >
                         <?php  echo $status->status;?>
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
<!--                                <button style="margin-right: 25px;" type="button" class="btn btn-info btn-md " data-toggle="modal" data-target="#myModal">Check Details</button>-->
<input type="submit" id="submitform" class="btn btn-primary " onclick="return addBookingDialog()" value="Submit Booking">
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
    $(".booking_source").select2();
    $("#service_id").select2();
    $("#booking_city").select2({
         tags: true
    });
    $("#partner_source").select2();

    $("#booking_date").datepicker({dateFormat: 'yy-mm-dd', minDate: 0});
    $("#booking_pincode").keyup(function(event) {
       
        check_pincode();
        getBrandForService();
        
    });
</script>
<script type="text/javascript">
    var regex = /^(.+?)(\d+)$/i;
    var cloneIndex = $(".clonedInput").length +1;

    function clone(){
       $(this).parents(".clonedInput").clone()
           .appendTo(".cloned")
           .attr("id", "cat" +  cloneIndex)
           .find("*")
           .each(function() {
               var id = this.id || "";
               var match = id.match(regex) || [];
               //console.log(match.length);
               if (match.length === 3) {
                   this.id = match[1] + (cloneIndex);
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
  
  //called when key is pressed in textbox
  $("#grand_total_price").keypress(function (e) {
     //if the letter is not digit then display error and don't type anything
     if (e.which !== 8 && e.which !== 0 && (e.which < 48 || e.which > 57)) {
        //display error message
        $("#errmsg1").html("Digits Only").show().fadeOut("slow");
               return false;
    }
   });
   
   
});
</script>
<style type="text/css">
#errmsg1
{
color: red;
}
</style>
<?php $this->session->unset_userdata('error'); ?>
