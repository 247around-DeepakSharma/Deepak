<?php 
// If vendor id is received it is assigned to d.id and that vendor will be displayed otherwise whole list of service center will be displayed
(isset($id)?$vid=$id:$vid='')
?>
<?php if (!isset($is_ajax)) { ?>
    <script>
        function outbound_call(phone_number) {
            var confirm_call = confirm("Call Vendor ?");

            if (confirm_call == true) {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo base_url(); ?>employee/booking/call_customer/' + phone_number,
                    success: function (response) {
                    }
                });
            } else {
                return false;
            }

        }

        function validate_form() {
            var state = $('#state_select').val();
            if (state == "") {
                $('#state_error').css('display', 'block');
                $('#state_form').css('margin-bottom', '20px');
                $('#inner_state_div').css('height', '75px');
                return false;
            } else {
                return true;
            }
        }



        function createPinCodeForm(id) {
            // document.getElementById("download_pin_code").href ="download_vendor_pin_code/"+id;
            //alert();
            $('#download_pin_code').attr('data-vendor_id', id);
            document.getElementById("upload_pin_code").href = "<?php echo base_url(); ?>employee/vendor/upload_pin_code_vendor/" + id;
            document.getElementById("download_pin_code").href = "<?php echo base_url(); ?>employee/vendor/download_vendor_pin_code/" + id;
            document.getElementById("v_name").innerHTML = $("#"+id).attr("data-vendor_name");
        }
    </script>
    <script>
        $(document).ready(function () {
            $('[data-toggle="popover"]').popover();
        });
    </script>
    <style>
        #vendor_details_filter{
            text-align: right;
        }
    </style>
    <div  id="page-wrapper">
        <div class="row">
            <?php
            if ($this->session->userdata('success')) {
                echo '<div class="alert alert-success alert-dismissible" role="alert">
                     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                     </button>
                     <strong>' . $this->session->userdata('success') . '</strong>
                 </div>';
            }
            ?>
            <?php
            if ($this->session->userdata('error')) {
                echo '<div class="alert alert-danger alert-dismissible" role="alert">
                     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                     </button>
                     <strong>' . $this->session->userdata('error') . '</strong>
                 </div>';
            }
            ?>
            <div >

                <h1>Service Center</h1>
                <div class="col-md-12" id="state_form">
                    <div class="row">
                        <!--                    <div class="pull-right" style="margin-bottom: 20px; margin-right: 20px;">-->
                        <div class="col-sm-2 col-lg-2 col-md-2">
                            <form action="<?php echo base_url(); ?>employee/vendor/viewvendor" method="get" id="get_vender" class="form-inline">
                                <select name="active_state" id="active_state" onchange="get_data();" class="form-control">
                                    <option value="all" <?php echo isset($selected) && $selected['active_state'] == 'all' ? 'selected="selected"' : '' ?>>ALL</option>
                                    <option value="1" <?php echo isset($selected) && $selected['active_state'] == '1' ? 'selected="selected"' : '' ?>>Active</option>
                                </select> 
                            </form>
                        </div>
                        <div class="col-sm-3 col-lg-3 col-md-3">
                            <select name="state" id="state"  class="form-control" style="width:100%" onchange="get_city()">
                                <option value="">Select State</option>
                                <?php
                                if (!empty($state_list)) {
                                    foreach ($state_list as $value) {
                                        ?>
                                        <option value="<?php echo $value['statevalue'] ?>"><?php echo $value['state']; ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select> 
                        </div>
                        <div class="col-sm-3 col-lg-3 col-md-3">
                            <select name="city" id="city"  class="form-control" style="width:100%" onchange="get_sf_cp()">
                                <option value="">Select City</option>
                            </select> 
                        </div>
    <?php if (!$saas_module) { ?>
                            <div class="col-sm-3 col-lg-3 col-md-3">
                                <select id="sf_cp" onchange="get_sf_cp();" class="form-control">
                                    <option value="sf">Service Center</option>
                                    <option value="cp">Collection Partner</option>
                                    <option value="wh">Warehouse</option>
                                </select>
                            </div> 
    <?php } ?>
                    </div>
                    <!--                </div>-->
                    <div class="row">
                        <div class="pull-right" style="margin-bottom: 20px;padding-top:8px">
    <?php if ($this->session->userdata('user_group') == _247AROUND_ADMIN || $this->session->userdata('user_group') == _247AROUND_RM || $this->session->userdata('user_group') == _247AROUND_ASM) { ?>
                                <a href="<?php echo base_url(); ?>employee/vendor/add_vendor"><input class="btn btn-primary" type="Button" value="Add Service Centre"></a>
                            <?php } ?>
                        </div>
                        <form method="POST" action ="<?php echo base_url(); ?>employee/vendor/get_sc_charges_list" style="padding-top:8px;margin-bottom: 20px;">
                            <input type="submit" value="Download Charges List" class="btn btn-primary" />
                        </form>
                    </div>

    <!--                <form method="POST" action ="<?php //echo base_url();  ?>employee/vendor/get_sc_charges_list" style="padding-top:8px;">
                <input type="submit" value="Download Charges List" class="btn btn-primary" />
        </form>-->
                    <div id="vendor_sf_cp_list">        
<?php } ?>
                    <table class="table table-bordered table-condensed" id="vendor_details">
                        <thead>
                            <tr>
                                <th class="jumbotron">ID</th>
                                <th class="jumbotron">Name</th>
                                <th class="jumbotron">CRM Login / Password</th>
                                <th class="jumbotron">PoC Name</th>
                                <th class="jumbotron">PoC Number</th>
                                <th class="jumbotron">Owner Name</th>
                                <th class="jumbotron">Owner Phone No.</th>
                                <th class="jumbotron">City/State</th>
                                <th class="jumbotron">Sub District Office</th>
                                <th class='jumbotron'>Go To Invoice Page</th>
                                <th class="jumbotron">Temporary</th>
                                <th class="jumbotron">Permanent</th>
                                <th class="jumbotron">Add Pin Code</th>
                                <th class="jumbotron">Resend Login Details</th>
                                <?php if ($this->session->userdata('user_group') == _247AROUND_ADMIN || ($this->session->userdata('user_group') == _247AROUND_ASM) || ($this->session->userdata('user_group') == _247AROUND_RM)) { ?>
                                <th class="jumbotron">View Active/De-active History</th>
                                <?php } 
                                 ?>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

<?php if (!isset($is_ajax)) { ?>

                    </div>
                </div>
            </div>
        </div>      
        <script type='text/javascript'>
            $('#state').select2({
                placeholder: "Select State",
                allowClear: true
            });
            $('#city').select2({
                placeholder: "Select City",
                allowClear: true
            });
            function login_to_vendor(vendor_id) {
                var c = confirm('Login to Service Center CRM?');
                if (c) {
                    $.ajax({
                        url: '<?php echo base_url() . "employee/login/allow_log_in_to_vendor/" ?>' + vendor_id,
                        success: function (data) {
                            window.open("<?php echo base_url() ?>" + data, '_blank');
                        }
                    });

                } else {
                    return false;
                }
            }


            function get_sf_cp() {
                var sf_cp = $('#sf_cp').val();
                var active_state = $('#active_state').val();
                var state = $('#state').val();
                var city = $('#city').val();
                $('#vendor_sf_cp_list').html('<div class="col-md-6 col-md-offset-6" style="margin-top: 46px;"><img src="/images/loadring.gif"></div>');
                $.ajax({
                    method: "POST",
                    url: '<?php echo base_url() . "employee/vendor/get_filterd_sf_cp_data" ?>',
                    data: {'sf_cp': sf_cp, 'active_state': active_state, 'state': state, 'city': city},
                    success: function (data) {
                        //console.log(data);
                        if (data === 'No Data Found') {
                            var resHTML = "<div class = 'text-center text-danger' style='margin-top:20px;'><strong>" + data + "</strong><div>";
                            $('#vendor_sf_cp_list').html(resHTML);
                        } else {
                            $('#vendor_sf_cp_list').html(data);
                        }

                    }
                });
            }

        </script>


    <?php
    if ($this->session->userdata('success')) {
        $this->session->unset_userdata('success');
    } if ($this->session->userdata('error')) {
        $this->session->unset_userdata('error');
    }
    ?>
    <?php } ?>

    <!-- This model class is used for pin code pop up-->
    <div id="pin_code" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" id="v_name" align="center"></h4>
                </div>
                <div class="modal-body" align="center">
                    <a id='download_pin_code' class='btn btn-info' href="javascript:void(0)" onclick="download_pincode_file()" href="<?php echo base_url(); ?>employee/vendor/download_vendor_pin_code/">Download Pin code</a>
                    <a id='upload_pin_code' class='btn btn-info' href="<?php echo base_url(); ?>employee/vendor/upload_vendor_pin_code" target="_blank">Upload Pin Code</a>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>

    <!-- This model class is used Update History View-->
    <div class="modal fade" id="history_view" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Updated History View</h4>
                </div>
                <div class="modal-body">
                    <div id="table_container"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>

    <!-- This model class is used Update On/Off History -->
    <div class="modal fade" id="on_off_history_view" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Active/De-active History View</h4>
                </div>
                <div class="modal-body">
                    <div id="on_off_table_container"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>

    <!-- This model is used send SMS to POC -->
    <div id="msg_poc" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" align="center">Send SMS to POC(<span id="poc_phone_no"></span>)</h4>
                </div>
                <div class="modal-body" style="min-height: 110px;">
                    <form class="form-horizontal" action="#">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="Description">Message</label>
                                <textarea class="form-control" placeholder="Enter Message" id="poc_msg" maxlength="160"></textarea>
                                <input type="hidden" id="sms_vendor_id" name="sms_vendor_id">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="send_sms_to_poc()">Send</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>

</div>
<script>



    function permanentVendorOff(vendorID) {
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/vendor/vendor_activate_deactivate/' + vendorID + '/0',
            success: function (response) {
                location.reload();
            }
        });
    }
    function tempVendorOff(vendorID) {
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/vendor/temporary_on_off_vendor/' + vendorID + '/0',
            success: function (response) {
                location.reload();
            }
        });
    }
    function pendingBookings(vendorID, tempPermanent, isMicroHouse) {
        var tempString = "off TEMPORARILY to";
        var microHouseMsg = "";
        if (tempPermanent == 'P') {
            var tempString = "off PERMANENTLY to";
        }
        if (isMicroHouse == '1') {
            microHouseMsg = "and a Micro WareHouse";
        }
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/vendor/pending_bookings_on_vendor/' + vendorID,
            success: function (response) {
                if (response > 0) {
                    if (confirm("This Service Center have " + response + " Pending Bookings, " + microHouseMsg + " are you sure you want to " + tempString + " this vendor")) {
                        if (tempPermanent == 'P') {
                            permanentVendorOff(vendorID);
                        } else {
                            tempVendorOff(vendorID)
                        }
                    }
                } else {
                    if (isMicroHouse == '1') {
                        if (confirm("This Service Center is a Micro WareHouse are you sure you want to " + tempString + " this vendor")) {
                            if (tempPermanent == 'P') {
                                permanentVendorOff(vendorID);
                            } else {
                                tempVendorOff(vendorID);
                            }
                        }
                    } else {
                        if (tempPermanent == 'P') {
                            permanentVendorOff(vendorID);
                        } else {
                            tempVendorOff(vendorID);
                        }
                    }
                }
            }
        });
    }
    function get_history_view(vendorID) {
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/vendor/get_partner_vendor_updation_history_view/' + vendorID + '/service_centres/trigger_service_centres',
            success: function (response) {
                console.log(response);
                $("#table_container").html(response);
            }
        });
    }

    /**
     * Function to get on-off history
     * @param {[[Number]]} vendorID [[service center id]]
     */
    function get_on_off_history_view(vendorID) {
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/vendor/get_partner_vendor_on_off_history_view/' + vendorID,
            success: function (response) {
                $("#on_off_table_container").html(response);
            }
        });
    }
    /** This function is used to send sms to poc  **/
    function send_sms_to_poc() {
        if ($("#poc_msg").val()) {
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/vendor/send_sms_to_poc',
                data: {phone_no: $("#poc_phone_no").text(), msg: $("#poc_msg").val(), sms_tag: 'sms_to_vendor_poc', vendor_id: $("#sms_vendor_id").val()},
                success: function (response) {
                    alert("SMS sent to vendor poc");
                    $("#msg_poc .close").click();
                }
            });
        } else {
            alert("please enter message");
        }
    }

    function model_for_sms(phone_no, vendor_id) {
        $("#poc_phone_no").text(phone_no);
        $("#sms_vendor_id").val(vendor_id);
    }

    function download_pincode_file() {
        var vendor_id = $("#download_pin_code").attr('data-vendor_id');

        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/vendor/download_vendor_pin_code/' + vendor_id,
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                var jsondata = JSON.parse(data);
                console.log(jsondata);
                if (jsondata['response'] === "success") {
                    //$("#spareDownload").css("display", "block");
                    //$("#messageSpare").text("");
                    // $("#spareDownload").attr("disabled", false).html("Download Spare");
                    window.location.href = jsondata['path'];
                } else if (jsondata['response'] === "failed") {
                    alert(jsondata['message']);
                    // $("#spareDownload").attr("disabled", false).html("Download Spare");
                    //$("#messageSpare").text("");
                } else {
                    //$("#spareDownload").attr("disabled", false).html("Download Spare");
                }
            }
        });
    }


    //  function get_inventory_list(){
    vendor_details = $('#vendor_details').DataTable({
        "processing": true,
        "serverSide": true,
        "dom": 'lBfrtip',
        "buttons": [
            {
                extend: 'excel',
                text: 'Export',
                exportOptions: {
                    columns: [0, 1, 3, 4, 5, 6, 7, 10, 11]
                },
                title: 'vendor_details',
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
            url: "<?php echo base_url(); ?>employee/vendor/get_vendor_list_ajax",
            type: "POST",
            data: function (d) {

                var entity_details = get_entity_details();
                d.state = entity_details.state,
                        d.city = entity_details.city,
                        d.id = '<?php echo $vid; ?>', //display vendor wrt id 
                        d.sf_cp = entity_details.sf_cp,
                        d.active = entity_details.active
            }
        }
    });
    //   }

    var oldExportAction = function (self, e, vendor_details, button, config) {
        if (button[0].className.indexOf('buttons-excel') >= 0) {
            if ($.fn.dataTable.ext.buttons.excelHtml5.available(vendor_details, config)) {
                $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, vendor_details, button, config);
            } else {
                $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, vendor_details, button, config);
            }
        } else if (button[0].className.indexOf('buttons-print') >= 0) {
            $.fn.dataTable.ext.buttons.print.action(e, vendor_details, button, config);
        }
    };

    var newExportAction = function (e, vendor_details, button, config) {
        var self = this;
        var oldStart = vendor_details.settings()[0]._iDisplayStart;

        vendor_details.one('preXhr', function (e, s, data) {
            // Just this once, load all data from the server...
            data.start = 0;
            data.length = vendor_details.page.info().recordsTotal;

            vendor_details.one('preDraw', function (e, settings) {
                // Call the original action function 
                oldExportAction(self, e, vendor_details, button, config);

                vendor_details.one('preXhr', function (e, s, data) {
                    // DataTables thinks the first item displayed is index 0, but we're not drawing that.
                    // Set the property to what it was before exporting.
                    settings._iDisplayStart = oldStart;
                    data.start = oldStart;
                });

                // Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
                setTimeout(vendor_details.ajax.reload, 0);

                // Prevent rendering of the full data to the DOM
                return false;
            });
        });

        // Requery the server with the new one-time export settings

    };


    function get_entity_details() {
        var data = {
            'state': $('#state').val(),
            'active': $("#active_state option:selected").val(),
            'city': $('#city').val(),
            'sf_cp': $('#sf_cp').val()
        };

        return data;
    }


    function get_data()
    {
        vendor_details.ajax.reload(null, false);

    }


    function get_city()
    {
        vendor_details.ajax.reload(null, false);
        var state_name = $("#state").val();
        if (state_name) {
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/vendor/get_city',
                data: {'state': state_name},
                success: function (html) {
                    $("#city").select2("val", "");
                    $('#city').html(html);
                    get_sf_cp();
                }
            });
        } else {
            $('#city').html('<option value="">Select State first</option>');

        }
    }


    function get_sf_cp() {
        vendor_details.ajax.reload(null, false);
        var sf_cp = $('#sf_cp').val();
        var active_state = $('#active_state').val();
        var state = $('#state').val();
        var city = $('#city').val();
        $('#vendor_sf_cp_list').html('<div class="col-md-6 col-md-offset-6" style="margin-top: 46px;"><img src="/images/loadring.gif"></div>');
        $.ajax({
            method: "POST",
            url: '<?php echo base_url() . "employee/vendor/get_filterd_sf_cp_data" ?>',
            data: {'sf_cp': sf_cp, 'active_state': active_state, 'state': state, 'city': city},
            success: function (data) {
                //console.log(data);
                if (data === 'No Data Found') {
                    var resHTML = "<div class = 'text-center text-danger' style='margin-top:20px;'><strong>" + data + "</strong><div>";
                    $('#vendor_sf_cp_list').html(resHTML);
                } else {
                    $('#vendor_sf_cp_list').html(data);
                }

            }
        });
    }

</script>
