<div id="page-wrapper" >
    <div class="container-fluid" >
        <div class="panel panel-info" style="margin-top:20px;">
            <div class="panel-heading">
                <h2>Agents Booking Stats </h2>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-3">
                        <?php $last_url_parm = $this->uri->segment(3); ?>
                        <select  onchange="get_data()" class="form-control"  id="period" name="period" >
                            <option  disabled>Select Period</option>
                            <option  value = "today" <?php if ($last_url_parm == 'today') { ?> selected <?php } ?> >Today</option>
                            <option value="yesterday" <?php if ($last_url_parm == 'yesterday') { ?> selected <?php } ?>>Yesterday</option>
                            <option value="week" <?php if ($last_url_parm == 'week') { ?> selected <?php } ?>>Weekly</option>
                            <option value="month" <?php if ($last_url_parm == 'month') { ?> selected <?php } ?>>Current Month</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary toggle-btn" onclick="toggle_btn()" id="toggle-btn"><span>Show Table</span></button>
                    </div>
                </div>
                <!-- chart div -->
                <div id="chart_container" style="height: 500px;margin-top:20px;width: 100%"></div>

                <!-- table data div -->
                <table class="table table-striped table-bordered" id="sum_table" style="display:none;margin-top:20px;">
                    <tr class="titlerow">
                        <th>Agent Name</th>
                        <th>Cancelled Queries</th>
                        <th>Booked Queries</th>
                        <th>Outgoing Calls</th>
                        <th>Incoming Calls</th>
                        <th>Rating</th>
                    </tr>
                    <tbody>
                        <?php foreach ($data as $key => $value) { ?>
                            <tr>
                                <td><?php echo $value['employee_id']; ?></td>
                                <td id="input_count"><?php echo $value['followup_to_cancel']; ?></td>
                                <td id="input_count"><?php echo $value['followup_to_pending']; ?></td>
                                <td id="input_count"><?php echo $value['calls_placed']; ?></td>
                                <td id="input_count"><?php echo $value['calls_recevied']; ?></td>
                                <td id="input_count"><?php echo $value['rating']; ?></td>
                            </tr>
                        <?php } ?>
                        <tr class="totalColumn info">
                            <td><b>Total<b></td>
                                        <td class="totalCol">-</td>
                                        <td class="totalCol">-</td>
                                        <td class="totalCol">-</td>
                                        <td class="totalCol">-</td>
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

                                        <!-- show data on dropdown selection JS -->
                                        <script type="text/javascript">
                                            $('#period').select2();

                                            function get_data()
                                            {
                                                var period = $("#period option:selected").val();
                                                var href = '<?php echo base_url() ?>BookingSummary/agent_working_details/' + period;
                                                window.location.href = href;
                                            }
                                        </script>

                                        <!-- load required JS for creating chart -->
                                        <script src="https://code.highcharts.com/highcharts.js"></script>
                                        <!--<script src="http://code.highcharts.com/modules/exporting.js"></script>-->

                                        <!-- create chart using MySQL data -->
                                        <script>
                                            var agent_name = [];
                                            var query_cancel = [];
                                            var query_booking = [];
                                            var calls_placed = [];
                                            var calls_received = [];
                                            var ratings = [];

<?php foreach ($data as $value) { ?>
                                                agent_name.push("<?php echo $value['employee_id']; ?>");
                                                query_cancel.push(parseInt("<?php echo $value['followup_to_cancel']; ?>"));
                                                query_booking.push(parseInt("<?php echo $value['followup_to_pending']; ?>"));
                                                calls_placed.push(parseInt("<?php echo $value['calls_placed']; ?>"));
                                                calls_received.push(parseInt("<?php echo $value['calls_recevied']; ?>"));
                                                ratings.push(parseInt("<?php echo $value['rating']; ?>"));


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
                                                    text: 'Agents Booking Stats',
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
                                                        data: query_cancel
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
                                                        name: 'Rating',
                                                        data: ratings
                                                    }]
                                            });

                                        </script>