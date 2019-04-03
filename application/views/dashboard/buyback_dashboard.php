<script src="<?php echo base_url(); ?>js/base_url.js"></script>

<script src = "https://ajax.googleapis.com/ajax/libs/angularjs/1.3.14/angular.min.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/app.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/controller/controllers.js"></script>
<style>
    .col-md-3{
        width: 24%;
    }
    #title_count .col-md-3 {
    width: 24.65% !important;
}
</style>
<div class="right_col" role="main" ng-app="buyback_dashboard">
    <?php if($this->session->userdata('user_group') === 'admin'){ ?>
    <!-- buyback balance -->
    <div class="row bb_balance col-md-12 col-sm-12 col-xs-12" style="margin: 0px;padding: 0px;border: 1px solid #edecec;">
        <div class="container-fluid" style="background-color:#fff; padding-top: 9px;" ng-controller="bb_balance">
            <div ng-if="showLoader">
                <center><img id="loader_gif_unit" src="<?php echo base_url(); ?>images/loadring.gif" ></center>
            </div>
            <div ng-if="showBuybackBalance">
                <div class="col-md-12" style="margin-bottom:10px;" ng-cloak="">  
                    <div class="col-md-3 text-center la_balance">
                        <b>LA Balance : </b>
                        Rs. <span>{{la_balance}}</span>
                    </div>
                    <div class="col-md-3 text-center tv_balance">
                        <b>TV Balance : </b>
                        Rs. <span>{{tv_balance}}</span>
                    </div>
                    <div class="col-md-3 text-center tv_balance">
                        <b>Mobile Balance : </b>
                        Rs. <span>{{mobile_balance}}</span>
                    </div>
                    <div class="col-md-3 text-center total_balance">
                        <b>Total Balance : </b>
                        Rs. <u><a target="_blank" href="<?php echo base_url()?>buyback/buyback_process/buyback_full_balance"><span><u>{{total_balance}}</u></span></a></u>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- /buyback balance -->
    <?php } ?>
    <!-- top tiles -->
    <div class="col-md-12 col-sm-12 col-xs-12 tile_count" id="title_count" ng-controller="bb_dashboard_summary" style=" margin: 10px 0px;padding: 0px;border: 1px solid #edecec;background:#fff;">
        <center><img id="loader_gif_unit" src="<?php echo base_url(); ?>images/loadring.gif" ></center>
    </div>
    <!-- /top tiles -->
    
    <!-- Review Page Summary -->
     <div class="col-md-12 col-sm-12 col-xs-12" style=" margin: 0px;padding: 0px;border: 1px solid #edecec;margin-bottom: 10px;">
        <div class="col-md-12 col-sm-12 col-xs-12" style="background-color:#fff;padding-top: 15px;" ng-controller="review_page_summary">
            <div class="x_title">
                    <h2><button type="button"class="btn btn-default" style="float: right;margin-bottom: 10px;padding: 1px 4px;margin-top: 0px;font-size: 8px;margin-left: 5px;background: #f7a35c;
                                color: #fff;border: none;" data-toggle="tooltip"data-placement="left"title="1) Without CP invoice Without Reimbursement Invoice 
                                2) These orders Will not be invoices in upcoming Invoice cycle  3) Include orders whoes delivery date is older than last month  4) Not include Review Page Orders">?</button>Disputed Orders Summary
                                <small>Without Invoice And WIthout Reimbursement Orders</small></h2>
                    <div class="clearfix"></div>
                </div>
            <div ng-if="showLoaderReview">
                <center><img id="loader_gif_unit" src="<?php echo base_url(); ?>images/loadring.gif" ></center>
            </div>
            <div ng-if="showReviewDetails">
                <table class="table table-striped table-bordered jambo_table bulk_action">
                    <thead>
                    <th><b>S.N</b></th>
                    <th><b>Status</b></th>
                    <th><b>247Around Deduction</b></th>
                    <th><b>Count</b></th>
                    </thead>
                    <tbody>
                        <tr ng-repeat="x in faulty_bookings | orderBy:'TAT_16'">
                           <td>{{$index+1}}</td>
                           <td>{{x.status}}</td>
                           <td>{{x.amount}}</td>
                           <?php
                            $value = "{{x.status}}";
                            $temp = base_url().'buyback/buyback_process/show_without_invoices_orders/'.$value; 
                            $url = urldecode($temp);
                            ?>
                           <td>
                               <a style="color: red;" target="_blank" href="<?php echo $url ?>"><u>{{x.count}}</u></a></td>
<!--                           <td><a target="_blank">{{x.count}}</a></td>-->
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<!--      Orders Setteled On  clamied price but did not get reimburshment-->
         <div class="col-md-12 col-sm-12 col-xs-12" style=" margin: 0px;padding: 0px;border: 1px solid #edecec;margin-bottom: 10px;">
        <div class="col-md-12 col-sm-12 col-xs-12" style="background-color:#fff;padding-top: 15px;" ng-controller="claimed_price_order_summary">
            <div class="x_title">
                    <h2>Disputed Orders Summary  
                                <small>Settlement With CP on Claimed Price but not get refund from amazon (Only TV) </small></h2>
                    <div class="clearfix"></div>
                </div>
            <div ng-if="showLoaderClaimed">
                <center><img id="loader_gif_unit" src="<?php echo base_url(); ?>images/loadring.gif" ></center>
            </div>
            <div ng-if="showClaimedDetails">
                <table class="table table-striped table-bordered jambo_table bulk_action">
                    <thead>
                    <th><b>S.N</b></th>
                    <th><b>Status</b></th>
                    <th><b>247Around Hold Amount </b></th>
                    <th><b>Count</b></th>
                    </thead>
                    <tbody>
                        <tr ng-repeat="x in faulty_bookings | orderBy:'TAT_16'">
                           <td>{{$index+1}}</td>
                           <td>{{x.status}}</td>
                           <td>{{x.amount}}</td>
                           <?php
                            $value = "{{x.status}}";
                            $temp = base_url().'buyback/buyback_process/show_without_invoices_orders/'.$value.'/cp'; 
                            $url = urldecode($temp);
                            ?>
                           <td>
                               <a style="color: red;" target="_blank" href="<?php echo $url ?>"><u>{{x.count}}</u></a></td>
<!--                           <td><a target="_blank">{{x.count}}</a></td>-->
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!--      Orders Setteled On  clamied price but did not get reimburshment-->
         <div class="col-md-12 col-sm-12 col-xs-12" style=" margin: 0px;padding: 0px;border: 1px solid #edecec;margin-bottom: 10px;">
        <div class="col-md-12 col-sm-12 col-xs-12" style="background-color:#fff;padding-top: 15px;" ng-controller="review_details">
            <div class="x_title">
                    <h2>Disputed Orders Summary  
                                <small>Orders On Review Page </small></h2>
                    <div class="clearfix"></div>
                </div>
            <div ng-if="showLoaderReviewOrders">
                <center><img id="loader_gif_unit" src="<?php echo base_url(); ?>images/loadring.gif" ></center>
            </div>
            <div ng-if="showReviewOrderDetails">
                <table class="table table-striped table-bordered jambo_table bulk_action">
                    <thead>
                    <th><b>S.N</b></th>
                    <th><b>Age (Age Count From Order Date)</b></th>
                    <th><b>247around Deduction</b></th>
                    <th><b>Count</b></th>
                    </thead>
                    <tbody>
                        <tr ng-repeat="x in faulty_bookings | orderBy:'TAT_16'">
                           <td>{{$index+1}}</td>
                           <td>{{x.status}}</td>
                           <td>{{x.amount}}</td>
                           <?php
                            $value = "{{x.status}}";
                            $temp = base_url().'buyback/buyback_process/bb_order_review/'.$value; 
                            $url = urldecode($temp);
                            ?>
                           <td>
                               <a style="color: red;" target="_blank" href="<?php echo $url ?>"><u>{{x.count}}</u></a></td>
<!--                           <td><a target="_blank">{{x.count}}</a></td>-->
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Buyback Orders<small>Completed</small></h2>
                    <div class="clearfix"></div>
                </div>
                <div class="col-md-12">
                    <center><img id="bb_completed_orders_loader" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center>
                </div>
                <div class="x_content">
                    <div id="bb_completed_orders" style="width:100%; height:400px;" ></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- /top tiles -->
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Buyback Orders<small>Detailed  Description</small></h2>
                    <div class="clearfix"></div>
                </div>
                <div class="col-md-12">
                    <center><img id="bb_orders_details_loader" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center>
                </div>
                <div class="x_content">
                    <div id="bb_orders_details" style="width:100%; height:400px;" ></div>
                </div>
            </div>
        </div>
    </div>
    
    
    <div class="row" ng-controller="buyback_dashboardController">

        <div class="clearfix"></div>

        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Collection Partner <small>Amount Balance</small></h2>
                    <div class="clearfix"></div>
                </div>

                <div class="x_content">

                    <div class="table-responsive" id="table_data">
                        <center><img id="loader_gif_unit" src="<?php echo base_url(); ?>images/loadring.gif" ></center>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- END -->
</div>
<style>
    .dropdown:hover .dropdown-menu {
display: block;
}
</style>
<script>
    
    var post_request = 'POST';
    var get_request = 'GET';
    
    function login_to_vendor(vendor_id){
        var c = confirm('Login to Service Center CRM?');
        if(c){
            $.ajax({
                url:'<?php echo base_url()."employee/login/allow_log_in_to_vendor/" ?>'+vendor_id,
                success: function (data) {
                    window.open("<?php echo base_url()?>service_center/buyback/bb_order_details",'_blank');
                }
            });
            
        }else{
            return false;
        }
    }
    
    $(document).ready(function(){
        
        //company monthly data
        bb_completed_orders_monthly_data();
        bb_orders_detailed_monthly_data();
        
    });
    
    function sendAjaxRequest(postData, url,type) {
        return $.ajax({
            data: postData,
            url: url,
            type: type
        });
    }
    
     function bb_orders_detailed_monthly_data(){
        $('#bb_orders_details_loader').fadeIn();
        $('#bb_orders_details').fadeOut();
        var data = {sf_id:''};
        url =  '<?php echo base_url(); ?>buyback/buyback_process/get_bb_order_detail_data_by_month';
        sendAjaxRequest(data,url,post_request).done(function(response){
                get_bb_mothly_booking_orders_all_details(response);
        });
    }
    
    function get_bb_mothly_booking_orders_all_details(response){
        $('#bb_orders_details_loader').hide();
        var render_div = 'bb_orders_details';
        var data = JSON.parse(response);
        $('#'+render_div).fadeIn();
        chart = new Highcharts.Chart({
            chart: {
                renderTo: render_div,
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
                categories: data.months
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
                    name: 'Total',
                    data: data.total
                }, {
                    name: 'Pending',
                    data: data.pending
                },
                {
                    name: 'Completed',
                    data: data.completed
                }, {
                    name: 'Cancelled',
                    data: data.cancelled
                },
                {
                    name: 'InProcess',
                    data: data.inprocess
                },
                {
                    name: 'Disputed',
                    data: data.disputed
                }]
        });
    }
    
    
    function bb_completed_orders_monthly_data(){
        $('#bb_completed_orders_loader').fadeIn();
        $('#bb_completed_orders').fadeOut();
        var data = {sf_id:''};
        url =  '<?php echo base_url(); ?>buyback/buyback_process/get_bb_acknowledge_data_by_month';
        
        sendAjaxRequest(data,url,post_request).done(function(response){
            get_bb_mothly_booking_orders(response);
        });
    }
    
    function get_bb_mothly_booking_orders(response){
        
        $('#bb_completed_orders_loader').hide();
        var render_div = 'bb_completed_orders';
        var data = JSON.parse(response);
        var month = data.month.split(',');
        var count = JSON.parse("[" + data.count + "]");
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
                    name: 'Completed Orders',
                    data: count
                }]
        });
    }
</script>

<style>
    .tooltip {
  position: relative;
  display: inline-block;
  border-bottom: 1px dotted black;
}

.tooltip .tooltiptext {
  visibility: hidden;
  width: 120px;
  background-color: black;
  color: #fff;
  text-align: center;
  border-radius: 6px;
  padding: 5px 0;

  /* Position the tooltip */
  position: absolute;
  z-index: 1;
}

.tooltip:hover .tooltiptext {
  visibility: visible;
}
    </style>