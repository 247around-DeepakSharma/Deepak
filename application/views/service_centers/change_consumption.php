<form method="post" action="<?php echo base_url(); ?>service_center/acknowledge_received_defective_parts/<?php echo $spare_part_detail['id']; ?>/<?php echo $spare_part_detail['booking_id']; ?>/<?php echo $spare_part_detail['partner_id']; ?>/0">
    <input type="hidden" name="wrong_part[<?php echo $spare_part_detail['id']; ?>]" id="wrong_part_<?php echo $spare_part_detail['id']; ?>" value=''>

    <div class="row form-group"> 
        <div class="col-md-3"> 
            <label>Consumption  Reason</label>
        </div>        
        <div class="col-md-9"> 
            <select style="width:100%;" name="spare_consumption_status[<?php echo $spare_part_detail['id']; ?>]" class="spare_consumption_status" id="spare_consumption_status_<?php echo $spare_part_detail['id']; ?>">
                <option value="" selected disabled>Select Reason</option>
                <?php $description_no = 1; foreach($spare_consumed_status as $k => $status) { ?>
                    <option value="<?php echo $status['id']; ?>" data-shipped_inventory_id="<?php echo $spare_part_detail['shipped_inventory_id']; ?>" data-tag="<?php echo $status['tag']; ?>" data-part_number="<?php echo $spare_part_detail['part_number']; ?>" data-spare_id="<?php echo $spare_part_detail['id']; ?>"
                    <?php if(isset($spare_part_detail)){
                        if($spare_part_detail['consumed_part_status_id'] == $status['id']){
                           echo "selected"; 
                        }
                    } ?>
                    ><?php echo $status['consumed_status']; ?></option>
                <?php $description_no++; } ?>
            </select>
        </div>        
    </div>    

    <div class="row form-group"> 
        <div class="col-md-3"> 
            <label>Remarks</label>
        </div>        
        <div class="col-md-9"> 
            <textarea class="form-control" rows="4" name="remarks"></textarea>
        </div>        
    </div>    
    
    <input type="submit" name="change-consumption" value="Done" class="btn btn-primary">
</form>

<!-- Wrong spare parts modal -->
<div id="WrongSparePartsModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" id="wrong_spare_part_model">
        <!-- Modal content-->
        <div class="modal-content" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Wrong Part</h4>
            </div>
            <div class="modal-body" >
            </div>
        </div>
    </div>
</div>


<script>
    $(".spare_consumption_status").select2();
    
    $('.spare_consumption_status').on('change', function() {
        if($(this).children("option:selected").data('tag') == '<?php echo WRONG_PART_RECEIVED_TAG; ?>') {
            open_wrong_spare_part_model($(this).children("option:selected").data('spare_id'), '<?php echo $booking_id; ?>', $(this).children("option:selected").data('part_number'), '<?php echo $booking_details['service_id']; ?>', $(this).children("option:selected").data('shipped_inventory_id'));
        }
    });

    function open_wrong_spare_part_model(spare_part_detail_id, booking_id, part_name, service_id, shipped_inventory_id = '') {
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/service_centers/wrong_spare_part/' + booking_id,
            data: {spare_part_detail_id:spare_part_detail_id, booking_id:booking_id, part_name:part_name, service_id:service_id, shipped_inventory_id:shipped_inventory_id},
            success: function (data) {
                $("#wrong_spare_part_model").children('.modal-content').children('.modal-body').html(data);   
                $('#WrongSparePartsModal').modal({backdrop: 'static', keyboard: false});
            }
        });
    }
    
    $(document).on('change',"#wrong_part", function() {
        $('#part_number').val($('#wrong_part').children("option:selected").data('part_number'));
    });
</script>