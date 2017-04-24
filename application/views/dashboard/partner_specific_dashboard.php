<style>
    .highcharts-credits,.highcharts-button-symbol{display:none}
</style>


<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3><?php echo $partner_name; ?> Reports</h3>
            </div>

            <!--              <div class="title_right">
                            <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                              <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search for...">
                                <span class="input-group-btn">
                                  <button class="btn btn-default" type="button">Go!</button>
                                </span>
                              </div>
                            </div>
                          </div>-->
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Booking based on services <small></small></h2>
                        <div class="nav navbar-right panel_toolbox">
                            <div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc">
                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                <span></span> <b class="caret"></b>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="col-md-12"><center><img id="loader_gif1" src="" style="display: none;"></center></div>
                    <div class="x_content">
                        <div id="services_booking_chart" style="width:100%; height:400px;"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Paid Vs FOC <small></small></h2>
                        <div class="nav navbar-right panel_toolbox">
                            <div id="reportrange2" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc">
                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                <span></span> <b class="caret"></b>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="col-md-12"><center><img id="loader_gif2" src="" style="display: none;"></center></div>
                    <div class="x_content1">
                        <div id="paid_vs_foc" style="width:100%; height:400px;"></div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            
            <div class="col-md-12"><center><img id="loader_gif3" src="" style="display: none;"></center></div>
            <div class="load_data_onScroll" style="display:none;">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Booking based on Service Type<small></small></h2>
                            <div class="nav navbar-right panel_toolbox">
                                <div id="reportrange3" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc">
                                    <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                    <span></span> <b class="caret"></b>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="col-md-12"><center><img id="loader_gif4" src="" style="display: none;"></center></div>
                        <div class="x_content2">
                            <div id="services_type_booking_chart" style="width:100%; height:400px;" ></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Booking based on Region<small></small></h2>
                            <div class="nav navbar-right panel_toolbox">
                                <div id="reportrange4" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc">
                                    <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                    <span></span> <b class="caret"></b>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="col-md-12"><center><img id="loader_gif5" src="" style="display: none;"></center></div>
                        <div class="x_content2">
                            <div id="state_type_booking_chart" style="width:100%; height:400px;" ></div>
                        </div>
                    </div>
                </div>
                
            </div>

            <!--      <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Pie Chart <small>Sessions</small></h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                </li>
                                                      <li class="dropdown">
                                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                                        <ul class="dropdown-menu" role="menu">
                                                          <li><a href="#">Settings 1</a>
                                                          </li>
                                                          <li><a href="#">Settings 2</a>
                                                          </li>
                                                        </ul>
                                                      </li>
                                <li><a class="close-link"><i class="fa fa-close"></i></a>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content2">
                            <div id="graph_donut" style="width:100%; height:400px;"></div>
                        </div>
                    </div>
                </div>
    
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Graph area <small>Sessions</small></h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                </li>
                                                      <li class="dropdown">
                                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                                        <ul class="dropdown-menu" role="menu">
                                                          <li><a href="#">Settings 1</a>
                                                          </li>
                                                          <li><a href="#">Settings 2</a>
                                                          </li>
                                                        </ul>
                                                      </li>
                                <li><a class="close-link"><i class="fa fa-close"></i></a>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content2">
                            <div id="graph_area" style="width:100%; height:400px;"></div>
                        </div>
                    </div>
                </div>
    
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Line Graph <small>Sessions</small></h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                </li>
                                                      <li class="dropdown">
                                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                                        <ul class="dropdown-menu" role="menu">
                                                          <li><a href="#">Settings 1</a>
                                                          </li>
                                                          <li><a href="#">Settings 2</a>
                                                          </li>
                                                        </ul>
                                                      </li>
                                <li><a class="close-link"><i class="fa fa-close"></i></a>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content2">
                            <div id="graph_line" style="width:100%; height:400px;"></div>
                        </div>
                    </div>
                </div>-->
        </div>
    </div>
</div>
<!-- /page content -->

<script>
    var partner_name = '<?php echo $partner_name; ?>';
    var partner_id = '<?php echo $partner_id; ?>';
    var startdate = "<?php echo $startDate; ?>";
    var enddate = "<?php echo $endDate; ?>"

    $(document).ready(function () {

        // Build the chart
        Highcharts.chart('services_booking_chart', {
            chart: {
                type: 'pie',
                events: {
                    drilldown: function (e) {
                        if (!e.seriesOptions) {
                            var get_date = $('#reportrange span').text().split('-');
                            var startdate = get_date[0];
                            var enddate = get_date[1];
                            var booking_status = e.point.name;
                            var chart = this;
                            get_partner_booking_based_services(e, chart, startdate, enddate, partner_name, partner_id, booking_status);
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
                        format: '<b>{point.name}</b>: {point.y:.f} ',
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
                            name: 'Completed',
                            y: <?php echo $booking[0]['Completed']; ?>,
                            drilldown: true
                        }, {
                            name: 'Cancelled',
                            y: <?php echo $booking[0]['Cancelled']; ?>,
                            drilldown: true
                        }, {
                            name: 'Pending',
                            y: <?php echo $booking[0]['Pending']; ?>,
                            drilldown: true
                        }, {
                            name: 'Rescheduled',
                            y: <?php echo $booking[0]['Rescheduled']; ?>,
                            drilldown: true
                        }, {
                            name: 'FollowUp',
                            y: <?php echo $booking[0]['FollowUp']; ?>,
                            drilldown: true
                        }]
                }],
            drilldown: {
                series: []
            }
        });
    });

    $(document).ready(function () {

        // Build the chart
        Highcharts.chart('paid_vs_foc', {
            chart: {
                type: 'pie',
                events: {
                    drilldown: function (e) {
                        if (!e.seriesOptions) {
                            var get_date = $('#reportrange2 span').text().split('-');
                            var startdate = get_date[0];
                            var enddate = get_date[1];
                            var type = e.point.name;
                            var chart = this;
                            foc_or_paid_data_groupby_services(e, startdate, enddate, type, chart);
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
                        format: '<b>{point.name}</b>: {point.y:.f} ',
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
                            y: <?php if (!empty($foc)) {
    echo $foc;
} else {
    echo '0';
} ?>,
                            drilldown: true
                        }, {
                            name: 'PAID',
                            y: <?php if (!empty($paid)) {
    echo $paid;
} else {
    echo '0';
} ?>,
                            drilldown: true
                        }]
                }],
            drilldown: {
                series: []
            }
        });
    });


    var start = moment(startdate);
    var end = moment(enddate);

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
                days: 120
            },
            showDropdowns: true,
            showWeekNumbers: true,
            timePicker: false,
            timePickerIncrement: 1,
            timePicker12Hour: true,
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
            $('#reportrange2 span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }

        $('#reportrange2').daterangepicker({
            startDate: start,
            endDate: end,
            minDate: '01/01/2000',
            maxDate: '12/31/2030',
            dateLimit: {
                days: 120
            },
            showDropdowns: true,
            showWeekNumbers: true,
            timePicker: false,
            timePickerIncrement: 1,
            timePicker12Hour: true,
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
            $('#reportrange3 span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }

        $('#reportrange3').daterangepicker({
            startDate: start,
            endDate: end,
            minDate: '01/01/2000',
            maxDate: '12/31/2030',
            dateLimit: {
                days: 120
            },
            showDropdowns: true,
            showWeekNumbers: true,
            timePicker: false,
            timePickerIncrement: 1,
            timePicker12Hour: true,
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
                days: 120
            },
            showDropdowns: true,
            showWeekNumbers: true,
            timePicker: false,
            timePickerIncrement: 1,
            timePicker12Hour: true,
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

    $('#reportrange').on('apply.daterangepicker', function (ev, picker) {

        var startDate = picker.startDate.format('YYYY-MM-DD');
        var endDate = picker.endDate.format('YYYY-MM-DD');
        var url = baseUrl + '/employee/dashboard/get_partner_booking_ajax_data';
        $('#loader_gif1').css('display', 'inherit');
        $('#loader_gif1').attr('src', "<?php echo base_url(); ?>images/loadring.gif");
        $('#services_booking_chart').hide();
        $.ajax({
            type: 'POST',
            url: url,
            data: {sDate: startDate, eDate: endDate, partner_id: partner_id},
            success: function (response) {
                $('#loader_gif1').attr('src', "");
                $('#loader_gif1').css('display', 'none');
                $('#services_booking_chart').show();
                var data = JSON.parse(response);
                chart = new Highcharts.Chart({
                    chart: {
                        renderTo: 'services_booking_chart',
                        type: 'pie',
                        plotBorderWidth: 0,
                        events: {
                            drilldown: function (e) {
                                if (!e.seriesOptions) {
                                    var get_date = $('#reportrange span').text().split('-');
                                    var startdate = get_date[0];
                                    var enddate = get_date[1];
                                    var booking_status = e.point.name;
                                    var chart = this;
                                    get_partner_booking_based_services(e, chart, startdate, enddate, partner_name, partner_id, booking_status);
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
                                    name: 'Completed',
                                    y: parseInt(data.Completed),
                                    drilldown: true
                                }, {
                                    name: 'Cancelled',
                                    y: parseInt(data.Cancelled),
                                    drilldown: true
                                }, {
                                    name: 'Pending',
                                    y: parseInt(data.Pending),
                                    drilldown: true
                                }, {
                                    name: 'Rescheduled',
                                    y: parseInt(data.Rescheduled),
                                    drilldown: true
                                }, {
                                    name: 'FollowUp',
                                    y: parseInt(data.FollowUp),
                                    drilldown: true
                                }]
                        }],
                    drilldown: {
                        series: []
                    }

                });
            }
        });
    });

    $('#reportrange2').on('apply.daterangepicker', function (ev, picker) {

        var startDate = picker.startDate.format('YYYY-MM-DD');
        var endDate = picker.endDate.format('YYYY-MM-DD');
        var url = baseUrl + '/employee/dashboard/get_paid_foc_count_ajax';
        $('#loader_gif2').css('display', 'inherit');
        $('#loader_gif2').attr('src', "<?php echo base_url(); ?>images/loadring.gif");
        $('#paid_vs_foc').hide();
        $.ajax({
            type: 'POST',
            url: url,
            data: {sDate: startDate, eDate: endDate, current_status: 'Completed', partner_id: partner_id},
            success: function (response) {
                $('#loader_gif2').attr('src', "");
                $('#loader_gif2').css('display', 'none');
                $('#paid_vs_foc').show();
                var data = JSON.parse(response);
                chart = new Highcharts.Chart({
                    chart: {
                        renderTo: 'paid_vs_foc',
                        type: 'pie',
                        plotBorderWidth: 0,
                        events: {
                            drilldown: function (e) {
                                if (!e.seriesOptions) {
                                    var get_date = $('#reportrange2 span').text().split('-');
                                    var startdate = get_date[0];
                                    var enddate = get_date[1];
                                    var type = e.point.name;
                                    var chart = this;
                                    foc_or_paid_data_groupby_services(e, startdate, enddate, type, chart);
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
                                    y: parseInt(data.paid),
                                    sliced: true,
                                    selected: true,
                                    drilldown: true
                                }]
                        }],
                    drilldown: {
                        series: []
                    }

                });
            }
        });
    });
    
     $('#reportrange3').on('apply.daterangepicker', function (ev, picker) {

        var startDate = picker.startDate.format('YYYY-MM-DD');
        var endDate = picker.endDate.format('YYYY-MM-DD');
        var url = baseUrl + '/employee/dashboard/get_data_onScroll/1';
        $('#loader_gif4').css('display', 'inherit');
        $('#loader_gif4').attr('src', "<?php echo base_url(); ?>images/loadring.gif");
        $('#services_type_booking_chart').hide();
        $.ajax({
            type: 'POST',
            url: url,
            data: {sDate: startDate, eDate: endDate,partner_id:partner_id},
            success: function (response) {
                $('#loader_gif4').attr('src', "");
                $('#loader_gif4').css('display', 'none');
                $('#services_type_booking_chart').show();
                if(response === 'No Data Found'){
                    $('#services_type_booking_chart').html(response).show();
                }else{
                    create_chart_on_ajax_call(response, true);
                }
                
            }
        });
    });
    
    $('#reportrange4').on('apply.daterangepicker', function (ev, picker) {

        var startDate = picker.startDate.format('YYYY-MM-DD');
        var endDate = picker.endDate.format('YYYY-MM-DD');
        var url = baseUrl + '/employee/dashboard/get_booking_data_by_region/1';
        $('#loader_gif5').css('display', 'inherit');
        $('#loader_gif5').attr('src', "<?php echo base_url(); ?>images/loadring.gif");
        $('#state_type_booking_chart').hide();
        $.ajax({
            type: 'POST',
            url: url,
            data: {sDate: startDate, eDate: endDate,partner_id:partner_id},
            success: function (response) {
                $('#loader_gif5').attr('src', "");
                $('#loader_gif5').css('display', 'none');
                $('#state_type_booking_chart').show();
                create_chart_based_on_bookings_state(response);
            }
        });
    });


    function get_partner_booking_based_services(e, chart, startdate, enddate, partner_name, partner_id, booking_status) {
        var url = baseUrl + '/employee/dashboard/get_partner_booking_based_on_services';

        var drilldown_data = [];
        chart.showLoading('Loading Data ...');
        $.ajax({
            type: 'POST',
            url: url,
            data: {sDate: startdate, eDate: enddate, current_status: booking_status, partner_id: partner_id},
            success: function (response) {
                console.log(response);
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

    function foc_or_paid_data_groupby_services(e, startdate, enddate, type, chart) {
        var url = baseUrl + '/employee/dashboard/get_paid_or_foc_booking_groupby_services';
        var services = [];
        var count = [];
        var drilldown_data = [];
        chart.showLoading('Loading Data ...');
        $.ajax({
            type: 'POST',
            url: url,
            data: {sDate: startdate, eDate: enddate, type: type, current_status: 'Completed', partner_id: partner_id},
            success: function (response) {
                var data = JSON.parse(response);
                services = data.services.split(',');
                count = JSON.parse("[" + data.count + "]");
                for (var i = 0; i < services.length; i++) {
                    var arr = [services[i], count[i]];
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
            $('#loader_gif3').css('display', 'inherit');
            $('#loader_gif3').attr('src', "<?php echo base_url(); ?>images/loadring.gif");
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/dashboard/get_data_onScroll/1',
                data: {sDate: startdate, eDate: enddate,partner_id:partner_id},
                success: function (data) {
                    if(data === "No Date Found"){
                        $('#loader_gif3').attr('src', "");
                        $('#loader_gif3').css('display', 'none');
                        $(".load_data_onScroll").html("No Data Found").show();
                    }else{
                        $('#loader_gif3').attr('src', "");
                        $('#loader_gif3').css('display', 'none');
                        create_chart_on_ajax_call(data, false);
                        
                    }
                    
                }
            });
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/dashboard/get_booking_data_by_region/1',
                data: {sDate: startdate, eDate: enddate,partner_id:partner_id},
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
                            var get_date = $('#reportrange3 span').text().split('-');
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
            data: {sDate: startdate, eDate: enddate, type: type,partner_id:partner_id},
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
        chart = new Highcharts.Chart({
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
</script>