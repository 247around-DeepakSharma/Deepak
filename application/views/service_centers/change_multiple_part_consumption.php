<div class="row form-group" style="padding: 10px;"> 
    <div class="col-md-2"> 
        <label>Consumption  Reason &nbsp;<span style="color:red;">*</span></label>
    </div>        
    <div class="col-md-4"> 
        <select style="width:100%;" name="spare_consumption_status" class="spare_consumption_status" id="spare_consumption_status">
            <option value="" selected disabled>Select Reason</option>
            <?php $description_no = 1;
            foreach ($spare_consumed_status as $k => $status) { ?>
                <option value="<?php echo $status['id']; ?>" data-tag="<?php echo $status['tag']; ?>" 
                <?php
                if (!empty($consumption_status_selected)) {
                    if ($consumption_status_selected == $status['consumed_status']) {
                        echo "selected";
                    }
                }
                ?>
                        ><?php echo $status['reason_text']; ?></option>
    <?php $description_no++;
} ?>
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
        <textarea class="form-control" rows="4" name="remarks" id="multiple-consumption-remarks"></textarea>
    </div>        
</div>    
<div class="row form-group"> 
    <div class="col-md-12" style="text-align: center; padding: 5px;"> 
        <input type="submit" name="change-consumption" class="btn btn-primary change-consumption-multiple" id="multiple_received" value="Submit" class="btn btn-primary">
    </div>
</div>

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
        }
    });
    $(".spare_consumption_status").select2();
</script>