<script src="<?php echo base_url(); ?>js/base_url.js"></script>
<script src = "https://ajax.googleapis.com/ajax/libs/angularjs/1.3.14/angular.min.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/app.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/controller/controllers.js"></script>
<style>
    .collape_icon {
        font-size: 18px;
        color: #4b5561 !important;
        float:right;
    }
</style>
<!-- page content -->
<div class="right_col" role="main">
    <!-- top tiles -->
    <div class="row tile_count" id="title_count">
        <div class="col-md-12">
            <center><img id="loader_gif_title" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center>
        </div>
    </div>
    <!-- /top tiles -->
    <hr>
    <?php 
//    if($this->session->userdata('id') == INVENTORY_HANDLER_ID){
//    ?>
        <!-- SF Brackets snapshot Section -->
<!--    <div class="row" ng-app="inventory_dashboard">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <div class="col-md-6">
                        <h2>Service Center Brackets Snapshot</h2>
                    </div>
                    <div class="col-md-6">
                        <div class="pull-right">
                            <a class="btn btn-sm btn-success" href="//<?php //echo base_url();?>employee/dashboard/brackets_snapshot_full_view" target="_blank">Show All</a>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="table-responsive">
                        <div class="table-responsive" id="escalation_data" ng-controller="bracketsSnapshot_Controller" ng-cloak="">
                            <center><img id="brackets_loader" src="//<?php //echo base_url(); ?>images/loadring.gif"></center>
                            <div ng-if="brackets_div">
                                <table class="table table-striped table-bordered jambo_table bulk_action">
                                    <thead>
                                        <tr>
                                            <th>S.no</th>
                                            <th>Service Center Name</th>
                                            <th colspan="2">Current Stock</th>
                                            <th>Expected Days Left to Consume Brackets</th>
                                            <th>Order Brackets</th>
                                        </tr>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th>Less than 32"</th>
                                            <th>32" and above</th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-repeat="x in bracketsSnapshot | limitTo:quantity">
                                           <td>{{$index+1}}</td>
                                           <td>{{x.sf_name}}</td>
                                           <td>{{x.l_32}}</td>
                                           <td>{{x.g_32}}</td>
                                           <td>{{x.brackets_exhausted_days}}</td>
                                           <td><a class="btn btn-sm btn-success" href="//<?php //echo base_url();?>employee/inventory/get_bracket_add_form/{{x.sf_id}}/{{x.sf_name}}" target="_blank">Order brackets</a></td>
                                        </tr>
                                    </tbody>
                                </table>                             
                            </div>
                            <div ng-if="brackets_div_err_msg">
                                <p class="text-center text-danger">{{brackets_div_err_msg_text}}</p>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>-->
    <!-- SF Brackets Snapshot Section -->
    <?php
//        }
//    ?>
    
    <!-- Agent Graph -->
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="dashboard_graph">
                <div class="row x_title">
                    <div class="col-md-6">
                        <h3>Agent Booking Status &nbsp;&nbsp;&nbsp;
                            <small>
                            </small>
                        </h3>
                    </div>
                    <div class="col-md-5">
                        <div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; margin-right: -15%;">
                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                            <span></span> <b class="caret"></b>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <span class="collape_icon" href="#chart_container2_div" data-toggle="collapse" onclick="agent_daily_report_call()"><i class="fa fa-plus-square" aria-hidden="true"></i></span>
                    </div>
                </div>
                <div class="collapse" id="chart_container2_div">
                <div class="col-md-12">
                    <center><img id="loader_gif" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center>
                </div>
                <div id="chart_container2" class="chart_containe2" style="width:100%; height:400px;" ></div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <!-- End Agent Graph -->
    
    <!-- Closure Team Graph -->
    <div class="row" style="margin-top:10px;">
        <div class="col-md-6 col-sm-12 col-xs-12" id="completed_booking_closure_status" style="padding-right:0px !important; padding-left: 0px !important;">
            <div class="x_panel">
                <div class="x_title">
                    <div class="col-md-6" style="padding: 0px;"><h2>Review Completed Booking Status <button type="button"class="btn btn-default" style="margin-bottom: 10px;padding: 1px 4px;margin-top: 0px;font-size: 8px;margin-left: 5px;background: #f7a35c;
    color: #fff;border: none;" data-toggle="tooltip"data-placement="right"title="Total Completed Bookings = Rejected Bookings + Direct Approved Bookings + Approved with Edit Bookings">?</button></h2></div>
                    <div class="col-md-5">
                        <small>
                        <div class="nav navbar-right panel_toolbox">
                            <div id="reportrange5" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; margin-right: -10%;">
                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                <span></span> <b class="caret"></b>
                            </div>
                        </div>
                        </small>
                    </div>
                    <div class="col-md-1" style="padding-right: 0px;"><span class="collape_icon" href="#completed_booking_closure_chart_div" data-toggle="collapse" onclick="get_completed_bookings_data()"><i class="fa fa-plus-square" aria-hidden="true"></i></span></div>
                    <div class="clearfix"></div>
                </div>
                <div class="col-md-12">
                    <center><img id="loader_gif7" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center>
                </div>
                <div class="x_content collapse" id="completed_booking_closure_chart_div">
                    <div id="completed_booking_closure_chart"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-12 col-xs-12" id="completed_booking_closure_status" style="padding-right:0px !important;">
            <div class="x_panel">
                <div class="x_title">
                    <div class="col-md-6" style="padding: 0px;"><h2>Review Cancelled Booking Status <button type="button"class="btn btn-default" style="margin-bottom: 10px;padding: 1px 4px;margin-top: 0px;font-size: 8px;margin-left: 5px;background: #f7a35c;
    color: #fff;border: none;" data-toggle="tooltip"data-placement="right"title="Total Cancelled Bookings = Rejected Bookings + Direct Approved Bookings + Cancelled to Completed Bookings">?</button></h2></div>
                    <div class="col-md-5">
                        <small>
                        <div class="nav navbar-right panel_toolbox">
                            <div id="reportrange6" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; margin-right: -10%;">
                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                <span></span> <b class="caret"></b>
                            </div>
                        </div>
                        </small>
                    </div>
                    <div class="col-md-1" style="padding-right: 0px;"><span class="collape_icon" href="#cancelled_booking_closure_chart_div" data-toggle="collapse" onclick="get_cancelled_bookings_data()"><i class="fa fa-plus-square" aria-hidden="true"></i></span></div>
                    <div class="clearfix"></div>
                </div>
                <div class="col-md-12">
                    <center><img id="loader_gif8" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center>
                </div>
                <div class="x_content collapse" id="cancelled_booking_closure_chart_div">
                    <div id="cancelled_booking_closure_chart"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Closure Team Graph -->
    
    <div class="row" style="margin-top:10px;">
        <!-- Company Monthly Status -->
        <div class="col-md-6 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Monthly Booking Status <small>Completed</small></h2>
                        <span class="collape_icon" href="#monthly_booking_chart_div" data-toggle="collapse" onclick="around_monthly_data()"><i class="fa fa-plus-square" aria-hidden="true"></i></span>
                    <div class="clearfix"></div>
                </div>
                <div class="collapse" id="monthly_booking_chart_div">
                <div class="col-md-12">
                    <center><img id="loader_gif2" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center>
                </div>
                <div class="x_content">
                    <div id="monthly_booking_chart" style="width:100%; height:400px;" ></div>
                </div>
                </div>
            </div>
        </div>
        <!-- End Company Monthly Status -->
    </div>
    
    <!-- Modal -->
    <div id="modalDiv" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div id="open_model"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal -->
</div>
<!-- /page content -->
<!-- Chart Script -->
<script>
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
    
    $(function () {
        function cb(start, end) {
            $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }
    
        $('#reportrange').daterangepicker(options, cb);
    
        cb(start, end);
    
    });
    
    $('#reportrange').on('apply.daterangepicker', function (ev, picker) {
        var startDate = picker.startDate.format('YYYY-MM-DD');
        var endDate = picker.endDate.format('YYYY-MM-DD');
        agent_daily_report(startDate,endDate);
       
    });
    
    $(document).ready(function(){
        
        //top count data
        get_query_data();
        //company monthly data
        //around_monthly_data();
        //agent performance data
        //agent_daily_report(start.format('MMMM D, YYYY'), end.format('MMMM D, YYYY'));
        
    });
    
    
    function agent_daily_report_call(){ 
        agent_daily_report(start.format('MMMM D, YYYY'), end.format('MMMM D, YYYY'));
    }
    
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
            $('#go_to_crm').show();
        });
    }
    
    function around_monthly_data(){
        $('#loader_gif2').fadeIn();
        $('#monthly_booking_chart').fadeOut();
        var data = {partner_id:''};
        url =  '<?php echo base_url(); ?>employee/dashboard/get_bookings_data_by_month';
        
        sendAjaxRequest(data,url,post_request).done(function(response){
            get_mothly_booking_status(response,'1');
        });
    }
    
    function get_mothly_booking_status(response,chart_render_to){
        
        if(chart_render_to === '1'){
            render_div = 'monthly_booking_chart';
            $('#loader_gif2').hide();
        }else{
            render_div = 'monthly_unit_booking_chart';
            $('#loader_gif6').hide();
        }
        var data = JSON.parse(response);
        var month = data.month.split(',');
        var completed_booking = JSON.parse("[" + data.completed_booking + "]");
        $('#'+render_div).fadeIn();
        chart = new Highcharts.Chart({
            chart: {
                renderTo: render_div,
                type: 'column'
            },
            title: {
                text: '',
                x: -20 //center
            },
            xAxis: {
                categories: month
            },
            yAxis: {
                title: {
                    text: 'Count'
                },
                plotLines: [{
                        value: 0,
                        width: 1,
                        color: '#808080'
                    }]
            },
            plotOptions: {
                column: {
                    dataLabels: {
                        enabled: true,
                        crop: false,
                        overflow: 'none'
                    }
                }
            },
            series: [
                {
                    name: 'Completed Bookings',
                    data: completed_booking
                }]
        });
    }
    
    
    function agent_daily_report(startDate,endDate){
        var url =  '<?php echo base_url();?>BookingSummary/agent_working_details_ajax';
        $('#loader_gif').fadeIn();
        $('#chart_container2').hide();
        $.ajax({
            type: 'POST',
            url: url,
            data: {sDate: startDate, eDate: endDate},
            success: function (response) {
                $('#loader_gif').hide();
                $('#chart_container2').fadeIn();
                var data = JSON.parse(response);
                var agent_name = data.agent_name.split(',');
                var query_cancel = JSON.parse("[" + data.query_cancel + "]");
                var calls_placed = JSON.parse("[" + data.calls_placed + "]");
                var query_booking = JSON.parse("[" + data.query_booking + "]");
                var calls_received = JSON.parse("[" + data.calls_received + "]");
                var rating = JSON.parse("[" + data.rating + "]");
                chart1 = new Highcharts.Chart({
                    chart: {
                        renderTo: 'chart_container2',
                        type: 'column',
                        events: {
                            load: Highcharts.drawTable
                        }
                    },
                    title: {
                        text: '',
                        x: -20 //center
                    },
                    xAxis: {
                        categories: agent_name
                    },
                    yAxis: {
                        title: {
                            text: 'Count'
                        },
                        plotLines: [{
                                value: 0,
                                width: 1,
                                color: '#808080'
                            }]
                    },
                    plotOptions: {
                        column: {
                            dataLabels: {
                                enabled: true,
                                crop: false,
                                overflow: 'none'
                            }
                        }
                    },
                    legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'middle',
                        borderWidth: 0
                    },
                    series: [
                        {
                            name: 'Cancelled Queries',
                            data: query_cancel,
                        }, {
                            name: 'Booked Queries',
                            data: query_booking
                        },
                        {
                            name: 'Outgoing Calls',
                            data: calls_placed
                        }, {
                            name: 'Incoming Calls',
                            data: calls_received
                        }, {
                            name: 'Ratings',
                            data: rating
                        }]
                });
            }
        });
    
    }
    
    function get_completed_bookings_data(){
        $('#loader_gif7').fadeIn();
        $('#completed_booking_closure_chart').fadeOut();
        var data = {};
        url =  '<?php echo base_url(); ?>employee/dashboard/get_completed_cancelled_booking_by_closure/Completed';
        
        sendAjaxRequest(data,url,post_request).done(function(response){
            if(response){
                create_chart_closure_completed_booking(response);   
            }
            else{
                alert("Graph Data Not Found");
            }
        });
    }
    
    function create_chart_closure_completed_booking(response) {
        var data = JSON.parse(response);
        var closures = data.closures.split(',');
        var reject = JSON.parse("[" + data.reject + "]");
        var approved = JSON.parse("[" + data.approved + "]");
        var edit_complete = JSON.parse("[" + data.edit_complete + "]");
        var total_bookings = JSON.parse("[" + data.total_bookings + "]");
        $('#loader_gif7').hide();
        $('#completed_booking_closure_chart').fadeIn();
        rm_based_chart = new Highcharts.Chart({
            chart: {
                renderTo: 'completed_booking_closure_chart',
                type: 'column',
                events: {
                    load: Highcharts.drawTable
                }
            },
            title: {
                text: '',
                x: -20 //center
            },
            xAxis: {
                categories: closures
            },
            yAxis: {
                title: {
                    text: 'Count'
                },
                plotLines: [{
                        value: 0,
                        width: 1,
                        color: '#808080'
                    }]
            },
            plotOptions: {
                column: {
                    dataLabels: {
                        enabled: true,
                        crop: false,
                        overflow: 'none'
                    }
                }
            },
            series: [
                {
                    name: 'Rejected Bookings',
                    data: reject
                }, {
                    name: 'Directly Approved Bookings',
                    data: approved
                },
                {
                    name: 'Approved With Edit Bookings',
                    data: edit_complete
                }, {
                    name: 'Total Completed Bookings',
                    data: total_bookings
                }]
        });
    
    }
    
    function get_cancelled_bookings_data(){
        $('#loader_gif8').fadeIn();
        $('#cancelled_booking_closure_chart').fadeOut();
        var data = {};
        url =  '<?php echo base_url(); ?>employee/dashboard/get_completed_cancelled_booking_by_closure/Cancelled';
        
        sendAjaxRequest(data,url,post_request).done(function(response){
            //console.log(response);
            if(response){
                create_chart_closure_cancelled_booking(response);
            }
            else{
                alert("Graph Data Not Found");
            }
        });
    }
    
    function create_chart_closure_cancelled_booking(response) {
        var data = JSON.parse(response);
        var closures = data.closures.split(',');
        var reject = JSON.parse("[" + data.reject + "]");
        var approved = JSON.parse("[" + data.approved + "]");
        var edit_complete = JSON.parse("[" + data.edit_complete + "]");
        var total_bookings = JSON.parse("[" + data.total_bookings + "]");
        $('#loader_gif8').hide();
        $('#cancelled_booking_closure_chart').fadeIn();
        rm_based_chart = new Highcharts.Chart({
            chart: {
                renderTo: 'cancelled_booking_closure_chart',
                type: 'column',
                events: {
                    load: Highcharts.drawTable
                }
            },
            title: {
                text: '',
                x: -20 //center
            },
            xAxis: {
                categories: closures
            },
            yAxis: {
                title: {
                    text: 'Count'
                },
                plotLines: [{
                        value: 0,
                        width: 1,
                        color: '#808080'
                    }]
            },
            plotOptions: {
                column: {
                    dataLabels: {
                        enabled: true,
                        crop: false,
                        overflow: 'none'
                    }
                }
            },
            series: [
                {
                    name: 'Rejected Bookings',
                    data: reject
                }, {
                    name: 'Directly Approved Bookings',
                    data: approved
                },
                {
                    name: 'Cancelled to Completed Bookings',
                    data: edit_complete
                }, {
                    name: 'Total Cancelled Bookings',
                    data: total_bookings
                }]
        });
    
    }
    
    $(function () {
        function cb(start, end) {
            $('#reportrange5 span').html(moment().subtract(6, 'days').format('MMMM D, YYYY') + ' - ' + moment().format('MMMM D, YYYY'));
        }

        $('#reportrange5').daterangepicker(options, cb);

        cb(moment().subtract(6, 'days'), moment());
    });
    
    $(function () {
        function cb(start, end) {
            $('#reportrange6 span').html(moment().subtract(6, 'days').format('MMMM D, YYYY') + ' - ' + moment().format('MMMM D, YYYY'));
        }

        $('#reportrange6').daterangepicker(options, cb);

        cb(moment().subtract(6, 'days'), moment());
    });
    
    $('#reportrange5').on('apply.daterangepicker', function (ev, picker) {
        $('#loader_gif7').show();
        $('#completed_booking_closure_chart').hide();
        var startDate = picker.startDate.format('YYYY-MM-DD');
        var endDate = picker.endDate.format('YYYY-MM-DD');
        url = baseUrl + '/employee/dashboard/get_completed_cancelled_booking_by_closure/Completed';
        var data = {sDate: startDate, eDate: endDate};
        
        sendAjaxRequest(data,url,post_request).done(function(response){
            if(response){
                create_chart_closure_completed_booking(response);
            }
            else{
                alert("Graph Data Not Found");
            }
        });
    });
    
    $('#reportrange6').on('apply.daterangepicker', function (ev, picker) {
        $('#loader_gif8').show();
        $('#completed_booking_closure_chart').hide();
        var startDate = picker.startDate.format('YYYY-MM-DD');
        var endDate = picker.endDate.format('YYYY-MM-DD');
        url = baseUrl + '/employee/dashboard/get_completed_cancelled_booking_by_closure/Cancelled';
        var data = {sDate: startDate, eDate: endDate};
        
        sendAjaxRequest(data,url,post_request).done(function(response){
            if(response){
                create_chart_closure_cancelled_booking(response);
            }
            else{
                alert("Graph Data Not Found");
            }
        });
    });
</script>