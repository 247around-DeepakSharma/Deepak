<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">
                    Reschedule Booking 
                </h1>
                <form class="form-horizontal" action="<?php echo base_url()?>employee/partner/process_reschedule_booking/<?php echo $data[0]['booking_id'] ?>" method="POST" >
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <div class="form-group <?php if( form_error('name') ) { echo 'has-error';} ?>">
                                <label for="name" class="col-md-4">Name</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control"  name="name" value = "<?php if (isset($data[0]['name'])) {echo $data[0]['name']; }?>"  disabled>
                                    <?php echo form_error('name'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="name" class="col-md-4">Current Booking Date</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control"  name="current_booking_date" value = "<?php if (isset($data[0]['booking_date'])) {echo $data[0]['booking_date']; }?>"  disabled>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="reason" class="col-md-4">Current Booking Timeslot</label>
                                <div class="col-md-6">
                                    <input type="text"  class="form-control" name="booking_timeslot" value="<?php if (isset($data[0]['booking_timeslot'])) {echo $data[0]['booking_timeslot']; }?>"  disabled>
                                    <?php echo form_error('booking_timeslot'); ?>
                                </div>
                            </div>
                            <hr style="width:200%;">
                            <div class="form-group  <?php if( form_error('booking_date') ) { echo 'has-error';} ?>">
                                <label for="reason" class="col-md-4"> New Booking Date</label>
                                <div class="col-md-6">
                                    <div class="input-group input-append date">
                                        <input id="booking_date" class="form-control" placeholder="Select Date" name="booking_date" type="text" value = "<?php echo set_value('booking_date'); ?>" required readonly='true' style="background-color:#fff;">
                                        <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                    </div>
                                    <?php echo form_error('booking_date'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="Mobile" class="col-md-4">Mobile</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control"  name="mobile" value = "<?php if (isset($data[0]['booking_primary_contact_no'])) {echo $data[0]['booking_primary_contact_no']; }?>"  disabled>
                                    
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="booking_id" class="col-md-4">Booking ID</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control"  name="booking_id" value = "<?php if (isset($data[0]['booking_id'])) {echo $data[0]['booking_id']; }?>"  disabled>
                                   
                                </div>
                            </div>
                            <div class="form-group ">
                                <label for="services" class="col-md-4">Appliance</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control"  name="services" value = "<?php if (isset($data[0]['services'])) {echo $data[0]['services']; }?>"  disabled>
                                   
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <center>
                            <input type="submit" value="Reschedule Booking" class="btn btn-danger" style="background-color: #2C9D9C;border-color: #2C9D9C;">
                            
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