<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.min.js"></script>
<div id="page-wrapper">
    <div class="container-fluid" ng-app="myApp" ng-controller="myCtrl" >
        <div class="row" style="margin-top: 40px;" >
            <div class="col-md-12">
                <div class="col-md-4"  >
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
                </div>
                <div class="col-md-4"  >
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
                                        {{ y.booking_id }}
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
                </div>
                <div class="col-md-4"  >
                    <h4>Customer Paid Less Than Amount Due</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                     <th class="text-center">Booking ID</th>
                                     <th class="text-center">Customer NetPayable</th>
                                    <th class="text-center">Customer Paid</th>
                                </tr>
                                <tr ng-repeat="z in custom_data2">
                                    <td   style="color:blue;cursor: pointer">
                                        {{ z.booking_id }}
                                    </td>
                                    <td class="text-center" >
                                        {{ z.customer_net_payable }}
                                    </td>
                                    <td class="text-center">
                                        {{ z.amount }}
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-4"  >
                    <h4>Customer, Partner And Around Net Payable zero</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                     <th class="text-center">Booking ID</th>

                                </tr>
                                <tr ng-repeat="a in custom_data3">
                                    <td   style="color:blue;cursor: pointer">
                                        {{ a.booking_id }}
                                    </td>
                                   
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-4"  >
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
                </div>

            </div>

        </div>
    </div>
</div>
<script>
    var app = angular.module('myApp', []);
    app.controller('myCtrl', function($scope, $http) {
        $http.get("<?php echo base_url()."employee/invoiceDashboard/get_completd_booking_for_sf" ?>").then(function(response) {
           $scope.myData = response.data;
           
        });
        $http.get("<?php echo base_url()."employee/invoiceDashboard/get_mis_match_vendor_basic" ?>").then(function(response1) {
           $scope.custom_data = response1.data;
          
          
        });
        $http.get("<?php echo base_url()."employee/invoiceDashboard/get_customer_paid_less_than_due" ?>").then(function(response2) {
           $scope.custom_data2 = response2.data;
        });
        
        $http.get("<?php echo base_url()."employee/invoiceDashboard/charges_total_should_not_zero" ?>").then(function(response3) {
           $scope.custom_data3 = response3.data;
        });
         $http.get("<?php echo base_url()."employee/invoiceDashboard/around_to_vendor_to_around" ?>").then(function(response4) {
           $scope.custom_data4 = response4.data;
        });
    });
   
</script>

</body>
</html>