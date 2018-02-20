<script src="<?php echo base_url(); ?>js/base_url.js"></script>
<script src = "https://ajax.googleapis.com/ajax/libs/angularjs/1.3.14/angular.min.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/app.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/controller/controllers.js"></script>
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
    <!-- Booking Report Start-->
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
     <tr ng-repeat="x in pendingBookingByRM | orderBy:'-total_pending'">
        <td>{{$index+1}}</td>
        <td><a type="button" id="vendor_{{x.rmID}}" class="btn btn-info" target="_blank" href="<?php echo base_url(); ?>employee/dashboard/pending_full_view_by_sf/{{x.rmID}}">{{x.rm}}</a></td>
        <td>{{x.last_2_day_installation_booking_count}}</td>
        <td class="text_warning">{{x.last_3_to_5_days_installation_count}}</td>
        <td class="text_warning">{{x.more_then_5_days_installation_count}}</td>
        <td>{{x.last_2_day_repair_booking_count}}</td>
         <td class="text_warning">{{x.last_3_to_5_days_repair_count}}</td>
        <td class="text_warning">{{x.more_then_5_days_repair_count}}</td>
        <td>{{x.total_pending}}</td>
      </tr>
    </tbody>
    </table>
    <center><img id="loader_gif_pending" src="<?php echo base_url(); ?>images/loadring.gif" ></center>
                    </div>
    </div>
    </div>
    <!-- Booking Report End-->
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
    <!-- Escalation Start-->
    <div class="table-responsive" id="escalation_data" ng-controller="admin_escalationController" ng-cloak="">
    <input type="text" id="session_id_holder" style="display:none;" value="<?php if($this->session->userdata('user_group') == 'regionalmanager') {echo $this->session->userdata('id');} ?>">
   <button type="button"class="btn btn-default" style="float:right" data-toggle="tooltip"data-placement="left"title="To calculate escalation percentage, logic use current months booking and current month escalation ">?</button>
    <button type="button" class="btn btn-info" ng-click="mytoggle=!mytoggle" id="order_by_toggal" onclick="change_toggal_text()"style="float:right">Sort By Number Of Escalation</button>
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
     <tr ng-repeat="y in escalationAllRMData  |orderBy:!mytoggle?'-esclation_per':'-total_escalation'">
        <td>{{$index+1}}</td>
        <td><a type="button" id="vendor_{{y.vendor_id}}" class="btn btn-info" target="_blank" href="<?php echo base_url(); ?>employee/dashboard/escalation_full_view/{{y.rm_id}}/{{y.startDate}}/{{y.endDate}}">{{y.rm_name}}</a></td>
        <td>{{y.total_booking}}</td>
        <td>{{y.total_escalation}}</td>
         <td>{{y.esclation_per}}%</td>
      </tr>
    </tbody>
    </table>
    <center><img id="loader_gif_escalation" src="<?php echo base_url(); ?>images/loadring.gif" ></center>
                    </div>
    <!-- Escalation End-->
    <!-- Partner Booking Status -->
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="dashboard_graph">
                <div class="row x_title">
                    <div class="col-md-6">
                        <h3>Partner Booking Status &nbsp;&nbsp;&nbsp;</h3>
                    </div>
                    <div class="col-md-6">
                        <div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc">
                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                            <span></span> <b class="caret"></b>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <center><img id="loader_gif1" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center>
                </div>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div id="chart_container" class="chart_container"></div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <!-- End Partner Booking Status -->
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
                    <h2>Booking based on Region <small></small></h2>
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
                    <div class="col-md-6">
                        <div id="reportrange3" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc">
                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                            <span></span> <b class="caret"></b>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <center><img id="loader_gif4" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center>
                </div>
                <div id="chart_container2" class="chart_containe2"></div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <!-- Agent Graph -->
    
    <!-- show more content -->
    <div class="row"  style="margin-top:20px;">
        <div id="show_more" style="display:none;"> 
            <div class="col-md-6 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Partner Unit Details</h2>
                        <div class="nav navbar-right panel_toolbox">
                            <div id="reportrange4" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc">
                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                <span></span> <b class="caret"></b>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="col-md-12">
                        <center><img id="loader_gif5" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center>
                    </div>
                    <div class="x_content">
                        <div id="unit_chart"></div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Monthly Booking Unit Status <small>Completed</small></h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="col-md-12">
                        <center><img id="loader_gif6" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center>
                    </div>
                    <div class="x_content">
                        <div id="monthly_unit_booking_chart"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end show more content -->
    
    <!-- show more button -->
    <div class="row">
        <center>
            <div class="btn btn-success" id="show_more_btn" style="margin: 20px;">Show More</div>
        </center>
    </div>
    <!-- End show more button -->
    
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
    
    $(function () {
        function cb(start, end) {
            $('#reportrange2 span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }

        $('#reportrange2').daterangepicker(options, cb);

        cb(start, end);

    });
    
    $(function () {
        function cb(start, end) {
            $('#reportrange3 span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }
    
        $('#reportrange3').daterangepicker(options, cb);
    
        cb(start, end);
    
    });
    
    $(function () {
        function cb(start, end) {
            $('#reportrange4 span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }

        $('#reportrange4').daterangepicker(options, cb);

        cb(start, end);

    });
    
    $('#reportrange').on('apply.daterangepicker', function (ev, picker) {
        var startDate = picker.startDate.format('YYYY-MM-DD');
        var endDate = picker.endDate.format('YYYY-MM-DD');
        partner_booking_status(startDate,endDate);
       
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
    
    $('#reportrange3').on('apply.daterangepicker', function (ev, picker) {
        var startDate = picker.startDate.format('YYYY-MM-DD');
        var endDate = picker.endDate.format('YYYY-MM-DD');
        agent_daily_report(startDate,endDate);
    });
    
    $('#reportrange4').on('apply.daterangepicker', function (ev, picker) {
        var startDate = picker.startDate.format('YYYY-MM-DD');
        var endDate = picker.endDate.format('YYYY-MM-DD');
        partner_unit_chart(startDate, endDate);
    });
    
    
    $(document).ready(function(){
        
        //top count data
        get_query_data();
        //missing pincode data
        get_missing_pincodes();
        //partner booking data
        partner_booking_status(start.format('MMMM D, YYYY'), end.format('MMMM D, YYYY'));
        //company monthly data
        around_monthly_data();
        //Rm wise bookings data
        get_bookings_data_by_rm();
        //agent performance data
        agent_daily_report(start.format('MMMM D, YYYY'), end.format('MMMM D, YYYY'));
        
    });
    
    //show next grapgh when show more button clicked
    $("#show_more_btn").click(function(){
       $('#show_more').fadeIn();
       //partner wise monthly booking unit data
       partner_unit_chart(start.format('MMMM D, YYYY'), end.format('MMMM D, YYYY'));
       //partner wise monthly booking unit data
       around_monthly_unit_data();
       $('#show_more_btn').hide();
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
    
    function get_missing_pincodes(){
        var data = {};
        url = '<?php echo base_url(); ?>employee/dashboard/get_pincode_not_found_sf_details_admin';
        data['partner_id'] = '';
        
        sendAjaxRequest(data,url,post_request).done(function(response){
            $("#pincode_table_data").html(response);
        });
    }
    
    function partner_booking_status(startDate,endDate){
        $('#loader_gif1').fadeIn();
        $('#chart_container').fadeOut();
        var data = {sDate: startDate, eDate: endDate};
        url =  '<?php echo base_url(); ?>employee/dashboard/get_partners_booking_report_chart';
        
        sendAjaxRequest(data,url,post_request).done(function(response){
            create_partner_booking_chart(response);
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
    
    function around_monthly_unit_data(){
        
        $('#loader_gif6').fadeIn();
        $('#monthly_unit_booking_chart').fadeOut();
        var data = {partner_id:''};
        url =  '<?php echo base_url(); ?>employee/dashboard/get_bookings_unit_data_by_month';
        
        sendAjaxRequest(data,url,post_request).done(function(response){
            get_mothly_booking_status(response,'2');
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
    
    function create_partner_booking_chart(response){
        var data = JSON.parse(response);
                var partners_id = data.partner_id;
                var partners = data.partner_name.split(',');
                var completed_bookings_count = JSON.parse("[" + data.completed_bookings_count + "]");
                var cancelled_bookings_count = JSON.parse("[" + data.cancelled_bookings_count + "]");
                $('#loader_gif1').hide();
                $('#chart_container').fadeIn();
                partner_booking_chart = new Highcharts.Chart({
                                    chart: {
                                        renderTo: 'chart_container',
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
                                        categories: partners,
                                        labels: {
                                            style: {
                                                fontSize:'13px'
                                            }
                                        }
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
                                            name: 'Completed Bookings',
                                            data: completed_bookings_count,
                                            cursor: 'pointer',
                                            point: {
                                                events: {
                                                    click: function (event) {
                                                        var get_date = $('#reportrange span').text().split('-');
                                                        var startdate = Date.parse(get_date[0]).toString('dd-MMM-yyyy');
                                                        var enddate = Date.parse(get_date[1]).toString('dd-MMM-yyyy');
                                                        window.open(baseUrl + '/employee/dashboard/partner_reports/' + this.category + '/' + partners_id[this.category] + '/' + 'Completed' + '/' + encodeURI(startdate) + '/' + encodeURI(enddate), '_blank');
    
                                                    }
                                                }
                                            }
                                        }, {
                                            name: 'Cancelled Bookings',
                                            data: cancelled_bookings_count,
                                            cursor: 'pointer',
                                            point: {
                                                events: {
                                                    click: function (event) {
                                                        var get_date = $('#reportrange span').text().split('-');
                                                        var startdate = Date.parse(get_date[0]).toString('dd-MMM-yyyy');
                                                        var enddate = Date.parse(get_date[1]).toString('dd-MMM-yyyy');
                                                        window.open(baseUrl + '/employee/dashboard/partner_reports/' + this.category + '/' + partners_id[this.category] + '/' + 'Cancelled' + '/' + encodeURI(startdate) + '/' + encodeURI(enddate), '_blank');
    
                                                    }
                                                }
                                            }
                                        }]
                                });
    //                var partners = [];
    //                var bookings = [];
    //                var partnerid = [];
    //                var chart_ajax_data = [];
    //                $.each(JSON.parse(response), function (key, value) {
    //                    partners.push(value.public_name);
    //                    bookings.push(parseInt(value.count));
    //                    partnerid[value.public_name] = parseInt(value.partner_id);
    //                    var arr = {
    //                        name: value.public_name,
    //                        y: parseInt(value.count)
    //                    };
    //                    chart_ajax_data.push(arr);
    //
    //                });
    //                $('#loader_gif1').attr('src', "");
    //                $('#loader_gif1').css('display', 'none');
    //                $('#chart_container').show();
    //                partner_booking_chart = new Highcharts.Chart({
    //                    chart: {
    //                        renderTo: 'chart_container',
    //                        type: 'column'
    //                    },
    //                    title: {
    //                        text: '',
    //                        x: -20 //center
    //                    },
    //                    xAxis: {
    //                        categories: partners
    //                    },
    //                    yAxis: {
    //                        title: {
    //                            text: 'Count'
    //                        },
    //                        plotLines: [{
    //                                value: 0, width: 1,
    //                                color: '#808080'
    //                            }]
    //                    },
    //                    plotOptions: {
    //                        column: {
    //                            dataLabels: {
    //                                enabled: true,
    //                                crop: false,
    //                                overflow: 'none'
    //                            }
    //                        },
    //                        pie: {
    //                            plotBorderWidth: 0,
    //                            allowPointSelect: true,
    //                            cursor: 'pointer',
    //                            size: '100%',
    //                            dataLabels: {
    //                                enabled: true,
    //                                format: '{point.name}: <b>{point.y}</b>'
    //                            },
    //                            showInLegend: true
    //                        }
    //                    },
    //                    series: [
    //                        {
    //                            name: booking_status,
    //                            data: chart_ajax_data,
    //                            cursor: 'pointer',
    //                            point: {
    //                                events: {
    //                                    click: function (event) {
    //                                        var get_date = $('#reportrange span').text().split('-');
    //                                        var startdate = Date.parse(get_date[0]).toString('dd-MMM-yyyy');
    //                                        var enddate = Date.parse(get_date[1]).toString('dd-MMM-yyyy');
    //                                        var booking_status = $('#booking_status').val();
    //                                        window.open(baseUrl + '/employee/dashboard/partner_reports/' + this.name + '/' + partnerid[this.name] + '/' + booking_status + '/' + encodeURI(startdate) + '/' + encodeURI(enddate), '_blank');
    //                                    }
    //                                }
    //                            }
    //                        }]
    //                });
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
    
    function agent_daily_report(startDate,endDate){
        var url =  '<?php echo base_url();?>BookingSummary/agent_working_details_ajax';
        $('#loader_gif4').fadeIn();
        $('#chart_container2').hide();
        $.ajax({
            type: 'POST',
            url: url,
            data: {sDate: startDate, eDate: endDate},
            success: function (response) {
                $('#loader_gif4').hide();
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
    
    function partner_unit_chart(startDate,endDate){
        var url = '<?php echo base_url(); ?>employee/dashboard/get_count_unit_details';
        $('#loader_gif5').fadeIn();
        $('#unit_chart').hide();
        $.ajax({
            type: 'POST',
            url: url,
            data: {sDate: startDate, eDate: endDate},
            success: function (response) {
                $('#loader_gif5').hide();
                $('#unit_chart').fadeIn();
                var data = JSON.parse(response);

                 unit_chart = new Highcharts.Chart({
                    chart: {
                        renderTo: 'unit_chart',
                        type: 'column',
                        events: {
                            load: Highcharts.drawTable
                        },
                    },
                    title: {
                        text: '',
                        x: -20 //center
                    },
                    xAxis: {
                        categories: data['partner_category'],
                        labels: {
                            style: {
                                fontSize:'13px'
                            }
                        }
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
                            name: 'Completed',
                            data: data['data']
                        }]
                });
              }
        });
    }
    
    
</script>
<script>
     function change_toggal_text(){
        var currentValue = document.getElementById("order_by_toggal").innerHTML;
        if(currentValue =="Sort By Number Of Escalation"){
            document.getElementById("order_by_toggal").innerHTML="Sort BY perentage";
        }
        else{
             document.getElementById("order_by_toggal").innerHTML="Sort By Number Of Escalation";
        }
    }
$(function() {
        var d = new Date();
        n = d.getMonth()+1;
        y = d.getFullYear();
        date = d.getDate();
    $('input[name="daterange"]').daterangepicker({
        timePicker: true,
        timePickerIncrement: 30,
        locale: {
            format: 'YYYY-MM-DD'
        },
        startDate: y+'-'+n+'-01'
    });
});
    </script>
<style>
    .text_warning{
        color:red;
    }
    [ng\:cloak], [ng-cloak], .ng-cloak {
  display: none !important;
}
    </style>
