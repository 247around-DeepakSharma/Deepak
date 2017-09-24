<script src="<?php echo base_url(); ?>js/base_url.js"></script>

<script src = "https://ajax.googleapis.com/ajax/libs/angularjs/1.3.14/angular.min.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/app.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/controller/controllers.js"></script>
<style>
    .col-md-2 {
        width: 16.666667%;
    }
    .tile_count .tile_stats_count, ul.quick-list li {
        white-space: normal;
    }
</style>
<div class="right_col" role="main" ng-app="buyback_dashboard">
    <!-- top tiles -->
    <div class="row tile_count" id="title_count" ng-controller="bb_dashboard_summary">
        <center><img id="loader_gif_unit" src="<?php echo base_url(); ?>images/loadring.gif" ></center>
    </div>
    <!-- /top tiles -->

    <div class="row" ng-controller="buyback_dashboardController">

        <div class="clearfix"></div>

        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Collection Partner <small>Amount Balance</small></h2>
                    <div class="clearfix"></div>
                </div>

                <div class="x_content">

                    <p>Below Table shows <code>Collection Partner</code> Balance</p>

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

