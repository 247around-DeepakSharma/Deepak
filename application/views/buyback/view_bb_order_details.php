<script src="<?php echo base_url(); ?>js/base_url.js"></script>
<script>
        var partner_order_id = '<?php echo $partner_order_id;?>';
</script>
<script src = "https://ajax.googleapis.com/ajax/libs/angularjs/1.3.14/angular.min.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/app.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/controller/controllers.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/directives/directives.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/services/services.js"></script>
<!-- page content -->
<div class="right_col" role="main">
    <div class="buyback_file_upload" ng-app="viewBuybackOrderDetails">
        <div class="order_details_file">
            <div class="page-title">
                <div class="title_left">
                    <h3>Buyback Order History</h3>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Order Details</h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                </li>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <br />
                            <div id="demo-form2" data-parsley-validate class="form-horizontal form-label-left">
                                <div ng-controller="viewOrderDetails">
                                    <div class="row">
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <table class="table table-bordered table-hover table-responsive" ng-cloak="">
                                                <thead>
                                                    <tr>
                                                        <td>
                                                            <strong>Order Date</strong>
                                                        </td>
                                                        <td>
                                                            {{order_date}}
                                                        </td>
                                                        <td>
                                                            <strong>Delivery Date</strong>
                                                        </td>
                                                        <td>
                                                            {{delivery_date}}
                                                        </td>
                                                    </tr>
                                                    
                                                     <tr>
                                                         <td>
                                                             <strong>Partner GC Id</strong>
                                                        </td>
                                                        <td>
                                                            {{partner_gc_id}}
                                                        </td>
                                                        <td>
                                                            <strong>Partner Tracking Id</strong>
                                                        </td>
                                                        <td>
                                                            {{partner_tracking_id}}
                                                        </td>
                                                    </tr>
                                                    
                                                    <tr>
                                                        <td>
                                                            <strong>Partner Order Id</strong>
                                                        </td>
                                                        <td>
                                                            <?php echo $partner_order_id;?>
                                                        </td>
                                                        <td>
                                                            <strong>City</strong>
                                                        </td>
                                                        <td>
                                                            {{city}}
                                                        </td>
                                                    </tr>
                                                   
                                                    <tr>
                                                        <td>
                                                            <strong>Current status</strong>
                                                        </td>
                                                        <td>
                                                            {{current_status}}
                                                        </td>
                                                         <td>
                                                             <strong>Internal Status</strong>
                                                        </td>
                                                        <td>
                                                            {{internal_status}}
                                                        </td>
                                                    </tr>
                                                    
                                                    <tr>
                                                         <td>
                                                             <strong>Partner Name</strong>
                                                        </td>
                                                        <td>
                                                            {{partner_name}}
                                                        </td>
                                                        <td>
                                                            <strong>Collection Partner Name</strong>
                                                        </td>
                                                        <td>
                                                            {{cp_name}}
                                                        </td>
                                                    </tr>
                                                   
                                                    
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Order Appliance Details</h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                </li>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <br />
                            <div id="demo-form2" data-parsley-validate class="form-horizontal form-label-left">
                                <div ng-controller="viewOrderAppLianceDetails">
                                    <div class="row">
                                        <div class="order_history">
                                            <table class="table table-bordered table-hover table-responsive" ng-cloak="">
                                                <thead>
                                                    <tr>
                                                        <th>S.No.</th>
                                                        <th>Category</th>
                                                        <th>Service</th>
                                                        <th>Physical Condition</th>
                                                        <th>Working Condition</th>
                                                        <th>Partner Charge</th>
                                                        <th>Collection Partner Charge</th>
                                                        <th>Around Charge</th>
                                                        <th>Partner Sweetner Charge</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr ng-repeat="x in orderHistoryDetails">
                                                        <td>{{$index + 1}}</td>
                                                        <td>{{x.category}}</td>
                                                        <td>{{x.service_name}}</td>
                                                        <td>{{x.physical_condition }}</td>
                                                        <td>{{x.working_condition }}</td>
                                                        <td>{{x.partner_charge}}</td>
                                                        <td>{{x.cp_tax}}</td>
                                                        <td>{{x.around_charges}}</td>
                                                        <td>{{x.partner_sweetner_charges}}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Order History</h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                </li>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <br />
                            <div id="demo-form2" data-parsley-validate class="form-horizontal form-label-left">
                                <div ng-controller="viewOrderHistory">
                                    <div class="row">
                                        <div class="order_history">
                                            <table class="table table-bordered table-hover table-responsive" ng-cloak="">
                                                <thead>
                                                    <tr>
                                                        <th>S.No.</th>
                                                        <th>Old State</th>
                                                        <th>New State</th>
                                                        <th>Remarks</th>
                                                        <th>Agent Name</th>
                                                        <th>Partner</th>
                                                        <th>Create Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr ng-repeat="x in orderHistoryDetails">
                                                        <td>{{$index + 1}}</td>
                                                        <td>{{x.old_state}}</td>
                                                        <td>{{x.new_state }}</td>
                                                        <td>{{x.remarks }}</td>
                                                        <td>{{x.agent_name}}</td>
                                                        <td>{{x.cp_name ? x.cp_name:x.partner_name }}</td>
                                                        <td>{{getDateFormat(x.create_date)|date:'dd-MM-yyyy' }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
