<script>
    $('#wrong_part').select2({
        placeholder:'Select wrong part'
    });
    
</script>
<div class="row form-group">
    <div class="col-md-3">
        <b>Booking ID</b>
    </div>    
    <div class="col-md-9">
        <input type="text" class="form-control" name="booking_id" value="<?php echo $booking_id; ?>" readonly="readonly">
    </div>    
</div>

<div class="row form-group">
    <div class="col-md-3">
        <b>Select Wrong Part <span style="color:red;">*</span></b>
    </div>    
    <div class="col-md-9">
        <?php if(!empty($shipped_inventory_id)) { ?>
        <select name="wrong_part" id="wrong_part" class="form-control" style="width:100%;">
            <option value="" selected disabled>Select Wrong Part</option>
            <?php foreach($parts as $part) { ?>
            <option value="<?php echo $part['inventory_id']; ?>"><?php echo $part['part_name']; ?></option>
            <?php } ?>
        </select>
        <?php } else { ?>
        <input type="text" name="wrong_part" id="non_inventory_wrong_part" style="width:100%;" class="form-control">
        <?php } ?>
    </div>    
</div>

<div class="row form-group">
    <div class="col-md-3">
        <b>Remarks <span style="color:red;">*</span></b>
    </div>    
    <div class="col-md-9">
        <textarea rows="3" name="remarks" id="remarks" class="form-control"></textarea>
    </div>    
</div>

<div class="form-group">
    <input type="submit" name="update_broken_qty" value="Save" class="btn btn-primary" onclick="save_details()">
</div>

<style>
    .close{
        display:none;
    }
</style>
<script>
    function save_details() {
        var spare_id = '<?php echo $spare_part_detail_id; ?>';
        var shipped_inventory_id = '<?php echo $shipped_inventory_id; ?>';
        if($('#wrong_part').length > 0) {
            var wrong_part = $('#wrong_part').val();
            var wrong_part_name = $('#wrong_part').children("option:selected").text();
        }
        if($('#non_inventory_wrong_part').length > 0) {
            var wrong_part = $('#non_inventory_wrong_part').val();
            var wrong_part_name = wrong_part;
        }
        var remarks = $('#remarks').val();
        if(wrong_part == '' || wrong_part == null) {
            alert('Please select wrong part.');
            return false;
        }
        if(remarks == '') {
            alert('Please enter remarks.');
            return false;
        }

        $.ajax({
            method: 'POST', dataType:'json',
            url : '<?php echo base_url(); ?>employee/booking/wrong_spare_part/<?php echo $booking_id; ?>',
            data: {wrong_flag:1, service_id: '<?php echo $service_id; ?>', wrong_part:wrong_part, wrong_part_name:wrong_part_name, remarks:remarks, part_name:'<?php echo $part_name; ?>', spare_part_detail_id:spare_id, shipped_inventory_id:shipped_inventory_id},
        }).success(function(data){
            $('#wrong_part_'+spare_id).val(JSON.stringify(data));
            $('#WrongSparePartsModal').modal('toggle');
        });
    }
</script>