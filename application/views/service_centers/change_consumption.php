<form id="change-consumption-form" method="post" action="#" enctype="multipart/form-data">
    <input type="hidden" name="wrong_part[<?php echo $spare_part_detail['id']; ?>]" id="wrong_part_<?php echo $spare_part_detail['id']; ?>" value=''>
    <div class="row form-group" style="padding: 10px;"> 
        <div class="col-md-2"> 
            <label>Consumption  Reason &nbsp;<span style="color:red;">*</span></label>
        </div>        
        <div class="col-md-4"> 
            <select style="width:100%;" name="spare_consumption_status[<?php echo $spare_part_detail['id']; ?>]" class="spare_consumption_status" id="spare_consumption_status_<?php echo $spare_part_detail['id']; ?>">
                <option value="" selected disabled>Select Reason</option>
                <?php $description_no = 1; foreach($spare_consumed_status as $k => $status) { ?>
                    <option value="<?php echo $status['id']; ?>" data-shipped_inventory_id="<?php echo $spare_part_detail['shipped_inventory_id']; ?>" data-tag="<?php echo $status['tag']; ?>" data-part_number="<?php echo $spare_part_detail['part_number']; ?>" data-spare_id="<?php echo $spare_part_detail['id']; ?>"
                    <?php if(isset($spare_part_detail)){
                        if($spare_part_detail['consumed_part_status_id'] == $status['id']){
                           echo "selected"; 
                        }
                    } ?>
                    ><?php echo $status['reason_text']; ?></option>
                <?php $description_no++; } ?>
            </select>
        </div>    
        <div class="col-md-2"> 
            <label>Received Defective Pic &nbsp;</label>
        </div>        
        <div class="col-md-4"> 
            <input type="file" name="received_defective_part_pic_by_wh" id="received_defective_part_pic_by_wh">
            <input type="hidden" name="received_defective_part_pic_by_wh_exist" id="received_defective_part_pic_by_wh_exist">
        </div>    
    </div>    

    <div class="row form-group" style="padding: 10px;"> 
        <div class="col-md-2"> 
            <label>Weight</label>
        </div>        
        <div class="col-md-4"> 
            <input type="number" class="form-control" style="width: 100%; display: inline-block;" id="defective_parts_shipped_weight_in_kg" name="defective_parts_shipped_kg" value="" placeholder="Weight"> 
        </div>
         <div class="col-md-2"> 
            <label><strong> in KG</strong> </label>
        </div> 
        <div class="col-md-4">
            <input type="number" class="form-control" style="width: 50%; display: inline-block;" id="defective_parts_shipped_weight_in_gram"   value=""   name="defective_parts_shipped_gram" placeholder="Weight">&nbsp;<strong>in Gram </strong>   
        </div>        
    </div> 
    <div class="row form-group" style="padding: 10px;"> 
        <div class="col-md-3"> 
            <label>Remarks&nbsp;<span style="color:red;">*</span></label>
        </div>        
        <div class="col-md-9"> 
            <textarea class="form-control" rows="4" name="remarks" id="consumption-remarks"></textarea>
        </div>        
    </div>
    <div class="row form-group"> 
        <div class="col-md-12" style="text-align: center; padding: 5px;"> 
        <input type="submit" name="change-consumption" class="btn btn-primary change-consumption" value="Submit" class="btn btn-primary">
        </div>
    </div>
</form>

<!-- Wrong spare parts modal -->
<!--<div id="WrongSparePartsModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" id="wrong_spare_part_model">
         Modal content
        <div class="modal-content" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Wrong Part</h4>
            </div>
            <div class="modal-body" >
            </div>
        </div>
    </div>
</div>-->


<script>
    $(".spare_consumption_status").select2();
    
//    $('.spare_consumption_status').on('change', function() {
//        if($(this).children("option:selected").data('tag') == '<?php echo WRONG_PART_RECEIVED_TAG; ?>') {
//            open_wrong_spare_part_model($(this).children("option:selected").data('spare_id'), '<?php echo $booking_id; ?>', $(this).children("option:selected").data('part_number'), '<?php echo $booking_details['service_id']; ?>', $(this).children("option:selected").data('shipped_inventory_id'));
//        }
//    });
//
//    function open_wrong_spare_part_model(spare_part_detail_id, booking_id, part_name, service_id, shipped_inventory_id = '') {
//        $.ajax({
//            type: 'POST',
//            url: '<?php echo base_url(); ?>employee/service_centers/wrong_spare_part/' + booking_id,
//            data: {spare_part_detail_id:spare_part_detail_id, booking_id:booking_id, part_name:part_name, service_id:service_id, shipped_inventory_id:shipped_inventory_id},
//            success: function (data) {
//                $("#wrong_spare_part_model").children('.modal-content').children('.modal-body').html(data);   
//                $('#WrongSparePartsModal').modal({backdrop: 'static', keyboard: false});
//            }
//        });
//    }
    
    $(document).on('change',"#wrong_part", function() {
        $('#part_number').val($('#wrong_part').children("option:selected").data('part_number'));
    });
    
    /*
     * @Js: It's use post Form data when we received defective send by SF
     */   
    
    $(document).ready(function () {
        $('#change-consumption-form').on('submit',(function(e) {
            
            var weight_in_kg = $("#defective_parts_shipped_weight_in_kg").val();
            var weight_in_gram = $("#defective_parts_shipped_weight_in_gram").val();
            
            if(parseInt(weight_in_kg) < 0){
                $("#defective_parts_shipped_weight_in_kg").val('');
                alert("Please Enter valid Weight in KG.");
                return false;
            }
            
            if(parseInt(weight_in_gram) < 0){
                $("#defective_parts_shipped_weight_in_gram").val('');
                alert("Please Enter valid Weight in Gram.");
                return false;
            }
            
           /*
            if($(".spare_consumption_status").val() == '' || $(".spare_consumption_status").val() == null) {
                e.stopImmediatePropagation(); 
                alert('Please consumption reason.');
                return false;
            }
           

            if($("#received_defective_part_pic_by_wh").val() == '' || $("#received_defective_part_pic_by_wh").val() == null) {
                e.stopImmediatePropagation(); 
                alert('Please choose defective image.');
                return false;
            }
        */

            if($('#consumption-remarks').val() == '' || $('#consumption-remarks').val() == null) {
                e.stopImmediatePropagation(); // to prevent multiple alerts
                alert('Please enter remarks.');
                return false;
            }
            e.preventDefault();
            var formData = new FormData(this);

            $.ajax({
                type:'POST',
                url: "<?php echo base_url(); ?>service_center/acknowledge_received_defective_parts/<?php echo $spare_part_detail['id']; ?>/<?php echo $spare_part_detail['booking_id']; ?>/<?php echo $spare_part_detail['partner_id']; ?>/0",
                data:formData,
                cache:false,
                contentType: false,
                processData: false,
                success:function(data){
                    alert(data);
                    $('#SpareConsumptionModal').modal('hide');
                    inventory_spare_table.ajax.reload();
                },
                error: function(data){
                    console.log("error");
                    console.log(data);
                }
            });
        }));

    $("#ImageBrowse").on("change", function() {
        $("#imageUploadForm").submit();
    });
});


    $("#defective_parts_shipped_weight_in_kg").on({
        "click": function () {
            var weight_kg = $(this).val();
            if (weight_kg.length > 3) {
                $(this).val('');
                return false;
            }

            if (weight_kg == '0' || weight_kg == '00' || weight_kg == '000') {
                $(this).val('');
                return false;
            }
        },
        "keypress": function () {
            var weight_kg = $(this).val();
            if (weight_kg.length > 2) {
                $(this).val('');
                return false;
            }

            if (weight_kg == '0' || weight_kg == '00' || weight_kg == '000') {
                $(this).val('');
                return false;
            }
        },
        "mouseleave": function () {
            var weight_kg = $(this).val();
            if (weight_kg.length > 3) {
                $(this).val('');
                return false;
            }
            if (weight_kg == '0' || weight_kg == '00' || weight_kg == '000') {
                $(this).val('');
                return false;
            }
        },
        "mouseout": function () {
            var weight_kg = $(this).val();
            if (weight_kg.length > 3 || weight_kg < 0 ) {
                $(this).val('');
                return false;
            }
        }
    });

    $("#defective_parts_shipped_weight_in_gram").on({
        "click": function () {
            var weight_kg = $(this).val();
            if (weight_kg.length > 3) {
                $(this).val('');
                return false;
            }

            if (weight_kg == '0' || weight_kg == '00' || weight_kg == '000') {
                $(this).val('');
                return false;
            }

        },
        "keypress": function () {
            var weight_kg = $(this).val();
            if (weight_kg.length > 2) {
                $(this).val('');
                return false;
            }

            if (weight_kg == '0' || weight_kg == '00' || weight_kg == '000') {
                $(this).val('');
                return false;
            }
        },
        "mouseleave": function () {
            var weight_kg = $(this).val();
            if (weight_kg.length > 3) {
                $(this).val('');
                return false;
            }
        },
        "mouseout": function () {
            var weight_kg = $(this).val();
            if (weight_kg.length > 3 || weight_kg < 0 ) {
                $(this).val('');
                return false;
            }
        }
    });
/*
    $(document).on('click',".change-consumption", function(e) {
        if($('#consumption-remarks').val() == '' || $('#consumption-remarks').val() == null) {
            e.stopImmediatePropagation(); // to prevent multiple alerts
            e.preventDefault();
            alert('Please enter remarks.');
            return false;
        }
        
        $.ajax({
            url:"<?php echo base_url(); ?>service_center/acknowledge_received_defective_parts/<?php echo $spare_part_detail['id']; ?>/<?php echo $spare_part_detail['booking_id']; ?>/<?php echo $spare_part_detail['partner_id']; ?>/0",
            method: "POST",
            data: $("#change-consumption-form").serialize()
        }).done(function(data){
            $('#SpareConsumptionModal').modal('hide');
            inventory_spare_table.ajax.reload();
            alert(data);
        });
        
        return false;
    });
    
    */

</script>