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
        <!-- Booking Report Start-->
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                    <h2>RM Booking Report</h2>
                    <div class="clearfix"></div>
                </div>
    <div class="table-responsive" id="escalation_data" ng-controller="pendngBooking_Controller">
    <table class="table table-striped table-bordered jambo_table bulk_action">
    <thead>
      <tr>
        <th>S.no</th>
        <th>RM</th>
        <th>0 to 2 days(Installation)</th>
        <th>3 to 5 Days(Installation)</th>
        <th> >5 Days(Installation)</th>
        <th>0 to 2 days(Repair)</th>
        <th>3 to 5 Days(Repair)</th>
        <th>>5 Days(Repair)</th>
         <th>Total Pending</th>
      </tr>
    </thead>
    <tbody>
     <tr ng-repeat="x in pendingBookingByRM | orderBy:'-total_pending'">
        <td>{{$index+1}}</td>
        <td><a type="button" id="vendor_{{x.rmID}}" class="btn btn-info" target="_blank" href="<?php echo base_url(); ?>employee/dashboard/pending_full_view_by_sf/{{x.rmID}}">{{x.rm}}</a></td>
        <td>{{x.last_2_day_installation_booking_count}}</td>
        <td class="text_warning">{{x.last_3_to_5_days_installation_count}}</td>
        <td class="text_warning">{{x.more_then_5_days_installation_count}}</td>
        <td>{{x.last_2_day_repair_booking_count}}</td>
         <td class="text_warning">{{x.last_3_to_5_days_repair_count}}</td>
        <td class="text_warning">{{x.more_then_5_days_repair_count}}</td>
        <td>{{x.total_pending}}</td>
      </tr>
    </tbody>
    </table>
    <center><img id="loader_gif_pending" src="<?php echo base_url(); ?>images/loadring.gif" ></center>
                    </div>
    </div>
    </div>
    <!-- Booking Report End-->
    <div class="row" ng-controller="rm_dashboardController">
<?php
if($this->session->userdata("wrong_pincode_msg")){
     echo "<p style='color: green;text-align: center;font-size: 18px;'>".$this->session->userdata("wrong_pincode_msg")."</p>";
    if($this->session->userdata('wrong_pincode_msg')){$this->session->unset_userdata('wrong_pincode_msg');}
}
?>
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
    <input type="text" id="session_id_holder" style="display:none;" value="<?php if($this->session->userdata('user_group') == 'regionalmanager') {echo $this->session->userdata('id');} ?>">
   <button type="button"class="btn btn-default" style="float:right" data-toggle="tooltip"data-placement="left"title="To calculate escalation percentage, logic use current months booking and current month escalation ">?</button>
    <button type="button" class="btn btn-info" ng-click="mytoggle=!mytoggle" id="order_by_toggal" onclick="change_toggal_text()"style="float:right">Sort By Number Of Escalation</button>
    <form class="form-inline"style="float:left;background: #46b8da;color: #fff;padding: 3px;border-radius: 4px;">
        <div class="form-group">
            <input type="text" class="form-control" name="daterange" id="daterange_id" ng-change="daterangeloadFullRMView()" ng-model="dates">
        </div>
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
     <tr ng-repeat="y in escalationAllRMData  |orderBy:!mytoggle?'-esclation_per':'-total_escalation'">
        <td>{{$index+1}}</td>
        <td><a type="button" id="vendor_{{y.vendor_id}}" class="btn btn-info" target="_blank" href="<?php echo base_url(); ?>employee/dashboard/escalation_full_view/{{y.rm_id}}/{{y.startDate}}/{{y.endDate}}">{{y.rm_name}}</a></td>
        <td>{{y.total_booking}}</td>
        <td>{{y.total_escalation}}</td>
         <td>{{y.esclation_per}}%</td>
      </tr>
    </tbody>
    </table>
     <center><img id="loader_gif_escalation" src="<?php echo base_url(); ?>images/loadring.gif" ></center>
                    </div>
                </div>
            </div>
        </div>
                <div class="clearfix"></div>
<!--<div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Escalation</h2>
                    <div class="clearfix"></div>
                </div>

                <div class="x_content">

<div class="table-responsive" id="pending_booking_data">
    <button type="button" class="btn btn-info" ng-click="pendingBookingToggle=!pendingBookingToggle" value="Sort By 3 days" id="order_by_toggal" onclick="change_toggal_text(this.value,'Sort By More than 5 days')"style="float:right">Sort By 3 days</button>
    <p>
    <table class="table table-striped table-bordered jambo_table bulk_action">
    <thead>
      <tr>
        <th>S.no</th>
        <th>Vendor</th>
        <th>Pending Bookings More Than 5 Days</th>
        <th>Pending Bookings More than 3 days</th>
        <th>Monthly Cancelled Bookings</th>
        <th>Monthly Completed Bookings</th>
      </tr>
    </thead>
    <tbody>
        <tr ng-repeat="y in pendingBookingData |orderBy:!pendingBookingToggle?'-greater_than_5_days':'-last_3_day' | limitTo:5">
        <td>{{$index+1}}</td>
        <td>{{y.name}}</td>
        <td>{{y.greater_than_5_days}}</td>
        <td>{{y.last_3_day}}</td>
        <td>{{y.month_cancelled}}</td>
        <td>{{y.monthly_completed}}</td>
      </tr>
    </tbody>
    </table>
                    <center><img id="loader_gif_unit_escalation" src="<?php echo base_url(); ?>images/loadring.gif" ></center>
                    </div>
                </div>
                <div class="full_view_form_container" style="float:right;">
                <form method="post" action="<?php echo base_url()?>employee/dashboard/escalation_full_view" target="_blank">
                    <input type="text"  id="sf_json_data" name="sf_json_data" value="apple" style="display:none;"/>
                    <button type="submit" class="btn btn-success">Full View</button>
                    </form>
                    </div>
            </div>
        </div>-->
    </div>
    </div>
    <!-- END -->
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

<style>
    .text_warning{
        color:red;
    }
    </style>