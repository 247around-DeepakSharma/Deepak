
<div id="page-wrapper"> 
   <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">

              <h1 class="page-header">
              <?php echo "Open ". $status. " Booking"; ?>
                   
              </h1>

              <form class="form-horizontal" action="<?php echo base_url()?>employee/booking/process_convert_booking_to_pending_form/<?php echo $booking_id ?>/<?php echo $status; ?>" method="POST" >

                <div class="form-group <?php if( form_error('booking_id') ) { echo 'has-error';} ?>">
                  <label for="booking_id" class="col-md-2">Booking ID</label>
                  <div class="col-md-6">
                    <input type="text" class="form-control"  name="booking_id" value = "<?php echo $booking_id; ?>"  disabled>
                    <?php echo form_error('booking_id'); ?>
                  </div>
                </div>

                <div class="form-group <?php if( form_error('name') ) { echo 'has-error';} ?>">
                  <label for="name" class="col-md-2">User</label>
                  <div class="col-md-6">
                    <input type="text" class="form-control"  name="name" value = "<?php echo $name; ?>"  disabled>
                    <?php echo form_error('name'); ?>
                  </div>
                </div>

                <div class="form-group <?php if( form_error('phone_number') ) { echo 'has-error';} ?>">
                  <label for="phone_number" class="col-md-2">Mobile</label>
                  <div class="col-md-6">
                    <input type="text" class="form-control"  name="phone_number" value = "<?php echo $phone_number; ?>"  disabled>
                    <?php echo form_error('phone_number'); ?>
                  </div>
                </div>


               <div class="form-group <?php if( form_error('booking_date') ) { echo 'has-error';} ?>">
                  <label for="booking_date" class="col-md-2">New Booking Date</label>
                  <div class="col-md-6">
                    <div class="input-group input-append date">
                                    <input id="booking_date" class="form-control" name="booking_date" type="text" value = "<?php echo set_value('booking_date'); ?>" required readonly='true'>
                                    <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                    </div>
                        <?php echo form_error('booking_date'); ?>
                  </div>
                </div>


                <div class="form-group <?php if( form_error('booking_timeslot') ) { echo 'has-error';} ?>">
                  <label for="booking_timeslot" class="col-md-2">New Booking Timeslot</label>
                  <div class="col-md-6">
                    <select type="text" class="form-control" name="booking_timeslot" value="<?php echo set_value('booking_timeslot'); ?>" >
                    <option>Select</option>
                    <option>10AM-1PM</option>
                    <option>1PM-4PM</option>
                    <option>4PM-7PM</option>
                    </select>
                    <?php echo form_error('booking_timeslot'); ?>
                  </div>
                </div>
                <?php if($status =="Completed"){?>
                <div class="form-group <?php if( form_error('complete_pending_reason') ) { echo 'has-error';} ?>">
                  <label for="complete_pending_reason" class="col-md-2">Reason</label>
                  <div class="col-md-6">
                      <textarea class="form-control"  name="complete_pending_reason" ></textarea>
                        <?php echo form_error('complete_pending_reason'); ?>
                  </div>
                </div>
                <?php }else{?>
                  <div class="form-group <?php if( form_error('cancelled_pending_reason') ) { echo 'has-error';} ?>">
                  <label for="cancelled_pending_reason" class="col-md-2">Reason</label>
                  <div class="col-md-6">
                      <textarea class="form-control"  name="cancelled_pending_reason" ></textarea>
                        <?php echo form_error('cancelled_pending_reason'); ?>
                  </div>
                </div>
                <?php }?>

                <div>
                  <center>
                    <input type="submit" value="Open" class="btn btn-danger">
<!--                    <?php echo "<a id='edit' class='btn btn-small btn-primary' href=".base_url()."employee/booking/view>Cancel</a>";?>-->
                    
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
