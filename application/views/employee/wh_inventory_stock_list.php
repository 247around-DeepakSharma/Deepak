<style>
    #inventory_stock_table_filter{
        text-align: right;
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
<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0 40px;">
            <div class="x_panel">
                <div class="x_title">
                    <h3>Warehouse Spare Parts Inventory</h3>
                    <hr>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="x_content_header">
                        <section class="fetch_inventory_data">
                            <div class="row">
                                <div class="form-inline">
                                    <div class="form-group col-md-3">
                                        <select class="form-control" id="wh_id">
                                            <option value="" disabled="">Select Warehouse</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <select class="form-control" id="partner_id">
                                            <option value="" disabled="">Select Partner</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <select class="form-control" id="service_id">
                                            <option value="" disabled="">Select Appliance</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label class="checkbox-inline"><input type="checkbox" value="1" id="show_all_inventory">Show All</label>
                                    </div>
                                    <button class="btn btn-success col-md-2" id="get_inventory_data">Submit</button>
                                </div>
                            </div>
                        </section>
                    </div>
                    <div class="clearfix"></div>
                    <hr>
                    <div class="inventory_stock_list">
                        <table id="inventory_stock_table" class="table table-bordered table-responsive">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Appliance</th>
                                    <th>Type</th>
                                    <th>Name</th>
                                    <th>Number</th>
                                    <th>Stock</th>
                                    <th>Baisc Price</th>
                                    <th>GST Rate</th>
                                    <th>Total Price</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
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

    var inventory_stock_table;
    var is_admin_crm = false;
    var time = moment().format('D-MMM-YYYY');
    $(document).ready(function () {
        $('#wh_id').select2({
            placeholder:"Select Warehouse"
        });
        
        get_partner();
        get_vendor();
        get_appliance();
        get_inventory_list();
    });
    
    $('#get_inventory_data').on('click',function(){
        var wh_id = $('#wh_id').val();
        var partner_id = $('#partner_id').val();
        if(wh_id && partner_id){
            is_admin_crm = true;
            inventory_stock_table.ajax.reload();
        }else{
            alert("Please Select Warehouse and Partner Both");
        }
    });
    
    function get_inventory_list(){
        inventory_stock_table = $('#inventory_stock_table').DataTable({
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
                    title: 'stock_details_'+time,
                    action: newExportAction
                },
            ],
            "language": {
                "processing": "<div class='spinner'>\n\
                                    <div class='rect1' style='background-color:#db3236'></div>\n\
                                    <div class='rect2' style='background-color:#4885ed'></div>\n\
                                    <div class='rect3' style='background-color:#f4c20d'></div>\n\
                                    <div class='rect4' style='background-color:#3cba54'></div>\n\
                                </div>",
                "emptyTable":     "No Data Found"
            },
            select: {
                style: 'multi'
            },
            "order": [],
            "pageLength": 25,
            "ordering": false,
            "ajax": {
                url: "<?php echo base_url(); ?>employee/inventory/get_inventory_stocks_details",
                type: "POST",
                data: function(d){
                    
                    var entity_details = get_entity_details();
                    d.receiver_entity_id = entity_details.receiver_entity_id,
                    d.receiver_entity_type = entity_details.receiver_entity_type,
                    d.sender_entity_id = entity_details.sender_entity_id,
                    d.sender_entity_type = entity_details.sender_entity_type,
                    d.service_id = entity_details.service_id,
                    d.is_show_all = entity_details.is_show_all_checked,
                    d.is_admin_crm = is_admin_crm
                }
            },
            "deferRender": true
        });
    }
    
    function get_entity_details(){
        var data = {
            'receiver_entity_id': $('#wh_id').val(),
            'receiver_entity_type' : '<?php echo _247AROUND_SF_STRING; ?>',
            'sender_entity_id': $('#partner_id').val(),
            'sender_entity_type' : '<?php echo _247AROUND_PARTNER_STRING; ?>',
            'service_id': $('#service_id').val(),
            'is_show_all_checked':$('#show_all_inventory:checked').val()
        };
        
        return data;
    }
    
    function get_partner(){
        $.ajax({
            type:'POST',
            url:'<?php echo base_url();?>employee/partner/get_partner_list',
            data:{is_wh:true},
            success:function(response){
                $('#partner_id').html(response);
                $('#partner_id').select2();
            }
        });
    }
    
    function get_vendor() {
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/vendor/get_service_center_details',
            data:{'is_wh' : 1},
            success: function (response) {
                $('#wh_id').html(response);
            }
        });
    }
    
    function get_appliance(){
        $.ajax({
            type: 'GET',
            url: '<?php echo base_url() ?>employee/booking/get_service_id',
            data:{is_option_selected:true},
            success: function (response) {
                $('#service_id').html(response);
                $('#service_id').select2({
                    allowClear: true,
                    placeholder: 'Select Appliance'
                });
            }
        });
    }
    
    var oldExportAction = function (self, e, inventory_stock_table, button, config) {
        if (button[0].className.indexOf('buttons-excel') >= 0) {
            if ($.fn.dataTable.ext.buttons.excelHtml5.available(inventory_stock_table, config)) {
                $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, inventory_stock_table, button, config);
            }
            else {
                $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, inventory_stock_table, button, config);
            }
        } else if (button[0].className.indexOf('buttons-print') >= 0) {
            $.fn.dataTable.ext.buttons.print.action(e, inventory_stock_table, button, config);
        }
    };

    var newExportAction = function (e, inventory_stock_table, button, config) {
        var self = this;
        var oldStart = inventory_stock_table.settings()[0]._iDisplayStart;

        inventory_stock_table.one('preXhr', function (e, s, data) {
            // Just this once, load all data from the server...
            data.start = 0;
            data.length = inventory_stock_table.page.info().recordsTotal;

            inventory_stock_table.one('preDraw', function (e, settings) {
                // Call the original action function 
                oldExportAction(self, e, inventory_stock_table, button, config);

                inventory_stock_table.one('preXhr', function (e, s, data) {
                    // DataTables thinks the first item displayed is index 0, but we're not drawing that.
                    // Set the property to what it was before exporting.
                    settings._iDisplayStart = oldStart;
                    data.start = oldStart;
                });

                // Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
                setTimeout(inventory_stock_table.ajax.reload, 0);

                // Prevent rendering of the full data to the DOM
                return false;
            });
        });

        // Requery the server with the new one-time export settings
        inventory_stock_table.ajax.reload();
    };

</script>