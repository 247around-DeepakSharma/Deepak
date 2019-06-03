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
<!-- page content -->
<div class="right_col ngCloak" role="main" ng-app="admin_dashboard">
    <!-- top tiles -->
    <div class="row tile_count" id="title_count">
        <div class="col-md-12">
            <center><img id="loader_gif_title" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center>
        </div>
    </div>
    <!-- /top tiles -->
    <hr>
    <!-- Booking Report Start-->
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0px !important;">
            <div class="x_panel">
                <div class="x_title" style="padding-left: 0px;">
                    <h2>RM TAT Report</h2>
                    <span class="collape_icon" href="#RM_TAT_Reporting" data-toggle="collapse" onclick="initiate_RM_TAT_Reporting()"><i class="fa fa-minus-square" aria-hidden="true"></i></span>
                    <div class="clearfix"></div>
                </div>
                <div id="RM_TAT_Reporting" class="collapse in">
                <div class="table-responsive" id="escalation_data" ng-controller="completedBooking_Controller" ng-cloak="">
                    <form action="" method="post" target="_blank" id="rm_completed_booking_form" style="float: left;width: 1110px;">
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
                        <div class="form-group col-md-3" style="width: 200px;">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <label for="">Request Type</label>
                            <select class="form-control filter_table" id="request_type" name="request_type[]" multiple="">
                                 <option value="not_set" >All</option>
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
                    <div class="form-group col-md-3">
                                         <label for=""> Closed Date</label>
                                         <input type="text" class="form-control" name="daterange_completed_bookings" id="completed_daterange_id" ng-change="ShowRMCompletedBookingBYDateRange()" ng-model="dates">
                            </div>
                    <div class="form-group col-md-3" style="width: 140px;">
                            <label for="">Booking Status</label>
                           <select class="form-control"  ng-model="status" id="completed_status"name="status">
                               <option value="">All</option>
                               <option value="Completed" ng-selected="true">Completed</option>
                               <option value="Cancelled">Cancelled</option>
                           </select>
                    </div>
                        </form>
                   <div class="form-group col-md-3" style="width: 120px;float:right;">
                        <button class="btn btn-primary" ng-click="ShowRMCompletedBookingBYDateRange()" ng-model="partner_dashboard_filter" style="margin-top: 23px;background: #405467;border-color: #405467;">Apply Filters</button>
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
                             <th>> D15</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat="x in completedBookingByRM.TAT | orderBy:'TAT_16'" ng-if='completedBookingByRM.leg_1 !== undefined'>
                            <td style="padding: 4px 12px;">{{$index+1}}</td>
<!--                           <td><a type="button" id="vendor_{{x.id}}" class="btn btn-info" target="_blank" href  onclick="open_full_view(this.id,'<?php echo base_url(); ?>employee/dashboard/tat_calculation_full_view/','0','0','rm_completed_booking_form')">="<?php echo base_url(); ?>employee/dashboard/tat_calculation_full_view/{{x.id}}/0/1">{{x.entity}}</a></td>-->
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
            </div>
            </div> 
            </div>
        </div>
    </div>
    
    
        <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0px !important;">
            <div class="x_panel">
                <div class="x_title" style="padding-left: 0px;">
                    <h2>RM Open Call Report</h2>
                    <span class="collape_icon" href="#RM_TAT_Reporting_pending" data-toggle="collapse" onclick="initiate_RM_Pending_TAT_Reporting()"><i class="fa fa-plus-square" aria-hidden="true"></i></span>
                    <div class="clearfix"></div>
                </div>
                <div id="RM_TAT_Reporting_pending" class="collapse">
                <div class="table-responsive" id="escalation_data" ng-controller="pendingBooking_ControllerRM" ng-cloak="">
                    <form action="" method="post" target="_blank" id="rm_pending_booking_form" style="float: left;width: 1110px;">
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
                        <div class="form-group col-md-3" style="width: 200px;">
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
                                <option value="not_set" selected="selected">All</option>
                                <option value="Yes">Yes</option>
                                 <option value="No">No</option>
                            </select>
                        </div>
                    </div>
                </div>
                        <div class="form-group col-md-3" style="margin-left: 33px;">
                                         <label for="">Dependency</label>
                                         <select class="form-control filter_table"  id="pending_dependency" name="status[]" multiple="">
                                             <option value="247Around">Admin</option>
                                            <option value="Partner">Partner</option>
                                            <option value="Vendor:not_define" selected="selected">SF</option>
                                        </select>
                    </div>
                    <div class="form-group col-md-3">
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
    </div>
    
    <?php if(isset($saas_flag) && (!$saas_flag)) { ?>
    <!-- Partner Booking Status -->
       <div class="row" style="margin-bottom: 10px;">
        <div class="col-md-12 col-sm-12 col-xs-12 dashboard_graph" style="">
<!--            <div class="x_panel">-->
                <div class="row x_title">
                    <div class="col-md-6">
                        <h2>Partner Booking Status &nbsp;&nbsp;&nbsp;</h2>
                    </div>
                    <div class="col-md-5">
                        <div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; margin-right: -12%;">
                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                            <span></span> <b class="caret"></b>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <span class="collape_icon" href="#chart_container_div" data-toggle="collapse" onclick="initiate_partner_chart_reporting()" style="margin-right: 8px;"><i class="fa fa-plus-square" aria-hidden="true"></i></span>
                    </div>
                </div>
                <div class="collapse" id="chart_container_div">
                <div class="col-md-12 x_content">
                    <center><img id="loader_gif1" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center>
                    <div id="chart_container" class="chart_container"></div>
                </div>
                </div>
                <div class="clearfix"></div>
<!--            </div>-->
        </div>
    </div>
    <!-- End Partner Booking Status -->
      <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0px !important;">
<!--                AM reporting-->
                <div class="x_panel">
                    <div class="x_title" style="padding-left: 0px;">
                    <h2>AM TAT Report</h2>
                    <span class="collape_icon" href="#AM_TAT_Reporting" data-toggle="collapse" onclick="initiate_AM_TAT_Reporting()"><i class="fa fa-plus-square" aria-hidden="true"></i></span>
                    <div class="clearfix"></div>
                </div>
                <div id="AM_TAT_Reporting" class="collapse">
                <div class="table-responsive" id="escalation_data" ng-controller="completedBooking_ControllerAM" ng-cloak="">
                    <form action="" method="post" target="_blank" id="am_completed_booking_form" style="float: left;width: 1110px;">
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
                            <select class="form-control filter_table" id="request_type_am" name="request_type[]" multiple="">
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
                            <select class="form-control filter_table" id="free_paid_am" name="free_paid">
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
                            <select class="form-control filter_table" id="upcountry_am" name="upcountry">
                                <option value="not_set">All</option>
                                <option value="Yes">Yes</option>
                                 <option value="No" selected="selected">No</option>
                            </select>
                        </div>
                    </div>
                </div>
                    <div class="form-group col-md-3">
                                         <label for="">Booking Completed Date</label>
                                         <input type="text" class="form-control" name="daterange_completed_bookings" id="completed_daterange_id_am" ng-change="ShowAMCompletedBookingBYDateRange()" ng-model="dates">
                            </div>
                    <div class="form-group col-md-3">
                            <label for="">Booking Status</label>
                            <select class="form-control"  ng-model="status" id="completed_status_am" name="status">
                               <option value="">All</option>
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
        </div>
    </div>  
    
    
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0px !important;">
<!--                AM reporting-->
                <div class="x_panel">
                    <div class="x_title" style="pending-left:0px;">
                    <h2>AM Open Call Report</h2>
                    <span class="collape_icon" href="#AM_TAT_Reporting_pending" data-toggle="collapse" onclick="initiate_AM_Pending_TAT_Reporting()"><i class="fa fa-plus-square" aria-hidden="true"></i></span>
                    <div class="clearfix"></div>
                </div>
                <div id="AM_TAT_Reporting_pending" class="collapse">
                <div class="table-responsive" id="escalation_data" ng-controller="pendingBooking_ControllerAM" ng-cloak="">
                    <form action="" method="post" target="_blank" id="am_pending_booking_form" style="float: left;width: 1110px;">
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
                        <div class="form-group col-md-3" style="width:200px;">
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
                                            <option value="247Around" selected="selected">247Around</option>
                                            <option value="Partner">Partner</option>
                                            <option value="Vendor:not_define" selected="selected">SF</option>
                                        </select>
                    </div>
                    <div class="form-group col-md-3">
                                         <label for="">Initial Booking Date</label>
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
                                            <input  value="{{x.TAT_2}} ({{x.TAT_2_per}}%)" ng-if="x.TAT_2 <= 0" class="btn btn-success" style="width: 68px;">
                                             </form></td>
                                             <td><form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank" style="width: 8%;">
                                            <input type="hidden" name="booking_id_status" value=" {{x.TAT_3_bookings}}">
                                            <input type="submit" value="{{x.TAT_3}} ({{x.TAT_3_per}}%)" ng-if="x.TAT_3 > 0" class="btn btn-danger">
                                            <input  value="{{x.TAT_3}} ({{x.TAT_3_per}}%)" ng-if="x.TAT_3 <= 0" class="btn btn-success" style="width: 68px;">
                                             </form></td>
                                             <td><form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank" style="width: 8%;">
                                            <input type="hidden" name="booking_id_status" value=" {{x.TAT_4_bookings}}">
                                            <input type="submit" value="{{x.TAT_4}} ({{x.TAT_4_per}}%)" ng-if="x.TAT_4 > 0" class="btn btn-danger">
                                            <input value="{{x.TAT_4}} ({{x.TAT_4_per}}%)" ng-if="x.TAT_4 <= 0" class="btn btn-success" style="width: 68px;">
                                             </form></td>
                                             <td><form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank" style="width: 8%;">
                                            <input type="hidden" name="booking_id_status" value=" {{x.TAT_5_bookings}}">
                                            <input type="submit" value="{{x.TAT_5}} ({{x.TAT_5_per}}%)" ng-if="x.TAT_5 > 0" class="btn btn-danger">
                                            <input  value="{{x.TAT_5}} ({{x.TAT_5_per}}%)" ng-if="x.TAT_5 <= 0" class="btn btn-success" style="width: 68px;">
                                             </form></td>
                                             <td><form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank" style="width: 8%;">
                                            <input type="hidden" name="booking_id_status" value=" {{x.TAT_8_bookings}}">
                                            <input type="submit" value="{{x.TAT_8}} ({{x.TAT_8_per}}%)" ng-if="x.TAT_8 > 0" class="btn btn-danger">
                                            <input  value="{{x.TAT_8}} ({{x.TAT_8_per}}%)" ng-if="x.TAT_8 <= 0" class="btn btn-success" style="width: 68px;">
                                             </form></td>
                                             <td><form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank" style="width: 8%;">
                                            <input type="hidden" name="booking_id_status" value=" {{x.TAT_16_bookings}}">
                                            <input type="submit" value="{{x.TAT_16}} ({{x.TAT_16_per}}%)" ng-if="x.TAT_16 > 0" class="btn btn-danger">
                                            <input value="{{x.TAT_16}} ({{x.TAT_16_per}}%)" ng-if="x.TAT_16 <= 0" class="btn btn-success" style="width: 68px;">
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
    </div>  
    <?php } ?>
    <!-- Booking Report End-->
    <!-- Missing Pincode Section -->
    <div class="row">
        <?php
            if($this->session->userdata("wrong_pincode_msg")){
                echo "<p style='color: green;text-align: center;font-size: 18px;'>".$this->session->userdata("wrong_pincode_msg")."</p>";
                if($this->session->userdata('wrong_pincode_msg')){$this->session->unset_userdata('wrong_pincode_msg');}
            }
            ?>
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0px !important;">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Pincode Call Missed Report</h2>
                    <span class="collape_icon" href="#pincode_table_data_div" data-toggle="collapse" onclick="get_missing_pincodes()"><i class="fa fa-plus-square" aria-hidden="true"></i></span>
                    <?php if(isset($saas_flag) && (!$saas_flag)) { ?>
                    <a id="download_pin_code" class="btn btn-success btn-xs" href="<?php echo base_url(); ?>employee/vendor/insert_pincode_form" style="float:right">Add New Pincode</a>
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
    <!-- get rm missing pincode ajax request-->
    <!-- get Am Total Booking Call data-->
     <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0px !important;">
            <div class="x_panel">
                <div class="x_title">
                    <h2>AM Call Load Report</h2>
                    <span class="collape_icon" href="#am_booking_report" data-toggle="collapse" onclick="get_am_booking_data()"><i class="fa fa-plus-square" aria-hidden="true"></i></span>
                   
                    <div class="clearfix"></div>
                </div>
                
                <div class="x_content collapse" id="am_booking_report">
                       <div class="table-responsive" id="am_booking_report_data">
                        <?php
                        if(!empty($am_data))
                        {
                            ?>
                        
                                
                                    <div class="col-md-12" id="am">
                                        <div class="item form-group">
                                            <div class="col-md-4 col-sm-4 col-xs-4" style="padding-left: 0px;padding-bottom:5px">
                                                <label for=""> Select AM For Comparison</label>
                                                <select class="form-control filter_table" id="am_id" name="am_id[]" multiple>
                                                    <?php foreach($am_data as $val){ ?>
                                                    <option value="<?php echo $val['id']?>"><?php echo $val['full_name']?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6 col-sm-6 col-xs-6">
                                                <button class="btn btn-primary"  style="margin-bottom:10px;background: #405467;border-color: #405467;" id="process">Process</button>
                                            </div>
                                        </div>
                                    </div>
                           <div id="amcompair">
                            
                           </div>
                                                             
                           <?php
                           }
                        ?>
                           <div>
                           <center><img id="compair_am_booking_loader" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center>
                           </div>
                           <div id="am_report">
                                <center><img id="missing_am_booking_loader" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center>
                           </div>
                    </div>
                </div>
              
            </div>
        </div>
    </div>
    <!-- get Am Total Booking Call data-->
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
    
<!--     SF Brackets snapshot Section 
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <div class="col-md-6">
                        <h2>Service Center Brackets Snapshot</h2>
                    </div>
                    <div class="col-md-6">
                        <div class="pull-right">
                            <a class="btn btn-sm btn-success" href="<?php //echo base_url();?>employee/dashboard/brackets_snapshot_full_view" target="_blank">Show All</a>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="table-responsive">
                        <div class="table-responsive" id="escalation_data" ng-controller="bracketsSnapshot_Controller" ng-cloak="">
                            <center><img id="brackets_loader" src="<?php //echo base_url(); ?>images/loadring.gif"></center>
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
                                           <td><a class="btn btn-sm btn-success" href="<?php //echo base_url();?>employee/inventory/get_bracket_add_form/{{x.sf_id}}/{{x.sf_name}}" target="_blank">Order brackets</a></td>
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
     SF Brackets Snapshot Section -->
    <?php if(isset($saas_flag) && (!$saas_flag)) { ?>
     <div class="row">
        <!-- Partner Spare Parts Details -->
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0px !important;">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Partner Spare Parts Details <span class="badge badge-info" data-toggle="popover" data-content="Below table shows parts which are OOT with respect to sf (after 7 days from booking completion by sf)"><i class="fa fa-info"></i></span></h2>
                    <span class="collape_icon" href="#spare_details_by_partner_div" data-toggle="collapse" onclick="spare_details_by_partner();"><i class="fa fa-plus-square" aria-hidden="true"></i></span>
                    <div class="clearfix"></div>
                </div>
                <div class="col-md-12">
                    <center><img id="loader_gif_spare_part_by_partner" src="<?php echo base_url(); ?>images/loadring.gif" style="display:none"></center>
                </div>
                <div class="x_content collapse" id="spare_details_by_partner_div">
                    <div id="spare_details_by_partner"></div>
                </div>
            </div>
        </div>
        <!-- End  Partner Spare Parts Details -->
    </div>
    <?php } ?>
    
    <!-- Escalation Start-->
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0px !important;">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Escalation</h2>
                    <span class="collape_icon" href="#escalation_data_div" data-toggle="collapse" onclick="initiate_escalation_data()"><i class="fa fa-plus-square" aria-hidden="true"></i></span>
                    <div class="clearfix"></div>
                </div>

                <div class="x_content collapse" id="escalation_data_div">

                    <div class="table-responsive" id="escalation_data" ng-controller="admin_escalationController" ng-cloak="">
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
    </div>
    <!-- Escalation End-->
 
    
    <div class="row" style="margin-top:10px;">
        <!-- Company Monthly Status -->
        <div class="col-md-6 col-sm-12 col-xs-12" style="padding : 0px !important;">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Monthly Booking Status <small>Completed</small></h2>
                    <span class="collape_icon" href="#monthly_booking_chart_div" data-toggle="collapse" onclick="around_monthly_data();"><i class="fa fa-plus-square" aria-hidden="true"></i></span>
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
        <div class="col-md-6 col-sm-12 col-xs-12" id="based_on_Region" style="padding-right:0px !important">
            <div class="x_panel">
                <div class="x_title">
                    <div class="col-md-5"><h2>Booking based on Region <small></small></h2></div>
                    <div class="col-md-6">
                        <small>
                        <div class="nav navbar-right panel_toolbox">
                            <div id="reportrange2" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; margin-right: -10%;">
                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                <span></span> <b class="caret"></b>
                            </div>
                        </div>
                        </small>
                    </div>
                    <div class="col-md-1" style="padding-right: 0px;"><span class="collape_icon" href="#state_type_booking_chart_div" data-toggle="collapse" onclick="get_bookings_data_by_rm()"><i class="fa fa-plus-square" aria-hidden="true"></i></span></div>
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
    
    <?php if(isset($saas_flag) && (!$saas_flag)) { ?>
    
    
     <!-- Closure Team Graph -->
    <div class="row" style="margin-top:10px;">
        <div class="col-md-6 col-sm-12 col-xs-12" id="completed_booking_closure_status" style="padding-right:0px !important; padding-left: 0px !important;">
            <div class="x_panel">
                <div class="x_title">
                    <div class="col-md-6" style="padding: 0px;"><h2>Review Completed Booking Status <button type="button"class="btn btn-default" style="margin-bottom: 10px;padding: 1px 4px;margin-top: 0px;font-size: 8px;margin-left: 5px;background: #f7a35c;
    color: #fff;border: none;" data-toggle="tooltip"data-placement="right"title="Total Bookings = Rejected Bookings + Direct Approved Bookings + Approved with Edit Bookings">?</button></h2></div>
                    <div class="col-md-5">
                        <small>
                        <div class="nav navbar-right panel_toolbox">
                            <div id="reportrange5" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; margin-right: -10%;">
                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                <span></span> <b class="caret"></b>
                            </div>
                        </div>
                        </small>
                    </div>
                    <div class="col-md-1" style="padding-right: 0px;"><span class="collape_icon" href="#completed_booking_closure_chart_div" data-toggle="collapse" onclick="get_completed_bookings_data()"><i class="fa fa-plus-square" aria-hidden="true"></i></span></div>
                    <div class="clearfix"></div>
                </div>
                <div class="col-md-12">
                    <center><img id="loader_gif7" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center>
                </div>
                <div class="x_content collapse" id="completed_booking_closure_chart_div">
                    <div id="completed_booking_closure_chart"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-12 col-xs-12" id="cancelled_booking_closure_status" style="padding-right:0px !important;">
            <div class="x_panel">
                <div class="x_title">
                    <div class="col-md-6" style="padding: 0px;"><h2>Review Cancelled Booking Status <button type="button"class="btn btn-default" style="margin-bottom: 10px;padding: 1px 4px;margin-top: 0px;font-size: 8px;margin-left: 5px;background: #f7a35c;
    color: #fff;border: none;" data-toggle="tooltip"data-placement="right"title="Total Bookings = Rejected Bookings + Direct Approved Bookings + Cancelled to Completed Bookings">?</button></h2></div>
                    <div class="col-md-5">
                        <small>
                        <div class="nav navbar-right panel_toolbox">
                            <div id="reportrange6" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; margin-right: -10%;">
                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                <span></span> <b class="caret"></b>
                            </div>
                        </div>
                        </small>
                    </div>
                    <div class="col-md-1" style="padding-right: 0px;"><span class="collape_icon" href="#cancelled_booking_closure_chart_div" data-toggle="collapse" onclick="get_cancelled_bookings_data()"><i class="fa fa-plus-square" aria-hidden="true"></i></span></div>
                    <div class="clearfix"></div>
                </div>
                <div class="col-md-12">
                    <center><img id="loader_gif8" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center>
                </div>
                <div class="x_content collapse" id="cancelled_booking_closure_chart_div">
                    <div id="cancelled_booking_closure_chart"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Closure Team Graph -->
    
    <!-- Logged In Users -->
    <!-- <div class="row" style="margin-top:10px;">
        <div class="col-md-6 col-sm-12 col-xs-12" id="div_loggedin" style="padding : 0px !important;">
            <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0px !important;">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Logged In Users</h2>
                        <span class="collape_icon" href="#loggedin_table_data_div" id='spn_loggedin_table' data-toggle="collapse" onclick="get_loggedin_users(this.id)"><i class="fa fa-plus-square" aria-hidden="true"></i></span>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content collapse" id="loggedin_table_data_div">
                        <center><img id="loggedin_loader" src="<?php echo base_url(); ?>images/loadring.gif"></center>
                        <div class="table-responsive" id="loggedin_table_data">
                            <i class="fa fa-refresh" aria-hidden="true" id='refresh_icon' onclick='get_loggedin_users(this.id)' style="float:right;cursor:pointer;"></i><br>
                            <table class="table table-striped table-bordered jambo_table bulk_action" border="1" style="text-align:center;">
                                <thead>
                                    <tr>
                                        <th style="text-align:center;">Vendor</th>
                                        <th style="text-align:center;">Partner</th>
                                        <th style="text-align:center;">Admin</th>
                                    </tr>
                                </thead>
                                <tbody id='tbody_loggedin_user'>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> -->
    <!-- Logged In Users -->
    <!-- Agent Graph -->
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0px !important;">
            <div class="dashboard_graph">
                <div class="row x_title">
                    <div class="col-md-6">
                        <h3>Agent Booking Status &nbsp;&nbsp;&nbsp;
                            <small>
                            </small>
                        </h3>
                    </div>
                    <div class="col-md-5">
                        <div id="reportrange3" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; margin-right: -12%;">
                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                            <span></span> <b class="caret"></b>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <span class="collape_icon" href="#chart_container2_div" data-toggle="collapse" onclick="agent_daily_report_call()" style="margin-right: 8px;"><i class="fa fa-plus-square" aria-hidden="true"></i></span>
                    </div>
                </div>
                <div class="x_content collapse" id="chart_container2_div">
                    <div class="col-md-12">
                        <center><img id="loader_gif4" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center>
                    </div>
                    <div id="chart_container2" class="chart_containe2" style="width:100%; height:400px;"></div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <!-- Agent Graph -->
    
   <!-- show more content -->
    <div class="row"  style="margin-top:20px;">
        <div id="show_more" style="display:none;"> 
            <div class="col-md-6 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Partner Unit Details</h2>
                        <div class="nav navbar-right panel_toolbox">
                            <div id="reportrange4" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc">
                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                <span></span> <b class="caret"></b>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="col-md-12">
                        <center><img id="loader_gif5" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center>
                    </div>
                    <div class="x_content">
                        <div id="unit_chart"></div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Monthly Booking Unit Status <small>Completed</small></h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="col-md-12">
                        <center><img id="loader_gif6" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center>
                    </div>
                    <div class="x_content">
                        <div id="monthly_unit_booking_chart"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end show more content -->
    
    <!-- show more button -->
    <div class="row">
        <center>
            <div class="btn btn-success" id="show_more_btn" style="margin: 20px;">Show More</div>
        </center>
    </div>
    <!-- End show more button -->
    <?php } ?>
    <!-- Modal -->
    <div id="modalDiv" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div id="open_model"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal -->
</div>
<!-- /page content -->
<!-- Chart Script -->
<script>
    $('#request_type').select2();
    $('#request_type_am').select2();
    $('#request_type_rm_pending').select2();
    $('#request_type_am_pending').select2();

    $('#pending_dependency').select2();
    $('#pending_dependency_am').select2();
    $('#upcountry_rm_pending').select2();
    $('#upcountry_am_pending').select2();

     $('#am_id').select2();

    
    $('#process').click(function(){
        var am_id=[];
        var result=1;
           var am_id=$('#am_id').val();
           var length=am_id.length;
//           
//           if((length<2)||(length>4))
//           {
//             alert("Please Select Min 2 and  Max 4 AM For comparision");
//             result=0;  
//             return false;
//           }
//           if(result==1)
//           {
//               var confirm_var=confirm("Do you want to compare more AM?");
//                 if(confirm_var==false)
//               {
                        var data = {};
                        url = '<?php echo base_url(); ?>employee/dashboard/get_am_drop_data';
                        data['am_id'] = am_id;
                        $('#compair_am_booking_loader').fadeIn();
                        sendAjaxRequest(data,url,post_request).done(function(response){
                         $('#compair_am_booking_loader').hide();  
                        $("#amcompair").html(response);
                        });
//                }
//           }
        });
     function getcompairamdata()
    {
        data = {};
        //var amdata=$("#am_partner").val();
        var amdata=$('#rm_compair_booking_form').serialize();
        url = '<?php echo base_url();?>employee/dashboard/compair_am_booking_data';
        data['amdata'] = amdata;
        $('#compair_am_booking_loader').fadeIn();
        sendAjaxRequest(data,url,post_request).done(function(response){
           $('#compair_am_booking_loader').hide();
           $("#am_report").html(response);
        });
    }

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
    var partner_name = [];
    var completed_booking = [];
    var partner_id = [];
    var change_chart_data = [];
    var partner_name = [];
    var completed_booking = [];
    var partner_id = [];
    var change_chart_data = [];
    var start = moment().startOf('month');
    var end = moment().endOf('month');
    var start_week = moment().subtract(6, 'days');
    var end_week = moment();
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
    
     var options_week = {
            startDate: start_week,
            endDate: end_week,
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
    
    $(function () {
        function cb(start, end) {
            $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }
        $('#reportrange').daterangepicker(options, cb);
        cb(start, end);
    });
    
    $(function () {
        function cb(start, end) {
            $('#reportrange2 span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }

        $('#reportrange2').daterangepicker(options, cb);

        cb(start, end);

    });
    
    $(function () {
        function cb(start, end) {
            $('#reportrange3 span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }
    
        $('#reportrange3').daterangepicker(options, cb);
    
        cb(start, end);
    
    });
    
    $(function () {
        function cb(start, end) {
            $('#reportrange4 span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }

        $('#reportrange4').daterangepicker(options, cb);

        cb(start, end);
    });
    
    $(function () {
        function cb(start_week, end_week) {
            $('#reportrange5 span').html(start_week.format('MMMM D, YYYY') + ' - ' + end_week.format('MMMM D, YYYY'));
        }

        $('#reportrange5').daterangepicker(options_week, cb);

        cb(start_week, end_week);
    });
    
    $(function () {
        function cb(start_week, end_week) {
            $('#reportrange6 span').html(start_week.format('MMMM D, YYYY') + ' - ' + end_week.format('MMMM D, YYYY'));
        }

        $('#reportrange6').daterangepicker(options_week, cb);

        cb(start_week, end_week);
    });
    
    $('#reportrange').on('apply.daterangepicker', function (ev, picker) {
        var startDate = picker.startDate.format('YYYY-MM-DD');
        var endDate = picker.endDate.format('YYYY-MM-DD');
        partner_booking_status(startDate,endDate);
       
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
    
    $('#reportrange3').on('apply.daterangepicker', function (ev, picker) {
        var startDate = picker.startDate.format('YYYY-MM-DD');
        var endDate = picker.endDate.format('YYYY-MM-DD');
        agent_daily_report(startDate,endDate);
    });
    
    $('#reportrange4').on('apply.daterangepicker', function (ev, picker) {
        var startDate = picker.startDate.format('YYYY-MM-DD');
        var endDate = picker.endDate.format('YYYY-MM-DD');
        partner_unit_chart(startDate, endDate);
    });
    
     $('#reportrange5').on('apply.daterangepicker', function (ev, picker) {
        $('#loader_gif7').show();
        $('#completed_booking_closure_chart').hide();
        var startDate = picker.startDate.format('YYYY-MM-DD');
        var endDate = picker.endDate.format('YYYY-MM-DD');
        url = baseUrl + '/employee/dashboard/get_completed_cancelled_booking_by_closure/Completed';
        var data = {sDate: startDate, eDate: endDate};
        
        sendAjaxRequest(data,url,post_request).done(function(response){
            if(response){
                create_chart_closure_completed_booking(response);
            }
            else{
                alert("Graph Data Not Found");
                $("#completed_booking_closure_status").find(".collape_icon").click();
            }
        });
    });
    
    $('#reportrange6').on('apply.daterangepicker', function (ev, picker) {
        $('#loader_gif8').show();
        $('#cancelled_booking_closure_chart').hide();
        var startDate = picker.startDate.format('YYYY-MM-DD');
        var endDate = picker.endDate.format('YYYY-MM-DD');
        url = baseUrl + '/employee/dashboard/get_completed_cancelled_booking_by_closure/Cancelled';
        var data = {sDate: startDate, eDate: endDate};
        
        sendAjaxRequest(data,url,post_request).done(function(response){
            if(response){
                create_chart_closure_cancelled_booking(response);
            }
            else{
                alert("Graph Data Not Found");
                $("#cancelled_booking_closure_status").find(".collape_icon").click();
            }
        });
    });
    
    $(document).ready(function(){
        $('[data-toggle="popover"]').popover({
            placement : 'top',
            trigger : 'hover'
        });
        
        //top count data
        get_query_data();
        //missing pincode data
        //get_missing_pincodes();
        //partner booking data
        
        //company monthly data
        // around_monthly_data();
        //Rm wise bookings data
        //get_bookings_data_by_rm();
        //agent performance data
        //agent_daily_report(start.format('MMMM D, YYYY'), end.format('MMMM D, YYYY'));
        
        //partner spare status
        //spare_details_by_partner();
        initiate_RM_TAT_Reporting();        
    });
    
    function initiate_AM_TAT_Reporting(){
        // $('input[name="daterange_completed_bookings"]').daterangepicker({
        $('input[id="completed_daterange_id_am"]').daterangepicker({
            timePicker: true,
            timePickerIncrement: 30,
            locale: {
                format: 'YYYY-MM-DD'
            },
            startDate:  "<?php echo date("Y-m-d", strtotime("-1 month")); ?>"
        });
    }
    
    function initiate_RM_TAT_Reporting(){
        // $('input[name="daterange_completed_bookings"]').daterangepicker({
        $('input[id="completed_daterange_id"]').daterangepicker({
            timePicker: true,
            timePickerIncrement: 30,
            locale: {
                format: 'YYYY-MM-DD'
            },
            startDate: "<?php echo date("Y-m-d", strtotime("-1 month")); ?>"
        });
    }
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
    }
    function initiate_AM_Pending_TAT_Reporting(){
        // $('input[name="daterange_completed_bookings"]').daterangepicker({
        $('input[id="pending_daterange_id_am"]').daterangepicker({
            timePicker: true,
            timePickerIncrement: 30,
            locale: {
                format: 'YYYY-MM-DD'
            },
            startDate: "<?php echo date("Y-m-d", strtotime("-12 month")); ?>",
        });
    }
    
    function initiate_partner_chart_reporting(){
        partner_booking_status(start.format('MMMM D, YYYY'), end.format('MMMM D, YYYY'));
    }
   
    function agent_daily_report_call(){ 
        agent_daily_report(start.format('MMMM D, YYYY'), end.format('MMMM D, YYYY'));
    }
    
    
    //show next grapgh when show more button clicked
    $("#show_more_btn").click(function(){
       $('#show_more').fadeIn();
       //partner wise monthly booking unit data
       partner_unit_chart(start.format('MMMM D, YYYY'), end.format('MMMM D, YYYY'));
       //partner wise monthly booking unit data
       around_monthly_unit_data();
       $('#show_more_btn').hide();
    });
    
    function sendAjaxRequest(postData, url,type) {
        return $.ajax({
            data: postData,
            url: url,
            type: type
        });
    }
    
    
    function get_query_data(){
        $('#loader_gif_title').fadeIn();
        var data = {};
        url = '<?php echo base_url(); ?>employee/dashboard/execute_title_query';
        
        sendAjaxRequest(data,url,post_request).done(function(response){
            $('#loader_gif_title').hide();
            $('#title_count').html(response);
        });
    }
    
    function get_missing_pincodes(){
        var data = {};
        url = '<?php echo base_url(); ?>employee/dashboard/get_pincode_not_found_sf_details_admin';
        data['partner_id'] = '';
        
        sendAjaxRequest(data,url,post_request).done(function(response){
            $("#pincode_table_data").html(response);
        });
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
    
    function get_am_booking_data(){
        
        var data = {};
        url = '<?php echo base_url(); ?>employee/dashboard/get_am_booking_data';
        data['partner_id'] = '';
        $('#missing_am_booking_loader').fadeIn();
        sendAjaxRequest(data,url,post_request).done(function(response){
         $('#missing_am_booking_loader').hide();
        $("#am_report").html(response);
        });
    }
    
    function partner_booking_status(startDate,endDate){ 
        $('#loader_gif1').fadeIn();
        $('#chart_container').fadeOut();
        var data = {sDate: startDate, eDate: endDate};
        url =  '<?php echo base_url(); ?>employee/dashboard/get_partners_booking_report_chart';
        
        sendAjaxRequest(data,url,post_request).done(function(response){
            create_partner_booking_chart(response);
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
    
    function around_monthly_unit_data(){
        
        $('#loader_gif6').fadeIn();
        $('#monthly_unit_booking_chart').fadeOut();
        var data = {partner_id:''};
        url =  '<?php echo base_url(); ?>employee/dashboard/get_bookings_unit_data_by_month';
        
        sendAjaxRequest(data,url,post_request).done(function(response){
            get_mothly_booking_status(response,'2');
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
    
    function get_loggedin_users(id) {
        if($('#'+id).children('i').attr('class') != 'fa fa-minus-square') {
            var data = {};
            url =  '<?php echo base_url(); ?>employee/dashboard/get_loggedin_users';
            $("#loggedin_table_data").hide();
            $('#loggedin_loader').fadeIn();
            sendAjaxRequest(data,url,post_request).done(function(response){
                response = JSON.parse(response);

                if(response['msg'] === 'failed')
                {
                    alert(response['data']);
                }
                var str = "<td><b>"+response['data']['service_center']+"</b></td><td><b>"+response['data']['partner']+"</b></td><td><b>"+response['data']['employee']+"</b></td>";

                $('#loggedin_loader').hide();
                $("#tbody_loggedin_user").html(str);
                $("#loggedin_table_data").show();
            });
        }
    }
    
    function create_partner_booking_chart(response){
        var data = JSON.parse(response);
                var partners_id = data.partner_id;
                var partners = data.partner_name.split(',');
                var completed_bookings_count = JSON.parse("[" + data.completed_bookings_count + "]");
                var cancelled_bookings_count = JSON.parse("[" + data.cancelled_bookings_count + "]");
                $('#loader_gif1').hide();
                $('#chart_container').fadeIn();
                partner_booking_chart = new Highcharts.Chart({
                                    chart: {
                                        renderTo: 'chart_container',
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
                                        categories: partners,
                                        labels: {
                                            style: {
                                                fontSize:'13px'
                                            }
                                        }
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
                                    legend: {
                                        layout: 'vertical',
                                        align: 'right',
                                        verticalAlign: 'middle',
                                        borderWidth: 0
                                    },
                                    series: [
                                        {
                                            name: 'Completed Bookings',
                                            data: completed_bookings_count,
                                            cursor: 'pointer',
                                            point: {
                                                events: {
                                                    click: function (event) {
                                                        var get_date = $('#reportrange span').text().split('-');
                                                        var startdate = Date.parse(get_date[0]).toString('dd-MMM-yyyy');
                                                        var enddate = Date.parse(get_date[1]).toString('dd-MMM-yyyy');
                                                        window.open(baseUrl + '/employee/dashboard/partner_reports/' + this.category + '/' + partners_id[this.category] + '/' + 'Completed' + '/' + encodeURI(startdate) + '/' + encodeURI(enddate), '_blank');
    
                                                    }
                                                }
                                            }
                                        }, {
                                            name: 'Cancelled Bookings',
                                            data: cancelled_bookings_count,
                                            cursor: 'pointer',
                                            point: {
                                                events: {
                                                    click: function (event) {
                                                        var get_date = $('#reportrange span').text().split('-');
                                                        var startdate = Date.parse(get_date[0]).toString('dd-MMM-yyyy');
                                                        var enddate = Date.parse(get_date[1]).toString('dd-MMM-yyyy');
                                                        window.open(baseUrl + '/employee/dashboard/partner_reports/' + this.category + '/' + partners_id[this.category] + '/' + 'Cancelled' + '/' + encodeURI(startdate) + '/' + encodeURI(enddate), '_blank');
    
                                                    }
                                                }
                                            }
                                        }]
                                });
    //                var partners = [];
    //                var bookings = [];
    //                var partnerid = [];
    //                var chart_ajax_data = [];
    //                $.each(JSON.parse(response), function (key, value) {
    //                    partners.push(value.public_name);
    //                    bookings.push(parseInt(value.count));
    //                    partnerid[value.public_name] = parseInt(value.partner_id);
    //                    var arr = {
    //                        name: value.public_name,
    //                        y: parseInt(value.count)
    //                    };
    //                    chart_ajax_data.push(arr);
    //
    //                });
    //                $('#loader_gif1').attr('src', "");
    //                $('#loader_gif1').css('display', 'none');
    //                $('#chart_container').show();
    //                partner_booking_chart = new Highcharts.Chart({
    //                    chart: {
    //                        renderTo: 'chart_container',
    //                        type: 'column'
    //                    },
    //                    title: {
    //                        text: '',
    //                        x: -20 //center
    //                    },
    //                    xAxis: {
    //                        categories: partners
    //                    },
    //                    yAxis: {
    //                        title: {
    //                            text: 'Count'
    //                        },
    //                        plotLines: [{
    //                                value: 0, width: 1,
    //                                color: '#808080'
    //                            }]
    //                    },
    //                    plotOptions: {
    //                        column: {
    //                            dataLabels: {
    //                                enabled: true,
    //                                crop: false,
    //                                overflow: 'none'
    //                            }
    //                        },
    //                        pie: {
    //                            plotBorderWidth: 0,
    //                            allowPointSelect: true,
    //                            cursor: 'pointer',
    //                            size: '100%',
    //                            dataLabels: {
    //                                enabled: true,
    //                                format: '{point.name}: <b>{point.y}</b>'
    //                            },
    //                            showInLegend: true
    //                        }
    //                    },
    //                    series: [
    //                        {
    //                            name: booking_status,
    //                            data: chart_ajax_data,
    //                            cursor: 'pointer',
    //                            point: {
    //                                events: {
    //                                    click: function (event) {
    //                                        var get_date = $('#reportrange span').text().split('-');
    //                                        var startdate = Date.parse(get_date[0]).toString('dd-MMM-yyyy');
    //                                        var enddate = Date.parse(get_date[1]).toString('dd-MMM-yyyy');
    //                                        var booking_status = $('#booking_status').val();
    //                                        window.open(baseUrl + '/employee/dashboard/partner_reports/' + this.name + '/' + partnerid[this.name] + '/' + booking_status + '/' + encodeURI(startdate) + '/' + encodeURI(enddate), '_blank');
    //                                    }
    //                                }
    //                            }
    //                        }]
    //                });
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
                    data: cancelled
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
    
    function agent_daily_report(startDate,endDate){
        var url =  '<?php echo base_url();?>BookingSummary/agent_working_details_ajax';
        $('#loader_gif4').fadeIn();
        $('#chart_container2').hide();
        $.ajax({
            type: 'POST',
            url: url,
            data: {sDate: startDate, eDate: endDate},
            success: function (response) {
                $('#loader_gif4').hide();
                $('#chart_container2').fadeIn();
                var data = JSON.parse(response);
                var agent_name = data.agent_name.split(',');
                var query_cancel = JSON.parse("[" + data.query_cancel + "]");
                var calls_placed = JSON.parse("[" + data.calls_placed + "]");
                var query_booking = JSON.parse("[" + data.query_booking + "]");
                var calls_received = JSON.parse("[" + data.calls_received + "]");
                var rating = JSON.parse("[" + data.rating + "]");
                chart1 = new Highcharts.Chart({
                    chart: {
                        renderTo: 'chart_container2',
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
                        categories: agent_name
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
                    legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'middle',
                        borderWidth: 0
                    },
                    series: [
                        {
                            name: 'Cancelled Queries',
                            data: query_cancel,
                        }, {
                            name: 'Booked Queries',
                            data: query_booking
                        },
                        {
                            name: 'Outgoing Calls',
                            data: calls_placed
                        }, {
                            name: 'Incoming Calls',
                            data: calls_received
                        }, {
                            name: 'Ratings',
                            data: rating
                        }]
                });
            }
        });
    
    }
    
    function partner_unit_chart(startDate,endDate){
        var url = '<?php echo base_url(); ?>employee/dashboard/get_count_unit_details';
        $('#loader_gif5').fadeIn();
        $('#unit_chart').hide();
        $.ajax({
            type: 'POST',
            url: url,
            data: {sDate: startDate, eDate: endDate},
            success: function (response) {
                $('#loader_gif5').hide();
                $('#unit_chart').fadeIn();
                var data = JSON.parse(response);

                 unit_chart = new Highcharts.Chart({
                    chart: {
                        renderTo: 'unit_chart',
                        type: 'column',
                        events: {
                            load: Highcharts.drawTable
                        },
                    },
                    title: {
                        text: '',
                        x: -20 //center
                    },
                    xAxis: {
                        categories: data['partner_category'],
                        labels: {
                            style: {
                                fontSize:'13px'
                            }
                        }
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
                            name: 'Completed',
                            data: data['data']
                        }]
                });
              }
        });
    }
    
    //this function is used to get the spare details for partner
    function spare_details_by_partner(){
        $('#loader_gif_spare_part_by_partner').show();
        url =  '<?php echo base_url(); ?>employee/dashboard/get_oot_spare_parts_count_by_partner';
        data = {};
        sendAjaxRequest(data,url,post_request).done(function(response){
            create_spare_parts_by_partner_chart(response);
        });
    }
    
    //this function is used to create chart for partner spare details
    function create_spare_parts_by_partner_chart(response){ 
        var data = JSON.parse(response);
        var partners_id = data.partner_id;
        var partners = data.partner_name.split(',');
        var spare_count = JSON.parse("[" + data.spare_count + "]");
        $('#loader_gif_spare_part_by_partner').hide();
        $('#spare_details_by_partner').fadeIn();
        partner_booking_chart = new Highcharts.Chart({
            chart: {
                renderTo: 'spare_details_by_partner',
            },
            title: {
                text: '',
                x: -20 //center
            },
            xAxis: {
                categories: partners,
            },
            plotOptions: {
                bar: {
                    dataLabels: {
                        formatter: function() {return this.y + ' / (Rs. ' + data.spare_amount[this.x] + ')'; },
                        enabled: true,
                        crop: false,
                        overflow: 'none'
                        }
                    }    
            },
            tooltip: {
                formatter: function() {
                    return this.x + '<br> Count: ' + this.y + '<br>' + ' Amount(Rs.): ' + data.spare_amount[this.x];
                }
            },
            legend: {
                enabled: false
            },
            series: [{
                type: 'bar',
                name: 'Count',
                data: spare_count,
                cursor: 'pointer',
                    point: {
                        events: {
                            click: function (event) {
                                window.open(baseUrl + '/employee/dashboard/partner_specific_spare_parts_dashboard/' + this.category + '/' + partners_id[this.category], '_blank');
                            }
                        }
                    }
            }]
        });
    }
    
    
</script>
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

function initiate_escalation_data(){
//    var d = new Date();
//    n = d.getMonth()+1;
//    y = d.getFullYear();
//    date = d.getDate();
    $('input[name="daterange"]').daterangepicker({
        timePicker: true,
        timePickerIncrement: 30,
        locale: {
            format: 'YYYY-MM-DD'
        },
       // startDate: y+'-'+n+'-01'
       startDate: "<?php echo date("Y-m-d", strtotime("first day of previous month")); ?>"
    });
}
    function open_full_view(id,url,is_am,is_pending,form_id){
      entity_id = id.split("_")[1];
      final_url = url+entity_id+'/0/'+is_am+'/'+is_pending;
      $('#'+form_id).attr('action', final_url);
      $('#'+form_id).submit();
    }
    
    function get_completed_bookings_data(){
        $('#loader_gif7').fadeIn();
        $('#completed_booking_closure_chart').fadeOut();
        var data = {};
        url =  '<?php echo base_url(); ?>employee/dashboard/get_completed_cancelled_booking_by_closure/Completed';
        
        sendAjaxRequest(data,url,post_request).done(function(response){
            if(response){
                create_chart_closure_completed_booking(response);   
            }
            else{
                alert("Graph Data Not Found");
                $("#completed_booking_closure_status").find(".collape_icon").click();
            }
        });
    }
    
    function create_chart_closure_completed_booking(response) {
        var data = JSON.parse(response);
        var closures = data.closures.split(',');
        var reject = JSON.parse("[" + data.reject + "]");
        var approved = JSON.parse("[" + data.approved + "]");
        var edit_complete = JSON.parse("[" + data.edit_complete + "]");
        var total_bookings = JSON.parse("[" + data.total_bookings + "]");
        $('#loader_gif7').hide();
        $('#completed_booking_closure_chart').fadeIn();
        rm_based_chart = new Highcharts.Chart({
            chart: {
                renderTo: 'completed_booking_closure_chart',
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
                categories: closures
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
                    name: 'Rejected Bookings',
                    data: reject
                }, {
                    name: 'Directly Approved Bookings',
                    data: approved
                },
                {
                    name: 'Approved With Edit Bookings',
                    data: edit_complete
                }, {
                    name: 'Total Bookings',
                    data: total_bookings
                }]
        });
    
    }
    
    function get_cancelled_bookings_data(){
        $('#loader_gif8').fadeIn();
        $('#cancelled_booking_closure_chart').fadeOut();
        var data = {};
        url =  '<?php echo base_url(); ?>employee/dashboard/get_completed_cancelled_booking_by_closure/Cancelled';
        
        sendAjaxRequest(data,url,post_request).done(function(response){
            //console.log(response);
            if(response){
                create_chart_closure_cancelled_booking(response);
            }
            else{
                alert("Graph Data Not Found");
                $("#cancelled_booking_closure_status").find(".collape_icon").click();
            }
        });
    }
    
    function create_chart_closure_cancelled_booking(response) {
        var data = JSON.parse(response);
        var closures = data.closures.split(',');
        var reject = JSON.parse("[" + data.reject + "]");
        var approved = JSON.parse("[" + data.approved + "]");
        var edit_complete = JSON.parse("[" + data.edit_complete + "]");
        var total_bookings = JSON.parse("[" + data.total_bookings + "]");
        $('#loader_gif8').hide();
        $('#cancelled_booking_closure_chart').fadeIn();
        rm_based_chart = new Highcharts.Chart({
            chart: {
                renderTo: 'cancelled_booking_closure_chart',
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
                categories: closures
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
                    name: 'Rejected Bookings',
                    data: reject
                }, {
                    name: 'Directly Approved Bookings',
                    data: approved
                },
                {
                    name: 'Cancelled to Completed Bookings',
                    data: edit_complete
                }, {
                    name: 'Total Bookings',
                    data: total_bookings
                }]
        });
    
    }
</script>
<style>
.text_warning{
        color:red;
    }
    [ng\:cloak], [ng-cloak], .ng-cloak {
  display: none !important;
}
select option:empty { display:none }
.select2-container--default{
        width: 154px !important;
}
.select2-selection--multiple{
        border: 1px solid #ccc !important;
    border-radius: 0px !important;
}
</style>
