<script src="<?php echo base_url(); ?>js/base_url.js"></script>
<script src = "https://ajax.googleapis.com/ajax/libs/angularjs/1.3.14/angular.min.js"></script>
<script src="<?php echo base_url(); ?>js/employee/employee.js"></script>
<script src="<?php echo base_url(); ?>js/employee/app.js"></script>
<script src="<?php echo base_url(); ?>js/employee/controller/controllers.js"></script>
<script src="<?php echo base_url(); ?>js/employee/directives/directives.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>css/jquery.loading.css">

<script src="<?php echo base_url(); ?>js/jquery.loading.js"></script>
<style>.dataTables_filter{display: none;}</style>

<div class="right_col" role="main"  ng-app="booking_advanced_search">
    <div class="clearfix"></div>
    <div class="row" ng-controller="bookingAdvancedSearchController">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel" style="height: auto;border:0px;">
                <form method="post" action="<?php echo base_url(); ?>employee/booking/download_booking_snapshot" >
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="x_title">
                            <h2>
                                <i class="fa fa-filter"></i> Advanced Search on Bookings                           
                            </h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li>  <input type="submit" class="btn btn-primary" value="Download Booking List" ></li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <div class="x_content" >
                                <table class="table table-striped table-bordered"  >
                                    <thead>
                                        <tr>
                                            <td style="width:13%;"><input type="text" placeholder="Booking Date" class="form-control" id="booking_date" name="booking_date"/></td>
                                            <td style="width:13%;"><input type="text" placeholder="SF close Date" class="form-control" id="service_center_closed_date" name="service_center_closed_date"/></td>
                                            <td style="width:13%;"><input type="text" placeholder="Close Date" class="form-control" id="close_date" name="close_date"/></td>
                                            <!-- ST-309 add new search feature booking search by created date -->
                                            <td style="width:13%;"><input type="text" placeholder="Created Date" class="form-control" id="created_date" name="created_date"/></td>
                                            <td>
                                                <select style="width:100%" name="partner" ui-select2 id="partner"  class="form-control data_change" data-placeholder="Select Partner">
                                                    <option value="" ng-show="false"></option>
                                                    <option ng-repeat="y in partner_list" value="{{y.partner_id}}">{{y.source}}</option>
                                                </select>
                                            </td>
                                            <td>
                                                <select style="width:100%" name="city" ui-select2 id="city"  class="form-control data_change" data-placeholder="Select City">
                                                    <option value="" ng-show="false"></option>
                                                    <option ng-repeat="y in city_list" value="{{y.city}}">{{y.city}}</option>
                                                </select>
                                            </td>
                                            <td>
                                                <select style="width:100%"  name="sf" ui-select2 id="sf"  class="form-control data_change" data-placeholder="Select SF">
                                                    <option value=""  ng-show="false"></option>
                                                    <option ng-repeat="y in service_centers_list" value="{{y.id}}">{{y.name}}</option>
                                                </select>
                                            </td>
                                            <td>
                                                <select multiple style="width:100%" name="current_status[]" ui-select2 id="current_status"  class="form-control data_change" data-placeholder="Current Status">
                                                    <option value="" ng-show="false"></option>
                                                    <option ng-repeat="y in current_status_list" value="{{y.current_status}}">{{y.current_status}}</option>   
                                                </select>
                                            </td>
                                            <td>
                                                <select  style="width:100%" name="internal_status" ui-select2 id="internal_status"  class="form-control data_change" data-placeholder="Internal Status">
                                                    <option value="" ng-show="false"></option>
                                                    <option ng-repeat="y in internal_status_list" value="{{y.status}}">{{y.status}}</option>   
                                                </select>
                                            </td>
                                            <td>
                                                <select style="width:100%" name="product_or_service" ui-select2 id="product_or_service"  class="form-control data_change" data-placeholder="Product Or Service">
                                                    <option value="" ng-show="false"></option>
                                                    <option ng-repeat="y in product_or_service_list" value="{{y.option}}">{{y.option}}</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <select multiple style="width:100%" name="state[]" ui-select2 id="state"  class="form-control data_change" data-placeholder="States">
                                                    <option value="" ng-show="false"></option>
                                                    <option ng-repeat="y in state_list" value="{{y.state}}">{{y.state}}</option>
                                                </select>
                                            </td>
                                            <td>
                                                <select style="width:100%" name="upcountry" ui-select2 id="upcountry"  class="form-control data_change" data-placeholder="Is Upcountry">
                                                    <option value="" ng-show="false"></option>
                                                    <option ng-repeat="y in is_upcountry_list" value="{{y.value}}">{{y.option}}</option>
                                                </select>
                                            </td>
                                            <td>
                                                <select style="width:100%" name="rating" ui-select2 id="rating"  class="form-control data_change" data-placeholder="Rating">
                                                    <option value="" ng-show="false"></option>
                                                    <option ng-repeat="y in rating_list" value="{{y.option}}">{{y.option}}</option>
                                                </select>
                                            </td>
                                            <td>
                                                <select multiple style="width:100%" name="service[]" ui-select2 id="service"  class="form-control data_change" data-placeholder="Service">
                                                    <option value="" ng-show="false"></option>
                                                    <option ng-repeat="y in service_list" value="{{y.id}}">{{y.services}}</option>
                                                </select>
                                            </td>
                                            <td>
                                                <select style="width:100%" name="brand" ui-select2 id="brand"  class="form-control data_change"data-placeholder="Brand">
                                                    <option value="" ng-show="false"></option>
                                                    <option ng-repeat="y in brand_list" value="{{y.brand_name}}">{{y.brand_name}}</option>   
                                                </select>
                                            </td>
                                            <td>
                                                <select style="width:100%" name="categories" ui-select2 id="categories"  class="form-control data_change "data-placeholder="Categories">
                                                    <option value="" ng-show="false"></option>
                                                    <option ng-repeat="y in category_list" value="{{y.category}}">{{y.category}}</option>
                                                </select>
                                            </td>
                                            <td>
                                                <select style="width:100%" name="capacity" ui-select2 id="capacity"  class="form-control data_change"data-placeholder="Capacity">
                                                    <option value="" ng-show="false"></option>
                                                    <option ng-repeat="y in capacity_list" value="{{y.capacity}}">{{y.capacity}}</option>   
                                                </select>
                                            </td>
                                            <td>
                                                <select style="width:100%" name="paid_by" ui-select2 id="paid_by"  class="form-control data_change"data-placeholder="Paid By">
                                                    <option value="" ng-show="false"></option>
                                                    <option ng-repeat="y in paid_by_list" value="{{y.option}}">{{y.option}}</option>   
                                                </select>
                                            </td>
                                            <td>
                                                <select multiple style="width:100%" name="request_type[]" ui-select2 id="request_type"  class="form-control data_change"data-placeholder="Request Type">
                                                    <option value="" ng-show="false"></option>
                                                    <option ng-repeat="y in request_type_list" value="{{y.request_type}}">{{y.request_type}}</option>
                                                </select>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-success" style="width: 100%;" id="get_result">Get Result</button>
                                            </td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            </form>
                            <form action="#" method="POST" id="reAssignForm" name="reAssignForm">
                                <div id="loader-gif" style="position: absolute; display: none; width: 100%; height: 100%; z-index: 3000; padding-top: 110px;"> <center><img src="<?php echo base_url(); ?>images/loadring.gif" ></center></div>
                                <table id="advance_booking_search" class="table table-striped table-bordered" style="overflow-x: auto;">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Booking ID</th>
                                            <th>Partner</th>
                                            <th>City</th>
                                            <th>Service&nbsp;Center</th>
                                            <th>Service</th>
                                            <th>Brand</th>
                                            <th>Category</th>
                                            <th>Capacity</th>
                                            <th>Request&nbsp;Type</th>
                                            <th>Product/Service</th>
                                            <th>Current&nbsp;Status</th>
                                            <th>Internal&nbsp;Status</th>
                                            <th>ASM&nbsp;Name</th>
                                            <th>RM&nbsp;Name</th>
                                            <th>AM&nbsp;Name</th>
                                            <th>Part&nbsp;Name&nbsp;Requested</th>
                                            <th>Part&nbsp;Type&nbsp;Requested</th>
                                            <th>Part&nbsp;Name&nbsp;Shipped</th>
                                            <th>Part&nbsp;Type&nbsp;Shipped</th>
                                            <th>Part&nbsp;Shipped&nbsp;Date</th>
                                            <th>Dependency</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>
            </div>
        </div>
    </div>    
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Error In Assign</h4>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-hover table-responsive">
                        <thead>
                        <th>S.No.</th>
                        <th>Order ID</th>   
                        <th>Message</th>   
                        </thead>
                        <tbody  id="error_td">
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $("#get_result").click(function () {
        $("#loader-gif").show();
        ad_table.ajax.reload(function (json) {
            $("#loader-gif").hide();
        });
    });
</script>