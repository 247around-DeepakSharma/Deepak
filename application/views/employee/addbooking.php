<script src="<?php echo base_url();?>js/custom_js.js"></script>
<div id="page-wrapper" >
    <div class="container" >
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
                                <label for="booking_pincode" class="col-md-4">Pincode *</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="booking_pincode" name="booking_pincode" value = "<?php if(isset($user[0]['pincode'])){echo $user[0]['pincode'];} ?>" placeholder="Enter Area Pin" required>
                                    <?php echo form_error('booking_pincode'); ?>
                                </div>
                            </div>
                                <div class="form-group ">
                                    <label for="booking_city" class="col-md-4">City *</label>
                                    <div class="col-md-6">
                                        <select type="text" onchange= "getCategoryForService()" class="form-control"  id="booking_city" name="city" required>
                                            <option selected="selected" disabled="disabled">Select City</option>
                                            <?php 
                                                foreach ($city as $key => $cites) { ?>
                                            <option <?php if($cites['district'] == $user[0]['city']){ echo "Selected"; }?>><?php echo $cites['district']; ?></option>
                                            <?php  }
                                                ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-group ">
                                    <label for="service_name" class="col-md-4">Appliance *</label>
                                    <div class="col-md-6">
                                        <input type="hidden" name="service" id="services"/>
                                        <select type="text" class="form-control"  id="service_id" name="service_id" value = "<?php echo set_value('service_id'); ?>" onChange="getBrandForService(this.value), getCategoryForService();"  required>
                                            <option selected disabled>Select Service</option>
                                            <?php foreach ($services as $key => $values) { ?>
                                            <option  value=<?= $values->id; ?>>
                                                <?php echo $values->services; }    ?>
                                            </option>
                                            
                                        </select>
                                    </div>
                                </div>
                                 <div class="form-group ">
                                     <label for="service_name" class="col-md-4">Order ID </label>
                                      <div class="col-md-6">
                                          <input class="form-control" name= "order_id"></input>
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
                                        <select type="text" onchange= "getCategoryForService()" class="booking_source form-control"  id="source_code" name="source_code" required>
                                            <option selected="selected" disabled="disabled">Select Booking Source</option>
                                            <?php foreach ($sources as $key => $values) { ?>
                                            <option  value=<?php echo $values['code']; ?>>
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
                                    <option>CallCenter</option>
                                    <option>Flipkart</option>
                                    <option>Ebay</option>
                                    <option>Offline</option>
                                    <option>STS</option>
                                    <option>Snapdeal-delivered-excel</option>
                                    <option>Snapdeal-shipped-excel</option>
                                    <option>Paytm-delivered-excel</option>
                                </select>
                                </div>
                                </div>
                                <div class="form-group <?php if( form_error('type') ) { echo 'has-error';} ?>">
                                    <label for="type" class="col-md-4">Type *</label>
                                    <div class="col-md-8">
                                        <input style="width:65px;height:20px;display:inline;" id="query" type="radio" class="form-control booking_type" name="type" value="Query" required>Query
                                        <input style="width:65px;height:20px;display:inline;" id="booking" type="radio" class="form-control booking_type" name="type" value="Booking" required>Booking
                                        <?php echo form_error('type'); ?>
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
                                                <select type="text" class="form-control appliance_brand"    name="appliance_brand[]" id="appliance_brand_1" required>
                                                    <option selected disabled>Select Brand</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="service_name" class="col-md-4">Category *</label>
                                            <div class="col-md-6">
                                                <select type="text" class="form-control appliance_category"   id="appliance_category_1" name="appliance_category[]"  onChange="getCapacityForCategory(service_id,this.value, this.id);" required>
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
                                          <div class="form-group ">
                                            <label for="type" class="col-md-4">Appliance Serial No </label>
                                            <div class="col-md-6">
                                                <input  type="text" class="form-control"  name="serial_number[]" id="serial_number_1" value = "<?php if(isset($unit_details[0]['serial_number'])) { echo $unit_details[0]['serial_number']; } ?>" placeholder="Enter Appliance Serial Number"  <?php if(!empty($appliance_id)) { echo "readonly"; } ?> >
                                               
                                            </div>
                                        </div>
                                         <div class="form-group ">
                                            <label for="type" class="col-md-4">Appliance Description </label>
                                            <div class="col-md-6">
                                                <input  type="text" class="form-control"  name="appliance_description[]" id="description_1" value = "<?php if(isset($unit_details[0]['description'])) { echo $unit_details[0]['description']; } ?>" placeholder="Enter Description"  <?php if(!empty($appliance_id)) { echo "readonly"; } ?> >
                                               
                                            </div>
                                        </div>
                                        <div class="form-group <?php if( form_error('appliance_tags') ) { echo 'has-error';} ?>">
                                            <label for="type" class="col-md-4">Appliance Tag</label>
                                            <div class="col-md-6">
                                                <input  type="text" class="form-control"  name="appliance_tags[]" id="appliance_tags_1" value = "<?php echo set_value('appliance_tags'); ?>" placeholder="Enter Tag" >
                                                <?php echo form_error('appliance_tags'); ?>
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="type" class="col-md-4">Purchase Year</label>
                                            <div class="col-md-4">
                                                <select  type="text" class=" form-control "   name="purchase_month[]" id="purchase_month_1" >
                                                    <option selected="selected" value="">Month</option>

                                                    <option>Jan</option>
                                                    <option>Feb</option>
                                                    <option>Mar</option>
                                                    <option>Apr</option>
                                                    <option>May</option>
                                                    <option>Jun</option>
                                                    <option>July</option>
                                                    <option>Aug</option>
                                                    <option>Sept</option>
                                                    <option>Oct</option>
                                                    <option>Nov</option>
                                                    <option>Dec</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-md-4">
                                                    <select  type="text" class="col-md-3 form-control "   name="purchase_year[]" id="purchase_year_1" required>
                                                        <option selected="selected" value="" >Year</option>
                                                        <?php for($i = 0; $i> -26; $i--){ ?>
                                                        <option>
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
                                    <input type="date" class="form-control"  id="booking_date" min="<?php echo date("Y-m-d") ?>" name="booking_date" value = "<?php echo  date("Y-m-d", strtotime("+1 day")); ?>" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label  for="booking_address" class="col-md-4">Booking Address *</label>
                                <div class="col-md-6">
                                    <textarea class="form-control" rows="4" id="booking_address" name="home_address"  required ><?php echo $user[0]['home_address']; ?></textarea>
                                </div>
                            </div>
                           
                            <div class="form-group ">
                                <label for="type" class="col-sm-4">Price To be Pay</label>
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
                                        <option>4PM-7PM</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group ">
                                <label for="type" class="col-md-4">Potential Value</label>
                                <div class="col-md-6">
                                   
                                    <input  type="text" class="form-control"  name="potential_value" id="potential_value" value = "<?php echo set_value('potential_value'); ?>" placeholder="Enter potential_value" >
                                </div>
                            </div>
                            <div class="form-group ">
                                <label for="type" class="col-md-4">Remarks</label>
                                <div class="col-md-6">
                                    <textarea class="form-control" rows="4" name="query_remarks" id="query_remarks" placeholder="Enter Quer Remarks" ></textarea>
                                </div>
                            </div>
                            <div>
                            </div>
                        </div>
                        <div class="form-group  col-md-12" >
                            <center>
                                <button style="margin-right: 25px;" type="button" class="btn btn-info btn-md open-AddBookingDialog" data-toggle="modal" data-target="#myModal">Preview</button>
                                <input type="submit" id="submitform" class="btn btn-info disabled" value="submit">
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
</script>
<script>
    $("#service_id").select2();
    $("#booking_city").select2();
    $("#partner_source").select2();
    //$(".appliance_capacity").select2();
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
               if (match.length == 3) {
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
</script>
<style type="text/css">
#errmsg1
{
color: red;
}
</style>