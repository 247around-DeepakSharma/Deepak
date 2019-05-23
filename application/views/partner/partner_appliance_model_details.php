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
                    <h2>Serviceable BOM</h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <a class="btn btn-success pull-right" style="margin-top: 10px;" id="add_model" title="Add New Model"><i class="fa fa-plus"></i></a>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="filter_box">
                        <div class="row">
                            <div class="form-inline">
                                <div class="form-group col-md-3">
                                    <select class="form-control" id="model_service_id">
                                        <option value="" disabled="">Select Appliance</option>
                                    </select>
                                </div>
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
                                    <th>Edit</th>
                                    <th>Get Part Details</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
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
                                                        <select class="form-control" id="service_id" name="service_id"></select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label col-md-4" for="part_name">Model Number *</label>
                                                    <div class="col-md-7 col-md-offset-1">
                                                        <input type="text" class="form-control" id="model_number" name="model_number">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <input type="hidden"  id="entity_id" name='entity_id' value='<?php echo $this->session->userdata('partner_id') ?>'>
                                            <input type="hidden" id="entity_type" name='entity_type' value="partner">
                                            <input type="hidden" id="model_id" name='model_id' value="">
                                            <button type="submit" class="btn btn-success" id="model_submit_btn" name='submit_type' value="">Submit</button>
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                            <p class="pull-left text-danger">* These fields are required</p>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal end -->
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
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
        get_services('model_service_id');
     //   get_appliance_model_list();
    });
    
    $('#get_appliance_model_data').on('click',function(){
        var model_service_id = $('#model_service_id').val();
        if(model_service_id){
            get_appliance_model_list();
        }else{
            alert("Please Select Partner");
        }
    });
    
    function get_appliance_model_list(){
        appliance_model_details_table = $('#appliance_model_details').DataTable({
            "processing": true, 
             destroy: true,
            "serverSide": true,
            "lengthMenu": [[ 50,100, -1], [ 50, 100,"All"]],
            "dom": 'lBfrtip',
                "buttons": [
                {
                    extend: 'excel',
                    text: '<span class="fa fa-file-excel-o"></span>  Export',
                    pageSize: 'LEGAL',
                    title: 'Model List', 
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
            "language":{ 
                "processing": "<div class='spinner'>\n\
                                    <div class='rect1' style='background-color:#db3236'></div>\n\
                                    <div class='rect2' style='background-color:#4885ed'></div>\n\
                                    <div class='rect3' style='background-color:#f4c20d'></div>\n\
                                    <div class='rect4' style='background-color:#3cba54'></div>\n\
                                </div>"
            },
            "order": [], 
            "pageLength": 50,
            "ordering": false,
            "ajax": {
                "url": "<?php echo base_url(); ?>employee/inventory/get_appliance_model_details",
                "type": "POST",
                data: function(d){
                    
                    var entity_details = get_entity_details();
                    d.entity_id = entity_details.entity_id,
                    d.entity_type = entity_details.entity_type,
                    d.service_id = entity_details.service_id,
                    d.partner_id = <?php echo $this->session->userdata('partner_id') ?>
                }
            },
            "deferRender": true       
        });
    }
    
    function get_entity_details(){
        var data = {
            'entity_id': '<?php echo $this->session->userdata('partner_id') ?>',
            'entity_type' : '<?php echo _247AROUND_PARTNER_STRING; ?>',
            'service_id': $('#model_service_id').val()
        };
        
        return data;
    }
    
    function get_services(div_to_update){
        $.ajax({
            type:'GET',
            url:'<?php echo base_url();?>employee/partner/get_partner_specific_appliance',
            data:{is_option_selected:true,partner_id: '<?php echo $this->session->userdata('partner_id')?>'},
            success:function(response){
                $('#'+div_to_update).html(response);
                $('#'+div_to_update).select2({
                    allowClear: true,
                    placeholder: 'Select Appliance'
                });
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
            var entity_id_options = "<option value='"+form_data.entity_id+"' selected='' >"+form_data.entity_id+"</option>";
            $('#entity_id').html(entity_id_options);
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
        var form_data = $("#applince_model_list_details").serializeArray();
        if(!$('#service_id').val()){
            alert("Please Select Appliance");
        }else if($('#model_number').val().trim() === "" || $('#model_number').val().trim() === " "){
            alert("Please Enter Model Number");
        }else{
            $('#model_submit_btn').html("<i class = 'fa fa-spinner fa-spin'></i> Processing...").attr('disabled',true);
            arr.name = 'submit_type';
            arr.value = $('#model_submit_btn').val();
            form_data.push(arr);
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
</script>
<style type="text/css">
    
    .dataTables_length {
    width: 12% !important;
}


        @media (min-width: 1200px){
.container {
    width: 100% !important;
}

.dataTables_filter{


    float: right !important;
    margin-top: -30px !important;
}
</style>