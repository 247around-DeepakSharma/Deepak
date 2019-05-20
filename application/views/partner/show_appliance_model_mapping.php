<style>
    #appliance_model_details_filter{
        text-align: right;
    }
    #appliance_model_details_processing{
            position: absolute;
            z-index: 999999;
            width: 100%;
            background: rgba(0,0,0,0.5);
            height: 100%;
            top: 10px;
            left:6%;
    }
    
    .dataTables_length{
        width: 12%;
    }
</style>
<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Model Mapping List</h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <input type="hidden" id="partner_id" value="<?php echo $this->session->userdata('partner_id'); ?>">
                        <a class="btn btn-success pull-right" style="margin-top: 10px;" id="map_model" title="Add New Model"><i class="fa fa-plus"></i></a>
                    </ul>
                    <div class="clearfix"></div>
                </div>
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
                <div class="x_content">
                    <div class="inventory-table">
                        <table class="table table-bordered table-hover table-striped" id="appliance_model_details">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Model Number</th>
                                    <th>Appliance</th>
                                    <th>Brand</th>
                                    <th>Category</th>
                                    <th>Capacity</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

 <!--Modal start [ map model number ]-->
      <div id="map_appliance_model" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal_title_action"> Model Mapping </h4>
                </div>
                <div class="modal-body">

                    <form class="form-horizontal">
                       <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="mapping_service_id">Appliance*</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <select class="form-control" id="mapping_service_id" name="mapping_service_id" onchange="get_mapping_model_number(); get_mapping_brands(); get_mapping_category()">
                                            <option selected disabled>Select Appliance</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="mapping_model_number">Model Number*</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <select class="form-control" id="mapping_model_number" name="mapping_model_number">
                                            <option selected disabled>Select Model Number</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="mapping_brand">Brand*</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <select class="form-control" id="mapping_brand" name="mapping_brand">
                                            <option selected disabled>Select Brand</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="mapping_category">Category*</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <select class="form-control" id="mapping_category" name="mapping_category" onchange="get_mapping_capacity()">
                                            <option selected disabled>Select Category</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                         <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="mapping_capacity">Capacity*</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <select class="form-control" id="mapping_capacity" name="mapping_capacity">
                                            <option selected disabled>Select Capacity</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                           
                        </div>
                        
                        <div class="modal-footer">
                            <input type="hidden" id="model_action">
                            <input type="hidden" id="model_mapping_id">
                            <button type="button" class="btn btn-success" onclick="model_number_mapping()">Submit</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--Modal end-->
<script>
    var appliance_model_details_table;
    
    $(document).ready(function(){ 
        get_services(); 
        appliance_model_details_table = $('#appliance_model_details').DataTable({
            "processing": true, 
            "serverSide": true,
            "dom": 'lBfrtip',
            "buttons": [
                {
                    extend: 'excel',
                    text: 'Export',
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6]
                    },
                    title: 'mapped_model_number',
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
            "order": [], 
            "pageLength": 25,
            "ordering": false,
            "ajax": {
                "url": "<?php echo base_url(); ?>employee/inventory/get_partner_mapped_model_details",
                "type": "POST",
                data: {partner_id: '<?php echo $this->session->userdata('partner_id'); ?>', source:'partner_crm'}
            },
        });
    });
    
    function get_services(){ 
        $.ajax({
            type:'GET',
            url:'<?php echo base_url();?>employee/partner/get_partner_specific_appliance',
            data:{is_option_selected:true,partner_id: $("#partner_id").val()},
            success:function(response){
                if(response){
                   $('#mapping_service_id').html(response); 
                }
            }
        });
    }
    
    $('#map_model').click(function(){
        $("#model_action").val("add");
        $("#mapping_service_id").prop('selectedIndex',0).change();
        $("#mapping_model_number").empty();
        $("#mapping_brand").empty();
        $("#mapping_category").empty();
        $("#mapping_capacity").empty();
        $('#map_appliance_model').modal('toggle');
    });
    
    function get_mapping_model_number(){
        $.ajax({
            type:'POST',
            url:'<?php echo base_url();?>employee/inventory/get_appliance_model_number',
            data:{partner_id:$('#partner_id').val(), service_id:$('#mapping_service_id').val()},
            success:function(response){
                    if(response){
                        $('#mapping_model_number').html(response);
                        $('#mapping_model_number').select2();
                    }
                    else{
                        response = "<option disabled selected>Select Model Number</option>";
                        $('#mapping_model_number').html(response);
                        $('#mapping_model_number').select2();
                    }
                    
            }
        });
    }
    
    function get_mapping_brands(){
        $.ajax({
            type:'POST',
            url:'<?php echo base_url();?>employee/partner/get_brands_from_service',
            data:{partner_id:$('#partner_id').val(), service_id:$('#mapping_service_id').val()},
            success:function(response){
                response = "<option disabled selected>Select Brand</option>"+response;
                $('#mapping_brand').html(response);
                $('#mapping_brand').select2();
                $("#mapping_service_id").select2();
            }
        });
    }
    
    
    function get_mapping_category(){ 
        $.ajax({
            type:'POST',
            url:'<?php echo base_url();?>employee/partner/get_category_from_service',
            data:{partner_id:$('#partner_id').val(), service_id:$('#mapping_service_id').val()},
            success:function(response){
                response = "<option disabled selected>Select Category</option>"+response;
                $('#mapping_category').html(response);
                $('#mapping_category').select2();
                get_mapping_capacity();
            }
        });
    }
    
    function get_mapping_capacity(){
         $.ajax({
            type:'POST',
            url:'<?php echo base_url();?>employee/partner/get_capacity_for_partner',
            data:{partner_id:$('#partner_id').val(), service_id:$('#mapping_service_id').val(), category:$('#mapping_category').val()},
            success:function(response){
                response = "<option disabled selected>Select Capacity</option>"+response;
                $('#mapping_capacity').html(response);
                $('#mapping_capacity').select2();
            }
        });
    }
    
    function model_number_mapping(){
        if(!$("#mapping_service_id").val()){
            alert("Please Select Service");
            return false;
        }
        else if(!$("#mapping_model_number").val()){
            alert("Please Select Model Number");
            return false;
        }
        else if(!$("#mapping_brand").val()){
            alert("Please Select Brand");
            return false;
        }
        else if(!$("#mapping_category").val()){
            alert("Please Select Category");
            return false;
        }
        else{
            var url = "";
            var data;
            if($("#model_action").val() == "add"){
                url = '<?php echo base_url();?>employee/inventory/process_model_number_mapping';
                data = {partner_id:$('#partner_id').val(), service_id:$('#mapping_service_id').val(), category:$('#mapping_category').val(), brand:$('#mapping_brand').val(), capacity:$('#mapping_capacity').val(), model: $('#mapping_model_number').val()};
            }
            else{
                url = '<?php echo base_url();?>employee/inventory/update_model_number_mapping';
                data = {partner_appliance_details_id:$("#model_mapping_id").val(), partner_id:$('#partner_id').val(), service_id:$('#mapping_service_id').val(), category:$('#mapping_category').val(), brand:$('#mapping_brand').val(), capacity:$('#mapping_capacity').val(), model: $('#mapping_model_number').val()};
            }
            
            $.ajax({
                type:'POST',
                url:url,
                data:data,
                success:function(response){
                    response = JSON.parse(response);
                    $('#map_appliance_model').modal('toggle');
                    if(response.status == true){
                        $('.success_msg_div').fadeTo(8000, 500).slideUp(500, function(){$(".success_msg_div").slideUp(1000);});   
                        $('#success_msg').html(response.message);
                        appliance_model_details_table.ajax.reload();
                    }
                    else{
                        $('.error_msg_div').fadeTo(8000, 500).slideUp(500, function(){$(".error_msg_div").slideUp(1000);});
                        $('#error_msg').html(response.message);
                    }
                    
                }
            });
        }
    }
    
    function edit_mapped_model(btn){
        var data = JSON.parse($(btn).attr("data"));
        console.log(data);
        $("#model_mapping_id").val(data.map_id);
        $("#model_action").val("update");
        $("#mapping_service_id").val(data.service);
        $("#mapping_service_id").trigger("change");
        $('#map_appliance_model').modal('toggle');
        setTimeout(function(){ 
            $("#mapping_model_number").val(data.model).trigger("change");
            $("#mapping_brand").val(data.brand).trigger("change");
            $("#mapping_category").val(data.category).trigger("change");
        }, 2000);
        setTimeout(function(){
            if(data.capacity){
                $("#mapping_capacity").val(data.capacity).trigger("change");
            }
        }, 3000);
    }
</script>