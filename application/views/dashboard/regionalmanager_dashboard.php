<script src="<?php echo base_url(); ?>js/base_url.js"></script>
<script src = "https://ajax.googleapis.com/ajax/libs/angularjs/1.3.14/angular.min.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/app.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/controller/controllers.js"></script>
<style>
    .col-md-2 {
        width: 16.666667%;
    }
    .tile_count .tile_stats_count, ul.quick-list li {
        white-space: normal;
    }

    [ng\:cloak], [ng-cloak], .ng-cloak {
        display: none !important;
    }
</style>
<div class="right_col ngCloak" role="main" ng-app="rm_dashboard" ng-cloak="">


    <!-- top tiles -->
    <div class="row tile_count" id="title_count">
        <div class="col-md-12">
            <center><img id="loader_gif_title" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center>
        </div>
    </div>
    <!-- /top tiles -->
                <div class="x_panel">
                <div class="x_title">
                    <h2>RM Completed Booking Reports</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="table-responsive" id="escalation_data" ng-controller="completedBooking_Controller" ng-cloak="">
                                    <div class="form-group" style="float:right;">
                                         <label for="">Booking Completed Date Range</label>
                                         <input type="text" class="form-control" name="daterange_completed_bookings" id="completed_daterange_id" ng-change="ShowRMCompletedBookingBYDateRange()" ng-model="dates">
                            </div>
                <br>
                <div class="clear"></div>
                <table class="table table-striped table-bordered jambo_table bulk_action">
                    <thead>
                        <tr>
                            <th>S.no</th>
                            <th>RM</th>
                            <th>D0</th>
                            <th>D1</th>
                            <th>D2</th>
                            <th>D3</th>
                            <th>D4</th>
                            <th>D5 - D7</th>
                             <th>D8 - D15</th>
                             <th>> D15</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat="x in completedBookingByRM | orderBy:'TAT_16'">
                           <td>{{$index+1}}</td>
                           <td><a type="button" id="vendor_{{x.id}}" class="btn btn-info" target="_blank" href="<?php echo base_url(); ?>employee/dashboard/tat_calculation_full_view/{{x.id}}">{{x.RM}}</a></td>
                           <td>{{x.TAT_0}} <br> ({{x.TAT_0_per}}%) </td>
                           <td>{{x.TAT_1}} <br> ({{x.TAT_1_per}}%) </td>
                           <td>{{x.TAT_2}} <br> ({{x.TAT_2_per}}%)</td>
                           <td>{{x.TAT_3}} <br> ({{x.TAT_3_per}}%)</td>
                           <td>{{x.TAT_4}} <br> ({{x.TAT_4_per}}%)</td>
                           <td>{{x.TAT_5}} <br> ({{x.TAT_5_per}}%) </td>
                           <td>{{x.TAT_8}} <br> ({{x.TAT_8_per}}%)</td>
                           <td>{{x.TAT_16}} <br> ({{x.TAT_16_per}}%)</td>
                        </tr>
                    </tbody>
                </table>
                <center><img id="loader_gif_pending" src="<?php echo base_url(); ?>images/loadring.gif" ></center>
            </div>
    <!-- Booking Report Start-->
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>RM Booking Report</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="table-responsive" id="escalation_data" ng-controller="pendngBooking_Controller" ng-cloak="">
                    <table class="table table-striped table-bordered jambo_table bulk_action">
                        <thead>
                            <tr>
                                <th>S.no</th>
                                <th>RM</th>
                                <th>0 to 2 days(Installation)</th>
                                <th>3 to 5 Days(Installation)</th>
                                <th> >5 Days(Installation)</th>
                                <th>0 to 2 days(Repair)</th>
                                <th>3 to 5 Days(Repair)</th>
                                <th>>5 Days(Repair)</th>
                                <th>Total Pending</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="x in pendingBookingByRM| orderBy:'-total_pending'">
                                <td>{{$index + 1}}</td>
                                <td><a type="button" id="vendor_{{x.rmID}}" class="btn btn-info" target="_blank" href="<?php echo base_url(); ?>employee/dashboard/pending_full_view_by_sf/{{x.rmID}}">{{x.rm}}</a></td>
                                <td>{{x.last_2_day_installation_booking_count}}</td>
                                <td class="text-danger">{{x.last_3_to_5_days_installation_count}}</td>
                                <td class="text-danger">{{x.more_then_5_days_installation_count}}</td>
                                <td>{{x.last_2_day_repair_booking_count}}</td>
                                <td class="text-danger">{{x.last_3_to_5_days_repair_count}}</td>
                                <td class="text-danger">{{x.more_then_5_days_repair_count}}</td>
                                <td>{{x.total_pending}}</td>
                            </tr>
                        </tbody>
                    </table>
                    <center><img id="loader_gif_pending" src="<?php echo base_url(); ?>images/loadring.gif" ></center>
                </div>
            </div>
        </div>
    </div>
    <!-- Booking Report End-->
    <div class="row" ng-controller="rm_dashboardController" ng-cloak="">
        <?php
        if ($this->session->userdata("wrong_pincode_msg")) {
            echo "<p style='color: green;text-align: center;font-size: 18px;'>" . $this->session->userdata("wrong_pincode_msg") . "</p>";
            if ($this->session->userdata('wrong_pincode_msg')) {
                $this->session->unset_userdata('wrong_pincode_msg');
            }
        }
        ?>
        <div class="clearfix"></div>
        <div class="col-md-12 col-sm-12 col-xs-12">
<!--            <div class="x_panel">
                <div class="x_title">
                    <h2>Missing Pincodes</h2>
                    <a id="download_pin_code" class="btn btn-success" href="<?php echo base_url(); ?>employee/vendor/insert_pincode_form" style="float:right">Add New Pincode</a>
                    <div class="clearfix"></div>
                </div>

                <div class="x_content">

                    <div class="table-responsive" id="pincode_table_data">
                        <center><img id="loader_gif_unit" src="<?php echo base_url(); ?>images/loadring.gif" ></center>
                    </div>
                </div>
                <a class="btn btn-success"  href="<?php echo base_url() ?>employee/dashboard/missing_pincode_full_view" target="_blank" style="float: right;">Full View</a>
            </div>-->
 <!-- Missing Pincode Section -->
    <div class="row" style="margin-top:10px;">
        <?php
if($this->session->userdata("wrong_pincode_msg")){
    echo "<p style='color: green;text-align: center;font-size: 18px;'>".$this->session->userdata("wrong_pincode_msg")."</p>";
    if($this->session->userdata('wrong_pincode_msg')){$this->session->unset_userdata('wrong_pincode_msg');}
}
?>
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Missing Pincodes</h2>
                    <a id="download_pin_code" class="btn btn-success" href="<?php echo base_url(); ?>employee/vendor/insert_pincode_form" style="float:right">Add New Pincode</a>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="table-responsive" id="pincode_table_data">
                        <center><img id="pincode_loader" src="<?php echo base_url(); ?>images/loadring.gif"></center>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Missing Pincode Section -->
        </div>
        <div class="clearfix"></div>
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Escalation</h2>
                    <div class="clearfix"></div>
                </div>

                <div class="x_content">

                    <div class="table-responsive" id="escalation_data">
                        <input type="text" id="session_id_holder" style="display:none;" value="<?php if ($this->session->userdata('user_group') == 'regionalmanager') {
            echo $this->session->userdata('id');
        } ?>">
                        <button type="button"class="btn btn-default" style="float:right" data-toggle="tooltip"data-placement="left"title="To calculate escalation percentage, logic use current months booking and current month escalation ">?</button>
                        <button type="button" class="btn btn-info" ng-click="mytoggle = !mytoggle" id="order_by_toggal" onclick="change_toggal_text()"style="float:right">Sort By Number Of Escalation</button>
                        <form class="form-inline"style="float:left;background: #46b8da;color: #fff;padding: 3px;border-radius: 4px;">
                            <div class="form-group">
                                <input type="text" class="form-control" name="daterange" id="daterange_id" ng-change="daterangeloadFullRMView()" ng-model="dates">
                            </div>
                        </form>
                        <p>
                        <table class="table table-striped table-bordered jambo_table bulk_action">
                            <thead>
                                <tr>
                                    <th>S.no</th>
                                    <th>Vendor</th>
                                    <th>Total Booking</th>
                                    <th>Total Escalation</th>
                                    <th>Escalation %</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="y in escalationAllRMData|orderBy:!mytoggle?'-esclation_per':'-total_escalation'" ng-cloak="">
                                    <td>{{$index + 1}}</td>
                                    <td><a type="button" id="vendor_{{y.vendor_id}}" class="btn btn-info" target="_blank" href="<?php echo base_url(); ?>employee/dashboard/escalation_full_view/{{y.rm_id}}/{{y.startDate}}/{{y.endDate}}">{{y.rm_name}}</a></td>
                                    <td>{{y.total_booking}}</td>
                                    <td>{{y.total_escalation}}</td>
                                    <td>{{y.esclation_per}}%</td>
                                </tr>
                            </tbody>
                        </table>
                        <center><img id="loader_gif_escalation" src="<?php echo base_url(); ?>images/loadring.gif" ></center>
                    </div>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <!--<div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Escalation</h2>
                            <div class="clearfix"></div>
                        </div>
        
                        <div class="x_content">
        
        <div class="table-responsive" id="pending_booking_data">
            <button type="button" class="btn btn-info" ng-click="pendingBookingToggle=!pendingBookingToggle" value="Sort By 3 days" id="order_by_toggal" onclick="change_toggal_text(this.value,'Sort By More than 5 days')"style="float:right">Sort By 3 days</button>
            <p>
            <table class="table table-striped table-bordered jambo_table bulk_action">
            <thead>
              <tr>
                <th>S.no</th>
                <th>Vendor</th>
                <th>Pending Bookings More Than 5 Days</th>
                <th>Pending Bookings More than 3 days</th>
                <th>Monthly Cancelled Bookings</th>
                <th>Monthly Completed Bookings</th>
              </tr>
            </thead>
            <tbody>
                <tr ng-repeat="y in pendingBookingData |orderBy:!pendingBookingToggle?'-greater_than_5_days':'-last_3_day' | limitTo:5">
                <td>{{$index+1}}</td>
                <td>{{y.name}}</td>
                <td>{{y.greater_than_5_days}}</td>
                <td>{{y.last_3_day}}</td>
                <td>{{y.month_cancelled}}</td>
                <td>{{y.monthly_completed}}</td>
              </tr>
            </tbody>
            </table>
                            <center><img id="loader_gif_unit_escalation" src="<?php echo base_url(); ?>images/loadring.gif" ></center>
                            </div>
                        </div>
                        <div class="full_view_form_container" style="float:right;">
                        <form method="post" action="<?php echo base_url() ?>employee/dashboard/escalation_full_view" target="_blank">
                            <input type="text"  id="sf_json_data" name="sf_json_data" value="apple" style="display:none;"/>
                            <button type="submit" class="btn btn-success">Full View</button>
                            </form>
                            </div>
                    </div>
                </div>-->
    </div>
    <!-- SF Brackets snapshot Section -->
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <div class="col-md-6">
                        <h2>Service Center Brackets Snapshot</h2>
                    </div>
                    <div class="col-md-6">
                        <div class="pull-right">
                            <a class="btn btn-sm btn-success" href="<?php echo base_url();?>employee/dashboard/brackets_snapshot_full_view" target="_blank">Show All</a>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="table-responsive">
                        <div class="table-responsive" id="escalation_data" ng-controller="bracketsSnapshot_Controller" ng-cloak="">
                            <center><img id="brackets_loader" src="<?php echo base_url(); ?>images/loadring.gif"></center>
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
                                           <td><a class="btn btn-sm btn-success" href="<?php echo base_url();?>employee/inventory/get_bracket_add_form/{{x.sf_id}}/{{x.sf_name}}" target="_blank">Order brackets</a></td>
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
    </div>
    <!-- SF Brackets Snapshot Section -->
    <div class="row" style="margin-top:10px;">
        <!-- Company Monthly Status -->
        <div class="col-md-6 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Monthly Booking Status <small>Completed</small></h2>
                    <div class="clearfix"></div>
                </div>
                <div class="col-md-12">
                    <center><img id="loader_gif2" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center>
                </div>
                <div class="x_content">
                    <div id="monthly_booking_chart" style="width:100%; height:400px;" ></div>
                </div>
            </div>
        </div>
        <!-- End Company Monthly Status -->
        <!-- RM wise booking status -->
        <div class="col-md-6 col-sm-12 col-xs-12" id="based_on_Region">
            <div class="x_panel">
                <div class="x_title">
                    <h2 style="font-size:15px;">Booking based on Region <small></small></h2>
                    <div class="nav navbar-right panel_toolbox">
                        <div id="reportrange2" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc">
                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                            <span></span> <b class="caret"></b>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="col-md-12">
                    <center><img id="loader_gif3" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center>
                </div>
                <div class="x_content">
                    <div id="state_type_booking_chart"></div>
                </div>
            </div>
        </div>
        <!-- RM wise booking status -->
    </div>
</div>
<!-- END -->
<style>
    .dropdown:hover .dropdown-menu {
        display: block;
    }
</style>
<script>
    var post_request = 'POST';
    var get_request = 'GET';
    var url = '';
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
    $(document).ready(function () {
        //top count data
        get_query_data();
        //company monthly data
        around_monthly_data();
        //Rm wise bookings data
        get_bookings_data_by_rm();
         //missing pincode data
        get_missing_pincodes();
    });
    function get_missing_pincodes(){
        var data = {};
        url = '<?php echo base_url(); ?>employee/dashboard/get_pincode_not_found_sf_details_admin';
        data['partner_id'] = '';
        
        sendAjaxRequest(data,url,post_request).done(function(response){ 
            $("#pincode_table_data").html(response);
        });
    }
    
    $(function () {
        function cb(start, end) {
            $('#reportrange2 span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }

        $('#reportrange2').daterangepicker(options, cb);

        cb(start, end);

    });
    
    $('#reportrange2').on('apply.daterangepicker', function (ev, picker) {
        $('#loader_gif3').show();
        $('#state_type_booking_chart').hide();
        var startDate = picker.startDate.format('YYYY-MM-DD');
        var endDate = picker.endDate.format('YYYY-MM-DD');
        url = baseUrl + '/employee/dashboard/get_booking_data_by_region/1';
        var data = {sDate: startDate, eDate: endDate};
        
        sendAjaxRequest(data,url,post_request).done(function(response){
            create_chart_based_on_bookings_state(response);
        });
    });

    $(function () {
        var d = new Date();
        n = d.getMonth() + 1;
        y = d.getFullYear();
        date = d.getDate();
        $('input[name="daterange"]').daterangepicker({
            timePicker: true,
            timePickerIncrement: 30,
            locale: {
                format: 'YYYY-MM-DD'
            },
            startDate: y + '-' + n + '-01'
        });
    });


    function change_toggal_text() {
        var currentValue = document.getElementById("order_by_toggal").innerHTML;
        if (currentValue == "Sort By Number Of Escalation") {
            document.getElementById("order_by_toggal").innerHTML = "Sort BY perentage";
        } else {
            document.getElementById("order_by_toggal").innerHTML = "Sort By Number Of Escalation";
        }
    }

    function get_query_data() {
        $('#loader_gif_title').fadeIn();
        var data = {};
        url = '<?php echo base_url(); ?>employee/dashboard/execute_title_query';

        sendAjaxRequest(data, url, post_request).done(function (response) {
            $('#loader_gif_title').hide();
            $('#title_count').html(response);
        });
    }

    function sendAjaxRequest(postData, url, type) {
        return $.ajax({
            data: postData,
            url: url,
            type: type
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
    
    function get_bookings_data_by_rm(){
        $('#loader_gif3').fadeIn();
        $('#state_type_booking_chart').fadeOut();
        var data = {};
        url =  '<?php echo base_url(); ?>employee/dashboard/get_booking_data_by_region';
        
        sendAjaxRequest(data,url,post_request).done(function(response){
            create_chart_based_on_bookings_state(response);
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
    
    function create_chart_based_on_bookings_state(response) {
        var data = JSON.parse(response);
        var rm = data.rm.split(',');
        var cancelled = JSON.parse("[" + data.cancelled + "]");
        var completed = JSON.parse("[" + data.completed + "]");
        var pending = JSON.parse("[" + data.pending + "]");
        var total = JSON.parse("[" + data.total + "]");
        $('#loader_gif3').hide();
        $('#state_type_booking_chart').fadeIn();
        rm_based_chart = new Highcharts.Chart({
            chart: {
                renderTo: 'state_type_booking_chart',
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
                categories: rm
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
                    name: 'Cancelled Bookings',
                    data: cancelled,
                }, {
                    name: 'Completed Bookings',
                    data: completed
                },
                {
                    name: 'Pending Bookings',
                    data: pending
                }, {
                    name: 'Total Bookings',
                    data: total
                }]
        });
    
    }
    $(function() {
     var d = new Date();
        n = d.getMonth();
        y = d.getFullYear();
        date = d.getDate();
        $('input[name="daterange_completed_bookings"]').daterangepicker({
             timePicker: true,
        timePickerIncrement: 30,
        locale: {
            format: 'YYYY-MM-DD'
        },
        startDate: y+'-'+n+'-'+date
    });
});
</script>

