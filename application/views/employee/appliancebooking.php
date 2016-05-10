<script>
  function service()
  {
      var service_name ='';
      $('#priceList .Checkbox1:checked').each(function(){

      service_name+=($(this).attr('name')).toString()+',';

      });
      
      $('#items_selected').val(service_name);
      
    }

</script>

<div id="page-wrapper"> 
   <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">

              <?php if (isset($details)) 
                  {
                    foreach ($details as $key => $data) 
                    $appliance_id = $data['id'];
                    
                  }?>
   
            <h1 class="page-header">
                    Appliance Booking 
            </h1>

            <form class="form-horizontal" action="<?php echo base_url()?>employee/booking/appliancebookingconf/<?php echo $appliance_id ?>" onSubmit="service()" method="POST" >

            <div class="form-group <?php if (form_error('name')) {echo 'has-error'; } ?>">
                <label for="name" class="col-md-2">User Name:</label>
                <div class="col-md-6">
                  <input type="text" class="form-control"  id="name" name="name" value = "<?php echo $user_details[0]['name']; ?>" readonly="readonly">
                  <?php echo form_error('name'); ?>
                </div>
            </div>

            <div class="form-group <?php
                       if (form_error('booking_primary_contact_no')) {
                           echo 'has-error';
                  } ?>">
                      <label for="booking_primary_contact_no" class="col-md-2">Primary Contact Number</label>
                      <div class="col-md-6">
                          <input type="text" class="form-control"  id="booking_primary_contact_no" name="booking_primary_contact_no" value = "<?php echo $user_details[0]['phone_number']; ?>">
                          <?php echo form_error('booking_primary_contact_no'); ?>
                      </div>
                  </div>

                  <div class="form-group <?php if (form_error('booking_alternate_contact_no')) {echo 'has-error'; } ?>">
                <label for="booking_alternate_contact_no" class="col-md-2">Alternate Contact No</label>
                <div class="col-md-6">
                  <input type="text" class="form-control"  id="booking_alternate_contact_no" name="booking_alternate_contact_no" value = "<?php echo set_value('booking_alternate_contact_no'); ?>">
                  <?php echo form_error('booking_alternate_contact_no'); ?>
                </div>
              </div>

              <div class="form-group <?php if (form_error('source_code')) { echo 'has-error';} ?>">
                <label for="source_name" class="col-md-2">Booking Source</label>
                <div class="col-md-6">

                    <select type="text" class="form-control"  id="source_code" name="source_code" value = "<?php echo set_value('source_code'); ?>" required>
                      <?php foreach ($sources as $key => $values) { ?>

                        <option  value=<?= $values->code; ?>>
                            <?php echo $values->source; }    ?>
                        </option>

                      <?php echo form_error('source_code'); ?>

                    </select>
                </div>
              </div>

            <div class="form-group <?php
                if (form_error('appliance_brand')) {
                   echo 'has-error';
                    }?>">
                <label for="appliance_brand" class="col-md-2">Appliance Brand</label>
                <div class="col-md-6">
                  <input type="text" class="form-control"  name="appliance_brand" value = "<?php echo $details[0]['brand']; ?>">
                  <?php echo form_error('appliance_brand'); ?>
                </div>
            </div>    

            <div class="form-group <?php
                if (form_error('appliance_category')) {
                   echo 'has-error';
                    }?>">
                <label for="appliance_category" class="col-md-2">Appliance Category</label>
                <div class="col-md-6">
                  <input type="text" class="form-control"  name="appliance_category" value = "<?php echo $details[0]['category']; ?>" readonly="readonly">
                  <?php echo form_error('appliance_category'); ?>
                </div>
            </div>

            <div class="form-group <?php
                if (form_error('appliance_capacity')) {
                   echo 'has-error';
                    }?>">
                <label for="appliance_capacity" class="col-md-2">Appliance Capacity</label>
                <div class="col-md-6">
                  <input type="text" class="form-control"  name="appliance_capacity" value = "<?php echo $details[0]['capacity']; ?>" readonly="readonly">
                  <?php echo form_error('addapplianceunitdetails'); ?>
                </div>
            </div>

            <div class="form-group <?php
                if (form_error('model_number')) {
                   echo 'has-error';
                    }?>">
                <label for="model_number" class="col-md-2">Model Number</label>
                <div class="col-md-6">
                  <input type="text" class="form-control"  name="model_number" value = "<?php echo $details[0]['model_number']; ?>">
                  <?php echo form_error('model_number'); ?>
                </div>
            </div>

            <div class="form-group <?php if (form_error('appliance_tag')) {echo 'has-error'; } ?>">
                <label for="appliance_tag" class="col-md-2">Appliance Tag</label>
                <div class="col-md-6">
                  <input type="text" class="form-control"  id="appliance_tag" name="appliance_tag" value = "<?php echo $details[0]['tag']; ?>">
                  <?php echo form_error('appliance_tag'); ?>
                </div>
              </div>

            <div class="form-group <?php
                if (form_error('purchase_year')) {
                   echo 'has-error';
                    }?>">
                <label for="purchase_year" class="col-md-2">Purchase Year</label>
                <div class="col-md-6">
                  <input type="text" class="form-control"  name="purchase_year" value = "<?php echo $details[0]['purchase_year']; ?>">
                  <?php echo form_error('purchase_year'); ?>
                </div>
            </div>

              <div class="form-group">
              <label for="purchase_year" class="col-md-2">Select Service</label>
              <div class="col-md-1">
                <table class="table table-striped table-bordered" name="priceList" id="priceList">
                  <tr>
                      <th>Service Category</th>
                      <th>Total Charges</th>
                      <th>Selected Services</th>
                  </tr>
                  <?php foreach($price_details as $prices)
                  { 

                    echo "<tr><td>".$prices['service_category']."</td><td>".$prices['total_charges']."</td><td><input id='Checkbox1' class='Checkbox1' type='checkbox' name='".$prices['service_category']."' value=".$prices['total_charges']."></td><tr>";

                  }
                  ?>
                </table>
              </div>
              </div>

              <div class="form-group <?php if (form_error('total_price')) {echo 'has-error'; } ?>">
                <label for="total_price" class="col-md-2">Total Price</label>
                <div class="col-md-6">
                  <input type="text" class="form-control"  id="total_price" name="total_price" value ="<?php echo set_value('total_price'); ?>" placeholder="Enter Total Price" required>
                  <?php echo form_error('total_price'); ?>
                </div>
              </div>

              <div class="form-group <?php
                    if (form_error('items_selected')) { echo 'has-error'; } ?>">
                      <div style="width:150px;" class="col-md-6">
                        <input style="width:150px;" type="hidden" class="form-control"  name="items_selected" id="items_selected" value = "<?php echo set_value('items_selected'); ?>">
                            <?php echo form_error('items_selected'); ?>
                      </div>
                    </div>

              <div class="form-group <?php
                if (form_error('booking_date')) {
                   echo 'has-error';
                  } ?>">
                  <label id="booking_date" for="booking_date" class="col-md-2">Booking Date</label>
                        
                  <div class="col-md-6">
                    <input type="date" class="form-control"  id="booking_date" name="booking_date" value = "<?php echo set_value('booking_date'); ?>" required>
                            <?php echo form_error('booking_date'); ?>
                  </div>
              </div>

              <div class="form-group <?php
                if (form_error('booking_timeslot')) {
                    echo 'has-error';
                  } ?>">
                  <label id="booking_timeslot1" for="booking_timeslot" class="col-md-2">Booking Timeslot</label>
                    <div class="col-md-6">
                      <select class="form-control" id="booking_timeslot" name="booking_timeslot" value = "<?php echo set_value('booking_timeslot'); ?>">

                        <option>10AM-1PM</option>
                        <option>1PM-4PM</option>
                        <option>4PM-7PM</option>
                      </select>
                      <?php echo form_error('booking_timeslot'); ?>
                    </div>
              </div>

              <div class="form-group <?php
                if (form_error('booking_address')) {
                   echo 'has-error';
                    }
                  ?>">
                <label id="booking_address" for="booking_address" class="col-md-2">Booking Address</label>
                <div class="col-md-6">
                  <input type="text" class="form-control"  name="booking_address" value = "<?php echo $user_details[0]['home_address']; ?>">
                  <?php echo form_error('booking_address'); ?>
                </div>
              </div>

              <div class="form-group <?php
                if (form_error('booking_pincode')) {
                  echo 'has-error';
                  } ?>">
                <label id="booking_pincode" for="booking_pincode" class="col-md-2">Booking Pincode</label>
                <div class="col-md-6">
                  <input type="text" class="form-control"  name="booking_pincode" value = "<?php echo set_value('booking_pincode'); ?>" placeholder="Enter Area Pin" required>
                  <?php echo form_error('booking_pincode'); ?>
                </div>
              </div>


              <div>
                <div class="form-group <?php if( form_error('booking_remarks') ) { echo 'has-error';} ?>">
                <label style="width:200px;" for="booking_remarks" class="col-md-2">Booking Remark:</label>
                <div class="col-md-2">
                <textarea style="height:120px;width:600px;" type="text" class="form-control"  name="booking_remarks" value = "<?php if (isset($data['booking_remarks'])){echo $data['booking_remarks'];}?>"><?php if (isset($data['booking_remarks'])){echo $data['booking_remarks'];}?></textarea>
                  <?php echo form_error('booking_remarks'); ?>
                </div>
              </div>
              </div>

              <div class="form-group <?php if( form_error('booking_remarks') ) { echo 'has-error';} ?>">
              <div class="col-md-10">
                  <input type="hidden" class="form-control"  name="user_id" value = "<?php echo $user_details[0]['user_id']; ?>" >
                  <?php echo form_error('user_id'); ?>
                </div>
                </div>

                <div class="form-group <?php if( form_error('user_email') ) { echo 'has-error';} ?>">
              <div class="col-md-10">
                  <input type="hidden" class="form-control"  name="user_email" value = "<?php echo $user_details[0]['user_email']; ?>">
                  <?php echo form_error('user_email'); ?>
                </div>
                </div>

                <div class="form-group <?php if( form_error('phone_number') ) { echo 'has-error';} ?>">
              <div class="col-md-10">
                  <input type="hidden" class="form-control"  name="phone_number" value = "<?php echo $user_details[0]['phone_number']; ?>">
                  <?php echo form_error('phone_number'); ?>
                </div>
                </div>

                <div class="form-group <?php if( form_error('name') ) { echo 'has-error';} ?>">
              <div class="col-md-10">
                  <input type="hidden" class="form-control"  name="name" value = "<?php echo $user_details[0]['name']; ?>">
                  <?php echo form_error('name'); ?>
                </div>
                </div>

                <div class="form-group <?php if( form_error('service_id') ) { echo 'has-error';} ?>">
              <div class="col-md-10">
                  <input type="hidden" class="form-control"  name="service_id" value = "<?php echo $details[0]['service_id']; ?>">
                  <?php echo form_error('service_id'); ?>
                </div>
                </div>

                <div>
                  <center>
                    <input type="submit" value="Save Booking" class="btn btn-danger">
                    <?php echo "<a id='edit' class='btn btn-small btn-primary' href=".base_url()."employee/booking/viewcompletedbooking>Cancel</a>";?>
                    
                  </center>
                </div>

              </form>
            </div>
        </div>
    </div>
</div>