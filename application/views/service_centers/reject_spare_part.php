<form id="reject-defective-form" method="post" enctype="multipart/form-data" action="<?php echo base_url(); ?>service_center/reject_defective_part/<?php echo $spare_id; ?>/<?php echo $booking_id; ?>/<?php echo urlencode(base64_encode($booking_details['partner_id'])); ?>">
   <center><img id="reject_loader_gif" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center>
    <div class="row form-group"> 
        <div class="col-md-2"> 
            <label>Reject  Reason&nbsp;<span style="color:red;">*</span></label>
        </div>        
        <div class="col-md-4"> 
            <select style="width:100%;" name="reject_reason[<?php echo $spare_part_detail['id']; ?>]" class="reject_reason" id="reject_reason_<?php echo $spare_part_detail['id']; ?>">
                <option value="" selected disabled>Select Reason</option>
                <?php $description_no = 1;
                foreach ($internal_status as $value) { ?>
                    <option value="<?php echo urlencode(base64_encode($value->status)); ?>" data-spare_id="<?php echo $spare_part_detail['id']; ?>"
                            ><?php echo $value->status; ?></option>
    <?php $description_no++;
} ?>
            </select>
        </div>  
        <div class="col-md-2"> 
            <label>Received Pic &nbsp;<span style="color:red;">*</span></label>
        </div>        
        <div class="col-md-4"> 
            <input type="file" name="rejected_defective_part_pic_by_wh" id="rejected_defective_part_pic_by_wh">
            <input type="hidden" name="rejected_defective_part_pic_by_wh_exist" id="rejected_defective_part_pic_by_wh_exist">
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
    <div class="row form-group"> 
        <div class="col-md-12" style="text-align: center; padding: 5px;"> 
            <input type="hidden" name="service_center_id" id="service_center_id" value="<?php echo $spare_part_detail['service_center_id']; ?>">
            <input type="submit" id="reject_part" name="reject-part" class="btn btn-primary reject-part" value="Submit">
        </div>
    </div>
</form>

<script>
    $(".reject_reason").select2();
    
    $(document).on('click',".reject-part__", function() {
        
        if($('.reject_reason').val() == '' || $('.reject_reason').val() == null) {
            alert('Please select reject reason.');
            return false;
        }
        
        if($('#reject-remarks').val() == '' || $('#reject-remarks').val() == null) {
            alert('Please enter remarks.');
            return false;
        }

        $.ajax({
            url:"<?php echo base_url(); ?>service_center/reject_defective_part/<?php echo $spare_id; ?>/<?php echo $booking_id; ?>/<?php echo urlencode(base64_encode($booking_details['partner_id'])); ?>",
            method: "POST",
            data: $("#reject-defective-form").serialize()
        }).done(function(data){
            $('#RejectSpareConsumptionModal').modal('hide');
            inventory_spare_table.ajax.reload();
            alert(data);
        });
        
        return false;
    });
    
    /*
     * @Js: It's use post Form data when we rejected defective send by SF
     */   
    
    $(document).ready(function () {
        $('#reject-defective-form').on('submit',(function(e) {
           
            if($('.reject_reason').val() == '' || $('.reject_reason').val() == null) {
                 alert('Please select reject reason.');
                 return false;
             }
             
            var file = $("#rejected_defective_part_pic_by_wh").val();
            
            if(file == '' || file == null){
                alert('Please upload reject image.');
                return false;
            }
                       
            var allowedFiles = [".gif", ".jpg",".png",".jpeg",".pdf"];
            var fileUpload = $("#rejected_defective_part_pic_by_wh");
            var regex = new RegExp("([a-zA-Z0-9\s_\\.\-:()])+(" + allowedFiles.join('|') + ")$");
            if (!regex.test(fileUpload.val().toLowerCase())) {
                alert("Please upload files having extensions:(" + allowedFiles.join(', ') + ") only.");
                return false;
            }

             if($('#reject-remarks').val() == '' || $('#reject-remarks').val() == null) {
                 alert('Please enter remarks.');
                 return false;
             }
             
            e.preventDefault();
            var formData = new FormData(this);
            $("#reject_loader_gif").css('display','block');
            $("#reject_part").attr('disabled', 'disabled');
            $.ajax({
                type:'POST',
                url: "<?php echo base_url(); ?>service_center/reject_defective_part/<?php echo $spare_id; ?>/<?php echo $booking_id; ?>/<?php echo urlencode(base64_encode($booking_details['partner_id'])); ?>",
                data:formData,
                cache:false,
                contentType: false,
                processData: false,
                success:function(data){
                    alert(data);
                    $("#reject_loader_gif").css('display','none');
                    $("#reject_part").removeAttr("disabled");
                    $('#RejectSpareConsumptionModal').modal('hide');
                    inventory_spare_table.ajax.reload();
                },
                error: function(data){
                    console.log("error");
                    console.log(data);
                }
            });
        }));

    
});
    
    
</script>