<style>
    #invoice_table_filter{
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
    #invoice_table_processing{
        position: absolute;
        z-index: 999999;
        width: 100%;
        background: rgba(0,0,0,0.5);
        height: 100%;
        top: 10px;
    }
</style>

<div id="page-wrapper" >
    <div class="col-md-12" style="border-bottom: 1px solid #ccc; margin-bottom: 30px;">
        <div class="col-md-6">
            <?php if (empty($dashboard)) { ?><h2 class="page-header" style="border: none;">Spare Invoice List</h2><?php } ?>
        </div>
        <div class="col-md-6">
            <button onclick="open_create_invoice_form()" style="<?php if (empty($dashboard)) { ?>margin-top: 45px;<?php } ?>float: right;" class="btn btn-md btn-primary" id="btn_create_invoice" name="btn_create_invoice">create</button>
        </div>
    </div>
    <div class="col-md-12">
        <div class="inventory-table">
            <table class="table table-bordered table-hover table-striped" id="invoice_table">
                <thead>
                    <tr>
                        <th>SNo</th>
                        <th>Spare Id</th>
                        <th>Booking Id</th>
                        <th>Part Name</th>
                        <th>Booking Type</th>
                        <th>Part Warranty Type</th>
                        <th>Spare Status</th>
                        <th>Partner Name</th>
                        <th>Purchase Price(With Tax) </th>
                        <th>Purchase Invoice Id</th>
                        <th>Total Quote Given(With Tax)</th>
                        <th>Sell Price(With Tax)</th>                    
                        <th>Purchase Invoice PDF</th>
                        <th>Sale Invoice Id</th>
                        <th>Create Purchase Invoice </th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>                
    </div>
    <div id="purchase_invoice" class="modal fade" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" style="width: 100%;">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" onclick="btn_create_invoice.disabled = false;close_model()">&times;</button>
                    <h4 class="modal-title" id="modal-title"><strong>Generate Purchase Invoice</strong></h4>
                </div>
                <div class="modal-body height-full-length">
                    <form class="form-horizontal" id ="purchase_invoice_form" action="#"  method="POST" >
                        <div class="col-md-12" >
                            <div class="col-md-6 ">
                                <div class="form-group col-md-12  ">
                                    <label for="Claimed Price">Invoice ID *</label>
                                    <input type="text" class="form-control" style="font-size: 13px;"  id="invoice_id" placeholder="Enter Invoice ID" name="invoice_id" value = "" onblur="check_invoice_id(this.id)" required>
                                </div>
                            </div>
                            <div class="col-md-6 ">
                                <div class="form-group col-md-12  ">
                                    <label for="remarks">Invoice Date *</label>
                                    <input type="text" class="form-control" style="font-size: 13px; background-color:#fff;" placeholder="Select Date" id="invoice_date" name="invoice_date" required readonly='true' >
                                </div>
                            </div>
                        </div>
                        <div id="spare_inner_html"></div>
                        <div class="col-md-12 ">
                            <div class="col-md-12 ">
                                <div class="form-group col-md-12  ">
                                    <label for="remarks">Remarks *</label>
                                    <input type="text" class="form-control" style="font-size: 13px;"  id="gst_rate" placeholder="Enter Remarks" name="remarks" value = "" required>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" id="btn_purchase_invoice" name="btn_purchase_invoice" onclick="genaerate_purchase_invoice()">Submit</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal" onclick="btn_create_invoice.disabled = false;close_model()">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="reverse_sale_invoice_model" class="modal fade" role="dialog"  data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close close_button_generate_invoice" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Generate Sale Invoice</h4>
            </div>
            <div class="modal-body">
                <label>Please Enter Remarks</label>
                <textarea id='remarks_revese_sale' class='form-control' style='height:100px;resize:none' onkeyup="$('#remarks_revese_sale').css('border', '');"></textarea>
                <input id='reverse_sale_id' class='form_control' type='hidden'>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="generate_sale_invoice()" id='generate_sale_invoice'>Generate Sale Invoice</button>
                <button type="button" class="btn btn-default close_button_generate_invoice" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>
<!-- Start Spare History Modal -->
<div id="spare_history_modal" class="modal fade" role="dialog">
    <div class="modal-dialog" style="width: 90%; margin-left: 10%;">
        <!-- Modal content-->
        <div class="modal-content" style="width: 90%;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="modal-title">Spare History Spare Id: <span id="sp_id" style="font-weight: bold;"></span></h4>
            </div>
            <div class="modal-body">
                <div id="tracking_body"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Start Spare History Modal -->
<?php
if (!empty($dashboard)) {
    ?>
    <script>
        $(document).ready(function () {
            $('#invoice_table').DataTable({
                "processing": true,
                "serverSide": false,
                "dom": 'lBfrtip',
                "buttons": [
                ],
                "order": [],
                "ordering": true,
                "deferRender": true,
                //"searching": false,
                //"paging":false
                "aLengthMenu": [[10, 20, 50, 100, -1], [10, 20, 50, 100, 'All']],
                "pageLength": 20,
                "language": {
                    "emptyTable": "No Data Found",
                    "searchPlaceholder": "Search by any column."
                },
            });
        });
        $(function () {
            $('#invoice_date').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                minYear: 1901,
                maxYear: parseInt(moment().format('YYYY'), 10),
                locale: {
                    format: 'YYYY-MM-D'
                }
            });
        });
    </script>
    <?php
    } else {
    ?>
    <script>

        var time = moment().format('D-MMM-YYYY');

        function get_entity_details() {
            var data = {
                'request_type': 'oow_spare_invoice',
            };
            return data;
        }

        get_spare_invoice_list();
        function get_spare_invoice_list() {
            invoice_table_table = $('#invoice_table').DataTable({
                "processing": true,
                "serverSide": true,
                "dom": 'lBfrtip',
                "buttons": [
                    {
                        extend: 'excel',
                        text: 'Export',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13]
                        },
                        title: 'invoice_table_' + time,
                        action: newExportAction
                    },
                ],
                "language": {
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
                "lengthMenu": [[50, 100, 150, 200, -1], [50, 100, 150, 200, "All"]],
                "ordering": false,
                "ajax": {
                    "url": "<?php echo base_url(); ?>employee/inventory/get_oow_spare_invoice_list",
                    "type": "POST",
                    data: function (d) {

                        var entity_details = get_entity_details();
                        d.request_type = entity_details.request_type
                    }
                },
                "deferRender": true
            });
        }


        var newExportAction = function (e, invoice_table_table, button, config) {
            var self = this;
            var oldStart = invoice_table_table.settings()[0]._iDisplayStart;

            invoice_table_table.one('preXhr', function (e, s, data) {
                // Just this once, load all data from the server...
                data.start = 0;
                data.length = invoice_table_table.page.info().recordsTotal;

                invoice_table_table.one('preDraw', function (e, settings) {
                    // Call the original action function 
                    oldExportAction(self, e, invoice_table_table, button, config);

                    invoice_table_table.one('preXhr', function (e, s, data) {
                        // DataTables thinks the first item displayed is index 0, but we're not drawing that.
                        // Set the property to what it was before exporting.
                        settings._iDisplayStart = oldStart;
                        data.start = oldStart;
                    });

                    // Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
                    setTimeout(invoice_table_table.ajax.reload, 0);

                    // Prevent rendering of the full data to the DOM
                    return false;
                });
            });

            // Requery the server with the new one-time export settings
            invoice_table_table.ajax.reload();
        };

        function sale_invoice_put_spare_id(spare_id) {
            $('#reverse_sale_id').val(spare_id);
            $('#remarks_revese_sale').val('');
            $('#remarks_revese_sale').css('border', '');
        }

        /*
         * Use to track the history of spare using spare_id
         */

        function spare_history_tracking(spare_id) {
            if (spare_id != '') {
                $.ajax({
                    method: "POST",
                    data: {spare_id: spare_id},
                    url: '<?php echo base_url(); ?>employee/spare_parts/get_spare_tracking_histroy',
                    success: function (resonse) {
                        $("#sp_id").html(spare_id);
                        $('#tracking_body').html(resonse);
                        $('#spare_history_modal').modal('toggle');
                    }
                });
            }
        }

        $("#invoice_date").datepicker({dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true});
    </script>
    <?php
}
?>
<script>

    function generate_sale_invoice() {
        var remarks_revese_sale = $("#remarks_revese_sale").val();
        remarks_revese_sale = remarks_revese_sale.trim();
        var reverse_sale_id = $("#reverse_sale_id").val();
        var flag = true;
        $("#remarks_revese_sale").css('border', '');
        if (flag) {
            var url = "<?php echo base_url(); ?>employee/invoice/generate_oow_parts_invoice/" + reverse_sale_id;
            var dashboard = "<?php if (!empty($dashboard)) {
            echo 'dashboard';
            } ?>";
            $.ajax({
                method: 'POST',
                dataType: "json",
                url: url,
                data: {remarks_revese_sale: remarks_revese_sale},
                beforeSend: function () {
                    $("#generate_sale_invoice").html("Generate Sale Invoice... <i class='fa fa-spinner fa-spin' aria-hidden='true'></i>");
                    $("#generate_sale_invoice").css('pointer-events', 'none');
                    $("#generate_sale_invoice").css('opacity', '.6');
                    $(".close_button_generate_invoice").css('pointer-events', 'none');
                },
                complete: function (data) {
                    alert('Invoice Generated Successfully');
                    $("#generate_sale_invoice").html("Generate Sale Invoice");
                    $("#generate_sale_invoice").css('pointer-events', '');
                    $("#generate_sale_invoice").css('opacity', '');
                    $(".close_button_generate_invoice").css('pointer-events', '');
                    if (dashboard == '') {
                        location.reload();
                    } else {
                        bring_generate_sale_invoice_view();
                    }
                }
            });
        }
    }


    function open_create_invoice_form() {
        var spare_id = [];
        var partner_id_array = [];
        var invoice_id_array = [];
        var data_list = [];
        if (!$('.spare_id').is(':checked')) {
            alert('Please check purchase invoice box');
            return false;
        } else {

            $('#btn_create_invoice').attr('disabled', true);

            $('.spare_id:checked').each(function (i) {
                spare_id[i] = $(this).val();
                var partner_id = $(this).attr('data-partner_id');
                var invoice_id = $(this).attr('data-invoice_id');
                partner_id_array.push(partner_id);
                invoice_id_array.push(invoice_id);
                data_list[i] = [];
                data_list[i]['spare_id'] = spare_id[i];
                data_list[i]['partner_id'] = partner_id;

            });
            var unique_partner = ArrayNoDuplicate(partner_id_array);
            var unique_invoice = ArrayNoDuplicate(invoice_id_array);
            var flag = true;
            if (unique_invoice.length > 1) {
                flag = false;
                $('#btn_create_invoice').attr('disabled', false);
                alert("You Can not select different invoice id.");
                return false;
            }

            if (unique_partner.length > 1) {
                flag = false;
                $('#btn_create_invoice').attr('disabled', false);
                alert("You Can not select multiple partner booking");
                return false;
            }
            if (flag) {
                $.ajax({
                    method: 'POST',
                    dataType: "json",
                    url: '<?php echo base_url(); ?>employee/inventory/get_spare_invoice_details',
                    data: {spare_id_array: spare_id},
                    success: function (data) {
                        if (data.length > 0) {
                            $("#invoice_id").val(data[0]['invoice_id']);
                            $("#invoice_date").val(data[0]['invoice_date']);
                            var html = '<input type="hidden" name="partner_id" value="' + unique_partner[0] + '" />';
                            for (k = 0; k < data.length; k++) {
                                if (data[k]["hsn_code"] == null) {
                                    data[k]["hsn_code"] = '';
                                }

                                html += '<div class="col-md-12">';
                                html += '<div class="col-md-6">';
                                html += '<div class="form-group col-md-8"> <div class="form-group col-md-12"><label for="remarks">Booking ID *</label>';
                                html += '<input required type="text" class="form-control" style="font-size: 13px;"  id="bookingid_' + k + '" readonly placeholder="Enter Booking ID" name="part[' + data_list[k]["spare_id"] + '][booking_id]" value = "' + data[k]['booking_id'] + '" >';
                                html += '</div></div>';

                                html += '<div class="form-group col-md-4" style="padding-left: 40px; width: 44%;"><div class="form-group col-md-12  ">';
                                html += ' <label for="remarks">HSN Code *</label>';
                                html += '<input required type="number" min="0" class="form-control" style="font-size: 13px;"  id="hsncode_' + k + '" placeholder="HSN Code" name="part[' + data_list[k]["spare_id"] + '][hsn_code]" value = "' + data[k]["hsn_code"] + '" >';
                                html += '</div></div></div>';

                                html += '<div class="col-md-6">';
                                html += '<div class="form-group col-md-4" style="padding-right: 40px;width: 44%;"><div class="form-group col-md-12  ">';
                                html += ' <label for="remarks">GST Rate *</label>';
                                html += '<select class="form-control" id="gstrate' + k + '" name="part[' + data_list[k]["spare_id"] + '][gst_rate]" required style="font-size: 13px;" >';
                                html += '<option selected disabled>Select GST Rate</option>';
                                <?php foreach (GST_NUMBERS_LIST as $key => $value) { ?>
                                    gst_number = '<?php echo $key; ?>';
                                    if (data[k]["gst_rate"] == gst_number) {
                                        html += '<option value = "' + data[k]["gst_rate"] + '" selected><?php echo $value; ?></option>';
                                    } else {
                                        html += '<option value = "' + data[k]["gst_rate"] + '"><?php echo $value; ?></option>';
                                    }
                                <?php } ?>
                                html += '</select>';
                                html += '</div></div>';

                                html += '<div class="form-group col-md-8"><div class="form-group col-md-12">';
                                html += ' <label for="remarks">Basic Amount *</label>';
                                html += '<input required type="number" step=".01" min="0" class="form-control invoice_amount" style="font-size: 13px;"  id="basic_amount' + k + '" placeholder="Enter Amount" name="part[' + data_list[k]["spare_id"] + '][basic_amount]" value = "' + data[k]["invoice_amount"] + '">';
                                html += '</div></div></div>';
                                html += '</div>';
                            }

                            $("#spare_inner_html").html(html);
                            $('#purchase_invoice').modal('toggle');
                        } else {
                            $('#btn_create_invoice').attr('disabled', false);
                        }

                    }
                });
            }
        }
    }

    function ArrayNoDuplicate(a) {
        var temp = {};
        for (var i = 0; i < a.length; i++)
            temp[a[i]] = true;
        var r = [];
        for (var k in temp)
            r.push(k);
        return r;
    }

    function genaerate_purchase_invoice() {

        var invoice_id = $("#invoice_id").val();
        var format = /[ `!@#$%^&*()+\=\[\]{};':"\\<>\?~]/;
        if (format.test(invoice_id)) {
            $("#invoice_id").val('');
            alert("Invoice id should not be special character.");
            return false;
        }

        $('#btn_purchase_invoice,#btn_create_invoice').attr('disabled', true);
        swal({
            title: "Do You Want To Continue?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            closeOnConfirm: true

        },
                function (isConfirm) {
                    if (isConfirm) {


                        var fd = new FormData(document.getElementById("purchase_invoice_form"));
                        var dashboard = "<?php if (!empty($dashboard)) {
                        echo 'dashboard';
                            } ?>";
                        fd.append("label", "WEBUPLOAD");
                        $.ajax({
                            type: "POST",
                            beforeSend: function () {
                                swal("Thanks!", "Please Wait..", "success");
                                $('body').loadingModal({
                                    position: 'auto',
                                    text: 'Loading Please Wait...',
                                    color: '#fff',
                                    opacity: '0.7',
                                    backgroundColor: 'rgb(0,0,0)',
                                    animation: 'wave'
                                });

                            },
                            data: fd,
                            processData: false,
                            contentType: false,
                            url: "<?php echo base_url() ?>employee/invoice/generate_spare_purchase_invoice",
                            success: function (data) {
                                console.log(data);
                                if (data === 'Success') {

                                    $('#purchase_invoice').modal('toggle');
                                    $("#invoice_id").val("");
                                    $("#invoice_date").val("");
                                    $("#parts_cost").val("");
                                    $("#gst_rate").val("");
                                    $("#hsn_code").val("");
                                    $("#remarks").val("");
                                    swal("Thanks!", "Booking updated successfully!", "success");
                                    if (dashboard == '') {
                                        location.reload();
                                    } else {
                                        bring_generate_sale_invoice_view();
                                    }

                                } else {
                                    swal("Oops", data, "error");
                                    alert(data);
                                    $('#btn_purchase_invoice,#btn_create_invoice').attr('disabled', false);
                                }
                                $('body').loadingModal('destroy');

                            }
                        });
                    } else {
                        $('#btn_purchase_invoice,#btn_create_invoice').attr('disabled', false);
                    }
                });
    }

    function check_invoice_id(id) {

        var invoice_id = $('#' + id).val().trim();
        if (invoice_id) {

            $.ajax({
                method: 'POST',
                url: '<?php echo base_url(); ?>check_invoice_id_exists/' + invoice_id,
                data: {is_ajax: true},
                success: function (res) {
                    //console.log(res);
                    var obj = JSON.parse(res);
                    if (obj.status === true) {

                        alert('Invoice number already exists');
                        $("#invoice_id").val('');
                    }
                }
            });

        }
    }

    function disable_btn(id) {
        $("#" + id).attr('disabled', true);
    }


    $("#invoice_id").keypress(function (e) {
        var keyCode = e.keyCode || e.which;
        var regex = /^[A-Za-z0-9-,./|_]+$/;
        //Validate TextBox value against the Regex.
        var isValid = regex.test(String.fromCharCode(keyCode));
        if (!isValid) {
            alert("Invoice id should not be special character.");
        }
        return isValid;
    });


    $('.invoice_amount').bind('keydown', function (event) {
        switch (event.keyCode) {
            case 8:  // Backspace
            case 9:  // Tab
            case 13: // Enter
            case 37: // Left
            case 38: // Up
            case 39: // Right
            case 40: // Down
                break;
            default:
                var regex = new RegExp("^[a-df-zA-DF-Z0-9,]+$");
                var key = event.key;
                if (!regex.test(key)) {
                    event.preventDefault();
                    return false;
                }
                break;
        }
    });


    $(".invoice_amount").on({
        "click": function () {
            var amount = $(this).val();
            if (amount < 0 || amount == 0) {
                $(this).val('');
                return false;
            }

        },
        "keyup": function () {
            var amount = $(this).val();
            if (amount < 0 || amount == 0) {
                $(this).val('');
                return false;
            }

        },
        "mouseleave": function () {
            var amount = $(this).val();
            if (amount < 0 || amount == 0) {
                $(this).val('');
                return false;
            }

        },
        "mouseout": function () {
            var amount = $(this).val();
            if (amount < 0 || amount == 0) {
                $(this).val('');
                return false;
            }
        }
    });
</script>
<style>
    .height-full-length
    {
        overflow: hidden;
    }
</style>
