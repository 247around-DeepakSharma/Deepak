<style>
    .tile_stats_count{color:#333;}
    .tile_count .tile_stats_count, ul.quick-list li {
        white-space: normal;
        overflow: visible;
        text-overflow: clip;
    }
    .highcharts-credits{display:none}
    #preview{display: none;}
</style>
<pre id="preview"></pre>
<!-- page content -->
<div class="right_col" role="main">
    <!-- top tiles -->
    <div class="row tile_count">
        <?php foreach ($query as $key => $value) { ?>
            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
                <span class="count_top" style="display:block;min-height: 60px;"><?php echo $value['description'] ?></span>
                <div class="count"><?php echo $data[$key][0]['count'] ?></div>
            </div>
        <?php } ?>
    </div>
    <!-- /top tiles -->

    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="dashboard_graph">

                <div class="row x_title">
                    <div class="col-md-6">
                        <h3>Partner Booking Status &nbsp;&nbsp;&nbsp;
                            <small>
                                <select class="form-control" style="width:30%; display: inline-block;" id="booking_status">
                                    <option selected>Completed</option>
                                    <option>Cancelled</option>
<!--                                    <option>FollowUp</option>
                                    <option>Pending</option>
                                    <option>Rescheduled</option>-->
                                    <option>ALL</option>
                                </select>
                            </small>
                        </h3>
                    </div>
                    <div class="col-md-6">
                        <div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc">
                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                            <span></span> <b class="caret"></b>
                        </div>
                    </div>
                </div>
                <div class="change_chart_type" >
                    <button id="column" class="btn btn-sm" style="margin-left: 2em; background-color:#ba68c8; color: #000;">Column</button>
                    <button id="line" class="btn btn-sm" style="background-color:#7986cb; color: #000;">Line</button>
                    <button id="spline" class="btn btn-sm" style="background-color:#4dd0e1; color: #000;">Spline</button>
                    <button id="area" class="btn btn-sm" style="background-color:#4db6ac; color: #000;">Area</button>
                    <button id="areaspline" class="btn btn-sm" style="background-color:#dce775; color: #000;">Areaspline</button>
                    <button id="scatter" class="btn btn-sm" style="background-color:#69f0ae; color: #000;">Scatter</button>
                    <button id="pie" class="btn btn-sm" style="background-color:#ffb74d; color: #000;">Pie</button>
                </div>
                <div class="col-md-12"><center><img id="loader_gif1" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center></div>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div id="chart_container" class="chart_container"></div>
                </div>

                <div class="clearfix"></div>
            </div>
        </div>

    </div>
       <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="dashboard_graph">

                <div class="row x_title">
                    <div class="col-md-6">
                        <h3>Partner Unit Details
                            <small>
                            </small>
                        </h3>
                    </div>
                    <div class="col-md-6">
                        <div id="unit_chart_range" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc">
                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                            <span></span> <b class="caret"></b>
                        </div>
                    </div>
                </div>

                <div class="col-md-12"><center><img id="loader_gif_unit" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center></div>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div id="unit_chart" class="unit_chart" style="height:600px;"></div>
                </div>

                <div class="clearfix"></div>
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="dashboard_graph">

                <div class="row x_title">
                    <div class="col-md-6">
                        <h3>Partner Booking Inflow Status
                            <small>
                            </small>
                        </h3>
                    </div>
                    <div class="col-md-6">
                        <div id="reportrange6" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc">
                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                            <span></span> <b class="caret"></b>
                        </div>
                    </div>
                </div>
<!--                <div class="change_chart_type" >
                    <button id="column" class="btn btn-sm" style="margin-left: 2em; background-color:#ba68c8; color: #000;">Column</button>
                    <button id="line" class="btn btn-sm" style="background-color:#7986cb; color: #000;">Line</button>
                    <button id="spline" class="btn btn-sm" style="background-color:#4dd0e1; color: #000;">Spline</button>
                    <button id="area" class="btn btn-sm" style="background-color:#4db6ac; color: #000;">Area</button>
                    <button id="areaspline" class="btn btn-sm" style="background-color:#dce775; color: #000;">Areaspline</button>
                    <button id="scatter" class="btn btn-sm" style="background-color:#69f0ae; color: #000;">Scatter</button>
                    <button id="pie" class="btn btn-sm" style="background-color:#ffb74d; color: #000;">Pie</button>
                </div>-->
                <div class="col-md-12"><center><img id="loader_gif7" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center></div>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div id="inflow_chart_container" class="chart_container" style="height:600px;"></div>
                </div>

                <div class="clearfix"></div>
            </div>
        </div>

    </div>
    <br/>
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
                        <div id="reportrange2" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc">
                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                            <span></span> <b class="caret"></b>
                        </div>
                    </div>
                </div>
                <div class="col-md-12"><center><img id="loader_gif2" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center></div>
                <div id="chart_container2" class="chart_containe2"></div>

                <div class="clearfix"></div>
            </div>
        </div>

    </div>
    <br/>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="dashboard_graph">

                <div class="row x_title">
                    <div class="col-md-6">
                        <h3>PAID VS FOC Bookings &nbsp;&nbsp;&nbsp;
                            <small>
                                <select class="form-control" style="width:30%; display: inline-block;" id="booking_status2">
                                    <option selected>Completed</option>
                                    <option>Cancelled</option>
<!--                                    <option>FollowUp</option>
                                    <option>Pending</option>
                                    <option>Rescheduled</option>-->
                                </select>
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

                <div>
                </div>
                <div class="col-md-12"><center><img id="loader_gif3" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center></div>
                <div id="piechart" class="piechart"></div>

                <div class="clearfix"></div>
            </div>
        </div>

    </div>

    <div class="col-md-12"><center><img id="loader_gif5" src="" style="display: none;"></center></div>
    <div class="load_data_onScroll" style="display:none;">
        <div class="row" style="margin-top:10px;">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Booking based on Service Type <small></small></h2>
                        <div class="nav navbar-right panel_toolbox">
                            <div id="reportrange4" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc">
                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                <span></span> <b class="caret"></b>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="col-md-12"><center><img id="loader_gif5" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center></div>
                    <div class="x_content">
                        <div id="services_type_booking_chart" style="width:100%; height:400px;"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" style="margin-top:10px;">
            <div class="col-md-12 col-sm-12 col-xs-12" id="based_on_Region">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Booking based on Region <small></small></h2>
                        <div class="nav navbar-right panel_toolbox">
                            <div id="reportrange5" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc">
                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                <span></span> <b class="caret"></b>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="col-md-12"><center><img id="loader_gif6" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center></div>
                    <div class="x_content">
                        <div id="state_type_booking_chart" style="width:100%; height:400px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- /page content -->
<!-- create chart using MySQL data -->
<script>
    
    var partner_name = [];
    var completed_booking = [];
    var partner_id = [];
    var change_chart_data = [];
     var partner_name = [];
    var completed_booking = [];
    var partner_id = [];
    var change_chart_data = [];
<?php foreach ($partner_data as $key => $value) { ?>
        partner_name.push("<?php echo $value['public_name']; ?>");
        completed_booking.push(parseInt("<?php echo $value['count']; ?>"));
        partner_id['<?php echo $value['public_name']; ?>'] = <?php echo $value['partner_id']; ?>;
        var arr = {
            name: '<?php echo $value['public_name']; ?>',
            y: parseInt(<?php echo $value['count']; ?>)
        };
        change_chart_data.push(arr);
<?php } ?>

    var chart = new Highcharts.Chart({
        chart: {
            renderTo: 'chart_container',
            type: 'column'
        },
        title: {
            text: '',
            x: -20 //center
        },
        xAxis: {
            categories: partner_name
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
                    enabled: true, }
            },
            pie: {
                plotBorderWidth: 0, allowPointSelect: true,
                cursor: 'pointer',
                size: '100%',
                dataLabels: {
                    enabled: true,
                    format: '{point.name}: <b>{point.y}</b>'
                },
                showInLegend: true
            }
        },
        series: [
            {
                name: 'Completed Booking',
                data: change_chart_data,
                cursor: 'pointer',
                point: {
                    events: {
                        click: function (event) {
                            var get_date = $('#reportrange span').text().split('-');
                            var startdate = Date.parse(get_date[0]).toString('dd-MMM-yyyy');
                            var enddate = Date.parse(get_date[1]).toString('dd-MMM-yyyy');
                            var booking_status = $('#booking_status').val();
                            window.open(baseUrl + '/employee/dashboard/partner_reports/' + this.name + '/' + partner_id[this.name] + '/' + booking_status + '/' + encodeURI(startdate) + '/' + encodeURI(enddate), '_blank');
                        }
                    }
                }
            }]
    });


    var start = moment().startOf('month');
    var end = moment().endOf('month');
    
    $(function () {
        function cb(start, end) {
            $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }

        $('#reportrange').daterangepicker({
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
        }, cb);

        cb(start, end);

    });
    
    
    $(function () {
        function cb(start, end) {
            $('#reportrange6 span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }

        $('#reportrange6').daterangepicker({
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
        }, cb);

        cb(start, end);

    });

    
    $(function () {
        function cb(start, end) {
            $('#reportrange2 span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }

        $('#reportrange2').daterangepicker({
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
            format: 'MM/DD/YYYY',
            separator: ' to ',
            locale: {
                applyLabel: 'Submit',
                cancelLabel: 'Clear',
                fromLabel: 'From',
                toLabel: 'To',
                customRangeLabel: 'Custom', daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                firstDay: 1
            }}, cb);
        cb(start, end);

    });

    $('#reportrange').on('apply.daterangepicker', function (ev, picker) {

        var startDate = picker.startDate.format('YYYY-MM-DD');
        var endDate = picker.endDate.format('YYYY-MM-DD');
        partner_booking_status(startDate,endDate);
       
    });
   // partner_booking_status (start.format('MMMM D, YYYY'), end.format('MMMM D, YYYY'));
    function partner_booking_status (startDate,endDate){
        var booking_status = $('#booking_status').val();
        var url =  '<?php echo base_url(); ?>BookingSummary/get_partners_booking_report_chart';
        $('#loader_gif1').css('display', 'inherit');
        $('#loader_gif1').attr('src', "<?php echo base_url(); ?>images/loadring.gif");
        $('#chart_container').hide();
        
        $.ajax({
            type: 'POST',
            url: url,
            data: {sDate: startDate, eDate: endDate, booking_status: booking_status},
            success: function (response) {
                var partners = [];
                var bookings = [];
                var partnerid = [];
                var chart_ajax_data = [];
                $.each(JSON.parse(response), function (key, value) {
                    partners.push(value.public_name);
                    bookings.push(parseInt(value.count));
                    partnerid[value.public_name] = parseInt(value.partner_id);
                    var arr = {
                        name: value.public_name,
                        y: parseInt(value.count)
                    };
                    chart_ajax_data.push(arr);

                });
                $('#loader_gif1').attr('src', "");
                $('#loader_gif1').css('display', 'none');
                $('#chart_container').show();
                chart = new Highcharts.Chart({
                    chart: {
                        renderTo: 'chart_container',
                        type: 'column'
                    },
                    title: {
                        text: '',
                        x: -20 //center
                    },
                    xAxis: {
                        categories: partners
                    },
                    yAxis: {
                        title: {
                            text: 'Count'
                        },
                        plotLines: [{
                                value: 0, width: 1,
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
                        },
                        pie: {
                            plotBorderWidth: 0,
                            allowPointSelect: true,
                            cursor: 'pointer',
                            size: '100%',
                            dataLabels: {
                                enabled: true,
                                format: '{point.name}: <b>{point.y}</b>'
                            },
                            showInLegend: true
                        }
                    },
                    series: [
                        {
                            name: booking_status,
                            data: chart_ajax_data,
                            cursor: 'pointer',
                            point: {
                                events: {
                                    click: function (event) {
                                        var get_date = $('#reportrange span').text().split('-');
                                        var startdate = Date.parse(get_date[0]).toString('dd-MMM-yyyy');
                                        var enddate = Date.parse(get_date[1]).toString('dd-MMM-yyyy');
                                        var booking_status = $('#booking_status').val();
                                        window.open(baseUrl + '/employee/dashboard/partner_reports/' + this.name + '/' + partnerid[this.name] + '/' + booking_status + '/' + encodeURI(startdate) + '/' + encodeURI(enddate), '_blank');
                                    }
                                }
                            }
                        }]
                });
            }
        });
    }
    $('#unit_chart_range').on('apply.daterangepicker', function (ev, picker) {
        var startDate = picker.startDate.format('YYYY-MM-DD');
        var endDate = picker.endDate.format('YYYY-MM-DD');
        partner_unit_chart(startDate, endDate);
        //console.log("apply event fired, start/end dates are " + picker.startDate.format('MMMM D, YYYY') + " to " + picker.endDate.format('MMMM D, YYYY'));
    });
    partner_unit_chart(start.format('MMMM D, YYYY'), end.format('MMMM D, YYYY'));
    function partner_unit_chart(startDate,endDate){
        var url = '<?php echo base_url(); ?>employee/dashboard/get_count_unit_details';
        $('#loader_gif_unit').css('display', 'inherit');
        $('#loader_gif_unit').attr('src', "<?php echo base_url(); ?>images/loadring.gif");
        $('#unit_chart').hide();
         $.ajax({
            type: 'POST',
            url: url,
            data: {sDate: startDate, eDate: endDate, current_status: $("#booking_status").val()},
            success: function (response) {
                $('#loader_gif_unit').attr('src', "");
                $('#loader_gif_unit').css('display', 'none');
                $('#loader_gif_unit').show();
               $('#unit_chart').show();
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
                        bar: {
                            dataLabels: {
                                enabled: true
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
                            name: $("#booking_status").val(),
                            data: data['data']
                        }]
                });
              }
        });
    }
    
    
    $(function () {
        function cb(start, end) {
            $('#unit_chart_range span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }

        $('#unit_chart_range').daterangepicker({
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
        }, cb);

        cb(start, end);

    });

    
    $('#reportrange6').on('apply.daterangepicker', function (ev, picker) {
        var startDate = picker.startDate.format('YYYY-MM-DD');
        var endDate = picker.endDate.format('YYYY-MM-DD');
        partner_flow_chart(startDate, endDate);
        //console.log("apply event fired, start/end dates are " + picker.startDate.format('MMMM D, YYYY') + " to " + picker.endDate.format('MMMM D, YYYY'));
    });
    partner_flow_chart(start.format('MMMM D, YYYY'), end.format('MMMM D, YYYY'));
    function partner_flow_chart(startDate,endDate ){
       
        var url = '<?php echo base_url(); ?>employee/dashboard/partners_booking_inflow/1';
        $('#loader_gif7').css('display', 'inherit');
        $('#loader_gif7').attr('src', "<?php echo base_url(); ?>images/loadring.gif");
        $('#inflow_chart_container').hide();
        $.ajax({
            type: 'POST',
            url: url,
            data: {sDate: startDate, eDate: endDate},
            success: function (response) {
                $('#loader_gif7').attr('src', "");
                $('#loader_gif7').css('display', 'none');
                $('#inflow_chart_container').show();
                var data = JSON.parse(response);
                var partner_name = data.partner_name.split(',');
                var followup = JSON.parse("[" + data.followup + "]");
                var pending = JSON.parse("[" + data.pending + "]");
                chart2 = new Highcharts.Chart({
                    chart: {
                        renderTo: 'inflow_chart_container',
                        type: 'bar',
                        events: {
                            load: Highcharts.drawTable
                        },
                    },
                    title: {
                        text: '',
                        x: -20 //center
                    },
                    xAxis: {
                        categories: partner_name,
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
                        bar: {
                            dataLabels: {
                                enabled: true
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
                            name: 'Booking Entered',
                            data: followup,
                        }, {
                            name: 'Booking Scheduled',
                            data: pending
                        }]
                });
            }
        });

        
    }
    
    $('#reportrange2').on('apply.daterangepicker', function (ev, picker) {
        var startDate = picker.startDate.format('YYYY-MM-DD');
        var endDate = picker.endDate.format('YYYY-MM-DD');
        agent_daily_report(startDate,endDate);
        
        //console.log("apply event fired, start/end dates are " + picker.startDate.format('MMMM D, YYYY') + " to " + picker.endDate.format('MMMM D, YYYY'));
    });
    
    agent_daily_report(start.format('MMMM D, YYYY'), end.format('MMMM D, YYYY'));
    
    function agent_daily_report(startDate,endDate){
        var url =  '<?php echo base_url();?>BookingSummary/agent_working_details_ajax';
        $('#loader_gif2').css('display', 'inherit');
        $('#loader_gif2').attr('src', "<?php echo base_url(); ?>images/loadring.gif");
        $('#chart_container2').hide();
        $.ajax({
            type: 'POST',
            url: url,
            data: {sDate: startDate, eDate: endDate},
            success: function (response) {
                $('#loader_gif2').attr('src', "");
                $('#loader_gif2').css('display', 'none');
                $('#chart_container2').show();
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
                        },
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
    
    $(function () {
        function cb(start, end) {
            $('#reportrange3 span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }

        $('#reportrange3').daterangepicker({
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
            format: 'MM/DD/YYYY',
            separator: ' to ',
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
        }, cb);

        cb(start, end);

    });

    $(function () {
        function cb(start, end) {
            $('#reportrange4 span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }

        $('#reportrange4').daterangepicker({
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
            format: 'MM/DD/YYYY',
            separator: ' to ',
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
        }, cb);

        cb(start, end);

    });

    $(function () {
        function cb(start, end) {
            $('#reportrange5 span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }

        $('#reportrange5').daterangepicker({
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
            format: 'MM/DD/YYYY',
            separator: ' to ',
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
        }, cb);

        cb(start, end);

    });

    $('#reportrange3').on('apply.daterangepicker', function (ev, picker) {

        var startDate = picker.startDate.format('YYYY-MM-DD');
        var endDate = picker.endDate.format('YYYY-MM-DD');
        foc_paid_pie_chart(startDate,endDate );
       
    });
    foc_paid_pie_chart(start.format('MMMM D, YYYY'), end.format('MMMM D, YYYY') );
    function foc_paid_pie_chart(startDate,endDate ){
         var booking_status = $('#booking_status2').val();
        var url =  '<?php echo base_url(); ?>employee/dashboard/get_paid_foc_count_ajax';
        $('#loader_gif3').css('display', 'inherit');
        $('#loader_gif3').attr('src', "<?php echo base_url(); ?>images/loadring.gif");
        $('#piechart').hide();
        $.ajax({
            type: 'POST',
            url: url,
            data: {sDate: startDate, eDate: endDate, current_status: booking_status},
            success: function (response) {
                $('#loader_gif3').attr('src', "");
                $('#loader_gif3').css('display', 'none');
                $('#piechart').show();
                var data = JSON.parse(response);
                chart3 = new Highcharts.Chart({
                    chart: {
                        renderTo: 'piechart',
                        type: 'pie',
                        plotBorderWidth: 0,
                        events: {
                            drilldown: function (e) {
                                if (!e.seriesOptions) {
                                    var get_date = $('#reportrange3 span').text().split('-');
                                    var startdate = get_date[0];
                                    var enddate = get_date[1];
                                    var type = e.point.name;
                                    var booking_status = $('#booking_status2').val();
                                    var chart = this;
                                    foc_paid_ajax_data(e, startdate, enddate, type, chart, booking_status)
                                }

                            }
                        }
                    },
                    title: {
                        text: ''
                    },
                    lang: {
                        drillUpText: '<< back {series.name}'
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            borderWidth: 0,
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b>: {point.y:.f}',
                                style: {
                                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                                }
                            },
                            showInLegend: true
                        }
                    },
                    series: [{
                            name: 'Bookings',
                            colorByPoint: true,
                            data: [{
                                    name: 'FOC',
                                    y: parseInt(data.foc),
                                    drilldown: true
                                }, {
                                    name: 'PAID',
                                    y: parseInt(data.paid), sliced: true, selected: true,
                                    drilldown: true
                                }]
                        }],
                    drilldown: {
                        series: []
                    }

                });
            }
        });
    }

    function foc_paid_ajax_data(e, startdate, enddate, type, chart, booking_status) {
        var url = baseUrl + '/employee/dashboard/get_total_foc_or_paid_booking';
        var partner_name = [];
        var count = [];
        var drilldown_data = [];
        chart.showLoading('Loading Data ...');
        $.ajax({
            type: 'POST', url: url,
            data: {sDate: startdate, eDate: enddate, type: type, current_status: booking_status},
            success: function (response) {
                var data = JSON.parse(response);
                partner_name = data.partner_name.split(',');
                count = JSON.parse("[" + data.count + "]");
                for (var i = 0; i < partner_name.length; i++) {
                    var arr = [partner_name[i], count[i]];
                    drilldown_data.push(arr);
                }
                var drilldowns = {
                    'FOC': {
                        name: 'FOC',
                        data: drilldown_data
                    },
                    'PAID': {
                        name: 'PAID',
                        data: drilldown_data
                    }
                },
                series = drilldowns[e.point.name];
                chart.hideLoading();
                chart.addSeriesAsDrilldown(e.point, series);
            }
        });
    }

    function get_partner_booking_based_services(e, chart, startdate, enddate, partner_name, partner_id, booking_status) {
        var url = baseUrl + '/employee/dashboard/get_partner_booking_based_on_services';

        var drilldown_data = [];
        chart.showLoading('Loading Data ...');
        $.ajax({
            type: 'POST',
            url: url,
            data: {sDate: startdate, eDate: enddate, current_status: booking_status, partner_id: partner_id},
            success: function (response) {
                $.each(JSON.parse(response), function (key, value) {
                    var arr = [value.services, parseInt(value.total)];
                    drilldown_data.push(arr);

                });

                var data = {'': {
                        name: partner_name,
                        data: drilldown_data
                    }}
                series = data[''];
                chart.hideLoading();
                chart.addSeriesAsDrilldown(e.point, series);
            }
        });
    }

    $('#reportrange4').on('apply.daterangepicker', function (ev, picker) {

        var startDate = picker.startDate.format('YYYY-MM-DD');
        var endDate = picker.endDate.format('YYYY-MM-DD');
        //var booking_status = $('#booking_status2').val();
        var url = baseUrl + '/employee/dashboard/get_data_onScroll/1';
        $('#loader_gif5').css('display', 'inherit');
        $('#loader_gif5').attr('src', "<?php echo base_url(); ?>images/loadring.gif");
        $('#services_type_booking_chart').hide();
        $.ajax({
            type: 'POST',
            url: url,
            data: {sDate: startDate, eDate: endDate},
            success: function (response) {
                $('#loader_gif5').attr('src', "");
                $('#loader_gif5').css('display', 'none');
                $('#services_type_booking_chart').show();
                create_chart_on_ajax_call(response, true);
            }
        });
    });

    $('#reportrange5').on('apply.daterangepicker', function (ev, picker) {

        var startDate = picker.startDate.format('YYYY-MM-DD');
        var endDate = picker.endDate.format('YYYY-MM-DD');
        //var booking_status = $('#booking_status2').val();
        var url = baseUrl + '/employee/dashboard/get_booking_data_by_region/1';
        $('#loader_gif6').css('display', 'inherit');
        $('#loader_gif6').attr('src', "<?php echo base_url(); ?>images/loadring.gif");
        $('#state_type_booking_chart').hide();
        $.ajax({
            type: 'POST',
            url: url,
            data: {sDate: startDate, eDate: endDate},
            success: function (response) {
                $('#loader_gif6').attr('src', "");
                $('#loader_gif6').css('display', 'none');
                $('#state_type_booking_chart').show();
                create_chart_based_on_bookings_state(response);
            }
        });
    });

    var track_page = 1; //track user scroll as page number, right now page number is 1
    var loading = false; //prevents multiple loads

    $(document).ready(function () {
        $(window).scroll(function () {
            if ($('body').height() <= ($(window).height() + $(window).scrollTop())) {
                load_contents();
            }
        });
    });
    //Ajax load function
    function load_contents() {
        if (loading == false) {
            loading = true;  //set loading flag on
            $('#loader_gif5').css('display', 'inherit');
            $('#loader_gif5').attr('src', "<?php echo base_url(); ?>images/loadring.gif");
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/dashboard/get_data_onScroll',
                success: function (data) {
                    $('#loader_gif5').attr('src', "");
                    $('#loader_gif5').css('display', 'none');

                    create_chart_on_ajax_call(data, false);
                }
            });
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/dashboard/get_booking_data_by_region',
                success: function (data) {
                    create_chart_based_on_bookings_state(data);
                }
            });


        }
    }

    function create_chart_on_ajax_call(data, is_repeat_ajax) {
        if (!is_repeat_ajax) {
            $(".load_data_onScroll").show();
            $('#based_on_Region').hide();
        }
        var request_type = [];
        var total_count = [];
        var chart_ajax_data = [];
        $.each(JSON.parse(data), function (key, value) {
            request_type.push(value.request_type);
            total_count.push(parseInt(value.total));
            var arr = {
                name: value.request_type,
                y: parseInt(value.total),
                drilldown: true
            };
            chart_ajax_data.push(arr);

        });
        request_type_chart = new Highcharts.Chart({
            chart: {
                renderTo: 'services_type_booking_chart',
                type: 'pie',
                events: {
                    drilldown: function (e) {
                        if (!e.seriesOptions) {
                            var get_date = $('#reportrange4 span').text().split('-');
                            var startdate = get_date[0];
                            var enddate = get_date[1];
                            var type = e.point.name;
                            var chart = this;
                            booking_based_on_status_groupby_request_type(e, startdate, enddate, type, chart);

                        }
                    }
                }
            },
            title: {
                text: '',
                x: -20 //center
            },
            xAxis: {
                categories: request_type
            },
            yAxis: {
                title: {
                    text: 'Count'
                },
                plotLines: [{
                        value: 0, width: 1,
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
                },
                pie: {
                    plotBorderWidth: 0,
                    allowPointSelect: true,
                    cursor: 'pointer',
                    size: '100%',
                    dataLabels: {
                        enabled: true,
                        format: '{point.name}: <b>{point.y}</b>'
                    },
                    showInLegend: true
                }
            },
            series: [
                {
                    name: 'Request Type',
                    data: chart_ajax_data
                }],
            drilldown: {
                series: []
            }
        });
    }


    function booking_based_on_status_groupby_request_type(e, startdate, enddate, type, chart) {
        var url = baseUrl + '/employee/dashboard/get_bookings_data_by_request_type_current_status';
        var drilldown_data = [];
        chart.showLoading('Loading Data ...');
        $.ajax({
            type: 'POST',
            url: url,
            data: {sDate: startdate, eDate: enddate, type: type},
            success: function (response) {
                //console.log(response);                
                var data = JSON.parse(response);
                var current_status = data.current_status.split(',');
                var count = JSON.parse("[" + data.count + "]");
                for (var i = 0; i < current_status.length; i++) {
                    var arr = [current_status[i], count[i]];
                    drilldown_data.push(arr);
                }
                var drilldowns = {
                    '': {
                        id: type,
                        data: drilldown_data
                    }
                },
                series = drilldowns[''];
                chart.hideLoading();
                chart.addSeriesAsDrilldown(e.point, series);
            }
        });
    }

    function create_chart_based_on_bookings_state(response) {
        $('#based_on_Region').show();
        var data = JSON.parse(response);
        var rm = data.rm.split(',');
        var cancelled = JSON.parse("[" + data.cancelled + "]");
        var completed = JSON.parse("[" + data.completed + "]");
        var pending = JSON.parse("[" + data.pending + "]");
        var total = JSON.parse("[" + data.total + "]");
        rm_based_chart = new Highcharts.Chart({
            chart: {
                renderTo: 'state_type_booking_chart',
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
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle',
                borderWidth: 0
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

    $('#preview').html(chart.getCSV());
    $.each(['line', 'column', 'spline', 'area', 'areaspline', 'scatter', 'pie'], function (i, type) {
        $('#' + type).click(function () {
            chart.series[0].update({
                type: type
            });
        });
    });
</script>