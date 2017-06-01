<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
<div id="page-wrapper">
    <div class="container-fluid" ng-app="myApp" ng-controller="myCtrl" >
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
                            startDate: '<?php echo date("Y/m/01", strtotime("-2 month")) ?>',
                            endDate: '<?php echo date("Y/m/01", strtotime("-1 month")) ?>'
                        });
                    
                        });
                </script>
            </div>
            <br/><br/><br/><br/><br/>
           
                <div class="col-md-12" >
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
                <div class="col-md-12"  >
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
                <div class="col-md-12"  >
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
                <div class="col-md-12"  >
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
                <div class="col-md-12"  >
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
        
        $http.post("<?php echo base_url()."employee/invoiceDashboard/get_completd_booking_for_sf" ?>",data, config).success(function 
        (response, status, headers, config) {
            
           $scope.myData = response;
           
        });
        $http.post("<?php echo base_url()."employee/invoiceDashboard/get_mis_match_vendor_basic" ?>", data, config).success(function 
        (response1, status, headers, config) {
           $scope.custom_data = response1;
          
          
        });
        $http.post("<?php echo base_url()."employee/invoiceDashboard/get_customer_paid_less_than_due" ?>",data, config).success(function 
        (response2, status, headers, config) {
           $scope.custom_data2 = response2;
        });
        
        $http.post("<?php echo base_url()."employee/invoiceDashboard/charges_total_should_not_zero" ?>", data, config).success(function 
        (response3, status, headers, config) {
           $scope.custom_data3 = response3;
        });
         $http.post("<?php echo base_url()."employee/invoiceDashboard/around_to_vendor_to_around" ?>", data, config).success(function 
        (response4, status, headers, config) {
           $scope.custom_data4 = response4;
        });
    }
    
</script>
</body>
</html>