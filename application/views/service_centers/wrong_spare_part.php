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
        <select name="wrong_part" id="wrong_part" class="form-control" style="width:100%;">
            <option value="" selected disabled>Select Wrong Part</option>
            <?php foreach($parts as $part) { ?>
            <option value="<?php echo $part['inventory_id']; ?>"><?php echo $part['part_name']; ?></option>
            <?php } ?>
        </select>
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


<script>
    function save_details() {
        var spare_id = '<?php echo $spare_part_detail_id; ?>';
        var wrong_part = $('#wrong_part').val();
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
            url : '<?php echo base_url(); ?>employee/service_centers/wrong_spare_part/<?php echo $booking_id; ?>',
            data: {wrong_flag:1, service_id: '<?php echo $service_id; ?>', wrong_part:wrong_part, remarks:remarks, part_name:'<?php echo $part_name; ?>', spare_part_detail_id : spare_id},
        }).success(function(data){
            $('#wrong_part_'+spare_id).val(JSON.stringify(data));
            $('#WrongSparePartsModal').modal('toggle');
        });
    }
</script>