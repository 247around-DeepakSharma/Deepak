<form method="post" action="<?php echo base_url(); ?>service_center/reject_defective_part/<?php echo $spare_id; ?>/<?php echo $booking_id; ?>/<?php echo urlencode(base64_encode($booking_details['partner_id'])); ?>">
    <div class="row form-group"> 
        <div class="col-md-3"> 
            <label>Reject  Reason&nbsp;<span style="color:red;">*</span></label>
        </div>        
        <div class="col-md-9"> 
            <select style="width:100%;" name="reject_reason[<?php echo $spare_part_detail['id']; ?>]" class="reject_reason" id="reject_reason_<?php echo $spare_part_detail['id']; ?>">
                <option value="" selected disabled>Select Reason</option>
                <?php $description_no = 1; foreach ($internal_status as $value) { ?>
                    <option value="<?php echo urlencode(base64_encode($value->status)); ?>" data-spare_id="<?php echo $spare_part_detail['id']; ?>"
                    ><?php echo $value->status; ?></option>
                <?php $description_no++; } ?>
            </select>
        </div>        
    </div>    

    <div class="row form-group"> 
        <div class="col-md-3"> 
            <label>Remarks&nbsp;<span style="color:red;">*</span></label>
        </div>        
        <div class="col-md-9"> 
            <textarea class="form-control" rows="4" name="remarks" id="reject-remarks"></textarea>
        </div>        
    </div>    
    
    <input type="submit" name="reject-part" class="btn btn-primary reject-part" value="Save" class="btn btn-primary">
</form>

<script>
    $(".reject_reason").select2();
    
    $(document).on('click',".reject-part", function() {
        if($('.reject_reason').val() == '' || $('.reject_reason').val() == null) {
            alert('Please select reject reason.');
            return false;
        }
        
        if($('#reject-remarks').val() == '' || $('#reject-remarks').val() == null) {
            alert('Please enter remarks.');
            return false;
        }

        return true;
    });
</script>