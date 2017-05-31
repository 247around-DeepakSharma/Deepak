<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row" ng-app="myApp" ng-controller="myCtrl" style="margin-top: 40px;" >
            <div class="col-md-12">
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
                <div class="col-md-4" >
                    <h4>Partner Completed Booking</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">Partner</th>
                                    <th class="text-center">Completed Unit</th>
                                </tr>
                                <tr ng-repeat="x in myData">
                                    <td class="text-center" ng-click="display_table(x.partner_id, x.source)"  style="color:blue;cursor: pointer">
                                        {{ x.source }}
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
                <div class="col-md-4" style="  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);">
                    <h4>{{partner_name + " Completed Booking"}}</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">Price Tags</th>
                                    <th class="text-center">Completed Unit</th>
                                </tr>
                                <tr ng-repeat="y in services">
                                    <td >
                                        {{ y.services }}
                                    </td>
                                    <td class="text-center">
                                        {{ y.total_unit }}
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-4" style=" box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);">
                    <h4>{{partner_name + " Main Invoice"}}</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">Price Tags</th>
                                    <th class="text-center">Completed Unit</th>
                                </tr>
                                <tr ng-repeat="invoice in main_invoice">
                                    <td >
                                        {{ invoice.description }}
                                    </td>
                                    <td class="text-center">
                                        {{ invoice.qty }}
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-12" style="margin-top:15px;">
               <div class="col-md-4" style=" box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);">
                    <h4>{{partner_name + " Duplicate Booking"}}</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">Price Tags</th>
                                    <th class="text-center">Completed Unit</th>
                                </tr>
                                <tr ng-repeat="booking in duplicate_booking">
                                    <td >
                                        {{ booking.booking_id }}
                                    </td>
                                    <td class="text-center">
                                        {{ booking.price_tags }}
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                 <div class="col-md-4" style=" box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);">
                    <h4>{{partner_name + " Installation Not Added"}}</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">Price Tags</th>
                                    
                                </tr>
                                <tr ng-repeat="installation in installation_not_added">
                                    <td >
                                        {{ installation.booking_id }}
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
         $scope.clickme=function(){
        get_data($scope, $http);

        };
    });
    
    function get_data($scope, $http){
     var date_range =  $('input[name="daterange"]').val();
       
        var data = $.param({
                date_range: date_range
               
        });
        var config = {
                headers : {
                    'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
                }
         };
        
        $http.post("<?php echo base_url()."employee/invoiceDashboard/get_count_unit_details" ?>",data, config).success(function 
        (response, status, headers, config) {
           console.log(response);
           $scope.myData = response;
           $scope.display_table = function(partner_id, source) {
               $scope.partner_name = source;
               $http.post("<?php echo base_url()."employee/invoiceDashboard/get_count_services/" ?>"+ partner_id,data, config).success(function 
               (response1, status, headers, config) {
                    $scope.services = response1;
               });
                $http.post("<?php echo base_url()."employee/invoiceDashboard/get_main_invoice/" ?>"+ partner_id,data, config).success(function 
               (response2, status, headers, config) {
                    $scope.main_invoice = response2;
               });
               
                $http.post("<?php echo base_url()."employee/invoiceDashboard/check_duplicate_completed_booking/" ?>"+ partner_id,data, config).success(function 
               (response3, status, headers, config) {
                    $scope.duplicate_booking = response3;
               });
                $http.post("<?php echo base_url()."employee/invoiceDashboard/installation_not_added/" ?>"+ partner_id,data, config).success(function 
               (response4, status, headers, config) {
                    $scope.installation_not_added = response4;
               });
        };
           
        });    
    }
</script>

</body>
</html>