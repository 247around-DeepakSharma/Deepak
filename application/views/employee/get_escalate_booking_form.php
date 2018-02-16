<style type="text/css">
    .has-error{
        color:red;
    }
</style>
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="panel panel-default" style="margin-top:10px">
            <div class="panel-heading"><center><span style="font-weight: bold; font-size: 120%">Booking Report Form</span></center></div>

            <div class="panel-body">
                <form class="form-horizontal" action="<?php echo base_url() ?>employee/vendor/process_vendor_escalation_form" method="POST" >
                    <div class="form-group">
                        <label for="BookingId" class="col-md-2">Booking ID</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control"  name="booking_id"  value = "<?php echo $booking_id; ?>" placeholder = "Booking Id"  readonly>
                        </div>
                    </div>
                    <div class="form-group ">
                        <label for="VendorId" class="col-md-2">Vendor Name</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control"  name="vendor_name"  value = "<?php if(isset($vendor_details[0]['name'])){echo $vendor_details[0]['name']; }?>"  disabled>
                            <input type="hidden" class="form-control"  name="vendor_id"  value = "<?php if(isset($vendor_details[0]['id'])){echo $vendor_details[0]['id'];} ?>" >
                            <input type="hidden" class="form-control"  name="penalty_active"  value = "<?php if(isset($penalty_active)){echo $penalty_active;} ?>" >
                            <input type="hidden" class="form-control"  name="status"  value = "<?php echo $status; ?>" >
                        </div>
                    </div>
                    <div class="form-group  <?php
                    if (form_error('escalation_reason_id')) {
                        echo 'has-error';
                    }
                    ?>">
                        <label for="Service" class="col-md-2">Reason</label>
                        <div class="col-md-6">
                            <select class=" form-control" name ="escalation_reason_id" >
                                <option selected disabled>----------- Select Reason ------------</option>
                                <?php
                                foreach ($escalation_reason as $reason) {
                                    ?>
                                    <option value = "<?php echo $reason['id'] ?>">
                                        <?php echo $reason['escalation_reason']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                            <?php echo form_error('escalation_reason_id'); ?>
                        </div>
                    </div>

                    <div class="form-group ">
                        <label for="remarks" class="col-md-2">Remarks</label>
                        <div class="col-md-6">
                            <textarea class="form-control"  name="remarks" placeholder="Enter Remarks" rows="3"></textarea>
                        </div>
                    </div>     

                    <div class="form-group ">
                        <input type= "submit"  class=" col-md-3 col-md-offset-4 btn btn-primary btn-md" value ="Save" onclick="this.disabled=true;this.value='Submitting...'; this.form.submit();">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $(".js-example-basic-multiple").select2();
</script>
