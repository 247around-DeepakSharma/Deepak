<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Reschedule Booking</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
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
                            <div class="form-group ">
                                <label for="services" class="col-md-4">Appliance</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control"  name="services" value = "<?php if (isset($data[0]['services'])) {echo $data[0]['services']; }?>"  disabled>
                                   
                                </div>
                            </div>
                            <hr style="width:200%;">
                            <div class="form-group  <?php if( form_error('booking_date') ) { echo 'has-error';} ?>">
                                <label for="reason" class="col-md-4"> New Booking Date *</label>
                                <div class="col-md-6">
                                    <div class="input-group input-append date">
                                        <input id="booking_date" class="form-control" placeholder="Select Date" name="booking_date" type="text" value = "<?php echo set_value('booking_date'); ?>" required readonly style="background-color:#fff;">
                                        <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                    </div>
                                    <?php echo form_error('booking_date'); ?>
                                </div>
                            </div>
                            
                            <input type="hidden" class="form-control" id="partner_id" name="partner_id" value = "<?php if (isset($data[0]['partner_id'])) {echo $data[0]['partner_id']; } ?>" >
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
                        </div>
                    </div>
                    <div>
                        <center>
                            <input type="submit" value="Reschedule Booking" class="btn btn-success">
                        </center>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var today = new Date();
    $('#booking_date').daterangepicker({
                autoUpdateInput: false,
                singleDatePicker: true,
                showDropdowns: true,
                minDate:today,
                locale:{
                    format: 'DD-MM-YYYY'
                }
            });
            
    $('#booking_date').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD-MM-YYYY'));
    });

    $('#booking_date').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
</script>