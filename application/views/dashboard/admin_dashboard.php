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
                <div class="x_title">
                    <h2>RM TAT Reporting</h2>
                    <span class="collape_icon" href="#RM_TAT_Reporting" data-toggle="collapse" onclick="collapse_icon_change(this)"><i class="fa fa-minus-square" aria-hidden="true"></i></span>
                    <div class="clearfix"></div>
                </div>
                <div id="RM_TAT_Reporting" class="collapse in">
                <div class="table-responsive" id="escalation_data" ng-controller="completedBooking_Controller" ng-cloak="">
                    <div class="col-md-3" style="margin: 0px;padding: 0px 1px;width: 160px;">
                        <div class="item form-group">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <label for="">Services</label>
                                <select class="form-control filter_table" id="service_id" name="services">
                                    <option value="" selected="selected">All</option>
                                    <?php foreach($services as $val){ ?>
                                    <option value="<?php echo $val['id']?>"><?php echo $val['services']?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                        <div class="form-group col-md-3">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <label for="">Request Type</label>
                            <select class="form-control filter_table" id="request_type" name="request_type[]" multiple="">
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
                            <label for="">Is Free</label>
                            <select class="form-control filter_table" id="free_paid" name="free_paid">
                                <option value="" selected="selected">All</option>
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
                                <option value="">All</option>
                                <option value="Yes">Yes</option>
                                 <option value="No" selected="selected">No</option>
                            </select>
                        </div>
                    </div>
                </div>
                    <div class="form-group col-md-3">
                                         <label for="">Booking Completed Date</label>
                                         <input type="text" class="form-control" name="daterange_completed_bookings" id="completed_daterange_id" ng-change="ShowRMCompletedBookingBYDateRange()" ng-model="dates">
                            </div>
                    <div class="form-group col-md-3">
                                         <label for="">Booking Status</label>
                                        <select class="form-control"  ng-model="status" id="completed_status">
                                            <option value="">All</option>
                                            <option value="Completed" ng-selected="true">Completed</option>
                                            <option value="Cancelled">Cancelled</option>
                                        </select>
                    </div>
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
                             <th>> D15</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat="x in completedBookingByRM | orderBy:'TAT_16'">
                           <td>{{$index+1}}</td>
                           <td><a type="button" id="vendor_{{x.id}}" class="btn btn-info" target="_blank" href="<?php echo base_url(); ?>employee/dashboard/tat_calculation_full_view/{{x.id}}">{{x.RM}}</a></td>
                           <td>{{x.TAT_0}} <br> ({{x.TAT_0_per}}%) </td>
                           <td>{{x.TAT_1}} <br> ({{x.TAT_1_per}}%) </td>
                           <td>{{x.TAT_2}} <br> ({{x.TAT_2_per}}%)</td>
                           <td>{{x.TAT_3}} <br> ({{x.TAT_3_per}}%)</td>
                           <td>{{x.TAT_4}} <br> ({{x.TAT_4_per}}%)</td>
                           <td>{{x.TAT_5}} <br> ({{x.TAT_5_per}}%) </td>
                           <td>{{x.TAT_8}} <br> ({{x.TAT_8_per}}%)</td>
                           <td>{{x.TAT_16}} <br> ({{x.TAT_16_per}}%)</td>
                        </tr>
                    </tbody>
                </table>
                <center><img id="loader_gif_completed_rm" src="<?php echo base_url(); ?>images/loadring.gif" ></center>
            </div>
            </div> 
            </div>
        </div>
    </div>
    <!-- Partner Booking Status -->
       <div class="row" style="margin-bottom: 10px;">
        <div class="col-md-12 col-sm-12 col-xs-12 dashboard_graph" style="">
<!--            <div class="x_panel">-->
                <div class="row x_title">
                    <div class="col-md-6">
                        <h3>Partner Booking Status &nbsp;&nbsp;&nbsp;</h3>
                    </div>
                    <div class="col-md-5">
                        <div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; margin-right: -12%;">
                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                            <span></span> <b class="caret"></b>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <span class="collape_icon" href="#chart_container_div" data-toggle="collapse" onclick="collapse_icon_change(this)" style="margin-right: 8px;"><i class="fa fa-minus-square" aria-hidden="true"></i></span>
                    </div>
                </div>
                <div class="collapse in" id="chart_container_div">
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
                <div class="x_title">
                    <h2>AM TAT Reporting</h2>
                    <span class="collape_icon" href="#AM_TAT_Reporting" data-toggle="collapse" onclick="initiate_AM_TAT_Reporting(this)"><i class="fa fa-plus-square" aria-hidden="true"></i></span>
                    <div class="clearfix"></div>
                </div>
                <div id="AM_TAT_Reporting" class="collapse">
                <div class="table-responsive" id="escalation_data" ng-controller="completedBooking_ControllerAM" ng-cloak="">
                    <div class="col-md-3" style="margin: 0px;padding: 0px 1px;width: 160px;">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <label for="">Services</label>
                            <select class="form-control filter_table" id="service_id_am" name="services">
                                <option value="" selected="selected">All</option>
                                <?php foreach($services as $val){ ?>
                                <option value="<?php echo $val['id']?>"><?php echo $val['services']?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                    <div class="form-group col-md-3">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <label for="">Request Type</label>
                            <select class="form-control filter_table" id="request_type_am" name="request_type_am" multiple="">
                                <option value="">All</option>
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
                            <label for="">Is Free</label>
                            <select class="form-control filter_table" id="free_paid_am" name="free_paid_am">
                                <option value="" selected="selected">All</option>
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
                            <select class="form-control filter_table" id="upcountry_am" name="upcountry_am">
                                <option value="">All</option>
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
                                        <select class="form-control"  ng-model="status" id="completed_status_am">
                                            <option value="">All</option>
                                            <option value="Completed" ng-selected="true">Completed</option>
                                            <option value="Cancelled">Cancelled</option>
                                        </select>
                    </div>
                    <button class="btn btn-primary" ng-click="ShowAMCompletedBookingBYDateRange()" ng-model="partner_dashboard_filter" style="margin-top: 23px;background: #405467;border-color: #405467;">Apply Filters</button>
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
                        <tr ng-repeat="x in completedBookingByAM | orderBy:'TAT_16'">
                           <td>{{$index+1}}</td>
                           <td><a type="button" id="vendor_{{x.id}}" class="btn btn-info" target="_blank" href="<?php echo base_url(); ?>employee/dashboard/tat_calculation_full_view/{{x.id}}/0/1">{{x.AM}}</a></td>
                           <td>{{x.TAT_0}} <br> ({{x.TAT_0_per}}%) </td>
                           <td>{{x.TAT_1}} <br> ({{x.TAT_1_per}}%) </td>
                           <td>{{x.TAT_2}} <br> ({{x.TAT_2_per}}%)</td>
                           <td>{{x.TAT_3}} <br> ({{x.TAT_3_per}}%)</td>
                           <td>{{x.TAT_4}} <br> ({{x.TAT_4_per}}%)</td>
                           <td>{{x.TAT_5}} <br> ({{x.TAT_5_per}}%) </td>
                           <td>{{x.TAT_8}} <br> ({{x.TAT_8_per}}%)</td>
                           <td>{{x.TAT_16}} <br> ({{x.TAT_16_per}}%)</td>
                        </tr>
                    </tbody>
                </table>
                <center><img id="loader_gif_pending_AM" src="<?php echo base_url(); ?>images/loadring.gif" ></center>
            </div>
            </div>
            </div>
        </div>
    </div>  
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0px !important;">
            <div class="x_panel" ng-controller="pendngBooking_Controller" ng-cloak="">
                <div class="x_title">
                    <h2>RM Pending Booking Report</h2>
                     <span class="collape_icon" href="#RM_Pending_Booking_Report_div" data-toggle="collapse" ng-click="callloadPendingBookingView(this)"><i class="fa fa-plus-square" aria-hidden="true"></i></span>
                     <div class="clearfix"></div>
                </div>
                <div id="RM_Pending_Booking_Report_div" class="collapse">
            <div class="table-responsive" id="escalation_data">
                                    <div class="form-group" style="float:right;">
                                         <label for="">Dependency On</label>
                                        <select class="form-control" ng-change="ShowBookingActorView()" ng-model="actor" id="actor">
                                            <option value="all" ng-selected="true">All</option>
                                            <option value="Vendor">Vendor</option>
                                            <option value="Partner">Partner</option>
                                            <option value="247Around">247Around</option>  
                                        </select>
                            </div>
                <br>
                <div class="clear"></div>
                <table class="table table-striped table-bordered jambo_table bulk_action">
                    <thead>
                        <tr>
                            <th>S.no</th>
                            <th>RM</th>
                            <th>0 to 2 days(Installation)</th>
                            <th>3 to 5 Days(Installation)</th>
                            <th> >5 Days(Installation)</th>
                            <th>0 to 2 days(Repair)</th>
                            <th>3 to 5 Days(Repair)</th>
                            <th>>5 Days(Repair)</th>
                             <th>Total Pending</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat="x in pendingBookingByRM | orderBy:'-total_pending'">
                           <td>{{$index+1}}</td>
                           <td><a type="button" id="vendor_{{x.rmID}}" class="btn btn-info" target="_blank" href="<?php echo base_url(); ?>employee/dashboard/pending_full_view_by_sf/{{x.rmID}}">{{x.rm}}</a></td>
                           <td>{{x.last_2_day_installation_booking_count}}</td>
                           <td class="text_warning">{{x.last_3_to_5_days_installation_count}}</td>
                           <td class="text_warning">{{x.more_then_5_days_installation_count}}</td>
                           <td>{{x.last_2_day_repair_booking_count}}</td>
                            <td class="text_warning">{{x.last_3_to_5_days_repair_count}}</td>
                           <td class="text_warning">{{x.more_then_5_days_repair_count}}</td>
                           <td>{{x.total_pending}}</td>
                        </tr>
                    </tbody>
                </table>
                <center><img id="loader_gif_pending" src="<?php echo base_url(); ?>images/loadring.gif" ></center>
            </div>
            </div>
            </div>
<!--          </div>-->
       </div>
    </div>
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
                    <h2>Missing Pincodes</h2>
                    <span class="collape_icon" href="#pincode_table_data_div" data-toggle="collapse" onclick="get_missing_pincodes(this)"><i class="fa fa-plus-square" aria-hidden="true"></i></span>
                    <a id="download_pin_code" class="btn btn-success btn-xs" href="<?php echo base_url(); ?>employee/vendor/insert_pincode_form" style="float:right">Add New Pincode</a>
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

     <div class="row">
        <!-- Partner Spare Parts Details -->
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0px !important;">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Partner Spare Parts Details <span class="badge badge-info" data-toggle="popover" data-content="Below table shows parts which are OOT with respect to sf (after 7 days from booking completion by sf)"><i class="fa fa-info"></i></span></h2>
                    <span class="collape_icon" href="#spare_details_by_partner_div" data-toggle="collapse" onclick="spare_details_by_partner(this);"><i class="fa fa-plus-square" aria-hidden="true"></i></span>
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
    
    
    <!-- Escalation Start-->
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0px !important;">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Escalation</h2>
                    <span class="collape_icon" href="#escalation_data_div" data-toggle="collapse" onclick="initiate_escalation_data(this)"><i class="fa fa-plus-square" aria-hidden="true"></i></span>
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
                    <span class="collape_icon" href="#monthly_booking_chart_div" data-toggle="collapse" onclick="around_monthly_data(this);"><i class="fa fa-plus-square" aria-hidden="true"></i></span>
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
                    <div class="col-md-1" style="padding-right: 0px;"><span class="collape_icon" href="#state_type_booking_chart_div" data-toggle="collapse" onclick="get_bookings_data_by_rm(this)"><i class="fa fa-plus-square" aria-hidden="true"></i></span></div>
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
                        <span class="collape_icon" href="#chart_container2_div" data-toggle="collapse" onclick="agent_daily_report_call(this)" style="margin-right: 8px;"><i class="fa fa-plus-square" aria-hidden="true"></i></span>
                    </div>
                </div>
                <div class="x_content collapse" id="chart_container2_div">
                    <div class="col-md-12">
                        <center><img id="loader_gif4" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center>
                    </div>
                    <div id="chart_container2" class="chart_containe2"></div>
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
    
    
    $(document).ready(function(){
        $('[data-toggle="popover"]').popover({
            placement : 'top',
            trigger : 'hover'
        });
        
        var d = new Date();
        n = d.getMonth();
        y = d.getFullYear();
        date = d.getDate();
        // $('input[name="daterange_completed_bookings"]').daterangepicker({
        $('input[id="completed_daterange_id"]').daterangepicker({
            timePicker: true,
            timePickerIncrement: 30,
            locale: {
                format: 'YYYY-MM-DD'
            },
            startDate: y+'-'+n+'-'+date
        });
        
        partner_booking_status(start.format('MMMM D, YYYY'), end.format('MMMM D, YYYY'));
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
        
    });
    
    function initiate_AM_TAT_Reporting(span){
        collapse_icon_change(span);
        var d = new Date();
        n = d.getMonth();
        y = d.getFullYear();
        date = d.getDate();
        // $('input[name="daterange_completed_bookings"]').daterangepicker({
        $('input[id="completed_daterange_id_am"]').daterangepicker({
            timePicker: true,
            timePickerIncrement: 30,
            locale: {
                format: 'YYYY-MM-DD'
            },
            startDate: y+'-'+n+'-'+date
        });
    }
   
    function agent_daily_report_call(span){ 
        collapse_icon_change(span);
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
    
    function get_missing_pincodes(span){
        collapse_icon_change(span);
        var data = {};
        url = '<?php echo base_url(); ?>employee/dashboard/get_pincode_not_found_sf_details_admin';
        data['partner_id'] = '';
        
        sendAjaxRequest(data,url,post_request).done(function(response){
            $("#pincode_table_data").html(response);
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
    
    function around_monthly_data(span){
        collapse_icon_change(span);
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
    
    function get_bookings_data_by_rm(span){
        collapse_icon_change(span);
        $('#loader_gif3').fadeIn();
        $('#state_type_booking_chart').fadeOut();
        var data = {};
        url =  '<?php echo base_url(); ?>employee/dashboard/get_booking_data_by_region';
        
        sendAjaxRequest(data,url,post_request).done(function(response){
            create_chart_based_on_bookings_state(response);
        });
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
    function spare_details_by_partner(span){
        collapse_icon_change(span);
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

function initiate_escalation_data(span){
    collapse_icon_change(span);
    var d = new Date();
    n = d.getMonth()+1;
    y = d.getFullYear();
    date = d.getDate();
    $('input[name="daterange"]').daterangepicker({
        timePicker: true,
        timePickerIncrement: 30,
        locale: {
            format: 'YYYY-MM-DD'
        },
        startDate: y+'-'+n+'-01'
    });
}

function initiate_pending_booking_controller(span){
    collapse_icon_change(span);
    $("#pending_booking_btn").click();
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
        width: 166px !important;
}
.select2-selection--multiple{
        border: 1px solid #ccc !important;
    border-radius: 0px !important;
}
</style>
