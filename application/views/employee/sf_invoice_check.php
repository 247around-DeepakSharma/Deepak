<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
<div id="page-wrapper">
    <div class="container-fluid" ng-app="myApp" ng-controller="myCtrl" >
         <h1>
                Completed Bookings - Audit Issues
        </h1>
        <div class="row" style="margin-top: 40px;" >
           
            <div class="col-md-6">
                <form class="form-inline">
                    <div class="form-group">
                        <label for="date ragne">Select Date Range:</label>
                        <input type="text" class="form-control" name="daterange"  />
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-default" ng-click="clickme()">Submit</button>
                    </div>
                </form>
                <script type="text/javascript">
                    $(function() {
                        $('input[name="daterange"]').daterangepicker({
                            locale: {
                               format: 'YYYY/MM/DD'
                            },
                            startDate: '<?php echo date("Y/m/01", strtotime("-1 month")) ?>',
                            endDate: '<?php echo date('Y-m-d', strtotime('last day of previous month')); ?>'
                        });
                    
                        });
                </script>
            </div>
            <br/><br/><br/><br/><br/>
           
<!--                <div class="col-md-12" >
                    <h4>SF Completed Booking</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">SF ID</th>
                                    <th class="text-center">SF Name</th>
                                    <th class="text-center">Completed Unit</th>
                                </tr>
                                <tr ng-repeat="x in myData">
                                    <td class="text-center"   style="color:blue;cursor: pointer">
                                        {{ x.sf_id }}
                                    </td>
                                    <td class="text-center"   style="color:blue;cursor: pointer">
                                        {{ x.name }}
                                    </td>
                                    <td class="text-center">
                                        {{ x.total_unit }}
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>-->
             <div class="col-md-12"  >
                    <h4>Customer Paid Less Basic Charges</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">Booking ID</th>
                                    <th class="text-center">Price Tags</th>
                                    <th class="text-center">Basic Charges - Due</th>
                                    <th class="text-center">Basic Charges - Paid</th>
                                    <th class="text-center">Total Charges - Paid</th>
                                    <th class="text-center">Closing Remarks</th>
                                </tr>
                                <tr ng-repeat="y in custom_paid_data">
                                    <td   style="color:blue;cursor: pointer">
                                        <a target="_blank" href="<?php echo base_url()."employee/booking/viewdetails/" ?>{{ y.booking_id }}">  {{ y.booking_id }} </a>
                                    </td>
                                    <td class="text-center" >
                                        {{ y.price_tags }}
                                    </td>
                                    <td class="text-center">
                                        {{ y.customer_net_payable }}
                                    </td>
                                    <td class="text-center">
                                        {{ y.customer_paid_basic_charges }}
                                    </td>
                                    <td class="text-center">
                                        {{ y.amount_paid }}
                                    </td>
                                    <td class="text-center">
                                        {{ y.closing_remarks }}
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-12"  >
                    <h4>Customer Paid Less Upcountry Charges</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">Booking ID</th>
                                    <th class="text-center">Upcountry - Due</th>
                                    <th class="text-center">Upcountry - Paid</th>
                                    <th class="text-center">Remarks</th>
                                </tr>
                                <tr ng-repeat="b in custom_data5">
                                    <td   style="color:blue;cursor: pointer">
                                        <a target="_blank" href="<?php echo base_url()."employee/booking/viewdetails/" ?>{{ b.booking_id }}">  {{ b.booking_id }} </a>
                                    </td>
                                    <td  >
                                        {{ b.up_due }}
                                    </td>
                                    <td  >
                                        {{ b.customer_paid_upcountry_charges }}
                                    </td>
                                    <td   >
                                        {{ b.closing_remarks }}
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-md-12"  >
                    <h4>Installation Not Added</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">Booking ID</th>
                                </tr>
                                <tr ng-repeat="b in custom_data7">
                                    <td   style="color:blue;cursor: pointer">
                                       <a target="_blank" href="<?php echo base_url()."employee/booking/viewdetails/" ?>{{ b.booking_id }}">   {{ b.booking_id }} </a>
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>

               <div class="col-md-12"  >
                    <h4>Wall Mount Stand Not Added</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">Booking ID</th>
                                    <th class="text-center">Brand</th>
                                    <th class="text-center">Capacity</th>
   
                                </tr>
                                <tr ng-repeat="b in custom_data6">
                                    <td   style="color:blue;cursor: pointer">
                                      <a target="_blank" href="<?php echo base_url()."employee/booking/viewdetails/" ?>{{ b.booking_id }}">    {{ b.booking_id }} </a>
                                    </td>
                                    <td  >
                                        {{ b.appliance_brand }}
                                    </td>
                                    <td  >
                                        {{ b.appliance_capacity }}
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>

<!--                <div class="col-md-12"  >
                    <h4>Vendor Basic Not Match</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">Booking ID</th>
                                    <th class="text-center">Basic Charge</th>
                                    <th class="text-center">Correct Basic Charge</th>
                                </tr>
                                <tr ng-repeat="y in custom_data">
                                    <td   style="color:blue;cursor: pointer">
                                       <a target="_blank" href="<?php //echo base_url()."employee/booking/viewdetails/" ?>{{ y.booking_id }}">   {{ y.booking_id }} </a>
                                    </td>
                                    <td class="text-center" >
                                        {{ y.vendor_basic_charges }}
                                    </td>
                                    <td class="text-center">
                                        {{ y.amount }}
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>-->
                
                <div class="col-md-12"  >
                    <h4>Bookings Not Paid By Anyone</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">Booking ID</th>
                                </tr>
                                <tr ng-repeat="a in custom_data3">
                                    <td   style="color:blue;cursor: pointer">
                                     <a target="_blank" href="<?php echo base_url()."employee/booking/viewdetails/" ?>{{ a.booking_id }}">     {{ a.booking_id }} </a>
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>

<!--                <div class="col-md-12"  >
                    <h4>Vendor to Around And Around to Vendor zero </h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">Booking ID</th>
                                    <th class="text-center">Customer Total</th>
                                    <th class="text-center">Price Tags</th>
                                </tr>
                                <tr ng-repeat="b in custom_data4">
                                    <td   style="color:blue;cursor: pointer">
                                        {{ b.booking_id }}
                                    </td>
                                    <td   style="color:blue;cursor: pointer">
                                        {{ b.customer_total }}
                                    </td>
                                    <td   style="color:blue;cursor: pointer">
                                        {{ b.price_tags }}
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>-->

                
            
        </div>
    </div>
</div>
<script>
    var app = angular.module('myApp', []);
    app.controller('myCtrl', function($scope, $http) {
       
        $scope.clickme=function(){
        call_to_get_data($scope, $http);

        };
    });
    
    function call_to_get_data($scope, $http){
        var date_range =  $('input[name="daterange"]').val();
       
        var data = $.param({
                date_range: date_range
               
        });
        var config = {
                headers : {
                    'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
                }
         };
        
//        $http.post("<?php //echo base_url()."employee/invoiceDashboard/get_completd_booking_for_sf" ?>",data, config).success(function 
//        (response, status, headers, config) {
//            
//           $scope.myData = response;
//           
//        });
        $http.post("<?php echo base_url()."employee/invoiceDashboard/get_customer_paid_basic_charge_less_than_cnp" ?>", data, config).success(function 
        (response1, status, headers, config) {
           $scope.custom_paid_data = response1;
          
          
        });
//        $http.post("<?php //echo base_url()."employee/invoiceDashboard/get_mis_match_vendor_basic" ?>", data, config).success(function 
//        (response1, status, headers, config) {
//           $scope.custom_data = response1;
//          
//          
//        });
//        $http.post("<?php //echo base_url()."employee/invoiceDashboard/get_customer_paid_less_than_due" ?>",data, config).success(function 
//        (response2, status, headers, config) {
//           $scope.custom_data2 = response2;
//        });
//        
        $http.post("<?php echo base_url()."employee/invoiceDashboard/charges_total_should_not_zero" ?>", data, config).success(function 
        (response3, status, headers, config) {
           $scope.custom_data3 = response3;
        });
        
//        $http.post("<?php //echo base_url()."employee/invoiceDashboard/around_to_vendor_to_around" ?>", data, config).success(function 
//        (response4, status, headers, config) {
//           $scope.custom_data4 = response4;
//        });
        
        $http.post("<?php echo base_url()."employee/invoiceDashboard/upcountry_booking_check" ?>", data, config).success(function 
        (response5, status, headers, config) {
           $scope.custom_data5 = response5;
        });
        $http.post("<?php echo base_url()."employee/invoiceDashboard/stand_not_added" ?>", data, config).success(function 
        (response6, status, headers, config) {
           $scope.custom_data6 = response6;
        });
        $http.post("<?php echo base_url()."employee/invoiceDashboard/installation_not_added_sf" ?>", data, config).success(function 
        (response7, status, headers, config) {
           $scope.custom_data7 = response7;
        });
    }
    
</script>
</body>
</html>