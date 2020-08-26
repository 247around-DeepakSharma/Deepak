<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/drilldown.js"></script>

<style>
    .highcharts-button{
        display:none;
    }
    .highcharts-credits{
        display: none;
    }
</style>
<div class ="right_col">
    <div class="calender_holder row">
        <form class="form-inline col-md-12">
            <div class="form-group" style="float: right;">
                <input type="text" class="form-control" name="daterange" id="daterange_id" onchange="get_date_data()">
            </div>
        </form>
   </div>
    <div class="picChartHolder row">
    <div id="upcountry_chart" style="float:left;" class="col-md-4">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Escalation <small></small></h2>
                    <div class="nav navbar-right panel_toolbox">
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
    <div id="request_type" style="float:left;" class="col-md-4">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Escalation <small></small></h2>
                    <div class="nav navbar-right panel_toolbox">
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
    <div id="appliance" style="float:left;" class="col-md-4">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Escalation <small></small></h2>
                    <div class="nav navbar-right panel_toolbox">
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
</div>
    <div class="barChartHolder row" style="margin-top: 10px;">
         <div id="performance_chart" align="center">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Escalation <small></small></h2>
                    <div class="nav navbar-right panel_toolbox">
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
    </div>
</div>
<script>
    var post_request = 'POST';
     $(document).ready(function(){
        pie_chart_url =  '<?php echo base_url(); ?>employee/dashboard/get_escalations_chart_data/<?php echo $data['vendor_id']; ?>/<?php echo $data['startDate']; ?>/<?php echo $data['endDate']; ?>';
        get_escalations_pie_chart(pie_chart_url);
        get_sf_performance_bar_chart();
    });
 function sendAjaxRequest(postData, url,type) {
        return $.ajax({
            data: postData,
            url: url,
            type: type
        });
    }
   function get_sf_performance_bar_chart(){
        $('#loader_gif3').fadeIn();
        $('#state_type_booking_chart').fadeOut();
        var data = {};
        url =  '<?php echo base_url(); ?>employee/dashboard/get_sf_performance_bar_chart_data/<?php echo $data['vendor_id']; ?>';
        sendAjaxRequest(data,url,post_request).done(function(response){
            var obj =JSON.parse(response);
            var finalArray =[];
             for (var key in obj) {
                 var newObj = {};
                  var tempArray =[];
                 if(key !== "months"){
                     for (var i =0 ;i<obj[key].length;i++ ) { 
                             tempArray.push(Number(obj[key][i]));
                         }
                    newObj.name = key;
                    newObj.data = tempArray;
                    finalArray.push(newObj);
             }
             }
            seriesData =finalArray;
            categoriesData = obj.months;
            createBarChart("performance_chart","Monthly Performance Chart",'Service Centers Data','Numbers',categoriesData,seriesData);
        });
    }
    function get_escalations_pie_chart(customUrl){
        $('#loader_gif3').fadeIn();
        $('#state_type_booking_chart').fadeOut();
        var data = {};
        url =  customUrl;
        sendAjaxRequest(data,url,post_request).done(function(response){
            $('#loader_gif3').fadeOut();
            var obj =JSON.parse(response);
            for (var key in obj) {
                var keyObj = obj[key];
                var finalArray =[];
                for (var keyName in keyObj) { 
                    var newObj = {};
                    newObj.name = keyName;
                    newObj.y = Number(keyObj[keyName]);
                    newObj.drilldown = keyName;
                    finalArray.push(newObj);
                }
                if(key==='request_type'){
                    var data =finalArray;
                    var htmlID = "request_type";
                    var title= 'SF Escalation BY Request Type';
                    createPieChart(data,htmlID,title);
                }
                 if(key==='upcountry'){
                    var data =finalArray;
                    var htmlID = "upcountry_chart";
                    var title= 'SF Escalation By Upcountry';
                    createPieChart(data,htmlID,title);
                }
                 if(key==='appliance'){
                     var finalDrillDownArray =[];
                     for (var appliancekey in obj.service_upcountry) {
                         var tempArray2 =[];
                         var newdrilldownObj = {};
                         newdrilldownObj.name = appliancekey;
                         finalDrillDownArray.push(newdrilldownObj);
                         for (var i =0 ;i<obj.service_upcountry[appliancekey].length;i++ ) { 
                             var tempArray =[];
                             tempArray.push(obj.service_upcountry[appliancekey][i][0]);
                             tempArray.push(Number(obj.service_upcountry[appliancekey][i][1]));
                             tempArray2.push(tempArray);
                         }
                         newdrilldownObj.id = appliancekey;
                         newdrilldownObj.data = tempArray2;
                         
            }
                   var seriesData = [newdrilldownObj];
                    var data =finalArray;
                    var htmlID = "appliance";
                    var title= 'SF Escalation BY Appliance';
                    var text = 'Click the slices to view Upcountry vise breackdown';
                    createPieChartWithDrillDown(data,seriesData,htmlID,title,text);
                }
            }
        });
    }
    function createPieChart(data,htmlID,title){
        Highcharts.chart(htmlID, {
    chart: {
        plotBackgroundColor: null,
        plotBorderWidth: null,
        plotShadow: false,
        type: 'pie'
    },
    title: {
        text: title
    },
//    tooltip: {
//        pointFormat: '{series.name}: <b>{series.y}:</b>'
//    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: true,
                format: '{point.name}: {point.y}',
                style: {
                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black',
                     fontSize: 14
                }
            }
        }
    },
    series: [{
        name: "Total",
        colorByPoint: true,
        data: data
    }]
});
    }
    function createPieChartWithDrillDown(baseData,drilldownData,htmlID,title,text){
      // Create the chart
Highcharts.chart(htmlID, {
    chart: {
        type: 'pie'
    },
    title: {
        text: title
    },
    subtitle: {
        text: text
    },
    plotOptions: {
        series: {
            dataLabels: {
                enabled: true,
                format: '{point.name}: {point.y}',
                style:{
                            fontSize: 14
                        }
            }
        }
    },

    tooltip: {
        headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
        pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b> of total<br/>'
    },
    series: [{
        name: 'Appliance',
        colorByPoint: true,
        data:baseData
    }],
    drilldown: {
        series: drilldownData
    }
});
    }
    
    function createBarChart(htmlDiv,titleText,subTitleTax,yAxisTax,categoriesData,seriesData){
    Highcharts.chart(htmlDiv, {
    chart: {
        type: 'column'
    },
    title: {
        text: titleText
    },
    subtitle: {
        text: subTitleTax
    },
    xAxis: {
        categories: categoriesData,
        crosshair: true,
        labels: {
                style: {
                    fontSize:'14px'
                }
            }
    },
    yAxis: {
        min: 0,
        title: {
            text: yAxisTax
        },
        labels: {
                style: {
                    fontSize:'14px'
                }
            }
    },
    tooltip: {
        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
            '<td style="padding:0"><b>{point.y} </b></td></tr>',
        footerFormat: '</table>',
        shared: true,
        useHTML: true
    },
            legend: {
              itemStyle: {
                 font: '14pt Trebuchet MS, Verdana, sans-serif',
                 color: '#000'
              },
              itemHoverStyle: {
                 color: 'green'
              },
              itemHiddenStyle: {
                 color: 'blue'
              }

        },
    plotOptions: {
                column: {
                    dataLabels: {
                        enabled: true,
                        crop: false,
                        overflow: 'none',
                        style:{
                            fontSize: 13
                        }
                    }
                }
            },
    series: seriesData
});
  }
 $(function() {
        var startDate = '<?php echo $data['startDate']; ?>';
        var endDate = '<?php echo $data['endDate']; ?>';
    $('input[name="daterange"]').daterangepicker({
        timePicker: true,
        timePickerIncrement: 30,
        locale: {
            format: 'YYYY-MM-DD'
        },
        startDate: startDate,
        endDate: endDate
    });
});
//$('#daterange_id').change(){
//    alert($('#daterange_id').val);
//    }
$("daterange_id").change(function(){
    alert( $('#daterange_id').val());
});
function get_date_data(){
    var dateRange = $('#daterange_id').val().split(" - ");
    pie_chart_url =  '<?php echo base_url(); ?>employee/dashboard/get_escalations_chart_data/<?php echo $data['vendor_id']; ?>/'+dateRange[0]+"/"+dateRange[1]; 
   get_escalations_pie_chart(pie_chart_url);
    }
    </script>