<script src="<?php echo base_url(); ?>js/base_url.js"></script>

<script src = "https://ajax.googleapis.com/ajax/libs/angularjs/1.3.14/angular.min.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/app.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/controller/controllers.js"></script>
<style>
    .col-md-3{
        width: 24%;
    }
</style>
<div class="right_col" role="main" ng-app="buyback_dashboard">
    <?php if($this->session->userdata('user_group') === 'admin'){ ?>
    <!-- buyback balance -->
    <div class="row bb_balance">
        <div class="container-fluid" style="background-color:#fff;" ng-controller="bb_balance">
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
                        Rs. <a target="_blank" href="<?php echo base_url()?>buyback/buyback_process/buyback_full_balance"><span>{{total_balance}}</span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /buyback balance -->
    <?php } ?>
    <!-- top tiles -->
    <div class="row tile_count" id="title_count" ng-controller="bb_dashboard_summary">
        <center><img id="loader_gif_unit" src="<?php echo base_url(); ?>images/loadring.gif" ></center>
    </div>
    <!-- /top tiles -->
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
        
    });
    
    function sendAjaxRequest(postData, url,type) {
        return $.ajax({
            data: postData,
            url: url,
            type: type
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

