<script src="<?php echo base_url(); ?>js/base_url.js"></script>
<script src = "https://ajax.googleapis.com/ajax/libs/angularjs/1.3.14/angular.min.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/app.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/controller/controllers.js"></script>
<style>
    .col-md-2 {
        width: 16.666667%;
    }
    .tile_count .tile_stats_count, ul.quick-list li {
        white-space: normal;
    }
</style>
<div class="right_col" role="main" ng-app="rm_dashboard">
    

    <div class="row" ng-controller="rm_dashboardController">
<div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Escalation</h2>
                    <div class="clearfix"></div>
                </div>

                <div class="x_content">

<div class="table-responsive" id="escalation_data">
    <input type="text" id="session_id_holder" style="display:none;" value="<?php if($this->session->userdata('user_group') == 'regionalmanager') {echo $this->session->userdata('id');} ?>">
    <button type="button" class="btn btn-info" ng-click="mytoggle=!mytoggle" id="order_by_toggal" onclick="change_toggal_text()"style="float:right">Sort By Number Of Escalation</button>
<form class="form-inline"style="float:left;background: #46b8da;color: #fff;padding: 3px;border-radius: 4px;">
        <div class="form-group">
            <input type="text" class="form-control" name="daterange" id="daterange_id">
        </div>
        <a href="#" ng-click="daterangeloadView()" class="btn btn-default" style="margin:0px;">Get Data</a>
    </form>
    <table class="table table-striped table-bordered jambo_table bulk_action">
    <thead>
      <tr>
        <th>S.no</th>
        <th>Vendor</th>
        <th>Total Booking</th>
        <th>Total Escalation</th>
        <th>Escalation %</th>
      </tr>
    </thead>
    <tbody>
     <tr ng-repeat="y in escalationData  |orderBy:!mytoggle?'-esclation_per':'-total_escalation'">
        <td>{{$index+1}}</td>
        <td><a type="button" class="btn btn-info" target="_blank" href="<?php echo base_url(); ?>employee/vendor/show_escalation_graph_by_sf/{{y.vendor_id}}">{{y.vendor_name}}</a></td>
        <td>{{y.total_booking}}</td>
        <td>{{y.total_escalation}}</td>
         <td>{{y.esclation_per}}%</td>
      </tr>
    </tbody>
    </table>
<!--                    <center><img id="loader_gif_unit_escalation" src="<?php echo base_url(); ?>images/loadring.gif" ></center>-->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- END -->
</div>
<style>
    .dropdown:hover .dropdown-menu {
display: block;
}
</style>
<script>
    function change_toggal_text(){
        var currentValue = document.getElementById("order_by_toggal").innerHTML;
        if(currentValue =="Sort By Number Of Escalation"){
            document.getElementById("order_by_toggal").innerHTML="Sort BY perentage";
        }
        else{
             document.getElementById("order_by_toggal").innerHTML="Sort By Number Of Escalation";
        }
    }
$(function() {
        var d = new Date();
        n = d.getMonth()+1;
        y = d.getFullYear();
        date = d.getDate();
    $('input[name="daterange"]').daterangepicker({
        timePicker: true,
        timePickerIncrement: 30,
        locale: {
            format: 'YYYY-MM-DD'
        },
        startDate: y+'-'+n+'-01'
    });
});
    </script>

