<style>
    #appliance_model_details_filter{
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
    
    #appliance_model_details_processing{
            position: absolute;
            z-index: 999999;
            width: 100%;
            background: rgba(0,0,0,0.5);
            height: 100%;
            top: 10px;
            left:6%;
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
    
    .dataTables_length{
        width: 20%;
    }
    .form-horizontal .control-label {
        text-align: left;
    }
</style>
<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Model Mapping List</h2>
                    <input type="hidden" id="partner_id" value="<?php echo $this->session->userdata('partner_id'); ?>">
                     <ul class="nav navbar-right panel_toolbox">
                        <a class="btn btn-success pull-right" style="margin-top: 10px;" id="add_model" title="Add New Model">Add New Model</a>
                        <a class="btn btn-success pull-right" style="margin-top: 10px;" id="map_model" title="Add New Mapping">Add New Mapping</a>
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
                                    
                                    <th>Appliance</th>
                                    <th>Model Number</th>
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

   <!--Modal start-->
                    <div id="appliance_model_details_data" class="modal fade" role="dialog">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="modal_title_action"> </h4>
                                </div>
                                <div class="modal-body">

                                    <form class="form-horizontal" id="applince_model_list_details">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label col-md-4" for="service_id">Appliance*</label>
                                                    <div class="col-md-7 col-md-offset-1">
                                                        <select class="form-control addservices" onchange="get_partner_brands();"  id="service_id" name="service_id"></select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label col-md-4" for="model_numbernew">Model Number *</label>
                                                    <div class="col-md-7 col-md-offset-1">
                                                        <input type="text" class="form-control" id="mapping_model_numbernew" name="model_number">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                          <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label col-md-4" for="mapping_brandnew">Brand*</label>
                                                    <div class="col-md-7 col-md-offset-1">
                                                        <select class="form-control" id="mapping_brandnew" onchange="get_partner_mapping_category();" name="mapping_brand"></select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label col-md-4" for="mapping_categorynew">Category *</label>
                                                    <div class="col-md-7 col-md-offset-1">
                                                        <select class="form-control" id="mapping_categorynew" onchange="get_partner_mapping_capacity();"  name="mapping_category"></select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                           <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label col-md-4" for="mapping_capacitynew">Capacity*</label>
                                                    <div class="col-md-7 col-md-offset-1">
                                                        <select class="form-control" id="mapping_capacitynew" name="mapping_capacity"></select>
                                                    </div>
                                                </div>
                                            </div>
 
                                        </div>

                                        <div class="modal-footer">
                                            <input type="hidden"  id="entity_id" name='entity_id' value='<?php echo $this->session->userdata('partner_id') ?>'>
                                            <input type="hidden" id="entity_type" name='entity_type' value="partner">
                                            <input type="hidden" id="model_id" name='model_id' value="">
                                            <button type="button" onclick="model_number_check_and_add_mapping()" class="btn btn-success"  name='submit_type' value="">Submit</button>
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                            <p class="pull-left text-danger">* These fields are required</p>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal end -->
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
        get_services('model_service_id'); 
        get_services_appliances();
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
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "ordering": false,
            "ajax": {
                "url": "<?php echo base_url(); ?>employee/inventory/get_partner_mapped_model_details",
                "type": "POST",
                data: {partner_id: '<?php echo $this->session->userdata('partner_id'); ?>', source:'partner_crm'}
            },
        });
    });
    
    function get_services(div_to_update){
        $.ajax({
            type:'GET',
            url:'<?php echo base_url();?>employee/partner/get_partner_specific_appliance',
            data:{is_option_selected:true,partner_id: '<?php echo $this->session->userdata('partner_id')?>'},
            success:function(response){
                $('#'+div_to_update).html(response).find("#allappliance").remove();  
                $('#'+div_to_update).select2({
                    allowClear: true,
                    placeholder: 'Select Appliance'
                });
            }
        });
    }
    function get_services_appliances(){ 
        $.ajax({
            type:'GET',
            url:'<?php echo base_url();?>employee/partner/get_partner_specific_appliance',
            data:{is_option_selected:true,partner_id: $("#partner_id").val()},
            success:function(response){
                if(response){
                   $('#mapping_service_id').html(response); 
                    $('#mapping_service_id').find("#allappliance").remove(); 

                }
            }
        });
    }
    
     $('#add_model').click(function(){
        $('#service_id').val(null).trigger('change');
        get_services('service_id');
        $("#applince_model_list_details")[0].reset();
        $('#model_submit_btn').val('Add');
        $('#modal_title_action').html("Add Item");
        $('#appliance_model_details_data').modal('toggle');
    });
    
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
    
        function get_partner_brands(){
        $.ajax({
            type:'POST',
            url:'<?php echo base_url();?>employee/partner/get_brands_from_service',
            data:{partner_id:$('#partner_id').val(), service_id:$('#service_id').val()},
            success:function(response){
              ///  alert(response);
                response = "<option disabled selected>Select Brand</option>"+response;
                $('#mapping_brandnew').html(response);
                $('#mapping_brandnew').select2();
                get_partner_mapping_category();
               //  $("#service_id").select2();
            }
        });
    }
    
    function get_partner_mapping_category(){ 
        $.ajax({
            type:'POST',
            url:'<?php echo base_url();?>employee/partner/get_category_from_service',
            data:{partner_id:$('#partner_id').val(), service_id:$('#service_id').val()},
            success:function(response){
                response = "<option disabled selected>Select Category</option>"+response;
                $('#mapping_categorynew').html(response);
                $('#mapping_categorynew').select2();
                $('#mapping_capacitynew').select2();
            }
        });
    }
    
     function get_partner_mapping_capacity(){
         $.ajax({
            type:'POST',
            url:'<?php echo base_url();?>employee/partner/get_capacity_for_partner',
            data:{partner_id:$('#partner_id').val(), service_id:$('#service_id').val(), category:$('#mapping_categorynew').val()},
            success:function(response){
                console.log("Res"+$('#mapping_categorynew').val());
                response = "<option disabled selected>Select Capacity</option>"+response;
                $('#mapping_capacitynew').html(response);
                $('#mapping_capacitynew').select2();
            }
        });
    }
    
    function model_number_check_and_add_mapping(){
        if(!$("#service_id").val()){
            alert("Please Select Service");
            return false;
        }
        else if(!$("#mapping_model_numbernew").val()){
            alert("Please Select Model Number");
            return false;
        }
        else if(!$("#mapping_brandnew").val()){
            alert("Please Select Brand");
            return false;
        }
        else if(!$("#mapping_categorynew").val()){
            alert("Please Select Category");
            return false;
        }
        else{
            var url = "";
            var data;
                url = '<?php echo base_url();?>employee/inventory/add_model_number_mapping';
                data = {partner_appliance_details_id:$("#model_mapping_id").val(), partner_id:$('#partner_id').val(), service_id:$('#service_id').val(), category:$('#mapping_categorynew').val(), brand:$('#mapping_brandnew').val(), capacity:$('#mapping_capacitynew').val(), model: $('#mapping_model_numbernew').val(),entity_id:$("#entity_id").val(),entity_type:$("#entity_type").val()};

            $.ajax({
                type:'POST',
                url:url,
                data:data,
                success:function(response){
                    response = JSON.parse(response);
                    $('#appliance_model_details_data').modal('toggle');
                    if(response.status == true){
                        $('.success_msg_div').fadeTo(8000, 500).slideUp(500, function(){$(".success_msg_div").slideUp(1000);});   
                        $('#success_msg').html(response.message);
                    //    appliance_model_details_table.ajax.reload();
                    }
                    else{
                        $('.error_msg_div').fadeTo(8000, 500).slideUp(500, function(){$(".error_msg_div").slideUp(1000);});
                        $('#error_msg').html(response.message);
                    }
                    
                }
            });
        }
    }
</script>