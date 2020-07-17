<style>
      
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
    
    .dataTables_filter{
        float: right;
    }
    .custom-style{
        padding: 10px;
        font-weight: bold;
    }
</style>
<div id="page-wrapper">
    <div class="row">
        <div class="title">
            <div class="row">
                <div class="col-md-6">
                    <h3>Micro Warehouse MSL Details</h3>
                </div>
            </div>
        </div>
        <hr>
        <div class="filter_box">
            <div class="form-group">
                <div class="form-inline">
                    <div class="form-group col-md-4">
                        <select class="form-control" id="partner_id">
                            <option value="" disabled="">Select Partner</option>
                        </select>
                        <p id="partner_err"></p>
                    </div>
                </div>
            </div>
            
            <div class="form-group col-md-4">
                <select class="form-control" id="service_centers_id">
                    <option value="" disabled="">Select Service Center</option>
                </select>
                <p id="service_centers_id_err"></p>
            </div>
            
            <div class="form-group">
                <button class="btn btn-success col-md-1" id="get_inventory_data">Submit</button>
            </div>
            
              <div class="pull-right">
                   <a class="btn btn-success"  href="#"  id="download_sf_consumption_list">Download</a><span class="badge" title="Download service centers consumption"><i class="fa fa-info"></i></span>
               </div>  

        </div>
        </br>
        </br>       
        <hr>   
        <div class="inventory-table">
            <input type="hidden" id="inventory_set_id" value="">
            <table class="table table-bordered table-hover table-striped" id="service_centers_consumption">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Invoice Id</th>
                        <th>Invoice Date</th>
                        <th>Invoice Type</th>
                        <th>Part Number</th>
                        <th>Description</th>
                        <th> Service Center Name </th>
                        <th>HSN Code</th>
                        <th>Quantity</th>
                        <th>Settled Quantity</th>
                        <th>Rate</th>
                        <th>Taxable Value</th>
                        <th>GST Rate</th>
                        <th>GST Tax Amount</th>
                        <th>Total Amount</th>
                        <th>From GST Number</th>
                        <th>To GST Number</th>
                        <th>Sub Category</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
  
</div>

<script> 
    var service_centers_consumption_table;
    var entity_type = '';
    var entity_id = '';
    var time = moment().format('D-MMM-YYYY');
    $('#service_centers_id').select2({
        placeholder:'Select Service Center'
    });
    
    $(document).ready(function(){
        get_partner('partner_id');
        get_inventory_list();
    });
    
      $('#download_sf_consumption_list').click(function(){
        var partner_id = $("#partner_id").val();
        var service_center_id = $("#service_centers_id").val();
        if(partner_id!=null && partner_id!=''){
            $("#partner_err").html('');
            $('#download_sf_consumption_list').html("<i class = 'fa fa-spinner fa-spin'></i> Processing...").attr('disabled',true);
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/inventory/download_service_centers_consumption_data',
                data: { partner_id : partner_id, service_center_id : service_center_id },
                success: function (data) {
                    $('#download_sf_consumption_list').html("Download").attr('disabled',false);
                    var obj = JSON.parse(data); 
                    if(obj['status']){
                        window.location.href = obj['msg'];
                    }else{
                        alert('File Download Failed. Please Refresh Page And Try Again...')
                    }
                }
            });
        }else{
        $("#partner_err").html("Please select partner.").css('color','red');
        }
    });
    
    $('#get_inventory_data').on('click',function(){
        var partner_id = $('#partner_id').val();
        var service_centers_id = $('#service_centers_id').val();
        if(partner_id == '' || partner_id == null){
            $("#partner_err").html("Please select partner.").css('color','red');
            return false;
        }else{
            $("#partner_err").html('');   
        }
        
//        if(service_centers_id == '' || service_centers_id == null){
//            $("#service_centers_id_err").html("Please select Service Center.").css('color','red');
//            return false;
//        }else{
//            $("#service_centers_id_err").html('');
//        }
        
        service_centers_consumption_table.ajax.reload();
    });
    
        
    function get_inventory_list(){
        service_centers_consumption_table = $('#service_centers_consumption').DataTable({
            "processing": true, 
            "serverSide": true,
            "dom": 'lBfrtip',
            "buttons": [
                {
                    extend: 'excel',
                    text: 'Export',
                    exportOptions: {
                        columns: [ 0, 1, 2,3,4, 5,6,7,8,9,10,11,12,13,14,15 ]
                    },
                    title: 'service_centers_consumption_'+time,
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
            "order": [[0, 'asc']], 
            "pageLength": 50,
            "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50, 100,"All"]],
            "ordering": false,
            "ajax": {
                "url": "<?php echo base_url(); ?>employee/inventory/get_partner_wise_service_centers_consumption_list",
                "type": "POST",
                data: function(d){
                    
                    var entity_details = get_entity_details();
                    d.entity_id = entity_details.entity_id,
                    d.entity_type = entity_details.entity_type,
                    d.service_centers_id = entity_details.service_centers_id                    
                }
            },
            "deferRender": true       
        });
    }
    
       
    function get_entity_details(){
        var data = {
            'entity_id': $('#partner_id').val(),
            'entity_type' : '<?php echo _247AROUND_PARTNER_STRING; ?>',
            'service_centers_id': $('#service_centers_id').val()
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
    
    $('#partner_id').on('change',function(){
        var partner_id = $('#partner_id').val();
        var service_id = $('#inventory_service_id').val();
        if(service_id == '' || service_id == null){
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url() ?>employee/service_centers/get_partners_wise_sf_list',
                data:{ is_micro_wh : 1 ,partner_id : partner_id },
                success: function (response) {
                  $("#service_centers_id").html(response);                
                }
            }); 
        }
    });
    

    
    
    var oldExportAction = function (self, e, service_centers_consumption_table, button, config) {
        if (button[0].className.indexOf('buttons-excel') >= 0) {
            if ($.fn.dataTable.ext.buttons.excelHtml5.available(service_centers_consumption_table, config)) {
                $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, service_centers_consumption_table, button, config);
            }
            else {
                $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, service_centers_consumption_table, button, config);
            }
        } else if (button[0].className.indexOf('buttons-print') >= 0) {
            $.fn.dataTable.ext.buttons.print.action(e, service_centers_consumption_table, button, config);
        }
    };

    var newExportAction = function (e, service_centers_consumption_table, button, config) {
        var self = this;
        var oldStart = service_centers_consumption_table.settings()[0]._iDisplayStart;

        service_centers_consumption_table.one('preXhr', function (e, s, data) {
            // Just this once, load all data from the server...
            data.start = 0;
            data.length = service_centers_consumption_table.page.info().recordsTotal;

            service_centers_consumption_table.one('preDraw', function (e, settings) {
                // Call the original action function 
                oldExportAction(self, e, service_centers_consumption_table, button, config);

                service_centers_consumption_table.one('preXhr', function (e, s, data) {
                    // DataTables thinks the first item displayed is index 0, but we're not drawing that.
                    // Set the property to what it was before exporting.
                    settings._iDisplayStart = oldStart;
                    data.start = oldStart;
                });

                // Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
                setTimeout(service_centers_consumption_table.ajax.reload, 0);

                // Prevent rendering of the full data to the DOM
                return false;
            });
        });

        // Requery the server with the new one-time export settings
        service_centers_consumption_table.ajax.reload();
    };
                
</script>