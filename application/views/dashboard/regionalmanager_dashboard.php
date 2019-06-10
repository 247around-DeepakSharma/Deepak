<script src="<?php echo base_url(); ?>js/base_url.js"></script>
<script src = "https://ajax.googleapis.com/ajax/libs/angularjs/1.3.14/angular.min.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/app.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/controller/controllers.js"></script>
<style>
    .collape_icon {
        font-size: 18px;
        color: #4b5561 !important;
        float:right;
    }
</style>
<style>
    .col-md-2 {
        width: 16.666667%;
    }
    .tile_count .tile_stats_count, ul.quick-list li {
        white-space: normal;
    }
    .text_warning{
        color:red;
    }
    [ng\:cloak], [ng-cloak], .ng-cloak {
        display: none !important;
    }
    .select2-selection--multiple{
        width: 156px !important;
        border: 1px solid #aaa;
    }
</style>
<div class="right_col" role="main">
    
    <div id ='admin_dashboard_app_rm'>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title" style="padding-left:0px;">
                    <h2>RM TAT Reporting</h2>

                    <span class="collape_icon" href="#RM_completed_booking_reports_div" data-toggle="collapse" onclick="initialise_RM_TAT_reporting(this)"><i class="fa fa-plus-square" aria-hidden="true"></i></span>
                   
                    <div class="clearfix"></div>
                </div>
                <div class="x_content collapse" id="RM_completed_booking_reports_div">
                <div class="table-responsive" id="escalation_data" ng-controller="completedBooking_Controller" ng-cloak="">
                    <form action="" method="post" target="_blank" id="rm_completed_booking_form" style="float: left;width: 1090px;">
                    <div class="col-md-3" style="margin: 0px;padding: 0px 1px;width: 130px;">
                        <div class="item form-group">
                            <div class="col-md-12 col-sm-12 col-xs-12" style="padding-left: 0px;">
                                <label for="">Partners</label>
                                <select class="form-control filter_table" id="partner_id" name="partner_id">
                                    <option value="not_set" selected="selected">All</option>
                                    <?php foreach($partners as $val){ ?>
                                    <option value="<?php echo $val['id']?>"><?php echo $val['public_name']?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
 <div class="col-md-3" style="margin: 0px;padding: 0px 1px;width: 130px;">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <label for="">Services</label>
                            <select class="form-control filter_table" id="service_id" name="services">
                                <option value="not_set" selected="selected">All</option>
                                <?php foreach($services as $val){ ?>
                                <option value="<?php echo $val['id']?>"><?php echo $val['services']?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                        <div class="form-group col-md-3" style="width:190px;">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <label for="">Request Type</label>
                            <select class="form-control filter_table" id="request_type" name="request_type[]" multiple="">
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
                            <select class="form-control filter_table" id="free_paid" name="free_paid">
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
                            <select class="form-control filter_table" id="upcountry" name="upcountry">
                                <option value="not_set">All</option>
                                <option value="Yes">Yes</option>
                                 <option value="No" selected="selected">No</option>
                            </select>
                        </div>
                    </div>
                </div>
                        <div class="form-group col-md-3" style="width:200px;">
                                         <label for="">Booking Completed Date</label>
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
                    <div class="form-group col-md-3" style="width:100px;">
                    <button class="btn btn-primary" ng-click="ShowRMCompletedBookingBYDateRange()" ng-model="partner_dashboard_filter" style="margin-top: 23px;background: #405467;border-color: #405467;">Apply Filters</button>
                </div>
                    <br>
                <div class="clear"></div>
                <table class="table table-striped table-bordered jambo_table bulk_action">
                    <thead>
                        <tr>
                            <th>S.N</th>
                            <th>RM</th>
                            <th>D0</th>
                            <th>D1</th>
                            <th>D2</th>
                            <th>D3</th>
                            <th>D4</th>
                            <th>D5 - D7</th>
                             <th>D8 - D15</th>
                             <th>> D15</th>
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
<!--                           <td><a type="button" id="vendor_{{x.id}}" class="btn btn-info" target="_blank" href="<?php echo base_url(); ?>employee/dashboard/tat_calculation_full_view/{{x.id}}/0/1">{{x.entity}}</a></td>-->
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
            </div>
        </div>
    </div>            
    </div>   
    </div>
        </div>
    
            <div id="admin_dashboard_app_pending_rm">
            <div class="x_panel">
                <div class="x_title" style="padding-left: 0px;">
                    <h2>RM Open Call Reports</h2>
                    <span class="collape_icon" href="#RM_TAT_Reporting_pending" data-toggle="collapse" onclick="initiate_RM_Pending_TAT_Reporting()"><i class="fa fa-plus-square" aria-hidden="true"></i></span>
                    <div class="clearfix"></div>
                </div>
                <div id="RM_TAT_Reporting_pending" class="collapse">
                <div class="table-responsive" id="escalation_data" ng-controller="pendingBooking_ControllerRM" ng-cloak="">
                    <form action="" method="post" target="_blank" id="rm_pending_booking_form" style="float: left;width: 1090px;">
                    <div class="col-md-3" style="margin: 0px;padding: 0px 1px;width: 130px;">
                        <div class="item form-group">
                            <div class="col-md-12 col-sm-12 col-xs-12" style="padding-left: 0px;">
                                <label for="">Partners</label>
                                <select class="form-control filter_table" id="partner_id_rm_pending" name="partner_id">
                                    <option value="not_set" selected="selected">All</option>
                                    <?php foreach($partners as $val){ ?>
                                    <option value="<?php echo $val['id']?>"><?php echo $val['public_name']?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
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
                        <div class="form-group col-md-3" style="width:190px;margin-left: -18px;">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <label for="">Request Type</label>
                            <select class="form-control filter_table" id="request_type_rm_pending" name="request_type[]" multiple="">
                                <option value="not_set" selected="selected">All</option>
                                <option value="Installation">Installations</option>
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
                            <select class="form-control filter_table" id="upcountry_rm_pending" name="upcountry" multiple="">
                                <option value="not_set">All</option>
                                <option value="Yes">Yes</option>
                                 <option value="No">No</option>
                            </select>
                        </div>
                    </div>
                </div>
                    <div class="form-group col-md-3" style="margin-left: 33px;">
                                         <label for="">Dependency</label>
                                         <select class="form-control filter_table"  id="pending_dependency" name="status[]" multiple="">
                                             <option value="not_set">All</option>
                                             <option value="247Around" selected="selected">Admin</option>
                                            <option value="Partner">Partner</option>
                                            <option value="Vendor:not_define" selected="selected">Vendor</option>
                                        </select>
                    </div>
                        <div class="form-group col-md-3" style="width:186px;">
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
                        <tr ng-repeat="x in pendingBookingByRM | orderBy:'TAT_16'" ng-if="x.entity !== 'Total'">
                           <td>{{$index+1}}</td>
                           <td><button type="button" id="vendor_{{x.id}}" class="btn btn-info" target="_blank" 
                                       onclick="open_full_view(this.id,'<?php echo base_url(); ?>employee/dashboard/tat_calculation_full_view/','0','Pending','rm_pending_booking_form')">{{x.entity}}</button></td>
                                       <td><form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank" style="width: 8%;">
                                            <input type="hidden" name="booking_id_status" value=" {{x.TAT_0_bookings}}">
                                            <input type="submit" value="{{x.TAT_0}} ({{x.TAT_0_per}}%)"  class="btn btn-success">
                                             </form></td>
                                              <td><form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank" style="width: 8%;">
                                            <input type="hidden" name="booking_id_status" value=" {{x.TAT_1_bookings}}">
                                            <input type="submit" value="{{x.TAT_1}} ({{x.TAT_1_per}}%)" class="btn btn-success">
                                             </form></td>
                                             <td><form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank" style="width: 8%;">
                                            <input type="hidden" name="booking_id_status" value=" {{x.TAT_2_bookings}}">
                            <input type="submit" value="{{x.TAT_2}} ({{x.TAT_2_per}}%)" ng-if="x.TAT_2 > 0" class="btn btn-danger">
                                            <input type="submit" value="{{x.TAT_2}} ({{x.TAT_2_per}}%)" ng-if="x.TAT_2 <= 0" class="btn btn-success">
                                             </form></td>
                                             <td><form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank" style="width: 8%;">
                                            <input type="hidden" name="booking_id_status" value=" {{x.TAT_3_bookings}}">
                             <input type="submit" value="{{x.TAT_3}} ({{x.TAT_3_per}}%)" ng-if="x.TAT_3 > 0" class="btn btn-danger">
                                            <input type="submit" value="{{x.TAT_3}} ({{x.TAT_3_per}}%)" ng-if="x.TAT_3 <= 0" class="btn btn-success">
                                             </form></td>
                                             <td><form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank" style="width: 8%;">
                                            <input type="hidden" name="booking_id_status" value=" {{x.TAT_4_bookings}}">
                              <input type="submit" value="{{x.TAT_4}} ({{x.TAT_4_per}}%)" ng-if="x.TAT_4 > 0" class="btn btn-danger">
                                            <input type="submit" value="{{x.TAT_4}} ({{x.TAT_4_per}}%)" ng-if="x.TAT_4 <= 0" class="btn btn-success">
                                             </form></td>
                                             <td><form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank" style="width: 8%;">
                                            <input type="hidden" name="booking_id_status" value=" {{x.TAT_5_bookings}}">
                      <input type="submit" value="{{x.TAT_5}} ({{x.TAT_5_per}}%)" ng-if="x.TAT_5 > 0" class="btn btn-danger">
                                            <input type="submit" value="{{x.TAT_5}} ({{x.TAT_5_per}}%)" ng-if="x.TAT_5 <= 0" class="btn btn-success">
                                             </form></td>
                                             <td><form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank" style="width: 8%;">
                                            <input type="hidden" name="booking_id_status" value=" {{x.TAT_8_bookings}}">
                            <input type="submit" value="{{x.TAT_8}} ({{x.TAT_8_per}}%)" ng-if="x.TAT_8 > 0" class="btn btn-danger">
                                            <input type="submit" value="{{x.TAT_8}} ({{x.TAT_8_per}}%)" ng-if="x.TAT_8 <= 0" class="btn btn-success">
                                             </form></td>
                                             <td><form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank" style="width: 8%;">
                                            <input type="hidden" name="booking_id_status" value=" {{x.TAT_16_bookings}}">
                              <input type="submit" value="{{x.TAT_16}} ({{x.TAT_16_per}}%)" ng-if="x.TAT_16 > 0" class="btn btn-danger">
                                            <input type="submit" value="{{x.TAT_16}} ({{x.TAT_16_per}}%)" ng-if="x.TAT_16 <= 0" class="btn btn-success">
                                             </form></td>
                           <td>{{x.Total_Pending}} <br> ({{x.TAT_total_per}}%)</td>
                        </tr>
                        <tr ng-repeat="x in pendingBookingByRM | orderBy:'TAT_16'" ng-if="x.entity == 'Total'">
                            <td>{{$index+1}}</td>
                            <td><button type="button" id="vendor_{{x.id}}" class="btn btn-info" target="_blank" 
                                       onclick="open_full_view(this.id,'<?php echo base_url(); ?>employee/dashboard/tat_calculation_full_view/','0','Pending','rm_pending_booking_form')">{{x.entity}}</button></td>
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
    <?php if(isset($saas_flag) && (!$saas_flag)) { ?>
    <!-- get rm missing pincode ajax request-->
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0px !important;">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Serviceability Missing Report <button type="button"class="btn btn-default" style="float: right;margin-bottom: 10px;padding: 1px 4px;margin-top: 0px;font-size: 8px;margin-left: 5px;background: #f7a35c;
    color: #fff;border: none;" data-toggle="tooltip"data-placement="right"title="Missing Pincode  And Missing Pincode Percent">?</button></h2>
                    <span class="collape_icon" href="#RM_Pincode_Reporting" data-toggle="collapse" onclick="get_rm_missing_data()"><i class="fa fa-plus-square" aria-hidden="true"></i></span>
                   
                    <div class="clearfix"></div>
                </div>
                <div class="x_content collapse" id="RM_Pincode_Reporting">
                    <div class="table-responsive" id="rm_pincode_data">
                        <center><img id="missing_rm_loader" src="<?php echo base_url(); ?>images/loadring.gif"></center>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="admin_dashboard_app">
        <div class="x_panel">
            <div class="x_title" style="padding-left:0px;">
                    <h2>AM TAT Reporting</h2>
                     <span class="collape_icon" href="#AM_TAT_Reporting_div" data-toggle="collapse" onclick="initialise_AM_TAT_reporting()" style="margin-right: 8px;"><i class="fa fa-plus-square" aria-hidden="true"></i></span>
                    <div class="clearfix"></div>
                </div>
            <div class="table-responsive collapse" id="AM_TAT_Reporting_div" ng-controller="completedBooking_ControllerAM" ng-cloak="">
                <form action="" method="post" target="_blank" id="am_completed_booking_form" style="float: left;width: 1090px;">
                <div class="col-md-3" style="margin: 0px;padding: 0px 1px;width: 130px;">
                        <div class="item form-group">
                            <div class="col-md-12 col-sm-12 col-xs-12" style="padding-left: 0px;">
                                <label for="">Partners</label>
                                <select class="form-control filter_table" id="partner_id_am" name="partner_id">
                                    <option value="not_set" selected="selected">All</option>
                                    <?php foreach($partners as $val){ ?>
                                    <option value="<?php echo $val['id']?>"><?php echo $val['public_name']?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3" style="margin: 0px;padding: 0px 1px;width: 130px;">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <label for="">Services</label>
                            <select class="form-control filter_table" id="service_id_am" name="services">
                                <option value="" selected="selected">Select Service</option>
                                <?php foreach($services as $val){ ?>
                                <option value="<?php echo $val['id']?>"><?php echo $val['services']?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                    <div class="form-group col-md-3" style="width:190px;">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <label for="">Request Type</label>
                            <select class="form-control filter_table" id="request_type_am" name="request_type[]" multiple="">
                                <option value="">Request Type</option>
                                <option value="not_set" selected="selected">All</option>
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
                            <select class="form-control filter_table" id="free_paid_am" name="free_paid">
                                <option value="" selected="selected">In Warranty</option>
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
                            <select class="form-control filter_table" id="upcountry_am" name="upcountry">
                                <option value="">Is Upcountry</option>
                                <option value="Yes">Yes</option>
                                 <option value="No" selected="selected">No</option>
                            </select>
                        </div>
                    </div>
                </div>
                    <div class="form-group col-md-3" style="width:200px;">
                                         <label for="">Booking Completed Date</label>
                                         <input type="text" class="form-control" name="daterange_completed_bookings" id="completed_daterange_id_am" ng-change="ShowAMCompletedBookingBYDateRange()" ng-model="dates">
                            </div>
                    <div class="form-group col-md-3">
                                         <label for="">Booking Status</label>
                                         <select class="form-control"  ng-model="status" id="completed_status_am" name="status">
                                            <option value="not_set">All</option>
                                            <option value="Completed" ng-selected="true">Completed</option>
                                            <option value="Cancelled">Cancelled</option>
                                        </select>
                    </div>
                    </form>
                <div class="form-group col-md-3" style="width:100px;">
                    <button class="btn btn-primary" ng-click="ShowAMCompletedBookingBYDateRange()" ng-model="partner_dashboard_filter" style="margin-top: 23px;background: #405467;border-color: #405467;">Apply Filters</button>
                </div>
                    <br>
                <div class="clear"></div>
                <table class="table table-striped table-bordered jambo_table bulk_action">
                    <thead>
                        <tr>
                            <th>S.no</th>
                            <th>AM</th>
                            <th>D0</th>
                            <th>D1</th>
                            <th>D2</th>
                            <th>D3</th>
                            <th>D4</th>
                            <th>D5 - D7</th>
                             <th>D8 - D15</th>
                             <th>> D15</th>
                        </tr>
                    </thead>
 <tbody>
                        <tr ng-repeat="x in completedBookingByAM.TAT | orderBy:'TAT_16'" ng-if='completedBookingByAM.leg_1 !== undefined'>
                            <td style="padding: 4px 12px;">{{$index+1}}</td>
<!--                           <td><a type="button" id="vendor_{{x.id}}" class="btn btn-info" target="_blank" href="<?php echo base_url(); ?>employee/dashboard/tat_calculation_full_view/{{x.id}}/0/1">{{x.entity}}</a></td>-->
                            <td style="padding: 4px 12px;"><button style="margin-top: 10px;" type="button" id="vendor_{{x.id}}" class="btn btn-info" target="_blank" 
                                       onclick="open_full_view(this.id,'<?php echo base_url(); ?>employee/dashboard/tat_calculation_full_view/','1','0','am_completed_booking_form')">
                                   {{completedBookingByAM.leg_1[$index].entity}} </button><p style="float:right;margin-bottom: 0px;">leg_1<br>leg_2<br>Total</p></td>
                                   
                           <td style="padding: 4px 12px;">{{completedBookingByAM.leg_1[$index].TAT_0}} ({{completedBookingByAM.leg_1[$index].TAT_0_per}}%)<br>
                               {{completedBookingByAM.leg_2[$index].TAT_0}} ({{completedBookingByAM.leg_2[$index].TAT_0_per}}%)<br>
                               {{completedBookingByAM.TAT[$index].TAT_0}} ({{completedBookingByAM.TAT[$index].TAT_0_per}}%) </td>
                           
                           <td style="padding: 4px 12px;">{{completedBookingByAM.leg_1[$index].TAT_1}} ({{completedBookingByAM.leg_1[$index].TAT_1_per}}%)<br>
                               {{completedBookingByAM.leg_2[$index].TAT_1}} ({{completedBookingByAM.leg_2[$index].TAT_1_per}}%)<br>
                              {{completedBookingByAM.TAT[$index].TAT_1}} ({{completedBookingByAM.TAT[$index].TAT_1_per}}%)</td>
                           
                           <td style="padding: 4px 12px;">{{completedBookingByAM.leg_1[$index].TAT_2}} ({{completedBookingByAM.leg_1[$index].TAT_2_per}}%)<br>
                               {{completedBookingByAM.leg_2[$index].TAT_2}} ({{completedBookingByAM.leg_2[$index].TAT_2_per}}%)<br>
                                {{completedBookingByAM.TAT[$index].TAT_2}} ({{completedBookingByAM.TAT[$index].TAT_2_per}}%) </td>
                           
                           <td style="padding: 4px 12px;">{{completedBookingByAM.leg_1[$index].TAT_3}} ({{completedBookingByAM.leg_1[$index].TAT_3_per}}%)<br>
                               {{completedBookingByAM.leg_2[$index].TAT_3}} ({{completedBookingByAM.leg_2[$index].TAT_3_per}}%)<br>
                                {{completedBookingByAM.TAT[$index].TAT_3}} ({{completedBookingByAM.TAT[$index].TAT_3_per}}%)</td>
                           
                           <td style="padding: 4px 12px;">{{completedBookingByAM.leg_1[$index].TAT_4}} ({{completedBookingByAM.leg_1[$index].TAT_4_per}}%)<br>
                               {{completedBookingByAM.leg_2[$index].TAT_4}} ({{completedBookingByAM.leg_2[$index].TAT_4_per}}%)<br>
                               {{completedBookingByAM.TAT[$index].TAT_4}} ({{completedBookingByAM.TAT[$index].TAT_4_per}}%) </td>
                           
                           <td style="padding: 4px 12px;">{{completedBookingByAM.leg_1[$index].TAT_5}} ({{completedBookingByAM.leg_1[$index].TAT_5_per}}%)<br>
                               {{completedBookingByAM.leg_2[$index].TAT_5}} ({{completedBookingByAM.leg_2[$index].TAT_5_per}}%)<br>
                                {{completedBookingByAM.TAT[$index].TAT_5}} ({{completedBookingByAM.TAT[$index].TAT_5_per}}%) </td>
                           
                           <td style="padding: 4px 12px;">{{completedBookingByAM.leg_1[$index].TAT_8}} ({{completedBookingByAM.leg_1[$index].TAT_8_per}}%)<br>
                               {{completedBookingByAM.leg_2[$index].TAT_8}} ({{completedBookingByAM.leg_2[$index].TAT_8_per}}%)<br>
                               {{completedBookingByAM.TAT[$index].TAT_8}} ({{completedBookingByAM.TAT[$index].TAT_8_per}}%)</td>
                           
                           <td style="padding: 4px 12px;">{{completedBookingByAM.leg_1[$index].TAT_16}} ({{completedBookingByAM.leg_1[$index].TAT_16_per}}%)<br>
                               {{completedBookingByAM.leg_2[$index].TAT_16}} ({{completedBookingByAM.leg_2[$index].TAT_16_per}}%)<br>
                               {{completedBookingByAM.TAT[$index].TAT_16}} ({{completedBookingByAM.TAT[$index].TAT_16_per}}%) </td>
                        </tr>
                        <tr ng-repeat="x in completedBookingByAM.TAT | orderBy:'TAT_16'" ng-if='completedBookingByAM.leg_1 == undefined'>
                           <td>{{$index+1}}</td>
<!--                           <td><a type="button" id="vendor_{{x.id}}" class="btn btn-info" target="_blank" href="<?php echo base_url(); ?>employee/dashboard/tat_calculation_full_view/{{x.id}}/0/1">{{x.entity}}</a></td>-->
                           <td><button type="button" id="vendor_{{x.id}}" class="btn btn-info" target="_blank" 
                                       onclick="open_full_view(this.id,'<?php echo base_url(); ?>employee/dashboard/tat_calculation_full_view/','1','0','am_completed_booking_form')">{{x.entity}}</button></td>
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
                <center><img id="loader_gif_completed_am" src="<?php echo base_url(); ?>images/loadring.gif" ></center>
            </div>
        </div>
        </div> 
    
          <div id="admin_dashboard_app_pending_am">
<!--                AM reporting-->
                <div class="x_panel">
                    <div class="x_title" style="pending-left:0px;">
                    <h2>AM Open Call Report</h2>
                    <span class="collape_icon" href="#AM_TAT_Reporting_pending" data-toggle="collapse" onclick="initiate_AM_Pending_TAT_Reporting()"><i class="fa fa-plus-square" aria-hidden="true"></i></span>
                    <div class="clearfix"></div>
                </div>
                <div id="AM_TAT_Reporting_pending" class="collapse">
                <div class="table-responsive" id="escalation_data" ng-controller="pendingBooking_ControllerAM" ng-cloak="">
                    <form action="" method="post" target="_blank" id="am_pending_booking_form" style="float: left;width: 1090px;">
                    <div class="col-md-3" style="margin: 0px;padding: 0px 1px;width: 130px;">
                        <div class="item form-group">
                            <div class="col-md-12 col-sm-12 col-xs-12" style="padding-left: 0px;">
                                <label for="">Partners</label>
                                <select class="form-control filter_table" id="partner_id_am_pending" name="partner_id">
                                    <option value="not_set" selected="selected">All</option>
                                    <?php foreach($partners as $val){ ?>
                                    <option value="<?php echo $val['id']?>"><?php echo $val['public_name']?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3" style="margin: 0px;padding: 0px 1px;width: 130px;">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <label for="">Services</label>
                            <select class="form-control filter_table" id="service_id_am_pending" name="services">
                                <option value="not_set" selected="selected">All</option>
                                <?php foreach($services as $val){ ?>
                                <option value="<?php echo $val['id']?>"><?php echo $val['services']?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                        <div class="form-group col-md-3" style="width:190px;margin-left: -18px;">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <label for="">Request Type</label>
                            <select class="form-control filter_table" id="request_type_am_pending" name="request_type[]" multiple="">
                                <option value="not_set" selected="selected">All</option>
                                <option value="Installation" >Installations</option>
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
                            <select class="form-control filter_table" id="free_paid_am_pending" name="free_paid">
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
                            <select class="form-control filter_table" id="upcountry_am_pending" name="upcountry" multiple="">
                                <option value="not_set" selected="selected">All</option>
                                <option value="Yes">Yes</option>
                                 <option value="No">No</option>
                            </select>
                        </div>
                    </div>
                </div>
                    <div class="form-group col-md-3" style="margin-left: 33px;">
                                         <label for="">Dependency</label>
                                         <select class="form-control" id="pending_dependency_am" name="status[]" multiple="">
                                             <option value="not_set" selected="selected">All</option>
                                            <option value="247Around" selected="selected">247Around</option>
                                            <option value="Partner">Partner</option>
                                            <option value="Vendor:not_define" selected="selected">Vendor</option>
                                        </select>
                    </div>
                        <div class="form-group col-md-3" style="width:186px;">
                                         <label for="">Booking Completed Date</label>
                                         <input type="text" class="form-control" name="daterange_completed_bookings" id="pending_daterange_id_am" ng-change="ShowAMPendingBookingBYDateRange()" ng-model="dates">
                            </div>
                    </form>
                    <div class="form-group col-md-3" style="width:100px;">
                    <button class="btn btn-primary" ng-click="ShowAMPendingBookingBYDateRange()" ng-model="partner_dashboard_filter" style="margin-top: 23px;background: #405467;border-color: #405467;">Apply Filters</button>
                </div>
                    <br>
                <div class="clear"></div>
                <table class="table table-striped table-bordered jambo_table bulk_action">
                    <thead>
                        <tr>
                            <th>S.no</th>
                            <th>AM</th>
                            <th>D0</th>
                            <th>D1</th>
                            <th>D2</th>
                            <th>D3</th>
                            <th>D4</th>
                            <th>D5 - D7</th>
                             <th>D8 - D15</th>
                             <th>> D15</th>
                             <th> Total</th>
                        </tr>
                    </thead>
                    <tbody>
                         <tr ng-repeat="x in pendingBookingByAM | orderBy:'TAT_16'" ng-if="x.entity !== 'Total'">
                           <td>{{$index+1}}</td>
                           <td><button type="button" id="vendor_{{x.id}}" class="btn btn-info" target="_blank" 
                                       onclick="open_full_view(this.id,'<?php echo base_url(); ?>employee/dashboard/tat_calculation_full_view/','1','pending','am_pending_booking_form')">{{x.entity}}</button></td>
                                       <td><form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank" style="width: 8%;">
                                            <input type="hidden" name="booking_id_status" value=" {{x.TAT_0_bookings}}">
                                            <input type="submit" value="{{x.TAT_0}} ({{x.TAT_0_per}}%)"  class="btn btn-success">
                                             </form></td>
                                              <td><form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank" style="width: 8%;">
                                            <input type="hidden" name="booking_id_status" value=" {{x.TAT_1_bookings}}">
                                            <input type="submit" value="{{x.TAT_1}} ({{x.TAT_1_per}}%)" class="btn btn-success">
                                             </form></td>
                                             <td><form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank" style="width: 8%;">
                                            <input type="hidden" name="booking_id_status" value=" {{x.TAT_2_bookings}}">
                            <input type="submit" value="{{x.TAT_2}} ({{x.TAT_2_per}}%)" ng-if="x.TAT_2 > 0" class="btn btn-danger">
                                            <input type="submit" value="{{x.TAT_2}} ({{x.TAT_2_per}}%)" ng-if="x.TAT_2 <= 0" class="btn btn-success">
                                             </form></td>
                                             <td><form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank" style="width: 8%;">
                                            <input type="hidden" name="booking_id_status" value=" {{x.TAT_3_bookings}}">
                             <input type="submit" value="{{x.TAT_3}} ({{x.TAT_3_per}}%)" ng-if="x.TAT_3 > 0" class="btn btn-danger">
                                            <input type="submit" value="{{x.TAT_3}} ({{x.TAT_3_per}}%)" ng-if="x.TAT_3 <= 0" class="btn btn-success">
                                             </form></td>
                                             <td><form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank" style="width: 8%;">
                                            <input type="hidden" name="booking_id_status" value=" {{x.TAT_4_bookings}}">
                              <input type="submit" value="{{x.TAT_4}} ({{x.TAT_4_per}}%)" ng-if="x.TAT_4 > 0" class="btn btn-danger">
                                            <input type="submit" value="{{x.TAT_4}} ({{x.TAT_4_per}}%)" ng-if="x.TAT_4 <= 0" class="btn btn-success">
                                             </form></td>
                                             <td><form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank" style="width: 8%;">
                                            <input type="hidden" name="booking_id_status" value=" {{x.TAT_5_bookings}}">
                      <input type="submit" value="{{x.TAT_5}} ({{x.TAT_5_per}}%)" ng-if="x.TAT_5 > 0" class="btn btn-danger">
                                            <input type="submit" value="{{x.TAT_5}} ({{x.TAT_5_per}}%)" ng-if="x.TAT_5 <= 0" class="btn btn-success">
                                             </form></td>
                                             <td><form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank" style="width: 8%;">
                                            <input type="hidden" name="booking_id_status" value=" {{x.TAT_8_bookings}}">
                            <input type="submit" value="{{x.TAT_8}} ({{x.TAT_8_per}}%)" ng-if="x.TAT_8 > 0" class="btn btn-danger">
                                            <input type="submit" value="{{x.TAT_8}} ({{x.TAT_8_per}}%)" ng-if="x.TAT_8 <= 0" class="btn btn-success">
                                             </form></td>
                                             <td><form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank" style="width: 8%;">
                                            <input type="hidden" name="booking_id_status" value=" {{x.TAT_16_bookings}}">
                              <input type="submit" value="{{x.TAT_16}} ({{x.TAT_16_per}}%)" ng-if="x.TAT_16 > 0" class="btn btn-danger">
                                            <input type="submit" value="{{x.TAT_16}} ({{x.TAT_16_per}}%)" ng-if="x.TAT_16 <= 0" class="btn btn-success">
                                             </form></td>
                           <td>{{x.Total_Pending}} <br> ({{x.TAT_total_per}}%)</td>
                        </tr>
                        <tr ng-repeat="x in pendingBookingByAM | orderBy:'TAT_16'" ng-if="x.entity == 'Total'">
                            <td>{{$index+1}}</td>
                           <td><button type="button" id="vendor_{{x.id}}" class="btn btn-info" target="_blank" 
                                       onclick="open_full_view(this.id,'<?php echo base_url(); ?>employee/dashboard/tat_calculation_full_view/','1','pending','am_pending_booking_form')">{{x.entity}}</button></td>
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
                <center><img id="loader_gif_pending_AM" src="<?php echo base_url(); ?>images/loadring.gif" ></center>
            </div>
            </div>
            </div>
    </div> 
    <?php } ?>
    
    <div ng-app="rm_dashboard" ng-cloak="">
    <!-- top tiles -->
    <div class="row tile_count" id="title_count">
        <div class="col-md-12">
            <center><img id="loader_gif_title" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center>
        </div>
    </div>
    <!-- Booking Report End-->
    <div class="row" ng-controller="rm_dashboardController" ng-cloak="">
        <?php
        if ($this->session->userdata("wrong_pincode_msg")) {
            echo "<p style='color: green;text-align: center;font-size: 18px;'>" . $this->session->userdata("wrong_pincode_msg") . "</p>";
            if ($this->session->userdata('wrong_pincode_msg')) {
                $this->session->unset_userdata('wrong_pincode_msg');
            }
        }
        ?>
        <div class="clearfix"></div>
        <div class="col-md-12 col-sm-12 col-xs-12">
<!--            <div class="x_panel">
                <div class="x_title">
                    <h2>Missing Pincodes</h2>
                    <a id="download_pin_code" class="btn btn-success" href="<?php echo base_url(); ?>employee/vendor/insert_pincode_form" style="float:right">Add New Pincode</a>
                    <div class="clearfix"></div>
                </div>

                <div class="x_content">

                    <div class="table-responsive" id="pincode_table_data">
                        <center><img id="loader_gif_unit" src="<?php echo base_url(); ?>images/loadring.gif" ></center>
                    </div>
                </div>
                <a class="btn btn-success"  href="<?php echo base_url() ?>employee/dashboard/missing_pincode_full_view" target="_blank" style="float: right;">Full View</a>
            </div>-->
 <!-- Missing Pincode Section -->
    <div class="row" style="margin-top:10px;">
        <?php
if($this->session->userdata("wrong_pincode_msg")){
    echo "<p style='color: green;text-align: center;font-size: 18px;'>".$this->session->userdata("wrong_pincode_msg")."</p>";
    if($this->session->userdata('wrong_pincode_msg')){$this->session->unset_userdata('wrong_pincode_msg');}
}
?>
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Missing Pincodes</h2>
                    <span class="collape_icon" href="#pincode_table_data_div" data-toggle="collapse" onclick="get_missing_pincodes()"><i class="fa fa-plus-square" aria-hidden="true"></i></span>
                    <?php if(isset($saas_flag) && (!$saas_flag)) { ?>
                    <a id="download_pin_code" class="btn btn-success" href="<?php echo base_url(); ?>employee/vendor/insert_pincode_form" style="float:right">Add New Pincode</a>
                    <?php } ?>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content collapse" id="pincode_table_data_div">
                    <div class="table-responsive" id="pincode_table_data">
                        <center><img id="pincode_loader" src="<?php echo base_url(); ?>images/loadring.gif"></center>
                    </div>
                </div>
            </div>
        </div>
    </div>
 <?php if(isset($saas_flag) && (!$saas_flag)) { ?>
 <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0px !important;">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Upcountry Over Limit Exceed</h2>
                    <span class="collape_icon" href="#upcountry_table_data_div" data-toggle="collapse" onclick="get_upcountry_data()"><i class="fa fa-plus-square" aria-hidden="true"></i></span>
                   
                    <div class="clearfix"></div>
                </div>
                <div class="x_content collapse" id="upcountry_table_data_div">
                    <div class="table-responsive" id="upcountry_table_data">
                        <center><img id="upcountry_loader" src="<?php echo base_url(); ?>images/loadring.gif"></center>
                    </div>
                </div>
            </div>
        </div>
    </div>
 <?php } ?>
    <!-- End Missing Pincode Section -->
        </div>
        <div class="clearfix"></div>
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Escalation</h2>
                    <span class="collape_icon" href="#escalation_data_div" data-toggle="collapse" onclick="initiate_escalation_data()"><i class="fa fa-plus-square" aria-hidden="true"></i></span>
                    <div class="clearfix"></div>
                </div>

                <div class="x_content collapse" id="escalation_data_div">

                    <div class="table-responsive" id="escalation_data">
                        <input type="text" id="session_id_holder" style="display:none;" value="<?php if ($this->session->userdata('user_group') == 'regionalmanager') {
            echo $this->session->userdata('id');
        } ?>">          
                        <button type="button"class="btn btn-default" style="float:right" data-toggle="tooltip"data-placement="left"title="To calculate escalation percentage, logic use current months booking and current month escalation ">?</button>
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
                                    <th>Vendor</th>
                                    <th>Total Booking</th>
                                    <th>Total Escalation</th>
                                    <th>Escalation %</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="y in escalationAllRMData|orderBy:!mytoggle?'-esclation_per':'-total_escalation'" ng-cloak="">
                                    <td>{{$index + 1}}</td>
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
                        <form method="post" action="<?php echo base_url() ?>employee/dashboard/escalation_full_view" target="_blank">
                            <input type="text"  id="sf_json_data" name="sf_json_data" value="apple" style="display:none;"/>
                            <button type="submit" class="btn btn-success">Full View</button>
                            </form>
                            </div>
                    </div>
                </div>-->
    </div>
    <?php if(isset($saas_flag) && (!$saas_flag)) { ?>
     <!-- SF Brackets snapshot Section -->
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel" ng-controller="bracketsSnapshot_Controller" ng-cloak="">
                <div class="x_title">
                    <div class="col-md-6" style="padding-left:0px">
                        <h2>Service Center Brackets Snapshot</h2>
                    </div>
                    <div class="col-md-5">
                        <div class="pull-right" style="margin-right: -20%;">
                            <a class="btn btn-sm btn-success" href="<?php echo base_url();?>employee/dashboard/brackets_snapshot_full_view" target="_blank">Show All</a>
                        </div>
                    </div>
                    <div class="col-md-1" style="    padding-right: 0px;">
                       <span class="collape_icon" href="#service_center_brackets_snapshot_div" data-toggle="collapse" data-ng-click="bracketsSnapshotCall()"><i class="fa fa-plus-square" aria-hidden="true"></i></span> 
                    </div>
                    <div class="clearfix" ></div>
                </div>
                <div class="x_content collapse" id="service_center_brackets_snapshot_div">
                    <div class="table-responsive">
                        <div class="table-responsive" id="escalation_data" >
                            <center><img id="brackets_loader" src="<?php echo base_url(); ?>images/loadring.gif"></center>
                            <div ng-if="brackets_div">
                                <table class="table table-striped table-bordered jambo_table bulk_action">
                                    <thead>
                                        <tr>
                                            <th>S.no</th>
                                            <th>Service Center Name</th>
                                            <th colspan="2">Current Stock</th>
                                            <th>Expected Days Left to Consume Brackets</th>
                                            <th>Order Brackets</th>
                                        </tr>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th>Less than 32"</th>
                                            <th>32" and above</th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-repeat="x in bracketsSnapshot | limitTo:quantity">
                                           <td>{{$index+1}}</td>
                                           <td>{{x.sf_name}}</td>
                                           <td>{{x.l_32}}</td>
                                           <td>{{x.g_32}}</td>
                                           <td>{{x.brackets_exhausted_days}}</td>
                                           <td><a class="btn btn-sm btn-success" href="<?php echo base_url();?>employee/inventory/get_bracket_add_form/{{x.sf_id}}/{{x.sf_name}}" target="_blank">Order brackets</a></td>
                                        </tr>
                                    </tbody>
                                </table>                             
                            </div>
                            <div ng-if="brackets_div_err_msg">
                                <p class="text-center text-danger">{{brackets_div_err_msg_text}}</p>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- SF Brackets Snapshot Section -->
    <?php } ?>
    <div class="row" style="margin-top:10px;">
        <!-- Company Monthly Status -->
        <div class="col-md-6 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Monthly Booking Status <small>Completed</small></h2>
                    <span class="collape_icon" href="#monthly_booking_chart_div" data-toggle="collapse" onclick="around_monthly_data()"><i class="fa fa-plus-square" aria-hidden="true"></i></span>
                    <div class="clearfix"></div>
                </div>
                <div class="col-md-12">
                    <center><img id="loader_gif2" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center>
                </div>
                <div class="x_content collapse" id="monthly_booking_chart_div">
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
                    <h2 style="font-size:15px;">Booking based on Region <small></small></h2>
                    </div>
                    <div class="col-md-6">
                    <small>
                    <div class="nav navbar-right panel_toolbox">
                        <div id="reportrange2" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; margin-right: -14%;">
                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                            <span></span> <b class="caret"></b>
                        </div></small>
                        
                    </div>
                    
                    </div>
                    <div class="col-md-1" style="padding-right:0px"><span class="collape_icon" href="#state_type_booking_chart_div" data-toggle="collapse" onclick="get_bookings_data_by_rm()"><i class="fa fa-plus-square" aria-hidden="true"></i></span></div>
                    <div class="clearfix"></div>
                </div>
                <div class="col-md-12">
                    <center><img id="loader_gif3" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center>
                </div>
                <div class="x_content collapse" id="state_type_booking_chart_div">
                    <div id="state_type_booking_chart"></div>
                </div>
            </div>
        </div>
        <!-- RM wise booking status -->
    </div>
    </div>
</div>   
<!-- END -->
<style>
    .dropdown:hover .dropdown-menu {
        display: block;
    }
</style>
<script>
    $('#request_type').select2();
    $('#request_type_rm_pending').select2();
    $('#request_type_am_pending').select2();
    $('#request_type_am').select2();
    $('#pending_dependency').select2();
    $('#pending_dependency_am').select2();
    $('#upcountry_rm_pending').select2();
    $('#upcountry_am_pending').select2();
    
    
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
    var post_request = 'POST';
    var get_request = 'GET';
    var url = '';
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
    $(document).ready(function () {
        //top count data
        get_query_data();
        //company monthly data
        //around_monthly_data();
        //Rm wise bookings data
        //get_bookings_data_by_rm();
        //missing pincode data
        //get_missing_pincodes();
        

    });
    function get_missing_pincodes(){
        var data = {};
        url = '<?php echo base_url(); ?>employee/dashboard/get_pincode_not_found_sf_details_admin';
        data['partner_id'] = '';
        
        sendAjaxRequest(data,url,post_request).done(function(response){ 
            $("#pincode_table_data").html(response);
        });
    }
    
    $(function () {
        function cb(start, end) {
            $('#reportrange2 span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }

        $('#reportrange2').daterangepicker(options, cb);

        cb(start, end);

    });
    
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
  
   function initiate_escalation_data(){
//        var d = new Date();
//        n = d.getMonth() + 1;
//        y = d.getFullYear();
//        date = d.getDate();
        $('input[id="daterange_id"]').daterangepicker({
            timePicker: true,
            timePickerIncrement: 30,
            locale: {
                format: 'YYYY-MM-DD'
            },
            //startDate: y + '-' + n + '-01'
            startDate: "<?php echo date("Y-m-d", strtotime("first day of previous month")); ?>"
        });
   }
  


    function change_toggal_text() {
        var currentValue = document.getElementById("order_by_toggal").innerHTML;
        if (currentValue == "Sort By Number Of Escalation") {
            document.getElementById("order_by_toggal").innerHTML = "Sort BY perentage";
        } else {
            document.getElementById("order_by_toggal").innerHTML = "Sort By Number Of Escalation";
        }
    }

    function get_query_data() {
        $('#loader_gif_title').fadeIn();
        var data = {};
        url = '<?php echo base_url(); ?>employee/dashboard/execute_title_query';

        sendAjaxRequest(data, url, post_request).done(function (response) {
            $('#loader_gif_title').hide();
            $('#title_count').html(response);
        });
    }

    function sendAjaxRequest(postData, url, type) {
        return $.ajax({
            data: postData,
            url: url,
            type: type
        });
    }
    
    function around_monthly_data(){
        $('#loader_gif2').fadeIn();
        $('#monthly_booking_chart').fadeOut();
        var data = {partner_id:''};
        url =  '<?php echo base_url(); ?>employee/dashboard/get_bookings_data_by_month';
        
        sendAjaxRequest(data,url,post_request).done(function(response){
            get_mothly_booking_status(response,'1');
        });
    }
    
    function get_bookings_data_by_rm(){
        $('#loader_gif3').fadeIn();
        $('#state_type_booking_chart').fadeOut();
        var data = {};
        url =  '<?php echo base_url(); ?>employee/dashboard/get_booking_data_by_region';
        
        sendAjaxRequest(data,url,post_request).done(function(response){
            create_chart_based_on_bookings_state(response);
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
    
    function create_chart_based_on_bookings_state(response) {
        var data = JSON.parse(response);
        var rm = data.rm.split(',');
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
                categories: rm
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
    
    function initialise_AM_TAT_reporting(){
         var dvSecond = document.getElementById('admin_dashboard_app');
         angular.element(document).ready(function() {
         angular.bootstrap(dvSecond, ['admin_dashboard']);
            $('#completed_daterange_id_am').daterangepicker({
                timePicker: true,
                timePickerIncrement: 30,
                locale: {
                     format: 'YYYY-MM-DD'
                },
                startDate: "<?php echo date("Y-m-d", strtotime("-1 month")); ?>"
            });
        });
    }
    
     function initialise_RM_TAT_reporting(){
        var dvSecond = document.getElementById('admin_dashboard_app_rm');
         angular.element(document).ready(function() {
         angular.bootstrap(dvSecond, ['admin_dashboard']);
            $('input[name="daterange_completed_bookings"]').daterangepicker({
              timePicker: true,
             timePickerIncrement: 30,
             locale: {
                 format: 'YYYY-MM-DD'
             },
             startDate: "<?php echo date("Y-m-d", strtotime("-1 month")); ?>"
            });
        });
    }
    
    function initiate_RM_Pending_TAT_Reporting(){
        var dvSecond = document.getElementById('admin_dashboard_app_pending_rm');
        angular.element(document).ready(function() {
        angular.bootstrap(dvSecond, ['admin_dashboard']);
           $('input[id="pending_daterange_id_rm"]').daterangepicker({
           timePicker: true,
           timePickerIncrement: 30,
           locale: {
               format: 'YYYY-MM-DD'
           },
           startDate: "<?php echo date("Y-m-d", strtotime("-12 month")); ?>"
       });
       });
    }
    
     function initiate_AM_Pending_TAT_Reporting(){
        var dvSecond = document.getElementById('admin_dashboard_app_pending_am');
        angular.element(document).ready(function() {
        angular.bootstrap(dvSecond, ['admin_dashboard']);
           $('input[id="pending_daterange_id_am"]').daterangepicker({
            timePicker: true,
            timePickerIncrement: 30,
            locale: {
                format: 'YYYY-MM-DD'
            },
            startDate: "<?php echo date("Y-m-d", strtotime("-12 month")); ?>"
        });
       });
    }
    function open_full_view(id,url,is_am,is_pending,form_id){
      entity_id = id.split("_")[1];
      final_url = url+entity_id+'/0/'+is_am+'/'+is_pending;
      $('#'+form_id).attr('action', final_url);
      $('#'+form_id).submit();
    }
    
    
    function get_upcountry_data(){
        
        var data = {};
        url = '<?php echo base_url(); ?>employee/dashboard/get_upcountry_data';
        data['partner_id'] = '';
        
        sendAjaxRequest(data,url,post_request).done(function(response){
            $("#upcountry_table_data").html(response);
        });
    }
    
     function get_rm_missing_data(){
        
        var data = {};
        url = '<?php echo base_url(); ?>employee/dashboard/get_rm_missing_pincode_data';
        data['partner_id'] = '';
        
        sendAjaxRequest(data,url,post_request).done(function(response){
           $("#rm_pincode_data").html(response);
        });
    }
</script>
