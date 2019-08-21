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
                                    <th>Status</th>
                                    <th style="width:250px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sn = 1; foreach ($model_details as $value) { ?>
                                <tr>
                                    <td><?php echo $sn; ?></td>
                                    <td><?php echo $value['services']; ?></td>
                                    <td><?php echo $value['model_number']; ?></td>
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
                        columns: [ 0, 1, 2]
                    },
                    title: 'model_used_in_part_'+part_name+time
                },
            ],
    });
    
    
        
    
    function change_model_status(is_active,model_mapping_id,model_number){
            if(model_mapping_id!=''){
                var button_content = '' ; 
                var active_status = ''
                if(is_active == 1){
                    status = '0';
                   button_content = 'Activate';
                }
                if(is_active == '0'){
                   status = '1';
                   button_content = 'Deactivate';
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
                                window.location.reload();
                           }     
                    }
                    }); 
                }
            
        }
        
    }
</script>