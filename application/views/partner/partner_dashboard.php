<style>
    .collape_icon {
        font-size: 18px;
        color: #4b5561 !important;
        float:right;
    }
</style>
<script src="<?php echo base_url(); ?>js/base_url.js"></script>
<script src = "https://ajax.googleapis.com/ajax/libs/angularjs/1.3.14/angular.min.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/app.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/controller/controllers.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>css/jquery.loading.css">
<!-- Highchart.js -->
        <script src="https://code.highcharts.com/highcharts.js"></script>
        <script src="https://code.highcharts.com/modules/data.js"></script>
        <script src="https://code.highcharts.com/modules/drilldown.js"></script>
        <script src="https://code.highcharts.com/modules/exporting.js"></script>
        <script src="https://highcharts.github.io/export-csv/export-csv.js"></script>

<script src="<?php echo base_url();?>js/jquery.loading.js"></script>
<div class="right_col" role="main" ng-app="admin_dashboard" style="margin-bottom: 9px;background: rgb(248, 248, 248);">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0px;margin: 0px 10px 9px;border: 1px solid #e6e9ed;width: 98.5%;background: #fff;">
            <div class="col-md-12" id="booking_summary">
                <center>  <img style="width: 46px;" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
            </div>
        </div>
    </div>
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>TAT Reporting<button type="button"class="btn btn-default" style="float: right;margin-bottom: 10px;padding: 1px 4px;margin-top: 0px;font-size: 8px;margin-left: 5px;background: #f7a35c;
    color: #fff;border: none;" data-toggle="tooltip"data-placement="right"title="(Booking Completed on x Day / Total Completed Bookings (Within Selected Range))*100">?</button></h2>
                    <span class="collape_icon" href="#TAT_reporting_div" data-toggle="collapse" onclick=""><i class="fa fa-minus-square" aria-hidden="true"></i></span>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content collapse in" id="TAT_reporting_div">
                <div class="table-responsive" id="escalation_data" ng-controller="completedBooking_Controller" ng-cloak="">
                    <form action="" method="post" target="_blank" id="rm_completed_booking_form" style="float: left;width: 1110px;">
                    <div class="col-md-3" style="margin: 0px;padding: 0px 1px;width: 160px;">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <label for="">Appliance</label>
                            <select class="form-control filter_table" id="service_id" name="services">
                                <option value="not_set" selected="selected">All</option>
                                <?php foreach($services as $val){ ?>
                                <option value="<?php echo $val['id']?>"><?php echo $val['services']?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                        <div class="form-group col-md-3" style="    width: 200px;">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <label for="">Service Type</label><br>
                            <select class="form-control filter_table" id="request_type" name="request_type[]" multiple="">
                                <option value="not_set">All</option>
                                <option value="Installation" selected="selected">Installations</option>
                                <option value="Repair_with_part">Repair With Spare</option>  
                                <option value="Repair_without_part">Repair Without Spare</option>  
                            </select>
                        </div>
                </div>
                    </div>
                    <div class="col-md-3" style="margin: 0px;padding: 0px 1px;width: 170px;">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">  
                            <label for="">In Warranty <button type="button"class="btn btn-default" style="float: right;margin-bottom: 10px;padding: 1px 4px;margin-top: 0px;font-size: 8px;margin-left: 5px;background: #f7a35c;
    color: #fff;border: none;" data-toggle="tooltip"data-placement="left"title="Free For Customer">?</button></label>
                            <select class="form-control filter_table" id="free_paid" name="free_paid">
                                <option value="not_set" selected="selected">All</option>
                                <option value="Yes">Yes (In Warranty)</option>
                                <option value="No">No (Out Of Warranty)</option>  
                            </select>
                        </div>
                    </div>
                </div>
                    <div class="col-md-3" style="margin: 0px;padding: 0px 1px;width: 170px;">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <label for="">Is Upcountry</label>
                            <select class="form-control filter_table" id="upcountry" name="upcountry">
                                <option value="not_set">All</option>
                                <option value="Yes">Yes</option>
                                 <option value="No" selected="selected">No</option>
                            </select>
                        </div>
                    </div>
                </div>
                        <div class="form-group col-md-3" style="width: 200px;">
                                         <label for="">Booking Completed Date <button type="button"class="btn btn-default" style="float: right;margin-bottom: 10px;padding: 1px 4px;margin-top: 0px;font-size: 8px;margin-left: 5px;background: #f7a35c;
    color: #fff;border: none;" data-toggle="tooltip"data-placement="left"title="By Default last 30 days">?</button></label>
                                         <input type="text" class="form-control" name="daterange_completed_bookings" id="completed_daterange_id" ng-change="ShowRMCompletedBookingBYDateRange()" ng-model="dates">
                            </div>
                    <div class="form-group col-md-3">
                                         <label for="">Booking Status</label>
                                         <select class="form-control"  ng-model="status" id="completed_status" name="status">
                                            <option value="not_set">All</option>
                                            <option value="Completed" ng-selected="true">Completed</option>
                                            <option value="Cancelled">Cancelled</option>
                                        </select>
                    </div>
                    </form>
                    <button class="btn btn-primary" ng-click="ShowRMCompletedBookingBYDateRange()" ng-model="partner_dashboard_filter" style="margin-top: 23px;background: #405467;border-color: #405467;">Apply Filters</button>
                <br>
                <div class="clear"></div>
                <table class="table table-striped table-bordered jambo_table bulk_action">
                    <thead>
                        <tr>
                            <th>S.no</th>
                            <th>RM</th>
                            <th>D0</th>
                            <th>D1</th>
                            <th>D2</th>
                            <th>D3</th>
                            <th>D4</th>
                            <th>D5 - D7</th>
                             <th>D8 - D15</th>
                             <th>> D15 (Total)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat="x in completedBookingByRM.TAT | orderBy:'TAT_16'" ng-if='completedBookingByRM.leg_1 !== undefined'>
                            <td style="padding: 4px 12px;">{{$index+1}}</td>
<!--                           <td><a type="button" id="vendor_{{x.id}}" class="btn btn-info" target="_blank" href="<?php echo base_url(); ?>employee/dashboard/tat_calculation_full_view/{{x.id}}/0/1">{{x.entity}}</a></td>-->
                            <td style="padding: 4px 12px;"><button style="margin-top: 10px;" type="button" id="vendor_{{completedBookingByRM.leg_1[$index].id}}" class="btn btn-info" target="_blank" 
                                       onclick="open_full_view(this.id,'<?php echo base_url(); ?>employee/dashboard/tat_calculation_full_view/','0','0','rm_completed_booking_form')">
                                   {{completedBookingByRM.leg_1[$index].entity}} </button><p style="float:right;margin-bottom: 0px;">leg_1<br>leg_2<br>Total</p></td>
                                   
                           <td style="padding: 4px 12px;">{{completedBookingByRM.leg_1[$index].TAT_0}} ({{completedBookingByRM.leg_1[$index].TAT_0_per}}%)<br>
                               {{completedBookingByRM.leg_2[$index].TAT_0}} ({{completedBookingByRM.leg_2[$index].TAT_0_per}}%)<br>
                               {{completedBookingByRM.TAT[$index].TAT_0}} ({{completedBookingByRM.TAT[$index].TAT_0_per}}%) </td>
                           
                           <td style="padding: 4px 12px;">{{completedBookingByRM.leg_1[$index].TAT_1}} ({{completedBookingByRM.leg_1[$index].TAT_1_per}}%)<br>
                               {{completedBookingByRM.leg_2[$index].TAT_1}} ({{completedBookingByRM.leg_2[$index].TAT_1_per}}%)<br>
                              {{completedBookingByRM.TAT[$index].TAT_1}} ({{completedBookingByRM.TAT[$index].TAT_1_per}}%)</td>
                           
                           <td style="padding: 4px 12px;">{{completedBookingByRM.leg_1[$index].TAT_2}} ({{completedBookingByRM.leg_1[$index].TAT_2_per}}%)<br>
                               {{completedBookingByRM.leg_2[$index].TAT_2}} ({{completedBookingByRM.leg_2[$index].TAT_2_per}}%)<br>
                                {{completedBookingByRM.TAT[$index].TAT_2}} ({{completedBookingByRM.TAT[$index].TAT_2_per}}%) </td>
                           
                           <td style="padding: 4px 12px;">{{completedBookingByRM.leg_1[$index].TAT_3}} ({{completedBookingByRM.leg_1[$index].TAT_3_per}}%)<br>
                               {{completedBookingByRM.leg_2[$index].TAT_3}} ({{completedBookingByRM.leg_2[$index].TAT_3_per}}%)<br>
                                {{completedBookingByRM.TAT[$index].TAT_3}} ({{completedBookingByRM.TAT[$index].TAT_3_per}}%)</td>
                           
                           <td style="padding: 4px 12px;">{{completedBookingByRM.leg_1[$index].TAT_4}} ({{completedBookingByRM.leg_1[$index].TAT_4_per}}%)<br>
                               {{completedBookingByRM.leg_2[$index].TAT_4}} ({{completedBookingByRM.leg_2[$index].TAT_4_per}}%)<br>
                               {{completedBookingByRM.TAT[$index].TAT_4}} ({{completedBookingByRM.TAT[$index].TAT_4_per}}%) </td>
                           
                           <td style="padding: 4px 12px;">{{completedBookingByRM.leg_1[$index].TAT_5}} ({{completedBookingByRM.leg_1[$index].TAT_5_per}}%)<br>
                               {{completedBookingByRM.leg_2[$index].TAT_5}} ({{completedBookingByRM.leg_2[$index].TAT_5_per}}%)<br>
                                {{completedBookingByRM.TAT[$index].TAT_5}} ({{completedBookingByRM.TAT[$index].TAT_5_per}}%) </td>
                           
                           <td style="padding: 4px 12px;">{{completedBookingByRM.leg_1[$index].TAT_8}} ({{completedBookingByRM.leg_1[$index].TAT_8_per}}%)<br>
                               {{completedBookingByRM.leg_2[$index].TAT_8}} ({{completedBookingByRM.leg_2[$index].TAT_8_per}}%)<br>
                               {{completedBookingByRM.TAT[$index].TAT_8}} ({{completedBookingByRM.TAT[$index].TAT_8_per}}%)</td>
                           
                           <td style="padding: 4px 12px;">{{completedBookingByRM.leg_1[$index].TAT_16}} ({{completedBookingByRM.leg_1[$index].TAT_16_per}}%)<br>
                               {{completedBookingByRM.leg_2[$index].TAT_16}} ({{completedBookingByRM.leg_2[$index].TAT_16_per}}%)<br>
                               {{completedBookingByRM.TAT[$index].TAT_16}} ({{completedBookingByRM.TAT[$index].TAT_16_per}}%) </td>
                        </tr>
                        <tr ng-repeat="x in completedBookingByRM.TAT | orderBy:'TAT_16'" ng-if='completedBookingByRM.leg_1 == undefined'>
                           <td>{{$index+1}}</td>
                           <td><button type="button" id="vendor_{{x.id}}" class="btn btn-info" target="_blank" 
                                       onclick="open_full_view(this.id,'<?php echo base_url(); ?>employee/dashboard/tat_calculation_full_view/','0','0','rm_completed_booking_form')">{{x.entity}}</button></td>
                           <td> {{x.TAT_0}}  ({{x.TAT_0_per}}%) </td>
                           <td>{{x.TAT_1}}  ({{x.TAT_1_per}}%) </td>
                           <td>{{x.TAT_2}}  ({{x.TAT_2_per}}%)</td>
                           <td>{{x.TAT_3}}  ({{x.TAT_3_per}}%)</td>
                           <td>{{x.TAT_4}}  ({{x.TAT_4_per}}%)</td>
                           <td>{{x.TAT_5}}  ({{x.TAT_5_per}}%) </td>
                           <td>{{x.TAT_8}}  ({{x.TAT_8_per}}%)</td>
                           <td>{{x.TAT_16}}  ({{x.TAT_16_per}}%)</td>
                        </tr>
                    </tbody>
                </table>
                <center><img id="loader_gif_completed_rm" src="<?php echo base_url(); ?>images/loadring.gif" ></center>
                <p style="background: #f7a35c;color: #fff;padding: 2px 6px;width: 36%;font-size: 14px;font: 14px/16px Arial;"><span><b>Note*</b></span> TAT has been calculated using "First Booking Date" and "Completion Date"</p>
            </div>
            </div>
        </div>
    </div>
    </div>
    
            <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0px !important;">
            <div class="x_panel">
                <div class="x_title" style="padding-left: 0px;">
                    <h2>Pending Booking Reports</h2>
<!--                    <span class="collape_icon" href="#RM_TAT_Reporting_pending" data-toggle="collapse" onclick="initiate_RM_Pending_TAT_Reporting()"><i class="fa fa-plus-square" aria-hidden="true"></i></span>-->
                    <div class="clearfix"></div>
                </div>
                <div id="RM_TAT_Reporting_pending">
                <div class="table-responsive" id="escalation_data" ng-controller="pendingBooking_ControllerRM" ng-cloak="">
                    <form action="" method="post" target="_blank" id="rm_pending_booking_form" style="float: left;width: 988px;">
                    <div class="col-md-3" style="margin: 0px;padding: 0px 1px;width: 130px;">
                        <div class="item form-group">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <label for="">Services</label>
                                <select class="form-control filter_table" id="service_id_rm_pending" name="services">
                                    <option value="not_set" selected="selected">All</option>
                                    <?php foreach($services as $val){ ?>
                                    <option value="<?php echo $val['id']?>"><?php echo $val['services']?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                        <div class="form-group col-md-3" style="width:200px;">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <label for="">Request Type</label>
                            <select class="form-control filter_table" id="request_type_rm_pending" name="request_type[]" multiple="">
                                <option value="not_set">All</option>
                                <option value="Installation" selected="selected">Installations</option>
                                <option value="Repair_with_part">Repair With Spare</option>  
                                <option value="Repair_without_part">Repair Without Spare</option>  
                            </select>
                        </div>
                </div>
                    </div>
                    <div class="col-md-3" style="margin: 0px;padding: 0px 1px;width: 130px;">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">  
                            <label for="">In Warranty</label>
                            <select class="form-control filter_table" id="free_paid_rm_pending" name="free_paid">
                                <option value="not_set" selected="selected">All</option>
                                <option value="Yes">Yes (In Warranty)</option>
                                <option value="No">No (Out Of Warranty)</option>  
                            </select>
                        </div>
                    </div>
                </div>
                    <div class="col-md-3" style="margin: 0px;padding: 0px 1px;width: 130px;">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <label for="">Is Upcountry</label>
                            <select class="form-control filter_table" id="upcountry_rm_pending" name="upcountry">
                                <option value="not_set">All</option>
                                <option value="Yes">Yes</option>
                                 <option value="No" selected="selected">No</option>
                            </select>
                        </div>
                    </div>
                </div>
                        <div class="col-md-3" style="margin: 0px;padding: 0px 1px;width: 197px;">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                             <label for="">Dependency</label>
                                         <select class="form-control filter_table"  id="pending_dependency" name="status[]" multiple="">
                                             <option value="247Around" selected="selected">Admin</option>
                                            <option value="Partner" selected="selected">Partner</option>
                                            <option value="Vendor:not_define" selected="selected">SF</option>
                                        </select>
                        </div>
                    </div>
                </div>
                    <div class="form-group col-md-3" style="width:200px;">
                                <label for="">Initial Booking Date</label>
                                <input type="text" class="form-control" name="daterange_completed_bookings" id="pending_daterange_id_rm" ng-change="ShowRMPendingBookingBYDateRange()" ng-model="dates">
                            </div>
                    </form>
                    <div class="form-group col-md-3" style="    width: 100px;">
                    <button class="btn btn-primary" ng-click="ShowRMPendingBookingBYDateRange()" ng-model="partner_dashboard_filter" style="margin-top: 23px;background: #405467;border-color: #405467;">Apply Filters</button>
                </div>
                    <br>
                <div class="clear"></div>
                <table class="table table-striped table-bordered jambo_table bulk_action">
                    <thead>
                        <tr>
                            <th>S.no</th>
                            <th>RM</th>
                            <th>D0</th>
                            <th>D1</th>
                            <th>D2</th>
                            <th>D3</th>
                            <th>D4</th>
                            <th>D5 - D7</th>
                             <th>D8 - D15</th>
                             <th>>D15</th>
                             <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat="x in pendingBookingByRM | orderBy:'TAT_16'">
                            <td>{{$index+1}}</td>
                            <td><button type="button" id="vendor_{{x.id}}" class="btn btn-info" target="_blank" >{{x.entity}}</button></td>
                            <td>{{x.TAT_0}} <br> ({{x.TAT_0_per}}%) </td>
                           <td>{{x.TAT_1}} <br> ({{x.TAT_1_per}}%) </td>
                           <td>{{x.TAT_2}} <br> ({{x.TAT_2_per}}%)</td>
                           <td>{{x.TAT_3}} <br> ({{x.TAT_3_per}}%)</td>
                           <td>{{x.TAT_4}} <br> ({{x.TAT_4_per}}%)</td>
                           <td>{{x.TAT_5}} <br> ({{x.TAT_5_per}}%) </td>
                           <td>{{x.TAT_8}} <br> ({{x.TAT_8_per}}%)</td>
                           <td>{{x.TAT_16}} <br> ({{x.TAT_16_per}}%)</td>
                            <td>{{x.Total_Pending}} <br> ({{x.TAT_total_per}}%)</td>
                        </tr>
                    </tbody>
                </table>
                <center><img id="loader_gif_pending_rm" src="<?php echo base_url(); ?>images/loadring.gif" ></center>
            </div>
            </div> 
            </div>
        </div>
    </div>
    
    
       <div class="row" style="margin-top:10px;">
        <!-- Company Monthly Status -->
        <div class="col-md-6 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Monthly Booking Status <small>Completed</small></h2>
                    <span class="collape_icon" href="#monthly_booking_chart_div" data-toggle="collapse" onclick=""><i class="fa fa-minus-square" aria-hidden="true"></i></span>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content collapse in" id="monthly_booking_chart_div">
                <div class="col-md-12">
                    <center><img id="loader_gif2" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center>
                </div>
               
                    <div id="monthly_booking_chart" style="width:100%; height:400px;" ></div>
                </div>
            </div>
        </div>
        <!-- End Company Monthly Status -->
        <!-- RM wise booking status -->
        <div class="col-md-6 col-sm-12 col-xs-12" id="based_on_Region">
            <div class="x_panel">
                <div class="x_title">
                    <div class="col-md-5">
                    <h2>Booking based on Region <small>Current Month</small></h2>
                    </div>
                    <div class="col-md-5">
                    <div class="nav navbar-right panel_toolbox">
                        <div id="reportrange2" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; margin-right: -30%;">
                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                            <span></span> <b class="caret"></b>
                        </div>
                    </div>
                    </div>
                    <div class="col-md-1" style="float: right;">
                        <span class="collape_icon" href="#state_type_booking_chart_div" data-toggle="collapse" onclick="collapse_icon_change(this);"><i class="fa fa-minus-square" aria-hidden="true"></i></span>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content collapse in" id="state_type_booking_chart_div">
                <div class="col-md-12">
                    <center><img id="loader_gif3" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center>
                </div>
                
                    <div id="state_type_booking_chart" style="width:100%; height:400px;"></div>
                </div>
            </div>
        </div>
        <!-- RM wise booking status -->
    </div>
    <div class="row">
            <div class="col-md-6 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Spare Details by Status <button type="button"class="btn btn-default" style="float: right;margin-bottom: 10px;padding: 1px 4px;margin-top: 0px;font-size: 8px;margin-left: 5px;background: #f7a35c;
    color: #fff;border: none;" data-toggle="tooltip"data-placement="left"title="Breakup of all spare status">?</button></h2>
                        <span class="collape_icon" href="#spare_details_by_status_div" data-toggle="collapse" onclick="get_partner_spare_data_by_status()"><i class="fa fa-plus-square" aria-hidden="true"></i></span>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content collapse" id="spare_details_by_status_div">
                        <div class="col-md-12"><center><img id="loader_gif1" src="<?php echo base_url(); ?>images/loadring.gif"></center></div>
                        <div id="spare_details_by_status" style="width:100%; height:400px;"></div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Spare Snapshot <button type="button"class="btn btn-default" style="float: right;margin-bottom: 10px;padding: 1px 4px;margin-top: 0px;font-size: 8px;margin-left: 5px;background: #f7a35c;
                                                   color: #fff;border: none;" data-toggle="tooltip"data-placement="left"title="Below graphs shows total parts pending shipped by partner and Out Of TAT (30 days from shipped date)">?</button></h2>
                        <span class="collape_icon" href="#spare_snapshot_div" data-toggle="collapse" onclick="get_partner_spare_snapshot()"><i class="fa fa-plus-square" aria-hidden="true"></i></span>                           
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content collapse" id="spare_snapshot_div">
                        <div class="col-md-12"><center><img id="loader_gif_spare_snapshot" src="<?php echo base_url(); ?>images/loadring.gif"></center></div>
                        <div id="spare_snapshot" style="width:100%; height:400px;"></div>
                    </div>
                </div>
            </div>
        </div>
    <!-- Booking Report End-->
        <!-- Escalation Start-->
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Escalation <button type="button"class="btn btn-default" style="float: right;margin-bottom: 10px;padding: 1px 4px;margin-top: 0px;font-size: 8px;margin-left: 5px;background: #f7a35c;
    color: #fff;border: none;" data-toggle="tooltip"data-placement="right"title="Number of booking in current Month 
    and Number Of escalation in current Month">?</button></h2>
                    <span class="collape_icon" href="#escalation_data_div" data-toggle="collapse" onclick="initiate_escalation_data()"><i class="fa fa-plus-square" aria-hidden="true"></i></span>
                    <div class="clearfix"></div>
                </div>

                <div class="x_content collapse" id="escalation_data_div">

                    <div class="table-responsive" id="escalation_data" ng-controller="admin_escalationController" ng-cloak="">
                        <button type="button" class="btn btn-info" ng-click="mytoggle = !mytoggle" id="order_by_toggal" onclick="change_toggal_text()"style="float:right">Sort By Number Of Escalation</button>
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
                                    <th>Region</th>
                                    <th>Total Booking</th>
                                    <th>Total Escalation</th>
                                    <th>Escalation %</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="y in escalationAllRMData|orderBy:!mytoggle?'-esclation_per':'-total_escalation'" ng-cloak="">
                                    <td>{{$index + 1}}</td>
                                    <td><a type="button" id="vendor_{{y.vendor_id}}" class="btn btn-info" target="_blank" href="<?php echo base_url(); ?>employee/dashboard/escalation_full_view/{{y.rm_id}}/{{y.startDate}}/{{y.endDate}}">{{y.zone}}</a></td>
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
    </div>
    <!-- Escalation End-->
 
  </div>
<script>
    $('#request_type').select2();
    $('#request_type_rm_pending').select2();
     $('#pending_dependency').select2();
    function getMultipleSelectedValues(fieldName){
    fieldObj = document.getElementById(fieldName);
    var values = [];
    var length = fieldObj.length;
    for(var i=0;i<length;i++){
       if (fieldObj[i].selected == true){
           values.push(fieldObj[i].value);
       }
    }
   return values.join(":");
}
    var start = moment().startOf('month');
    var end = moment().endOf('month');
    var options = {
            startDate: start,
            endDate: end,
            minDate: '01/01/2000',
            maxDate: '12/31/2030',
            dateLimit: {
                days: 120},
            showDropdowns: true,
            showWeekNumbers: true,
            timePicker: false,
            timePickerIncrement: 1, timePicker12Hour: true,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            opens: 'left',
            buttonClasses: ['btn btn-default'],
            applyClass: 'btn-small btn-primary',
            cancelClass: 'btn-small',
            format: 'MM/DD/YYYY', separator: ' to ',
            locale: {
                applyLabel: 'Submit',
                cancelLabel: 'Clear',
                fromLabel: 'From',
                toLabel: 'To',
                customRangeLabel: 'Custom',
                daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                firstDay: 1
            }
    };
    var post_request = 'POST';
    var get_request = 'GET';
    var url = '';
    var partner_name = '<?php echo $this->session->userdata('partner_name')?>';
    var partner_id = '<?php echo $this->session->userdata('partner_id')?>'; 
    $(function() {
//        var d = new Date();
//        n = d.getMonth();
//        y = d.getFullYear();
//        date = d.getDate();
        $('input[name="daterange_completed_bookings"]').daterangepicker({
                timePicker: true,
           timePickerIncrement: 30,
           locale: {
               format: 'YYYY-MM-DD'
           },
           //startDate: y+'-'+n+'-'+date
           startDate: "<?php echo date("Y-m-d", strtotime("-1 month")); ?>"
        });
          function cb(start, end) {
            $('#reportrange2 span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }
        $('#reportrange2').daterangepicker(options, cb);
        cb(start, end);
    });
 //this function is used to call ajax request
    function sendAjaxRequest(postData, url,type) {
        return $.ajax({
            data: postData,
            url: url,
            type: type
        });
    }
    

  $(document).ready(function () {
     //get_partner_spare_snapshot();
     //get_partner_spare_data_by_status();
       around_monthly_data();
       get_bookings_data_by_rm();
       initiate_RM_Pending_TAT_Reporting();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/partner/get_partner_booking_summary_data/'+<?php echo $this->session->userdata('partner_id')?>,
            success: function (data) {
                $("#booking_summary").html(data);   
            }
        });
      });
    function get_partner_spare_snapshot(){
        url =  '<?php echo base_url(); ?>employee/dashboard/get_partner_spare_snapshot';
        data = {partner_id:+<?php echo $this->session->userdata('partner_id')?>};
        sendAjaxRequest(data,url,post_request).done(function(response){
            create_partner_spare_snapshot_chart(response);
        });
    }
    function create_partner_spare_snapshot_chart(response) {
        var data = JSON.parse(response);
        var spare_status = data.status.split(',');
        var spare_count = JSON.parse("[" + data.spare_count + "]");
        $('#loader_gif_spare_snapshot').hide();
        $('#spare_snapshot').fadeIn();
        rm_based_chart = new Highcharts.Chart({
            chart: {
                renderTo: 'spare_snapshot',
            },
            title: {
                text: ''
            },
            xAxis: {
                categories: spare_status
            },
            tooltip: {
                formatter: function() {
                    return this.x + '<br> Count: ' + this.y + '<br>' + ' Amount(Rs.): ' + data.spare_amount[this.x];
                }
            },
            plotOptions: {
                column: {
                    dataLabels: {
                        formatter: function() {return this.y + ' / (Rs. ' + data.spare_amount[this.x] + ')'; },
                        enabled: true,
                        crop: false,
                        overflow: 'none'
                        }
                    }
            },
            legend: {
                enabled: false
            },
            series: [{
                type: 'column',
                name: 'Count',
                data: spare_count
            }]
        });
    
    }
      function get_partner_spare_data_by_status(){
        var url = baseUrl + '/employee/dashboard/get_partner_specific_count_by_status';
        $('#loader_gif1').css('display', 'inherit');
        $('#loader_gif1').attr('src', "<?php echo base_url(); ?>images/loadring.gif");
        $('#spare_details_by_status').hide();
        $.ajax({
            type: 'POST',
            url: url,
            data: {partner_id: partner_id},
            success: function (response) {
                $('#loader_gif1').attr('src', "");
                $('#loader_gif1').css('display', 'none');
                $('#spare_details_by_status').show();
                var data = $.parseJSON(response);
                chart = new Highcharts.Chart({
                    chart: {
                        renderTo: 'spare_details_by_status',
                        type: 'pie'
                    },
                    title: {
                        text: ''
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            borderWidth: 0,
                            showInLegend: true,
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b>: {point.y}'
                            }
                        }
                    },
                    series: [{
                            name: 'Bookings',
                            colorByPoint: true,
                            data: data
                        }],
                    drilldown: {
                        series: []
                    }
                });
            }
        });
    }
         function change_toggal_text(){
        var currentValue = document.getElementById("order_by_toggal").innerHTML;
        if(currentValue =="Sort By Number Of Escalation"){
            document.getElementById("order_by_toggal").innerHTML="Sort BY perentage";
        }
        else{
             document.getElementById("order_by_toggal").innerHTML="Sort By Number Of Escalation";
        }
    }
    
function initiate_escalation_data(){
//    var d = new Date();
//        n = d.getMonth();
//        y = d.getFullYear();
//        date = d.getDate();
        $('input[name="daterange"]').daterangepicker({
             timePicker: true,
        timePickerIncrement: 30,
        locale: {
            format: 'YYYY-MM-DD'
        },
        //startDate: y+'-'+n+'-'+date
        startDate: "<?php echo date("Y-m-d", strtotime("-1 month")); ?>"
    });
}

function around_monthly_data(){
        $('#loader_gif2').fadeIn();
        $('#monthly_booking_chart').fadeOut();
        var data = {partner_id:'<?php echo $this->session->userdata('partner_id') ?>'};
        url =  '<?php echo base_url(); ?>employee/dashboard/get_bookings_data_by_month';
        
        sendAjaxRequest(data,url,post_request).done(function(response){
            get_mothly_booking_status(response,'1');
        });
    }
        function get_mothly_booking_status(response,chart_render_to){
        
        if(chart_render_to === '1'){
            render_div = 'monthly_booking_chart';
            $('#loader_gif2').hide();
        }else{
            render_div = 'monthly_unit_booking_chart';
            $('#loader_gif6').hide();
        }
        var data = JSON.parse(response);
        var month = data.month.split(',');
        var completed_booking = JSON.parse("[" + data.completed_booking + "]");
        $('#'+render_div).fadeIn();
        chart = new Highcharts.Chart({
            chart: {
                renderTo: render_div,
                type: 'column'
            },
            title: {
                text: '',
                x: -20 //center
            },
            xAxis: {
                categories: month
            },
            yAxis: {
                title: {
                    text: 'Count'
                },
                plotLines: [{
                        value: 0,
                        width: 1,
                        color: '#808080'
                    }]
            },
            plotOptions: {
                column: {
                    dataLabels: {
                        enabled: true,
                        crop: false,
                        overflow: 'none'
                    }
                }
            },
            series: [
                {
                    name: 'Completed Bookings',
                    data: completed_booking
                }]
        });
    }
    function get_bookings_data_by_rm(){
        partner_id = '<?php echo $this->session->userdata('partner_id'); ?>';
        $('#loader_gif3').fadeIn();
        $('#state_type_booking_chart').fadeOut();
        var data = {'partner_id':partner_id};
        url =  '<?php echo base_url(); ?>employee/dashboard/get_booking_data_by_region';
        
        sendAjaxRequest(data,url,post_request).done(function(response){
            create_chart_based_on_bookings_state(response);
        });
    }
    function create_chart_based_on_bookings_state(response) {
        var data = JSON.parse(response);
        var rm = data.rm.split(',');
        var region = data.region.split(',');
        var cancelled = JSON.parse("[" + data.cancelled + "]");
        var completed = JSON.parse("[" + data.completed + "]");
        var pending = JSON.parse("[" + data.pending + "]");
        var total = JSON.parse("[" + data.total + "]");
        $('#loader_gif3').hide();
        $('#state_type_booking_chart').fadeIn();
        rm_based_chart = new Highcharts.Chart({
            chart: {
                renderTo: 'state_type_booking_chart',
                type: 'column',
                events: {
                    load: Highcharts.drawTable
                }
            },
            title: {
                text: '',
                x: -20 //center
            },
            xAxis: {
                categories: region
            },
            yAxis: {
                title: {
                    text: 'Count'
                },
                plotLines: [{
                        value: 0,
                        width: 1,
                        color: '#808080'
                    }]
            },
            plotOptions: {
                column: {
                    dataLabels: {
                        enabled: true,
                        crop: false,
                        overflow: 'none'
                    }
                }
            },
            series: [
                {
                    name: 'Cancelled Bookings',
                    data: cancelled,
                }, {
                    name: 'Completed Bookings',
                    data: completed
                },
                {
                    name: 'Pending Bookings',
                    data: pending
                }, {
                    name: 'Total Bookings',
                    data: total
                }]
        });
    
    }
    $('#reportrange2').on('apply.daterangepicker', function (ev, picker) {
        $('#loader_gif3').show();
        $('#state_type_booking_chart').hide();
        var startDate = picker.startDate.format('YYYY-MM-DD');
        var endDate = picker.endDate.format('YYYY-MM-DD');
        url = baseUrl + '/employee/dashboard/get_booking_data_by_region/1';
        var data = {sDate: startDate, eDate: endDate};
        
        sendAjaxRequest(data,url,post_request).done(function(response){
            create_chart_based_on_bookings_state(response);
        });
    });
    
     function initiate_RM_Pending_TAT_Reporting(){
        // $('input[name="daterange_completed_bookings"]').daterangepicker({
        $('input[id="pending_daterange_id_rm"]').daterangepicker({
            timePicker: true,
            timePickerIncrement: 30,
            locale: {
                format: 'YYYY-MM-DD'
            },
            startDate: "<?php echo date("Y-m-d", strtotime("-12 month")); ?>"
        });
    } function open_full_view(id,url,is_am,is_pending,form_id){
      entity_id = id.split("_")[1];
      final_url = url+entity_id+'/0/'+is_am+'/'+is_pending;
      $('#'+form_id).attr('action', final_url);
      $('#'+form_id).submit();
    }
    </script>
    <style>
        .highcharts-credits{
            display: none;
        }
        .tile_count{
            margin-bottom: 0px;
            margin-top: 15px;
        }
/*        .highcharts-contextbutton{
            display: none;
        }*/
        .select2-container--default{
            width: 175px !important;
        }
        </style>
  

    
