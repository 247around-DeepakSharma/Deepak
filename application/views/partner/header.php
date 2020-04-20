<?php
    $CI = & get_instance();
    $CI->load->library('email');
    $userdata = $CI->session->all_userdata();
    $partner_name = $this->session->userdata('partner_name');
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <!-- Meta, title, CSS, favicons, etc. -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>247around <?php echo $partner_name; ?> CRM</title>
        <!-- Bootstrap -->
        <link href="<?php echo base_url() ?>css/bootstrap.min.css" rel="stylesheet">
        <!-- Font Awesome -->
        <link href="<?php echo base_url() ?>font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <!-- bootstrap-progressbar -->
        <link href="<?php echo base_url() ?>css/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet">
        <!-- bootstrap-daterangepicker -->
        <link href="<?php echo base_url() ?>css/daterangepicker.css" rel="stylesheet">
        <!-- Custom Theme Style -->
        <link href="<?php echo base_url() ?>css/dashboard_custom.min.css" rel="stylesheet">
        <!-- Sweet Alert Css -->
        <link href="<?php echo base_url() ?>css/sweetalert.css" rel="stylesheet">
        <!-- Select2 CSS -->
        <link href="<?php echo base_url()?>css/select2.min.css" rel="stylesheet" />
        <!-- DataTable CSS -->
        <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assest/DataTables/datatables.min.css"/>
        <!-- jQuery -->
        <script src="<?php echo base_url() ?>js/jquery.min.js"></script>
        <!-- moment.js -->
        <script src="<?php echo base_url() ?>js/moment.min.js"></script>
        <!-- bootstrap-daterangepicker -->
        <script src="<?php echo base_url() ?>js/daterangepicker.js"></script>
        <!-- DateJS -->
        <script src="<?php echo base_url() ?>assest/DateJS/build/date.js"></script>
        <!-- Select2 JS -->
        <script src="<?php echo base_url();?>js/select2.min.js"></script>
        <!-- sweet Alert JS -->
        <script src="<?php echo base_url();?>js/sweetalert.min.js"></script>
        <script type="text/javascript" src="<?php echo base_url() ?>assest/DataTables/datatables.min.js"></script>
        <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
        <script src="<?php echo base_url() ?>js/partner.js"></script>
        <style>
            .right_col{
                min-height:700px!important;
            }
            .profile_pic {
                width: 100%;
                float: left;
            }
            .img-circle {
                border-radius: 0%;
            }
            .profile_info{
                width: 100%;
            }
            .img-circle.profile_img {
                background: #fff;
                z-index: 1000;
                position: inherit;
                margin: 0px;
                border: 1px solid rgba(52,73,94,.44);
                padding: 4px;
            }
            .select2-container--default .select2-selection--single {
                border-radius: 0px;
                border: 1px solid #ccc;
            }
            .select2-container--default .select2-selection--single .select2-selection__rendered{
                padding-top: 0px;
            }
            .select2-container--default .select2-selection--multiple, .select2-container--default .select2-selection--single{
                min-height: 34px;
            }
            .profile_details .profile_view{
                padding: 0px;
            }
            .nav-sm .main_container .top_nav{
                margin-left: 0px;
            }
            .nav-sm .container.body .col-md-3.left_col{
                margin-top: 60px;
                width: 40px;
            }
            .nav-md .container.body .col-md-3.left_col{
                margin-top: 60px;
                width: 206px;
            }
            .nav-md .container.body .right_col{
                margin-left: 206px;;
            }
            .nav-sm .container.body .right_col{
                margin-left: 40px;
            }
            .navbar-brand, .navbar-nav > li > a{
                line-height: 24px;
            }
            .right_col{
                min-height: 600px!important;
                margin-top: 50px!important;
            }
            .navbar{
                height: 45px;
            }
            .navbar-header{
                background: transparent;
                border: 1px solid #eee;
            }
            .navbar-brand {
                padding: 0px;
                height: 45px;
            }
            .navbar-brand>img {
                height: 100%;
                padding: 15px;
                width: auto;
            }
            .nav-sm .sidebar-footer{
                width: 40px;
            }
            .nav-sm .sidebar-footer a{
                width: 100%;
            }
            .nav-sm footer ,.nav-md footer{
                margin-left: 0px;
            }
            footer{
                padding: 20px;
            }
            .nav.side-menu > li:hover{
                background: #1ABB9C;
            }
            .side_menu_list_title{
                display: none;
            }
            .nav-md .sidebar-footer{
                width: 206px;
            }
            .sidebar-footer a{
                width: 100%;
            }
            .custom_pagination{
                float: right;
                background: #ddd;
                margin: 2px 0;
                padding: 4px;
                border-radius: 4px;
            }
            .custom_pagination a{
                border: 1px solid #ddd;
                padding: 6px 10px;
                line-height: 24px;
            }
            .custom_pagination > strong {
                color:#fff;
            }
            .nav-sm .nav.side-menu li a{
                font-size: 12px;
            }
            .navbar-brand > img{
                padding: 0px 2px;
            }
            .form-control{
                font-size: 13px;
            }
            ul.bar_tabs{
                background: transparent;
            }
            @media (max-width: 768px) {
                .navbar-fixed-top {
                  position: static;
                  bottom: 0;
                }
                .top_nav .navbar-right{
                    width: 94%;
                }
            }
            @media (max-width: 480px) {
                .navbar-header{
                    display: none;
                }
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
    <body class="nav-sm">
        <div class="container body">
        <div class="main_container">
            <!-- top navigation -->
            <div class="top_nav">
                <div class="nav_menu">
                    <nav class="navbar navbar-default navbar-fixed-top">
                        <div class="navbar-header">
                            <?php $src = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/misc-images/".$this->session->userdata('partner_logo');?>
                            <a class="navbar-brand" href="#"><img src="<?php echo $src;?>" alt="partner_logo"></a>
                        </div>
<!--                        <div class="nav toggle">
                            <a id="menu_toggle"><i class="fa fa-bars"></i></a>
                        </div>-->
                        <ul class="nav navbar-nav navbar-right">
<!--                             <li><div class="dropdown" style="float:right;margin: 16px 14px 0px 0px;">
                                    <a class=" dropdown-toggle fa fa-bell" id="notification_holder"  data-toggle="dropdown" onclick="get_notifications(<?php echo $this->session->userdata('partner_id'); ?>,'partner')"></a>
                                    <ul class="dropdown-menu" role="menu" aria-labelledby="notification_holder" id="notification_container" style="width: 350px;padding-top: 0px;margin-top: 12px;border: 1px solid #2a3f54;"> 
                                    <center>
                                        <img id="loader_gif_escalation" src="<?php echo base_url(); ?>images/loadring.gif" >
                                    </center>
                                    </ul>
                                </div>
                             </li>-->
                            <li class="">
                                <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <?php echo $partner_name; ?>
                                    <span><i class="fa fa-angle-down"></i></span>
                                </a>
                                <ul class="dropdown-menu dropdown-usermenu pull-right">
                                    <?php
                                    if($this->session->userdata('agent_id') != '980084' && $this->session->userdata('agent_id') != '980083'){
                                    ?>
                                    <li><a href="<?php echo base_url() ?>employee/partner/show_partner_edit_details_form"><i class="fa fa-edit pull-right"></i> Edit Details</a></li>
                                    <li><a href="<?php echo base_url() ?>employee/partner/reset_partner_password"><i class="fa fa-key pull-right"></i> Reset Password</a></li>
                                    <?php
                                    }
                                    ?>
                                    <li><a href="<?php echo base_url() ?>employee/partner/logout"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
                                </ul>
                            </li>

                            <li class="col-md-4">
                                <form method="POST" class="navbar-form navbar-left" role="search" action="<?php echo base_url(); ?>partner/search" onsubmit="return checkStringLength()">
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="searched_text" placeholder="Search Booking ID or Mobile" name="searched_text" style="width: 130%;border-radius:25px 25px 25px 25px">
                                    </div> 
                                </form>
                            </li>
                           <li><a style="color:#00ff7e;font-size:20px;font-weight:900;" id="myBtn">COVID-19</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
            <div class="col-md-3 left_col menu_fixed">
                <div class="left_col scroll-view" style="width:100%;">
                    <br>
                    <!-- sidebar menu -->
                    <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                        <div class="menu_section">
                            <ul class="nav side-menu">
                                <li><a href="<?php echo base_url(); ?>partner/get_user_form" data-toggle="tooltip" data-placement="right" title="" data-original-title="Advance Search"><i class="fa fa-search"></i><span class="side_menu_list_title">Advance Search</span></a></li>
                                <li>
                                    <a data-toggle="tooltip" data-placement="right" title="" data-original-title="Bookings"><i class="fa fa-book"></i> <span class="side_menu_list_title">Bookings</span><span class="fa fa-chevron-down"></span></a>
                                    <ul class="nav child_menu" style="display:none!important;">
                                        <li><a href="<?php echo base_url(); ?>partner/home">Pending Bookings</a></li>
                                        <li><a href="<?php echo base_url(); ?>partner/closed_booking/Completed">Completed Bookings</a></li>
                                        <li><a href="<?php echo base_url(); ?>partner/closed_booking/Cancelled">Cancelled Bookings</a></li>
                                    </ul>
                                </li>
                                <?php
                                if($this->session->userdata('agent_id') != '980084' && $this->session->userdata('agent_id') != '980083'){
                                    ?>
                                <li>
                                    <a data-toggle="tooltip" data-placement="right" title="" data-original-title="Spare bookings"><i class="fa fa-truck"></i><span class="side_menu_list_title">Spare Bookings</span><span class="fa fa-chevron-down"></span></a>
                                    <ul class="nav child_menu">
                                        <li><a href="<?php echo base_url(); ?>partner/get_spare_parts_booking">Pending Spares On <?php echo $partner_name ?></a></li>
                                        <li><a href="<?php echo base_url(); ?>partner/get_shipped_parts_list">Shipped Spares by <?php echo $partner_name ?></a></li>
                                         <li><a href="<?php echo base_url(); ?>partner/get_pending_part_on_sf">Pending Spares On SF</a></li>
                                        <li><a href="<?php echo base_url(); ?>partner/get_waiting_defective_parts">Shipped Spares by SF</a></li>
                                        <li><a href="<?php echo base_url(); ?>partner/get_approved_defective_parts_booking">Received Spares by <?php echo $partner_name ?></a></li>
                                    </ul>
                                </li>
				<?php if($this->session->userdata('is_wh')) { ?> 
				<li>
                                    <a data-toggle="tooltip" data-placement="right" title="" data-original-title="Inventory"><i class="fa fa-dropbox"></i><span class="side_menu_list_title">Inventory</span><span class="fa fa-chevron-down"></span></a>
                                    <ul class="nav child_menu">
                                        
                                        <li><a href="<?php echo base_url(); ?>partner/inventory/inventory_list">247around warehouse Inventory</a></li>
                                        <li><a href="<?php echo base_url(); ?>partner/inventory/ack_spare_send_by_wh">Acknowledge Defective Spare Send by Warehouse</a></li>
					<li><a href="<?php echo base_url(); ?>partner/inventory/show_inventory_details">Spare Part List</a></li>
                                        <li><a href="<?php echo base_url(); ?>partner/inventory/show_inventory_appliance_details">Spare Model List</a></li>
					<li><a href="<?php echo base_url(); ?>partner/inventory/tag_spare_invoice">Spare Dispatched by Partner To Warehouse</a></li>
                                      
                                    </ul>
                                </li>
				<?php } ?>
                                <li>
                                    <a data-toggle="tooltip" data-placement="right" title="" data-original-title="Invoices"><i class="fa fa-inr"></i><span class="side_menu_list_title">Invoice</span><span class="fa fa-chevron-down"></span></a>
                                    <ul class="nav child_menu">
                                        <li><a href="<?php echo base_url(); ?>partner/invoices_details">Invoice</a></li>
                                        <li><a href="<?php echo base_url(); ?>partner/banktransaction">Bank Transaction</a></li>
                                        <li><a href="<?php echo base_url(); ?>payment/details">Pay</a></li>
                                    </ul>
                                </li>
<!--                                <li>
                                    <a data-toggle="tooltip" data-placement="right" title="" data-original-title="Reports"><i class="fa fa-line-chart"></i><span class="side_menu_list_title">Reports</span><span class="fa fa-chevron-down"></span></a>
                                    <ul class="nav child_menu">
                                 <li><a href="<?php echo base_url(); ?>partner/download_partner_summary/<?php echo $this->session->userdata('partner_id'); ?>">Summary Report</a></li>
                                        <li><a href="<?php echo base_url(); ?>partner/report">Report</a></li>
                                        <li><a href="<?php echo base_url(); ?>partner/serviceability_list">Serviceability List</a></li>
                                        <li><a href="<?php echo base_url(); ?>partner/download_sf_list_excel">Service Center List</a></li>
                                    </ul>
                                </li>-->
                                 <li><a href="<?php echo base_url(); ?>partner/reports" data-toggle="tooltip" data-placement="right" title="" data-original-title="Downloads">
                                         <i class="glyphicon glyphicon-download-alt"></i><span class="side_menu_list_title">Downloads</span></a></li>
                                         
                                 <li><a href="<?php echo base_url(); ?>partner/contracts" data-toggle="tooltip" data-placement="right" title="" data-original-title="Contracts">
                                         <i class="fa fa-handshake-o"></i><span class="side_menu_list_title">Contracts</span></a></li>
                                 <?php } ?>
                                <li><a href="<?php echo base_url(); ?>partner/contact_us" data-toggle="tooltip" data-placement="right" title="" data-original-title="Contact Us">
                                        <i class="fa fa-phone"></i><span class="side_menu_list_title">Contact Us</span></a></li>
                            </ul>
                        </div>
                    </div>
                    <!-- /sidebar menu -->
                    <!-- /menu footer buttons -->
                    <div class="sidebar-footer">
                        <a id="menu_toggle"><i class="fa fa-arrow-right"></i></a>
                    </div>
                    <!-- /menu footer buttons -->
            </div>
        </div>    
    </div>
    <div id="myModal" class="modal">

  <!-- Modal content -->
 
  <div class="modal-content">
      <div class="modal-header">
        <button style="color:#110101;font-weight:900;" type="button" id="close_covid" class="close hide" data-dismiss="modal">X</button>
        <h4 class="modal-title">Technician Safety Guidelines COVID 19 Working 20th April</h4>
      </div>

      <div class="modal-body" style="font-weight:700;font-size:18px !important;">
<p>1. Technician Temperature to be checked before issuing calls.</p>
<p>2. Face mask, Hand Gloves, Hand sanitizer are mandatory.</p>
<p>3. No Sign to be taken on any document.</p>
<p>4. Call Customer on phone from door. Do not use Door bell.</p>
<p>5. Wash hands before work start, Wash hands after work finishes.</p>
<p>6. If customer looks unwell (Cough, fever) no work to be done just apologise and leave.</p>
<p>7. Customer to stand at a safe distance 3 feet from technician and helper.</p>
<p>8. Leave all your belongings like helmet etc outside the customer house.</p>
<p>9. Helper to follow same guidelines and technician to make sure all the above for helper.</p>

      </div>

 
    
  </div>

</div>
<style>
.nav .open>a, .nav .open>a:focus, .nav .open>a:hover {
    background-color: #2c9d9c;
    color: #fff;
}
    .navigation_li{
    font: normal 16px/16px Century Gothic;
    list-style: none;
    padding: 7px 0px 6px 42px;
    }
    .navigation_li a:hover {
    background: none;
    color:#fff;
}
.navigation_li>li>a:focus, .dropdown-menu>li>a:hover {
    color: #262626;
    text-decoration: none;
    background-color: transparent;
}
.normal{
    background: url(<?php echo base_url() ?>/images/logo_small.png) 7px 3px no-repeat;
}    
.important{
    background: url(<?php echo base_url() ?>/images/i.png) 7px 3px no-repeat;
}
.no_new_notification{
    color: #000;
        text-align: center;
    padding: 3px;
}
            </style>
<style>


/* The Modal (background) */
.modal {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  padding-top: 100px; /* Location of the box */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content */
.modal-content {
  background-color: #fefefe;
  margin: auto;
  padding: 20px;
  border: 1px solid #888;
  width: 80%;
}

/* The Close Button */
.close {
  color: #aaaaaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
}

.close:hover,
.close:focus {
  color: #000;
  text-decoration: none;
  cursor: pointer;
}
</style>
            <script>
                function get_notifications(entity_id,entity_type){
                    $.ajax({
                    type: 'POST',
                    url: '<?php echo base_url(); ?>push_notification/get_notifications',
                    data: {entity_id: entity_id,entity_type: entity_type},
                    success: function (response) {
                        $("#notification_container").html(response);
                    }
                    });
                }
                </script>
