    <div class="row form-group"> 
        <div class="col-md-3"> 
            <label>Consumption  Reason</label>
        </div>        
        <div class="col-md-9"> 
            <select style="width:100%;" name="spare_consumption_status" class="spare_consumption_status" id="spare_consumption_status">
                <option value="" selected disabled>Select Reason</option>
                <?php $description_no = 1; foreach($spare_consumed_status as $k => $status) { ?>
                    <option value="<?php echo $status['id']; ?>" data-tag="<?php echo $status['tag']; ?>" 
                    <?php
                        if(!empty($consumption_status_selected)){
                            if($consumption_status_selected == $status['consumed_status']){
                               echo "selected"; 
                            }
                        } 
                    ?>
                    ><?php echo $status['reason_text']; ?></option>
                <?php $description_no++; } ?>
            </select>
        </div>        
    </div>    

    <div class="row form-group"> 
        <div class="col-md-3"> 
            <label>Remarks&nbsp;<span style="color:red;">*</span></label>
        </div>        
        <div class="col-md-9"> 
            <textarea class="form-control" rows="4" name="remarks" id="multiple-consumption-remarks"></textarea>
        </div>        
    </div>    
    
    <input type="submit" name="change-consumption" class="btn btn-primary change-consumption-multiple" value="Save" class="btn btn-primary">


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
</script>