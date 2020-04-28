<style>
    #chat_table_filter{
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
 
    .pull-right{
        padding: 0 0 0 19px;
    }
</style>
<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0 40px;">
            <div class="x_panel">
                <div class="x_title">
                    <h3>Review Bookings Completed by Technicians

                    </h3>
                    <hr>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">

                    <div class="clearfix"></div>
                    <hr>
                    <div class="inventory_stock_list">
                        <table id="chat_table" class="table table-bordered table-responsive">
                            <thead>
                                <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Booking ID</th>
                                <th class="text-center">SF Name</th>
                                <th class="text-center">Engineer Name</th>
                                <th class="text-center">Amount Due</th>
                                <th class="text-center" >Amount Paid  </th>
                                <th class="text-center" >Broken  </th>
                                <th class="text-center" >Remarks  </th>
                                <th class="text-center" >Status</th>
                                <th class="text-center" >Booking Address</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    <script>


        //  function get_inventory_list(){
        chat_table = $('#chat_table').DataTable({
            "processing": true,
            "serverSide": true,
            "dom": 'lBfrtip',
            "buttons": [
                {
                    extend: 'excel',
                    text: 'Export',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6,7,8,9]
                    },
                    title: 'Engineer_review_data',
                    action: newExportAction
                },
            ],
            "language": {
                searchPlaceholder: "Search Boooking ID",

                "emptyTable": "No Data Found"
            },

            "order": [],
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "ordering": false,
            "ajax": {
                url: "<?php echo base_url(); ?>employee/engineer/review_engineer_action_by_admin_list",
                type: "POST",
                data: function (d) {

                }
            }
        });
        //   }



        var oldExportAction = function (self, e, chat_table, button, config) {
            if (button[0].className.indexOf('buttons-excel') >= 0) {
                if ($.fn.dataTable.ext.buttons.excelHtml5.available(chat_table, config)) {
                    $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, chat_table, button, config);
                } else {
                    $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, chat_table, button, config);
                }
            } else if (button[0].className.indexOf('buttons-print') >= 0) {
                $.fn.dataTable.ext.buttons.print.action(e, chat_table, button, config);
            }
        };

        var newExportAction = function (e, chat_table, button, config) {
            var self = this;
            var oldStart = chat_table.settings()[0]._iDisplayStart;

            chat_table.one('preXhr', function (e, s, data) {
                // Just this once, load all data from the server...
                data.start = 0;
                data.length = chat_table.page.info().recordsTotal;

                chat_table.one('preDraw', function (e, settings) {
                    // Call the original action function 
                    oldExportAction(self, e, chat_table, button, config);

                    chat_table.one('preXhr', function (e, s, data) {
                        // DataTables thinks the first item displayed is index 0, but we're not drawing that.
                        // Set the property to what it was before exporting.
                        settings._iDisplayStart = oldStart;
                        data.start = oldStart;
                    });

                    // Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
                    setTimeout(chat_table.ajax.reload, 0);

                    // Prevent rendering of the full data to the DOM
                    return false;
                });
            });

            // Requery the server with the new one-time export settings
          
        };

 

    </script>