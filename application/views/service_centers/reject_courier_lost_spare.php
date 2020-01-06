<form id="form-reject">
    <input type="hidden" name="spare_id" value="<?php echo $spare_id; ?>"> 
    <input type="hidden" name="reject" value="1"> 
<div class="row">
    <div class="col-md-3">
        <label> Upload POD</label>
    </div>
    <div class="col-md-9">
        <div class="form-group">
            <input type="file" name="reject_courier_lost_spare_part_pod" id="reject_courier_lost_spare_part_pod" value=""> 
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <textarea class="form-control" rows="4" placeholder="Enter remarks" name="reject_courier_lost_spare_part_remarks" id="reject_courier_lost_spare_part_remarks"></textarea>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <input type="submit" name="reject_courier_lost_spare_part_btn" id="reject_courier_lost_spare_part_btn" value="Reject" class="btn btn-primary">
    </div>
</div>
</form>

<script>
    
    $(document).ready(function(e){
        // Submit form data via Ajax
        $("#form-reject").on('submit', function(e){
            e.preventDefault();
            
            var remarks = $('#reject_courier_lost_spare_part_remarks').val();
            if(remarks == '' || remarks == null) {
                alert('Please enter remarks.');
                return false;
            }

            $('#reject_courier_lost_spare_part_btn').attr("disabled", true);
            $('#reject_courier_lost_spare_part_btn').val("Please wait...");

            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/spare_parts/reject_courier_lost_spare',
                data: new FormData(this),
         //       dataType: 'json',
                contentType: false,
                cache: false,
                processData:false,
                beforeSend: function(){
                    $('#reject_courier_lost_spare_part_btn').attr("disabled","disabled");
                    $('#form-reject').css("opacity",".5");
                },
                success: function(response){ 
                    alert('Spare part has been rejected successfully.');
                    courier_lost_spare_parts_table.ajax.reload(null, false);
                    $('#RejectCourierLostSparePartModal').modal('hide');
                    $('#reject_courier_lost_spare_part_remarks').val('');
                }
            });
        });
    });


</script>