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
<!--    <div class="calender_holder">
    <form class="form-inline"style="float:right;background: #46b8da;color: #fff;padding: 3px;border-radius: 4px;margin-right:70px;">
        <div class="form-group">
            <input type="text" class="form-control" name="daterange" id="daterange_id">
        </div>
        <a href="#" ng-click="daterangeloadView()" class="btn btn-default" style="margin:0px;">Get Data</a>
    </form>
        </div>-->
    <div class="clear"></div>
    <div id="upcountry_chart" style="float:left;width:35%">
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
    <div id="request_type" style="float:left;width:30%">
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
    <div id="appliance" style="float:left;width:30%">
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

<script>
    var post_request = 'POST';
     $(document).ready(function(){
        get_escalations_pie_chart();
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
        });
    }
    function get_escalations_pie_chart(){
        $('#loader_gif3').fadeIn();
        $('#state_type_booking_chart').fadeOut();
        var data = {};
        url =  '<?php echo base_url(); ?>employee/dashboard/get_escalations_chart_data/<?php echo $data['vendor_id']; ?>';
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
            console.log(JSON.stringify(finalArray));
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
    tooltip: {
        pointFormat: '{series.name}: <b>{series.y}:</b>'
    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: true,
                format: '{point.name}: {point.y}',
                style: {
                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                }
            }
        }
    },
    series: [{
        name: 'Brands',
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
                format: '{point.name}: {point.y}'
            }
        }
    },

    tooltip: {
        headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
        pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b> of total<br/>'
    },
    series: [{
        name: 'Brands',
        colorByPoint: true,
        data:baseData
    }],
    drilldown: {
        series: drilldownData
    }
});
    }
    </script>