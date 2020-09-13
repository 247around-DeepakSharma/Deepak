<style>
    .select2.select2-container.select2-container--default{
        width: 100%!important;
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
    
    #inventory_stock_table_processing{
            position: absolute;
            z-index: 999999;
            width: 100%;
            background: rgba(0,0,0,0.5);
            height: 100%;
            top: 10px;
            left:6%;
    }
    .dataTables_length{
     width: 15%;   
    }
</style>
<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0 40px;">
            <div class="x_panel">
                <div class="x_title">
                    <h3>View Alternate Parts</h3>
                    <hr>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="filter_box">
                        <div class="form-group col-md-3">
                            <select class="form-control" id="inventory_service_id">
                                <option value="" disabled="">Select Appliance</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <div class="form-group col-md-4">
                                <select class="form-control" id="spare_parts_inventory_id">
                                    <option value="" selected="" disabled="">Select Spare Number</option>
                                </select>
                            </div>
                            <button class="btn btn-success col-md-2" id="get_inventory_data">Submit</button>
                        </div>

                    </div>
                    <div class="clearfix"></div>
                    <hr>
                    <div class="alternate_inventory_master_list">
                        <table class="table table-bordered table-hover table-striped" id="alternate_inventory_master_list">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Appliance</th>
                                    <th>Type</th>
                                    <th>Name</th>
                                    <th>Parts Number</th>
                                    <th>Model Number</th>
                                    <th>Description</th>
                                    <th>Size</th>
                                    <th>HSN</th>
                                    <th>Basic Price</th>
                                    <th>GST Rate</th>
                                    <th>Total Price</th>
                                    <th>Customer Price</th>
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
<script>
    var alternate_inventory_master_list_table;
    var entity_type = '';
    var entity_id = '';
    var time = moment().format('D-MMM-YYYY');
    $('#inventory_service_id').select2({
        allowClear: true,
        placeholder: 'Select Appliance'
    });
    
    $('#spare_parts_inventory_id').select2({
        allowClear: true,
        placeholder: 'Select Spare Number'
    });
    
    $(document).ready(function(){
        get_inventory_list();
        get_services('inventory_service_id','<?php echo $partner_id; ?>');
    });
    
    $('#get_inventory_data').on('click',function(){
        var service_id = $('#inventory_service_id').val();
        var spare_parts_inventory_id = $('#spare_parts_inventory_id').val();
      
        if(service_id == '' || service_id == null){
            alert("Please Select Appliance"); 
            return false;
        }
        
        if(spare_parts_inventory_id == '' || spare_parts_inventory_id == null){
            alert("Please Select Spare Number"); 
            return false;
        }
        
        alternate_inventory_master_list_table.ajax.reload(function(data) {
            $("#inventory_set_id").val(data.set_id)
        });
                
    });
        
    function get_inventory_list(){
        alternate_inventory_master_list_table = $('#alternate_inventory_master_list').DataTable({
            "processing": true, 
            "serverSide": true,
            "dom": 'lBfrtip',
            "buttons": [
                {
                    extend: 'excel',
                    text: 'Export',
                    exportOptions: {
                        columns: [ 0, 1, 2,3,4, 5,6,7,8,9,10,11 ]
                    },
                    title: 'alternate_inventory_master_list_'+time,
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
            "pageLength": 50,
            "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50, 100,"All"]],
            "ordering": false,
            "ajax": {
                "url": "<?php echo base_url(); ?>employee/inventory/get_partner_wise_alternate_inventory_list",
                "type": "POST",
                data: function(d){
                    
                    var entity_details = get_entity_details();
                    d.entity_id = entity_details.entity_id,
                    d.entity_type = entity_details.entity_type,
                    d.inventory_id = entity_details.inventory_id,
                    d.service_id = entity_details.service_id,
                    d.request_type = true;
                }
            },
            "deferRender": true       
        });
    }
    
       
    function get_entity_details(){
        var data = {
            'entity_id': '<?php echo $partner_id; ?>',
            'entity_type' : '<?php echo _247AROUND_PARTNER_STRING; ?>',
            'inventory_id': $("#spare_parts_inventory_id").find('option:selected').attr("data-inventory"),
            'service_id': $('#inventory_service_id').val()
        };
        
        return data;
    }
    
    $('#inventory_service_id').on('change',function(){
        var service_id = $('#inventory_service_id').val();
        if(service_id == '' || service_id == null){
            alert('Please Select Appliance');
        }else{       
            $.ajax({
                method:'POST',
                url:'<?php echo base_url(); ?>employee/inventory/partner_wise_inventory_spare_parts_list',
                data:{ entity_id : '<?php echo $partner_id; ?>', entity_type : '<?php echo _247AROUND_PARTNER_STRING ; ?>', service_id : service_id },
                success:function(data){   
                    $("#spare_parts_inventory_id").html(data);
                }
            });
            
            
        }
    });
    
       
    
    function get_services(div_to_update,partner_id){
        
        $.ajax({
            type:'GET',
            async: true,
            url:'<?php echo base_url();?>employee/partner/get_partner_specific_appliance',
            data:{is_option_selected:true,partner_id:partner_id},
            success:function(response){
                $('#'+div_to_update).html(response);
            }
        });
    }
  
    $(document).on("click", "#change_status_alternate_spare_part", function () {
        var inventory_set_id = $('#inventory_set_id').val();
        var data = $(this).data('alternate_spare_details');
        var status = data.status;
        var inventory_id = data.inventory_id;
        if(inventory_id!='' && status != ''){
            if(status == 1 && confirm("Are you sure you want to deactivate ?")){
                status = '0';
            }else if(status == 0 && confirm("Are you sure you want to activate ?")){
                status = '1';
            }
            
            $.ajax({
                method:'POST',            
                url:'<?php echo base_url(); ?>employee/inventory/upate_alternate_inventory_set',
                dataType: "json",
                data: {inventory_id:inventory_id,status:status,inventory_set_id:inventory_set_id},
                success:function(response){
                      if(response.status == true){
                          alternate_inventory_master_list_table.ajax.reload();
                      }     
                }
            });
        }
                
     });
    
    var oldExportAction = function (self, e, alternate_inventory_master_list_table, button, config) {
        if (button[0].className.indexOf('buttons-excel') >= 0) {
            if ($.fn.dataTable.ext.buttons.excelHtml5.available(alternate_inventory_master_list_table, config)) {
                $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, alternate_inventory_master_list_table, button, config);
            }
            else {
                $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, alternate_inventory_master_list_table, button, config);
            }
        } else if (button[0].className.indexOf('buttons-print') >= 0) {
            $.fn.dataTable.ext.buttons.print.action(e, alternate_inventory_master_list_table, button, config);
        }
    };

    var newExportAction = function (e, alternate_inventory_master_list_table, button, config) {
        var self = this;
        var oldStart = alternate_inventory_master_list_table.settings()[0]._iDisplayStart;

        alternate_inventory_master_list_table.one('preXhr', function (e, s, data) {
            // Just this once, load all data from the server...
            data.start = 0;
            data.length = alternate_inventory_master_list_table.page.info().recordsTotal;

            alternate_inventory_master_list_table.one('preDraw', function (e, settings) {
                // Call the original action function 
                oldExportAction(self, e, alternate_inventory_master_list_table, button, config);

                alternate_inventory_master_list_table.one('preXhr', function (e, s, data) {
                    // DataTables thinks the first item displayed is index 0, but we're not drawing that.
                    // Set the property to what it was before exporting.
                    settings._iDisplayStart = oldStart;
                    data.start = oldStart;
                });

                // Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
                setTimeout(alternate_inventory_master_list_table.ajax.reload, 0);

                // Prevent rendering of the full data to the DOM
                return false;
            });
        });

        // Requery the server with the new one-time export settings
        alternate_inventory_master_list_table.ajax.reload();
    };
    
</script>
<style>
    .dataTables_length {
    width: 12% !important;
}
</style>