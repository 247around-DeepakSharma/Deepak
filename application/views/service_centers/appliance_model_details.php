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
    .form-horizontal .control-label {
        text-align: left;
    }



    div.dataTables_wrapper div.dataTables_processing {
    position: absolute;
    top: 50%;
    /* left: 50%; */
    width: 200px;
    /* margin-left: -100px; */
    margin-top: -26px;
    text-align: center;
    padding: 1em 0;
}


div.dataTables_wrapper div.dataTables_processing {
    position: absolute;
    top: 50%;
      left: 0% !important;  
    width: 200px;
    margin-left: 0px !important;  
    margin-top: -26px;
    text-align: center;
    padding: 1em 0;
}


</style>
<div id="page-wrapper">
    <div class="row">
        <div class="title">
            <div class="row">
                <div class="col-md-6">
                    <h3>Appliance Model List</h3>
                </div>
                <div class="col-md-6">
                    <a class="btn btn-primary pull-right hide" style="margin-top: 10px; margin-left: 10px;" id="map_model" title="Map Model Number"><i class="fa fa-files-o" style="margin-right:5px"></i>Map Model</a>
                    <a class="btn btn-success pull-right hide" style="margin-top: 10px;" id="add_model" title="Add New Model"><i class="fa fa-plus" style="margin-right:5px"></i>Add New Model</a>
                </div>
            </div>
        </div>
        <hr>
        <div class="filter_box">
            <div class="row">
                <div class="form-inline">
                    <div class="form-group col-md-3">
                        <select class="form-control" id="model_partner_id">
                            <option value="" disabled="">Select Partner  </option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <select class="form-control" id="model_service_id">
                            <option value="" disabled="">Select Appliance</option>
                        </select>
                    </div>
<!--                    <div class="form-group col-md-2">
                        <label class="checkbox-inline"><input type="checkbox" value="1" id="show_all_inventory">Show All</label>
                    </div>-->
                    <button class="btn btn-success col-md-2" id="get_appliance_model_data">Submit</button>
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
        <div class="model-table">
            <table class="table table-bordered table-hover table-striped" id="appliance_model_details">
                <thead>
                    <tr>
                        <th>S.No</th>
                         <th>Appliance</th>
                        <th>Model Number</th>
                       
                           <th>Brand</th>
                        <th>Category</th>
                        
                        <th>Capacity</th>

                        <?php 

                        if ($this->session->userdata('userType') == 'service_center') {

                        }else{ ?>

                             <th>Edit</th> 
                       <?php  }


                         ?>




                      


                        <th>Get Part Details</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
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
                                    <label class="control-label col-md-4" for="model_number">Model Number *</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <input type="text" class="form-control" id="model_number" name="model_number">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="entity_id">Partner*</label>
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
                                    <label class="control-label col-md-4" for="service_id">Appliance*</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <select class="form-control" id="service_id" name="service_id"></select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="status">Active</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <input type="checkbox" class="form-control" id="active_inactive" name="active_inactive" checked>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <input type="hidden" id="entity_type" name='entity_type' value="partner">
                            <input type="hidden" id="model_id" name='model_id' value="">
                            <button type="submit" class="btn btn-success" id="model_submit_btn" name='submit_type' value="">Submit</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            <p class="pull-left text-danger">* These Fields are required</p>
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
                                    <label class="control-label col-md-4" for="model_entity_id">Partner*</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <select class="form-control" id="model_entity_id" name="model_entity_id" onchange="get_mapping_services()">
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="mapping_service_id">Service*</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <select class="form-control" id="mapping_service_id" name="mapping_service_id" onchange="get_mapping_category()">
                                              <option selected disabled>Select Service</option>
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
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" onclick="model_number_mapping()">Submit</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--Modal end-->
</div>
<script>
    var appliance_model_details_table;
    var entity_type = '';
    var entity_id = '';
    var time = moment().format('D-MMM-YYYY');
    $('#model_service_id').select2({
        allowClear: true,
        placeholder: 'Select Appliance'
    });
    $(document).ready(function(){
        
        get_partner('model_partner_id');
        get_appliance_model_list();
    });
    
    $('#get_appliance_model_data').on('click',function(){
        var model_partner_id = $('#model_partner_id').val();
        if(model_partner_id){
            appliance_model_details_table.ajax.reload(null, false);
        }else{
            alert("Please Select Partner");
        }
    });
    
    function get_appliance_model_list(){
        appliance_model_details_table = $('#appliance_model_details').DataTable({
            "processing": true, 
            "serverSide": true,
            "order": [], 
            "pageLength": 25,
            "dom": 'lBfrtip',
            "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50, 100,"All"]],
            "ordering": false,
               "buttons": [
                {
                    extend: 'excel',
                    text: '<span class="fa fa-file-excel-o"></span>  Export',
                    pageSize: 'LEGAL',
                    title: 'Appliance Model List',
                    exportOptions: {
                       columns: [0,1,2,3,4,5],
                        modifier : {
                             // DataTables core
                             order : 'index',  // 'current', 'applied', 'index',  'original'
                             page : 'current',      // 'all',     'current'
                             search : 'none'     // 'none',    'applied', 'removed'
                         }
                    }
                    
                }
            ],


            // "buttons": [
            //     {
            //         extend: 'excel',
            //         text: 'Export',
            //         exportOptions: {
            //             columns: [ 0, 1, 2 ]
            //         },
            //         title: 'appliance_model_details'+time
            //        // action: newExportAction
            //     },
            // ],
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
            "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50, 100,"All"]],
            "ordering": false,
            "ajax": {
                "url": "<?php echo base_url(); ?>employee/inventory/get_appliance_model_details",
                "type": "POST",
                data: function(d){
                    
                    var entity_details = get_entity_details();
                    d.entity_id = entity_details.entity_id,
                    d.entity_type = entity_details.entity_type,
                    d.service_id = entity_details.service_id,
                    d.partner_id = $('#model_partner_id').val()
 
                }
            },
            "deferRender": true       
        });
    }
    
    function get_entity_details(){
        var data = {
            'entity_id': $('#model_partner_id').val(),
            'entity_type' : '<?php echo _247AROUND_PARTNER_STRING; ?>',
            'service_id': $('#model_service_id').val()
        };
        
        return data;
    }
    
    function get_partner(div_to_update){
        var data = {};
        if(div_to_update != "entity_id"){
            data.is_wh = true;
        }
        $.ajax({
            type:'POST',
            url:'<?php echo base_url();?>employee/partner/get_partner_list',
            data: data,
            success:function(response){
                $('#'+div_to_update).html(response);
                $('#'+div_to_update).select2();
            }
        });
    }
    
    $('#model_partner_id').on('change',function(){
        var partner_id = $('#model_partner_id').val();
        if(partner_id){
            get_services('model_service_id',partner_id);
        }
        
    });
    
    $('#entity_id').on('change',function(){
        var partner_id = $('#entity_id').val();
        if(partner_id){
            get_services('service_id',partner_id);
        }
        
    });
    
    function get_services(div_to_update,partner_id){
        $.ajax({
            type:'GET',
            url:'<?php echo base_url();?>employee/partner/get_partner_specific_appliance',
            data:{is_option_selected:true,partner_id:partner_id},
            success:function(response){


                  console.log(response);


                $('#'+div_to_update).html(response);
            }
        });
    }
    
    $('#add_model').click(function(){
        $('#service_id').val(null).trigger('change');
        $('#service_id').select2({
            allowClear: true,
            placeholder: 'Select Appliance'
        });
        get_partner('entity_id');
        $("#applince_model_list_details")[0].reset();
        $('#model_submit_btn').val('Add');
        $('#modal_title_action').html("Add New Model");
        $('#appliance_model_details_data').modal('toggle');
    });
    
    $(document).on("click", "#edit_appliance_model_details", function () {
        var form_data = $(this).data('id');
        if(form_data.service_id){
                // Set the value, creating a new option if necessary
                if ($('#service_id').find("option[value='" + form_data.service_id + "']").length) {
                $('#service_id').val(form_data.service_id).trigger('change');
                } else { 
                 // Create a DOM Option and pre-select by default
                    var newOption = new Option(form_data.services, form_data.service_id, true, true);
                    // Append it to the select
                    $('#service_id').append(newOption).trigger('change');
                } 
        }else{
            get_services('service_id');
        }
        
        if(form_data.entity_id){
            var entity_id_options = "<option value='"+form_data.entity_id+"' selected='' >"+form_data.entity_public_name+"</option>";
            $('#entity_id').html(entity_id_options);
        }
        
        if(form_data.active === '1'){
            $("#active_inactive").prop('checked', true);
        }
        else{
            $("#active_inactive").prop('checked', false);
        }
        
        $('#model_number').val(form_data.model_number);
        $('#model_id').val(form_data.id);
        $('#model_submit_btn').val('Edit');
        $('#modal_title_action').html("Edit Details");
        $('#appliance_model_details_data').modal('toggle');
           
    });
    
    $("#model_submit_btn").click(function(){
        event.preventDefault();
        var arr = {};
        var status_arr = {}
        status_arr.name = 'status';
        if($("#active_inactive").is(':checked')){ 
          status_arr.value = 1;
        }
        else{ 
           status_arr.value = 0;
        }
        var form_data = $("#applince_model_list_details").serializeArray();
        if(!$('#entity_id').val()){
            alert("Please Select Partner");
        }else if(!$('#service_id').val()){
            alert("Please Select Appliance");
        }else if($('#model_number').val().trim() === "" || $('#model_number').val().trim() === " "){
            alert("Please Enter Model Number");
        }else{
            $('#model_submit_btn').html("<i class = 'fa fa-spinner fa-spin'></i> Processing...").attr('disabled',true);
            arr.name = 'submit_type';
            arr.value = $('#model_submit_btn').val();
            form_data.push(arr);
            form_data.push(status_arr);
            $.ajax({
                type:'POST',
                url:'<?php echo base_url(); ?>employee/inventory/process_appliance_model_list_data',
                data : form_data,
                success:function(response){
                    $('#appliance_model_details_data').modal('toggle');
                    var data = JSON.parse(response);
                    if(data.response === 'success'){
                        $('.success_msg_div').fadeTo(8000, 500).slideUp(500, function(){$(".success_msg_div").slideUp(1000);});   
                        $('#success_msg').html(data.msg);
                        appliance_model_details_table.ajax.reload();
                    }else if(data.response === 'error'){
                        $('.error_msg_div').fadeTo(8000, 500).slideUp(500, function(){$(".error_msg_div").slideUp(1000);});
                        $('#error_msg').html(data.msg);
                        appliance_model_details_table.ajax.reload();
                    }
                    $('#model_submit_btn').attr('disabled',false).html('Add');
                }
            });
        }

    });
    
    var oldExportAction = function (self, e, appliance_model_details_table, button, config) {
        if (button[0].className.indexOf('buttons-excel') >= 0) {
            if ($.fn.dataTable.ext.buttons.excelHtml5.available(appliance_model_details_table, config)) {
                $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, appliance_model_details_table, button, config);
            }
            else {
                $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, appliance_model_details_table, button, config);
            }
        } else if (button[0].className.indexOf('buttons-print') >= 0) {
            $.fn.dataTable.ext.buttons.print.action(e, appliance_model_details_table, button, config);
        }
    };

    var newExportAction = function (e, appliance_model_details_table, button, config) {
        var self = this;
        var oldStart = appliance_model_details_table.settings()[0]._iDisplayStart;

        appliance_model_details_table.one('preXhr', function (e, s, data) {
            // Just this once, load all data from the server...
            data.start = 0;
            data.length = appliance_model_details_table.page.info().recordsTotal;

            appliance_model_details_table.one('preDraw', function (e, settings) {
                // Call the original action function 
                oldExportAction(self, e, appliance_model_details_table, button, config);

                appliance_model_details_table.one('preXhr', function (e, s, data) {
                    // DataTables thinks the first item displayed is index 0, but we're not drawing that.
                    // Set the property to what it was before exporting.
                    settings._iDisplayStart = oldStart;
                    data.start = oldStart;
                });

                // Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
                setTimeout(appliance_model_details_table.ajax.reload, 0);

                // Prevent rendering of the full data to the DOM
                return false;
            });
        });

        // Requery the server with the new one-time export settings
        appliance_model_details_table.ajax.reload();
    };
    
    $("#map_model").click(function(){
        $.ajax({
            type:'POST',
            url:'<?php echo base_url();?>employee/partner/get_partner_list',
            data:{},
            success:function(response){
                $('#model_entity_id').html(response);
                $('#model_entity_id').select2();
            }
        });
        //$("#mapping_service_id, #mapping_model_number, #mapping_brand, #mapping_category, #mapping_capacity").val(null).trigger('change');
        $('#map_appliance_model').modal('toggle');
    });
    
    function get_mapping_services(){ 
        $.ajax({
            type:'POST',
            url:'<?php echo base_url();?>employee/service_centre_charges/get_partner_data',
            data:{partner:$('#model_entity_id').val()},
            success:function(response){
                $('#mapping_service_id').html(response);
                $('#mapping_service_id').select2();
            }
        });
    }
    
    function get_mapping_category(){
        $.ajax({
            type:'POST',
            url:'<?php echo base_url();?>employee/partner/get_brands_from_service',
            data:{partner_id:$('#model_entity_id').val(), service_id:$('#mapping_service_id').val()},
            success:function(response){
                response = "<option disabled selected>Select Brand</option>"+response;
                $('#mapping_brand').html(response);
                $('#mapping_brand').select2();
            }
        });
        
        $.ajax({
            type:'POST',
            url:'<?php echo base_url();?>employee/partner/get_category_from_service',
            data:{partner_id:$('#model_entity_id').val(), service_id:$('#mapping_service_id').val()},
            success:function(response){
                response = "<option disabled selected>Select Category</option>"+response;
                $('#mapping_category').html(response);
                $('#mapping_category').select2();
            }
        });
        
        $.ajax({
            type:'POST',
            url:'<?php echo base_url();?>employee/inventory/get_appliance_model_number',
            data:{partner_id:$('#model_entity_id').val(), service_id:$('#mapping_service_id').val()},
            success:function(response){
                if(response){
                    $('#mapping_model_number').html(response);
                    $('#mapping_model_number').select2();
                }
            }
        });
    }
    
    function get_mapping_capacity(){
         $.ajax({
            type:'POST',
            url:'<?php echo base_url();?>employee/partner/get_capacity_for_partner',
            data:{partner_id:$('#model_entity_id').val(), service_id:$('#mapping_service_id').val(), category:$('#mapping_category').val()},
            success:function(response){
                response = "<option disabled selected>Select Capacity</option>"+response;
                $('#mapping_capacity').html(response);
                $('#mapping_capacity').select2();
            }
        });
    }
    
    function model_number_mapping(){
        if(!$("#model_entity_id").val()){
            alert("Please Select Partner");
            return false;
        }
        else if(!$("#mapping_service_id").val()){
            alert("Please Select Service");
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
        else if(!$("#mapping_model_number").val()){
            alert("Please Select Model Number");
            return false;
        }
        else{
            $.ajax({
                type:'POST',
                url:'<?php echo base_url();?>employee/inventory/process_model_number_mapping',
                data:{partner_id:$('#model_entity_id').val(), service_id:$('#mapping_service_id').val(), category:$('#mapping_category').val(), brand:$('#mapping_brand').val(), capacity:$('#mapping_capacity').val(), model: $('#mapping_model_number').val()},
                success:function(response){
                    response = JSON.parse(response);
                    $('#map_appliance_model').modal('toggle');
                    if(response.status == true){
                        $('.success_msg_div').fadeTo(8000, 500).slideUp(500, function(){$(".success_msg_div").slideUp(1000);});   
                        $('#success_msg').html(response.message);
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