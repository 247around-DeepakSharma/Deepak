<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <meta name="format-detection" content="telephone=no">
        <title>247Around</title>
        <!-- Bootstrap Core CSS -->
        <link href="<?php echo base_url()?>css/bootstrap.min.css" rel="stylesheet">
        <!-- Custom CSS -->
        <link href="<?php echo base_url()?>css/sb-admin.css" rel="stylesheet">
        <!-- Animate CSS -->
        <link href="<?php echo base_url()?>css/animate.css" rel="stylesheet">
        <!-- Custom Fonts -->
        <link href="<?php echo base_url()?>font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <script src="<?php echo base_url()?>js/jquery.js"></script>
        <!-- Load jQuery UI Main CSS-->
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
        <!-- Load jqgrid -->
        <script type='text/javascript' src='<?php echo base_url()?>js/jquery.jqGrid.js'></script>
        <link rel='stylesheet' type='text/css' href='<?php echo base_url()?>css/ui.jqgrid.css' />
        <link rel='stylesheet' type='text/css' href='https://code.jquery.com/ui/1.10.3/themes/redmond/jquery-ui.css' />
        <script type='text/javascript' src='<?php echo base_url()?>js/grid.locale-en.js'></script>
        <!-- Load jQuery UI Main JS  -->
        <script src="https://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
        <!-- Bootstrap Core JavaScript -->
        <script src="<?php echo base_url()?>js/bootstrap.min.js"></script>
        <link href="<?php echo base_url()?>css/select2.min.css" rel="stylesheet" />
        <link href="<?php echo base_url()?>css/style.css" rel="stylesheet" />
        <script src="<?php echo base_url();?>js/select2.min.js"></script>
        <!-- Loading Form js -->
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/3.51/jquery.form.min.js"></script>
        <!-- Loading Noty script library -->
        <script type="text/javascript" src="<?php echo base_url()?>js/plugins/noty/packaged/jquery.noty.packaged.min.js"></script>
        <style>
            .navbar{
            min-height: 80px;
            }
        </style>
        <script src="<?php echo base_url()?>assest/datatables.net/js/jquery.dataTables.min.js"></script>
        <script src="<?php echo base_url()?>assest/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>

        <script src="<?php echo base_url()?>assest/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
        <script src="<?php echo base_url()?>assest/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
    </head>
    <body>
        <?php $CI =& get_instance(); 
            $logged_id = $CI->session->userdata('id'); ?>
        <div id="wrapper">
        <!-- Navigation -->
        <!-- Navigation -->
        <nav class="navbar navbar-custom">
            <div class="container-fluid">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar" style="border: 1px solid #fff;"></span>
                    <span class="icon-bar" style="border: 1px solid #fff;"></span>
                    <span class="icon-bar" style="border: 1px solid #fff;"></span>
                    </button>
                    <a class="navbar-brand" href="#">
                    <img alt="Brand" src="<?php echo base_url()?>images/logo.jpg">
                    </a>
                </div>
                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        <li>
                            <a href="<?php echo base_url()?>employee/user">Find User</a>
                        </li>
                        <li class="dropdown ">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            Queries  <i class="fa fa-caret-down"></i>
                            </a>
                            <ul class="dropdown-menu  ">
                                <li >
                                    <a  href="<?php echo base_url() ?>employee/booking/view_queries/FollowUp/p_av"><i class="fa fa-fw fa-desktop"></i> <strong> Pending Queries (Pincode Available)</a></strong>
                                </li>
                                <li class="divider"></li>
                                <li >
                                    <a href="<?php echo base_url() ?>employee/booking/get_missed_calls_view"><i class="fa fa-fw fa-desktop"></i> <strong> Missed Calls</strong></a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a  href="<?php echo base_url() ?>employee/booking/view_queries/FollowUp/p_nav"><i class="fa fa-fw fa-desktop"></i> <strong> Pending Queries (Pincode Not Available)</a></strong>
                                </li>
                                <li class="divider"></li>
                                <li >
                                    <a href="<?php echo base_url() ?>employee/booking/view_queries/Cancelled/p_all"><i class="fa fa-fw fa-desktop"></i> <strong> Cancelled Queries</strong></a>
                                </li>
                            </ul>
                            <!-- /.dropdown-messages -->
                        </li>
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            Bookings <i class="fa fa-caret-down"></i>
                            </a>
                            <ul class="dropdown-menu ">
                                <li>
                                    <a href="<?php echo base_url() ?>employee/booking/view"><i class="fa fa-fw fa-desktop"></i> <strong> Pending Booking</strong></a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="<?php echo base_url() ?>employee/inventory/get_spare_parts"><i class="fa fa-fw fa-desktop"></i> <strong> Spare Parts Booking</strong></a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="<?php echo base_url() ?>employee/booking/view_bookings_by_status/Completed"><i class="fa fa-fw fa-desktop"></i> <strong>Completed Booking</strong></a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="<?php echo base_url() ?>employee/booking/view_bookings_by_status/Cancelled"><i class="fa fa-fw fa-desktop"></i> <strong>Cancelled Booking</strong></a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="<?php echo base_url() ?>employee/booking/get_pending_booking_by_partner_id"><i class="fa fa-fw fa-desktop"></i> <strong>Repair Bookings</strong></a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="<?php echo base_url()?>employee/booking/update_not_pay_to_sf_booking"><i class="fa fa-fw fa-desktop"></i> <strong>Wall Mount Given</strong></a>
                                </li>
                                 <li class="divider"></li>
                                <li>
                                    <a href="<?php echo base_url()?>employee/booking/auto_assigned_booking"><i class="fa fa-fw fa-desktop"></i> <strong>Auto Assigned Booking</strong></a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="<?php echo base_url()?>employee/upcountry/get_upcountry_failed_details"><i class="fa fa-fw fa-desktop"></i> <strong>Upcountry Failed Booking</strong></a>
                                </li>
                                
                            </ul>
                            <!-- /.dropdown-tasks -->
                        </li>
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            Service Center <i class="fa fa-caret-down"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="<?php echo base_url() ?>employee/vendor/viewvendor" ><i class="fa fa-fw fa-desktop"></i> <strong> View Service Centres</strong></a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="<?php echo base_url() ?>employee/vendor/vendor_availability_form"><i class="fa fa-fw fa-desktop "></i> <strong> Search Service Centre</strong></a>
                                </li>
                                <li class="divider"></li>
                                <li class="dropdown dropdown-submenu">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-fw fa-desktop "></i> <strong>Engineers</strong></a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="<?php echo base_url() ?>employee/vendor/add_engineer" ><i class="fa fa-fw fa-desktop"></i> <strong> Add Engineer</strong></a>
                                        </li>
                                        <li class="divider"></li>
                                        <li>
                                            <a href="<?php echo base_url() ?>employee/vendor/get_engineers" ><i class="fa fa-fw fa-desktop"></i> <strong> View Engineers</strong></a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                            <!-- /.dropdown-user -->
                        </li>
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            Appliances <i class="fa fa-caret-down"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="<?php echo base_url() ?>employee/booking/get_add_new_brand_form"><i class="fa fa-fw fa-desktop"></i> <strong> Add New Brand</strong></a>
                                </li>
                            </ul>
                            <!-- /.dropdown-user -->
                        </li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li>
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" id="verifyby"><i class="fa fa-user"></i> <?php echo $this->session->userdata('employee_id'); ?> <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="<?php echo base_url() ?>employee/user/update_employee/<?php echo $logged_id; ?>"><i class="fa fa-fw fa-desktop "></i> <strong>Edit Profile</strong></a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="<?php echo base_url() ?>employee/user/show_employee_list"><i class="fa fa-fw fa-desktop "></i> <strong>Employee List</strong></a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="<?php echo base_url() ?>employee/user/show_holiday_list"><i class="fa fa-fw fa-desktop "></i> <strong>Holiday List 2017</strong></a>
                                </li>
                            </ul>
                        </li>
                        <li><a href="<?php echo base_url()?>employee/login/logout"><i class="fa fa-fw fa-power-off"></i></a></li>
                    </ul>
                </div>
                <!-- /.navbar-collapse -->
            </div>
            <!-- /.container-fluid -->
        </nav>
        <script type="text/javascript">
            (function($){
            $(document).ready(function(){
            $('ul.dropdown-menu [data-toggle=dropdown]').on('click', function(event) {
            event.preventDefault();
            event.stopPropagation();
            $(this).parent().siblings().removeClass('open');
            $(this).parent().toggleClass('open');
            });
            });
            })(jQuery);
        </script>
        <style type="text/css">
            .marginBottom-0 {margin-bottom:0;}
            .dropdown-submenu{position:relative;}
            .dropdown-submenu>.dropdown-menu{top:0;left:100%;margin-top:-6px;margin-left:-1px;-webkit-border-radius:0 6px 6px 6px;-moz-border-radius:0 6px 6px 6px;border-radius:0 6px 6px 6px;}
            .dropdown-submenu>a:after{display:block;content:" ";float:right;width:0;height:0;border-color:transparent;border-style:solid;border-width:5px 0 5px 5px;border-left-color:#cccccc;margin-top:5px;margin-right:-10px;}
            .dropdown-submenu:hover>a:after{border-left-color:#555;}
            .dropdown-submenu.pull-left{float:none;}.dropdown-submenu.pull-left>.dropdown-menu{left:-100%;margin-left:10px;-webkit-border-radius:6px 0 6px 6px;-moz-border-radius:6px 0 6px 6px;border-radius:6px 0 6px 6px;}
        </style>