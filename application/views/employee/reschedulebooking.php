<div id="page-wrapper"> 
   <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">

              <?php if (isset($data['booking_id'])) 
                  {
                    foreach ($data['booking_id'] as $key => $data) 
                    $booking = $data['booking_id'];
                  }?>

                  <?php if (isset($data1['booking_id'])) {
                    foreach ($data1['booking_id'] as $key => $data1) 
                    $booking1 = $data1['booking_id'];
                    }?>
              <h1 class="page-header">
                    Reschedule Booking 
              </h1>

              <form class="form-horizontal" action="<?php echo base_url()?>employee/booking/process_reschedule_booking_form/<?php echo $booking ?>" method="POST" >

                <div class="col-md-12">
                  <div class="col-md-6">

                    <div class="form-group <?php if( form_error('name') ) { echo 'has-error';} ?>">
                      <label for="name" class="col-md-4">Name</label>
                      <div class="col-md-6">
                        <input type="text" class="form-control"  name="name" value = "<?php if (isset($data1['name'])) {echo $data1['name']; }?>"  disabled>
                        <?php echo form_error('name'); ?>
                      </div>
                    </div>


                    <div class="form-group">
                      <label for="name" class="col-md-4">Current Booking Date</label>
                      <div class="col-md-6">
                        <input type="text" class="form-control"  name="current_booking_date" value = "<?php if (isset($data['booking_date'])) {echo $data['booking_date']; }?>"  disabled>
                        
                      </div>
                    </div>

                    <div class="form-group <?php if( form_error('booking_timeslot') ) { echo 'has-error';} ?>">
                      <label for="reason" class="col-md-4">Current Booking Timeslot</label>
                      <div class="col-md-6">
                        <input type="text"  class="form-control" name="booking_timeslot" value="<?php if (isset($data['booking_timeslot'])) {echo $data['booking_timeslot']; }?>"  disabled>
                        <?php echo form_error('booking_timeslot'); ?>
                      </div>
                    </div>

                    <hr>
                    <div class="form-group <?php if( form_error('booking_date') ) { echo 'has-error';} ?>">
                      <label for="reason" class="col-md-4"> New Booking Date</label>
                      <div class="col-md-6">
                        <div class="input-group input-append date">
                                    <input id="booking_date" class="form-control" placeholder="Select Date" style="z-index: 10000;" name="booking_date" type="text" value = "<?php echo set_value('booking_date'); ?>" required>
                                    <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                        </div>
                            <?php echo form_error('booking_date'); ?>
                      </div>
                    </div>

                    <div class="form-group <?php if( form_error('booking_timeslot') ) { echo 'has-error';} ?>">
                      <label for="reason" class="col-md-4">New Booking Timeslot</label>
                      <div class="col-md-6">
                        <select type="text" name="booking_timeslot" class= "form-control" value="<?php echo set_value('booking_timeslot'); ?>" >
                        <option>Select</option>
                        <option>10AM-1PM</option>
                        <option>1PM-4PM</option>
                        <option>4PM-7PM</option>
                        </select>
                        <?php echo form_error('booking_timeslot'); ?>
                      </div>
                    </div>


                  </div>

                  <div class="col-md-6">

                    <div class="form-group <?php if( form_error('name') ) { echo 'has-error';} ?>">
                      <label for="Mobile" class="col-md-4">Mobile</label>
                      <div class="col-md-6">
                        <input type="text" class="form-control"  name="mobile" value = "<?php if (isset($data['booking_primary_contact_no'])) {echo $data['booking_primary_contact_no']; }?>"  disabled>
                        <?php echo form_error('mobile'); ?>
                      </div>
                    </div>

                    <div class="form-group <?php if( form_error('booking_id') ) { echo 'has-error';} ?>">
                      <label for="booking_id" class="col-md-4">Booking ID</label>
                      <div class="col-md-6">
                        <input type="text" class="form-control"  name="booking_id" value = "<?php if (isset($data['booking_id'])) {echo $data['booking_id']; }?>"  disabled>
                        <?php echo form_error('booking_id'); ?>
                      </div>
                    </div>

                    <div class="form-group <?php if( form_error('services') ) { echo 'has-error';} ?>">
                      <label for="services" class="col-md-4">Appliance</label>
                      <div class="col-md-6">
                        <input type="text" class="form-control"  name="services" value = "<?php if (isset($data1['services'])) {echo $data1['services']; }?>"  disabled>
                        <?php echo form_error('services'); ?>
                      </div>
                    </div>
                  
                   

                  </div>

                </div>
               

                <div>
                  <center>
                    <input type="submit" value="Reschedule" class="btn btn-danger">
                    <?php echo "<a id='edit' class='btn btn-small btn-primary' href=".base_url()."employee/booking/view>Cancel</a>";?>
                    
                  </center>
                </div>

              </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">

   $("#booking_date").datepicker({dateFormat: 'yy-mm-dd', minDate: 0});

</script>