<?php if(isset($ajax_call)){?>
<div id="chart_container" style="height: 500px;margin-top:20px;width: 100%"></div>

                    <!-- table data div -->
                    <table class="table table-striped table-bordered" id="sum_table" style="display:none;margin-top:20px;">
                        <tr class="titlerow">
                            <th>Partner Name</th>
                            <th>Completed Bookings</th>
                        </tr>
                        <tbody>
                            <?php foreach ($data as $key => $value) { ?>
                                <tr>
                                    <td><?php echo $value['public_name']; ?></td>
                                    <td id="input_count"><?php echo $value['completed']; ?></td>
                                </tr>
                            <?php } ?>
                            <tr class="totalColumn info">
                                <td><b>Total<b></td>
                                            <td class="totalCol">-</td>
                                            </tr>
                                            </tbody>
                                            </table>

                                            <!-- show/hide table/chart JS -->
                                            <script>
                                                function toggle_btn() {
                                                    $('#sum_table').toggle();
                                                    $('#chart_container').toggle();
                                                    $(".toggle-btn span").html($(".toggle-btn span").html() == 'Show Table' ? 'Show Chart' : 'Show Table');
                                                    $(this).toggleClass('red');
                                                }
                                            </script>

                                            <!-- table column sum JS -->
                                            <script type="text/javascript">
                                                $("#sum_table tr:last td:not(:first)").text(function (i) {
                                                    var t = 0;
                                                    $(this).parent().prevAll().find("td:nth-child(" + (i + 2) + ")").each(function () {
                                                        //console.log(t);
                                                        t += parseInt($(this).text(), 10) || 0;
                                                    });
                                                    return t;
                                                });
                                            </script>

                                            <!-- load required JS for creating chart -->
                                            <script src="https://code.highcharts.com/highcharts.js"></script>

                                             <!-- create chart using MySQL data -->
                                            <script>
                                                var partner_name = [];
                                                var completed_booking = [];

                                            <?php foreach ($data as $value) { ?>
                                                        partner_name.push("<?php echo $value['public_name']; ?>");
                                                        completed_booking.push(parseInt("<?php echo $value['completed'];  ?>"));


                                            <?php } ?>

                                                window.chart = new Highcharts.Chart({
                                                    chart: {
                                                        renderTo: 'chart_container',
                                                        type: 'column',
                                                        events: {
                                                            load: Highcharts.drawTable
                                                        },
                                                    },
                                                    title: {
                                                        text: 'Partner Completed Booking',
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
                                                    legend: {
                                                        layout: 'vertical',
                                                        align: 'right',
                                                        verticalAlign: 'middle',
                                                        borderWidth: 0
                                                    },
                                                    series: [
                                                        {
                                                            name: 'Completed Booking',
                                                            data: completed_booking
                                                        }]
                                                });

                                            </script>
<?php }else{?>
<div id="page-wrapper" >
    <div class="container-fluid" >
        <div class="panel panel-info" style="margin-top:20px;">
            <div class="panel-heading">
                <h2>Partner Completed Booking Report</h2>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-3">
                        <?php $last_url_parm = $this->uri->segment(3); ?>
                        <select  onchange="get_data()" class="form-control"  id="period" name="period" >
                            <option  disabled>Select Period</option>
                            <option  value = "cuurent_month">Current Month</option>
                            <option value="last_month">Last Month</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary toggle-btn" onclick="toggle_btn()" id="toggle-btn"><span>Show Table</span></button>
                    </div>
                </div>
                <!-- chart div -->
                <div class="chart_div">
                    <div id="chart_container" style="height: 500px;margin-top:20px;width: 100%"></div>

                    <!-- table data div -->
                    <table class="table table-striped table-bordered" id="sum_table" style="display:none;margin-top:20px;">
                        <tr class="titlerow">
                            <th>Partner Name</th>
                            <th>Completed Bookings</th>
                        </tr>
                        <tbody>
                            <?php foreach ($data as $key => $value) { ?>
                                <tr>
                                    <td><?php echo $value['public_name']; ?></td>
                                    <td id="input_count"><?php echo $value['count']; ?></td>
                                </tr>
                            <?php } ?>
                            <tr class="totalColumn info">
                                <td><b>Total<b></td>
                                            <td class="totalCol">-</td>
                                            </tr>
                                            </tbody>
                                            </table>

                                            <!-- show/hide table/chart JS -->
                                            <script>
                                                function toggle_btn() {
                                                    $('#sum_table').toggle();
                                                    $('#chart_container').toggle();
                                                    $(".toggle-btn span").html($(".toggle-btn span").html() == 'Show Table' ? 'Show Chart' : 'Show Table');
                                                    $(this).toggleClass('red');
                                                }
                                            </script>

                                            <!-- table column sum JS -->
                                            <script type="text/javascript">
                                                $("#sum_table tr:last td:not(:first)").text(function (i) {
                                                    var t = 0;
                                                    $(this).parent().prevAll().find("td:nth-child(" + (i + 2) + ")").each(function () {
                                                        //console.log(t);
                                                        t += parseInt($(this).text(), 10) || 0;
                                                    });
                                                    return t;
                                                });
                                            </script>

                                            <!-- load required JS for creating chart -->
                                            <script src="https://code.highcharts.com/highcharts.js"></script>

                                             <!-- create chart using MySQL data -->
                                            <script>
                                                var partner_name = [];
                                                var completed_booking = [];

                                            <?php foreach ($data as $value) { ?>
                                                        partner_name.push("<?php echo $value['public_name']; ?>");
                                                        completed_booking.push(parseInt("<?php echo $value['count'];  ?>"));


                                            <?php } ?>

                                                window.chart = new Highcharts.Chart({
                                                    chart: {
                                                        renderTo: 'chart_container',
                                                        type: 'column',
                                                        events: {
                                                            load: Highcharts.drawTable
                                                        },
                                                    },
                                                    title: {
                                                        text: 'Partner Completed Booking',
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
                                                    legend: {
                                                        layout: 'vertical',
                                                        align: 'right',
                                                        verticalAlign: 'middle',
                                                        borderWidth: 0
                                                    },
                                                    series: [
                                                        {
                                                            name: 'Completed Booking',
                                                            data: completed_booking
                                                        }]
                                                });

                                            </script>
                </div>
            </div>
             <!-- show data on dropdown selection JS -->
                                            <script type="text/javascript">
                                                $('#period').select2();
                                                var date = new Date();
                                                var firstDay = new Date(date.getFullYear(), date.getMonth()-1, 1);
                                                var lastDay = new Date(date.getFullYear(), date.getMonth(), 0);

                                                function get_data()
                                                {
                                                    $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>BookingSummary/get_partners_booking_report_chart',
            data: {sDate: firstDay, eDate: lastDay, booking_status: 'Completed'},
            success: function (response) {
                //console.log(response);
//                $('#loader_gif1').attr('src', "");
//                $('#loader_gif1').css('display', 'none');
                var data = JSON.parse(response);
                var partner_name = data.partner_name.split(',');
                var completed_booking = JSON.parse("[" + data.completed_booking + "]");
//                var booking_type = $('#booking_status').val();
//                $('#chart_container').show();
                //console.log(booking_type);
                chart = new Highcharts.Chart({
                    chart: {
                        renderTo: 'chart_container',
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
                    legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'middle',
                        borderWidth: 0
                    },
                    series: [
                        {
                            showInLegend: false,
                            name: 'completed booking',
                            data: completed_booking
                        }]
                });
            }
        });
                                                    
                                                }
                                            </script>
        </div>
    </div>
</div>
<?php }?>