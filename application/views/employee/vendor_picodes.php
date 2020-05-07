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
                    <h3>Vendor Pincode Mapping 

                    </h3>
                    <hr>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">

                    <div class="clearfix"></div>
                    <hr>
                    <div class="inventory_stock_list">
                        <table id="pincode_table" class="table table-bordered table-responsive">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Appliance</th>
                                    <th>City</th>
                                    <th>State</th>
                                    <th>Pincode</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


  
    <script>

        pincode_table = $('#pincode_table').DataTable({
            "processing": true,
            "serverSide": true,
            "dom": 'lBfrtip',
            "buttons": [
                {
                    extend: 'excel',
                    text: 'Export',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    },
                    title: 'Vendor_pincode_mapping',
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
                "emptyTable": "No Data Found"
            },

            "order": [],
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "ordering": false,
            "ajax": {
                url: "<?php echo base_url(); ?>employee/vendor/get_vendor_pincode_mapping/<?php echo $vendor; ?>",
                type: "POST",
                data: function (d) {

                }
            }
        });
        //   }



        var oldExportAction = function (self, e, pincode_table, button, config) {
            if (button[0].className.indexOf('buttons-excel') >= 0) {
                if ($.fn.dataTable.ext.buttons.excelHtml5.available(pincode_table, config)) {
                    $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, pincode_table, button, config);
                } else {
                    $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, pincode_table, button, config);
                }
            } else if (button[0].className.indexOf('buttons-print') >= 0) {
                $.fn.dataTable.ext.buttons.print.action(e, pincode_table, button, config);
            }
        };

        var newExportAction = function (e, pincode_table, button, config) {
            var self = this;
            var oldStart = pincode_table.settings()[0]._iDisplayStart;

            pincode_table.one('preXhr', function (e, s, data) {
                // Just this once, load all data from the server...
                data.start = 0;
                data.length = pincode_table.page.info().recordsTotal;

                pincode_table.one('preDraw', function (e, settings) {
                    // Call the original action function 
                    oldExportAction(self, e, pincode_table, button, config);

                    pincode_table.one('preXhr', function (e, s, data) {
                        // DataTables thinks the first item displayed is index 0, but we're not drawing that.
                        // Set the property to what it was before exporting.
                        settings._iDisplayStart = oldStart;
                        data.start = oldStart;
                    });

                    // Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
                    setTimeout(pincode_table.ajax.reload, 0);

                    // Prevent rendering of the full data to the DOM
                    return false;
                });
            });

            // Requery the server with the new one-time export settings
          
        };



$('body').on('click','.makeactive',function(){
let id = $(this).attr("id");
            swal({
            title: "Are you sure?",
            text: "You will activate the pincode of vendor !",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, activate it!",
            closeOnConfirm: false
        },
         function(isConfirm){
           if (isConfirm) {
            $.ajax({
                url: "<?php echo base_url(); ?>employee/vendor/activate_deactivate_pincode",
                type: "POST",
                data: {id: id,action:'activate'},
                dataType: "html",
                success: function (data) { 
                    pincode_table.ajax.reload(null, false);
                    if(data=='1'){
                      swal("Done!","It was succesfully activated!","success");  
                    }else{
                       swal("Error!","Error Occured . Please try again after page refresh","error");  
                    }
                   
                    
                }
            });
          }else{
                swal("Cancelled", "Your pincode activation not processed!", "error");
          } 
       })

});


$('body').on('click','.makedeactive',function(){

    let id = $(this).attr("id");
            swal({
            title: "Are you sure?",
            text: "You will deactivate the pincode of vendor !",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, deactivate it!",
            closeOnConfirm: false
        },
         function(isConfirm){
           if (isConfirm) {
            $.ajax({
                url: "<?php echo base_url(); ?>employee/vendor/activate_deactivate_pincode",
                type: "POST",
                data: {id: id,action:'deactivate'},
                dataType: "html",
                success: function (data) {
                    pincode_table.ajax.reload(null, false);
                    if(data=='1'){
                      swal("Done!","It was succesfully deactivated!","success"); 
                    }else{
                       swal("Error!","Error Occured . Please try again after page refresh","error");  
                    }
                    
                }
            });
          }else{
                swal("Cancelled", "Your pincode deactivation not processed!", "error");
          } 
       })

});




    </script>