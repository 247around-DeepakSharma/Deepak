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
      .select2-selection--multiple{
        width: 156px !important;
        border: 1px solid #aaa !important;
    }
</style>
<!-- page content -->
<div class="right_col" role="main">
    <!-- top tiles -->
    <div class="row tile_count" id="title_count">
        <div class="col-md-12">
            <center><img id="loader_gif_title" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center>
        </div>
    </div>
    <!-- /top tiles -->
    <hr>
    <?php if(isset($saas_flag) && (!$saas_flag)) { ?>
    <div class="row" style="margin-top:10px;">
        <!-- Partner Spare Parts Details -->
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Partner Spare Parts Details <span class="badge badge-info" data-toggle="popover" data-content="Below graph shows parts which are OOT with respect to sf (after 7 days from booking completion by sf)"><i class="fa fa-info"></i></span></h2>
                    <div class="clearfix"></div>
                </div>
                <div class="col-md-12">
                    <center><img id="loader_gif1" src="<?php echo base_url(); ?>images/loadring.gif"></center>
                </div>
                <div class="x_content">
                    <div id="spare_details_by_partner"></div>
                </div>
            </div>
        </div>
        <!-- End  Partner Spare Parts Details -->
    </div>
    <?php } ?>
    
    <div class="row" style="margin-top:10px;">
        <!-- SF Spare Parts Details -->
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Defective Parts Pending On SF <b>(OOT)</b> <span class="badge badge-info" data-toggle="popover" data-content="Below table shows defective parts pending which are OOT with respect to sf (after 7 days from booking completion by sf)"><i class="fa fa-info"></i></span> </h2>
                    <div class="nav navbar-right panel_toolbox">
                        <div class="pull-right">
                            <a href="<?php echo base_url();?>employee/dashboard/sf_oot_spare_full_view" class="btn btn-sm btn-success" target="_blank">Show All</a>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="col-md-12">
                    <center><img id="loader_gif2" src="<?php echo base_url(); ?>images/loadring.gif"></center>
                </div>
                <div class="x_content">
                    <div id="spare_details_by_sf" style="width:100%; display: none;" >
                        <table id="spare_details_by_sf_table" class="table table-bordered table-responsive" width="100%">
                            <thead>
                                <th>S.No.</th>
                                <th>Service Center</th>
                                <th>Spare Count</th>
                            </thead>
                            <tbody id="spare_details_by_sf_table_data"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- End SF Spare Parts Details-->
    </div>
    
    <?php if(isset($saas_flag) && (!$saas_flag)) { ?>
    <!-- SF Brackets snapshot Section -->
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <div class="col-md-6">
                        <h2>Service Center Brackets Inventory <span class="badge badge-info" data-toggle="popover" data-content="Below table show data about those sf who don't have brackets inventory.( Expected Days Left to Consume Brackets column tell that days left to consumne the brackets according to last 30 days wall mount booking.)"><i class="fa fa-info"></i></span></h2>
                    </div>
                    <div class="col-md-6">
                        <div class="pull-right">
                            <a class="btn btn-sm btn-success" href="<?php echo base_url();?>employee/dashboard/brackets_snapshot_full_view" target="_blank">Show All</a>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="table-responsive">
                        <div class="table-responsive">
                            <center><img id="brackets_loader" src="<?php echo base_url(); ?>images/loadring.gif"></center>
                            <table class="table table-striped table-bordered table-responsive" id="sf_brackets_table" style="display: none">
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
                                <tbody id="sf_brackets_table_data"></tbody>
                            </table>                      
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- SF Brackets Snapshot Section -->
    <?php } ?>
    
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
                                    <option value="" selected="selected">All</option>
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
                                <option value="" selected="selected">All</option>
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
                                <option value="">All</option>
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
                            <label for="">Is Free</label>
                            <select class="form-control filter_table" id="free_paid" name="free_paid">
                                <option value="" selected="selected">All</option>
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
                                <option value="">All</option>
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
                                            <option value="">All</option>
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
                        <tr ng-repeat="x in completedBookingByRM | orderBy:'TAT_16'">
                           <td>{{$index+1}}</td>
<!--                           <td><a type="button" id="vendor_{{x.id}}" class="btn btn-info" target="_blank" href="<?php echo base_url(); ?>employee/dashboard/tat_calculation_full_view/{{x.id}}">{{x.entity}}</a></td>-->
                           <td><button type="button" id="vendor_{{x.id}}" class="btn btn-info" target="_blank" 
                                       onclick="open_full_view(this.id,'<?php echo base_url(); ?>employee/dashboard/tat_calculation_full_view/','0','0','rm_completed_booking_form')">{{x.entity}}</button></td>
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
        </div>
    
    
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
    var partner_id = [];
    
    $(document).ready(function(){
        
        //top count data
        get_query_data();
        //partner spare status
        spare_details_by_partner();
        //sf spare status
        spare_details_by_sf();
        //get sf brackets details
        sf_brackets_details();
        
        $('[data-toggle="popover"]').popover({
            placement : 'top',
            trigger : 'hover'
        });
        
    });
    
    
    //this function is used to call ajax request
    function sendAjaxRequest(postData, url,type) {
        return $.ajax({
            data: postData,
            url: url,
            type: type
        });
    }
    
    //this function is used to get the header dashboard count
    function get_query_data(){
        $('#loader_gif_title').fadeIn();
        var data = {};
        url = '<?php echo base_url(); ?>employee/dashboard/execute_inventory_title_query';
        
        sendAjaxRequest(data,url,post_request).done(function(response){
            $('#loader_gif_title').hide();
            $('#title_count').html(response);
            $('#go_to_crm').show();
        });
    }
    
    
    //this function is used to get the spare details for partner
    function spare_details_by_partner(){
        url =  '<?php echo base_url(); ?>employee/dashboard/get_oot_spare_parts_count_by_partner';
        data = {};
        sendAjaxRequest(data,url,post_request).done(function(response){
            create_spare_parts_by_partner_chart(response);
        });
    }
    
    //this function is used to get the spare details for sf
    function spare_details_by_sf(){
        url =  '<?php echo base_url(); ?>employee/dashboard/get_spare_details_by_sf';
        data = {is_show_all:0};
        sendAjaxRequest(data,url,post_request).done(function(response){
            create_spare_parts_by_sf_table(response);
        });
    }
    
    //this function is used to get the brackets data of sf
    function sf_brackets_details(){
        url =  '<?php echo base_url(); ?>employee/inventory/get_inventory_snapshot';
        data = {is_show_all:false};
        sendAjaxRequest(data,url,post_request).done(function(response){
            create_sf_brackets_table(response);
        });
    }
    
    //this function is used to create chart for partner spare details
    function create_spare_parts_by_partner_chart(response){
        var data = JSON.parse(response);
        var partners_id = data.partner_id;
        var partners = data.partner_name.split(',');
        var spare_count = JSON.parse("[" + data.spare_count + "]");
        $('#loader_gif1').hide();
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
    
    function create_spare_parts_by_sf_table(response){
        obj = JSON.parse(response);
        $('#loader_gif2').hide();
        $('#spare_details_by_sf').fadeIn();
        var table_body_html = '';
        $.each(obj, function (index,val) {
            table_body_html += '<tr>';
            table_body_html += '<td>' + (Number(index)+1) +'</td>';
            table_body_html += '<td>' +val['name'] +'</td>';
            table_body_html += "<td><a href='#' onclick = show_dashboard_modal('"+val['booking_id']+"') >" +val['oot_defective_parts_count'] +"<a/></td>";
            table_body_html += '</tr>';
        });
        $('#spare_details_by_sf_table_data').html(table_body_html);
    }
    
    function create_sf_brackets_table(response){
        $('#brackets_loader').hide();
        $('#sf_brackets_table').fadeIn();
        var obj = JSON.parse(response);
        var table_body_html = '';
        $.each(obj, function (index,val) {
            if(index >= 5 ){
                return;
            }else{
                table_body_html += '<tr>';
                table_body_html += '<td>' + (Number(index)+1) +'</td>';
                table_body_html += '<td>' +val['sf_name'] +'</td>';
                table_body_html += '<td>' +val['l_32'] +'</td>';
                table_body_html += '<td>' +val['g_32'] +'</td>';
                table_body_html += '<td>' +val['brackets_exhausted_days'] +'</td>';
                table_body_html += "<td><a class='btn btn-sm btn-success' href='<?php echo base_url();?>employee/inventory/get_bracket_add_form/"+val['sf_id'] + "/"+ val['sf_name']+ "' target='_blank'>Order brackets</a></td>";
                table_body_html += '</tr>';
            }
            
        });
        $('#sf_brackets_table_data').html(table_body_html);
    }
    
    function show_dashboard_modal(modal_data){
        var modal_body = modal_data.split(',');
        var html = "<table class='table table-bordered table-hover table-responsive'><thead><th>Booking Id</th></thead><tbody>";
        $(modal_body).each(function(index,value){
            html += "<tr><td>";
            html += "<a href='/employee/user/finduser?search_value="+value+"' target='_blank'>"+value+"</a>";
            html += "</td></tr>";
        });
        html += "</tbody></table>";
        $('#open_model').html(html);
        $('#modalDiv').modal('show'); 
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
     function open_full_view(id,url,is_am,is_pending,form_id){
      entity_id = id.split("_")[1];
      final_url = url+entity_id+'/0/'+is_am+'/'+is_pending;
      $('#'+form_id).attr('action', final_url);
      $('#'+form_id).submit();
    }
</script>