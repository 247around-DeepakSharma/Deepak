<script type="text/javascript">
  $(document).ready(function () {
  //called when key is pressed in textbox
  $("#service_charge").keypress(function (e) {
     //if the letter is not digit then display error and don't type anything
     if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        //display error message
        $("#errmsg").html("Digits Only").show().fadeOut("slow");
               return false;
    }
   });
});

  $(document).ready(function () {
  //called when key is pressed in textbox
  $("#additional_service_charge").keypress(function (e) {
     //if the letter is not digit then display error and don't type anything
     if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        //display error message
        $("#errmsg1").html("Digits Only").show().fadeOut("slow");
               return false;
    }
   });
});

   $(document).ready(function () {
  //called when key is pressed in textbox
  $("#parts_cost").keypress(function (e) {
     //if the letter is not digit then display error and don't type anything
     if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        //display error message
        $("#errmsg2").html("Digits Only").show().fadeOut("slow");
               return false;
    }
   });
});



</script>

<style type="text/css">
#errmsg
{
color: red;
}
#errmsg1
{
color: red;
}
#errmsg2
{
color: red;
}
</style>
<script>
    function total_amount_check()
    {
        var servicecharge = $('#service_charge').val();
        var additionalservicecharge = $('#additional_service_charge').val();
        var partscost = $('#parts_cost').val();
        if(servicecharge == 0 && additionalservicecharge == 0 && partscost == 0)
        {
            alert("Service Charge, Additional Service Charge & Parts Cost are filled as 0. Is it ok! ");
        }
    }
</script>
<div id="page-wrapper"> 
   <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">

              <?php if (isset($data)) 
                  {
                    foreach ($data as $key => $data) 
                    $booking = $data['booking_id'];
                    
                  }?>

              <?php if (isset($data1)) 
              {
                foreach ($data1 as $key => $data1) 
                $booking1 = $data1['booking_id'];
                    
              }?>

              <?php 
                if (isset($query2)) 
                {
                  $brand  ="";
                  $category="";
                  $capacity="";
                  
                  for($i=0; $i<$data['quantity']; $i++)
                  {
                    $brand .=$query2[$i]['appliance_brand'].",";
                    $category .=$query2[$i]['appliance_category'].",";
                    $capacity .=$query2[$i]['appliance_capacity'].",";

                  }
                } 
                ?>

              <h1 class="page-header">
                    Edit Completed Booking 
              </h1>

              <form class="form-horizontal" action="<?php echo base_url()?>employee/booking/process_edit_completed_booking_form/<?php echo $booking ?>" method="POST" >

                <div class="form-group <?php if( form_error('name') ) { echo 'has-error';} ?>">
                  <label for="name" class="col-md-2">User Name</label>
                  <div class="col-md-6">
                    <input type="text" class="form-control"  name="name" value = "<?php if (isset($data1['name'])) {echo $data1['name']; }?>"  disabled>
                    <?php echo form_error('name'); ?>
                  </div>
                </div>

                <div class="form-group <?php if( form_error('phone_number') ) { echo 'has-error';} ?>">
                  <label for="name" class="col-md-2">Phone No.</label>
                  <div class="col-md-6">
                    <input type="text" class="form-control"  name="booking_date" value = "<?php if (isset($data1['phone_number'])) {echo $data1['phone_number']; }?>"  disabled>
                    <?php echo form_error('phone_number'); ?>
                  </div>
                </div>

                <div class="form-group <?php if( form_error('booking_id') ) { echo 'has-error';} ?>">
                  <label for="booking_id" class="col-md-2">Booking ID</label>
                  <div class="col-md-6">
                    <input type="text" class="form-control"  name="booking_id" value = "<?php if (isset($data['booking_id'])) {echo $data['booking_id']; }?>"  disabled>
                    <?php echo form_error('booking_id'); ?>
                  </div>
                </div>

                <div class="form-group <?php if( form_error('services') ) { echo 'has-error';} ?>">
                  <label for="services" class="col-md-2">Service Name</label>
                  <div class="col-md-6">
                    <input type="text" class="form-control"  name="booking_date" value = "<?php if (isset($data1['services'])) {echo $data1['services']; }?>"  disabled>
                    <?php echo form_error('services'); ?>
                  </div>
                </div>

                <div class="form-group <?php if( form_error('appliance_brand') ) { echo 'has-error';} ?>">
                        <label for="appliance_brand" class="col-md-2">Brand</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control"  name="appliance_brand" value = "<?php echo $brand;?>"  disabled>
                            <?php echo form_error('appliance_brand'); ?>
                        </div>
                    </div>

                    <div class="form-group <?php if( form_error('appliance_category') ) { echo 'has-error';} ?>">
                        <label for="appliance_category" class="col-md-2">Category</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control"  name="appliance_category" value = "<?php echo $category;?>"  disabled>
                            <?php echo form_error('appliance_category'); ?>
                        </div>
                    </div>

                    <div class="form-group <?php if( form_error('appliance_capacity') ) { echo 'has-error';} ?>">
                        <label for="capacity" class="col-md-2">Capacity</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control"  name="appliance_capacity" value = "<?php echo $capacity;?>"  disabled>
                            <?php echo form_error('appliance_capacity'); ?>
                        </div>
                    </div>

                <div class="form-group <?php if( form_error('amount_due') ) { echo 'has-error';} ?>">
                        <label for="amount_due" class="col-md-2">Amount Due</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control"  name="amount_due" value = "<?php if (isset($data['amount_due'])) {echo $data['amount_due']; }?>"  disabled>    
                            <?php echo form_error('amount_due'); ?>
                        </div>
                    </div>

                <div class="form-group <?php if (form_error('service_charge')) {echo 'has-error'; } ?>">
                <label for="service_charge" class="col-md-2">Service Charge</label>
                <div class="col-md-2">
                  <input type="text" class="form-control"  id="service_charge" name="service_charge" value = "<?php if (isset($data['service_charge'])) {echo $data['service_charge']; }?>">&nbsp;<span id="errmsg"></span>
                  <?php echo form_error('service_charge'); ?>
                </div>
                <label for="service_charge" class="col-md-2">Collected By</label>
                  <div class="col-md-4">
                    <input type="radio" id="vendor" name="service_charge_collected_by" value="Vendor" <?php echo ($data['service_charge_collected_by']=='Vendor')?'checked':'' ?> required>Vendor &nbsp;
                    <input type="radio" id="around" name="service_charge_collected_by" value="Around" <?php echo ($data['service_charge_collected_by']=='Around')?'checked':'' ?> required>Around &nbsp;
                    <input type="radio" id="snapdeal" name="service_charge_collected_by" value="Snapdeal" <?php echo ($data['service_charge_collected_by']=='Snapdeal')?'checked':'' ?> required>Snapdeal &nbsp;
                  </div>
              </div>

              <div class="form-group <?php if (form_error('additional_service_charge')) {echo 'has-error'; } ?>">
                <label for="additional_service_charge" class="col-md-2">Additional Service Charge</label>
                <div class="col-md-2">
                  <input type="text" class="form-control"  id="additional_service_charge" name="additional_service_charge" value = "<?php if (isset($data['additional_service_charge'])) {echo $data['additional_service_charge']; }?>">&nbsp;<span id="errmsg1"></span>
                  <?php echo form_error('additional_service_charge'); ?>
                </div>
                <label for="service_charge" class="col-md-2">Collected By</label>
                  <div class="col-md-4">
                    <input type="radio" id="vendor" name="additional_service_charge_collected_by" value="Vendor" <?php echo ($data['additional_service_charge_collected_by']=='Vendor')?'checked':'' ?> required>Vendor &nbsp;
                    <input type="radio" id="around" name="additional_service_charge_collected_by" value="Around" <?php echo ($data['additional_service_charge_collected_by']=='Around')?'checked':'' ?> required>Around &nbsp;
                    <input type="radio" id="snapdeal" name="additional_service_charge_collected_by" value="Snapdeal" <?php echo ($data['additional_service_charge_collected_by']=='Snapdeal')?'checked':'' ?> required>Snapdeal &nbsp;
                  </div>
              </div>

              <div class="form-group <?php if (form_error('parts_cost')) {echo 'has-error'; } ?>">
                <label for="parts_cost" class="col-md-2">Parts Cost</label>
                <div class="col-md-2">
                  <input type="text" class="form-control"  id="parts_cost" name="parts_cost" value = "<?php if (isset($data['parts_cost'])) {echo $data['parts_cost']; } ?>">&nbsp;<span id="errmsg2"></span>
                  <?php echo form_error('parts_cost'); ?>
                </div>
                <label for="service_charge" class="col-md-2">Collected By</label>
                  <div class="col-md-4">
                    <input type="radio" id="vendor" name="parts_cost_collected_by" value="Vendor" <?php echo ($data['parts_cost_collected_by']=='Vendor')?'checked':'' ?> required>Vendor &nbsp;
                    <input type="radio" id="around" name="parts_cost_collected_by" value="Around" <?php echo ($data['parts_cost_collected_by']=='Around')?'checked':'' ?> required>Around &nbsp;
                    <input type="radio" id="snapdeal" name="parts_cost_collected_by" value="Snapdeal" <?php echo ($data['parts_cost_collected_by']=='Snapdeal')?'checked':'' ?> required>Snapdeal &nbsp;
                  </div>
              </div>

              <div class="form-group <?php if (form_error('amount_paid')) {echo 'has-error'; } ?>">
                <label for="amount_paid" class="col-md-2">Amount Paid</label>
                <div class="col-md-6">
                  <input type="text" class="form-control"  id="amount_paid" name="amount_paid" value = "<?php if (isset($data['amount_paid'])) {echo $data['amount_paid']; }?>" disabled>
                  <?php echo form_error('amount_paid'); ?>
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

                <div>
                <div class="form-group <?php if( form_error('closing_remarks') ) { echo 'has-error';} ?>">
                <label style="width:200px;" for="query_remarks" class="col-md-2">Closing Remark:</label>
                <div class="col-md-2">
                <textarea style="height:120px;width:600px;" type="text" class="form-control"  name="closing_remarks" value = "<?php if (isset($data['closing_remarks'])){echo $data['closing_remarks'];}?>"><?php if (isset($data['closing_remarks'])){echo $data['closing_remarks'];}?></textarea>
                  <?php echo form_error('closing_remarks'); ?>
                </div>
              </div>
              </div>

                <div>
                  <center>
                    <input type="submit" value="Save" class="btn btn-danger" onclick="total_amount_check()">
                    <?php echo "<a id='edit' class='btn btn-small btn-primary' href=".base_url()."employee/booking/viewcompletedbooking>Cancel</a>";?>
                    
                  </center>
                </div>

              </form>
            </div>
        </div>
    </div>
</div>


