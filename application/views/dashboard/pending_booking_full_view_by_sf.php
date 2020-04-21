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
<div class="right_col" role="main" ng-app="rm_Bookings" ng-cloak="">
    <input type="text" value="<?php echo $rm;  ?>" id="rm_id_holder" style="display:none;">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
        <div class="x_title">
                <h2>Installation Bookings</h2>
                <div class="clearfix"></div>
            </div>
    <div class="table-responsive" id="pending_report" ng-controller="rm_PendingBookingControllerInstallation" ng-cloak="">
    <table class="table table-striped table-bordered jambo_table bulk_action">
    <thead>
      <tr>
        <th>S.no</th>
        <th>Vendor</th>
        <th>0 to 2 days</th>
        <th>3 to 5 Days</th>
        <th>>5 Days</th>
        <th>Total Pending</th>
      </tr>
    </thead>
    <tbody>
     <tr ng-repeat="y in pendingBookingByRMFullView  |orderBy:'-total_pending_installation' | limitTo:totalBookings ">
        <td>{{$index+1}}</td>
        <td>{{y.name}}</td>
        <td><button ng-click="createBookingIDView(y.last_2_days_installation_booking_list,y.last_2_days_installation_remarks,y.last_2_days_installation_status)" 
                    type="button" class="btn btn-info btn-lg" style='margin: 0px;padding: 0px 6px;' data-toggle="modal" data-target="#pendingBookingDetails">{{y.last_2_days_installation_pending}}
            </button></td>
        <td><button ng-click="createBookingIDView(y.last_3_to_5_days_installation_booking_list,y.last_3_to_5_days_installation_remarks,y.last_3_to_5_days_installation_status)" 
                    type="button" class="btn btn-info btn-lg" style='margin: 0px;padding: 0px 6px;' data-toggle="modal" data-target="#pendingBookingDetails">{{y.last_3_to_5_days_installation_pending}}
            </button></td>
        <td><button ng-click="createBookingIDView(y.more_then_5_days_installation_booking_list,y.more_then_5_days_installation_remarks,y.more_then_5_days_installation_status)" 
                    type="button" class="btn btn-info btn-lg" style='margin: 0px;padding: 0px 6px;' data-toggle="modal" data-target="#pendingBookingDetails">{{y.more_then_5_days_installation_pending}}
            </button></td>
        <td>{{ y.total_pending_installation }}</td>
      </tr>
    </tbody>
    </table>
        <button type="button" class="btn btn-success" id="full_view_installation" ng-click="full_view_bookings_installation()" style="float: right;">Show All Vendors Installation</button>
          <div class="x_title">
                <h2>Repair Bookings</h2>
                <div class="clearfix"></div>
            </div>
        <table class="table table-striped table-bordered jambo_table bulk_action">
    <thead>
    <tr>
        <th>S.no</th>
        <th>Vendor</th>
        <th>0 to 2 days</th>
        <th>3 to 5 Days</th>
        <th>>5 Days</th>
        <th>Total Pending</th>
      </tr>
    </thead>
    <tbody>
     <tr ng-repeat="y in pendingBookingByRMFullView  |orderBy:'-total_pending_repair' | limitTo:totalBookingsRepair">
        <td>{{$index+1}}</td>
        <td>{{y.name}}</td>
         <td><button ng-click="createBookingIDView(y.last_2_days_repair_booking_list,y.last_2_days_repair_remarks,y.last_2_days_repair_status)" type="button" 
                     class="btn btn-info btn-lg" style='margin: 0px;padding: 0px 6px;' data-toggle="modal" data-target="#pendingBookingDetails">{{y.last_2_days_repair_pending}}</button></td>
         <td><button ng-click="createBookingIDView(y.last_3_to_5_days_repair_booking_list,y.last_3_to_5_days_repair_remarks,y.last_3_to_5_days_repair_status)" type="button" 
                     class="btn btn-info btn-lg" style='margin: 0px;padding: 0px 6px;' data-toggle="modal" data-target="#pendingBookingDetails">{{y.last_3_to_5_days_repair_pending}}</button></td>
        <td><button ng-click="createBookingIDView(y.more_then_5_days_repair_booking_list,y.more_then_5_days_repair_remarks,y.more_then_5_days_repair_status)" type="button"
                    class="btn btn-info btn-lg" style='margin: 0px;padding: 0px 6px;' data-toggle="modal" data-target="#pendingBookingDetails">{{y.more_then_5_days_repair_pending}}</button></td>
         <td>{{y.total_pending_repair}}</td>
      </tr>
    </tbody>
    </table>
        <button type="button" class="btn btn-success" id="full_view_repair" ng-click="full_view_bookings_repair()" style="float: right;">Show All Vendors Repair</button>
    <center><img id="loader_gif_pending" src="<?php echo base_url(); ?>images/loadring.gif" ></center>
                    </div>
    </div>
    </div>
    

    <div id="pendingBookingDetails" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Booking Details</h4>
      </div>
      <div class="modal-body">
          <p id="booking_id_holder"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
    <script>
        
        </script>
        <style>
            [ng\:cloak], [ng-cloak], .ng-cloak {
  display: none !important;
}
            </style>