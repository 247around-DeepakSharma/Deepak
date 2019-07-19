<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <!-- Meta, title, CSS, favicons, etc. -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Dashboard | 247around</title>
        <link rel="shortcut icon" href="<?php echo base_url();?>images/favicon.ico" />
        <!-- Bootstrap -->
        <link href="<?php echo base_url() ?>css/bootstrap.min.css" rel="stylesheet">
        <!-- Font Awesome -->
        <link href="<?php echo base_url() ?>font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <!-- NProgress -->
        <link href="<?php echo base_url() ?>css/nprogress.css" rel="stylesheet" type="text/css">
        <!-- iCheck -->
        <link href="<?php echo base_url() ?>assest/iCheck/skins/flat/green.css" rel="stylesheet">
        <!-- bootstrap-progressbar -->
        <link href="<?php echo base_url() ?>css/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet">
        <!-- bootstrap-daterangepicker -->
        <link href="<?php echo base_url() ?>css/daterangepicker.css" rel="stylesheet">
        <!-- Custom Theme Style -->
        <link href="<?php echo base_url() ?>css/dashboard_custom.min.css" rel="stylesheet">
        <!-- Sweet Alert Css -->
        <link href="<?php echo base_url() ?>css/sweetalert.css" rel="stylesheet">
        <!-- jQuery -->
        <link href="https://cdn.datatables.net/buttons/1.4.0/css/buttons.dataTables.min.css" rel="stylesheet">
        <script src="<?php echo base_url() ?>js/jquery.min.js"></script>
        <script src="<?php echo base_url();?>js/select2.min.js"></script>
         <link href="<?php echo base_url()?>css/select2.min.css" rel="stylesheet" />
        <!-- Highchart.js -->
        <script src="https://code.highcharts.com/highcharts.js"></script>
        <script src="https://code.highcharts.com/modules/data.js"></script>
        <script src="https://code.highcharts.com/modules/drilldown.js"></script>
        <script src="https://code.highcharts.com/modules/exporting.js"></script>
        <script src="https://highcharts.github.io/export-csv/export-csv.js"></script>
        <!-- bootstrap-daterangepicker -->
        <script src="<?php echo base_url() ?>js/moment.min.js"></script>
        <script src="<?php echo base_url() ?>js/daterangepicker.js"></script>
        <!-- DateJS -->
        <script src="<?php echo base_url() ?>assest/DateJS/build/date.js"></script>
        <!-- Select2 JS -->
        <script src="<?php echo base_url();?>js/select2.min.js"></script>
        <!-- sweet Alert JS -->
        <script src="<?php echo base_url();?>js/sweetalert.min.js"></script>
        <style>
             [ng\:cloak], [ng-cloak], [data-ng-cloak], [x-ng-cloak], .ng-cloak, .x-ng-cloak {
                display: none !important;
            }
            .highcharts-credits{display:none}
            #title_count .col-md-3 {
                width: 23%;
            }
            .tile_count .tile_stats_count, ul.quick-list li {
                white-space: normal;
            }
            .bb_balance {
                margin-right: -23px;
                margin-left: -23px;
            }
            .tile_count .tile_stats_count{color:#333;border: 1px solid #e5e5e5;margin: 2px;}
            .tile_count .tile_stats_count:hover{
                border: 1px solid #ccc;
                box-shadow: 0 0 10px #ccc;
                background: #fff;
            }
            .tile_count .tile_stats_count, ul.quick-list li {
                white-space: normal;
                overflow: visible;
                text-overflow: clip;
            }
            .tile_count .tile_stats_count:before{
                content: "";
                height:0px;
            }
            .tile_stats_count hr {
                margin-top: 0px; 
                margin-bottom: 10px;
                border: 0;
                border-top: 1px solid #eee;
            }
            .sub_description2:before {
                content: "";
                position: absolute;
                left: 0;
                height: 65px;
                border-left: 2px solid #ADB2B5;
                margin-top: 10px;
            }
            .tile_stats_count .count {
                font-size: 24px!important;
            }
            .tile_stats_count .count_top{
                min-height: 38px;
            }
            .tile_stats_count .query_description{
                min-height: 30px;
            }
        </style>
        
        <?php if(ENVIRONMENT === 'production') { ?> 
            <!-- Global site tag (gtag.js) - Google Analytics -->
            <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo _247AROUND_CRM_GGL_ANALYTICS_TAG_ID; ?>"></script>
            <script>
              window.dataLayer = window.dataLayer || [];
              function gtag(){dataLayer.push(arguments);}
              gtag('js', new Date());

              gtag('config', '<?php echo _247AROUND_CRM_GGL_ANALYTICS_TAG_ID; ?>');
            </script>

        <?php } ?>
    </head>
    <body class="nav-md">
        <div class="container body">
        <div class="main_container">
        <div class="col-md-3 left_col">
            <div class="left_col scroll-view">
                <div class="navbar nav_title" style="border: 0;">
                    <?php if($saas_flag){ ?>
                    <a class="navbar-brand" href="#"><img src="<?php echo base_url()?>images/small_logo.png" style="display: inline;"></a>
                    <?php } 
                    else {
                    ?>
                     <a class="navbar-brand" href="#">247Around</a>
                    <?php } ?>
                </div>
                <div class="clearfix"></div>
                <!-- menu profile quick info -->
                <div class="profile clearfix">
                    <div class="profile_pic">
                        <!--                              <img src="images/img.jpg" alt="..." class="img-circle profile_img">-->
                    </div>
                    <div class="profile_info">
                        <span>Welcome</span>
                        <h2><?php echo $this->session->userdata('emp_name') ?></h2>
                    </div>
                </div>
                <!-- /menu profile quick info -->
                <br />
                <!-- sidebar menu -->
                <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                    <div class="menu_section">
                        <ul class="nav side-menu">
                            <li><a href="<?php echo base_url(); ?>employee/user" target="_blank"><i class="fa fa-home"></i>Go TO CRM</a>
                            <li><a href="<?php echo base_url(); ?>employee/dashboard" target="_blank"><i class="fa fa-bar-chart-o"></i>SERVICE DASHBOARD</a>
                            </li>
                            <li><a href="<?php echo base_url(); ?>employee/dashboard/buyback_dashboard" target="_blank"><i class="fa fa-bar-chart-o"></i>BUYBACK DASHBOARD</a>
                            </li>
                            <li>
                                <a><i class="fa fa-edit"></i>BUYBACK<span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu">
                                    
                                    <li><a href="<?php echo base_url(); ?>buyback/buyback_process/view_bb_order_details">Pending Orders</a></li>
                                    <li><a href="<?php echo base_url(); ?>buyback/upload_buyback_process">Upload Order File</a></li>
                                    <li><a href="<?php echo base_url(); ?>buyback/collection_partner/get_cp_shop_address">Shop Addresses</a></li>
                                    <li><a href="<?php echo base_url(); ?>buyback/buyback_process/bb_order_review">Review Order</a></li>
                                    <li>
                                        <a>Disputed Orders <span class="fa fa-chevron-down"></span></a>
                                        <ul class="nav child_menu">
                                            <li><a href="<?php echo base_url(); ?>buyback/buyback_process/disputed_auto_settel">Auto Settled (Cancelled / Rejected)</a></li>

                                            <li><a href="<?php echo base_url(); ?>buyback/buyback_process/disputed_30_days_breech">30 Days TAT Breach</a></li>
                                            <li><a href="<?php echo base_url();?>buyback/buyback_process/vendor_rejected">Rejected By CP</a></li>
                                        </ul>
                                    </li>
                                    <li><a href="<?php echo base_url(); ?>buyback/buyback_process/tag_untag_bb_orders">Claimed Orders</a></li>
                                    <li><a href="<?php echo base_url(); ?>buyback/buyback_process/bb_claimed_raised_order_data">Debit Note Raised</a></li>
                                    <li><a href="<?php echo base_url(); ?>buyback/buyback_process/filter_bb_price_list">Charges List</a></li>
                                    <li><a href="<?php echo base_url(); ?>buyback/upload_buyback_process/price_sheet_upload">Upload Price Sheet</a></li>
                                    <li><a href="<?php echo base_url(); ?>buyback/buyback_process/bb_order_search">Advanced Search</a></li>
                                    <li><a href="<?php echo base_url(); ?>buyback/upload_buyback_process/highest_quote_price_sheet_upload">Upload Highest Price Quote</a></li>
                                </ul>
                            </li>
                        </ul>
                        </li>                  
                        <!--                  <li><a href="javascript:void(0)"><i class="fa fa-laptop"></i> Landing Page <span class="label label-success pull-right">Coming Soon</span></a></li>-->
                        </ul>
                    </div>
                </div>
                <!-- /sidebar menu -->
                <!-- /menu footer buttons -->
                <div class="sidebar-footer hidden-small">
                    <a data-toggle="tooltip" data-placement="top" title="Settings">
                    <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
                    </a>
                    <a data-toggle="tooltip" data-placement="top" title="FullScreen">
                    <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
                    </a>
                    <a data-toggle="tooltip" data-placement="top" title="Lock">
                    <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
                    </a>
                    <a data-toggle="tooltip" data-placement="top" title="Logout" href="login.html">
                    <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
                    </a>
                </div>
                <!-- /menu footer buttons -->
            </div>
        </div>
        <!-- top navigation -->
        <div class="top_nav">
            <div class="nav_menu">
                <nav>
                    <div class="nav toggle">
                        <a id="menu_toggle"><i class="fa fa-bars"></i></a>
                    </div>
                    <ul class="nav navbar-nav navbar-right">
                        <li class="">
                            <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            <?php echo $this->session->userdata('emp_name') ?>
                            <span class=" fa fa-angle-down"></span>
                            </a>
                            <ul class="dropdown-menu dropdown-usermenu pull-right">
                                <li><a href="<?php echo base_url() ?>employee/login/logout"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
                            </ul>
                        </li>
                        <li class="col-md-4">
                            <a href="javascript:void(0)" style="background: #EDEDED;">
                           
                                <input type="text" class="form-control" placeholder="Search Order/Tracking ID ..." onkeydown="search_order_id(this)" 
                                       style="border-radius:25px 25px 25px 25px">
                            
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        <!-- /top navigation -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/waypoints/2.0.3/waypoints.min.js"></script> 
        <script src="<?php echo base_url(); ?>js/jquery.counterup.min.js"></script> 
        <script src="<?php echo base_url();?>js/select2.min.js"></script>
        <script>          
            function search_order_id(ele){
                if(event.keyCode === 13 && ele.value !== '') {
                
                $.ajax({
                    type: 'POST',
                    url: '<?php echo base_url()?>buyback/buyback_process/search_for_buyback',
                    data: {search:ele.value},
                    success: function (response) {
                     //console.log(response);
                     $(".right_col").html(response);
                      
                   }
                 });
                }
            }
        </script>