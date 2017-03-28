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

            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Bar Chart Group <small>Sessions</small></h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>
                            <!--                      <li class="dropdown">
                                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                                    <ul class="dropdown-menu" role="menu">
                                                      <li><a href="#">Settings 1</a>
                                                      </li>
                                                      <li><a href="#">Settings 2</a>
                                                      </li>
                                                    </ul>
                                                  </li>-->
                            <li><a class="close-link"><i class="fa fa-close"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content2">
                        <div id="graphx" style="width:100%; height:300px;" ></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Pie Chart <small>Sessions</small></h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>
                            <!--                      <li class="dropdown">
                                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                                    <ul class="dropdown-menu" role="menu">
                                                      <li><a href="#">Settings 1</a>
                                                      </li>
                                                      <li><a href="#">Settings 2</a>
                                                      </li>
                                                    </ul>
                                                  </li>-->
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
                            <!--                      <li class="dropdown">
                                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                                    <ul class="dropdown-menu" role="menu">
                                                      <li><a href="#">Settings 1</a>
                                                      </li>
                                                      <li><a href="#">Settings 2</a>
                                                      </li>
                                                    </ul>
                                                  </li>-->
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
                            <!--                      <li class="dropdown">
                                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                                    <ul class="dropdown-menu" role="menu">
                                                      <li><a href="#">Settings 1</a>
                                                      </li>
                                                      <li><a href="#">Settings 2</a>
                                                      </li>
                                                    </ul>
                                                  </li>-->
                            <li><a class="close-link"><i class="fa fa-close"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content2">
                        <div id="graph_line" style="width:100%; height:400px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /page content -->

<script>
    var partner_name = '<?php echo $partner_name; ?>';
    var partner_id = '<?php echo $partner_id; ?>';

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
                            y: <?php echo $foc; ?>,
                            drilldown: true
                        }, {
                            name: 'PAID',
                            y: <?php echo $paid; ?>,
                            drilldown: true
                        }]
                }],
            drilldown: {
                series: []
            }
        });
    });
    
    
    $('#reportrange').on('apply.daterangepicker', function (ev, picker) {

        var startDate = picker.startDate.format('YYYY-MM-DD');
        var endDate = picker.endDate.format('YYYY-MM-DD');
        var url = baseUrl + '/employee/dashboard/get_partner_booking_ajax_data';
        $('#loader_gif1').css('display', 'inherit');
        $('#loader_gif1').attr('src', "<?php echo base_url(); ?>images/loader.gif");
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
    
    var start = moment().startOf('month');
    var end = moment().endOf('month');
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
    
    $('#reportrange2').on('apply.daterangepicker', function (ev, picker) {

        var startDate = picker.startDate.format('YYYY-MM-DD');
        var endDate = picker.endDate.format('YYYY-MM-DD');
        var url = baseUrl + '/employee/dashboard/get_paid_foc_count_ajax';
        $('#loader_gif2').css('display', 'inherit');
        $('#loader_gif2').attr('src', "<?php echo base_url(); ?>images/loader.gif");
        $('#paid_vs_foc').hide();
        $.ajax({
            type: 'POST',
            url: url,
            data: {sDate: startDate, eDate: endDate, current_status: 'Completed',partner_id: partner_id},
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
            data: {sDate: startdate, eDate: enddate, type: type, current_status: 'Completed',partner_id: partner_id},
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
</script>