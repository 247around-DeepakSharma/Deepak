<script src="http://code.highcharts.com/3.0.2/highcharts.js"></script>

<div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>

<script type="text/javascript">

$(function () {
        $('#container').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: 'RM Crime Report'
        },
        xAxis: {
            categories: [<?php echo $rm?>],
            crosshair: true,
            labels: {
                //useHTML: true,
                formatter: function() {
                    return '<a href="javascript:alert(\'hello\')">'+
                        this.value +'</a>';
                }
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Crimes Count'
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.f}</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: [{
            name: 'Updated',
            data: [<?php echo $updated?>]

        }, {
            name: 'Not Updated',
            data: [<?php echo $not_updated?>]

        }, {
            name: 'Total Bookings',
            data: [<?php echo $total_booking?>]

        }
        , {
            name: 'Monthly Crimes',
            data: [<?php echo $monthly_total_crimes?>]

        }
        , {
            name: 'Monthly Escalations',
            data: [<?php echo $monthly_escalations?>]

        }
            ]
    });
});

</script>