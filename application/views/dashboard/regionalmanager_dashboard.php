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

        <div class="clearfix"></div>
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Missing Pincodes</h2>
                    <div class="clearfix"></div>
                </div>

                <div class="x_content">

                    <div class="table-responsive" id="pincode_table_data">
                        <center><img id="loader_gif_unit" src="<?php echo base_url(); ?>images/loadring.gif" ></center>
                    </div>
                </div>
                <a class="btn btn-success"  href="<?php echo base_url()?>employee/dashboard/missing_pincode_full_view" target="_blank" style="float: right;">Full View</a>
            </div>
        </div>
        <div class="clearfix"></div>
<div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Escalation</h2>
                    <div class="clearfix"></div>
                </div>

                <div class="x_content">

<div class="table-responsive" id="escalation_data">
    <button type="button" class="btn btn-info" ng-click="mytoggle=!mytoggle" id="order_by_toggal" onclick="change_toggal_text()"style="float:right">Sort By Number Of Escalation</button>
    <form class="form-inline"style="float:left;background: #46b8da;color: #fff;padding: 3px;border-radius: 4px;">
        <div class="form-group">
            <input type="text" class="form-control" name="daterange" id="daterange_id">
        </div>
        <a href="#" ng-click="daterangeloadView()" class="btn btn-default" style="margin:0px;">Get Data</a>
    </form>
    <p>
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
     <tr ng-repeat="y in escalationData  |orderBy:!mytoggle?'-esclation_per':'-total_escalation' | limitTo : 5">
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
                <div class="full_view_form_container" style="float:right;">
                <form method="post" action="<?php echo base_url()?>employee/dashboard/escalation_full_view" target="_blank">
                    <input type="text"  id="sf_json_data" name="sf_json_data" value="apple" style="display:none;"/>
                    <button type="submit" class="btn btn-success">Full View</button>
                    </form>
                    </div>
            </div>
        </div>
    </div>
<div class="clearfix"></div>
<div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Pending Bookings More Then 5 days</h2>
                    <div class="clearfix"></div>
                </div>

                <div class="x_content">

<div class="table-responsive" id="escalation_data">
    <table class="table table-striped table-bordered jambo_table bulk_action">
    <thead>
      <tr>
        <th>S.no</th>
        <th>Booking_id</th>
        <th>Pending Days</th>
        <th>Assign SF</th>
        <th>escalated</th>
      </tr>
    </thead>
    <tbody>
     <tr>
        <td>1</td>
        <td><a type="button" class="btn btn-info" target="_blank" href="#">SS-123456789</a></td>
        <td>6</td>
        <td>Amritsar Harjit Electronics</td>
         <td>1 Times</td>
      </tr>
      <tr>
        <td>2</td>
       <td><a type="button" class="btn btn-info" target="_blank" href="#">SS-9873456789</a></td>
        <td>5</td>
        <td>Amritsar Harjit Electronics</td>
         <td>2 Times</td>
      </tr>
      <tr>
        <td>3</td>
        <td><a type="button" class="btn btn-info" target="_blank" href="#">SS-9876099999</a></td>
        <td>5</td>
        <td>Bareilly Alpha Video Care</td>
         <td>2 Times</td>
      </tr>
      <tr>
        <td>4</td>
        <td><a type="button" class="btn btn-info" target="_blank" href="#">SS-76752699999</a></td>
        <td>5</td>
        <td>Amritsar Harjit Electronics</td>
         <td>0 Times</td>
      </tr>
      <tr>
        <td>5</td>
        <td><a type="button" class="btn btn-info" target="_blank" href="#">SS-65876099999</a></td>
        <td>5</td>
        <td>Bareilly Alpha Video Care</td>
         <td>2 Times</td>
      </tr>
    </tbody>
    </table>
<!--                    <center><img id="loader_gif_unit_escalation" src="<?php echo base_url(); ?>images/loadring.gif" ></center>-->
                    </div>
                </div>
                <div class="full_view_form_container" style="float:right;">
                <form method="post" action="<?php echo base_url()?>employee/dashboard/escalation_full_view" target="_blank">
                    <input type="text"  id="sf_json_data" name="sf_json_data" value="apple" style="display:none;"/>
                    <button type="submit" class="btn btn-success">Full View</button>
                    </form>
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
    $('input[name="daterange"]').daterangepicker({
        timePicker: true,
        timePickerIncrement: 30,
        locale: {
            format: 'YYYY-MM-DD'
        }
    });
});
    </script>

