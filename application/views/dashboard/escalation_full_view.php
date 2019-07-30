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
<div class="right_col" role="main" ng-app="rm_escalation">
    <div class="row" ng-controller="rm_escalationController">
<div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Escalation</h2>
                    <div class="clearfix"></div>
                </div>

                <div class="x_content">

<div class="table-responsive" id="escalation_data">
    <?php
    $RmArray = explode("_",$rm);
    ?>
    <p id="rm_id_holder" style="display:none;"><?php echo $RmArray[1]; ?></p>
    <button type="button" class="btn btn-info" ng-click="mytoggle=!mytoggle" id="order_by_toggal" onclick="change_toggal_text()"style="float:right">Sort By Number Of Escalation</button>
 <form class="form-inline"style="float:left;background: #46b8da;color: #fff;padding: 3px;border-radius: 4px;">
        <div class="form-group">
            <input type="text" class="form-control" name="daterange" id="daterange_id" ng-change="daterangeloadView()" ng-model="dates">
        </div>
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
     <tr ng-repeat="y in escalationData  |orderBy:!mytoggle?'-esclation_per':'-total_escalation' | limitTo:totalItems">
        <td>{{$index+1}}</td>
         <td><a type="button" id="vendor_{{y.vendor_id}}" class="btn btn-info" target="_blank" href="<?php echo base_url(); ?>employee/vendor/show_escalation_graph_by_sf/{{y.vendor_id}}/{{y.startDate}}/{{y.endDate}}"><?php if($this->session->userdata('partner_id')){ ?> {{y.sf_name_for_partner}} <?php } else{ ?> {{y.vendor_name}} <?php } ?></a></td>
        <td>{{y.total_booking}}</td>
        <td>{{y.total_escalation}}</td>
         <td>{{y.esclation_per}}%</td>
      </tr>
    </tbody>
    </table>
                 <center><img id="loader_gif_escalation_n" src="<?php echo base_url(); ?>images/loadring.gif" ></center>
    <button type="button" class="btn btn-success" id="full_view_escalation" ng-click="full_view_escalation()" style="float: right;">Show All SF</button>
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
       var startDate = '<?php echo $startDate ?>';
        var endDate = '<?php echo $endDate ?>';
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
    </script>

