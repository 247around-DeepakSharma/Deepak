<script src="http://code.highcharts.com/3.0.2/highcharts.js"></script>
<style type="text/css">
     div.vertical-line{
      width: 1px; 
      background-color: black;
      height: 100%; 
      float: left; 
    }
</style>
<div class='panel panel-info' style="width: 96%;margin-left: 2%;">
    <div class="panel-heading" style="font-size:120%;"><center><b>RM Performance Stats</b></center></div>
    <div class="panel-body">
        <div id="sf_crimes_report" class="col-md-6"></div>
        <div class="vertical-line" />
        <div id="sf_snapshot_report" class="col-md-6"></div>
    </div>
</div>
<script type="text/javascript">

   var user_group = '<?php echo $user_group?>';
$(window).bind('load',function(){
   
   $('text tspan:contains("Highcharts.com")').css('display','none'); 
});

$(function () {
        $('#sf_crimes_report').highcharts({
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
//                formatter: function() {
//                    return '<a href="javascript:alert(\'hello\')">'+
//                        this.value +'</a>';
//                }
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

$(function () {
        $('#sf_snapshot_report').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: 'RM Booking Report'
        },
        subtitle: {
             text: '(Click Name for Details)'
       },
        xAxis: {
            categories: [<?php echo $rm?>],
            crosshair: true,
            labels: {
                useHTML: true,
                formatter: function() {
                    //Showing Link only to Admin Group
                    if(user_group == 'admin'){
                    return '<a href="<?php echo base_url()?>BookingSummary/show_rm_specific_snapshot/'+encodeURI(this.value)+'" target="_blank">'+
                        this.value +'</a>';
                    }else{
                        //No Link is attached
                        return '<a href="javascript:void(0)">'+
                        this.value +'</a>';
                    }
                    
                },
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Bookings Count'
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
            name: 'Monthly Completed',
            data: [<?php echo $month_completed?>]

        }, {
            name: 'Monthly Cancelled',
            data: [<?php echo $month_cancelled?>]

        }, {
            name: 'Last 0-2 Days',
            data: [<?php echo $last_2_day?>]

        }
        , {
            name: 'Last 2-3 Days',
            data: [<?php echo $last_3_day?>]

        }
        , {
            name: '>5 Days',
            data: [<?php echo $greater_than_5_days?>]

        }
            ]
    });
});


</script>