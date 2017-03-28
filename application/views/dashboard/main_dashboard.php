<style>
    .tile_stats_count{color:#333;}
    .tile_count .tile_stats_count, ul.quick-list li {
        white-space: normal;
        overflow: visible;
        text-overflow: clip;
    }
    .highcharts-credits,.highcharts-button-symbol{display:none}
</style>

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
                                    <option>FollowUp</option>
                                    <option>Pending</option>
                                    <option>Rescheduled</option>
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
                <div class="col-md-12"><center><img id="loader_gif1" src="" style="display: none;"></center></div>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div id="chart_container" class="chart_container"></div>
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
                <div class="col-md-12"><center><img id="loader_gif2" src="" style="display: none;"></center></div>
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
                                    <option>FollowUp</option>
                                    <option>Pending</option>
                                    <option>Rescheduled</option>
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
                <div class="col-md-12"><center><img id="loader_gif3" src="" style="display: none;"></center></div>
                <div id="piechart" class="piechart"></div>

                <div class="clearfix"></div>
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

<?php foreach ($partner_data as $key => $value) { ?>
        partner_name.push("<?php echo $value['public_name']; ?>");
        completed_booking.push(parseInt("<?php echo $value['count']; ?>"));
        partner_id['<?php echo $value['public_name']; ?>'] = <?php echo $value['partner_id']; ?>


<?php } ?>
    
    window.chart = new Highcharts.Chart({
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
                    enabled: true,
                    crop: false,
                    overflow: 'none'
                }
            }
        },
        series: [
            {
                name: 'Completed Booking',
                data: completed_booking,
                cursor: 'pointer',
                point: {
                    events: {
                        click: function () {
                            window.open(baseUrl+'/employee/dashboard/partner_reports/'+this.category+'/'+partner_id[this.category], '_blank');
                        }
                    }
                }
            }]
    });


    window.chart = new Highcharts.Chart({
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
            categories: <?php echo $agent_name; ?>
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
                data: [<?php echo $query_cancel; ?>]
            }, {
                name: 'Booked Queries',
                data: [<?php echo $query_booking; ?>]
            },
            {
                name: 'Outgoing Calls',
                data: [<?php echo $calls_placed; ?>]
            }, {
                name: 'Incoming Calls',
                data: [<?php echo $calls_received; ?>]
            }]
    });

    $('#reportrange').on('apply.daterangepicker', function (ev, picker) {

        var startDate = picker.startDate.format('YYYY-MM-DD');
        var endDate = picker.endDate.format('YYYY-MM-DD');
        var booking_status = $('#booking_status').val();
        var url = baseUrl + '/BookingSummary/get_partners_booking_report_chart';
        $('#loader_gif1').css('display', 'inherit');
        $('#loader_gif1').attr('src', "<?php echo base_url(); ?>images/loader.gif");
        $('#chart_container').hide();
        $.ajax({
            type: 'POST',
            url: url,
            data: {sDate: startDate, eDate: endDate, booking_status: booking_status},
            success: function (response) {
                var partners = [];
                var bookings = [];
                var partnerid = [];
                $.each(JSON.parse(response), function (key, value) {
                    partners.push(value.public_name);
                    bookings.push(parseInt(value.count));
                    partnerid[value.public_name]=parseInt(value.partner_id);

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
                            name: booking_status,
                            data: bookings,
                            cursor: 'pointer',
                            point: {
                                events: {
                                    click: function () {
                                        window.open(baseUrl+'/employee/dashboard/partner_reports/'+this.category+'/'+partnerid[this.category], '_blank');
                                    }
                                }
                            }
                        }]
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
        var url = baseUrl + '/BookingSummary/agent_working_details_ajax';
        $('#loader_gif2').css('display', 'inherit');
        $('#loader_gif2').attr('src', "<?php echo base_url(); ?>images/loader.gif");
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
                chart = new Highcharts.Chart({
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
                        }]
                });
            }
        });

        //console.log("apply event fired, start/end dates are " + picker.startDate.format('MMMM D, YYYY') + " to " + picker.endDate.format('MMMM D, YYYY'));
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
    $(document).ready(function () {

        // Build the chart
        Highcharts.chart('piechart', {
            chart: {
                type: 'pie',
                events: {
                    drilldown: function (e) {
                        if (!e.seriesOptions) {
                            var get_date = $('#reportrange3 span').text().split('-');
                            var startdate = get_date[0];
                            var enddate = get_date[1];
                            var type = e.point.name;
                            var booking_status = $('#booking_status2').val();
                            var chart = this;
                            foc_paid_ajax_data(e, startdate, enddate, type, chart, booking_status);

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
                            y: <?php echo $foc_and_paid[0]['FOC']; ?>,
                            drilldown: true
                        }, {
                            name: 'PAID',
                            y: <?php echo $foc_and_paid[0]['Paid']; ?>,
                            sliced: true,
                            selected: true,
                            drilldown: true
                        }]
                }],
            drilldown: {
                series: []
            }
        });
    });
    $('#reportrange3').on('apply.daterangepicker', function (ev, picker) {

        var startDate = picker.startDate.format('YYYY-MM-DD');
        var endDate = picker.endDate.format('YYYY-MM-DD');
        var booking_status = $('#booking_status2').val();
        var url = baseUrl + '/employee/dashboard/get_paid_foc_count_ajax';
        $('#loader_gif3').css('display', 'inherit');
        $('#loader_gif3').attr('src', "<?php echo base_url(); ?>images/loader.gif");
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
                chart = new Highcharts.Chart({
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

    function foc_paid_ajax_data(e, startdate, enddate, type, chart, booking_status) {
        var url = baseUrl + '/employee/dashboard/get_total_foc_or_paid_booking';
        var partner_name = [];
        var count = [];
        var drilldown_data = [];
        chart.showLoading('Loading Data ...');
        $.ajax({
            type: 'POST',
            url: url,
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
</script>