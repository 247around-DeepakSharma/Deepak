<style>
    .select2-container{
        width: 100%!important;
    }
    .select2-container .select2-selection--single{
        height: 35px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered{
        line-height: 33px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow{
        height: 31px;
    }
    #inventory_part_and_model_mapping_table_filter{
        text-align: right;
    }
    .x_title span {
        color: #333;
    }
    #errmsg
{
color: red;
font-weight:900;
}
</style>
<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0 40px;">
            
            <?php if(!empty($model_details)) { ?>
            <h4 style="text-align: center;color: green;" id="success_message"></h4>
            <div class="x_panel">
                <div class="x_title">
                    <h3>Model Used In Part <span id="part_name"><strong><?php echo array_unique(array_column($model_details, 'part_number'))[0] ;?></strong></span></h3>
                    <hr>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="clearfix"></div>
                    <hr>
                    <div class="inventory_part_and_model_mapping">
                        <table id="inventory_part_and_model_mapping_table" class="table table-bordered table-responsive">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Appliance</th>
                                    <th>Model Number</th>
                                    <th>Part Number</th>
                                    <th>Max Quantity</th>
                                    <th>BOM Status</th>
                                    <th style="width:250px;">BOM Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sn = 1; foreach ($model_details as $value) { ?>
                                <tr>
                                    <td><?php echo $sn; ?></td>
                                    <td><?php echo $value['services']; ?></td>
                                    <td><?php echo $value['model_number']; ?></td>
                                    <td><?php echo $value['part_number']; ?></td>
                                    <td><input type="text" class="form-control max_qty" id="btn_max_text<?php echo $value['id']; ?>" name="max_quantity" value="<?php echo $value['max_quantity']; ?>" placeholder="Enter Max Quantity"><button class="updatemax_qty btn btn-success" data-id="<?php echo $value['id']; ?>">Update</button> &nbsp;<span id="errmsg"></span> </td>
                                    <td><?php if($value['active'] == 1 ){ echo 'Active'; } else { echo ' Inactive'; } ?></td>

                                    <td><?php 
                                    if($value['active'] == 1){ ?>
                                       <button type="button" class="btn" onclick="change_model_status(<?php echo $value['active']; ?>,<?php echo $value['id']; ?>,'<?php echo $value['model_number']; ?>');" style="background-color:#d9534f; border-color: #fff; color: #fff;">Deactivate</button> 
                                    <?php }else{ ?>
                                       <button type="button" class="btn" onclick="change_model_status(<?php echo $value['active']; ?>,<?php echo $value['id']; ?>,'<?php echo $value['model_number']; ?>');" style="background-color: #337ab7; border-color: #fff; color: #fff;width: 95px;">Activate</button> 
                                    <?php } ?></td>                                    
                                </tr>
                                <?php $sn++;} ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php }else { ?> 
            <div class="alert alert-danger text-center">
                <p>No Details Found</p>
            </div>
            <?php } ?>
        </div>

        <!--Modal start-->
        <div id="modal_data" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-body">
                        <div id="open_model"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal end -->
    </div>
</div>
<script>
    var time = moment().format('D-MMM-YYYY-H-i-s');
    var part_name = $('#part_name').text();
    inventory_part_and_model_mapping = $('#inventory_part_and_model_mapping_table').DataTable({
        "dom": 'lBfrtip',
        "buttons": [
                {
                    extend: 'excel',
                    text: 'Export',
                    exportOptions: {
                        columns: [ 0, 1, 2,3,4]
                    },
                    title: 'bom_mapping_list_'+part_name+time
                },
            ],
    });
    
    
  //$(".updatemax_qty").keyup(function (e) {
    $(document).ready(function () {
    $('body').on('keypress','.max_qty', function (e) {
     //if the letter is not digit then display error and don't type anything
     if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        //display error message
        $("#errmsg").html("Digits Only and Greater than 0").show();
               return false;
    }else{
       $("#errmsg").html("Digits Only and Greater than 0").hide(); 
    }
   });

 });
        
    
    function change_model_status(is_active,model_mapping_id,model_number){
            if(model_mapping_id!=''){
                var button_content = '' ; 
                var active_status = ''
                if(is_active == 1){
                    status = '0';
                   button_content = 'Deactivate';
                }
                if(is_active == '0'){
                   status = '1';
                   button_content = 'Activate';
                }
            
                if(confirm("Are you sure you want to "+button_content+" ?")){
                    $.ajax({
                     method:'POST',            
                     url:'<?php echo base_url(); ?>employee/inventory/upate_inventory_model_mapping',
                     dataType: "json",
                     data: {model_mapping_id:model_mapping_id,status:status},
                     success:function(response){
                           if(response.status == true){
                               $("#success_message").html('Model Number ('+model_number+') Successfully '+button_content+' !');
                               setTimeout(function(){
                                  window.location.reload();  
                               },1500);
                               
                           }     
                    }
                    }); 
                }
            
        }
        
    }
/*  UPdate Click Dynamically Binding */
$('body').on('click', '.updatemax_qty', function() {
    // do something
var id = $(this).attr("data-id");
var max_qty = $("#btn_max_text"+id).val(); //btn_max_text59925953
if(max_qty==0){
// Show and hide error msg in 0 value
$("#errmsg").html("Digits Only and Greater than 0").show(); 
}else{
// Show and hide error msg in 0 value 
$("#errmsg").html("Digits Only and Greater than 0").hide(); 
       if(confirm("Are you sure you want to update max quantity ?")){
                    $.ajax({
                     method:'POST',            
                     url:'<?php echo base_url(); ?>employee/inventory/upate_inventory_model_mapping_max_qty',
                     dataType: "json",
                     data: {model_mapping_id:id,max_qty:max_qty},
                     success:function(response){
                           if(response.status == true){
                               $("#success_message").html('Max Quantity Updated Successfully!');
                               setTimeout(function(){
                                  window.location.reload();  
                               },1500);
                               
                           }     
                    }
                    }); 
    } 
} 

});





    
</script>