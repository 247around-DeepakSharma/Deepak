<style>
    #no_return_parts_by_sf_filter{
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
    .loader {
    position: fixed;
    left: 0px;
    top: 0px;
    display: none;
    width: 100%;
    height: 100%;
    z-index: 9999999;
    background: url('<?php echo base_url();  ?>images/loading_new.gif') 50% 50% no-repeat rgba(249,249,249,0.62);
  }
</style>
<div class="right_col" role="main">
    <div class="loader"></div>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0 40px;">
            <div class="x_panel">
                <div class="x_title">
                    <h3>Non Return Part By SF 

                    </h3>
                    <hr>
                    <div class="row">
                    </div>
                    <button class="btn btn-success pull-right" id="generate_invoice_btn">Generate Invoice</button>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">

                    <div class="clearfix"></div>
                    <hr>
                    <div class="inventory_stock_list">
                        <table id="no_return_parts_by_sf" class="table table-bordered table-responsive">
                            <thead>
                                <tr>
                                    <th class="text-center" >No</th>
                                    <th class="text-center" data-orderable="false">Booking Id</th>
                                    <th class="text-center" data-orderable="false">Service Center</th>
                                    <th class="text-center" data-orderable="false">Partner</th>
                                    <th class="text-center" data-orderable="false">Shipped Model Number</th>
                                    <th class="text-center" data-orderable="false">Shipped Part</th>
                                    <th class="text-center" data-orderable="false">Parts Number</th>   
                                    <th class="text-center" data-orderable="false">Shipped Part Type</th>
                                    <th class="text-center" data-orderable="false">Shipped  Quantity</th>
                                    <th class="text-center" data-orderable="false">Part Status</th>
                                    <th class="text-center" data-orderable="false">Select All<input style="margin-left:5px;" id="selectbox_all" type="checkbox" /></th>
                                </tr>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>




        <script>
            no_return_parts_by_sf = $('#no_return_parts_by_sf').DataTable({
                "processing": true,
                "serverSide": true,
                "dom": 'lBfrtip',
                "buttons": [
                    {
                        extend: 'excel',
                        text: 'Export',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                        },
                        title: 'no_return_parts_by_sf',
                        action: newExportAction
                    },
                ],
                "language": {
                    "processing": "<Processing...",
                    "emptyTable": "No Data Found"
                },

                "order": [],
                "pageLength": 25,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                "ordering": false,
                "ajax": {
                    url: "<?php echo base_url(); ?>employee/inventory/get_no_return_parts_by_sf_list",
                    type: "POST",
                    data: function (d) {
                        
                    }
                }
            });
            //   }



            var oldExportAction = function (self, e, no_return_parts_by_sf, button, config) {
                if (button[0].className.indexOf('buttons-excel') >= 0) {
                    if ($.fn.dataTable.ext.buttons.excelHtml5.available(no_return_parts_by_sf, config)) {
                        $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, no_return_parts_by_sf, button, config);
                    } else {
                        $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, no_return_parts_by_sf, button, config);
                    }
                } else if (button[0].className.indexOf('buttons-print') >= 0) {
                    $.fn.dataTable.ext.buttons.print.action(e, no_return_parts_by_sf, button, config);
                }
            };

            var newExportAction = function (e, no_return_parts_by_sf, button, config) {
                var self = this;
                var oldStart = no_return_parts_by_sf.settings()[0]._iDisplayStart;

                no_return_parts_by_sf.one('preXhr', function (e, s, data) {
                    // Just this once, load all data from the server...
                    data.start = 0;
                    data.length = no_return_parts_by_sf.page.info().recordsTotal;

                    no_return_parts_by_sf.one('preDraw', function (e, settings) {
                        // Call the original action function 
                        oldExportAction(self, e, no_return_parts_by_sf, button, config);

                        no_return_parts_by_sf.one('preXhr', function (e, s, data) {
                            // DataTables thinks the first item displayed is index 0, but we're not drawing that.
                            // Set the property to what it was before exporting.
                            settings._iDisplayStart = oldStart;
                            data.start = oldStart;
                        });

                        // Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
                        setTimeout(no_return_parts_by_sf.ajax.reload, 0);

                        // Prevent rendering of the full data to the DOM
                        return false;
                    });
                });

                // Requery the server with the new one-time export settings

            };

            $('body').on('click', '#selectbox_all', function () {
                // do something


                if ($(this).is(":checked")) {

                    $(".select_part").each(function () {
                        //select ebery checkbox
                        $(this).prop('checked', true);
                    });

                } else {
                    $(".select_part").each(function () {
                        //unselect ebery checkbox
                        $(this).prop('checked', false);
                    });
                }

            });
                
            $("#generate_invoice_btn").click(function(){
            $("#generate_invoice_btn").attr('disabled',true);
            var all_spare_details = new Array();
            var partner_id = new Array();
            $('.select_part').each(function () {
                if (this.checked) { 
                    //array to store values needed for each spare part
                    var spare_details = {"spare_id" : $(this).attr("data-spare-id"), "is_micro_wh" : $(this).attr("data-is-micro-wh"), "inventory_id" : $(this).attr("data-shipped-inventory-id"), 
                        "booking_partner_id" : $(this).attr("data-partner-id"), "shipping_quantity" : $(this).attr("data-shipping-quantity"), "part_name" : $(this).attr("data-part-name")
                        ,"booking_id" : $(this).attr("data-booking-id") ,"defective_return_to_entity_id" : $(this).attr("data-defective-return-to-entity-id"),"sc_code" : $(this).attr("data-sc-code")};
                    all_spare_details.push(spare_details);
                    partner_id.push($(this).attr("data-partner-id"));
                }
            });
            if(all_spare_details.length > 0) {
                var total_partners = jQuery.unique(partner_id).length;
                if(total_partners > 1) {
                    alert('Please select parts for single partner only.');
                    $("#generate_invoice_btn").attr('disabled',false);
                    return false;
                }  
                var c = confirm("Are You Sure?");
                if(!c) {
                    $("#generate_invoice_btn").attr('disabled',false);
                    return false;
                }
                $(".loader").css("display","block");
                            
                    var url = '<?php echo base_url(); ?>employee/invoice/generate_reverse_purchase_invoice_non_return_parts';
                    //generate reverse puchase invoice for SF and challan for partner
                     $.ajax({
                        type: 'POST',
                        url: url,
                        data: {data : JSON.stringify(all_spare_details)},
                        success: function (data) {
                         //   console.log("res="+data);
                            $(".loader").css("display","none");
                            alert(data);
                            location.reload();
                        }
                    });


            }
            else {
                alert("Please Select At Least One Checkbox");
                $("#generate_invoice_btn").attr('disabled',false);
            }


            });
            


        </script>