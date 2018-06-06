<style>
    #inventory_master_list_filter{
        text-align: right;
    }
    
    .spinner {
        margin: 0px auto;
        wiinventory_master_list_tableh: 50px;
        height: 50px;
        text-align: center;
        font-size: 10px;
    }

    .spinner > div {
        height: 100%;
        wiinventory_master_list_tableh: 6px;
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
            wiinventory_master_list_tableh: 100%;
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
                    <h3>Inventory Master List</h3>
                </div>
                <div class="col-md-6">
                    <a class="btn btn-success pull-right" style="margin-top: 10px;" id="add_master_list" title="Add New Inventory"><i class="fa fa-plus"></i></a>
                </div>
            </div>
        </div>
        <hr>
        <div class="filter_box">
            <div class="row">
                <div class="form-inline">
                    <div class="form-group col-md-3">
                        <select class="form-control" id="partner_id">
                            <option value="" disabled="">Select Partner</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <select class="form-control" id="inventory_service_id">
                            <option value="" disabled="">Select Appliance</option>
                        </select>
                    </div>
<!--                    <div class="form-group col-md-2">
                        <label class="checkbox-inline"><input type="checkbox" value="1" id="show_all_inventory">Show All</label>
                    </div>-->
                    <button class="btn btn-success col-md-2" id="get_inventory_data">Submit</button>
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
                        <th>Spare Type</th>
                        <th>Spare Part Name</th>
                        <th>Spare Part Number</th>
                        <th>Spare Description</th>
                        <th>Spare Size</th>
                        <th>Spare Basic Price</th>
                        <th>HSN</th>
                        <th>Gst Rate</th>
                        <th>Edit</th>
                        <th>Get Model</th>
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
                                    <label class="control-label col-md-4" for="service_id">Appliance*</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <select class="form-control" id="service_id" name="service_id"></select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="entity_id">Partner *</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <select class="form-control" id="entity_id" name="entity_id">
                                            <option value="" selected="" disabled="">Please Select Partner</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="part_name">Part Name*</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <input type="text" class="form-control" id="part_name" name="part_name" placeholder="Part Name">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="part_number">Part Number*</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <input type="text" class="form-control" id="part_number" name="part_number" placeholder="Part Number">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="type">Part Type*</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <textarea class="form-control" id="type" name="type" placeholder="Type"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="description">Description</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <textarea class="form-control" id="description" name="description" placeholder="description"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="price">Price*</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <input type="number" class="form-control" id="price" name="price"  placeholder="Price">
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
                                    <label class="control-label col-md-4" for="hsn_code">Hsn Code*</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <input type="text" class="form-control" id="hsn_code" name="hsn_code" placeholder="HSN Code">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="gst_rate">Gst Rate*</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <input type="number" class="form-control" id="gst_rate"  name="gst_rate" placeholder="GST Rate">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="serial_number">Serial Number</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <input type="text" class="form-control" id="serial_number" name="serial_number" placeholder="Serial Number">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6" id="model_number_div">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="model_number_id">Model Number*</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <select class="form-control" id="model_number_id" name="model_number_id">
                                            <option value="" selected="" disabled="">Please Select Model</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <input type="hidden"  id="entity_type" name='entity_type' value="partner">
                            <input type="hidden"  id="inventory_id" name='inventory_id' value="">
                            <button type="submit" class="btn btn-success" id="master_list_submit_btn" name='submit_type' value="Submit">Submit</button>
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
    var time = moment().format('D-MMM-YYYY');
    $(document).ready(function(){
        
        get_partner('partner_id');
        get_inventory_list();
        get_services('inventory_service_id');
    });
    
    $('#get_inventory_data').on('click',function(){
        var partner_id = $('#partner_id').val();
        if(partner_id){
            inventory_master_list_table.ajax.reload();
        }else{
            alert("Please Select Partner");
        }
    });
    
    function get_inventory_list(){
        inventory_master_list_table = $('#inventory_master_list').DataTable({
            "processing": true, 
            "serverSide": true,
            "dom": 'lBfrtip',
            "buttons": [
                {
                    extend: 'excel',
                    text: 'Export',
                    exportOptions: {
                        columns: [ 0, 1, 2,3,4, 5,6,7 ]
                    },
                    title: 'inventory_master_list_'+time,
                    action: newExportAction
                },
            ],
            "language":{ 
                "processing": "<div class='spinner'>\n\
                                    <div class='rect1' style='background-color:#db3236'></div>\n\
                                    <div class='rect2' style='background-color:#4885ed'></div>\n\
                                    <div class='rect3' style='background-color:#f4c20d'></div>\n\
                                    <div class='rect4' style='background-color:#3cba54'></div>\n\
                                </div>"
            },
            select: {
                style: 'multi'
            },
            "order": [], 
            "pageLength": 25,
            "ordering": false,
            "ajax": {
                "url": "<?php echo base_url(); ?>employee/inventory/get_inventory_master_list",
                "type": "POST",
                data: function(d){
                    
                    var entity_details = get_entity_details();
                    d.entity_id = entity_details.entity_id,
                    d.entity_type = entity_details.entity_type,
                    d.service_id = entity_details.service_id
                }
            },
            "deferRender": true       
        });
    }
    
    function get_entity_details(){
        var data = {
            'entity_id': $('#partner_id').val(),
            'entity_type' : '<?php echo _247AROUND_PARTNER_STRING; ?>',
            'service_id': $('#inventory_service_id').val()
        };
        
        return data;
    }
    
    function get_partner(div_to_update){
        $.ajax({
            type:'POST',
            url:'<?php echo base_url();?>employee/partner/get_partner_list',
            data:{is_wh:true},
            success:function(response){
                $('#'+div_to_update).html(response);
                $('#'+div_to_update).select2();
            }
        });
    }
    
    function get_services(div_to_update){
        $.ajax({
            type:'GET',
            url:'<?php echo base_url();?>employee/booking/get_service_id',
            data:{is_option_selected:true},
            success:function(response){
                $('#'+div_to_update).html(response);
                $('#'+div_to_update).select2();
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
    
    $('#add_master_list').click(function(){
        $('#model_number_div').show();
        get_services('service_id');
        get_partner('entity_id');
        $("#master_list_details")[0].reset();
        $('#master_list_submit_btn').val('Add');
        $('#modal_title_action').html("Add New Inventory");
        $('#inventory_master_list_data').modal('toggle');
    });
    
    $('#entity_id, #service_id').on('change',function(){
        var service_id = $('#service_id').val();
        var entity_id = $('#entity_id').val();
        
        if(service_id && entity_id){
            $('#model_number_id').val(null).trigger('change');
            $("#model_number_id").select2({
                ajax: { 
                    url: '<?php echo base_url()?>employee/inventory/get_appliance_models',
                    type: 'post',
                    dataType: 'json',
                    data:{entity_id:entity_id,entity_type: '<?php echo _247AROUND_PARTNER_STRING ; ?>', service_id:service_id},
                    delay: 250,
                    processResults: function (data) {
                        return {
                            results: $.map(data, function(obj) {
                                return { id: obj.id, text: obj.model_number };
                            })
                        };
                    },
                    cache: true
                 }
            });
        }
        
    });
    
    $(document).on("click", "#edit_master_details", function () {
        $('#model_number_div').hide();
        var form_data = $(this).data('id');
        if(form_data.service_id){
            var service_options = "<option value='"+form_data.service_id+"' selected=''>"+form_data.services+"</option>";
            $('#service_id').html(service_options);
        }else{
            get_services('service_id');
        }
        
        if(form_data.entity_id){
            var entity_id_options = "<option value='"+form_data.entity_id+"' selected='' >"+form_data.entity_id+"</option>";
            $('#entity_id').html(entity_id_options);
        }
        
        
        $('#part_name').val(form_data.part_name);
        $('#part_number').val(form_data.part_number);
        $('#serial_number').val(form_data.serial_number);
        $('#entity_type').val(form_data.entity_type);
        $('#size').val(form_data.size);
        $('#price').val(JSON.parse(form_data.price));
        $('#hsn').val(form_data.hsn_code);
        $('#gst_rate').val(form_data.gst_rate);
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
        if(!$('#service_id').val()){
            alert('Please Select Appliance');
        }else if(!$('#entity_id').val()){
            alert("Please Select Partner");
        }else if($('#part_name').val().trim() === "" || $('#part_name').val().trim() === " "){
            alert("Please Enter Part Name");
        }else if($('#part_number').val().trim() === "" || $('#part_number').val().trim() === " "){
            alert("Please Enter Part Number");
        }else if($('#type').val().trim() === "" || $('#type').val().trim() === " "){
            alert("Please Enter Part Type");
        }else if($('#price').val().trim() === "" || $('#price').val().trim() === " " || $('#price').val().trim() === '0'){
            alert("Please Enter Valid Price");
        }else if($('#hsn_code').val().trim() === "" || $('#hsn_code').val().trim() === " " || $('#hsn_code').val().trim() === '0'){
            alert("Please Enter Valid Hsn Code");
        }else if($('#gst_rate').val().trim() === "" || $('#gst_rate').val().trim() === " "){
            alert("Please Enter Valid Gst Rate");
        }else{
            $('#master_list_submit_btn').attr('disabled',true).html("<i class = 'fa fa-spinner fa-spin'></i> Processing...");
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
                        $('.success_msg_div').fadeTo(8000, 500).slideUp(500, function(){$(".success_msg_div").slideUp(1000);});   
                        $('#success_msg').html(data.msg);
                        inventory_master_list_table.ajax.reload();
                    }else if(data.response === 'error'){
                        $('.error_msg_div').fadeTo(8000, 500).slideUp(500, function(){$(".error_msg_div").slideUp(1000);});
                        $('#error_msg').html(data.msg);
                        inventory_master_list_table.ajax.reload();
                    }
                    $('#master_list_submit_btn').attr('disabled',false).html('Add');
                }
            });
        }

    });
    
    var oldExportAction = function (self, e, inventory_master_list_table, button, config) {
        if (button[0].className.indexOf('buttons-excel') >= 0) {
            if ($.fn.dataTable.ext.buttons.excelHtml5.available(inventory_master_list_table, config)) {
                $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, inventory_master_list_table, button, config);
            }
            else {
                $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, inventory_master_list_table, button, config);
            }
        } else if (button[0].className.indexOf('buttons-print') >= 0) {
            $.fn.dataTable.ext.buttons.print.action(e, inventory_master_list_table, button, config);
        }
    };

    var newExportAction = function (e, inventory_master_list_table, button, config) {
        var self = this;
        var oldStart = inventory_master_list_table.settings()[0]._iDisplayStart;

        inventory_master_list_table.one('preXhr', function (e, s, data) {
            // Just this once, load all data from the server...
            data.start = 0;
            data.length = inventory_master_list_table.page.info().recordsTotal;

            inventory_master_list_table.one('preDraw', function (e, settings) {
                // Call the original action function 
                oldExportAction(self, e, inventory_master_list_table, button, config);

                inventory_master_list_table.one('preXhr', function (e, s, data) {
                    // DataTables thinks the first item displayed is index 0, but we're not drawing that.
                    // Set the property to what it was before exporting.
                    settings._iDisplayStart = oldStart;
                    data.start = oldStart;
                });

                // Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
                setTimeout(inventory_master_list_table.ajax.reload, 0);

                // Prevent rendering of the full data to the DOM
                return false;
            });
        });

        // Requery the server with the new one-time export settings
        inventory_master_list_table.ajax.reload();
    };
    
    
</script>