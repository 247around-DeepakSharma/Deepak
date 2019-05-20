<style>
    #inventory_master_list_filter{
        text-align: right;
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
    .form-horizontal .control-label {
        text-align: left;
    }
</style>
<div id="page-wrapper">
    <div class="row">
        <div class="title">
            <div class="row">
                <div class="col-md-6">
                    <h3>Alternate Spare Part List</h3>
                </div>
            </div>
        </div>
        <hr>
        <div class="filter_box">
            <div class="form-group">
                <div class="form-inline">
                    <div class="form-group col-md-3">
                        <select class="form-control" id="partner_id">
                            <option value="" disabled="">Select Partner</option>
                        </select>
                    </div>

                </div>
            </div>
            <div class="form-group col-md-3">
                <select class="form-control" id="inventory_service_id">
                    <option value="" disabled="">Select Appliance</option>
                </select>
            </div>
            <div class="form-group">
                <div class="form-group col-md-3">
                    <select class="form-control" id="spare_parts_type">
                        <option value="" selected="" disabled="">Select Spare Part Type</option>
                    </select>
                </div>

                <div class="form-group col-md-3">
                    <select class="form-control" id="spare_parts_inventory_id">
                        <option value="" selected="" disabled="">Select Spare Part</option>
                    </select>
                </div>

                <button class="btn btn-success col-md-2" id="get_inventory_data">Submit</button>
            </div>

        </div>
        </br>
        </br>       
        <hr>   
        <div class="inventory-table">
            <input type="hidden" id="inventory_set_id" value="">
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
                        <th>SF Basic Price</th>
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
<script>
    var alternate_inventory_master_list_table;
    var entity_type = '';
    var entity_id = '';
    var time = moment().format('D-MMM-YYYY');
    $('#inventory_service_id,#spare_parts_inventory_id').select2({
        allowClear: true,
        placeholder: 'Select Appliance'
    });
    
   $('#spare_parts_type').select2({
        allowClear: true,
        placeholder: 'Select Part Type'
    });
    
    $('#spare_parts_inventory_id').select2({
        allowClear: true,
        placeholder: 'Select Spare Part'
    });
    
    $(document).ready(function(){
        get_partner('partner_id');
        get_inventory_list();
    });
    
    $('#get_inventory_data').on('click',function(){
        var partner_id = $('#partner_id').val();
        var service_id = $('#inventory_service_id').val();
        var spare_parts_inventory_id = $('#spare_parts_inventory_id').val();
        var spare_parts_type = $('#spare_parts_type').val();
      
        if(partner_id == '' || partner_id == null){
            alert("Please Select Partner"); 
            return false;
        }
        
        if(service_id == '' || service_id == null){
            alert("Please Select Appliance"); 
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
                        columns: [ 0, 1, 2,3,4, 5,6,7,8,9,10,11]
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
                    d.request_type = '1'
                }
            },
            "deferRender": true       
        });
    }
    
       
    function get_entity_details(){
        var data = {
            'entity_id': $('#partner_id').val(),
            'entity_type' : '<?php echo _247AROUND_PARTNER_STRING; ?>',
            'inventory_id': $("#spare_parts_inventory_id").find('option:selected').attr("data-inventory"),
            'service_id': $('#inventory_service_id').val(),
            'part_type': $('#spare_parts_type').val()
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
    
    $('#spare_parts_type').on('change',function(){
        var partner_id = $('#partner_id').val();
        var service_id = $('#inventory_service_id').val();
        var type = $("#spare_parts_type").val();
        if(service_id == '' || service_id == null){
            alert('Please Select Appliance');
        }else{       
            $.ajax({
                method:'POST',
                url:'<?php echo base_url(); ?>employee/inventory/partner_wise_inventory_spare_parts_list',
                data:{ entity_id : partner_id, entity_type : '<?php echo _247AROUND_PARTNER_STRING ; ?>', service_id : service_id,type : type },
                success:function(data){   
                   console.log(data);
                    $("#spare_parts_inventory_id").html(data);
                }
            });
            
            
        }
    });
    
        $('#inventory_service_id').on('change',function(){
        var partner_id = $('#partner_id').val();
        var inventory = $(this).val();
        var service_id = $('#inventory_service_id').val();
        if(service_id == '' || service_id == null){
            alert('Please Select Appliance');
        }else{       
            $.ajax({
                method:'POST',
                url:'<?php echo base_url(); ?>employee/inventory/partner_wise_inventory_spare_parts_list_type',
                data:{ entity_id : partner_id, entity_type : '<?php echo _247AROUND_PARTNER_STRING ; ?>', service_id : service_id,inventory : inventory },
                success:function(data){   
                   // console.log(data);
                    $("#spare_parts_type").html(data);
                }
            });
            
            
        }
    });
    
    
    $('#partner_id').on('change',function(){
        var partner_id = $('#partner_id').val();
        if(partner_id){
            get_services('inventory_service_id',partner_id);
        }else{
            alert('Please Select Partner');
        }
    });
    
    
    function get_services(div_to_update,partner_id){
        
        $.ajax({
            type:'GET',
            async: false,
            url:'<?php echo base_url();?>employee/service_centers/get_service_id_by_partner',
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