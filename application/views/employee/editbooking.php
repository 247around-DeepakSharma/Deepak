
<script>
    function validate()
    {
      var total_price=$('#total_price').val();
      var pin=$('#booking_pincode').val();
      var address=$('#booking_address').val();

      if(total_price=="")
      {
        alert("Please enter total price");
        return false;
      }
      if (pin=="" || pin.length!=6) 
      {
        alert("Please enter 6 digit Pincode.");
        return false;
      }
      if (address=="")
      {
        alert("Please enter the booking address.");
        return false;
      }
      return confimation();     //For the confermation Pop up once validation is completed
    }

    function query_validate()
    {
      var query_remarks=$('#query_remarks').val();
      var potential_val=$('#potential_value').val();
      if (query_remarks=="") 
      {
        alert("Please Enter Query Remarks..");
        return false;
      }
      if(potential_val=="" || potential_val== 0)
      {
        alert("Please Enter Potential Value");
        return false;
      }
      return confimation();     //For the confermation Pop up once validation is completed
    }

    function confimation()
    {
      var check_confermation_value = confirm("Press Ok to continue, else press Cancel");
      if(check_confermation_value!= true)
      {
        return false;
      }
      return true;
    }

    function getCapacityForCategory(category)
    {
        var service_id = $("#service_id").val();
        //alert(category);
        
        $("#priceList").html("<tr><th>Service Category</th><th>Total Charges</th><th>Selected Services</th></tr>");
        
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/booking/getCapacityForCategory/' + service_id + "/" + category,
            success: function (data) {
                //alert(data);
                
                $("#appliance_capacity").html(data);
                if (data != "<option></option>")
                {
                    var capacity = $("#appliance_capacity").val();
                    getPricesForCategoryCapacity();
                }
                else
                {
                    $("#appliance_capacity").html(data);
                    var capacity = "NULL";
                    getPricesForCategoryCapacity();
                }
            }
        });
    }

    function getPricesForCategoryCapacity()
    {
        var service_id = $("#service_id").val();
        var category = $("#appliance_category").val();
        if ($("#appliance_capacity").val() != "")
        {
            var capacity = $("#appliance_capacity").val();
        }
        else
        {
            var capacity = "NULL";
        }
        
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/booking/getPricesForCategoryCapacity/' + service_id + "/" + category + "/" + capacity,
            success: function (data) {
                //alert(data);
                
                $("#priceList").html(data);
                //Check selected services
                var ch = '<?php echo $unit_details['price_tags']; ?>';
                //console.log(ch);
                var dh = ch.split(",");
                //console.log(dh);
                for (var i = 0 ; i < dh.length-1  ; i++) {
                    document.getElementsByName(dh[i].replace(/\s/g, ""))[0].checked = true;
                }
            }
        });

    }

    function service()
    {
        var service_name = '';
        $('#priceList .Checkbox1:checked').each(function () {
            service_name += ($(this).attr('name')).toString() + ',';
        });

        $('#items_selected').val(service_name);
        //alert(service_name);
    }
    $(document).ready ( function() {
        //alert('ok');
        var category = $("#appliance_category").val();
        if (category != "") {
            //alert(category);
            getPricesForCategoryCapacity();
        }
        //auto-select pre-filled date/time
        var b_date = <?php echo json_encode($query1[0]['booking_date']); ?>;
        var d = b_date.split('-');
        var date_str = d[2] + "-" + d[1] + "-" + d[0];
        //alert(date_str);
        $("#booking_date").val(date_str);
        var timeslot = <?php echo json_encode($query1[0]['booking_timeslot']); ?>;
        //alert(timeslot);
        $("[value=" + timeslot + "]").attr('selected','selected');
    });
    
</script>

<div id="page-wrapper"> 
   <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">

              <?php if (isset($query1)) 
                {
                  foreach ($query1 as $key => $data1)
                  { 
                    $booking['id']      = $data1['id'];
                    $booking['user_id'] = $data1['user_id'];
                    $booking['booking_id'] =  $data1['booking_id']; 
                      $booking['service_id'] =  $data1['service_id']; 
                  }
                }
              ?>

              <?php if (isset($query3)) 
                {
                  foreach ($query3 as $key => $data3)
                  { 
                    $booking3 = $data3['booking_id'];
                  }
                }
              ?>
              
              <h1 class="page-header">
                  <b>  Edit Booking / Convert to Query</b>
              </h1>

              <form class="form-horizontal" action="<?php echo base_url()?>employee/booking/process_edit_booking_form/<?php echo $booking['booking_id']; ?>" onSubmit="service()" method="POST" >

                <div class="form-group <?php if( form_error('name') ) { echo 'has-error';} ?>">
                  <label for="name" class="col-md-2">User Name</label>
                  <div class="col-md-6">
                    <input type="text" class="form-control"  name="name" value = "<?php if (isset($data3['name'])) {echo $data3['name']; }?>"  disabled>
                    <?php echo form_error('name'); ?>
                  </div>
                </div>

                <div class="form-group <?php if( form_error('booking_primary_contact_no') ) { echo 'has-error';} ?>">
                  <label for="booking_primary_contact_no" class="col-md-2">Phone Number</label>
                  <div class="col-md-6">
                    <input type="text" class="form-control"  name="booking_primary_contact_no" value = "<?php if (isset($data3['booking_primary_contact_no'])) {echo $data3['booking_primary_contact_no']; } else if (isset($data3['phone_number'])){ echo $data3['phone_number']; }?>"/>
                    <?php echo form_error('booking_primary_contact_no'); ?>
                  </div>
                </div>

                <div class="form-group <?php if (form_error('booking_alternate_contact_no')) {echo 'has-error'; } ?>">
                <label for="booking_alternate_contact_no" class="col-md-2">Alternate Contact No</label>
                <div class="col-md-6">
                  <input type="text" class="form-control"  id="booking_alternate_contact_no" 
                         name="booking_alternate_contact_no" 
                         value = "<?php if (isset($data3['booking_alternate_contact_no']) && $data3['booking_alternate_contact_no']!="") {
                             echo $data3['booking_alternate_contact_no']; 
                         }else{
                             echo $data3['alternate_phone_number'];
                         }?>">
                  <?php echo form_error('booking_alternate_contact_no'); ?>
                </div>
                </div>

                 <div class="form-group ">
                  <label for="service_name" class="col-md-2">Order ID</label>
                  <div class="col-md-6">
                    <input type="text" class="form-control"  name="order_id" value = "<?php if (isset($data3['order_id'])) {echo $data3['order_id']; }?>"  >
                   
                  </div>
                </div>
                

                <div class="form-group <?php if( form_error('service_name') ) { echo 'has-error';} ?>">
                  <label for="service_name" class="col-md-2">Service Name</label>
                  <div class="col-md-6">
                    <input type="text" class="form-control"  name="service_name" value = "<?php if (isset($data3['services'])) {echo $data3['services']; }?>"  disabled>
                    <?php echo form_error('service_name'); ?>
                  </div>
                </div>
                
                <div class="form-group <?php if( form_error('quantity') ) { echo 'has-error';} ?>">
                  <label for="quantity" class="col-md-2">No. of Appliances</label>
                  <div class="col-md-6">
                    <input type="text" class="form-control"  name="quantity" value = "<?php if (isset($data1['quantity'])) {echo $data1['quantity']; }?>"  disabled>
                    <?php echo form_error('quantity'); ?>
                  </div>
                </div>

              <div class="form-group <?php if (form_error('appliance_brand')) { echo 'has-error';} ?>">
                <label for="appliance_brand" class="col-md-2">Brand</label>
                <div class="col-md-6">
                    <select type="text" class="form-control"  id="appliance_brand" name="appliance_brand" value = "<?php echo set_value('appliance_brand'); ?>" required>
                      <?php foreach ($brands as $key => $values) { ?>

                        <option  value=<?= $values['brand_name']; ?>>
                            <?php echo $values['brand_name']; }    ?>
                        </option>

                      <?php echo form_error('appliance_brand'); ?>

                    </select>
                </div>
              </div>
              
                <div class="form-group <?php
                if (form_error('ProductType')) {
                    echo 'has-error';
                } ?>">
                    <label for="ProductType" class="col-md-2">Booking Desctiption</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control" name="ProductType" value="<?php if (isset($description)) { if(!empty($description)){echo $description[0]['description']; } } ?>" >
                        <?php echo form_error('ProductType'); ?>
                    </div>
                </div>              

              <div class="form-group <?php if (form_error('appliance_category')) { echo 'has-error';} ?>">
                <label for="appliance_category" class="col-md-2">Category</label>
                <div class="col-md-6">
                    <select type="text" class="form-control"  id="appliance_category" name="appliance_category" value = "<?php echo set_value('appliance_category'); ?>" onChange="getCapacityForCategory(this.value);" required>
                      <?php foreach ($categories as $key => $values) { ?>

                        <option>
                            <?php echo $values['category']; }    ?>
                        </option>

                      <?php echo form_error('appliance_category'); ?>

                    </select>
                </div>
              </div>

              <div class="form-group <?php if (form_error('appliance_capacity')) { echo 'has-error';} ?>">
                <label for="appliance_capacity" class="col-md-2">Capacity</label>
                <div class="col-md-6">
                    <select type="text" class="form-control"  id="appliance_capacity" name="appliance_capacity" value = "<?php echo set_value('appliance_capacity'); ?>" onChange="getPricesForCategoryCapacity();" >
                      <?php foreach ($capacities as $key => $values) { ?>

                        <option>
                            <?php echo $values['capacity']; }    ?>
                        </option>

                      <?php echo form_error('appliance_capacity'); ?>

                    </select>
                </div>
              </div>  

               <div class="form-group <?php if( form_error('model_number') ) { echo 'has-error';} ?>">
                  <label for="model_number" class="col-md-2">Model</label>
                  <div class="col-md-6">
                    <input type="text" class="form-control"  name="model_number" value = "<?php if (isset($unit_details['model_number'])) {echo $unit_details['model_number']; }?>">
                    <?php echo form_error('model_number'); ?>
                  </div>
                </div>   

                  <div class="form-group ">
                  <label for="model_number" class="col-md-2">Serial Number</label>
                  <div class="col-md-6">
                    <input type="text" class="form-control"  name="serial_number" value = "<?php if (isset($unit_details['serial_number'])) {echo $unit_details['serial_number']; }?>">
                   
                  </div>
                </div>             
               
                <div class="form-group">
                    <label for="select_service" class="col-md-2">Select Service</label>
                    <div style="width:300px;" class="col-md-1">

                        <table class="table table-striped table-bordered" name="priceList" id="priceList">
                            <tr><th>Service Category</th>
                                <th>Total Charges</th>
                                <th>Selected Services</th>
                            </tr>
                        </table>
                    </div>
                </div>

                    <div class="form-group <?php
                    if (form_error('items_selected')) { echo 'has-error'; } ?>">
                      <div style="width:100px;" class="col-md-6">
                        <input style="width:150px;" type="hidden" class="form-control"  name="items_selected" id="items_selected" value = "<?php echo set_value('items_selected'); ?>" placeholder="Enter Selected Services" >
                            <?php echo form_error('items_selected'); ?>
                      </div>
                    </div>

                <div class="form-group <?php if( form_error('total_price') ) { echo 'has-error';} ?>">
                    <label for="total_price" class="col-md-2">Total Price</label>
                      <div class="col-md-6">
                        <input type="text" class="form-control" id="total_price" name="total_price" value = "<?php if (isset($unit_details['total_price'])) {echo $unit_details['total_price']; }?>" placeholder="Enter total price..">
                    <?php echo form_error('total_price'); ?>
                  </div>
                </div>
               
                <div class="form-group <?php if( form_error('potential_value') ) { echo 'has-error';} ?>">
                    <label for="potential_value" class="col-md-2">Potential Value</label>
                      <div class="col-md-6">
                        <input type="text" class="form-control" id="potential_value" name="potential_value" value = "<?php if (isset($data1['potential_value'])) {echo $data1['potential_value']; }?>" placeholder="Enter potential value..">
                    <?php echo form_error('potential_value'); ?>
                  </div>
                </div>

                <div class="form-group <?php if( form_error('purchase_year') ) { echo 'has-error';} ?>">
                  <label for="purchase_year" class="col-md-2">Purchase Year</label>
                  <div class="col-md-6">
                    <input type="text" class="form-control"  name="purchase_year" value = "<?php if (isset($unit_details['purchase_year'])) {echo $unit_details['purchase_year']; }?>">
                    <?php echo form_error('purchase_year'); ?>
                  </div>
                </div>

                <div class="form-group <?php if( form_error('appliance_tag') ) { echo 'has-error';} ?>">
                  <label for="appliance_tag" class="col-md-2">Appliance Tag</label>
                  <div class="col-md-6">
                    <input type="text" class="form-control"  name="appliance_tag" value = "<?php if (isset($unit_details['appliance_tag'])) {echo $unit_details['appliance_tag']; }?>">
                    <?php echo form_error('appliance_tag'); ?>
                  </div>
                </div>




                <div class="form-group <?php if( form_error('booking_date') ) { echo 'has-error';} ?>">
                  <label for="reason" class="col-md-2">Booking Date</label>
                  <div class="col-md-6">
                    <input type="date" name="booking_date" id="booking_date" required>
                    <?php echo form_error('booking_date'); ?>
                  </div>
                </div>

                <div class="form-group <?php if( form_error('booking_timeslot') ) { echo 'has-error';} ?>">
                  <label for="reason" class="col-md-2">Booking Timeslot</label>
                  <div class="col-md-6">
                    <select type="text" name="booking_timeslot" id="booking_timeslot" required>
                    
                        <option value="10AM-1PM">10AM-1PM</option>
                        <option value="1PM-4PM">1PM-4PM</option>
                        <option value="4PM-7PM">4PM-7PM</option>
                    </select>
                    
                    <?php echo form_error('booking_timeslot'); ?>
                  </div>
                </div>
                <div class="form-group <?php if (form_error('booking_address')) { echo 'has-error'; } ?>">
                    <label for="booking_address" class="col-md-2">Booking Address</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control" id="booking_address" name="booking_address" 
                        value = "<?php 
                                    if (isset($data3['booking_address']) && ($data3['booking_address'] != "")) {
                                        echo $data3['booking_address']; 
                                    } else {
                                        echo $data3['home_address'];
                    }
                    ?>">
                            <?php echo form_error('booking_address'); ?>
                        </div>
                    </div>

                    <div>
                        <label class="col-md-2"> </label>
                        <div>
                            <p>Enter booking address if its not same as home address.</p></div>
                    </div>

                  <div class="form-group <?php if( form_error('booking_city') ) { echo 'has-error';} ?>">
                  <label for="city" class="col-md-2">Booking City</label>
                  <div class="col-md-6">
                    <input type="text" class="form-control"  id="city" name="booking_city" 
                            value = "<?php if (isset($data3['city']) && ($data3['city'] != "")) {
                               echo $data3['city']; 
                           } elseif(isset($data3['city'])){
                               echo $data3['city'];
                           }?>" placeholder="Enter city">
                            <?php echo form_error('booking_city'); ?>
                  </div>
                </div>

                 <div class="form-group <?php if( form_error('booking_city') ) { echo 'has-error';} ?>">
                  <label for="city" class="col-md-2">Booking State</label>
                  <div class="col-md-6">
                    <input type="text" class="form-control"  id="state" name="booking_state" 
                            value = "<?php if (isset($data3['state']) && ($data3['state'] != "")) {
                               echo $data3['state']; 
                           } elseif(isset($data3['state'])){
                               echo $data3['state'];
                           }?>" placeholder="Enter state">
                            <?php echo form_error('booking_state'); ?>
                  </div>
                </div>

                <div class="form-group <?php if( form_error('booking_pincode') ) { echo 'has-error';} ?>">
                  <label for="booking_pincode" class="col-md-2">Booking Pincode</label>
                  <div class="col-md-6">
                    <input type="text" class="form-control"  id="booking_pincode" name="booking_pincode" 
                            value = "<?php if (isset($data3['booking_pincode']) && ($data3['booking_pincode'] != "")) {
                               echo $data3['booking_pincode']; 
                           } elseif(isset($data3['pincode'])){
                               echo $data3['pincode'];
                           }?>" placeholder="Enter Area Pin">
                            <?php echo form_error('booking_pincode'); ?>
                  </div>
                </div>

                <div class="form-group <?php if( form_error('query_remarks') ) { echo 'has-error';} ?>">
                  <label for="query_remarks" class="col-md-2">Query Remarks</label>
                  <div class="col-md-6">
                    <textarea class="form-control" id="query_remarks" name="query_remarks"><?php if (isset($data1['query_remarks'])) {echo $data1['query_remarks']; }?></textarea>
                    <?php echo form_error('query_remarks'); ?>
                  </div>
                </div>

                <div class="form-group <?php
                    if (form_error('booking_remarks')) {
                        echo 'has-error';
                    } ?>">
                        <label for="booking_remarks" class="col-md-2">Booking Remarks</label>
                        <div class="col-md-6">
                            <textarea class="form-control" name="booking_remarks"><?php if (isset($data1['booking_remarks'])) {echo $data1['booking_remarks']; }?></textarea>
                            <?php echo form_error('booking_remarks'); ?>
                        </div>
                    </div>

                    <input type="hidden" class="form-control"  id="service_id" name="service_id" value = "<?php echo $booking['service_id']; ?>">
                    <input type="hidden" class="form-control"  id="unit_id" name="unit_id" value = "<?php echo $unit_id; ?>">
                    <input type="hidden" class="form-control"  id="appliance_id" name="appliance_id" value = "<?php echo $appliance_id; ?>">
                    <input type="hidden" class="form-control"  id="user_id" name="user_id" value = "<?php echo $booking['user_id'] ?>">
               
                <div>
                  <center>
                    <input type="submit" value="Edit Booking"  name='sbm' class="btn btn-primary" onclick="return(validate());">
                    <input type="submit" value="Convert to Query"  name='sbm' class="btn btn-success" onclick="return(query_validate());">
                  </center>
                </div>
              </form>
            </div>
        </div>
    </div>
</div>
