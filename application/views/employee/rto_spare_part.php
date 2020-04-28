<form id="rto-form">
    <input type="hidden" name="spare_id" value="<?php echo $spare_id; ?>"> 
    <input type="hidden" name="rto" value="1"> 
    <div class="row form-group">
        <div class="col-md-3">
            <label for="rto_file">Upload POD</label>
        </div>
        <div class="col-md-9">
            <input type="file" name="rto_file" id="rto_file" value="">
        </div>
    </div>
    <div class="row form-group">
        <div class="col-md-3">
            <label for="rto_case_spare_part_remarks">Remarks&nbsp;<span style="color:red;">*</span></label>
        </div>
        <div class="col-md-9">
            <textarea name="remarks" class="form-control" id="rto_case_spare_part_remarks" rows="4" placeholder="Enter Remarks"></textarea>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <input type="submit" id="rto_case_spare_part_btn" name="rto-case-part" value="Save" class="btn btn-primary form-control" style="margin-top:2px;">
        </div>
    </div>
</form>

<script>
    
    $(document).ready(function(e){
        // Submit form data via Ajax
        $("#rto-form").on('submit', function(e){
            e.preventDefault();
            
            var file = $('#rto_file').val();
            
            var remarks = $('#rto_case_spare_part_remarks').val();
            if(remarks == '' || remarks == null) {
                alert('Please enter remarks.');
                return false;
            }

            $('#rto_case_spare_part_btn').attr("disabled", true);
            $('#rto_case_spare_part_btn').val("Please wait...");

            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/spare_parts/rto_case_spare',
                data: new FormData(this),
         //       dataType: 'json',
                contentType: false,
                cache: false,
                processData:false,
                beforeSend: function(){
                    $('#rto_case_spare_part_btn').attr("disabled","disabled");
                    $('#rto-form').css("opacity",".5");
                },
                success: function(response){ 
                    if(tab_type == 1) {
                        partner_shipped_part_table.ajax.reload(null, false);
                    }
                    if(tab_type == 2) {
                        sf_received_part_table.ajax.reload(null, false);
                    }
                    if(tab_type == 12) {
                        courier_lost_spare_parts_table.ajax.reload(null, false);  
                    }
                    $('#RtoCaseSparePartModal').modal('hide');
                    $('#rto_case_spare_part_remarks').val('');
                    alert('All spare parts has been cancelled successfully associated with the awb number.');
                }
            });
        });
    });


</script>