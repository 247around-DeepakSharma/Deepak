
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

                <div class="form-group <?php if( form_error('name') ) { echo 'has-error';} ?>">
                  <label for="name" class="col-md-2">User Name</label>
                  <div class="col-md-6">
                    <input type="text" class="form-control"  name="name" value = "<?php if (isset($data1['name'])) {echo $data1['name']; }?>"  disabled>
                    <?php echo form_error('name'); ?>
                  </div>
                </div>

                <div class="form-group <?php if( form_error('booking_date') ) { echo 'has-error';} ?>">
                  <label for="name" class="col-md-2">Current Booking Date</label>
                  <div class="col-md-6">
                    <input type="text" class="form-control"  name="booking_date" value = "<?php if (isset($data['booking_date'])) {echo $data['booking_date']; }?>"  disabled>
                    <?php echo form_error('booking_date'); ?>
                  </div>
                </div>

                <div class="form-group <?php if( form_error('booking_timeslot') ) { echo 'has-error';} ?>">
                  <label for="reason" class="col-md-2">Current Booking Timeslot</label>
                  <div class="col-md-6">
                    <input type="text" name="booking_timeslot" value="<?php if (isset($data['booking_timeslot'])) {echo $data['booking_timeslot']; }?>"  disabled>
                    <?php echo form_error('booking_timeslot'); ?>
                  </div>
                </div>

                <div class="form-group <?php if( form_error('booking_date') ) { echo 'has-error';} ?>">
                  <label for="reason" class="col-md-2"> New Booking Date</label>
                  <div class="col-md-6">
                    <input type="date" name="booking_date" value="<?php echo set_value('booking_date'); ?>" required>
                    <?php echo form_error('booking_date'); ?>
                  </div>
                </div>

                <div class="form-group <?php if( form_error('booking_timeslot') ) { echo 'has-error';} ?>">
                  <label for="reason" class="col-md-2">New Booking Timeslot</label>
                  <div class="col-md-6">
                    <select type="text" name="booking_timeslot" value="<?php echo set_value('booking_timeslot'); ?>" >
                    <option>Select</option>
                    <option>10AM-1PM</option>
                    <option>1PM-4PM</option>
                    <option>4PM-7PM</option>
                    </select>
                    <?php echo form_error('booking_timeslot'); ?>
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
