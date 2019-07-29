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
</style>
<div id="page-wrapper">
    <div class="row">
        <div class="title">
            <div class="row">
                <div class="col-md-6">
                    <h3>MSL List Sent By Partner</h3>
                </div>
            </div>
        </div>
        <hr>
        <div class="pull-right">
            <a class="btn btn-success"  href="#"  id="
               ">Download All Ledger</a><span class="badge" title="download all ledger details"><i class="fa fa-info"></i></span>
        </div>
        <div class="filter_box">
            <div class="row">
                <div class="form-inline">
                    <div class="form-group col-md-3">
                        <select class="form-control" id="partner_id">
                            <option value="" disabled="">Select Partner</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <select class="form-control" id="warehouse_id">
                            <option value="" disabled="">Select Warehouse</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <input type="text" placeholder="Invoice Date" class="form-control" id="invoice_date" name="invoice_date"/>
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
        <div class="model-table">
            <table class="table table-bordered table-hover table-striped" id="sent_msl_history_details">
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
    </div>
</div>
<script>
    var sent_msl_history_table;
    var entity_type = '';
    var entity_id = '';
    var time = moment().format('D-MMM-YYYY');
    $('#warehouse_id').select2({
        allowClear: true,
        placeholder: 'Select Warehouse'
    });
    $(document).ready(function(){
        get_partner('partner_id');
        get_sent_msl_history_list();
    });
    
    $('#get_appliance_model_data').on('click',function(){
        var partner_id = $('#partner_id').val();
        if(partner_id){
            sent_msl_history_table.ajax.reload(null, false);
        }else{
            alert("Please Select Partner");
        }
    });
    
    function get_sent_msl_history_list(){
        sent_msl_history_table = $('#sent_msl_history_details').DataTable({
            "processing": true, 
            "serverSide": true,
            "dom": 'lBfrtip',
            "buttons": [
                {
                    extend: 'excel',
                    text: 'Export',
                    exportOptions: {
                        columns: [ 0, 1, 2,3,4,5 ]
                    },
                    title: 'sent_msl_history_details'+time,
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
            "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50, 100,"All"]],
            "ordering": false,
            "ajax": {
                "url": "<?php echo base_url(); ?>employee/inventory/get_sent_msl_history_details",
                "type": "POST",
                data: function(d){
                    
                    var entity_details = get_entity_details();
                    d.sender_entity_id = entity_details.entity_id,
                    d.sender_entity_type = entity_details.entity_type,
                    d.receiver_entity_id = entity_details.warehouse_id,
                    d.receiver_entity_type = entity_details.receiver_entity_type,
                    d.invoice_date = entity_details.invoice_date
                }
            },
            "deferRender": true       
        });
    }
    
    function get_entity_details(){
        var data = {
            'entity_id': $('#partner_id').val(),
            'entity_type' : '<?php echo _247AROUND_PARTNER_STRING; ?>',
            'warehouse_id' : $('#warehouse_id').val(),
            'receiver_entity_type' : '<?php echo _247AROUND_SF_STRING; ?>',
            'invoice_date' : $('#invoice_date').val()
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
                var option_length = $('#'+div_to_update).children('option').length;
                if(option_length == 2){
                 $('#'+div_to_update).change();   
                }
                $('#'+div_to_update).select2();
            }
        });
    }
       
    $('#partner_id').on('change',function(){
        var partner_id = $('#partner_id').val();
        if(partner_id){
            get_warehouse_list('warehouse_id',partner_id);
        }
        
    });
    
    function get_warehouse_list(div_to_update,partner_id){
        $.ajax({
            type:'POST',
            url:'<?php echo base_url();?>employee/vendor/get_service_center_with_micro_wh',
            data:{partner_id:partner_id},
            success:function(response){
                $('#'+div_to_update).html(response);
            }
        });
    }
           
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
        
    var oldExportAction = function (self, e, sent_msl_history_table, button, config) {
        if (button[0].className.indexOf('buttons-excel') >= 0) {
            if ($.fn.dataTable.ext.buttons.excelHtml5.available(sent_msl_history_table, config)) {
                $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, sent_msl_history_table, button, config);
            }
            else {
                $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, sent_msl_history_table, button, config);
            }
        } else if (button[0].className.indexOf('buttons-print') >= 0) {
            $.fn.dataTable.ext.buttons.print.action(e, sent_msl_history_table, button, config);
        }
    };

    var newExportAction = function (e, sent_msl_history_table, button, config) {
        var self = this;
        var oldStart = sent_msl_history_table.settings()[0]._iDisplayStart;

        sent_msl_history_table.one('preXhr', function (e, s, data) {
            // Just this once, load all data from the server...
            data.start = 0;
            data.length = sent_msl_history_table.page.info().recordsTotal;

            sent_msl_history_table.one('preDraw', function (e, settings) {
                // Call the original action function 
                oldExportAction(self, e, sent_msl_history_table, button, config);

                sent_msl_history_table.one('preXhr', function (e, s, data) {
                    // DataTables thinks the first item displayed is index 0, but we're not drawing that.
                    // Set the property to what it was before exporting.
                    settings._iDisplayStart = oldStart;
                    data.start = oldStart;
                });

                // Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
                setTimeout(sent_msl_history_table.ajax.reload, 0);

                // Prevent rendering of the full data to the DOM
                return false;
            });
        });

        // Requery the server with the new one-time export settings
        sent_msl_history_table.ajax.reload();
    };
        
        
    $('input[name="invoice_date"]').daterangepicker({
            autoUpdateInput: false,
            locale: {
                format: 'YYYY/MM/DD',
                 cancelLabel: 'Clear'
            }
    });
    
    $('#invoice_date').on('apply.daterangepicker', function(ev, picker) {
        $('#invoice_date').val(picker.startDate.format('YYYY-MM-DD') + '/' + picker.endDate.format('YYYY-MM-DD'));
    });
           
</script>