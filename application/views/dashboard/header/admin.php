<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <!-- Meta, title, CSS, favicons, etc. -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Dashboard </title>
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
    </head>
    <body class="nav-md">
        <div class="container body">
        <div class="main_container">
        <div class="col-md-3 left_col">
            <div class="left_col scroll-view">
                <div class="navbar nav_title" style="border: 0;">
                    <a href="<?php echo base_url(); ?>employee/dashboard" class="site_title"><i class="fa fa-paw"></i> <span>247Around</span></a>
                </div>
                <div class="clearfix"></div>
                <!-- menu profile quick info -->
                <div class="profile clearfix">
                    <div class="profile_pic">
                        <!--                              <img src="images/img.jpg" alt="..." class="img-circle profile_img">-->
                    </div>
                    <div class="profile_info">
                        <span>Welcome,</span>
                        <h2><?php echo $this->session->userdata('employee_id') ?></h2>
                    </div>
                </div>
                <!-- /menu profile quick info -->
                <br />
                <!-- sidebar menu -->
                <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                    <div class="menu_section">
                        <ul class="nav side-menu">
                            <li><a href="<?php echo base_url(); ?>employee/user" target="_blank"><i class="fa fa-home"></i> Go TO CRM</a>
                            <li><a href="<?php echo base_url(); ?>employee/dashboard" target="_blank"><i class="fa fa-bar-chart-o"></i>DASHBOARD</a>
                            </li>
                            <li><a href="<?php echo base_url(); ?>employee/dashboard/buyback_dashbord" target="_blank"><i class="fa fa-bar-chart-o"></i>Buyback DASHBOARD</a>
                            </li>
                            <li><a href="<?php echo base_url();?>buyback/buyback_process/download_bb_shop_address" ><i class="fa fa-download"></i>Download Shop Address File</a></li>
                            <li>
                                <a><i class="fa fa-edit"></i> BUYBACK <span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu">
                                    <li><a href="<?php echo base_url(); ?>buyback/buyback_process/bb_order_search">Orders Snapshot</a></li>
                                    <li><a href="<?php echo base_url(); ?>buyback/upload_buyback_process">Upload Order File</a></li>
                                    <li><a href="<?php echo base_url(); ?>buyback/upload_buyback_process/price_sheet_upload">Upload Price Sheet</a></li>

                                    <li><a href="<?php echo base_url(); ?>buyback/buyback_process/view_bb_order_details">Pending Orders</a></li>
                                    <li>
                                        <a>Disputed Orders <span class="fa fa-chevron-down"></span></a>
                                        <ul class="nav child_menu">
                                            <li><a href="<?php echo base_url(); ?>buyback/buyback_process/disputed_auto_settel">Auto Settled (Cancelled / Rejected)</a></li>

                                            <li><a href="<?php echo base_url(); ?>buyback/buyback_process/disputed_30_days_breech">30 Days TAT Breach</a></li>
                                            <li><a href="<?php echo base_url();?>buyback/buyback_process/vendor_rejected">Rejected By CP</a></li>
                                        </ul>
                                    </li>
                                    <li><a href="<?php echo base_url(); ?>buyback/buyback_process/bb_order_review">Review Order</a></li>
                                    <li><a href="<?php echo base_url(); ?>buyback/collection_partner/get_cp_shop_address">Shop Addresses</a></li>
                                    <li><a href="<?php echo base_url(); ?>buyback/buyback_process/filter_bb_price_list">Charges List</a></li>
                                </ul>
                            </li>
                            <!--                        <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                                <div class="menu_section">
                                    <h3>General</h3>
                                    <ul class="nav side-menu">
                                        <li><a href="<?php echo base_url(); ?>employee/user" target="_blank"><i class="fa fa-home"></i> Go TO CRM <span class="fa fa-chevron-down"></span></a>
                                            <ul class="nav child_menu" style="display: none">
                                                <li><a href="<?php echo base_url(); ?>employee/dashboard">Dashboard</a></li>
                                                <li><a href="<?php echo base_url(); ?>employee/user" target="_blank">CRM</a></li>
                                                <li><a href="index3.html">Dashboard3</a></li>
                                            </ul>
                                        </li>
                                       <li><a href="<?php echo base_url(); ?>buyback/upload_buyback_process" target="_blank"><i class="fa fa-edit"></i> Buyback <span class="fa fa-chevron-down"></span></a>
                                          <ul class="nav child_menu">
                                            <li><a href="form.html">General Form</a></li>
                                            <li><a href="form_advanced.html">Advanced Components</a></li>
                                            <li><a href="form_validation.html">Form Validation</a></li>
                                            <li><a href="form_wizards.html">Form Wizard</a></li>
                                            <li><a href="form_upload.html">Form Upload</a></li>
                                            <li><a href="form_buttons.html">Form Buttons</a></li>
                                          </ul>
                                        </li>-->
                            <!--    <li><a><i class="fa fa-desktop"></i> UI Elements <span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu">
                                  <li><a href="general_elements.html">General Elements</a></li>
                                  <li><a href="media_gallery.html">Media Gallery</a></li>
                                  <li><a href="typography.html">Typography</a></li>
                                  <li><a href="icons.html">Icons</a></li>
                                  <li><a href="glyphicons.html">Glyphicons</a></li>
                                  <li><a href="widgets.html">Widgets</a></li>
                                  <li><a href="invoice.html">Invoice</a></li>
                                  <li><a href="inbox.html">Inbox</a></li>
                                  <li><a href="calendar.html">Calendar</a></li>
                                </ul>
                                </li>
                                <li><a><i class="fa fa-table"></i> Tables <span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu">
                                  <li><a href="tables.html">Tables</a></li>
                                  <li><a href="tables_dynamic.html">Table Dynamic</a></li>
                                </ul>
                                </li>
                                <li><a><i class="fa fa-bar-chart-o"></i> Data Presentation <span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu">
                                  <li><a href="chartjs.html">Chart JS</a></li>
                                  <li><a href="chartjs2.html">Chart JS2</a></li>
                                  <li><a href="morisjs.html">Moris JS</a></li>
                                  <li><a href="echarts.html">ECharts</a></li>
                                  <li><a href="other_charts.html">Other Charts</a></li>
                                </ul>
                                </li>
                                <li><a><i class="fa fa-clone"></i>Layouts <span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu">
                                  <li><a href="fixed_sidebar.html">Fixed Sidebar</a></li>
                                  <li><a href="fixed_footer.html">Fixed Footer</a></li>
                                </ul>
                                </li>
                                </ul>
                                </div>
                                <div class="menu_section">
                                <h3>Live On</h3>
                                <ul class="nav side-menu">
                                <li><a><i class="fa fa-bug"></i> Additional Pages <span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu">
                                  <li><a href="e_commerce.html">E-commerce</a></li>
                                  <li><a href="projects.html">Projects</a></li>
                                  <li><a href="project_detail.html">Project Detail</a></li>
                                  <li><a href="contacts.html">Contacts</a></li>
                                  <li><a href="profile.html">Profile</a></li>
                                </ul>
                                </li>
                                <li><a><i class="fa fa-windows"></i> Extras <span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu">
                                  <li><a href="page_403.html">403 Error</a></li>
                                  <li><a href="page_404.html">404 Error</a></li>
                                  <li><a href="page_500.html">500 Error</a></li>
                                  <li><a href="plain_page.html">Plain Page</a></li>
                                  <li><a href="login.html">Login Page</a></li>
                                  <li><a href="pricing_tables.html">Pricing Tables</a></li>
                                </ul>
                                </li>
                                <li><a><i class="fa fa-sitemap"></i> Multilevel Menu <span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu">
                                    <li><a href="#level1_1">Level One</a>
                                    <li><a>Level One<span class="fa fa-chevron-down"></span></a>
                                      <ul class="nav child_menu">
                                        <li class="sub_menu"><a href="level2.html">Level Two</a>
                                        </li>
                                        <li><a href="#level2_1">Level Two</a>
                                        </li>
                                        <li><a href="#level2_2">Level Two</a>
                                        </li>
                                      </ul>
                                    </li>
                                    <li><a href="#level1_2">Level One</a>
                                    </li>-->
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
                            <?php echo $this->session->userdata('employee_id') ?>
                            <span class=" fa fa-angle-down"></span>
                            </a>
                            <ul class="dropdown-menu dropdown-usermenu pull-right">
                                <!--                                        <li><a href="javascript:;"> Profile</a></li>
                                    <li>
                                        <a href="javascript:;">
                                            <span class="badge bg-red pull-right">50%</span>
                                            <span>Settings</span>
                                        </a>
                                    </li>
                                    <li><a href="javascript:;">Help</a></li>-->
                                <li><a href="<?php echo base_url() ?>employee/login/logout"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
                            </ul>
                        </li>
                        <li><a href="javascript:void(0)">Credit Amount (Rs. <span class="numbers-with-commas">0</span>)</a></li>
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
            jQuery(document).ready(function( $ ) {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo base_url(); ?>buyback/buyback_process/get_credit_amount',
                   
                    success: function (data) {
                        
                      $('.numbers-with-commas').text(data);
                      $('.numbers-with-commas').counterUp({
                        delay: 10, // the delay time in ms
                        time: 1000 // the speed time in ms
                       });
                   }
                 });
            
            });
            
            function search_order_id(ele){
                if(event.keyCode === 13 && ele.value !== '') {
                
                $.ajax({
                    type: 'POST',
                    url: '<?php echo base_url()?>buyback/buyback_process/search_for_buyback',
                    data: {search:ele.value},
                    success: function (response) {
                     console.log(response);
                     $(".right_col").html(response);
                      
                   }
                 });
                }
            }
        </script>