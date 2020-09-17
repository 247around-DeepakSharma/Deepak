<div class="row">
    <div class="col-md-12">
        <div class="x_panel">
            <div class="x_title" style="padding-left: 0px;">
                <h2>Customer Paid Service Charges Less than Rs. 100</h2>
                <span class="collape_icon" href="#section_1" data-toggle="collapse" onclick=""><i class="fa fa-minus-square" aria-hidden="true"></i></span>
                <div class="clearfix"></div>
            </div>
            <div id="section_1" class="collapse in">
                <div class="col-md-4">
                    <label class="control-label" for="daterange">Date</label><br>
                    <?php
                        $endDate = date('Y/m/d');
                        $startDate = date('Y/m/d', strtotime('-1 day', strtotime($endDate)));
                        $dateRange = $startDate . " - " . $endDate;
                    ?>
                    <input style="border-radius: 5px;"  type="text" placeholder="Date" class="form-control" id="data_range_1" value="" name="data_range_1" value="<?php echo $dateRange; ?>"/>
                </div>
                <div class="col-md-4">
                    <a href="javascript:void(0);" class="btn btn-primary btn-sm data_1 search_btn" onclick="get_data_1()">Search</a>
                </div>
                <div class="col-md-4">
                    <a id="export_1" href="javascript:void(0);" class="btn btn-primary btn-sm export search_btn" data-id="1" style="float:right;display:none;">Export</a>
                    <a id="process_1" href="javascript:void(0);" class="btn btn-primary btn-sm process search_btn" data-id="1" style="float:right;display:none;margin-right:4px;">Process</a>
                </div>
                <div class="col-md-12">
                    <form id="<?php echo DASHBOARD_INVOICE_PROCESS_1; ?>" name="<?php echo DASHBOARD_INVOICE_PROCESS_1; ?>">
                        <input type="hidden" name="dashboard_process" value="<?php echo DASHBOARD_INVOICE_PROCESS_1; ?>">
                        <table class="table table-bordered table-collasped" id="table_1">
                            <thead>
                                <tr>
                                    <th>S. No.</th>
                                    <th>Booking ID</th>
                                    <th>Basic Charges Paid By Customer</th>
                                    <th>Extra Charges Paid By Customer</th>
                                    <th>Parts Paid By Customer</th>
                                    <th class="text-center">Process<br><input type="checkbox" class="form-control checkall_1"  name="all" value="all" onclick="checkall('checkall_1')"></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </form>
                    <center><img id="loader_gif_pending_1" src="<?php echo base_url(); ?>images/loadring.gif" style="display:none;"></center>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="x_panel">
            <div class="x_title" style="padding-left: 0px;">
                <h2>Check Serial Number/Image</h2>
                <span class="collape_icon" href="#section_2" data-toggle="collapse"><i class="fa fa-plus-square" aria-hidden="true"></i></span>
                <div class="clearfix"></div>
            </div>
            <div id="section_2" class="collapse">
                <div class="col-md-4">
                    <label class="control-label" for="daterange">Date</label><br>
                    <?php
                        $endDate = date('Y/m/d');
                        $startDate = date('Y/m/d', strtotime('-1 day', strtotime($endDate)));
                        $dateRange = $startDate . " - " . $endDate;
                    ?>
                    <input style="border-radius: 5px;"  type="text" placeholder="Date" class="form-control" id="data_range_2" value="" name="data_range_2" value="<?php echo $dateRange; ?>"/>
                </div>
                <div class="col-md-4">
                    <a href="javascript:void(0);" class="btn btn-primary btn-sm data_2 search_btn" onclick="get_data_2()">Search</a>
                </div>
                <div class="col-md-4">
                    <a id="export_2" href="javascript:void(0);" class="btn btn-primary btn-sm export search_btn" data-id="2" style="float:right;display:none;">Export</a>
                    <a id="process_2" href="javascript:void(0);" class="btn btn-primary btn-sm process search_btn" data-id="1" style="float:right;display:none;margin-right:4px;">Process</a>
                </div>
                <div class="col-md-12">
                    <form id="<?php echo DASHBOARD_INVOICE_PROCESS_2; ?>" name="<?php echo DASHBOARD_INVOICE_PROCESS_2; ?>">
                        <input type="hidden" name="dashboard_process" value="<?php echo DASHBOARD_INVOICE_PROCESS_2; ?>">
                        <table class="table table-bordered table-collasped" id="table_2">
                            <thead>
                                <tr>
                                    <th>S. No.</th>
                                    <th>Booking ID</th>
                                    <th>Basic Charges Paid By Customer</th>
                                    <th>Extra Charges Paid By Customer</th>
                                    <th>Parts Paid By Customer</th>
                                    <th>Message</th>
                                    <th class="text-center">Process<br><input type="checkbox" class="form-control checkall_2"  name="all" value="all" onclick="checkall('checkall_2')"></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </form>
                    <center><img id="loader_gif_pending_2" src="<?php echo base_url(); ?>images/loadring.gif" style="display:none;"></center>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="x_panel">
            <div class="x_title" style="padding-left: 0px;">
                <h2>Invoice Not Billed To Partner</h2>
                <span class="collape_icon" href="#section_3" data-toggle="collapse"><i class="fa fa-plus-square" aria-hidden="true"></i></span>
                <div class="clearfix"></div>
            </div>
            <div id="section_3" class="collapse">
                <div class="col-md-4">
                    <label class="control-label" for="daterange">Date</label><br>
                    <?php
                        $endDate = date('Y/m/d');
                        $startDate = date('Y/m/d', strtotime('-1 day', strtotime($endDate)));
                        $dateRange = $startDate . " - " . $endDate;
                    ?>
                    <input style="border-radius: 5px;"  type="text" placeholder="Date" class="form-control" id="data_range_3" value="" name="data_range_3" value="<?php echo $dateRange; ?>"/>
                </div>
                <div class="col-md-4">
                    <a href="javascript:void(0);" class="btn btn-primary btn-sm data_2 search_btn" onclick="get_data_3()">Search</a>
                </div>
                <div class="col-md-4">
                    <a id="export_3" href="javascript:void(0);" class="btn btn-primary btn-sm export search_btn" data-id="3" style="float:right;display:none;">Export</a>
                    <a id="process_3" href="javascript:void(0);" class="btn btn-primary btn-sm process search_btn" data-id="1" style="float:right;display:none;margin-right:4px;">Process</a>
                </div>
                <div class="col-md-12">
                    <form id="<?php echo DASHBOARD_INVOICE_PROCESS_3; ?>" name="<?php echo DASHBOARD_INVOICE_PROCESS_3; ?>">
                        <input type="hidden" name="dashboard_process" value="<?php echo DASHBOARD_INVOICE_PROCESS_3; ?>">
                        <table class="table table-bordered table-collasped" id="table_3">
                            <thead>
                                <tr>
                                    <th>S. No.</th>
                                    <th>Booking ID</th>
                                    <th>Basic Charges Paid By Customer</th>
                                    <th>Extra Charges Paid By Customer</th>
                                    <th>Parts Paid By Customer</th>
                                    <th class="text-center">Process<br><input type="checkbox" class="form-control checkall_3"  name="all" value="all" onclick="checkall('checkall_3')"></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </form>    
                    <center><img id="loader_gif_pending_3" src="<?php echo base_url(); ?>images/loadring.gif" style="display:none;"></center>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="x_panel">
            <div class="x_title" style="padding-left: 0px;">
                <h2>Completed Unit Details Of Pending Bookings</h2>
                <span class="collape_icon" href="#section_4" data-toggle="collapse"><i class="fa fa-plus-square" aria-hidden="true"></i></span>
                <div class="clearfix"></div>
            </div>
            <div id="section_4" class="collapse">
                <div class="col-md-4">
                    <label class="control-label" for="daterange">Date</label><br>
                    <?php
                        $endDate = date('Y/m/d');
                        $startDate = date('Y/m/d', strtotime('-1 day', strtotime($endDate)));
                        $dateRange = $startDate . " - " . $endDate;
                    ?>
                    <input style="border-radius: 5px;"  type="text" placeholder="Date" class="form-control" id="data_range_4" value="" name="data_range_4" value="<?php echo $dateRange; ?>"/>
                </div>
                <div class="col-md-4">
                    <a href="javascript:void(0);" class="btn btn-primary btn-sm data_4 search_btn" onclick="get_data_4()">Search</a>
                </div>
                <div class="col-md-4">
                    <a id="export_4" href="javascript:void(0);" class="btn btn-primary btn-sm export search_btn" data-id="4" style="float:right;display:none;">Export</a>
                    <a id="process_4" href="javascript:void(0);" class="btn btn-primary btn-sm process search_btn" style="float:right;display:none;margin-right:4px;">Process</a>                    
                </div>
                <div class="col-md-12">
                    <form id="<?php echo DASHBOARD_INVOICE_PROCESS_4; ?>" name="<?php echo DASHBOARD_INVOICE_PROCESS_4; ?>">
                        <input type="hidden" name="dashboard_process" value="<?php echo DASHBOARD_INVOICE_PROCESS_4; ?>">
                        <table class="table table-bordered table-collasped" id="table_4">
                            <thead>
                                <tr>
                                    <th>S. No.</th>
                                    <th>Booking ID</th>
                                    <th>Basic Charges Paid By Customer</th>
                                    <th>Extra Charges Paid By Customer</th>
                                    <th>Parts Paid By Customer</th>
                                    <th class="text-center">Process<br><input type="checkbox" class="form-control checkall_4"  name="all" value="all" onclick="checkall('checkall_4')"></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </form>    
                    <center><img id="loader_gif_pending_4" src="<?php echo base_url(); ?>images/loadring.gif" style="display:none;"></center>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="x_panel">
            <div class="x_title" style="padding-left: 0px;">
                <h2>Pending Unit Details Of Completed Bookings</h2>
                <span class="collape_icon" href="#section_5" data-toggle="collapse"><i class="fa fa-plus-square" aria-hidden="true"></i></span>
                <div class="clearfix"></div>
            </div>
            <div id="section_5" class="collapse">
                <div class="col-md-4">
                    <label class="control-label" for="daterange">Date</label><br>
                    <?php
                        $endDate = date('Y/m/d');
                        $startDate = date('Y/m/d', strtotime('-1 day', strtotime($endDate)));
                        $dateRange = $startDate . " - " . $endDate;
                    ?>
                    <input style="border-radius: 5px;"  type="text" placeholder="Date" class="form-control" id="data_range_5" value="" name="data_range_5" value="<?php echo $dateRange; ?>"/>
                </div>
                <div class="col-md-4">
                    <a href="javascript:void(0);" class="btn btn-primary btn-sm data_5 search_btn" onclick="get_data_5()">Search</a>
                </div>
                <div class="col-md-4">
                    <a id="export_5" href="javascript:void(0);" class="btn btn-primary btn-sm export search_btn" data-id="5" style="float:right;display:none;">Export</a>
                    <a id="process_5" href="javascript:void(0);" class="btn btn-primary btn-sm process search_btn" style="float:right;display:none;margin-right:4px;">Process</a>                    
                </div>
                <div class="col-md-12">
                    <form id="<?php echo DASHBOARD_INVOICE_PROCESS_5; ?>" name="<?php echo DASHBOARD_INVOICE_PROCESS_5; ?>">
                        <input type="hidden" name="dashboard_process" value="<?php echo DASHBOARD_INVOICE_PROCESS_5; ?>">
                        <table class="table table-bordered table-collasped" id="table_5">
                            <thead>
                                <tr>
                                    <th>S. No.</th>
                                    <th>Booking ID</th>
                                    <th>Basic Charges Paid By Customer</th>
                                    <th>Extra Charges Paid By Customer</th>
                                    <th>Parts Paid By Customer</th>
                                    <th class="text-center">Process<br><input type="checkbox" class="form-control checkall_5"  name="all" value="all" onclick="checkall('checkall_5')"></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </form>    
                    <center><img id="loader_gif_pending_4" src="<?php echo base_url(); ?>images/loadring.gif" style="display:none;"></center>
                </div>
            </div>
        </div>
    </div>
</div>


<style>
    .row, hr {
        margin-left:2%;
        margin-right:2%;
        margin-top: 2%;
    }
    
    .search_btn {
        margin-top: 5%;
    }
    
    table {
        margin-top:1%;
    }
    
    body {
        background: #F7F7F7 ;
    }
</style>

<style>
    .collape_icon {
        font-size: 18px;
        color: #4b5561 !important;
        float:right;
    }
    tr[id^='arm_table_'],
    tr[id^='arm_open_call_table_']{
        background-color:#5997aa !important;
    }
    .sub-table{
        width:98%;
        margin:auto;
    }
    table.sub-table thead{
        background:#8cc6ab;
    }
</style>

<script>
    $('input[name="data_range_1"]').daterangepicker({
        autoUpdateInput: false,
        locale: {
            format: 'YYYY/MM/DD',
            cancelLabel: 'Clear',
            maxDate: 'now'
        }
    });

    $('input[name="data_range_1"]').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('YYYY/MM/DD') + ' - ' + picker.endDate.format('YYYY/MM/DD'));
    });
    
    //$('#table_1').dataTable();
    $('input[name="data_range_2"]').daterangepicker({
        autoUpdateInput: false,
        locale: {
            format: 'YYYY/MM/DD',
            cancelLabel: 'Clear',
            maxDate: 'now'
        }
    });

    $('input[name="data_range_2"]').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('YYYY/MM/DD') + ' - ' + picker.endDate.format('YYYY/MM/DD'));
    });

    $('input[name="data_range_3"]').daterangepicker({
        autoUpdateInput: false,
        locale: {
            format: 'YYYY/MM/DD',
            cancelLabel: 'Clear',
            maxDate: 'now'
        }
    });

    $('input[name="data_range_3"]').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('YYYY/MM/DD') + ' - ' + picker.endDate.format('YYYY/MM/DD'));
    });

    $('input[name="data_range_4"]').daterangepicker({
        autoUpdateInput: false,
        locale: {
            format: 'YYYY/MM/DD',
            cancelLabel: 'Clear',
            maxDate: 'now'
        }
    });

    $('input[name="data_range_4"]').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('YYYY/MM/DD') + ' - ' + picker.endDate.format('YYYY/MM/DD'));
    });

    $('input[name="data_range_5"]').daterangepicker({
        autoUpdateInput: false,
        locale: {
            format: 'YYYY/MM/DD',
            cancelLabel: 'Clear',
            maxDate: 'now'
        }
    });

    $('input[name="data_range_5"]').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('YYYY/MM/DD') + ' - ' + picker.endDate.format('YYYY/MM/DD'));
    });

    function get_data_1() {
        $('#export_1').css('display', 'none');
        $('#process_1').css('display', 'none');
        
        var date = $('#data_range_1').val();
        if(date == '' || date == null) {
            alert('Please select date.');
            return false;
        }        
        var dateArray = date.split(" - ");
        var startDate = dateArray[0];
        var endDate =   dateArray[1];
        var startDateObj = new Date(startDate);
        var endDateObj = new Date(endDate);
        var timeDiff = Math.abs(endDateObj.getTime() - startDateObj.getTime());
        var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24)); 
        
        if(diffDays>31){
            alert("Maximum range allowed is 31 days.");
            return false;
        }  
        
        $("#loader_gif_pending_1").css("display", "block");
        
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/dashboard/get_bookings_to_be_invoiced',
            data: {date},
        }).done(function(data) {
            $('#table_1').children('tbody').html(data);
            $('#export_1').css('display', 'inline-block');
            $('#process_1').css('display', 'inline-block');
            $("#loader_gif_pending_1").css("display", "none");
        });
    }
    
    function get_data_2() {
        $('#export_2').css('display', 'none');
        $('#process_2').css('display', 'none');
        var date = $('#data_range_2').val();
        if(date == '' || date == null) {
            alert('Please select date.');
            return false;
        }        
        var dateArray = date.split(" - ");
        var startDate = dateArray[0];
        var endDate =   dateArray[1];
        var startDateObj = new Date(startDate);
        var endDateObj = new Date(endDate);
        var timeDiff = Math.abs(endDateObj.getTime() - startDateObj.getTime());
        var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24)); 
        
        if(diffDays>31){
            alert("Maximum range allowed is 31 days.");
            return false;
        }  
        
        $("#loader_gif_pending_2").css("display", "block");
        
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/dashboard/get_missing_serial_number_bookings',
            data: {date},
        }).done(function(data) {
            $('#table_2').children('tbody').html(data);
            $('#export_2').css('display', 'inline-block');
            $('#process_2').css('display', 'inline-block');
            $("#loader_gif_pending_2").css("display", "none");
        });
    }
    
    function get_data_3() {
        $('#export_3').css('display', 'none');
        $('#process_3').css('display', 'none');
        
        var date = $('#data_range_3').val();
        if(date == '' || date == null) {
            alert('Please select date.');
            return false;
        }        
        var dateArray = date.split(" - ");
        var startDate = dateArray[0];
        var endDate =   dateArray[1];
        var startDateObj = new Date(startDate);
        var endDateObj = new Date(endDate);
        var timeDiff = Math.abs(endDateObj.getTime() - startDateObj.getTime());
        var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24)); 
        
        if(diffDays>31){
            alert("Maximum range allowed is 31 days.");
            return false;
        }  
        
        $("#loader_gif_pending_3").css("display", "block");
        
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/dashboard/get_missing_partner_invoice',
            data: {date},
        }).done(function(data) {
            $('#table_3').children('tbody').html(data);
            $('#export_3').css('display', 'inline-block');
            $('#process_3').css('display', 'inline-block');
            $("#loader_gif_pending_3").css("display", "none");
        });
    
    }

    function get_data_4() {
        $('#export_4').css('display', 'none');
        $('#process_4').css('display', 'none');
        var date = $('#data_range_4').val();
        if(date == '' || date == null) {
            alert('Please select date.');
            return false;
        }        
        var dateArray = date.split(" - ");
        var startDate = dateArray[0];
        var endDate =   dateArray[1];
        var startDateObj = new Date(startDate);
        var endDateObj = new Date(endDate);
        var timeDiff = Math.abs(endDateObj.getTime() - startDateObj.getTime());
        var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24)); 
        
        if(diffDays>31){
            alert("Maximum range allowed is 31 days.");
            return false;
        }  
        
        $("#loader_gif_pending_4").css("display", "block");
        
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/dashboard/get_completed_booking_unit_details_of_pending_bookings',
            data: {date},
        }).done(function(data) {
            $('#table_4').children('tbody').html(data);
            $('#export_4').css('display', 'inline-block');
            $('#process_4').css('display', 'inline-block');
            $("#loader_gif_pending_4").css("display", "none");
        });
    
    }

    function get_data_5() {
        $('#export_5').css('display', 'none');
        $('#process_5').css('display', 'none');
        var date = $('#data_range_5').val();
        if(date == '' || date == null) {
            alert('Please select date.');
            return false;
        }        
        var dateArray = date.split(" - ");
        var startDate = dateArray[0];
        var endDate =   dateArray[1];
        var startDateObj = new Date(startDate);
        var endDateObj = new Date(endDate);
        var timeDiff = Math.abs(endDateObj.getTime() - startDateObj.getTime());
        var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24)); 
        
        if(diffDays>31){
            alert("Maximum range allowed is 31 days.");
            return false;
        }  
        
        $("#loader_gif_pending_5").css("display", "block");
        
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/dashboard/get_pending_booking_unit_details_of_completed_bookings',
            data: {date},
        }).done(function(data) {
            $('#table_5').children('tbody').html(data);
            $('#export_5').css('display', 'inline-block');
            $('#process_5').css('display', 'inline-block');
            $("#loader_gif_pending_5").css("display", "none");
        });
    
    }

    $('.export').on('click', function(data) {
        var tableId = $(this).data('id');
        var date = $('#data_range_'+tableId).val();
        if(date == '' || date == null) {
            alert('Please select date.');
            return false;
        }        
        
        window.open("<?php echo base_url(); ?>employee/dashboard/export_data?tableId="+tableId+"&date="+date, '_blank');
    });
    
    function checkall(classname) {
        var id = classname.split('_')[1];
        if($('.'+classname).prop("checked") == true){ 
            $('.check_'+id).prop("checked", true);
        } else {
            $('.check_'+id).prop("checked", false);
        }
    }
    
    $('.process').on('click', function(){
        
        var btn_id = $(this).attr('id');
        var id = $(this).attr('id').split('_')[1];
        var form_id = '';
        
        if($('.check_'+id).filter(':checked').length == 0) {
            alert("Please select at least one checkbox to proceed.");
            return false;
        }
        
        $('#'+btn_id).val('Please wait...');
        switch (id) {
            case '1':
                form_id = '<?php echo DASHBOARD_INVOICE_PROCESS_1; ?>';
            break;
            case '2':
                form_id = '<?php echo DASHBOARD_INVOICE_PROCESS_2; ?>';
            break;
            case '3':
                form_id = '<?php echo DASHBOARD_INVOICE_PROCESS_3; ?>';
            break;
            case '4':
                form_id = '<?php echo DASHBOARD_INVOICE_PROCESS_4; ?>';
            break;
            case '5':
                form_id = '<?php echo DASHBOARD_INVOICE_PROCESS_5; ?>';
            break;
        }

        $.ajax({
            method:'POST',
            url:'<?php echo base_url(); ?>employee/dashboard/process_invoice',
            data: $('#'+form_id).serialize()
        }).done(function() {
            alert("Invoice has been processed successfully.");
            $('#'+btn_id).val('Process');
            switch (id) {
                case '1':
                    get_data_1();
                break;
                case '2':
                    get_data_2();
                break;
                case '3':
                    get_data_3();
                break;
                case '4':
                    get_data_4();
                break;
                case '5':
                    get_data_5();
                break;
            }
        });
    });
    
    $(".collape_icon").click(function(){
     if($(this).find("i").hasClass("fa fa-plus-square")){ 
            $(this).find("i").removeClass("fa fa-plus-square"); 
            $(this).find("i").addClass("fa fa-minus-square");
        }
        else{ 
            $(this).find("i").removeClass("fa fa-minus-square"); 
            $(this).find("i").addClass("fa fa-plus-square");
        }
    });
</script>
