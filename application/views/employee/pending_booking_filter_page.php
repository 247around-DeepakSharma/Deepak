<div class="table_filter">
    <div class="row">
        <div class="col-md-4">
            <div class="item form-group">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <select class="form-control filter_table" onchange="filter_changes()" id="partner_id">
                        <option value="" selected="selected" disabled="">Select Partner</option>
                        <?php foreach($partners as $val){ ?>
                        <option value="<?php echo $val['id']?>"><?php echo $val['public_name']?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="item form-group">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <select class="form-control filter_table" onchange="filter_changes()" id="sf_id">
                        <option value="" selected="selected" disabled="">Select Service Center</option>
                        <?php foreach($sf as $val){ ?>
                        <option value="<?php echo $val['id']?>"><?php echo $val['name']?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="item form-group">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <select class="form-control filter_table" onchange="filter_changes()" id="appliance">
                        <option value="" selected="selected" disabled="">Select Services</option>
                        <?php foreach($services as $val){ ?>
                        <option value="<?php echo $val->id?>"><?php echo $val->services?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="row" style="margin-top: 12px;">
        <div class="col-md-4">
            <div class="item form-group">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <select class="form-control filter_table" onchange="filter_changes()" id="city">
                        <option value="" selected="selected" disabled="">Select City</option>
                        <?php foreach($cities as $val){ ?>
                        <option value="<?php echo $val['city']?>"><?php echo $val['city']?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="item form-group">
                <div class="col-md-12 col-sm-12 col-xs-12" >
                    <select class="form-control filter_table" onchange="filter_changes()" id="internal_status" multiple="" name="internal_status[]">
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="item form-group">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <input  type="text" placeholder="Booking Date Range" onchange="filter_changes()" class="form-control" id="booking_date" value="" name="booking_date"/>
                </div>
            </div>
        </div>
    </div>
    <div class="row" style="margin-top: 12px;">
        <div class="col-md-4">
            <div class="item form-group">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <select class="form-control filter_table" onchange="filter_changes()" id="request_type" multiple="" name="request_type[]">
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="item form-group">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <select class="form-control filter_table" onchange="filter_changes()" id="current_status">
                        <option value="" selected="selected" disabled="">Select Current Status</option>
                        <option value="Pending">Pending</option>
                        <option value="Rescheduled">Rescheduled</option>
                    </select>
                </div>
            </div>
        </div>
                <div class="col-md-4">
            <div class="item form-group">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <select class="form-control filter_table" onchange="filter_changes()" id="rm_id" style="padding-left: 3px;">
                        <option value="" selected="selected" disabled="">Select Regional Manager</option>
                        <?php foreach($rm as $val){ ?>
                        <option value="<?php echo $val['id']?>"><?php echo $val['full_name']?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="row" style="margin-top: 12px;">
        <div class="col-md-4">
            <div class="item form-group">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <select class="form-control filter_table" onchange="filter_changes()" id="is_upcountry" style="padding-left: 3px;">
                        <option value="" selected="selected" disabled="">Select Upcountry Details</option>
                        <option value="yes">Upcountry</option>
                        <option value="no">Non Upcountry</option>
                    </select>
                </div>
            </div>
        </div>
          <div class="col-md-4">
            <div class="item form-group">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <select class="form-control filter_table" onchange="filter_changes()" id="state">
                        <option value="" selected="selected" disabled="">Select State</option>
                        <?php foreach($state as $val){ ?>
                        <option value="<?php echo $val['state']?>"><?php echo $val['state']?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-4" <?php if($saas_module){ ?> style="display: none;" <?php } ?>>
            <div class="item form-group">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <select class="form-control filter_table" onchange="filter_changes()" id="actor" onchange="get_internal_status_and_request_type(this.value)">
                        <option value="" selected="selected" disabled="">Select Actor</option>
                        <option value="247Around">247Around</option>
                        <option value="Partner">Partner</option>
                        <option value="Vendor" <?php if(!$saas_module){ ?> selected="" <?php } ?>>Vendor</option>
                        <option value="not_define">not_define</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('#partner_id').select2({
            placeholder: "Select Partner",
            allowClear: true
        });
        $('#sf_id').select2({
            placeholder: "Select Service Center",
            allowClear: true
        });
        $('#rm_id').select2({
            placeholder: "Select Regional Manager",
            allowClear: true
        });
        $('#appliance').select2({
            placeholder: "Select Appliance",
            allowClear: true
        });
        $('#city').select2({
            placeholder: "Select City",
            allowClear: true
        });
        $('#state').select2({
            placeholder: "Select State",
            allowClear: true
        });
        
          $('#internal_status').select2({
            placeholder: "Select Partner Internal Status",
            allowClear: true
        });
        $('#request_type').select2({
            placeholder: "Select Request Type",
            allowClear: true
        });
        $('#current_status').select2({
            placeholder: "Select Current Status",
            allowClear: true
        });
        $('#actor').select2({
            placeholder: "Select Actor",
            allowClear: true
        });
        $('#is_upcountry').select2({
            placeholder: "Select Upcountry Details",
            allowClear: true
        });
        $('input[name="booking_date"]').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    format: 'YYYY-MM-DD',
                     cancelLabel: 'Clear'
                }
            });
        $('input[name="booking_date"]').on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));  
                datatable1.ajax.reload();
            });
            $('input[name="booking_date"]').on('cancel.daterangepicker', function (ev, picker) {
                $('input[name="booking_date"]').val("");
            });
            get_internal_status_and_request_type("Vendor");
            function get_internal_status(actor){
                $.ajax({
                        type: 'POST',
                        url: '<?php echo base_url(); ?>employee/booking/get_internal_status/' + actor,
                        success: function(response) {
                               $("#internal_status").html(response);
                        }
                });
            }
            function get_request_type(actor){
                $.ajax({
                        type: 'POST',
                        url: '<?php echo base_url(); ?>employee/booking/get_request_type/' + actor,
                        success: function(response) {
                            $("#request_type").html(response);
                        }
                });
            }
            function get_internal_status_and_request_type(actor){
                if(actor === ""){
                    actor = "blank";
                }
                get_internal_status(actor);
                get_request_type(actor);
            }
</script>