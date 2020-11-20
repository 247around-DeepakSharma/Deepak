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
    tr[id^='arm_table_'],
    tr[id^='arm_open_call_table_']{
        background-color:#5997aa !important;
    }
    .sub-table{
        width:98%;
        margin:auto;
    }
    table.sub-table thead{
        background:#8cc6ab;
    }
    #sales_partner_div .select2-container--default{
        width:500px !important;
    }
    #invoice_datatable tbody th{
        text-align: right;
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
                <p ng-if='completedBookingByRM.leg_1 !== undefined'>
                    <?php echo LEG_DESCRIPTION ; ?>
                </p>
                <table class="table table-striped table-bordered jambo_table bulk_action">
                    <thead>
                        <tr>
                            <th>S.no</th>
                            <th>RM</th>
                            <th ng-if='completedBookingByRM.leg_1 !== undefined'></th>
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
                        <tr class="tat-report" data-rm-row-id="{{completedBookingByRM.TAT[$index].id}}" ng-repeat="x in completedBookingByRM.TAT | orderBy:'TAT_16'" ng-if='completedBookingByRM.leg_1 !== undefined'>
                            <td style="padding: 4px 12px;">{{$index+1}}</td>
                            <td style="padding: 4px 12px;"><button style="margin-top: 10px;" type="button" id="vendor_{{completedBookingByRM.TAT[$index].id}}" class="btn btn-info" target="_blank" 
                                       onclick="open_full_view(this.id,'<?php echo base_url(); ?>employee/dashboard/tat_calculation_full_view/','0','0','rm_completed_booking_form')">
                                   {{completedBookingByRM.TAT[$index].entity}} </button>
                                <span ng-if="completedBookingByRM.TAT[$index].id == '00'"></span>
                                <span ng-if="completedBookingByRM.TAT[$index].id != '00'">
                                    <span class="tat-report collape_icon toggle-arm-details" data-rm-id="{{completedBookingByRM.TAT[$index].id}}" onclick="get_arm_details_for_rm($(this).data('rm-id'))" style="margin-top: 10px;">
                                        <i class="fa fa-plus-square" aria-hidden="true"></i>
                                    </span>
                                </span>
                            </td>
                            <td>
                                <p style="float:right;margin-bottom: 0px;">leg_1<br>leg_2<br>Total</p>
                            </td>                                   
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
                        <tr class="tat-report" data-rm-row-id="{{x.id}}" ng-repeat="x in completedBookingByRM.TAT | orderBy:'TAT_16'" ng-if='completedBookingByRM.leg_1 == undefined'>
                           <td>{{$index+1}}</td>
                           <td><button type="button" id="vendor_{{x.id}}" class="btn btn-info" target="_blank" 
                                       onclick="open_full_view(this.id,'<?php echo base_url(); ?>employee/dashboard/tat_calculation_full_view/','0','0','rm_completed_booking_form')">{{x.entity}}</button>
                                <span ng-if="x.id == '00'"></span>
                                <span ng-if="x.id != '00'">
                                    <span class="tat-report collape_icon toggle-arm-details" data-rm-id="{{x.id}}" onclick="get_arm_details_for_rm($(this).data('rm-id'))">
                                        <i class="fa fa-plus-square" aria-hidden="true"></i>
                                    </span>
                                </span>
                            </td>
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
                                             <option value="247Around:Warehouse">Admin</option>
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
                        <tr class="open-call-report" data-rm-row-id="{{x.id}}" ng-repeat="x in pendingBookingByRM | orderBy:'TAT_16'" ng-if="x.entity !== 'Total'">
                           <td>{{$index+1}}</td>
                           <td><button type="button" id="vendor_{{x.id}}" class="btn btn-info" target="_blank" 
                                       onclick="open_full_view(this.id,'<?php echo base_url(); ?>employee/dashboard/tat_calculation_full_view/','0','Pending','rm_pending_booking_form')">{{x.entity}}</button>
                                 <span ng-if="x.id == '00'"></span>
                                 <span ng-if="x.id != '00'">
                                    <span class="open-call-report collape_icon toggle-arm-details" data-rm-id="{{x.id}}" onclick="get_arm_open_call_details_for_rm($(this).data('rm-id'))">
                                        <i class="fa fa-plus-square" aria-hidden="true"></i>
                                    </span>
                                </span>
                            </td>
                                       <td><form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank" style="width: 8%;">
                                            <input type="hidden" name="booking_id_status" value=" {{x.TAT_0_bookings}}">
                                            <input type="submit" value="{{x.TAT_0}} ({{x.TAT_0_per}}%)"  class="btn btn-success">
                                             </form>
                                             </td>
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
                           <td><form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank" style="width: 8%;">
                                            <input type="hidden" name="booking_id_status" value=" {{x.TAT_Total_bookings}}">
                              <input type="submit" value="{{x.Total_Pending}} ({{x.TAT_total_per}}%)" ng-if="x.Total_Pending > 0" class="btn btn-danger">
                                            <input type="submit" value="{{x.Total_Pending}} ({{x.TAT_total_per}}%)" ng-if="x.Total_Pending <= 0" class="btn btn-success">
                                             </form></td>
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
    
        <div class="row" style="display: none;">
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0px !important;">
            <div class="x_panel">
                <div class="x_title" style="padding-left: 0px;">
                    <h2>Brand Sales Report</h2>
                    <span class="collape_icon" href="#brand_sales_reporting" data-toggle="collapse" onclick=""><i class="fa fa-minus-square" aria-hidden="true"></i></span>
                    <div class="clearfix"></div>
                </div>
                <div id="brand_sales_reporting" class="collapse in">
                <div class="table-responsive" id="escalation_data">
                    <form action="" method="post" id="brand_sales_form" style="float: left;width: 1110px;">
                    <div class="col-md-3">
                        <div class="item form-group">
                            <div class="col-md-12 col-sm-12 col-xs-12" style="padding-left: 0px;">
                                <label for="">Year</label>
                                <select class="form-control filter_table" id="sales_year" name="sales_year">
                                    <option value="">Select Year</option>
                                    <?php $from_year = 2015;
                                            $to_year = date('Y');
                                    for($i=$from_year; $i<=$to_year;$i++){ ?>
                                    <option value="<?php echo $i?>"><?php echo $i; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                        <div class="col-md-6" id="sales_partner_div">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <label for="">Partner</label>
                            <select class="form-control filter_table" id="sales_partner" name="sales_partner[]" multiple="">
                                <?php foreach($partners as $val){ ?>
                                 <option value="<?php echo $val['id']?>"><?php echo $val['public_name']?></option>
                               
                                <?php } ?>
                            </select>
                        </div>
                </div>
                    </div>
                    
                  
               
                       
                   <div class="col-md-3">
                       <button type="button" id="btn_brand_sales" class="btn btn-primary" style="margin-top: 23px;background: #405467;border-color: #405467;">Apply Filters</button>
                   </div>
                         </form>
                    <br>
                <div class="clear"></div>
               
                <table id="brand_sales" class="table table-striped table-bordered jambo_table bulk_action" style="margin-top:30px;">
                    <thead>
                        <tr>
                            <th>Brand</th>
                            <th>January</th>
                            <th>February</th>
                            <th>March</th>
                            <th>April</th>
                            <th>May</th>
                            <th>June</th>
                            <th>July</th>
                            <th>August</th>
                            <th>September</th>
                            <th>October</th>
                            <th>November</th>
                            <th>December</th>
                            <th>Total</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
                <center><img id="loader_gif_brand_sales" src="<?php echo base_url(); ?>images/loadring.gif" ></center>
            </div>
                    
                    <div id="brand_sales_chart">
                        
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
                <p ng-if='completedBookingByAM.leg_1 !== undefined'>
                    <?php echo LEG_DESCRIPTION ; ?>
                </p>
                <table class="table table-striped table-bordered jambo_table bulk_action">
                    <thead>
                        <tr>
                            <th>S.no</th>
                            <th>AM</th>
                            <th ng-if='completedBookingByAM.leg_1 !== undefined'></th>
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
                        <tr class="am-tat-report" data-am-row-id="{{completedBookingByAM.TAT[$index].id}}" ng-repeat="x in completedBookingByAM.TAT | orderBy:'TAT_16'" ng-if='completedBookingByAM.leg_1 !== undefined'>
                            <td style="padding: 4px 12px;">{{$index+1}}</td>
                            <td style="padding: 4px 12px;"><button style="margin-top: 10px;" type="button" id="vendor_{{completedBookingByAM.TAT[$index].id}}" class="btn btn-info" target="_blank" 
                                       onclick="open_full_view(this.id,'<?php echo base_url(); ?>employee/dashboard/tat_calculation_full_view/','1','0','am_completed_booking_form')">
                                   {{completedBookingByAM.leg_1[$index].entity}} </button>
                                <span ng-if="completedBookingByAM.TAT[$index].id != '00'">
                                    <span class="am-tat-report collape_icon toggle-brand-details" data-am-id="{{completedBookingByAM.TAT[$index].id}}" onclick="get_brand_details_for_am($(this).data('am-id'))" style="margin-top: 10px;">
                                        <i class="fa fa-plus-square" aria-hidden="true"></i>
                                    </span>
                                </span>
                            </td>
                            <td><p style="float:right;margin-bottom: 0px;">leg_1<br>leg_2<br>Total</p></td>                                   
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
                        <tr class="am-tat-report" data-am-row-id="{{x.id}}" ng-repeat="x in completedBookingByAM.TAT | orderBy:'TAT_16'" ng-if='completedBookingByAM.leg_1 == undefined'>
                           <td>{{$index+1}}</td>
                           <td><button type="button" id="vendor_{{x.id}}" class="btn btn-info" target="_blank" 
                                       onclick="open_full_view(this.id,'<?php echo base_url(); ?>employee/dashboard/tat_calculation_full_view/','1','0','am_completed_booking_form')">{{x.entity}}</button>
                                <span ng-if="x.id == '00'"></span>
                                <span ng-if="x.id != '00'">
                                    <span class="am-tat-report collape_icon toggle-brand-details" data-am-id="{{x.id}}" onclick="get_brand_details_for_am($(this).data('am-id'))">
                                        <i class="fa fa-plus-square" aria-hidden="true"></i>
                                    </span>
                                </span>
                           </td>
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
                                            <option value="247Around:Warehouse">247Around</option>
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
                        <tr class="open-call-report-am" data-am-row-id="{{x.id}}" ng-repeat="x in pendingBookingByAM | orderBy:'TAT_16'" ng-if="x.entity !== 'Total'">
                           <td>{{$index+1}}</td>
                           <td><button type="button" id="vendor_{{x.id}}" class="btn btn-info" target="_blank" 
                                       onclick="open_full_view(this.id,'<?php echo base_url(); ?>employee/dashboard/tat_calculation_full_view/','1','pending','am_pending_booking_form')">{{x.entity}}</button>
				<span ng-if="x.id == '00'"></span>
				<span ng-if="x.id != '00'">
                                    <span class="open-call-report-am collape_icon toggle-brand-details" data-am-id="{{x.id}}" onclick="get_brand_open_call_details_for_am($(this).data('am-id'))">
                                        <i class="fa fa-plus-square" aria-hidden="true"></i>
                                    </span>
                                </span>
			   </td>
                                       <td><form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank" style="width: 8%;">
                                            <input type="hidden" name="booking_id_status" value=" {{x.TAT_0_bookings}}">
                                            <input type="submit" value="{{x.TAT_0}} ({{x.TAT_0_per}}%)"  class="btn btn-success">
                                             </form>
					</td>
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
                        <input type="text" id="session_id_holder" style="display:none;" value="<?php if($this->session->userdata('user_group') == _247AROUND_RM || $this->session->userdata('user_group') == _247AROUND_ASM){
            echo $this->session->userdata('id');
        } ?>">
                        <button type="button"class="btn btn-default" style="float:right" data-toggle="tooltip"data-placement="left"title="To calculate escalation percentage, logic use current months booking and current month escalation ">?</button>
                        <button type="button" class="btn btn-info" ng-click="mytoggle = !mytoggle" id="order_by_toggal" onclick="change_toggal_text()"style="float:right">Sort By Number Of Escalation</button>
                        <form class="form-inline"style="float:left;background: #46b8da;color: #fff;padding: 3px;border-radius: 4px;">
                            <input type='hidden' name='esDate' id='esDate' value="<?php echo date("Y-m-d", strtotime("first day of previous month")); ?>">
                            <input type='hidden' name='eeDate' id='eeDate' value="<?php echo date('Y-m-d'); ?>">
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
                                    <td><a type="button" id="rm_{{y.rm_id}}" class="btn btn-info" target="_blank" href="<?php echo base_url(); ?>employee/dashboard/escalation_full_view/{{y.rm_id}}/{{y.startDate}}/{{y.endDate}}">{{y.rm_name}}</a></td>
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
    <?php
        $saas_flag = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        if($saas_flag) { 
    ?>
    <!-- Not assigned booking report -->
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0px !important;">
             <div class="x_panel">
                <div class="x_title" style="padding-left: 0px;">
                    <h2>Non Assigned Bookings</h2>
                    <span class="collape_icon" href="#Unassigned_Booking_Reporting" data-toggle="collapse" onclick="initiate_non_assigned_booking_Reporting()" ><i class="fa fa-plus-square" aria-hidden="true"></i></span>
                    <div class="clearfix"></div>
                </div>
                <div id="Unassigned_Booking_Reporting" class="collapse">
                    <table class="table table-striped table-bordered jambo_table bulk_action">
                        <thead>
                            <tr>
                                <th>S.no</th>
                                <th>RM</th>
                                <th>Total Booking</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        </table>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
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
        
        <!-- Booking cancellation -->
        <div class="row" style="margin-top:10px;">
        <div class="col-md-12 col-sm-12 col-xs-12" id="based_on_booking_cancellation_reason" style="padding-right:0px !important">
            <div class="x_panel">
                <div class="x_title">
                    <div class="col-md-5"><h2>Booking cancellation reason wise <small></small></h2></div>
                    <div class="col-md-6">
                        <small>
                            <div class="nav navbar-right panel_toolbox">
                                <div id="reportrange_booking_cancellation" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; margin-right: -10%;">
                                    <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                    <span></span> <b class="caret"></b>
                                </div>
                            </div>
                        </small>
                    </div>
                    <div class="col-md-1" style="padding-right: 0px;"><span class="collape_icon" href="#booking_cancellation_chart_div" data-toggle="collapse" onclick="get_bookings_cancellation_reason()"><i class="fa fa-plus-square" aria-hidden="true"></i></span></div>
                    <div class="clearfix"></div>
                </div>
                <div class="col-md-12">
                        <center><img id="loader_gif_booking_cancellation" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center>
                </div>
                <div class="x_content collapse" id="booking_cancellation_chart_div">
                    <div id="booking_cancellation_chart"></div>
                </div>
            </div>
        </div>
        </div>
        <!-- Booking cancellation -->
    </div>
    
    <?php if(isset($saas_flag) && (!$saas_flag)) { ?>
    
    
     <!-- Closure Team Graph -->
    <div class="row" style="margin-top:10px;">
        <div class="col-md-6 col-sm-12 col-xs-12" id="completed_booking_closure_status" style="padding-right:0px !important; padding-left: 0px !important;">
            <form action="<?php echo base_url(); ?>employee/dashboard/get_completed_cancelled_booking_by_closure/Completed/excel" method="post">            
            <input type="hidden" name="sDate" id="sDate5" value="">
            <input type="hidden" name="eDate" id="eDate5" value="">
            <div class="x_panel">
                <div class="x_title">
                    <div class="col-md-6" style="padding: 0px;"><h2>Review Completed Booking Status <button type="button"class="btn btn-default" style="margin-bottom: 10px;padding: 1px 4px;margin-top: 0px;font-size: 8px;margin-left: 5px;background: #f7a35c;
    color: #fff;border: none;" data-toggle="tooltip"data-placement="right"title="Total Bookings = Rejected Bookings + Direct Approved Bookings + Approved with Edit Bookings">?</button></h2></div>
                    <div class="col-md-4">
                        <small>
                        <div class="nav navbar-right panel_toolbox">
                            <div id="reportrange5" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; margin-right: -10%;">
                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                <span></span> <b class="caret"></b>
                            </div>
                        </div>
                        </small>
                    </div>
                    <div class="col-md-2" style="padding-right: 0px;">
                        <!--Adding Button to Download Agent wise Completed Bookings data in Excel-->
                        <button type="submit" class="btn btn-success btn-xs" style="margin-left:5px;" title="Download Agent wise completed Bookings">Excel</button>
                        <span class="collape_icon" href="#completed_booking_closure_chart_div" data-toggle="collapse" onclick="get_completed_bookings_data()"><i class="fa fa-plus-square" aria-hidden="true"></i></span>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="col-md-12">
                    <center><img id="loader_gif7" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center>
                </div>
                <div class="x_content collapse" id="completed_booking_closure_chart_div">
                    <div id="completed_booking_closure_chart"></div>
                </div>
            </div>
            </form>
        </div>
        <div class="col-md-6 col-sm-12 col-xs-12" id="cancelled_booking_closure_status" style="padding-right:0px !important;">
            <form action="<?php echo base_url(); ?>employee/dashboard/get_completed_cancelled_booking_by_closure/Cancelled/excel" method="post">            
            <input type="hidden" name="sDate" id="sDate6" value="">
            <input type="hidden" name="eDate" id="eDate6" value="">
            <div class="x_panel">
                <div class="x_title">
                    <div class="col-md-6" style="padding: 0px;"><h2>Review Cancelled Booking Status <button type="button"class="btn btn-default" style="margin-bottom: 10px;padding: 1px 4px;margin-top: 0px;font-size: 8px;margin-left: 5px;background: #f7a35c;
    color: #fff;border: none;" data-toggle="tooltip"data-placement="right"title="Total Bookings = Rejected Bookings + Direct Approved Bookings + Cancelled to Completed Bookings">?</button></h2></div>
                    <div class="col-md-4">
                        <small>
                        <div class="nav navbar-right panel_toolbox">
                            <div id="reportrange6" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; margin-right: -10%;">
                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                <span></span> <b class="caret"></b>
                            </div>
                        </div>
                        </small>
                    </div>
                    <div class="col-md-2" style="padding-right: 0px;">
                        <!--Adding Button to Download Agent wise Cancelled Bookings data in Excel-->
                        <button type="submit" class="btn btn-success btn-xs" style="margin-left:5px;" title="Download Agent wise cancelled Bookings">Excel</button>
                        <span class="collape_icon" href="#cancelled_booking_closure_chart_div" data-toggle="collapse" onclick="get_cancelled_bookings_data()"><i class="fa fa-plus-square" aria-hidden="true"></i></span>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="col-md-12">
                    <center><img id="loader_gif8" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center>
                </div>
                <div class="x_content collapse" id="cancelled_booking_closure_chart_div">
                    <div id="cancelled_booking_closure_chart"></div>
                </div>
            </div></form>
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
    
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0px !important;">
            <div class="dashboard_graph">
                <div class="row x_title">
                    <div class="col-md-6">
                        <h3>Account Manager Performance Score &nbsp;&nbsp;&nbsp;
                            <small>
                            </small>
                        </h3>
                    </div>
                    <div class="col-md-5">
                        <div id="action_agent_date" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; margin-right: -12%;">
                             <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                           
                            <span></span> <b class="caret"></b>
                        </div>
                    </div>
                    <div class="col-md-1">
<!--                        onclick="agent_action_status()"-->
                        <span class="collape_icon" href="#chart_containeragentdiv" data-toggle="collapse" onclick="agent_action_status()" style="margin-right: 8px;"><i class="fa fa-plus-square" aria-hidden="true"></i></span>
                    </div>
                </div>
                <div class="x_content collapse" id="chart_containeragentdiv">
                    <div class="col-md-12">
                        <center><img id="loader_gifagent" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center>
                    </div>
                    <div id="chart_agentdiv" class="chart_agentdiv" style="width:100%; height:400px;"></div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    
    
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0px !important;">
            <div class="dashboard_graph">
                <div class="row x_title">
                    <div class="col-md-6">
                        <h3>Account Manager Total Performance Score &nbsp;&nbsp;&nbsp;
                            <small>
                            </small>
                        </h3>
                    </div>
                    <div class="col-md-5">
                        <div id="action_agent_date_performance" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; margin-right: -12%;">
                             <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                           
                            <span></span> <b class="caret"></b>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <span class="collape_icon" href="#chart_containeragentperformancediv" data-toggle="collapse" onclick="agent_performance_status()" style="margin-right: 8px;"><i class="fa fa-plus-square" aria-hidden="true"></i></span>
                    </div>
                </div>
                <div class="x_content collapse" id="chart_containeragentperformancediv">
                    <div class="col-md-12">
                        <center><img id="loader_gifagentperformance" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center>
                    </div>
                    <div id="chart_agentperformancediv" class="chart_agentperformancediv" style="width:100%; height:400px;"></div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <!-- Agent Graph -->
    
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0px !important;">
            <div class="dashboard_graph">
                <div class="row x_title">
                    <div class="col-md-6">
                        <h3>Audit Team Performance Score &nbsp;&nbsp;&nbsp;
                            <small>
                            </small>
                        </h3>
                    </div>
                    <div class="col-md-5">
                        <div id="action_agent_date_audit" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; margin-right: -12%;">
                             <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                           
                            <span></span> <b class="caret"></b>
                        </div>
                    </div>
                    <div class="col-md-1">
<!--                        onclick="agent_action_status()"-->
                        <span class="collape_icon" href="#chart_containeragentdiv_audit" data-toggle="collapse" onclick="agent_action_status_audit()" style="margin-right: 8px;"><i class="fa fa-plus-square" aria-hidden="true"></i></span>
                    </div>
                </div>
                <div class="x_content collapse" id="chart_containeragentdiv_audit">
                    <div class="col-md-12">
                        <center><img id="loader_gifagentaudit" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center>
                    </div>
                    <div id="chart_agentdiv_audit" class="chart_agentdiv" style="width:100%; height:400px;"></div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <form action="<?php echo base_url(); ?>employee/invoice/download_dashboard_invoice_data" method="post">  
            <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0px !important;">
                <div class="dashboard_graph">
                    <div class="row x_title">
                        <div class="col-md-6">
                            <h3>Invoice Details &nbsp;&nbsp;&nbsp;
                                <small>
                                </small>
                            </h3>
                        </div>
                        <div class="col-md-5">
                            <input type="hidden" value="<?php echo date("Y-m-01"); ?>" name="sDate" id="sDate">
                            <input type="hidden" value="<?php echo date("Y-m-d"); ?>" name="eDate" id="eDate">
                            
                            <div id="action_total_invoice" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; margin-right: -12%;">
                                 <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>

                                <span></span> <b class="caret"></b>
                            </div>
                            
                            <!--Adding Button to Download invoice data in Excel-->
                                <button type="submit" class="btn btn-success btn-xs" style="margin-left:5px;float:right;padding: 6px 10px;" title="Download Invoice Details">Download Excel</button>

                        </div>
                        <div class="col-md-1">
                            <span class="collape_icon" href="#chart_container_partner_total_booking_div" data-toggle="collapse" onclick="dashboard_total_invoice_data()" style="margin-right: 8px;"><i class="fa fa-plus-square" aria-hidden="true"></i></span>
                        </div>
                    </div>
                    <div class="x_content collapse" id="chart_container_partner_total_booking_div">
                        <div class="col-md-12">
                            <center><img id="loader_gif_total_invoice" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center>
                        </div>
                        <div id="chart_total_invoice_div" class="chart_total_invoice_div" style="width:100%; height:100%;">

                             <div class="model-table">
                                <table class="table table-bordered table-hover table-striped" id="invoice_datatable" style="text-align:right;">
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </form>
    </div>
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
    $('#sales_partner').select2({ multiple: true,maximumSelectionLength: 5 });
    $("#sales_partner").on("select2:select", function(e) {
       if (
         $(this).select2("data").length >=
         $(this).data("select2").results.data.maximumSelectionLength
       ) {
         $(this).select2("close");
       }
     });
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
    //this function is used to find out current financial year start date and end date on basis of date.
    function getCurrentFinancialYear(strDate) {
        var startYear = "";
        var endYear = "";
        var docDate = new Date(strDate);
        if ((docDate.getMonth() + 1) <= 3) {
          startYear = docDate.getFullYear() - 1;
          endYear = docDate.getFullYear();
        } else {
          startYear = docDate.getFullYear();
          endYear = docDate.getFullYear() + 1;
        }
        var start_date = new Date(startYear + "-04-01");
        var end_date = new Date(endYear + "-03-31");
        return {startDate : start_date, endDate: end_date };
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
    var current_financial_year = getCurrentFinancialYear(start);
    var current_financial_year_start_date = current_financial_year.startDate;
    var current_financial_year_end_date = current_financial_year.endDate;
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
                days: 30},
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
    
    var options_year = {
            startDate: start,
            endDate: end,
            minDate: '01/01/2000',
            maxDate: '12/31/2030',
            dateLimit: {
                days: 366},
            showDropdowns: true,
            showWeekNumbers: true,
            timePicker: false,
            timePickerIncrement: 1, timePicker12Hour: true,
            ranges: {
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Current FY': [moment(current_financial_year_start_date), moment(current_financial_year_end_date)],
                'Last FY': [moment(current_financial_year_start_date).subtract(1, 'years'), moment(current_financial_year_end_date).subtract(1, 'years')],
                '2nd Last FY': [moment(current_financial_year_start_date).subtract(2, 'years'), moment(current_financial_year_end_date).subtract(2, 'years')],
                '3rd Last FY': [moment(current_financial_year_start_date).subtract(3, 'years'), moment(current_financial_year_end_date).subtract(3, 'years')],
                '4th Last FY': [moment(current_financial_year_start_date).subtract(4, 'years'), moment(current_financial_year_end_date).subtract(4, 'years')],
                '5th Last FY': [moment(current_financial_year_start_date).subtract(5, 'years'), moment(current_financial_year_end_date).subtract(5, 'years')]
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
            $('#action_agent_date_performance span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }

        $('#action_agent_date_performance').daterangepicker(options, cb);

        cb(start, end);
    });
    
    $(function () {
        function cb(start, end) {
            $('#action_total_invoice span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }

        $('#action_total_invoice').daterangepicker(options_year, cb);

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
        function cb(start, end) {
            $('#action_agent_date span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }

//        $('#action_agent_date span').daterangepicker({
//                autoUpdateInput: false,
//                singleDatePicker: true,
//                showDropdowns: true,
//                minDate:"01-01-1998",
//                locale:{
//                    format: 'MMMM D, YYYY'
//                }
//            });
         $('#action_agent_date').daterangepicker(options, cb);

        cb(start, end);
    });
    $(function () {
        function cb(start, end) {
            $('#action_agent_date_audit span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }

//        $('#action_agent_date span').daterangepicker({
//                autoUpdateInput: false,
//                singleDatePicker: true,
//                showDropdowns: true,
//                minDate:"01-01-1998",
//                locale:{
//                    format: 'MMMM D, YYYY'
//                }
//            });
         $('#action_agent_date_audit').daterangepicker(options, cb);

        cb(start, end);
    });
    
    $(function () {
        function cb(start, end) {
            $('#reportrange_booking_cancellation span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }

        $('#reportrange_booking_cancellation').daterangepicker(options, cb);

        cb(start, end);
    });
            
//    $('#action_agent_date span').on('apply.daterangepicker', function(ev, picker) {
//        
//        $('#action_agent_date span').html(picker.startDate.format('MMMM D, YYYY') + ' - ' + picker.endDate.format('MMMM D, YYYY'));
//        alert(picker.endDate.format('MMMM D, YYYY'));
//        agent_click_count(picker.startDate.format('MMMM D, YYYY'), picker.endDate.format('MMMM D, YYYY'));
//    });
    
    $('#action_agent_date').on('apply.daterangepicker', function (ev, picker) {
      //  $('#loader_gifagentperformance').show();
       // $('#chart_agentdiv').hide();
        var startDate = picker.startDate.format('YYYY-MM-DD');
        var endDate = picker.endDate.format('YYYY-MM-DD');
        agent_click_count(startDate, endDate);
        
    });
    $('#action_agent_date_audit').on('apply.daterangepicker', function (ev, picker) {
      //  $('#loader_gifagentperformance').show();
       // $('#chart_agentdiv').hide();
        var startDate = picker.startDate.format('YYYY-MM-DD');
        var endDate = picker.endDate.format('YYYY-MM-DD');
        agent_click_count_audit(startDate, endDate);
        
    });
    
    function agent_action_status(){
        agent_click_count();
    }
    function agent_action_status_audit(){
        agent_click_count_audit();
    }
    
//    $('#action_agent_date').on('cancel.daterangepicker', function(ev, picker) {
//        $(this).val('');
//    });
    
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
    
    $('#action_agent_date_performance').on('apply.daterangepicker', function (ev, picker) {
        $('#loader_gifagentperformance').show();
        $('#chart_agentdiv').hide();
        var startDate = picker.startDate.format('YYYY-MM-DD');
        var endDate = picker.endDate.format('YYYY-MM-DD');
        agent_click_performance(startDate, endDate);
        
    });
    
    //set values of start date and end date in input fields
    function set_invoice_date_values(startDate, endDate){
        $("#sDate").val(startDate);
        $("#eDate").val(endDate);
    }
    
    $('#action_total_invoice').on('apply.daterangepicker', function (ev, picker) {
        $('#loader_gif_total_invoice').show();
        $('#chart_total_invoice_div').hide();
        var startDate = picker.startDate.format('YYYY-MM-DD');
        var endDate = picker.endDate.format('YYYY-MM-DD');
        set_invoice_date_values(startDate, endDate);
        dashboard_click_total_invoice_data();
        
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
        $("#sDate5").val(startDate);
        $("#eDate5").val(endDate);
        url = baseUrl + '/employee/dashboard/get_completed_cancelled_booking_by_closure/Completed';
        var data = {sDate: startDate, eDate: endDate};
        
        sendAjaxRequest(data,url,post_request).done(function(response){
            console.log(response);
            if($.trim(response)){
                create_chart_closure_completed_booking(response);
            }
            else{
                alert("Graph Data Not Found");
                $('#loader_gif7').hide();
            }
        });
    });
     
   $('#reportrange6').on('apply.daterangepicker', function (ev, picker) {
        $('#loader_gif8').show();
        $('#cancelled_booking_closure_chart').hide();
        var startDate = picker.startDate.format('YYYY-MM-DD');
        var endDate = picker.endDate.format('YYYY-MM-DD');
        $("#sDate6").val(startDate);
        $("#eDate6").val(endDate);
        url = baseUrl + '/employee/dashboard/get_completed_cancelled_booking_by_closure/Cancelled';
        var data = {sDate: startDate, eDate: endDate};
        
        sendAjaxRequest(data,url,post_request).done(function(response){
            console.log(response);
            if($.trim(response)){
                create_chart_closure_cancelled_booking(response);
            }
            else{
                alert("Graph Data Not Found");
                $('#loader_gif8').hide();
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
    
    function agent_daily_report_call(){ 
        agent_daily_report(start.format('MMMM D, YYYY'), end.format('MMMM D, YYYY'));
    }
    
    function agent_performance_status(){
        agent_click_performance(start.format('MMMM D, YYYY'), end.format('MMMM D, YYYY'));
        
    }
    
    function dashboard_click_total_invoice_data(){
        url = baseUrl + '/employee/invoice/get_dashboard_invoice_data';
        $('#loader_gif_total_invoice').show();
        $("#invoice_datatable tbody").html("");
        $('#chart_total_invoice_div').fadeIn();
        $.ajax({
           type: 'POST',
           url: url,
           data: {sDate : $("#sDate").val(), eDate : $("#eDate").val()}
         })
         .done (function(data) { 
            $('#loader_gif_total_invoice').hide();
            $("#invoice_datatable tbody").html(data);
         })
         .fail(function(jqXHR, textStatus, errorThrown){
             $('#loader_gif_total_invoice').hide();
             alert("Something went wrong while loading invoice!");
          })
    }
    
    //this function is used to get invoice data
    function dashboard_total_invoice_data(){
        set_invoice_date_values(moment().startOf('month').format('MMMM D, YYYY'), moment().endOf('month').format('MMMM D, YYYY'));
        dashboard_click_total_invoice_data();
        
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
    
    function initiate_non_assigned_booking_Reporting() {
        $('#loader_gif_title').fadeIn();
        var data = {};
        url = '<?php echo base_url(); ?>employee/dashboard/get_non_assigned_bookings';
        
        sendAjaxRequest(data,url,post_request).done(function(response){
            $('#loader_gif_title').hide();
            $('#Unassigned_Booking_Reporting').children('table').children('tbody').html(response);
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
    function get_arm_details_for_rm(rm_id){
        if($("tr.tat-report[data-rm-row-id='"+ rm_id+ "']").data("has_data")){
            if($("#arm_table_"+ rm_id).length>0){
                if($("#arm_table_"+ rm_id).is(":hidden")){
                    $("span.tat-report[data-rm-id='"+ rm_id+ "']").find("i").removeClass("fa-plus-square").addClass("fa-minus-square");
                }else{
                    $("span.tat-report[data-rm-id='"+ rm_id+ "']").find("i").removeClass("fa-minus-square").addClass("fa-plus-square");
                }
                $("#arm_table_"+ rm_id).slideToggle();
                return false;
            }
        }else{
            if($("#arm_table_"+ rm_id).length>0){
                if(!$("#arm_table_"+ rm_id).is(":hidden")){
                    $("span.tat-report[data-rm-id='"+ rm_id+ "']").find("i").removeClass("fa-minus-square").addClass("fa-plus-square");
                    $("#arm_table_"+ rm_id).remove();
                    return false;
                }
            }
        }
        $("span.tat-report[data-rm-id='"+ rm_id+ "']").find("i").removeClass("fa-plus-square").addClass("fa-minus-square");
        var html = "<tr id='arm_table_"+ rm_id +"' class='arm-tat-table'><td class='text-center' colspan=10><img src='<?php echo base_url(); ?>images/loadring.gif' ></td><tr>";
        $("tr.tat-report[data-rm-row-id='"+ rm_id+ "']").after(html);
        dateRange = $("#completed_daterange_id").val();
        dateArray = dateRange.split(" - ");
        startDate = dateArray[0];
        endDate = dateArray[1];
        status = $("#completed_status").val();
        service_id = $("#service_id").val();
        partner_id = $("#partner_id").val();
        request_type = getMultipleSelectedValues("request_type");
        free_paid = $("#free_paid").val();
        upcountry = $("#upcountry").val();
        if(!status){
            status = "not_set";
        }
        if(!service_id){
            service_id = "not_set";
        }
        if(!request_type){
            request_type = "not_set";
        }
        if(!free_paid){
            free_paid = "not_set";
        }
         if(!upcountry){
            upcountry = "not_set";
        }
        if(!partner_id){
            partner_id = "not_set";
        }
        
        url =  baseUrl + "/employee/dashboard/get_booking_tat_report/"+startDate+"/"+endDate+"/"+status+"/"+service_id+"/"+request_type+"/"+free_paid+"/"+upcountry+"/ARM/0/"+partner_id;
        var data = {rm:rm_id};
        sendAjaxRequest(data,url,post_request).done(function(response){
            create_arm_tat_report_table(rm_id, JSON.parse(response));
        });
    }
    function create_arm_tat_report_table(tableRow,data){
        html_leg_tbl = "";
        if(!!data.leg_1 && data.leg_1.length>0){
            html_leg_tbl = "<th></th>";
        }
        html='<table class="table table-striped table-bordered sub-table">'
                +'<thead><tr><th>S.no</th><th>ASM</th>'
                +html_leg_tbl
                +'<th>D0</th><th>D1</th><th>D2</th><th>D3</th><th>D4</th><th>D5 - D7</th><th>D8 - D15</th><th>> D15</th></tr></thead>';
        if(!!data.TAT && data.TAT.length>0){
            html += "<tbody>";
            for(var i in data.TAT){
                html += '<tr>';
                html += "<td>"+ (parseInt(i)+1)+ "</td>";
                if(data.TAT[i].id === "00"){
                    html += "<td>"+ data.TAT[i].entity+ "</td>";
                }else{
                    html += "<td><button type='button' id='vendor_"+ data.TAT[i].id+ "' class='btn btn-info' target='_blank' onclick=\"open_full_view(this.id,'<?php echo base_url(); ?>employee/dashboard/tat_calculation_full_view/','0','0','rm_completed_booking_form','<?php echo _247AROUND_ASM ?>')\">"+ data.TAT[i].entity+ "</button></td>";
                }
                // Show leg wise ASM Report
                if(!!data.leg_1 && data.leg_1.length>0){
                    html += "<td>leg_1<br/>leg_2<br/>Total</td>";
                    html += "<td>"
                            + data.leg_1[i].TAT_0+ "("+ data.leg_1[i].TAT_0_per+ "%)<br/>"
                            + data.leg_2[i].TAT_0+ "("+ data.leg_2[i].TAT_0_per+ "%)<br/>"
                            + data.TAT[i].TAT_0+ "("+ data.TAT[i].TAT_0_per+ "%)"
                            + "</td>";
                    html += "<td>"
                            + data.leg_1[i].TAT_1+ "("+ data.leg_1[i].TAT_1_per+ "%)<br/>"
                            + data.leg_2[i].TAT_1+ "("+ data.leg_2[i].TAT_1_per+ "%)<br/>"
                            + data.TAT[i].TAT_1+ "("+ data.TAT[i].TAT_1_per+ "%)"
                            + "</td>";
                    html += "<td>"
                            + data.leg_1[i].TAT_2+ "("+ data.leg_1[i].TAT_2_per+ "%)<br/>"
                            + data.leg_2[i].TAT_2+ "("+ data.leg_2[i].TAT_2_per+ "%)<br/>"
                            + data.TAT[i].TAT_2+ "("+ data.TAT[i].TAT_2_per+ "%)"
                            + "</td>";
                    html += "<td>"
                            + data.leg_1[i].TAT_3+ "("+ data.leg_1[i].TAT_3_per+ "%)<br/>"
                            + data.leg_2[i].TAT_3+ "("+ data.leg_2[i].TAT_3_per+ "%)<br/>"
                            + data.TAT[i].TAT_3+ "("+ data.TAT[i].TAT_3_per+ "%)"
                            + "</td>";
                    html += "<td>"
                            + data.leg_1[i].TAT_4+ "("+ data.leg_1[i].TAT_4_per+ "%)<br/>"
                            + data.leg_2[i].TAT_4+ "("+ data.leg_2[i].TAT_4_per+ "%)<br/>"
                            + data.TAT[i].TAT_4+ "("+ data.TAT[i].TAT_4_per+ "%)"
                            + "</td>";
                    html += "<td>"
                            + data.leg_1[i].TAT_5+ "("+ data.leg_1[i].TAT_5_per+ "%)<br/>"
                            + data.leg_2[i].TAT_5+ "("+ data.leg_2[i].TAT_5_per+ "%)<br/>"
                            + data.TAT[i].TAT_5+ "("+ data.TAT[i].TAT_5_per+ "%)"
                            + "</td>";
                    html += "<td>"
                            + data.leg_1[i].TAT_8+ "("+ data.leg_1[i].TAT_8_per+ "%)<br/>"
                            + data.leg_2[i].TAT_8+ "("+ data.leg_2[i].TAT_8_per+ "%)<br/>"
                            + data.TAT[i].TAT_8+ "("+ data.TAT[i].TAT_8_per+ "%)"
                            + "</td>";
                    html += "<td>"
                            + data.leg_1[i].TAT_16+ "("+ data.leg_1[i].TAT_16_per+ "%)<br/>"
                            + data.leg_2[i].TAT_16+ "("+ data.leg_2[i].TAT_16_per+ "%)<br/>"
                            + data.TAT[i].TAT_16+ "("+ data.TAT[i].TAT_16_per+ "%)"
                            + "</td>";
                    html += '</tr>';
                }
                else
                {
                    html += "<td>"+ data.TAT[i].TAT_0+ "("+ data.TAT[i].TAT_0_per+ "%)</td>";
                    html += "<td>"+ data.TAT[i].TAT_1+ "("+ data.TAT[i].TAT_1_per+ "%)</td>";
                    html += "<td>"+ data.TAT[i].TAT_2+ "("+ data.TAT[i].TAT_2_per+ "%)</td>";
                    html += "<td>"+ data.TAT[i].TAT_3+ "("+ data.TAT[i].TAT_3_per+ "%)</td>";
                    html += "<td>"+ data.TAT[i].TAT_4+ "("+ data.TAT[i].TAT_4_per+ "%)</td>";
                    html += "<td>"+ data.TAT[i].TAT_5+ "("+ data.TAT[i].TAT_5_per+ "%)</td>";
                    html += "<td>"+ data.TAT[i].TAT_8+ "("+ data.TAT[i].TAT_8_per+ "%)</td>";
                    html += "<td>"+ data.TAT[i].TAT_16+ "("+ data.TAT[i].TAT_16_per+ "%)</td>";
                    html += '</tr>';
                }                
            }
            
            html += "</tbody></table>";            
            if(!!data.leg_1 && data.leg_1.length>0){
                html = "<td colspan=11>"+ html+ "</td>"
            }
            else
            {
                html = "<td colspan=10>"+ html+ "</td>"
            }
            $("#arm_table_"+ tableRow).empty().html(html);
            $("tr.tat-report[data-rm-row-id='"+ tableRow+ "']").data("has_data", true);
        }else{
            html += "<tbody><tr><td colspan=10 class='text-center'>No Data.</td></tr></tbody></table>";
            html = "<td colspan=10>"+ html+ "</td>"
            $("#arm_table_"+ tableRow).empty().html(html);
            $("tr.tat-report[data-rm-row-id='"+ tableRow+ "']").data("has_data", false);
        }
    }

    function get_arm_open_call_details_for_rm(rm_id){
        if($("tr.tat-report[data-rm-row-id='"+ rm_id+ "']").data("has_data")){
            if($("#arm_open_call_table_"+ rm_id).length>0){
                if($("#arm_open_call_table_"+ rm_id).is(":hidden")){
                    $("span.open-call-report[data-rm-id='"+ rm_id+ "']").find("i").removeClass("fa-plus-square").addClass("fa-minus-square");
                }else{
                    $("span.open-call-report[data-rm-id='"+ rm_id+ "']").find("i").removeClass("fa-minus-square").addClass("fa-plus-square");
                }
                $("#arm_open_call_table_"+ rm_id).slideToggle();
                return false;
            }
        }else{
            if($("#arm_open_call_table_"+ rm_id).length>0){
                if(!$("#arm_open_call_table_"+ rm_id).is(":hidden")){
                    $("span.open-call-report[data-rm-id='"+ rm_id+ "']").find("i").removeClass("fa-minus-square").addClass("fa-plus-square");
                    $("#arm_open_call_table_"+ rm_id).remove();
                    return false;
                }
            }
        }
        $("span.open-call-report[data-rm-id='"+ rm_id+ "']").find("i").removeClass("fa-plus-square").addClass("fa-minus-square");
        var html = "<tr id='arm_open_call_table_"+ rm_id +"' class='arm-open-call-table'><td class='text-center' colspan=11><img src='<?php echo base_url(); ?>images/loadring.gif' ></td><tr>";
        $("tr.open-call-report[data-rm-row-id='"+ rm_id+ "']").after(html);
        dateRange = $("#pending_daterange_id_rm").val();
        dateArray = dateRange.split(" - ");
        startDate = dateArray[0];
        endDate = dateArray[1];
        service_id = $("#service_id_rm_pending").val();
        partner_id = $("#partner_id_rm_pending").val();
        request_type = getMultipleSelectedValues("request_type_rm_pending");
        free_paid = $("#free_paid_rm_pending").val();
        upcountry = getMultipleSelectedValues("upcountry_rm_pending");
        status = getMultipleSelectedValues("pending_dependency");
        if(!status){
            status = "not_set";
        }
        if(!service_id){
            service_id = "not_set";
        }
        if(!request_type){
            request_type = "not_set";
        }
        if(!free_paid){
            free_paid = "not_set";
        }
         if(!upcountry){
            upcountry = "not_set";
        }
        if(!partner_id){
            partner_id = "not_set";
        }

        url =  baseUrl + "/employee/dashboard/get_booking_tat_report/"+startDate+"/"+endDate+"/"+status+"/"+service_id+"/"+request_type+"/"+free_paid+"/"+upcountry+"/ARM/Pending/"+partner_id;
        var data = {rm:rm_id};
        sendAjaxRequest(data,url,post_request).done(function(response){
            create_arm_open_call_tat_report_table(rm_id, JSON.parse(response));
        });
    }
    function create_arm_open_call_tat_report_table(tableRow,data){
        html='<table class="table table-striped table-bordered sub-table">'
                +'<thead><tr><th>S.no</th><th>ASM</th><th>D0</th><th>D1</th><th>D2</th><th>D3</th><th>D4</th><th>D5 - D7</th><th>D8 - D15</th><th>> D15</th><th>Total</th></tr></thead>';
        if(!!data && data.length>0){
            html += "<tbody>";
            for(var i in data){
                var total = 0;
                html += '<tr>';
                html += "<td>"+ (parseInt(i)+1)+ "</td>";
                if(data[i].id === "00"){
                    html += "<td>"+ data[i].entity+ "</td>";
                    html += '<td>'+ data[i].TAT_0+ ' ('+ data[i].TAT_0_per+ '%)</td>';
                    total += data[i].TAT_0;
                    html += '<td>'+ data[i].TAT_1+ ' ('+ data[i].TAT_1_per+ '%)</td>'; 
                    total += data[i].TAT_1;
                    html += '<td>'+ data[i].TAT_2+ ' ('+ data[i].TAT_2_per+ '%)</td>';
                    total += data[i].TAT_2;
                    html += '<td>'+ data[i].TAT_3+ ' ('+ data[i].TAT_3_per+ '%)</td>';
                    total += data[i].TAT_3;
                    html += '<td>'+ data[i].TAT_4+ ' ('+ data[i].TAT_4_per+ '%)</td>';
                    total += data[i].TAT_4;
                    html += '<td>'+ data[i].TAT_5+ ' ('+ data[i].TAT_5_per+ '%)</td>';
                    total += data[i].TAT_5;
                    html += '<td>'+ data[i].TAT_8+ ' ('+ data[i].TAT_8_per+ '%)</td>';
                    total += data[i].TAT_8;
                    html += '<td>'+ data[i].TAT_16+ ' ('+ data[i].TAT_16_per+ '%)</td>';
                    total += data[i].TAT_16;
                    html += '<td>'+ data[i].Total_Pending + " ("+ data[i].TAT_total_per+ "%) </td>";                
                }else{
                    html += "<td><button type='button' id='vendor_"+ data[i].id+ "' class='btn btn-info' target='_blank' onclick=\"open_full_view(this.id,'<?php echo base_url(); ?>employee/dashboard/tat_calculation_full_view/','0','Pending','rm_pending_booking_form','<?php echo _247AROUND_ASM ?>')\">"+ data[i].entity+ "</button></td>";
                    html += '<td><form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank" style="width: 8%;">'
                            +'<input type="hidden" name="booking_id_status" value="'+ data[i].TAT_0_bookings+ '">'
                            +'<input type="submit" value="'+ data[i].TAT_0+ ' ('+ data[i].TAT_0_per+ '%)"  class="btn btn-success">'
                             +'</form></td>';
                    total += data[i].TAT_0;
                    html += '<td><form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank" style="width: 8%;">'
                                +'<input type="hidden" name="booking_id_status" value="'+ data[i].TAT_1_bookings+ '">'
                                +'<input type="submit" value="'+ data[i].TAT_1+ ' ('+ data[i].TAT_1_per+ '%)"  class="btn btn-success">'
                                 +'</form></td>';
                    total += data[i].TAT_1;
                    html += '<td><form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank" style="width: 8%;">'
                                +'<input type="hidden" name="booking_id_status" value="'+ data[i].TAT_2_bookings+ '">'
                                +'<input type="submit" value="'+ data[i].TAT_2+ ' ('+ data[i].TAT_2_per+ '%)"  class="btn btn-'+ ((data[i].TAT_2<1)?'success':'danger')+ '">'
                                 +'</form></td>';
                    total += data[i].TAT_2;
                    html += '<td><form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank" style="width: 8%;">'
                                +'<input type="hidden" name="booking_id_status" value="'+ data[i].TAT_3_bookings+ '">'
                                +'<input type="submit" value="'+ data[i].TAT_3+ ' ('+ data[i].TAT_3_per+ '%)"  class="btn btn-'+ ((data[i].TAT_3<1)?'success':'danger')+ '">'
                                 +'</form></td>';
                    total += data[i].TAT_3;
                    html += '<td><form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank" style="width: 8%;">'
                                +'<input type="hidden" name="booking_id_status" value="'+ data[i].TAT_4_bookings+ '">'
                                +'<input type="submit" value="'+ data[i].TAT_4+ ' ('+ data[i].TAT_4_per+ '%)"  class="btn btn-'+ ((data[i].TAT_4<1)?'success':'danger')+ '">'
                                 +'</form></td>';
                    total += data[i].TAT_4;
                    html += '<td><form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank" style="width: 8%;">'
                                +'<input type="hidden" name="booking_id_status" value="'+ data[i].TAT_5_bookings+ '">'
                                +'<input type="submit" value="'+ data[i].TAT_5+ ' ('+ data[i].TAT_5_per+ '%)"  class="btn btn-'+ ((data[i].TAT_5<1)?'success':'danger')+ '">'
                                 +'</form></td>';
                    total += data[i].TAT_5;
                    html += '<td><form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank" style="width: 8%;">'
                                +'<input type="hidden" name="booking_id_status" value="'+ data[i].TAT_8_bookings+ '">'
                                +'<input type="submit" value="'+ data[i].TAT_8+ ' ('+ data[i].TAT_8_per+ '%)"  class="btn btn-'+ ((data[i].TAT_8<1)?'success':'danger')+ '">'
                                 +'</form></td>';
                    total += data[i].TAT_8;
                    html += '<td><form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank" style="width: 8%;">'
                                +'<input type="hidden" name="booking_id_status" value="'+ data[i].TAT_16_bookings+ '">'
                                +'<input type="submit" value="'+ data[i].TAT_16+ ' ('+ data[i].TAT_16_per+ '%)"  class="btn btn-'+ ((data[i].TAT_16<1)?'success':'danger')+ '">'
                                 +'</form></td>';
                    total += data[i].TAT_16;
                    html += '<td><form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank" style="width: 8%;">'
                                +'<input type="hidden" name="booking_id_status" value="'+ data[i].TAT_Total_bookings + '">'
                                +'<input type="submit" value="'+ data[i].Total_Pending + ' ('+ data[i].TAT_total_per + '%)"  class="btn btn-'+ ((data[i].Total_Pending<1)?'success':'danger')+ '">'
                                 +'</form></td>';
                }
                html += '</tr>';
            }
            html += "</tbody></table>";
            html = "<td colspan=11>"+ html+ "</td>"
            $("#arm_open_call_table_"+ tableRow).empty().html(html);
            $("tr.open-call-report[data-rm-row-id='"+ tableRow+ "']").data("has_data", true);
        }else{
            html += "<tbody><tr><td colspan=11 class='text-center'>No Data.</td></tr></tbody></table>";
            html = "<td colspan=11>"+ html+ "</td>"
            $("#arm_open_call_table_"+ tableRow).empty().html(html);
            $("tr.open-call-report[data-rm-row-id='"+ tableRow+ "']").data("has_data", false);
        }
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
                //console.log(response);
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
    $('input[name="daterange"]').daterangepicker({
        timePicker: true,
        timePickerIncrement: 30,
        locale: {
            format: 'YYYY-MM-DD'
        },
       startDate: "<?php echo date("Y-m-d", strtotime("first day of previous month")); ?>"
    },  function(start, end, label) {
            var startDateObj = new Date(start);
            var endDateObj = new Date(end);
            var timeDiff = Math.abs(endDateObj.getTime() - startDateObj.getTime());
            var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24)); 

            var date = startDateObj.getFullYear()+'-'+(("0" + (startDateObj.getMonth() + 1)).slice(-2))+'-'+(("0" + startDateObj.getDate()).slice(-2))+' - '+endDateObj.getFullYear()+'-'+(("0" + (endDateObj.getMonth() + 1)).slice(-2))+'-'+(("0" + endDateObj.getDate()).slice(-2));
            var esDate = startDateObj.getFullYear()+'-'+(("0" + (startDateObj.getMonth() + 1)).slice(-2))+'-'+(("0" + startDateObj.getDate()).slice(-2));
            var eeDate = endDateObj.getFullYear()+'-'+(("0" + (endDateObj.getMonth() + 1)).slice(-2))+'-'+(("0" + endDateObj.getDate()).slice(-2));
            if(diffDays > 92) {
                alert("Maximum range allowed is 3 month.");
                $('#daterange_id').data('daterangepicker').setStartDate("<?php echo date("Y-m-d", strtotime("-1 month")); ?>");
                $('#daterange_id').data('daterangepicker').setEndDate("<?php echo date("Y-m-d"); ?>");
                $("#esDate").val("<?php echo date("Y-m-d", strtotime("-1 month")); ?>");
                $("#eeDate").val("<?php echo date("Y-m-d"); ?>");
                return false;
            }
            $("#esDate").val(esDate);
            $("#eeDate").val(eeDate);
    });
}
    function open_full_view(id,url,is_am,is_pending,form_id,entity_type="",sub_id=""){
      // Add entity_type(ASM/RM/Brand) 
      entity_id = id.split("_")[1];
      final_url = url+entity_id+'/0/'+is_am+'/'+is_pending+'/'+entity_type+'/'+sub_id;
      $('#'+form_id).attr('action', final_url);
      $('#'+form_id).submit();
    }
    
    function get_completed_bookings_data(){
        $('#loader_gif7').fadeIn();
        $('#completed_booking_closure_chart').fadeOut();
        var data = {};
        url =  '<?php echo base_url(); ?>employee/dashboard/get_completed_cancelled_booking_by_closure/Completed';
        
        sendAjaxRequest(data,url,post_request).done(function(response){
            console.log(response);
            if($.trim(response)){
                create_chart_closure_completed_booking(response);   
            }
            else{
                alert("Graph Data Not Found");
                $('#loader_gif7').fadeOut();
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
            console.log(response);
            if($.trim(response)){
                create_chart_closure_cancelled_booking(response);
            }
            else{
                alert("Graph Data Not Found");
                $('#loader_gif8').fadeOut();
            }
        });
    }
    
    function create_chart_closure_cancelled_booking(response) {       
        var data = JSON.parse(response);
        var closures = data.closures.split(',');
        var reject = JSON.parse("[" + data.reject + "]");
        var approved = JSON.parse("[" + data.approved + "]");
        var edit_complete = JSON.parse("[" + data.edit_complete + "]");
        var edit_cancel = JSON.parse("[" + data.edit_cancel + "]");
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
                }, {
                    name: 'Edit Cancelled Bookings',
                    data: edit_cancel
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
    
    function agent_click_performance(startDate, endDate){
        url = baseUrl + '/employee/dashboard/get_am_total_performace_score';
        var data = {sDate: startDate, eDate: endDate};
        
        sendAjaxRequest(data,url,post_request).done(function(response){
            console.log(response);
            create_chart_am_performace_total_score(response);
        });
    }
    
    function create_chart_am_performace_total_score(response = false){
        console.log(response);
        if(response){
            $('#loader_gifagentperformance').hide();
            $('#chart_agentperformancediv').fadeIn();
            
            var data = JSON.parse(response);
            var xaxis = data.xaxis.split(',');
            var yaxis = JSON.parse("[" + data.yaxis + "]");
            
            chart1 = new Highcharts.Chart({
                    chart: {
                        renderTo: 'chart_agentperformancediv',
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
                        categories: xaxis
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
                    name: 'Account Manager',
                    data: yaxis
                }]
                });
                $('#loader_gifagentperformance').hide();
        
            
        } else {
            alert("Graph Data Not Found");
            $('#loader_gifagentperformance').hide();
            $('#chart_agentperformancediv').hide();
        }
    }
    
    function agent_click_count(startDate ="", endDate = ""){
        if(startDate === ""){
            var d = $('#action_agent_date span').text();
            var d1 = d.split("-");
            console.log(d1[0]);
            startDate = d1[0].trim();
            endDate =d1[1].trim();
            //"June 1, 2020 - June 30, 2020"
        }
        
        $('#loader_gifagent').fadeIn();
        $('#chart_agentdiv').hide();
        var data = {startDate: startDate, endDate: endDate};
        url =  '<?php echo base_url(); ?>employee/dashboard/get_agent_action_log_per_hour';
        
        sendAjaxRequest(data,url,post_request).done(function(response){
            
            if(response){
               // console.log(response);
                var data = JSON.parse(response);
                var theHour = data.xaxis.split(',');
                //console.log(theHour);
                var series = data.series;
                
                 for (i = 0; i < series.length; i++) {
                    series[i].data = JSON.parse("[" + series[i].count + "]");
                }
                $('#chart_agentdiv').fadeIn();
                chart1 = new Highcharts.Chart({
                    chart: {
                        renderTo: 'chart_agentdiv',
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
                        categories: theHour
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
                    series: series
                });
                $('#loader_gifagent').hide();
            }
            
            else{
                alert("Graph Data Not Found");
                $('#loader_gifagent').hide();
            }
        });
    }
    
    function agent_click_count_audit(startDate ="", endDate = ""){
        if(startDate === ""){
            var d = $('#action_agent_date_audit span').text();
            var d1 = d.split("-");
            console.log(d1[0]);
            startDate = d1[0].trim();
            endDate =d1[1].trim();
            //"June 1, 2020 - June 30, 2020"
        }
        
        $('#loader_gifagentaudit').fadeIn();
        $('#chart_agentdiv_audit').hide();
        var data = {startDate: startDate, endDate: endDate, group : 'closure'};
        url =  '<?php echo base_url(); ?>employee/dashboard/get_agent_action_log_per_hour';
        
        sendAjaxRequest(data,url,post_request).done(function(response){
            
            if(response){
               // console.log(response);
                var data = JSON.parse(response);
                var theHour = data.xaxis.split(',');
                //console.log(theHour);
                var series = data.series;
                
                 for (i = 0; i < series.length; i++) {
                    series[i].data = JSON.parse("[" + series[i].count + "]");
                }
                $('#chart_agentdiv_audit').fadeIn();
                chart1 = new Highcharts.Chart({
                    chart: {
                        renderTo: 'chart_agentdiv_audit',
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
                        categories: theHour
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
                    series: series
                });
                $('#loader_gifagentaudit').hide();
            }
            
            else{
                alert("Graph Data Not Found");
                $('#loader_gifagentaudit').hide();
            }
        });
    }
    /*
     * By choosing dates from datapicker and expand chart div and send AJAX request to get cancelled booking data
     * by sending start and end dates of selected option (last wwek/last month/ custom dates)  
     * and get response data in JSON format aand pass to  
     * create_piechart_based_on_bookings_cancellation_reason function
     */
    $('#reportrange_booking_cancellation').on('apply.daterangepicker', function (ev, picker) {
        $('#loader_gif_booking_cancellation').show();
        $('#state_type_booking_chart').hide();
        var startDate = picker.startDate.format('YYYY-MM-DD');
        var endDate = picker.endDate.format('YYYY-MM-DD');
        url = baseUrl + '/employee/dashboard/get_booking_cancellation_reasons';
        var data = {sDate: startDate, eDate: endDate};
        
        sendAjaxRequest(data,url,post_request).done(function(response){
            if(response){
                create_piechart_based_on_bookings_cancellation_reason(response);
            }else{
                $('#loader_gif_booking_cancellation').hide();
                $('#booking_cancellation_chart_div').fadeIn();
                $('#booking_cancellation_chart').html('No data');
            }
        });
    });
    /*
     * On click of plus buttion expand chart div and send AJAX request to get cancelled booking data
     * by sending first and last date of current 
     * and get response data in JSON format aand pass to  
     * create_piechart_based_on_bookings_cancellation_reason function
     */
    function get_bookings_cancellation_reason(){
        $('#loader_gif_booking_cancellation').fadeIn();
        $('#booking_cancellation_chart_div').fadeOut();
        var startDate = '<?php echo date('Y-m-01') ?>';
        var endDate = '<?php echo date('Y-m-t') ?>';
        url = baseUrl + '/employee/dashboard/get_booking_cancellation_reasons';
        var data = {sDate: startDate, eDate: endDate};        
        sendAjaxRequest(data,url,post_request).done(function(response){
            //console.log(response);
            if(response){
                create_piechart_based_on_bookings_cancellation_reason(response);
            }else{
                $('#loader_gif_booking_cancellation').hide();
                $('#booking_cancellation_chart_div').fadeIn();
                $('#booking_cancellation_chart').html('No data');
            }
        });
    }

    /*
     * This function create pie chart of cancelled booking by cancellation reason
     * Input param JSON data array
     */
    function create_piechart_based_on_bookings_cancellation_reason(response) {
        console.log(response);
        var test = JSON.parse(response);
        var tt =  [{
            name : test.series.name,
            colorByPoint : test.series.colorByPoint,
            data: test.series.data
        }];
        $('#loader_gif_booking_cancellation').hide();
        $('#booking_cancellation_chart_div').fadeIn();
        // Configure and put JSON data into piechart
        Highcharts.chart('booking_cancellation_chart', {
                  chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                  },
                  title: {
                    text: ''
                  },
                  tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                  },
                  accessibility: {
                    point: {
                      valueSuffix: ''
                    }
                  },
                  plotOptions: {
                    pie: {
                      allowPointSelect: true,
                      cursor: 'pointer',
                      dataLabels: {
                        enabled: false
                      },
                      showInLegend: true
                    }
                  },
                  series:tt
                });
    
    }
    
    /*
     * Brand wise sales for each month of selected year
     */
    $('#btn_brand_sales').on('click',function(){
       var _sales_year = $('#sales_year').val(); 
       var _sales_partners = $('#sales_partner').val();
       if(_sales_year !== '' && (_sales_partners.length > 0 && _sales_partners.length < 6)){
           var sales_partner_text = [];
           $('#sales_partner option:selected').each(function(){
               sales_partner_text.push($(this).text().split(' ').join(''));
           });
           $.ajax({
               type:'POST',
               url:'<?php echo base_url('employee/dashboard/brand_sales_analytics') ?>',
               data:{sales_year:_sales_year,sales_partner:_sales_partners},
               success: function(response){
                   var data = JSON.parse(response);
                   $('#loader_gif_brand_sales').css('display','none');
                   $('#brand_sales tbody').html(data.table_data);
                   console.log(data.series);
                   brand_sales_analysis_table(sales_partner_text);
                   brand_sales_bar_chart(data.series);
               },beforeSend: function(){
                    $('#brand_sales tbody').html('');
                    $('#loader_gif_brand_sales').css('display','block');
               }
           });        
        }
    });
    function brand_sales_analysis_table(sales_partner_text){
        if ( $.fn.dataTable.isDataTable( '#brand_sales' ) ) {
            table = $('#brand_sales').DataTable();
        }
        else {
                    
           table = $('#brand_sales').DataTable({
             dom: 'Bfrtip',
             searching: false,
             paging: false,
             buttons: [{
                 extend: 'excelHtml5',
                 text: 'Export',
                 title: sales_partner_text.join('_')+'_'+$('#sales_year').val()+'_sales_report',
                 exportOptions: {
                     format: {
                         header: function ( data, columnIdx ) {
                                return data;
                            },
                         body: function ( data, column, row ) {
                            //if it is html, return the text of the html instead of html
                             if (/<\/?[^>]*>/.test(data)) {                                    
                                 return $(data).text();
                             } else {
                                 return data;
                             }                                                                
                         }
                     }
                 }
             }]
         });
        }
     }
  
   function brand_sales_bar_chart(series){ 
    Highcharts.chart('brand_sales_chart', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'Brand Sales Chart'
    },
    subtitle: {
        text: 'Month wise report'
    },
    xAxis: {
        categories: [
            'Jan',
            'Feb',
            'Mar',
            'Apr',
            'May',
            'Jun',
            'Jul',
            'Aug',
            'Sep',
            'Oct',
            'Nov',
            'Dec'
        ],
        crosshair: true
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Registered Calls'
        }
    },
    tooltip: {
        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
            '<td style="padding:0"><b>{point.y}</b></td></tr>',
        footerFormat: '</table>',
        shared: true,
        useHTML: true
    },
    plotOptions: {
        column: {
           // pointPadding: 0.2,
            borderWidth: 0
        }
    },
    series: series
});
}
    // Functions to get Brand Wise AM completed/cancelled calls Data
    function get_brand_details_for_am(am_id)
    {
        if($("tr.am-tat-report[data-am-row-id='"+ am_id+ "']").data("has_data")){
            if($("#brand_table_"+ am_id).length>0){
                if($("#brand_table_"+ am_id).is(":hidden")){
                    $("span.am-tat-report[data-am-id='"+ am_id+ "']").find("i").removeClass("fa-plus-square").addClass("fa-minus-square");
                }else{
                    $("span.am-tat-report[data-am-id='"+ am_id+ "']").find("i").removeClass("fa-minus-square").addClass("fa-plus-square");
                }
                $("#brand_table_"+ am_id).slideToggle();
                return false;
            }
        }else{
            if($("#brand_table_"+ am_id).length>0){
                if(!$("#brand_table_"+ am_id).is(":hidden")){
                    $("span.am-tat-report[data-am-id='"+ am_id+ "']").find("i").removeClass("fa-minus-square").addClass("fa-plus-square");
                    $("#brand_table_"+ am_id).remove();
                    return false;
                }
            }
        }
        $("span.am-tat-report[data-am-id='"+ am_id+ "']").find("i").removeClass("fa-plus-square").addClass("fa-minus-square");
        var html = "<tr id='brand_table_"+ am_id +"' class='brand-tat-table'><td class='text-center' colspan=10><img src='<?php echo base_url(); ?>images/loadring.gif' ></td><tr>";
        $("tr.am-tat-report[data-am-row-id='"+ am_id+ "']").after(html);
        partner_id = $("#partner_id_am").val();
        service_id = $("#service_id_am").val();
        request_type = getMultipleSelectedValues("request_type_am");
        free_paid = $("#free_paid_am").val();
        upcountry = $("#upcountry_am").val();
        dateRange = $("#completed_daterange_id_am").val();
        dateArray = dateRange.split(" - ");
        startDate = dateArray[0];
        endDate = dateArray[1];
        status = $("#completed_status_am").val();

        if(!partner_id){
            partner_id = "not_set";
        }   
        if(!service_id){
            service_id = "not_set";
        }
        if(!request_type){
            request_type = "not_set";
        }
        if(!free_paid){
            free_paid = "not_set";
        }
        if(!upcountry){
            upcountry = "not_set";
        }         
        if(!status){
            status = "not_set";
        }

        url =  baseUrl + "/employee/dashboard/get_booking_tat_report/"+startDate+"/"+endDate+"/"+status+"/"+service_id+"/"+request_type+"/"+free_paid+"/"+upcountry+"/Brand/0/"+partner_id;
        var data = {am:am_id};
        sendAjaxRequest(data,url,post_request).done(function(response){
            create_brand_tat_report_table(am_id, JSON.parse(response));
        });
    }

    function create_brand_tat_report_table(tableRow,data){
        html_leg_tbl = "";
        if(!!data.leg_1 && data.leg_1.length>0){
            html_leg_tbl = "<th></th>";
        }
        html='<table class="table table-striped table-bordered sub-table">'
                +'<thead><tr><th>S.no</th><th>Brand</th>'
                +html_leg_tbl
                +'<th>D0</th><th>D1</th><th>D2</th><th>D3</th><th>D4</th><th>D5 - D7</th><th>D8 - D15</th><th>> D15</th></tr></thead>';
        if(!!data.TAT && data.TAT.length>0){
            html += "<tbody>";
            for(var i in data.TAT){
                html += '<tr>';
                html += "<td>"+ (parseInt(i)+1)+ "</td>";
                if(data.TAT[i].id === "00"){
                    html += "<td>"+ data.TAT[i].entity+ "</td>";
                }
                else {
                    html += "<td><button type='button' id='brand_"+ data.TAT[i].id+ "' class='btn btn-info' target='_blank' onclick=\"open_full_view(this.id,'<?php echo base_url(); ?>employee/dashboard/tat_calculation_full_view/','1','0','am_completed_booking_form','Brand','"+data.TAT[i].sub_id+"')\">"+ data.TAT[i].entity+ "</button></td>";
                }
                // Show leg wise Brand Report
                if(!!data.leg_1 && data.leg_1.length>0){
                    html += "<td>leg_1<br/>leg_2<br/>Total</td>";
                    html += "<td>"
                            + data.leg_1[i].TAT_0+ "("+ data.leg_1[i].TAT_0_per+ "%)<br/>"
                            + data.leg_2[i].TAT_0+ "("+ data.leg_2[i].TAT_0_per+ "%)<br/>"
                            + data.TAT[i].TAT_0+ "("+ data.TAT[i].TAT_0_per+ "%)"
                            + "</td>";
                    html += "<td>"
                            + data.leg_1[i].TAT_1+ "("+ data.leg_1[i].TAT_1_per+ "%)<br/>"
                            + data.leg_2[i].TAT_1+ "("+ data.leg_2[i].TAT_1_per+ "%)<br/>"
                            + data.TAT[i].TAT_1+ "("+ data.TAT[i].TAT_1_per+ "%)"
                            + "</td>";
                    html += "<td>"
                            + data.leg_1[i].TAT_2+ "("+ data.leg_1[i].TAT_2_per+ "%)<br/>"
                            + data.leg_2[i].TAT_2+ "("+ data.leg_2[i].TAT_2_per+ "%)<br/>"
                            + data.TAT[i].TAT_2+ "("+ data.TAT[i].TAT_2_per+ "%)"
                            + "</td>";
                    html += "<td>"
                            + data.leg_1[i].TAT_3+ "("+ data.leg_1[i].TAT_3_per+ "%)<br/>"
                            + data.leg_2[i].TAT_3+ "("+ data.leg_2[i].TAT_3_per+ "%)<br/>"
                            + data.TAT[i].TAT_3+ "("+ data.TAT[i].TAT_3_per+ "%)"
                            + "</td>";
                    html += "<td>"
                            + data.leg_1[i].TAT_4+ "("+ data.leg_1[i].TAT_4_per+ "%)<br/>"
                            + data.leg_2[i].TAT_4+ "("+ data.leg_2[i].TAT_4_per+ "%)<br/>"
                            + data.TAT[i].TAT_4+ "("+ data.TAT[i].TAT_4_per+ "%)"
                            + "</td>";
                    html += "<td>"
                            + data.leg_1[i].TAT_5+ "("+ data.leg_1[i].TAT_5_per+ "%)<br/>"
                            + data.leg_2[i].TAT_5+ "("+ data.leg_2[i].TAT_5_per+ "%)<br/>"
                            + data.TAT[i].TAT_5+ "("+ data.TAT[i].TAT_5_per+ "%)"
                            + "</td>";
                    html += "<td>"
                            + data.leg_1[i].TAT_8+ "("+ data.leg_1[i].TAT_8_per+ "%)<br/>"
                            + data.leg_2[i].TAT_8+ "("+ data.leg_2[i].TAT_8_per+ "%)<br/>"
                            + data.TAT[i].TAT_8+ "("+ data.TAT[i].TAT_8_per+ "%)"
                            + "</td>";
                    html += "<td>"
                            + data.leg_1[i].TAT_16+ "("+ data.leg_1[i].TAT_16_per+ "%)<br/>"
                            + data.leg_2[i].TAT_16+ "("+ data.leg_2[i].TAT_16_per+ "%)<br/>"
                            + data.TAT[i].TAT_16+ "("+ data.TAT[i].TAT_16_per+ "%)"
                            + "</td>";
                    html += '</tr>';
                }
                else
                {
                    html += "<td>"+ data.TAT[i].TAT_0+ "("+ data.TAT[i].TAT_0_per+ "%)</td>";
                    html += "<td>"+ data.TAT[i].TAT_1+ "("+ data.TAT[i].TAT_1_per+ "%)</td>";
                    html += "<td>"+ data.TAT[i].TAT_2+ "("+ data.TAT[i].TAT_2_per+ "%)</td>";
                    html += "<td>"+ data.TAT[i].TAT_3+ "("+ data.TAT[i].TAT_3_per+ "%)</td>";
                    html += "<td>"+ data.TAT[i].TAT_4+ "("+ data.TAT[i].TAT_4_per+ "%)</td>";
                    html += "<td>"+ data.TAT[i].TAT_5+ "("+ data.TAT[i].TAT_5_per+ "%)</td>";
                    html += "<td>"+ data.TAT[i].TAT_8+ "("+ data.TAT[i].TAT_8_per+ "%)</td>";
                    html += "<td>"+ data.TAT[i].TAT_16+ "("+ data.TAT[i].TAT_16_per+ "%)</td>";
                    html += '</tr>';
                }                
            }

            html += "</tbody></table>";            
            if(!!data.leg_1 && data.leg_1.length>0){
                html = "<td colspan=11>"+ html+ "</td>"
            }
            else
            {
                html = "<td colspan=10>"+ html+ "</td>"
            }
            $("#brand_table_"+ tableRow).empty().html(html);
            $("tr.am-tat-report[data-am-row-id='"+ tableRow+ "']").data("has_data", true);
        }else{
            html += "<tbody><tr><td colspan=10 class='text-center'>No Data.</td></tr></tbody></table>";
            html = "<td colspan=10>"+ html+ "</td>"
            $("#brand_table_"+ tableRow).empty().html(html);
            $("tr.am-tat-report[data-am-row-id='"+ tableRow+ "']").data("has_data", false);
        }
    }
    
    // Functions to get Brand Wise AM open calls Data
    function get_brand_open_call_details_for_am(am_id){
        if($("tr.open-call-report-am[data-am-row-id='"+ am_id+ "']").data("has_data")){
            if($("#brand_open_call_table_"+ am_id).length>0){
                if($("#brand_open_call_table_"+ am_id).is(":hidden")){
                    $("span.open-call-report-am[data-am-id='"+ am_id+ "']").find("i").removeClass("fa-plus-square").addClass("fa-minus-square");
                }else{
                    $("span.open-call-report-am[data-am-id='"+ am_id+ "']").find("i").removeClass("fa-minus-square").addClass("fa-plus-square");
                }
                $("#brand_open_call_table_"+ am_id).slideToggle();
                return false;
            }
        }else{
            if($("#brand_open_call_table_"+ am_id).length>0){
                if(!$("#brand_open_call_table_"+ am_id).is(":hidden")){
                    $("span.open-call-report-am[data-am-id='"+ am_id+ "']").find("i").removeClass("fa-minus-square").addClass("fa-plus-square");
                    $("#brand_open_call_table_"+ am_id).remove();
                    return false;
                }
            }
        }
        $("span.open-call-report-am[data-am-id='"+ am_id+ "']").find("i").removeClass("fa-plus-square").addClass("fa-minus-square");
        var html = "<tr id='brand_open_call_table_"+ am_id +"' class='brand-open-call-table'><td class='text-center' colspan=11><img src='<?php echo base_url(); ?>images/loadring.gif' ></td><tr>";
        $("tr.open-call-report-am[data-am-row-id='"+ am_id+ "']").after(html);
        dateRange = $("#pending_daterange_id_am").val();
        dateArray = dateRange.split(" - ");
        startDate = dateArray[0];
        endDate = dateArray[1];
        service_id = $("#service_id_am_pending").val();
        partner_id = $("#partner_id_am_pending").val();
        request_type = getMultipleSelectedValues("request_type_am_pending");
        free_paid = $("#free_paid_am_pending").val();
        upcountry = getMultipleSelectedValues("upcountry_am_pending");
        status = getMultipleSelectedValues("pending_dependency_am");
        if(!status){
            status = "not_set";
        }
        if(!service_id){
            service_id = "not_set";
        }
        if(!request_type){
            request_type = "not_set";
        }
        if(!free_paid){
            free_paid = "not_set";
        }
         if(!upcountry){
            upcountry = "not_set";
        }
        if(!partner_id){
            partner_id = "not_set";
        }

        url =  baseUrl + "/employee/dashboard/get_booking_tat_report/"+startDate+"/"+endDate+"/"+status+"/"+service_id+"/"+request_type+"/"+free_paid+"/"+upcountry+"/Brand/Pending/"+partner_id;
        var data = {am:am_id};
        sendAjaxRequest(data,url,post_request).done(function(response){
            create_brand_open_call_tat_report_table(am_id, JSON.parse(response));
        });
    }
    
    // HTML for Brand wise Open Call Table
    function create_brand_open_call_tat_report_table(tableRow,data){
        html='<table class="table table-striped table-bordered sub-table">'
                +'<thead><tr><th>S.no</th><th>Brand</th><th>D0</th><th>D1</th><th>D2</th><th>D3</th><th>D4</th><th>D5 - D7</th><th>D8 - D15</th><th>> D15</th><th>Total</th></tr></thead>';
        if(!!data && data.length>0){
            html += "<tbody>";
            for(var i in data){
                var total = 0;
                html += '<tr>';
                html += "<td>"+ (parseInt(i)+1)+ "</td>";
                if(data[i].id === "00"){
                    html += "<td>"+ data[i].entity+ "</td>";
                    html += '<td>'+ data[i].TAT_0+ ' ('+ data[i].TAT_0_per+ '%)</td>';
                    total += data[i].TAT_0;
                    html += '<td>'+ data[i].TAT_1+ ' ('+ data[i].TAT_1_per+ '%)</td>'; 
                    total += data[i].TAT_1;
                    html += '<td>'+ data[i].TAT_2+ ' ('+ data[i].TAT_2_per+ '%)</td>';
                    total += data[i].TAT_2;
                    html += '<td>'+ data[i].TAT_3+ ' ('+ data[i].TAT_3_per+ '%)</td>';
                    total += data[i].TAT_3;
                    html += '<td>'+ data[i].TAT_4+ ' ('+ data[i].TAT_4_per+ '%)</td>';
                    total += data[i].TAT_4;
                    html += '<td>'+ data[i].TAT_5+ ' ('+ data[i].TAT_5_per+ '%)</td>';
                    total += data[i].TAT_5;
                    html += '<td>'+ data[i].TAT_8+ ' ('+ data[i].TAT_8_per+ '%)</td>';
                    total += data[i].TAT_8;
                    html += '<td>'+ data[i].TAT_16+ ' ('+ data[i].TAT_16_per+ '%)</td>';
                    total += data[i].TAT_16;
                    html += '<td>'+ data[i].Total_Pending + " ("+ data[i].TAT_total_per+ "%) </td>";                
                }else{
                    var entity_type = "Brand";
                    html += "<td><button type='button' id='vendor_"+ data[i].id+ "' class='btn btn-info' target='_blank' onclick=\"open_full_view(this.id,'<?php echo base_url(); ?>employee/dashboard/tat_calculation_full_view/','1','Pending','am_pending_booking_form','"+entity_type+"','"+data[i].sub_id+"')\">"+ data[i].entity+ "</button></td>";
                    html += '<td><form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank" style="width: 8%;">'
                            +'<input type="hidden" name="booking_id_status" value="'+ data[i].TAT_0_bookings+ '">'
                            +'<input type="submit" value="'+ data[i].TAT_0+ ' ('+ data[i].TAT_0_per+ '%)"  class="btn btn-success">'
                             +'</form></td>';
                    total += data[i].TAT_0;
                    html += '<td><form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank" style="width: 8%;">'
                                +'<input type="hidden" name="booking_id_status" value="'+ data[i].TAT_1_bookings+ '">'
                                +'<input type="submit" value="'+ data[i].TAT_1+ ' ('+ data[i].TAT_1_per+ '%)"  class="btn btn-success">'
                                 +'</form></td>';
                    total += data[i].TAT_1;
                    html += '<td><form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank" style="width: 8%;">'
                                +'<input type="hidden" name="booking_id_status" value="'+ data[i].TAT_2_bookings+ '">'
                                +'<input type="submit" value="'+ data[i].TAT_2+ ' ('+ data[i].TAT_2_per+ '%)"  class="btn btn-'+ ((data[i].TAT_2<1)?'success':'danger')+ '">'
                                 +'</form></td>';
                    total += data[i].TAT_2;
                    html += '<td><form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank" style="width: 8%;">'
                                +'<input type="hidden" name="booking_id_status" value="'+ data[i].TAT_3_bookings+ '">'
                                +'<input type="submit" value="'+ data[i].TAT_3+ ' ('+ data[i].TAT_3_per+ '%)"  class="btn btn-'+ ((data[i].TAT_3<1)?'success':'danger')+ '">'
                                 +'</form></td>';
                    total += data[i].TAT_3;
                    html += '<td><form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank" style="width: 8%;">'
                                +'<input type="hidden" name="booking_id_status" value="'+ data[i].TAT_4_bookings+ '">'
                                +'<input type="submit" value="'+ data[i].TAT_4+ ' ('+ data[i].TAT_4_per+ '%)"  class="btn btn-'+ ((data[i].TAT_4<1)?'success':'danger')+ '">'
                                 +'</form></td>';
                    total += data[i].TAT_4;
                    html += '<td><form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank" style="width: 8%;">'
                                +'<input type="hidden" name="booking_id_status" value="'+ data[i].TAT_5_bookings+ '">'
                                +'<input type="submit" value="'+ data[i].TAT_5+ ' ('+ data[i].TAT_5_per+ '%)"  class="btn btn-'+ ((data[i].TAT_5<1)?'success':'danger')+ '">'
                                 +'</form></td>';
                    total += data[i].TAT_5;
                    html += '<td><form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank" style="width: 8%;">'
                                +'<input type="hidden" name="booking_id_status" value="'+ data[i].TAT_8_bookings+ '">'
                                +'<input type="submit" value="'+ data[i].TAT_8+ ' ('+ data[i].TAT_8_per+ '%)"  class="btn btn-'+ ((data[i].TAT_8<1)?'success':'danger')+ '">'
                                 +'</form></td>';
                    total += data[i].TAT_8;
                    html += '<td><form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank" style="width: 8%;">'
                                +'<input type="hidden" name="booking_id_status" value="'+ data[i].TAT_16_bookings+ '">'
                                +'<input type="submit" value="'+ data[i].TAT_16+ ' ('+ data[i].TAT_16_per+ '%)"  class="btn btn-'+ ((data[i].TAT_16<1)?'success':'danger')+ '">'
                                 +'</form></td>';
                    total += data[i].TAT_16;
                    html += '<td><form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank" style="width: 8%;">'
                                +'<input type="hidden" name="booking_id_status" value="'+ data[i].TAT_Total_bookings + '">'
                                +'<input type="submit" value="'+ data[i].Total_Pending + ' ('+ data[i].TAT_total_per + '%)"  class="btn btn-'+ ((data[i].Total_Pending<1)?'success':'danger')+ '">'
                                 +'</form></td>';
                }
                html += '</tr>';
            }
            html += "</tbody></table>";
            html = "<td colspan=11>"+ html+ "</td>"
            $("#brand_open_call_table_"+ tableRow).empty().html(html);
            $("tr.open-call-report-am[data-am-row-id='"+ tableRow+ "']").data("has_data", true);
        }else{
            html += "<tbody><tr><td colspan=11 class='text-center'>No Data.</td></tr></tbody></table>";
            html = "<td colspan=11>"+ html+ "</td>"
            $("#brand_open_call_table_"+ tableRow).empty().html(html);
            $("tr.open-call-report-am[data-am-row-id='"+ tableRow+ "']").data("has_data", false);
        }
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
