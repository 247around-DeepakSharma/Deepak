<form id="form-change-consumption">
    <input type="hidden" name="spare_id" value="<?php echo $spare_id; ?>"> 
    <input type="hidden" name="change" value="1"> 
<div class="row">
    <div class="col-md-3">
        <label> Reason<span style="color:red;">*</span></label>
    </div>
    <div class="col-md-9">
        <div class="form-group">
            <select style="width:100%;" name="spare_consumption_status[<?php echo $spare_part_detail['id']; ?>]" class="spare_consumption_status" id="spare_consumption_status_<?php echo $spare_part_detail['id']; ?>">
                <option value="" selected disabled>Select Reason</option>
                <?php foreach($spare_consumed_status as $k => $status) { ?>
                    <option value="<?php echo $status['id']; ?>" data-shipped_inventory_id="<?php echo $spare_part_detail['shipped_inventory_id']; ?>" data-tag="<?php echo $status['tag']; ?>" data-part_number="<?php echo $spare_part_detail['part_number']; ?>" data-spare_id="<?php echo $spare_part_detail['id']; ?>"
                    <?php if(isset($spare_part_detail)){
                        if($spare_part_detail['consumed_part_status_id'] == $status['id']){
                           echo "selected"; 
                        }
                    } ?>
                    ><?php echo $status['consumed_status']; ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <label> Remarks</label>
    </div>
    <div class="col-md-9">
        <div class="form-group">
            <textarea class="form-control" rows="4" placeholder="Enter remarks" name="change_consumption_remarks" id="change_consumption_remarks"><?php if(!empty($spare_part_detail['consumption_remarks'])) { echo $spare_part_detail['consumption_remarks']; } ?></textarea>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <input type="submit" name="change_consumption_btn" id="change_consumption_btn" value="Save" class="btn btn-primary">
    </div>
</div>
</form>

<script>
    $(".spare_consumption_status").select2();
    $(document).ready(function(e){
        // Submit form data via Ajax
        $("#form-change-consumption").on('submit', function(e){
            e.preventDefault();
            
            var reason_id = $('#spare_consumption_status').val();
            
            var remarks = $('#change_consumption_remarks').val();
            if(remarks == '' || remarks == null) {
                alert('Please enter remarks.');
                return false;
            }

            $('#change_consumption_btn').attr("disabled", true);
            $('#change_consumption_btn').val("Please wait...");

            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/service_centers/change_consumption_by_sf',
                data: new FormData(this),
         //       dataType: 'json',
                contentType: false,
                cache: false,
                processData:false,
                beforeSend: function(){
                    $('#change_consumption_btn').attr("disabled","disabled");
                    $('#form-change-consumption').css("opacity",".5");
                },
                success: function(response){ 
                    alert('Part consumption has been changed successfully.');
                    $('#ChangeConsumptionModal').modal('hide');
                    $('#change_consumption_spare_model').val('');
                    location.reload();
                }
            });
        });
    });


</script>