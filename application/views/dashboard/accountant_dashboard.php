<script src="<?php echo base_url(); ?>js/base_url.js"></script>
<script src = "https://ajax.googleapis.com/ajax/libs/angularjs/1.3.14/angular.min.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/app.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/controller/controllers.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>css/jquery.loading.css">
<script src="<?php echo base_url(); ?>js/jquery.loading.js"></script>
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
<!-- page content -->
<div class="right_col ngCloak" role="main" ng-app="admin_dashboard">
    <!-- top tiles -->
    <div class="row tile_count" id="title_count">
        <div class="col-md-12">
            <center><img id="loader_gif_title" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center>
        </div>
    </div>
    <!-- /top tiles -->
    <hr>
    <!-- Invoice Dashboard-->
        <div class="row">
        <form action="<?php echo base_url(); ?>employee/invoice/download_dashboard_invoice_data" method="post">  
            <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0px !important;">
                <div class="dashboard_graph">
                    <div class="row x_title">
                        <div class="col-md-6">
                            <h3>Invoice Summary Details &nbsp;&nbsp;&nbsp;
                                <small>
                                </small>
                            </h3>
                        </div>
                        <div class="col-md-5">
                            <input type="hidden" value="<?php echo date("Y-m-01"); ?>" name="sDate" id="sDate">
                            <input type="hidden" value="<?php echo date("Y-m-d"); ?>" name="eDate" id="eDate">
                            
                            <div id="action_total_invoice" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; margin-right: -12%;">
                                 <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>

                                <span></span> <b class="caret"></b>
                            </div>
                            
                            <!--Adding Button to Download invoice data in Excel-->
                                <button type="submit" class="btn btn-success btn-xs" style="margin-left:5px;float:right;padding: 6px 10px;" title="Download Invoice Details">Download Excel</button>

                        </div>
                        <div class="col-md-1">
                            <span class="collape_icon" href="#chart_container_partner_total_booking_div" data-toggle="collapse" onclick="dashboard_total_invoice_data()" style="margin-right: 8px;"><i class="fa fa-plus-square" aria-hidden="true"></i></span>
                        </div>
                    </div>
                    <div class="x_content collapse" id="chart_container_partner_total_booking_div">
                        <div class="col-md-12">
                            <center><img id="loader_gif_total_invoice" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center>
                        </div>
                        <div id="chart_total_invoice_div" class="chart_total_invoice_div" style="width:100%; height:100%;">

                             <div class="model-table">
                                <table class="table table-bordered table-hover table-striped" id="invoice_datatable" style="text-align:right;">
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </form>
    </div>
    
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0px !important;">
            <div class="dashboard_graph">
                <div class="row x_title">
                    <div class="col-md-11">
                        <h3>Generate Sale Invoice &nbsp;&nbsp;&nbsp;
                        </h3>
                    </div>
                    <div class="col-md-1">
                            <span class="collape_icon" href="#accountant_dashboard_generate_sale_invoice" data-toggle="collapse" onclick="bring_generate_sale_invoice_view()" style="margin-right: 8px;"><i class="fa fa-plus-square" aria-hidden="true"></i></span>
                        </div>
                </div>
                <div class="x_content collapse" id='accountant_dashboard_generate_sale_invoice'>

                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>

</div>

<script>

function bring_generate_sale_invoice_view() {
	url = '<?php echo base_url(); ?>employee/inventory/spare_invoice_list';
	$.ajax({
            method: 'POST',
            url: url,
            data: { dashboard : 'account_dashboard' },
            beforeSend: function () {
                $("#accountant_dashboard_generate_sale_invoice").html("<center><img src='<?php echo base_url(); ?>images/loadring.gif' style=></center>");
            },
            success: function (data) {
                    $("#accountant_dashboard_generate_sale_invoice").html(data);
            }
	});
}
</script>

<!-- /page content -->
<!-- Chart Script -->
<script>
    function getCurrentFinancialYear(strDate) {
        var startYear = "";
        var endYear = "";
        var docDate = new Date(strDate);
        if ((docDate.getMonth() + 1) <= 3) {
          startYear = docDate.getFullYear() - 1;
          endYear = docDate.getFullYear();
        } else {
          startYear = docDate.getFullYear();
          endYear = docDate.getFullYear() + 1;
        }
        var start_date = new Date(startYear + "-04-01");
        var end_date = new Date(endYear + "-03-31");
        return {startDate : start_date, endDate: end_date };
      }
var post_request = 'POST';
    var get_request = 'GET';
    var url = '';
    var partner_name = [];
    var completed_booking = [];
    var partner_id = [];
    var change_chart_data = [];
    var partner_name = [];
    var completed_booking = [];
    var partner_id = [];
    var change_chart_data = [];
    var start = moment().startOf('month');
    var end = moment().endOf('month');
    var start_week = moment().subtract(6, 'days');
    var end_week = moment();
    var current_financial_year = getCurrentFinancialYear(start);
    var current_financial_year_start_date = current_financial_year.startDate;
    var current_financial_year_end_date = current_financial_year.endDate;
    var options = {
            startDate: start,
            endDate: end,
            minDate: '01/01/2000',
            maxDate: '12/31/2030',
            dateLimit: {
                days: 120},
            showDropdowns: true,
            showWeekNumbers: true,
            timePicker: false,
            timePickerIncrement: 1, timePicker12Hour: true,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            opens: 'left',
            buttonClasses: ['btn btn-default'],
            applyClass: 'btn-small btn-primary',
            cancelClass: 'btn-small',
            format: 'MM/DD/YYYY', separator: ' to ',
            locale: {
                applyLabel: 'Submit',
                cancelLabel: 'Clear',
                fromLabel: 'From',
                toLabel: 'To',
                customRangeLabel: 'Custom',
                daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                firstDay: 1
            }
    };
    
     var options_week = {
            startDate: start_week,
            endDate: end_week,
            minDate: '01/01/2000',
            maxDate: '12/31/2030',
            dateLimit: {
                days: 30},
            showDropdowns: true,
            showWeekNumbers: true,
            timePicker: false,
            timePickerIncrement: 1, timePicker12Hour: true,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            opens: 'left',
            buttonClasses: ['btn btn-default'],
            applyClass: 'btn-small btn-primary',
            cancelClass: 'btn-small',
            format: 'MM/DD/YYYY', separator: ' to ',
            locale: {
                applyLabel: 'Submit',
                cancelLabel: 'Clear',
                fromLabel: 'From',
                toLabel: 'To',
                customRangeLabel: 'Custom',
                daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                firstDay: 1
            }
    };
    
    var options_year = {
            startDate: start,
            endDate: end,
            minDate: '01/01/2000',
            maxDate: '12/31/2030',
            dateLimit: {
                days: 366},
            showDropdowns: true,
            showWeekNumbers: true,
            timePicker: false,
            timePickerIncrement: 1, timePicker12Hour: true,
            ranges: {
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Current FY': [moment(current_financial_year_start_date), moment(current_financial_year_end_date)],
                'Last FY': [moment(current_financial_year_start_date).subtract(1, 'years'), moment(current_financial_year_end_date).subtract(1, 'years')],
                '2nd Last FY': [moment(current_financial_year_start_date).subtract(2, 'years'), moment(current_financial_year_end_date).subtract(2, 'years')],
                '3rd Last FY': [moment(current_financial_year_start_date).subtract(3, 'years'), moment(current_financial_year_end_date).subtract(3, 'years')],
                '4th Last FY': [moment(current_financial_year_start_date).subtract(4, 'years'), moment(current_financial_year_end_date).subtract(4, 'years')],
                '5th Last FY': [moment(current_financial_year_start_date).subtract(5, 'years'), moment(current_financial_year_end_date).subtract(5, 'years')]
            },
            opens: 'left',
            buttonClasses: ['btn btn-default'],
            applyClass: 'btn-small btn-primary',
            cancelClass: 'btn-small',
            format: 'MM/DD/YYYY', separator: ' to ',
            locale: {
                applyLabel: 'Submit',
                cancelLabel: 'Clear',
                fromLabel: 'From',
                toLabel: 'To',
                customRangeLabel: 'Custom',
                daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                firstDay: 1
            }
    };
    $(document).ready(function(){
        $('[data-toggle="popover"]').popover({
            placement : 'top',
            trigger : 'hover'
        });
        
        //top count data
        get_query_data();       
    });
    
    $('#action_total_invoice').on('apply.daterangepicker', function (ev, picker) {
        $('#loader_gif_total_invoice').show();
        $('#chart_total_invoice_div').hide();
        var startDate = picker.startDate.format('YYYY-MM-DD');
        var endDate = picker.endDate.format('YYYY-MM-DD');
        set_invoice_date_values(startDate, endDate);
        dashboard_click_total_invoice_data();
        
    });
    
    $(function () {
        function cb(start, end) {
            $('#action_total_invoice span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }

        $('#action_total_invoice').daterangepicker(options_year, cb);

        cb(start, end);
    });
    
    
    function sendAjaxRequest(postData, url,type) {
        return $.ajax({
            data: postData,
            url: url,
            type: type
        });
    }
    
    
    function get_query_data(){
        $('#loader_gif_title').fadeIn();
        var data = {};
        url = '<?php echo base_url(); ?>employee/dashboard/execute_title_query';
        
        sendAjaxRequest(data,url,post_request).done(function(response){
            $('#loader_gif_title').hide();
            $('#title_count').html(response);
        });
    }
    
    //set values of start date and end date in input fields
    function set_invoice_date_values(startDate, endDate){
        $("#sDate").val(startDate);
        $("#eDate").val(endDate);
    }
    
    function dashboard_click_total_invoice_data(){
        url = baseUrl + '/employee/invoice/get_dashboard_invoice_data';
        $('#loader_gif_total_invoice').show();
        $("#invoice_datatable tbody").html("");
        $('#chart_total_invoice_div').fadeIn();
        $.ajax({
           type: 'POST',
           url: url,
           data: {sDate : $("#sDate").val(), eDate : $("#eDate").val()}
         })
         .done (function(data) { 
            $('#loader_gif_total_invoice').hide();
            $("#invoice_datatable tbody").html(data);
         })
         .fail(function(jqXHR, textStatus, errorThrown){
             $('#loader_gif_total_invoice').hide();
             alert("Something went wrong while loading invoice!");
          })
    }
    
    //this function is used to get invoice data
    function dashboard_total_invoice_data(){
        set_invoice_date_values(moment().startOf('month').format('MMMM D, YYYY'), moment().endOf('month').format('MMMM D, YYYY'));
        dashboard_click_total_invoice_data();
        
    }
</script>
<style>
.text_warning{
        color:red;
    }
    [ng\:cloak], [ng-cloak], .ng-cloak {
  display: none !important;
}
select option:empty { display:none }
.select2-container--default{
        width: 154px !important;
}
.select2-selection--multiple{
        border: 1px solid #ccc !important;
    border-radius: 0px !important;
}
</style>
