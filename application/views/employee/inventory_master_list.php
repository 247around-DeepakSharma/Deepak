<style>
    #inventory_master_list_filter{
        text-align: right;
    }
    
    .spinner {
        margin: 0px auto;
        width: 50px;
        height: 50px;
        text-align: center;
        font-size: 10px;
    }

    .spinner > div {
        height: 100%;
        width: 6px;
        display: inline-block;

        -webkit-animation: sk-stretchdelay 1.2s infinite ease-in-out;
        animation: sk-stretchdelay 1.2s infinite ease-in-out;
    }

    .spinner .rect2 {
        -webkit-animation-delay: -1.1s;
        animation-delay: -1.1s;
    }

    .spinner .rect3 {
        -webkit-animation-delay: -1.0s;
        animation-delay: -1.0s;
    }

    .spinner .rect4 {
        -webkit-animation-delay: -0.9s;
        animation-delay: -0.9s;
    }

    .spinner .rect5 {
        -webkit-animation-delay: -0.8s;
        animation-delay: -0.8s;
    }

    @-webkit-keyframes sk-stretchdelay {
        0%, 40%, 100% { -webkit-transform: scaleY(0.4) }  
        20% { -webkit-transform: scaleY(1.0) }
    }

    @keyframes sk-stretchdelay {
        0%, 40%, 100% { 
            transform: scaleY(0.4);
            -webkit-transform: scaleY(0.4);
        }  20% { 
            transform: scaleY(1.0);
            -webkit-transform: scaleY(1.0);
        }
    }
    
    #inventory_master_list_processing{
            position: absolute;
            z-index: 999999;
            width: 100%;
            background: rgba(0,0,0,0.5);
            height: 100%;
            top: 10px;
    }
    
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
</style>
<div id="page-wrapper">
    <div class="row">
        <div class="title">
            <div class="row">
                <div class="col-md-6">
                    <h3>Inventory List</h3>
                </div>
                <div class="col-md-6">
                    <a class="btn btn-success pull-right" style="margin-top: 10px;" id="add_master_list">Add New List</a>
                </div>
            </div>
        </div>
        <hr>
        <div class="success_msg_div" style="display:none;">
            <div class="alert alert-success alert-dismissible" role="alert" style="margin-top:15px;">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong><span id="success_msg"></span></strong>
            </div>
        </div>
        <div class="error_msg_div" style="display:none;">
            <div class="alert alert-danger alert-dismissible" role="alert" style="margin-top:15px;">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong><span id="error_msg"></span></strong>
            </div>
        </div>
        <div class="inventory-table">
            <table class="table table-bordered table-hover table-striped" id="inventory_master_list">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Service</th>
                        <th>Spare Model Number</th>
                        <th>Spare Type</th>
                        <th>Spare Part Name</th>
                        <th>Spare Part Number</th>
                        <th>Spare Serial Number</th>
                        <th>Spare Description</th>
                        <th>Spare Size</th>
                        <th>Price</th>
                        <th>Entity Type</th>
                        <th>Entity Name</th>
                        <th>Edit</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    
    <!--Modal start-->
    <div id="inventory_master_list_data" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal_title_action"> </h4>
                </div>
                <div class="modal-body">

                    <form class="form-horizontal" id="master_list_details">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="service_id">Service</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <select class="form-control" id="service_id" name="service_id"></select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="part_name">Part Name*</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <input type="text" class="form-control" id="part_name" name="part_name" placeholder="Part Name">
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="part_number">Part Number*</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <input type="text" class="form-control" id="part_number" name="part_number" placeholder="Part Number">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="serial_number">Serial Number</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <input type="text" class="form-control" id="serial_number" name="serial_number" placeholder="Serial Number">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="model_number">Model Number*</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <input type="text" class="form-control" id="model_number" name="model_number" placeholder="Model Number">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="size">Size</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <input type="text" class="form-control" id="size"  name="size" placeholder="Size">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="entity_type">Entity Type</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <select class="form-control" id="entity_type" name="entity_type">
                                            <option value="" selected="" disabled="">Select Entity</option>
                                            <option value="<?php echo _247AROUND_EMPLOYEE_STRING; ?>"><?php echo _247AROUND_EMPLOYEE_STRING; ?></option>
                                            <option value="<?php echo _247AROUND_PARTNER_STRING; ?>"><?php echo _247AROUND_PARTNER_STRING; ?></option>
                                            <option value="<?php echo _247AROUND_SF_STRING; ?>"><?php echo _247AROUND_SF_STRING; ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="entity_id">Entity Id</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <select class="form-control" id="entity_id" name="entity_id">
                                            <option value="" selected="" disabled="">Please Select Entity Type First</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="price">Price</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <input type="number" class="form-control" id="price" name="price" placeholder="Price">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="type">Part Type</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <textarea class="form-control" id="type" name="type" placeholder="Type"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="description">Description</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <textarea class="form-control" id="description" name="description" placeholder="description"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <input type="hidden" class="btn btn-success" id="inventory_id" name='inventory_id' value="">
                            <input type="submit" class="btn btn-success" id="master_list_submit_btn" name='submit_type' value="Submit">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            <p class="pull-left text-danger">* These Fields are required</p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal end -->
</div>
<script>
    var inventory_master_list_table;
    var entity_type = '';
    var entity_id = '';
    $(document).ready(function(){
        
        inventory_master_list_table = $('#inventory_master_list').DataTable({
            "processing": true, 
            "serverSide": true,
            "language":{ 
                "processing": "<div class='spinner'>\n\
                                    <div class='rect1' style='background-color:#db3236'></div>\n\
                                    <div class='rect2' style='background-color:#4885ed'></div>\n\
                                    <div class='rect3' style='background-color:#f4c20d'></div>\n\
                                    <div class='rect4' style='background-color:#3cba54'></div>\n\
                                </div>"
            },
            "order": [], 
            "pageLength": 25,
            "ordering": false,
            "ajax": {
                "url": "<?php echo base_url(); ?>employee/inventory/get_inventory_master_list/",
                "type": "POST"
            },
            "deferRender": true       
        });
        
    });
    
    function get_partner(div_to_update){
        $.ajax({
            type:'POST',
            url:'<?php echo base_url();?>employee/partner/get_partner_list',
            success:function(response){
                $('#'+div_to_update).html(response);
                $('#'+div_to_update).select2();
            }
        });
    }
    
    function get_services(){
        $.ajax({
            type:'POST',
            url:'<?php echo base_url();?>employee/booking/get_service_id',
            success:function(response){
                $('#service_id').html(response);
                //$('#service_id').select2();
            }
        });
    }
    
    function get_service_center(div_to_update){
        $.ajax({
            type:'POST',
            url:'<?php echo base_url();?>employee/vendor/get_service_center_details',
            success:function(response){
                $('#'+div_to_update).html(response);
                $('#'+div_to_update).select2();
            }
        });
    }
    
    $('#entity_type').change(function(){
        entity_type = $('#entity_type').val();
        if(entity_type === '<?php echo _247AROUND_PARTNER_STRING; ?>'){
            get_partner('entity_id');
        }else if(entity_type === '<?php echo _247AROUND_EMPLOYEE_STRING; ?>'){
            var entity_id_options = "<option value='<?php echo _247AROUND_DEFAULT_AGENT;?>' selected='' ><?php echo _247AROUND_DEFAULT_AGENT_NAME; ?></option>";
            $('#entity_id').html(entity_id_options);
        }else if(entity_type === '<?php echo _247AROUND_SF_STRING; ?>'){
            get_service_center('entity_id');
        }else{
            var entity_id_options = "<option value='' selected='' >Select</option>";
            $('#entity_id').html(entity_id_options);
        }
    });
    $('#add_master_list').click(function(){
        get_services();
        $("#master_list_details")[0].reset();
        $('#master_list_submit_btn').val('Add');
        $('#modal_title_action').html("Add New Inventory");
        $('#inventory_master_list_data').modal('toggle');
    });
    
    $(document).on("click", "#edit_master_details", function () {
        
        var form_data = $(this).data('id');
        if(form_data.service_id){
            var service_options = "<option value='"+form_data.service_id+"' selected=''>"+form_data.services+"</option>";
            $('#service_id').html(service_options);
        }else{
            get_services();
        }
        
        
        if(form_data.entity_type){
            var entity_type_options = "<option value='"+form_data.entity_type+"' selected='' >"+form_data.entity_type+"</option>";
            $('#entity_type').html(entity_type_options);
        }
        
        if(form_data.entity_id){
            var entity_id_options = "<option value='"+form_data.entity_id+"' selected='' >"+form_data.entity_id+"</option>";
            $('#entity_id').html(entity_id_options);
        }
        
        
        $('#part_name').val(form_data.part_name);
        $('#part_number').val(form_data.part_number);
        $('#serial_number').val(form_data.serial_number);
        $('#model_number').val(form_data.model_number);
        $('#size').val(form_data.size);
        $('#price').val(JSON.parse(form_data.price));
        $('#type').val(form_data.type);
        $('#description').val(form_data.description);
        $('#inventory_id').val(form_data.inventory_id);
        $('#master_list_submit_btn').val('Edit');
        $('#modal_title_action').html("Edit Details");
        $('#inventory_master_list_data').modal('toggle');
           
    });
    
    $("#master_list_submit_btn").click(function(){
        event.preventDefault();
        var arr = {};
        var form_data = $("#master_list_details").serializeArray();
        
        if($('#part_name').val() === ""){
            alert("Please Enter Part Name");
        }else if($('#part_number').val() === ""){
            alert("Please Enter Part Number");
        }else if($('#model_number').val() === ""){
            alert("Please Enter Model Number");
        }else{
            $('#master_list_submit_btn').attr('disabled',true).html('Processing...');
            arr.name = 'submit_type';
            arr.value = $('#master_list_submit_btn').val();
            form_data.push(arr);
            $.ajax({
                type:'POST',
                url:'<?php echo base_url(); ?>employee/inventory/process_inventoy_master_list_data',
                data : form_data,
                success:function(response){
                    $('#inventory_master_list_data').modal('toggle');
                    var data = JSON.parse(response);
                    if(data.response === 'success'){
                        $('.success_msg_div').fadeTo(2000, 500).slideUp(500, function(){$(".success_msg_div").slideUp(1000);});   
                        $('#success_msg').html(data.msg);
                        inventory_master_list_table.ajax.reload();
                    }else if(data.response === 'error'){
                        $('.error_msg_div').fadeTo(2000, 500).slideUp(500, function(){$(".error_msg_div").slideUp(1000);});
                        $('#error_msg').html(data.msg);
                        inventory_master_list_table.ajax.reload();
                    }
                    $('#master_list_submit_btn').attr('disabled',false).Html('Add');
                }
            });
        }

    });
    
    
</script>