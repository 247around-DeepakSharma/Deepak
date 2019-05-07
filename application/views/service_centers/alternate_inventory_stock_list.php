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
      
</style>

<div id="page-wrapper">
    <div class="row">
        <div class="title">
            <div class="row">
                <div class="col-md-6">
                    <h3><?php echo $part_name; ?> <small style="font-size: 16px;">Alternate Parts List</small> </h3>
                </div>
            </div>
        </div>
        <hr>   
        <div class="inventory-table">
            <table class="table table-bordered table-hover table-striped" id="alternate_inventory_master_list">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Appliance</th>
                        <th>Type</th>
                        <th>Name</th>
                        <th>Parts Number</th>
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
<script>
    var alternate_inventory_master_list_table;
    var entity_type = '';
    var entity_id = '';
    var time = moment().format('D-MMM-YYYY');
       
    $(document).ready(function(){
        get_inventory_list();
       
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
                    d.entity_id = '<?php echo $partner_id; ?>',
                    d.entity_type = '<?php echo _247AROUND_PARTNER_STRING; ?>',
                    d.inventory_id = '<?php echo $inventory_id; ?>',
                    d.service_id = '<?php echo $service_id; ?>'
                }
            },
            "deferRender": true       
        });
    }
   
       
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