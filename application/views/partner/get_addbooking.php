<?php  $phone_number = $this->uri->segment(3);  ?>
<div id="page-wrapper" >
    <div class="container-fluid" >
        <form name="myForm" class="form-horizontal" id ="booking_form" action="<?php echo base_url()?>employee/partner/process_addbooking"  method="POST" enctype="multipart/form-data">
            <div class="panel panel-info" style="margin-top:20px;">
                <div class="panel-heading">User Details</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="col-md-4">User Name</label>
                                    <div class="col-md-6">
                                     <input type="hidden" class="form-control" id="user_id" name="user_id" value = "<?php if(isset($user[0]['user_id'])){ echo $user[0]['user_id']; }  ?>"  >
                                        <input type="text" class="form-control" id="name" name="user_name" value = "<?php if(isset($user[0]['name'])){ echo $user[0]['name']; }  ?>" <?php if(isset($user[0]['name'])){ echo "readonly"; }  ?> placeholder="Please Enter User Name">
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="booking_primary_contact_no" class="col-md-4">Primary Contact Number *</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control"  id="booking_primary_contact_no" name="booking_primary_contact_no" value = "<?php if(isset($user[0]['phone_number'])){ echo $user[0]['phone_number']; } else { echo $phone_number; }  ?>" required>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="booking_city" class="col-md-4">Booking City *</label>
                                    <div class="col-md-6">
                                        <select type="text" onchange= "getCategory()" class="form-control"  id="booking_city" name="city" required>
                                            <option selected="selected" disabled="disabled">Select City</option>
                                            <?php 
                                                foreach ($city as $key => $cites) { ?>
                                            <option <?php if(isset($user[0]['city'])){ if($cites['district'] == $user[0]['city']){ echo "Selected"; } }?>><?php echo $cites['district']; ?></option>
                                            <?php  }
                                                ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="booking_pincode" class="col-md-4">Booking Pincode *</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="booking_pincode" name="booking_pincode" value = "<?php if(isset($user[0]['pincode'])){echo $user[0]['pincode'];} ?>" placeholder="Enter Area Pin" required>
                                    </div>
                                </div>
                                  <div class="form-group ">
                                    <label for="booking_pincode" class="col-md-4">Near Landmark </label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="landmark" name="landmark" value = "<?php if(isset($user[0]['landmark'])){echo $user[0]['landmark'];} ?>" placeholder="Enter Any Near Landmark" required>
                                    </div>
                                </div>
                                <!--  end col-md-6  -->
                            </div>
                            <!--  start col-md-6  -->
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label  class="col-md-4">User Email</label>
                                    <div class="col-md-6">
                                        <input type="email" class="form-control"  id="booking_user_email" name="user_email" value = "<?php if(isset($user[0]['city'])){  echo $user[0]['user_email'];  }  ?>" placeholder="Please Enter User Email">
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="booking_alternate_contact_no" class="col-md-4">Alternate Contact No</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control booking_alternate_contact_no"  id="booking_alternate_contact_no" name="booking_alternate_contact_no" value = "<?php if(isset($user[0]['city'])){  echo $user[0]['alternate_phone_number']; } ?>" placeholder ="Please Enter Alternate Contact No" >
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label  for="booking_address" class="col-md-4">Booking Address *</label>
                                    <div class="col-md-6">
                                        <textarea class="form-control" rows="6" id="booking_address" name="home_address"  required ><?php if(isset($user[0]['city'])){  echo $user[0]['home_address']; } ?></textarea>
                                    </div>
                                </div>
                                <!-- end col-md-6 -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-info" style="margin-top:20px;">
                <div class="panel-heading">Order Details</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label for="service_name" class="col-md-4">Order ID </label>
                                    <div class="col-md-6">
                                        <input class="form-control" name= "order_id" value="<?php if(isset($user[0]['order_id'])){  echo $user[0]['order_id']; } ?>" placehoder ="Please Enter Order ID" id="order_id"></input>
                                    </div>
                                </div>

                              <input type="hidden" name="service_id" id="service_id" value="46" />
                              <input type="hidden" name="service_name"  value="Television"></input>
                               <div class="form-group ">
                                    <label for="booking_date" class="col-md-4">Booking Date *</label>
                                    <div class="col-md-6">
                                        <input type="date" class="form-control"  id="booking_date" min="<?php echo date("Y-m-d", strtotime("+1 day")) ?>" name="booking_date" value = "<?php echo  date("Y-m-d", strtotime("+1 day")); ?>"  required>
                                    </div>
                                </div>
                                
                                <!--  end col-md-6  -->
                            </div>
                            <!--  start col-md-6  -->
                            <div class="col-md-6">
                            <div class="form-group ">
                                    <label for="source_name" class="col-md-4">Booking Source *</label>
                                    <div class="col-md-6">
                                        <select type="text" class="booking_source form-control"  id="partner_source" name="partner_source" required>
                                            <option selected="selected" disabled="disabled">Select Booking Source</option>
                                            <option>CallCenter</option>
                                            <option>Snapdeal</option>
                                            <option>Flipkart</option>
                                            <option>Ebay</option>
                                            <option>Offline</option>
                                        </select>
                                    </div>
                                </div>
                               
                                <div class="form-group ">
                                    <label  for="booking_timeslot" class="col-md-4">Booking Time Slot *</label>
                                    <div class="col-md-6">
                                        <select class="form-control" id="booking_timeslot" name="booking_timeslot" value = "<?php echo set_value('booking_timeslot'); ?>"  required>
                                            <option selected disabled>Select time slot</option>
                                        
                                            <option>1PM-4PM</option>
                                            <option>4PM-7PM</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- end col-md-6 -->
                            </div>
                        </div>
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
                                          <?php if($this->session->userdata('partner_id') == "247011"){ ?>
                                          <option selected="selected">Ray</option>

                                        <?php } else if($this->session->userdata('partner_id') == "247010"){ ?>
                                         <option selected="selected">Wybor</option>
                                        <?php    } ?>
                                        </select>
                                    </div>
                                </div>
                                <input hidden="text" name="partner_id" id="partner_id" value="<?php echo $this->session->userdata('partner_id') ; ?>"></input>
                                <div class="form-group">
                                    <label for="service_name" class="col-md-4">Category *</label>
                                    <div class="col-md-6">
                                        <select type="text" class="form-control appliance_category"   id="appliance_category_1" name="appliance_category[]"  onChange="getCapacity(this.value, this.id);" required>
                                            <option selected disabled>Select Appliance Category</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group <?php if (form_error('appliance_capacity')) { echo 'has-error';} ?>">
                                    <label for="service_name" class="col-md-4">Capacity *</label>
                                    <div class="col-md-6">
                                        <select type="text" class="form-control appliance_capacity"   id="appliance_capacity_1" name="appliance_capacity[]"  onChange="get_price_table(this.id);">
                                            <option selected disabled>Select Appliance Capacity</option>
                                        </select>
                                        <?php echo form_error('appliance_capacity'); ?>
                                    </div>
                                </div>
                                <div class="form-group <?php if( form_error('model_number') ) { echo 'has-error';} ?>">
                                    <label for="type" class="col-md-4">Appliance Model </label>
                                    <div class="col-md-6">
                                        <input  type="text" class="form-control"  name="model_number[]" id="model_number_1" value = "<?php echo set_value('model_number'); ?>" placeholder="Enter Model" >
                                        <?php echo form_error('model_number'); ?>
                                    </div>
                                </div>
                                
                                <div class="form-group ">
                                    <label for="type" class="col-md-4">Purchase Year</label>
                                    <div class="col-md-4">
                                        <select  type="text" class=" form-control "   name="purchase_month[]" id="purchase_month_1" >
                                            <option selected="selected" value="">Month</option>
                                            <option <?php if(date("m") == "01"){echo "selected";} ?> >Jan</option>
                                            <option <?php if(date("m") == "02"){echo "selected";} ?>>Feb</option>
                                            <option <?php if(date("m") == "03"){echo "selected";} ?>>Mar</option>
                                            <option <?php if(date("m") == "04"){echo "selected";} ?>>Apr</option>
                                            <option <?php if(date("m") == "05"){echo "selected";} ?>>May</option>
                                            <option <?php if(date("m") == "06"){echo "selected";} ?>>Jun</option>
                                            <option <?php if(date("m") == "07"){echo "selected";} ?>>July</option>
                                            <option <?php if(date("m") == "08"){echo "selected";} ?>>Aug</option>
                                            <option <?php if(date("m") == "09"){echo "selected";} ?>>Sept</option>
                                            <option <?php if(date("m") == "10"){echo "selected";} ?>>Oct</option>
                                            <option <?php if(date("m") == "11"){echo "selected";} ?>>Nov</option>
                                            <option <?php if(date("m") == "12"){echo "selected";} ?>>Dec</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-4">
                                            <select  type="text" class="col-md-3 form-control "   name="purchase_year[]" id="purchase_year_1" required>
                                                <option selected="selected" value="" >Year</option>
                                                <?php for($i = 0; $i> -26; $i--){ ?>
                                                <option <?php if(date("Y",strtotime($i." year")) == "2016"){ echo "selected";} ?> >
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
                        <label for="type" class="col-sm-4">Price To be Pay</label>
                        <div class="col-md-6">
                            <div class="input-group">
                                <div class="input-group-addon">Rs.</div>
                                <input  type="text" class="form-control"  name="grand_total_price" id="grand_total_price" value="0" placeholder="Total Price" readonly>
                            </div>
                            &nbsp;<span id="errmsg1"></span>
                        </div>
                    </div>
                    <div class="form-group ">
                        <label for="type" class="col-md-4">Problem Description</label>
                        <div class="col-md-8">
                            <textarea class="form-control" rows="5" name="booking_remarks" id="query_remarks" placeholder="Enter Problem Description" ></textarea>
                        </div>
                    </div>
                </div>
                <div class="form-group  col-md-12" >
                    <center>
                        <button style="margin-right: 25px;" type="button" class="btn btn-info btn-md open-AddBookingDialog" data-toggle="modal" data-target="#myModal1">Preview</button>
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
    
    $("#booking_city").select2();
     $(".booking_source").select2();
    $(".appliance_category").select2();
    $(".appliance_capacity").select2();
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
        var numItems = $('.clonedInput').length -1;
    
        if(numItems == 0){
            alert("Please atleast one Details fill");
            return false;

        } else {

            $(this).parents(".clonedInput").remove();
            final_price();
            return false;
        }
      
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